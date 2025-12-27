<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:datarandevular --offset=100000 --limit=10000
 * */

class ImportDataRandevular extends Command
{
    protected $signature = 'import:datarandevular {--offset=0} {--limit=10000}';
    protected $description = 'Import data from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_data_randevu/$offset/$limit";
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
            DB::table('data_randevular')->insertOrIgnore([
                'id' => $row['id'],
                'data_id' => $row['data_id'],
                'kurs_id' => $row['kurs_id'],
                'personel_id' => $row['cm_id'],
                'randevu_tarihi' => $row['randevu_tarihi'],
                'durum' => $row['durum'],
                'created_at' => $row['tarih'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
