<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TemizleTekrarliCevaplar extends Command
{
    protected $signature = 'temizle:tekrarli-cevaplar';
    protected $description = 'ogrenci_cevaplari tablosunda aynı ogrenci_sinav_id ve soru_id için birden fazla kayıt varsa sadece bir tanesini bırakır.';

    public function handle()
    {
        $this->info("Tekrar eden cevaplar taranıyor...");

        $gruplar = DB::table('ogrenci_cevaplari')
            ->select('ogrenci_sinav_id', 'soru_id', DB::raw('COUNT(*) as adet'))
            ->groupBy('ogrenci_sinav_id', 'soru_id')
            ->having('adet', '>', 1)
            ->get();

        if ($gruplar->isEmpty()) {
            $this->info("Tekrar eden kayıt bulunamadı.");
            return Command::SUCCESS;
        }

        foreach ($gruplar as $g) {
            $ids = DB::table('ogrenci_cevaplari')
                ->where('ogrenci_sinav_id', $g->ogrenci_sinav_id)
                ->where('soru_id', $g->soru_id)
                ->orderBy('id')
                ->pluck('id');

            $toDelete = $ids->slice(1); // ilkini bırak, diğerlerini sil

            DB::table('ogrenci_cevaplari')->whereIn('id', $toDelete)->delete();

            //$this->line("Silinen " . $toDelete->count() . " kayıt. (Sınav: {$g->ogrenci_sinav_id}, Soru: {$g->soru_id})");
        }

        $this->info("Tekrar eden öğrenci cevapları başarıyla temizlendi.");
        return Command::SUCCESS;
    }
}
