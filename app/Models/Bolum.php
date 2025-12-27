<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bolum extends Model
{
    protected $table = 'bolumler';

    protected $fillable = ['bolum_adi','kurs_id', 'permalink', 'aciklama', 'durum', 'sira'];

}
