<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * 30.05.2025 itibariyle kullandığım kod:
 * php artisan guncelle:ogrenci-cevaplar-json --offset=40000 --limit=10000
 * */

class GuncelleOgrenciCevaplarJson extends Command {
    protected $signature = 'guncelle:ogrenci-cevaplar-json {--offset=0} {--limit=1000}';
    protected $description = 'CI3 üzerinden gelen cevaplar ve sınav sorularına göre ogrenci_sinavlari.cevaplar alanını günceller';

    public function handle() {
        $offset = (int)$this->option('offset');
        $limit = (int)$this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export_user_sinav_with_sorular/{$offset}/{$limit}";
        $this->info("Veriler şu adresten çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if($response->failed()) {
            $this->error("Veri alınamadı. HTTP Hatası: " . $response->status());
            return Command::FAILURE;
        }

        $veriler = $response->json();
        if(empty($veriler)) {
            $this->info("Taşınacak veri bulunamadı.");
            return Command::SUCCESS;
        }

        $sayac = 0;

        foreach($veriler as $item) {
            if(empty($item['cevaplar']) || empty($item['sinav_sorular'])) {
                continue;
            }

            $cevaplar = json_decode($item['cevaplar'], true);
            $sorular = json_decode($item['sinav_sorular'], true);

            if(!is_array($cevaplar) || !is_array($sorular)) {
                continue;
            }

            $map = [];

            foreach($cevaplar as $index => $harf) {
                if($harf !== '0' && isset($sorular[$index])) {
                    $map[$sorular[$index]] = $harf;
                }
            }

            if(empty($map)) {
                continue;
            }

            DB::table('ogrenci_sinavlari')->where('id', $item['id']) // crm_user_sinav.id = ogrenci_sinavlari.id
                ->update(['cevaplar' => json_encode($map)]);

            $sayac++;
        }

        $this->info("Toplam {$sayac} ogrenci_sinavlari kaydı güncellendi.");
        return Command::SUCCESS;
    }
}
