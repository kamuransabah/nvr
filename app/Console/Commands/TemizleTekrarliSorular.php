<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TemizleTekrarliSorular extends Command
{
    protected $signature = 'temizle:tekrarli-sorular';
    protected $description = 'Aynı kurs_id, bolum ve soru içeriğine sahip tekrar eden sorulardan sadece birini bırakır.';

    public function handle()
    {
        $this->info("Tekrar eden sorular taranıyor...");

        $gruplar = DB::table('sorular')
            ->select('kurs_id', 'bolum', 'soru', DB::raw('COUNT(*) as adet'))
            ->groupBy('kurs_id', 'bolum', 'soru')
            ->having('adet', '>', 1)
            ->get();

        if ($gruplar->isEmpty()) {
            $this->info("Tekrar eden kayıt bulunamadı.");
            return Command::SUCCESS;
        }

        foreach ($gruplar as $g) {
            $ids = DB::table('sorular')
                ->where('kurs_id', $g->kurs_id)
                ->where('bolum', $g->bolum)
                ->where('soru', $g->soru)
                ->orderBy('id')
                ->pluck('id');

            $toDelete = $ids->slice(1); // ilkini bırak, diğerlerini sil

            DB::table('sorular')->whereIn('id', $toDelete)->delete();

            $this->line("Silinen " . $toDelete->count() . " kayıt. (Kurs: {$g->kurs_id}, Bolum: {$g->bolum})");
        }

        $this->info("Tekrar eden sorular başarıyla temizlendi.");
        return Command::SUCCESS;
    }
}
