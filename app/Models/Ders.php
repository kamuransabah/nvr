<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ders extends Model
{
    protected $table = 'dersler';

    protected $fillable = [
        'kurs_id',
        'bolum_id',
        'egitmen_id',
        'demo',
        'permalink',
        'baslik',
        'ozet',
        'icerik',
        'ders_suresi',
        'video_kaynak_id',
        'dosya',
        'resim',
        'sira',
        'durum'
    ];
}
