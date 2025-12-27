<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personel extends Model
{
    protected $table = 'personel';

    protected $fillable = [
        'user_id',
        'sirket_telefon',
        'sirket_email',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
