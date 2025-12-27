<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiparisUrunu extends Model
{
    protected $table = 'siparis_urunleri';

    public function siparis()
    {
        return $this->belongsTo(Siparis::class, 'siparis_id');
    }
}
