<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifika extends Model
{
    protected $table = 'sertifikalar';

    protected $fillable = ['ogrenci_id', 'kurs_id', 'belge_turu', 'tur', 'sertifika_no', 'dosya', 'isim', 'soyisim', 'tarih', 'created_at'];

    public function sertifikaBelgeTuru()
    {
        return $this->hasOne(Setting::class, 'key', 'belge_turu')
            ->where('type', 'sertifika_turu')
            ->select('key', 'value');
    }

    public function sertifikaTuru()
    {
        return $this->hasOne(Setting::class, 'key', 'tur')
            ->where('type', 'sertifika_turu')
            ->select('key', 'value');
    }

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id');
    }
}
