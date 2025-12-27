<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Ogrenci extends Authenticatable
{
    use Notifiable;

    protected $table = 'ogrenciler'; // Tablo adı

    protected $fillable = [
        'isim',
        'soyisim',
        'email',
        'telefon',
        'password',
        'tc_kimlik_no',
        'cinsiyet',
        'dogum_tarihi',
        'kaynak',
        'mezuniyet',
        'meslek',
        'adres',
        'il_id',
        'ilce_id',
        'profil_resmi',
        'kayit_ip',
        'son_giris_tarihi',
        'son_giris_ip',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id')->withDefault([
            'isim' => '',
            'soyisim' => ''
        ]);
    }

    public function il()
    {
        return $this->belongsTo(Adres_Il::class, 'il_id');
    }

    public function ilce()
    {
        return $this->belongsTo(Adres_Ilce::class, 'ilce_id');
    }

    public function kayitKaynak()
    {
        return $this->hasOne(Setting::class, 'key', 'kaynak')
            ->where('type', 'ogrenci_kaynak')
            ->select('key', 'value');
    }

    public function ogrenciKurslari()
    {
        return $this->belongsTo(OgrenciKurslari::class, 'il_id');
    }

    public function mezuniyet()
    {
        return $this->hasOne(Setting::class, 'key', 'mezuniyet')
            ->where('type', 'mezuniyet')
            ->select('key', 'value');
    }

}
