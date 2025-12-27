<?php

use App\Http\Controllers\Crm\SinavController;
use App\Http\Controllers\Crm\UserController;
use App\Http\Controllers\Crm\BlogController;
use App\Http\Controllers\Crm\BolumController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\DataController;
use App\Http\Controllers\Crm\DersController;
use App\Http\Controllers\Crm\KategoriController;
use App\Http\Controllers\Crm\KursController;
use App\Http\Controllers\Crm\MenuController;
use App\Http\Controllers\Crm\ModulController;
use App\Http\Controllers\Crm\OgrenciController;
use App\Http\Controllers\Crm\PersonelController;
use App\Http\Controllers\Crm\RoleController;
use App\Http\Controllers\crm\SayfaController;
use App\Http\Controllers\Crm\SiparisController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'Admin route is working!';
});

Route::get('/check', function () {
    return auth()->user()->hasRole('admin') ? 'Admin yetkisi var' : 'Admin yetkisi yok';
});

// Admin Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index')->middleware('permission:blog.view');;
    Route::get('/data', [BlogController::class, 'getBlogs'])->name('data')->middleware('permission:blog.view');
    Route::get('/add', [BlogController::class, 'add'])->name('add')->middleware('permission:blog.add');
    Route::post('/store', [BlogController::class, 'store'])->name('store')->middleware('permission:blog.add');
    Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('edit')->middleware('permission:blog.edit');
    Route::put('/update/{id}', [BlogController::class, 'update'])->name('update')->middleware('permission:blog.edit');
    Route::delete('/delete/{id}', [BlogController::class, 'delete'])->name('delete')->middleware('permission:blog.delete');
});

Route::prefix('sayfa')->name('sayfa.')->group(function () {
    Route::get('/', [SayfaController::class, 'index'])->name('index')->middleware('permission:sayfa.view');
    Route::get('/data', [SayfaController::class, 'getData'])->name('data')->middleware('permission:sayfa.view');
    Route::get('/add', [SayfaController::class, 'add'])->name('add')->middleware('permission:sayfa.add');
    Route::post('/store', [SayfaController::class, 'store'])->name('store')->middleware('permission:sayfa.add');
    Route::get('/edit/{id}', [SayfaController::class, 'edit'])->name('edit')->middleware('permission:sayfa.edit');
    Route::put('/update/{id}', [SayfaController::class, 'update'])->name('update')->middleware('permission:sayfa.edit');
    Route::delete('/delete/{id}', [SayfaController::class, 'delete'])->name('delete')->middleware('permission:sayfa.delete');
});

Route::prefix('kategori/{tur}')->name('kategori.')->group(function () {
    Route::get('/', [KategoriController::class, 'index'])->name('index')->middleware('permission:kategori.view');
    Route::post('/store', [KategoriController::class, 'store'])->name('store')->middleware('permission:kategori.add');
    Route::get('/edit/{id}', [KategoriController::class, 'edit'])->name('edit')->middleware('permission:kategori.edit');
    Route::put('/update/{id}', [KategoriController::class, 'update'])->name('update')->middleware('permission:kategori.edit');
    Route::delete('/delete/{id}', [KategoriController::class, 'delete'])->name('delete')->middleware('permission:kategori.delete');
    Route::post('/updateOrder', [KategoriController::class, 'updateOrder'])->name('updateOrder')->middleware('permission:kategori.edit');
});

Route::prefix('menu/{tur}')->name('menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index')->middleware('permission:menu.view');
    Route::post('/store', [MenuController::class, 'store'])->name('store')->middleware('permission:menu.add');
    Route::get('/edit/{id}', [MenuController::class, 'edit'])->name('edit')->middleware('permission:menu.edit');
    Route::put('/update/{id}', [MenuController::class, 'update'])->name('update')->middleware('permission:menu.edit');
    Route::delete('/delete/{id}', [MenuController::class, 'delete'])->name('delete')->middleware('permission:menu.delete');
    Route::post('/updateOrder', [MenuController::class, 'updateOrder'])->name('updateOrder')->middleware('permission:menu.edit');
});

Route::prefix('iletisim')->name('iletisim.')->group(function () {
    Route::get('/', [ModulController::class, 'iletisim'])->name('iletisim')->middleware('permission:iletisim.view');
    Route::get('/reply/{id}', [ModulController::class, 'iletisimReply'])->name('reply')->middleware('permission:iletisim.reply');
    Route::get('/data', [ModulController::class, 'iletisimData'])->name('data')->middleware('permission:iletisim.view');
    Route::delete('/delete/{id}', [ModulController::class, 'iletisimdelete'])->name('delete')->middleware('permission:iletisim.delete');
});

Route::prefix('data')->name('data.')->group(function () {
    Route::get('/', [DataController::class, 'index'])->name('index')->middleware('permission:data.view');
    Route::get('/datatable', [DataController::class, 'dataTable'])->name('datatable')->middleware('permission:data.view');
    Route::post('/ekle', [DataController::class, 'dataekle'])->name('dataekle')->middleware('permission:data.create');
    Route::post('/personel-ata', [DataController::class, 'personelAta'])->name('personelAta')->middleware('permission:data.create');
    Route::get('/kurs-listesi', [DataController::class, 'ajaxKursList'])->name('kursListesi')->middleware('permission:data.view');
    Route::delete('/delete/{id}', [DataController::class, 'destroy'])->name('delete')->middleware('permission:data.delete');
    Route::post('/export', [DataController::class, 'exportExcel'])->name('export')->middleware('permission:data.view');
    Route::post('/topludatayukle', [DataController::class, 'topludatayukle'])->name('topludatayukle')->middleware('permission:data.create');
});

Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:roles.index');
    Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:roles.create');
    Route::post('/store', [RoleController::class, 'store'])->name('store')->middleware('permission:roles.create');
    Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('edit')->middleware('permission:roles.edit');
    Route::put('/update/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:roles.edit');
});

Route::prefix('ogrenci')->name('ogrenci.')->group(function () {
    Route::get('/', [OgrenciController::class, 'index'])->name('index')->middleware('permission:ogrenci.view');
    Route::get('/profil/{id}', [OgrenciController::class, 'profil'])->name('profil')->middleware('permission:ogrenci.view');
    Route::get('/data', [OgrenciController::class, 'getData'])->name('data')->middleware('permission:ogrenci.view');
    Route::get('/add', [OgrenciController::class, 'add'])->name('add')->middleware('permission:ogrenci.add');
    Route::post('/store', [OgrenciController::class, 'store'])->name('store')->middleware('permission:ogrenci.add');
    Route::get('/edit/{id}', [OgrenciController::class, 'edit'])->name('edit')->middleware('permission:ogrenci.edit');
    Route::put('/update/{id}', [OgrenciController::class, 'update'])->name('update')->middleware('permission:ogrenci.edit');
    Route::post('/export', [OgrenciController::class, 'exportExcel'])->name('export')->middleware('permission:ogrenci.view');
    Route::delete('/delete/{id}', [OgrenciController::class, 'delete'])->name('delete')->middleware('permission:ogrenci.delete');
    Route::prefix('kurslar')->name('kurslar.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'kurslar'])->name('index')->middleware('permission:ogrenci.view');
        Route::get('getir/{id}', [OgrenciController::class, 'kursVerisi'])->name('getir')->middleware('permission:ogrenci.view');
        Route::put('guncelle/{id}', [OgrenciController::class, 'kursGuncelle'])->name('guncelle')->middleware('permission:ogrenci.edit');
        Route::delete('sil/{id}', [OgrenciController::class, 'kursSil'])->name('sil')->middleware('permission:ogrenci.delete');
    });
    Route::prefix('siparisler')->name('siparisler.')->middleware('permission:ogrenci.view')->group(function () {
       Route::get('{id}', [OgrenciController::class, 'siparisler'])->name('index')->middleware('permission:ogrenci.view');
    });
    Route::prefix('belgeler')->name('belgeler.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'belgeler'])->name('index')->middleware('permission:ogrenci.view');
        Route::post('ekle', [OgrenciController::class, 'belgeEkle'])->name('ekle')->middleware('permission:ogrenci.view');
        Route::delete('sil/{id}', [OgrenciController::class, 'belgeSil'])->name('sil')->middleware('permission:ogrenci.delete');
        Route::post('onayla/{id}', [OgrenciController::class, 'belgeDurumGuncelle'])->name('onayla')->middleware('permission:ogrenci.edit');
        Route::post('iptal/{id}', [OgrenciController::class, 'belgeDurumGuncelle'])->name('iptal')->middleware('permission:ogrenci.edit');
    });
    Route::prefix('sinavlar')->name('sinavlar.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'sinavlar'])->name('index')->middleware('permission:ogrenci.view');
        Route::post('ekle', [OgrenciController::class, 'sinavEkle'])->name('ekle')->middleware('permission:ogrenci.view');
        Route::delete('sil/{id}', [OgrenciController::class, 'sinavSil'])->name('sil')->middleware('permission:ogrenci.delete');
    });
    Route::prefix('sertifikalar')->name('sertifikalar.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'sertifikalar'])->name('index')->middleware('permission:ogrenci.view');
        Route::post('ekle', [OgrenciController::class, 'sertifikaEkle'])->name('ekle')->middleware('permission:ogrenci.view');
        Route::delete('sil/{id}', [OgrenciController::class, 'sertifikaSil'])->name('sil')->middleware('permission:ogrenci.delete');
    });
    Route::prefix('loglar')->name('loglar.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'loglar'])->name('index')->middleware('permission:ogrenci.view');
        Route::delete('sil/{id}', [OgrenciController::class, 'logSil'])->name('sil')->middleware('permission:ogrenci.delete');
    });

    Route::prefix('notlar')->name('notlar.')->middleware('permission:ogrenci.view')->group(function () {
        Route::get('{id}', [OgrenciController::class, 'notlar'])->name('index')->middleware('permission:ogrenci.view');
        Route::post('ekle', [OgrenciController::class, 'notEkle'])->name('ekle')->middleware('permission:ogrenci.view');
        Route::delete('sil/{id}', [OgrenciController::class, 'notSil'])->name('sil')->middleware('permission:ogrenci.delete');
    });

});

Route::prefix('kurs')->name('kurs.')->group(function () {
    Route::get('/', [KursController::class, 'index'])->name('index')->middleware('permission:kurs.view');;
    Route::get('/data', [KursController::class, 'getData'])->name('data')->middleware('permission:kurs.view');
    Route::get('/add', [KursController::class, 'add'])->name('add')->middleware('permission:kurs.add');
    Route::post('/store', [KursController::class, 'store'])->name('store')->middleware('permission:kurs.add');
    Route::get('/edit/{id}', [KursController::class, 'edit'])->name('edit')->middleware('permission:kurs.edit');
    Route::put('/update/{id}', [KursController::class, 'update'])->name('update')->middleware('permission:kurs.edit');
    Route::delete('/delete/{id}', [KursController::class, 'delete'])->name('delete')->middleware('permission:kurs.delete');
});

Route::prefix('siparis')->name('siparis.')->group(function () {
    Route::get('/', [SiparisController::class, 'index'])->name('index')->middleware('permission:siparis.view');
    Route::get('/data', [SiparisController::class, 'getData'])->name('data')->middleware('permission:siparis.view');
    Route::get('/detay/{id}', [SiparisController::class, 'detay'])->name('detay')->middleware('permission:siparis.view');
    Route::get('/edit/{id}', [SiparisController::class, 'edit'])->name('edit')->middleware('permission:siparis.edit');
    Route::put('/update/{id}', [SiparisController::class, 'update'])->name('update')->middleware('permission:siparis.edit');
    Route::delete('/delete/{id}', [SiparisController::class, 'delete'])->name('delete')->middleware('permission:siparis.delete');
});

Route::prefix('personel')->name('personel.')->group(function () {
    Route::get('/', [PersonelController::class, 'index'])->name('index')->middleware('permission:personel.view');
    Route::get('/data', [PersonelController::class, 'getData'])->name('data')->middleware('permission:personel.view');
    Route::get('/profil/{id}', [PersonelController::class, 'profil'])->name('profil')->middleware('permission:personel.view');
    Route::get('/metrics/{id}', [PersonelController::class, 'metrics'])->name('metrics')->middleware('permission:personel.view');
    Route::get('/add', [PersonelController::class, 'add'])->name('add')->middleware('permission:personel.add');
    Route::post('/store', [PersonelController::class, 'store'])->name('store')->middleware('permission:personel.add');
    Route::get('/edit/{id}', [PersonelController::class, 'edit'])->name('edit')->middleware('permission:personel.edit');
    Route::put('/update/{id}', [PersonelController::class, 'update'])->name('update')->middleware('permission:personel.edit');
    Route::delete('/delete/{id}', [PersonelController::class, 'delete'])->name('delete')->middleware('permission:personel.delete');
    Route::get('/satislar/{id}', [PersonelController::class, 'satislar'])->name('satislar')->middleware('permission:personel.view');
    Route::get('/performans/{id}', [PersonelController::class, 'performans'])->name('performans')->middleware('permission:personel.view');
    Route::get('/gorusmeler/{id}', [PersonelController::class, 'gorusmeler'])->name('gorusmeler')->middleware('permission:personel.view');
});

Route::prefix('bolum/{kurs_id}')->name('bolum.')->group(function () {
    Route::get('/', [BolumController::class, 'index'])->name('index')->middleware('permission:bolum.view');
    Route::post('/store', [BolumController::class, 'store'])->name('store')->middleware('permission:bolum.add');
    Route::get('/edit/{id}', [BolumController::class, 'edit'])->name('edit')->middleware('permission:bolum.edit');
    Route::put('/update/{id}', [BolumController::class, 'update'])->name('update')->middleware('permission:bolum.edit');
    Route::delete('/delete/{id}', [BolumController::class, 'delete'])->name('delete')->middleware('permission:bolum.delete');
    Route::post('/updateOrder', [BolumController::class, 'updateOrder'])->name('updateOrder')->middleware('permission:bolum.edit');
});

Route::prefix('ders/{kurs_id}')->name('ders.')->group(function () {
    Route::get('/', [DersController::class, 'index'])->name('index')->middleware('permission:ders.view');
    Route::get('/add', [DersController::class, 'add'])->name('add')->middleware('permission:ders.add');
    Route::post('/store', [DersController::class, 'store'])->name('store')->middleware('permission:ders.add');
    Route::get('/edit/{id}', [DersController::class, 'edit'])->name('edit')->middleware('permission:ders.edit');
    Route::put('/update/{id}', [DersController::class, 'update'])->name('update')->middleware('permission:ders.edit');
    Route::delete('/delete/{id}', [DersController::class, 'delete'])->name('delete')->middleware('permission:ders.delete');
    Route::post('/updateOrder', [DersController::class, 'updateOrder'])->name('updateOrder')->middleware('permission:ders.edit');
});


Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:user.view');
    Route::get('/data', [UserController::class, 'getData'])->name('data')->middleware('permission:user.view');
    //Route::get('/add', [UserController::class, 'add'])->name('add')->middleware('permission:user.add');
    Route::post('/store', [UserController::class, 'store'])->name('store')->middleware('permission:user.add');
    //Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit')->middleware('permission:user.edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:user.edit');
    Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete')->middleware('permission:user.delete');
    Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore')->middleware('permission:user.edit');
    Route::get('/modal/add',       [UserController::class, 'addModal'])->name('modal.add')->middleware('permission:user.add');
    Route::get('/modal/edit/{id}', [UserController::class, 'editModal'])->name('modal.edit')->middleware('permission:user.edit');
});

Route::prefix('sinav')->name('sinav.')->group(function () {
    Route::get('/', [SinavController::class, 'index'])->name('index')->middleware('permission:sinav.view');
    Route::get('/data', [SinavController::class, 'getData'])->name('data')->middleware('permission:sinav.view');
    Route::get('/add', [SinavController::class, 'add'])->name('add')->middleware('permission:sinav.add');
    Route::post('/store', [SinavController::class, 'store'])->name('store')->middleware('permission:sinav.add');
    Route::get('/edit/{id}', [SinavController::class, 'edit'])->name('edit')->middleware('permission:sinav.edit');
    Route::put('/update/{id}', [SinavController::class, 'update'])->name('update')->middleware('permission:sinav.edit');
    Route::delete('/delete/{id}', [SinavController::class, 'delete'])->name('delete')->middleware('permission:sinav.delete');
});
