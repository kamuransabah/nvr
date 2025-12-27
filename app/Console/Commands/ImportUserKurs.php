<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:ogrenci-kurslari --offset=0 --limit=10000
 * */

class ImportUserKurs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ogrenci-kurslari {--offset=0} {--limit=10000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ogrenci-kurslari table import from CI3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_user_kurs/$offset/$limit";
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
            DB::table('ogrenci_kurslari')->insertOrIgnore([
                'id' => $row['id'],
                'ogrenci_id' => $row['user_id'],
                'kurs_id' => $row['kurs_id'],
                'personel_id' => $row['cm_id'],
                'tarih_baslangic' => $row['tarih_baslangic'],
                'tarih_bitis' => $row['tarih_bitis'],
                'sinav_hakki' => $row['sinav_hakki'],
                'sinav_tercihi' => $row['sinav_tercihi'],
                'sertifika_turu' => $row['sertifika_turu'],
                'sozlesme' => $row['sozlesme'],
                'sozlesme_tarihi' => $row['sozlesme_tarihi'],
                'sozlesme_ip' => $row['sozlesme_ip'],
                'durum' => $row['durum'],
                'created_at' => isset($row['tarih_baslangic']) ? $row['tarih_baslangic'] . ' 00:00:00' : now(),
                'updated_at' => null
            ]);
        }

        $this->info("Data import complete for offset $offset.");
    }
}
