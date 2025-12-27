<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = ['isim','tur', 'aciklama', 'durum', 'sira', 'ust_id'];

    public function children()
    {
        return $this->hasMany(Kategori::class, 'ust_id', 'id')->orderBy('sira');
    }

}
