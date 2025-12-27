<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adres_Il extends Model
{
    protected $table = 'adres_il';
    protected $fillable = ['id', 'ulke', 'il', 'telefon_kodu', 'sira'];

    public function ilceler()
    {
        return $this->hasMany(Adres_Ilce::class, 'il', 'id');
    }
}
