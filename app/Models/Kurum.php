<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurum extends Model
{
    protected $table = 'kurum';

    protected $fillable = [
        'user_id',
        'kurum_adi',
        'kurum_telefon',
        'kurum_logo',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
