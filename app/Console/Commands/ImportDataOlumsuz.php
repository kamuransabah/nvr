<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:dataolumsuz
 * */

class ImportDataOlumsuz extends Command
{
    protected $signature = 'import:dataolumsuz';
    protected $description = 'Import olumsuz data from the old system';

    public function handle()
    {
        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get('https://www.novarge.com.tr/bridge/export/crm_data_red');


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
            DB::table('data_olumsuz_nedenler')->insertOrIgnore([
                'id' => $row['id'],
                'isim' => $row['isim'],
                'mesaj' => $row['mesaj']
            ]);
        }


        $this->info('Data Olumsuz imported successfully.');
    }
}
