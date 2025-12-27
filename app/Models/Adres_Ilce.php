<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adres_Ilce extends Model
{
    protected $table = 'adres_ilce';
    protected $fillable = ['id', 'il', 'ilce', 'sira'];

    public function il()
    {
        return $this->belongsTo(Adres_Il::class, 'il', 'id');
    }

}
