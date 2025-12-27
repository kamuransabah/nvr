<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:bolum --offset=0 --limit=10000
 * */

class ImportBolum extends Command
{
    protected $signature = 'import:bolum {--offset=0} {--limit=10000}';
    protected $description = 'Import bolum from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_kurs_bolum/$offset/$limit";
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

            DB::table('bolumler')->insertOrIgnore([
                'id' => $row['id'],
                'kurs_id' => $row['kurs_id'],
                'bolum_adi' => $row['isim'],
                'permalink' => $row['permalink'],
                'aciklama' => $row['aciklama'],
                'sira' => $row['sira'],
                'durum' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
