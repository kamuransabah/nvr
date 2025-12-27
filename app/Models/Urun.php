<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urun extends Model
{
    protected $table = 'urunler';

    protected $fillable = [
        'isim',
        'slug',
        'aciklama',
        'fiyat',
        'stok',
        'resim',
        'aktif',
    ];
}
