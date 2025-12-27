<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $table;

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
