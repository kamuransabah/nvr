<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kurs extends Model
{
    use SoftDeletes;

    protected $table = 'kurslar'; // Tablonun adı
    protected $primaryKey = 'id'; // Birincil anahtar
    protected $casts = ['belgeler' => 'array', 'neler_ogrenecegim' => 'array', 'ozellikler' => 'array'];

    protected $fillable = [
        'kategori_id',
        'kurs_adi',
        'permalink',
        'ozet',
        'aciklama',
        'neler_ogrenecegim',
        'gereksinimler',
        'kurs_icerigi',
        'gecme_notu',
        'kurs_puani',
        'label',
        'fiyat',
        'kdv_orani',
        'ucretsiz',
        'egitim_suresi',
        'egitim_sureci',
        'sertifika',
        'kitap_destegi',
        'sinav_basari_orani',
        'ders_sayisi',
        'egitim_seviyesi',
        'belgeler',
        'resim',
        'sertifika_ornegi',
        'sira',
        'tur',
        'seo_title',
        'seo_description',
        'durum',
    ];

    public function bloglar()
    {
        return $this->belongsToMany(Blog::class, 'blog', 'kurs_id', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function getDefaultKdvOraniAttribute()
    {
        return Setting::where('type', 'kdv_orani')
            ->where('key', 'kurs')
            ->value('value');
    }

    public function belgeTurleri()
    {
        return $this->hasOne(Setting::class, 'key', 'tur')
            ->where('type', 'belge_turleri')
            ->select('key', 'value');
    }

    public function getRepeaterFormat(string $field): array
    {
        $data = $this->{$field} ?? [];

        return collect($data)
            ->map(fn($v) => ['value' => $v])
            ->values()
            ->all();
    }
}
