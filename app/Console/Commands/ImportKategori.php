<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:kategori
 * */
class ImportKategori extends Command
{
    protected $signature = 'import:kategori';
    protected $description = 'Import categories from the old system';

    public function handle()
    {
        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get('https://www.novarge.com.tr/bridge/export/crm_kategori');


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
            DB::table('kategori')->insertOrIgnore([
                'id' => $row['id'],
                'ust_id' => $row['ust_id'],
                'permalink' => $row['permalink'],
                'isim' => $row['isim'],
                'aciklama' => $row['aciklama'],
                'tur' => $row['tur'],
                'sira' => $row['sira'],
                'durum' => $row['durum'],
            ]);
        }


        $this->info('Categories imported successfully.');
    }
}
