<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Sinav;


/*
 * Uygulama Komutu
  php artisan import:sinavlar --offset=0 --limit=10000
 * */


class ImportSinavlar extends Command
{
    protected $signature = 'import:sinavlar {--offset=0} {--limit=10000}';
    protected $description = 'CI3 API üzerinden sinav verilerini alır ve Laravel veritabanına kaydeder.';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/sinav/{$offset}/{$limit}";
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

        $this->info(count($data) . " sınav verisi alındı. Aktarma işlemi başlıyor...");

        foreach ($data as $row) {
            Sinav::updateOrCreate(
                ['id' => $row['id']],
                [
                    'kurs_id'          => $row['kurs_id'],
                    'sinav_adi'        => $row['sinav_adi'],
                    'sinav_tarih'      => $row['sinav_tarih'],
                    'sinav_saat'       => $row['sinav_saat'],
                    'sinav_yer'        => $row['sinav_yer'],
                    'sinav_sure'       => $row['sinav_sure'],
                    'tur'              => $row['tur'],
                    'baslangic_tarihi' => $row['baslangic_tarihi'],
                    'bitis_tarihi'     => $row['bitis_tarihi'],
                    'sira'             => $row['sira'],
                    'otosinav'         => $row['otosinav'],
                    'durum'            => $row['durum'],
                ]
            );

            //$this->info("Sınav ID {$row['id']} başarıyla aktarıldı.");
        }

        $this->info("Tüm veriler başarıyla aktarıldı.");
        return Command::SUCCESS;
    }
}
