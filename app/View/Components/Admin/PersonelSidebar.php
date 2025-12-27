<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonelSidebar extends Component
{
    public function __construct(
        public $profil,
        public $personel = null,
        public $class = ''
    ){}

    public function render(): View|Closure|string
    {
        return view('components.admin.personel.sidebar');
    }
}
