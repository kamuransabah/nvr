<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siparis extends Model
{
    protected $table = 'siparisler';

    protected $fillable = [
        'siparis_no', 'user_id', 'personel_id', 'toplam_tutar',
        'indirim_tutari', 'odenecek_tutar', 'odeme_durum',
        'odeme_turu', 'satis_kaynak', 'kargo_turu', 'durum', 'ip_adresi',
        'odeme_tarihi', 'personel_notu', 'user_agent'
    ];

    public function urunler()
    {
        return $this->hasMany(SiparisUrunu::class, 'siparis_id');
    }

    public function urunListesi(): array
    {
        $liste = [];

        foreach ($this->urunler as $u) {
            $model = null;
            $baslik = 'Ürün';
            $resim = 'noimage.png';

            if ($u->satis_turu == 1) {
                $model = Kurs::find($u->urun_id);
                $baslik = $model->kurs_adi ?? 'Kurs';
                $resim = $model->resim ?? 'noimage.png';
                $resim = asset('storage/' . config('upload.kurs.path') . '/thumb/' . $resim);
            } elseif ($u->satis_turu == 2) {
                $model = Urun::find($u->urun_id);
                $baslik = $model->isim ?? 'Ürün';
                $resim = $model->resim ?? 'noimage.png';
                $resim = asset('storage/' . config('upload.urun.path') . '/thumb/' . $resim);
            }

            $liste[] = [
                'id'          => $u->id,
                'satis_turu'  => $u->satis_turu,
                'adet'        => $u->adet,
                'birim_fiyat' => $u->birim_fiyat,
                'toplam'      => $u->toplam,
                'kdv'         => $model?->kdv_orani ?? 0,
                'baslik'      => $baslik,
                'resim'       => $resim,
            ];
        }

        return $liste;
    }

    public function uye()
    {
        return $this->belongsTo(Ogrenci::class, 'user_id', 'id');
    }

    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id', 'id');
    }

    public function odemeDurum()
    {
        return $this->belongsTo(Setting::class, 'odeme_durum', 'key')
            ->where('type', 'odeme_durum')
            ->select('key', 'value');
    }

    public function odemeTuru()
    {
        return $this->belongsTo(Setting::class, 'odeme_turu', 'key')
            ->where('type', 'odeme_turu')
            ->select('key', 'value');
    }

    public function siparisDurum()
    {
        return $this->belongsTo(Setting::class, 'durum', 'key')
            ->where('type', 'siparis_durum')
            ->select('key', 'value');
    }

    public function kaynak()
    {
        return $this->belongsTo(Setting::class, 'satis_kaynak', 'key')
            ->where('type', 'satis_kaynak')
            ->select('key', 'value');
    }

    public function getToplamSiparisTutariAttribute(): float
    {
        return collect($this->urunListesi())->sum('toplam');
    }

    public function gecmis()
    {
        return $this->hasMany(SiparisGecmisi::class, 'siparis_id')->latest();
    }
}
