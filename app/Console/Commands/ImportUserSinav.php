<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:ogrenci-sinavlari --offset=0 --limit=10000
 * */

class ImportUserSinav extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ogrenci-sinavlari {--offset=0} {--limit=10000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ogrenci-sinavlari table import from CI3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_user_sinav/$offset/$limit";
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
            DB::table('ogrenci_sinavlari')->insertOrIgnore([
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'sinav_id' => $row['sinav_id'],
                'kurs_id' => $row['kurs_id'],
                'sonuc' => $row['sonuc'],
                'puan' => $row['not'],
                'dogru_cevap' => $row['dogru_cevap'],
                'yalnis_cevap' => $row['yalnis_cevap'],
                'bos_cevap' => $row['bos_cevap'],
                'durum' => $row['durum'],
                'sinav_tarihi' => $row['tarih'],
                'created_at' => $row['tarih'],
                'updated_at' => null
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
