<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sayfa extends Model
{
    protected $table = 'sayfa';
    protected $fillable = ['baslik', 'kategori_id', 'permalink', 'created_at', 'durum'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }
}
