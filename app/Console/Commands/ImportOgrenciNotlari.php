<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:crm_user_not --offset=0 --limit=10000

4216 veri taşıdım
 * */
class ImportOgrenciNotlari extends Command
{
    protected $signature = 'import:crm_user_not {--offset=0} {--limit=10000}';
    protected $description = 'Import crm_user_not from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_user_not/$offset/$limit";
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

            DB::table('personel_notlari')->insertOrIgnore([
                'id' => $row['id'],
                'personel_id' => $row['olusturan'],
                'item_id' => $row['user_id'],
                'type' => 'ogrenciler',
                'icerik' => $row['not'],
                'created_at' => $row['tarih']
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
