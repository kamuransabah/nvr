<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Sertifika;


/*
 * Uygulama Komutu
  php artisan import:sertifikalar --offset=0 --limit=10000
 * */


class ImportSertifika extends Command
{
    protected $signature = 'import:sertifikalar {--offset=0} {--limit=10000}';
    protected $description = 'CI3 API to Laravel';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/sertifika/{$offset}/{$limit}";
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
            DB::table('sertifikalar')->insertOrIgnore([
                'id' => $row['id'],
                'ogrenci_id' => $row['ogrenci_id'],
                'kurs_id' => $row['kurs_id'],
                'belge_turu' => $row['belge_turu'],
                'dosya' => $row['dosya'],
                'isim' => $row['isim'],
                'soyisim' => $row['soyisim'],
                'tarih' => $row['tarih'],
                'created_at' => $row['olusturma_tarihi'],
                'updated_at' => null
            ]);

        }

        $this->info("Tüm veriler başarıyla aktarıldı.");
        return Command::SUCCESS;
    }
}
