<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixOgrenciMezuniyet extends Command
{
    protected $signature = 'fix:ogrenci-mezuniyet';
    protected $description = 'Ogrenciler tablosundaki mezuniyet alanını settings tablosundaki key ile eşleştirerek günceller.';

    public function handle()
    {
        // 1. Settings tablosundan mezuniyet değerlerini al
        $mezuniyetMap = DB::table('settings')
            ->where('type', 'mezuniyet')
            ->pluck('key', 'value') // [ 'lise' => 3, 'önlisans' => 4, ... ]
            ->toArray();

        $this->info("Mezuniyet eşleştirme listesi yüklendi. Toplam: " . count($mezuniyetMap));

        // 2. ogrenciler tablosunu gez, mezuniyet string'ine göre key değerini bul
        $ogrenciler = DB::table('ogrenciler')
            ->whereNotNull('mezuniyet')
            ->get();

        $updatedCount = 0;

        foreach ($ogrenciler as $ogrenci) {
            $currentMezuniyet = trim($ogrenci->mezuniyet);

            // Özel durum: İlkokul ve Ortaokul => key = 1
            if (in_array(Str::lower($currentMezuniyet), ['İlkokul', 'ortaokul'])) {
                $newKey = 1;
            }
            // Normal mapping: settings tablosundan al
            elseif (!is_numeric($currentMezuniyet) && isset($mezuniyetMap[$currentMezuniyet])) {
                $newKey = $mezuniyetMap[$currentMezuniyet];
            } else {
                continue; // eşleşme yoksa atla
            }

            DB::table('ogrenciler')
                ->where('id', $ogrenci->id)
                ->update([
                    'mezuniyet' => $newKey,
                    'updated_at' => now(),
                ]);

            $updatedCount++;
            //$this->line("🟢 ID {$ogrenci->id} → '{$currentMezuniyet}' → {$newKey}");
        }


        $this->info("İşlem tamamlandı. Güncellenen kayıt sayısı: $updatedCount");
    }
}
