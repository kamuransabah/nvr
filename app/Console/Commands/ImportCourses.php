<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Uygulama Komutu: php artisan import:courses --offset=0 --limit=10000
 */

class ImportCourses extends Command
{
    protected $signature = 'import:courses {--offset=0} {--limit=10000}';
    protected $description = 'Import courses from external API to Laravel';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_kurs/$offset/$limit";
        $this->info("Fetching data from $url...");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("Failed to fetch data from $url");
            return;
        }

        $data = $response->json();
        if (empty($data)) {
            $this->info("No data to import.");
            return;
        }

        $this->info("Processing data and inserting into database...");

        foreach ($data as $course) {
            // SEO alanını JSON olarak decode et
            $seoData = [];
            if (!empty($course['seo']) && is_string($course['seo'])) {
                $seoData = json_decode($course['seo'], true);

                // JSON hatalarını kontrol et
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Invalid JSON data for course ID {$course['id']}");
                    $seoData = [];
                }
            }

            // SEO title ve description'ı al ve maksimum 250 karaktere sınırla
            $seoTitle = isset($seoData['title']) ? substr($seoData['title'], 0, 250) : null;
            $seoDescription = isset($seoData['description']) ? substr($seoData['description'], 0, 250) : null;

            DB::table('kurslar')->updateOrInsert(
                ['id' => $course['id']], // Benzersiz kontrol
                [
                    'permalink'   => $course['permalink'],
                    'kategori_id' => $course['kategori'],
                    'kurs_adi' => $course['kurs_adi'],
                    'ozet' => $course['ozet'],
                    'aciklama' => $course['aciklama'],
                    'neler_ogrenecegim' => $course['neler_ogrenecegim'],
                    'gereksinimler' => $course['gereksinimler'],
                    'kurs_icerigi' => $course['kurs_icerigi'],
                    'kurs_puani' => $course['kurs_puani'],
                    'label' => $course['label'],
                    'fiyat' => $course['fiyat'],
                    'ucretsiz' => $course['ucretsiz'],
                    'egitim_suresi' => $course['egitim_suresi'],
                    'egitim_sureci' => $course['egitim_sureci'],
                    'sertifika_turu' => $course['sertifika'],
                    'kitap_destegi' => $course['kitap_destegi'],
                    'sinav_basari_orani' => $course['sinav_basari_orani'],
                    'ders_sayisi' => $course['ders_sayisi'],
                    'egitim_seviyesi' => $course['egitim_seviyesi'],
                    'belgeler' => convertSerializeToJson($course['belgeler'] ?? null),
                    'resim' => $course['resim'],
                    'sertifika_ornegi' => $course['sertifika_ornegi'],
                    'sira' => $course['sira'],
                    'tur' => $course['tur'],
                    'seo_title' => $seoTitle,
                    'seo_description' => $seoDescription,
                    'durum' => $course['durum'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info("Courses imported successfully from offset $offset.");
    }
}
