<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Egitmen extends Model
{
    protected $table = 'egitmen';

    protected $fillable = [
        'user_id',
        'ozgecmis',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function kurslar()
    {
        return $this->hasMany(EgitmenKurs::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
