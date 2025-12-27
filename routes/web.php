<?php

use App\Http\Controllers\Crm\AuthController;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\AdresController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ogrenci-test', function () {
    if (Auth::guard('ogrenci')->check()) {
        dd('Öğrenci giriş yaptı', Auth::guard('ogrenci')->user());
    } else {
        dd('Öğrenci giriş yapmadı');
    }
});

// Yetkili kullanıcı giriş sistemi
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    Lfm::routes();
});

Route::get('/check', function () {
    return auth()->user()->hasRole('admin') ? 'Admin yetkisi var' : 'Admin yetkisi yok';
});

Route::middleware(['auth', 'role:personel'])->group(function () {
    Route::get('/personel/dashboard', function () {
        return 'Personel Dashboard';
    });
});

Route::get('/adres/iller', [AdresController::class, 'getIller']);
Route::get('/adres/ilceler/{il_id}', [AdresController::class, 'getIlceler']);

use Vimeo\Laravel\Facades\Vimeo;

Route::get('/vimeo-test', function () {
    $res = Vimeo::request('/me/videos', [
        'per_page' => 5,
        'sort'     => 'date',
        'direction'=> 'desc',
        'fields'   => 'uri,name,link'
    ], 'GET');

    return response()->json($res['body']);
});
