<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataOlumsuz extends Model
{
    protected $table = 'data_olumsuz';
    protected $fillable = ['isim', 'mesaj'];

    public function dataKayitlari()
    {
        return $this->hasMany(Data::class, 'olumsuz_id');
    }
}
