<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Belge extends Model
{
    protected $table = 'belgeler';

    protected $fillable = [
        'user_id',
        'tur',
        'belge',
        'aciklama',
        'durum',
        'created_at',
        'updated_at',
    ];

    public function belgeTuru()
    {
        return $this->hasOne(Setting::class, 'key', 'tur')
            ->where('type', 'belge_turleri')
            ->select('key', 'value');
    }

    public function belgeDurum()
    {
        return $this->hasOne(Setting::class, 'key', 'durum')
            ->where('type', 'belge_durum')
            ->select('key', 'value');
    }
}
