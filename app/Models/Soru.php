<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soru extends Model
{
    protected $table = 'sorular';
    protected $fillable = [
        'kurs_id', 'bolum', 'soru', 'cevap', 'durum'
    ];

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id', 'id');
    }

    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id', 'id');
    }
}

