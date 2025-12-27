<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportOgrenciCevaplari extends Command
{
    protected $signature = 'import:cevaplar {--offset=0} {--limit=1000}';
    protected $description = 'CI3 API üzerinden ogrenci_cevaplari verisini sorular ile birlikte aktarır';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export_user_sinav_with_sorular/{$offset}/{$limit}";
        $this->info("Veriler şu adresten çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("Veri alınamadı. HTTP Hatası: " . $response->status());
            return Command::FAILURE;
        }

        $veriler = $response->json();
        if (empty($veriler)) {
            $this->info("Taşınacak veri bulunamadı.");
            return Command::SUCCESS;
        }

        $sayac = 0;

        foreach ($veriler as $item) {
            $cevaplar = $this->parseDizi($item['cevaplar'] ?? '');
            $sorular = $this->parseDizi($item['sinav_sorular'] ?? '');

            if (!is_array($cevaplar) || !is_array($sorular)) {
                //$this->warn("Cevaplar veya sorular çözümlenemedi. ID: {$item['id']}");
                continue;
            }

            if (count($cevaplar) !== count($sorular)) {
                //$this->warn("Soru ve cevap sayısı eşleşmiyor. ID: {$item['id']}");
                continue;
            }

            foreach ($cevaplar as $index => $harf) {
                if ($harf === '0' || empty($harf)) continue;

                $soruId = (int) $sorular[$index];

                //$this->info("Cevaplar: " . json_encode($cevaplar));
                //$this->info("Sorular: " . json_encode($sorular));
                //$this->info("Eklenecek veri => SınavID: {$item['sinav_id']} | SoruID: $soruId | Harf: $harf");

                if (!\DB::table('ogrenci_sinavlari')->where('id', $item['id'])->exists()) {
                    //$this->warn("Atlanıyor: ogrenci_sinav_id bulunamadı ({$item['id']})");
                    continue;
                }

                DB::table('ogrenci_cevaplari')->insertOrIgnore([
                    'ogrenci_sinav_id' => $item['id'],
                    'sinav_id'         => $item['sinav_id'],
                    'soru_id'          => $soruId,
                    'secenek_harf'     => $harf,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
                $sayac++;
            }
        }

        $this->info("Toplam {$sayac} cevap başarıyla aktarıldı.");
        return Command::SUCCESS;
    }

    protected function parseDizi(string $raw): array|null
    {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $unserialized = @unserialize($raw);
        if ($unserialized !== false && is_array($unserialized)) {
            return $unserialized;
        }

        return null;
    }
}
