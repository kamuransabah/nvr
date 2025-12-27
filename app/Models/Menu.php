<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = ['isim', 'ust_id', 'link', 'tur', 'durum'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'ust_id', 'id')->orderBy('sira');
    }
}
