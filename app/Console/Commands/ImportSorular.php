<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Soru;
use App\Models\Secenek;

/*
 * Uygulama Komutu
  php artisan import:sorular --offset=0 --limit=10000
 * */

class ImportSorular extends Command
{
    protected $signature = 'import:sorular {--offset=0} {--limit=10000}';
    protected $description = 'CI3 API üzerinden soruları ve seçenekleri getirir, Laravel sistemine aktarır.';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/sorular/{$offset}/{$limit}";
        $this->info("Veriler şu adresten çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("Veri alınamadı. HTTP Hatası: " . $response->status());
            return Command::FAILURE;
        }

        $data = $response->json();

        if (empty($data)) {
            $this->info("Aktarılacak soru verisi bulunamadı.");
            return Command::SUCCESS;
        }

        $this->info(count($data) . " soru verisi alındı. Aktarma başlıyor...");

        foreach ($data as $row) {
            DB::table('sorular')->insertOrIgnore([
                'id'         => $row['id'],
                'kurs_id'    => $row['kurs_id'],
                'bolum_id'      => $row['bolum'],
                'soru'       => $row['soru'],
                'cevap'      => $row['cevap'],
                'durum'      => $row['durum'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $secenekler = $this->parseSecenekler($row['secenekler']);
            $dogru = strtoupper($row['cevap']);

            if ($secenekler) {
                foreach ($secenekler as $item) {
                    \App\Models\Secenek::updateOrCreate(
                        [
                            'soru_id' => $row['id'],
                            'harf'    => $item['harf'] ?? '',
                            'secenek' => $item['secenek'] ?? '',
                        ],
                        [
                            'resim'     => $item['resim'] ?? null,
                            'dogru_mu'  => $dogru === strtoupper($item['harf']),
                        ]
                    );
                }
            }
        }

        $this->info("Tüm sorular ve seçenekler başarıyla aktarıldı.");
        return Command::SUCCESS;
    }


    protected function parseSecenekler(string $raw)
    {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // JSON değilse serialize olup olmadığını test et
        if (@unserialize($raw) !== false) {
            return unserialize($raw);
        }

        $this->warn("Seçenekler parse edilemedi.");
        return null;
    }
}
