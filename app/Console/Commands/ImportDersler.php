<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Sertifika;


/*
 * Uygulama Komutu
  php artisan import:dersler --offset=0 --limit=10000
 * */


class ImportDersler extends Command
{
    protected $signature = 'import:dersler {--offset=0} {--limit=10000}';
    protected $description = 'CI3 API to Laravel';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/video/{$offset}/{$limit}";
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
            DB::table('dersler')->insertOrIgnore([
                'id' => $row['id'],
                'kurs_id' => $row['kurs_id'],
                'egitmen_id' => $row['egitmen_id'],
                'demo' => $row['demo'],
                'permalink' => $row['permalink'],
                'baslik' => $row['baslik'],
                'ozet' => $row['ozet'],
                'icerik' => $row['icerik'],
                'ders_suresi' => $row['sure'],
                'video_kaynak_id' => $row['video_kaynak_id'],
                'dosya' => $row['dosya'],
                'resim' => $row['resim'],
                'sira' => $row['sira'],
                'durum' => $row['durum'],
                'created_at' => $row['tarih'],
                'updated_at' => $row['tarih_guncelleme'],
            ]);

        }

        $this->info("Tüm veriler başarıyla aktarıldı.");
        return Command::SUCCESS;
    }
}
