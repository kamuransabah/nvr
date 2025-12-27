<?php
use Illuminate\Support\Facades\DB;

if (!function_exists('kursLog')) {
    function kursLog($kurs_id, $ogrenci_id): float
    {
        // Toplam ders süresi (saniye)
        $toplamSure = DB::table('dersler')
            ->where('kurs_id', $kurs_id)
            ->sum('ders_suresi');

        // Öğrencinin izlediği süre
        $izlenenSure = DB::table('ders_loglari')
            ->where('kurs_id', $kurs_id)
            ->where('ogrenci_id', $ogrenci_id)
            ->sum('izledigi_sure');

        // Yüzde olarak hesapla
        if ($toplamSure == 0) return 0;

        return round(($izlenenSure / $toplamSure) * 100, 2);
    }
}
