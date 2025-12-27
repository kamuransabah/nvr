<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OgrenciKursu extends Model
{
    protected $table = 'ogrenci_kurslari';
    protected $fillable = ['id', 'ogrenci_id', 'personel_id', 'tarih_baslangic', 'tarih_bitis', 'sinav_tercihi', 'sertifika_turu', 'sozlesme', 'sozlesme_tarihi', 'sozlesme_ip', 'durum', 'created_at', 'updated_at'];

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id');
    }

    public function ogrenci()
    {
        return $this->belongsTo(Ogrenci::class, 'ogrenci_id');
    }

    public function sinavTercihi()
    {
        return $this->hasOne(Setting::class, 'key', 'sinav_tercihi')
            ->where('type', 'sinav_tercihi')
            ->select('key', 'value');
    }

    public function sertifikaTuru()
    {
        return $this->hasOne(Setting::class, 'key', 'sertifika_turu')
            ->where('type', 'sertifika_turu')
            ->select('key', 'value');
    }

}
