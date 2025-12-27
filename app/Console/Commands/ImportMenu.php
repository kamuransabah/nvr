<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:menu
 * */
class ImportMenu extends Command
{
    protected $signature = 'import:menu';
    protected $description = 'Import menu from the old system';

    public function handle()
    {
        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get('https://www.novarge.com.tr/bridge/export/menu');


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
            DB::table('menu')->insertOrIgnore([
                'id' => $row['id'],
                'ust_id' => $row['ust_id'],
                'tur' => $row['tur'],
                'isim' => $row['isim'],
                'link' => $row['link'],
                'sira' => $row['sira'],
                'durum' => $row['durum']
            ]);
        }


        $this->info('Categories imported successfully.');
    }
}
