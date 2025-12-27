<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataGorusme extends Model
{
    protected $table = 'data_gorusmeler';

    protected $fillable = [
        'personel_id',
        'kurs_id',
        'data_id',
        'olumsuz_id',
        'randevu_id',
        'kayit',
        'personel_notu',
    ];
}
