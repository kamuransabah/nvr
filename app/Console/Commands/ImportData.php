<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu (298.000 tane veri çektim)
  php artisan import:data --offset=290000 --limit=10000
 * */
class ImportData extends Command
{
    protected $signature = 'import:data {--offset=0} {--limit=10000}';
    protected $description = 'Import data from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_data/$offset/$limit";
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
            DB::table('data')->insertOrIgnore([
                'id' => $row['id'],
                'kurs_id' => $row['kurs_id'],
                'personel_id' => $row['cm_id'],
                'kaynak' => $row['kaynak'],
                'isim' => $row['isim'],
                'sehir' => $row['sehir'],
                'eposta' => $row['eposta'],
                'telefon' => $row['telefon'],
                'basvuru_tarihi' => $row['tarih'],
                'atama_tarihi' => $row['atama_tarihi'],
                'olumsuz_id' => $row['olumsuz_id'],
                'cevapsiz' => $row['cevapsiz'],
                'durum' => $row['durum'],
                'created_at' => $row['yuklenme_tarihi'],
                'updated_at' => $row['atama_tarihi'],
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
