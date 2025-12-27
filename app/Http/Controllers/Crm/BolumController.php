<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\BolumRequest;
use App\Models\Bolum;
use App\Models\Kurs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BolumController extends Controller
{
    public function index(Request $request, $kurs_id)
    {
        if (!$kurs_id) {
            return redirect()->route(config('system.admin_prefix').'.kurs.index', ['kurs_id' => $kurs_id])->with('alert', [
                'library' => 'sweetalert',
                'type' => 'warning',
                'message' => 'Önce kurs seçiniz.',
            ]);
        }

        $kurslar = Kurs::orderBy('kurs_adi', 'asc')->get();
        $kurs = Kurs::findOrFail($kurs_id);
        $bolumler = Bolum::where('kurs_id', $kurs_id)->orderBy('sira')->get();

        return view(theme_view('admin', 'pages.kurs.bolum'), compact('kurslar', 'kurs', 'kurs_id', 'bolumler'));
    }

    public function store(BolumRequest $request, $kurs_id)
    {
        $insertData = new Bolum();
        $insertData->bolum_adi = $request->bolum_adi;
        $insertData->kurs_id = $kurs_id;
        $insertData->aciklama = $request->aciklama;
        $insertData->sira = $request->sira ?? 1;
        $insertData->permalink = Str::slug($request->bolum_adi);
        $insertData->durum = $request->durum ?? 0;

        $insertData->save();

        return redirect()->route(config('system.admin_prefix').'.bolum.index', ['kurs_id' => $kurs_id])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kayıt başarıyla eklendi.',
            ]);
    }

    public function edit($kurs_id, $id)
    {
        $data = Bolum::findOrFail($id);
        $kurs = Kurs::findOrFail($kurs_id);
        $bolumler = Bolum::where('kurs_id', $kurs_id)
            ->orderBy('sira')
            ->get();

        return view(theme_view('admin', 'pages.kurs.bolum'), compact('data','bolumler', 'kurs_id', 'kurs'));
    }

    public function update(BolumRequest $request, $kurs_id, $id)
    {
        $kategori = Bolum::findOrFail($id);

        $kategori->update([
            'bolum_adi' => $request->bolum_adi,
            'aciklama' => $request->aciklama,
            'permalink' => Str::slug($request->bolum_adi),
            'sira' => $request->sira ?? 1,
            'durum' => $request->durum ?? 1,
        ]);

        return redirect()->route(config('system.admin_prefix').'.bolum.index', ['kurs_id' => $kurs_id])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kayıt başarıyla güncellendi.',
            ]);
    }

    public function delete(Request $request, $kurs_id, $id)
    {
        $kategori = Bolum::findOrFail($id);
        $kategori->delete();
        return redirect()->route(config('system.admin_prefix').'.bolum.index', ['kurs_id' => $kurs_id])->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Kayıt başarıyla silindi.',
        ]);
    }
}
