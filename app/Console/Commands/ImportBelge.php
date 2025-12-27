<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:belge --offset=0 --limit=10000
 * */
class ImportBelge extends Command
{
    protected $signature = 'import:belge {--offset=0} {--limit=10000}';
    protected $description = 'Import belge from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/belge/$offset/$limit";
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

            DB::table('belgeler')->insertOrIgnore([
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'belge' => $row['belge'],
                'aciklama' => $row['not'],
                'created_at' => $row['tarih'],
                'updated_at' => null,
                'tur' => $row['tur'],
                'durum' => $row['durum'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
