<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sinav extends Model
{
    protected $table = 'sinavlar';

    protected $fillable = [
        'kurs_id',
        'sinav_adi',
        'sinav_tarih',
        'sinav_saat',
        'sinav_yer',
        'sinav_sure',
        'tur',
        'baslangic_tarihi',
        'bitis_tarihi',
        'sira',
        'otosinav',
        'durum',
    ];

    public function sinavTuru()
    {
        return $this->belongsTo(Setting::class, 'tur', 'key')
            ->where('type', 'sinav_turu')
            ->select('key', 'value')
            ->withDefault(['value' => null]); // ilişki yoksa null değer dönsün
    }

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id', 'id');
    }
}
