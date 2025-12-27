<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonelNavbar extends Component
{
    // $active: 'genel-bakis' | 'satislar' | 'performans' | 'gorusmeler' | 'iletisim'
    public function __construct(
        public $id,
        public $active = 'genel-bakis'
    ){}

    public function render(): View|Closure|string
    {
        return view('components.admin.personel.navbar');
    }
}
