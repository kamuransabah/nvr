<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secenek extends Model
{
    protected $table = 'secenekler';

    protected $fillable = [
        'soru_id', 'harf', 'secenek', 'resim', 'dogru_mu'
    ];
}
