<?php

namespace App\Http\Controllers;

use App\Models\Adres_Il;
use App\Models\Adres_Ilce;
use Illuminate\Http\Request;

class AdresController extends Controller
{
    public function getIller()
    {
        return Adres_Il::orderBy('sira')->get(['id', 'il']);
    }

    public function getIlceler($il_id)
    {
        return Adres_Ilce::where('il', $il_id)
            ->orderBy('sira')
            ->get(['id', 'ilce']);
    }
}


