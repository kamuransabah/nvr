<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Data extends Model
{

    protected $table = 'data';

    protected $fillable = [
        'kurs_id',
        'cm_id',
        'kaynak',
        'isim',
        'sehir',
        'eposta',
        'telefon',
        'basvuru_tarihi',
        'atama_tarihi',
        'olumsuz_id',
        'cevapsiz',
        'durum',
    ];

    protected $casts = [
        'kurs_id' => 'integer',
        'cm_id' => 'integer',
        'kaynak' => 'integer',
        'olumsuz_id' => 'integer',
        'cevapsiz' => 'integer',
        'durum' => 'integer',
        'basvuru_tarihi' => 'datetime',
        'atama_tarihi' => 'datetime',
    ];

    public function kurs()
    {
        return $this->belongsTo(Kurs::class, 'kurs_id', 'id');
    }

    public function dataDurum()
    {
        return $this->hasOne(Setting::class, 'key', 'durum')
            ->where('type', 'data_durum')
            ->select('key', 'value');
    }


    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id')->withDefault([
            'isim' => '',
            'soyisim' => ''
        ]);
    }

    public function olumsuzNedeni()
    {
        return $this->hasOne(Setting::class, 'key', 'olumsuz_id')
            ->where('type', 'data_olumsuz')
            ->select('key', 'value');
    }


}
