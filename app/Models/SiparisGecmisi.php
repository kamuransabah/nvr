<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiparisGecmisi extends Model
{
    protected $table = 'siparis_gecmisi';

    public function siparis()
    {
        return $this->belongsTo(Siparis::class);
    }

    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id'); // Eğer personel başka tabloda ise değiştir
    }

    public function siparisDurum()
    {
        return $this->belongsTo(Setting::class, 'durum', 'key')
            ->where('type', 'siparis_durum')
            ->select('key', 'value');
    }
}
