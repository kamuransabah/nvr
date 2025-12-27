<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:sayfa --offset=0 --limit=10000
 * */
class ImportSayfa extends Command
{
    protected $signature = 'import:sayfa {--offset=0} {--limit=10000}';
    protected $description = 'Import sayfa from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/icerik/$offset/$limit";
        $this->info("Fetching data from $url...");

        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get('https://www.novarge.com.tr/bridge/export/icerik/0/10000');


        if ($response->failed()) {
            $this->error("Failed to fetch data.");
            return;
        }

        $data = $response->json();
        if (empty($data)) {
            $this->info("No data to import.");
            return;
        }

        $this->info("Transforming and importing data...");

        foreach ($data as $row) {
            if (!empty($row['seo'])) {
                try {
                    $seoData = json_decode($row['seo'], true); // JSON olarak decode et
                } catch (\Exception $e) {
                    $this->error("Invalid JSON data for SEO: " . $row['seo']);
                    $seoData = [];
                }
            }

            // seo_description uzunluğunu 230 karakterle sınırlıyoruz
            $seoDescription = $seoData['description'] ?? null;
            if (!is_null($seoDescription)) {
                $seoDescription = substr($seoDescription, 0, 230);
                $seoDescription = mb_convert_encoding($seoDescription, 'UTF-8', 'auto');
            }
            DB::table('sayfa')->insertOrIgnore([
                'id' => $row['id'],
                'kategori_id' => $row['kategori'],
                'permalink' => $row['permalink'],
                'baslik' => $row['baslik'],
                'icerik' => $row['icerik'],
                'resim' => $row['resim'],
                'created_at' => $row['tarih'],
                'updated_at' => $row['tarih'],
                'seo_title' => $seoData['title'] ?? null,
                'seo_description' => $seoDescription ?? null,
                'durum' => $row['durum'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
