<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Sertifika;


/*
 * Uygulama Komutu
  php artisan import:dersloglari --offset=0 --limit=10000

son olarak bu komutu uyguladım

 php artisan import:dersloglari --offset=150000 --limit=10000

yaklaşık 158.000 veri çektim
 * */


class ImportDersLog extends Command
{
    protected $signature = 'import:dersloglari {--offset=0} {--limit=10000}';
    protected $description = 'CI3 API to Laravel';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/user_log/{$offset}/{$limit}";
        $this->info("Veriler şu adresten çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("Veri alınamadı. HTTP Hatası: " . $response->status());
            return Command::FAILURE;
        }

        $data = $response->json();

        if (empty($data)) {
            $this->info("Aktarılacak veri bulunamadı.");
            return Command::SUCCESS;
        }

        $this->info(count($data) . " veri alındı. Aktarma işlemi başlıyor...");

        foreach ($data as $row) {
            DB::table('ders_loglari')->insertOrIgnore([

                'id' => $row['id'],
                'ogrenci_id' => $row['user_id'],
                'ders_id' => $row['ders_id'],
                'ilk_izleme' => $row['ilk_izleme'],
                'son_izleme' => $row['son_izleme'],
                'ip_adresi_ilk' => $row['ip_adresi_ilk'],
                'ip_adresi_son' => $row['ip_adresi_son'],
                'izledigi_sure' => $row['izledigi_sure'],
                'nerede_kaldi' => $row['nerede_kaldi'],
            ]);

        }

        $this->info("Tüm veriler başarıyla aktarıldı.");
        return Command::SUCCESS;
    }
}
