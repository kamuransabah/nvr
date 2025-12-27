<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';
    protected $fillable = ['baslik', 'kategori_id', 'permalink', 'tarih', 'hit', 'durum'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function kurslar()
    {
        $kursIds = json_decode($this->kurs_id, true) ?? [];
        return Kurs::whereIn('id', $kursIds)->get();
    }
}
