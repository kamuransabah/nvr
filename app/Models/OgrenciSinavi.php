<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OgrenciSinavi extends Model
{
    protected $table = 'ogrenci_sinavlari';
    protected $fillable = ['id', 'user_id', 'sinav_id', 'kurs_id', 'sonuc', 'cevaplar', 'puan', 'dogru_cevap', 'yalnis_cevap', 'bos_cevap', 'sinav_tarihi', 'durum', 'created_at', 'updated_at'];

    public function sinav()
    {
        return $this->belongsTo(Sinav::class, 'sinav_id');
    }

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id');
    }

}
