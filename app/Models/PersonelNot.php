<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonelNot extends Model
{
    protected $table = 'personel_notlari';
    protected $fillable = ['personel_id', 'item_id', 'type', 'icerik', 'created_at'];

    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id');
    }
}
