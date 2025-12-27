<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DersLog extends Model {
    protected $table = 'ders_loglari';

    protected $fillable = [
        'ogrenci_id',
        'kurs_id',
        'ders_id',
        'ilk_izleme',
        'son_izleme',
        'ip_adresi_ilk',
        'ip_adresi_son',
        'izledigi_sure',
        'nerede_kaldi'
    ];

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id');
    }

    public function ders()
    {
        return $this->belongsTo(Ders::class, 'ders_id');
    }

}
