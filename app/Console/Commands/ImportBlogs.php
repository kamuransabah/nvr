<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:blog --offset=0 --limit=10000
 * */
class ImportBlogs extends Command
{
    protected $signature = 'import:blogs {--offset=0} {--limit=10000}';
    protected $description = 'Import blogs from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/blog/$offset/$limit";
        $this->info("Fetching data from $url...");

        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get($url);


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
            DB::table('blog')->insertOrIgnore([
                'id' => $row['id'],
                'kategori_id' => $row['kategori'],
                'permalink' => $row['permalink'],
                'kurs_id' => $row['kurs_id'],
                'baslik' => $row['baslik'],
                'ozet' => $row['ozet'],
                'icerik' => $row['icerik'],
                'resim' => $row['resim'],
                'detay_resim' => $row['detay_resim'],
                'yayin_tarihi' => $row['tarih'],
                'created_at' => $row['tarih'],
                'updated_at' => $row['tarih'],
                'hit' => $row['hit'],
                'seo_title' => $seoData['title'] ?? null,
                'seo_description' => $seoDescription ?? null,
                'tur' => $row['tur'],
                'durum' => $row['durum'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
