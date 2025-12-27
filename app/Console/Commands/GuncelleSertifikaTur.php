<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sertifika;
use App\Models\OgrenciKursu;

/*
 * Bu komut tüm öğrenci kursları taşındıktan sonra ve sertifikalar taşındıktan sonra çalıştırılacak
 * php artisan sertifika:tur-guncelle
 * */
class GuncelleSertifikaTur extends Command
{
    protected $signature = 'sertifika:tur-guncelle';
    protected $description = 'Sertifikalar tablosundaki tur alanını ogrenci_kurslari tablosuna göre günceller';

    public function handle()
    {
        $this->info('Sertifika tur güncellemesi başlatıldı...');

        Sertifika::chunk(100, function ($sertifikalar) {
            foreach ($sertifikalar as $sertifika) {
                $ogrenciKurs = OgrenciKursu::where('kurs_id', $sertifika->kurs_id)
                    ->where('ogrenci_id', $sertifika->ogrenci_id)
                    ->first();

                if ($ogrenciKurs && $ogrenciKurs->sertifika_turu !== null) {
                    $sertifika->tur = $ogrenciKurs->sertifika_turu;
                    $sertifika->save();
                    //$this->line("Güncellendi: Sertifika ID {$sertifika->id}");
                }
            }
        });

        $this->info('Güncelleme tamamlandı.');
    }
}
