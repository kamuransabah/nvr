<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:iletisim
 * */
class ImportIletisim extends Command
{
    protected $signature = 'import:iletisim';
    protected $description = 'Import iletisim from the old system';

    public function handle()
    {
        //$response = Http::get($url);
        $response = Http::withOptions(['verify' => false])->get('https://www.novarge.com.tr/bridge/export/iletisim');


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
            DB::table('iletisim')->insertOrIgnore([
                'id' => $row['id'],
                'isim' => $row['isim'],
                'soyisim' => $row['soyisim'],
                'eposta' => $row['eposta'],
                'telefon' => $row['telefon'],
                'mesaj' => $row['mesaj'],
                'dosya' => $row['dosya'],
                'ip_adresi' => $row['ip_adresi'],
                'cevap' => $row['cevap'],
                'created_at' => $row['tarih'],
                'updated_at' => $row['tarih'],
                'durum' => $row['durum']
            ]);
        }


        $this->info('Data imported successfully.');
    }
}
