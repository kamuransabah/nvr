<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgitmenKurs extends Model
{
    protected $table = 'egitmen_kurs';

    protected $fillable = [
        'user_id',
        'kurs_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
