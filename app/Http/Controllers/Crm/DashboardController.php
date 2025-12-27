<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        return view(theme_view('admin', 'pages.dashboard.admin'));
    }

    public function personel()
    {
        return view(theme_view('admin', 'pages.dashboard.personel'));
    }
}
