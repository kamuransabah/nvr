<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OgrenciNavbar extends Component
{
    public $ogrenci;
    public $personel;

    public function __construct($ogrenci, $personel)
    {
        $this->ogrenci = $ogrenci;
        $this->personel = $personel;
    }

    public function render(): View|Closure|string
    {
        return view('components.admin.ogrenci-navbar');
    }
}
