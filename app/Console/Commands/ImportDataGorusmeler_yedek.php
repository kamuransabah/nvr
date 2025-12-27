<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * 2024 den 07-09-2025 tarihine kadar 138.000 veri var.
 * Uygulama Komutu
  php artisan import:datagorusmeler --offset=0 --limit=10000
 * */

class ImportDataGorusmelerYedek extends Command
{
    protected $signature = 'import:datagorusmeler_yedek {--offset=0} {--limit=10000}';
    protected $description = 'Import data from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_data_gorusme/$offset/$limit";
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
            DB::table('data_gorusmeler')->insertOrIgnore([
                'id' => $row['id'],
                'data_id' => $row['data_id'],
                'kurs_id' => $row['kurs_id'],
                'personel_id' => $row['cm_id'],
                'randevu_id' => $row['randevu_id'],
                'olumsuz_id' => $row['red_id'],
                'kayit' => $row['kayit'],
                'personel_notu' => $row['not'],
                'created_at' => $row['tarih'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
