<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\DersRequest;
use App\Models\Ders;
use App\Models\Bolum;
use App\Models\Egitmen;
use App\Models\Kurs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DersController extends Controller
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
        $dersler = Ders::where('kurs_id', $kurs_id)->orderBy('sira')->get();

        return view(theme_view('admin', 'pages.kurs.ders'), compact('kurslar', 'kurs', 'kurs_id', 'bolumler', 'dersler'));
    }

    public function add($kurs_id)
    {
        $ders = new Ders();
        $kurs = Kurs::findOrFail($kurs_id);

        $bolumler = Bolum::where('kurs_id', $kurs_id)
            ->orderBy('sira')
            ->get();

        $egitmenler = Egitmen::join('users', 'users.id', '=', 'egitmen.user_id')
            ->join('egitmen_kurs as ek', function ($j) use ($kurs_id) {
                $j->on('ek.user_id', '=', 'users.id')
                    ->where('ek.kurs_id', $kurs_id);
            })
            ->orderBy('users.isim')
            ->orderBy('users.soyisim')
            ->distinct() // aynı user birden fazla kayıtla eşleşirse tekille
            ->get([
                'egitmen.id as egitmen_id',
                'users.id as user_id',
                'users.isim',
                'users.soyisim',
            ]);

        return view(theme_view('admin', 'pages.kurs.ders_form'),
            compact('ders','bolumler','kurs_id','kurs','egitmenler'));
    }

    public function store(DersRequest $request, $kurs_id)
    {
        $insertData = new Ders();
        $insertData->kurs_id = $kurs_id;
        $insertData->bolum_id = $bolum_id;
        $insertData->egitmen_id = $egitmen_id;
        $insertData->demo = $demo ?? 0;
        $insertData->baslik = $baslik;
        $insertData->permalink = Str::slug($request->baslik);
        $insertData->ozet = $request->ozet;
        $insertData->icerik = $request->icerik;
        $insertData->ders_suresi = $request->ders_suresi;
        $insertData->video_kaynak_id = $request->video_kaynak_id;
        $insertData->sira = $request->sira ?? 1;
        $insertData->durum = $request->durum ?? 0;

        $insertData->save();

        return redirect()->route(config('system.admin_prefix').'.ders.index', ['kurs_id' => $kurs_id])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kayıt başarıyla eklendi.',
            ]);
    }

    public function edit($kurs_id, $id)
    {
        $ders = Ders::findOrFail($id);
        $kurs = Kurs::findOrFail($kurs_id);
        $bolumler = Bolum::where('kurs_id', $kurs_id)
            ->orderBy('sira')
            ->get();
        $egitmenler = Egitmen::join('users', 'users.id', '=', 'egitmen.user_id')
            ->join('egitmen_kurs as ek', function ($j) use ($kurs_id) {
                $j->on('ek.user_id', '=', 'users.id')
                    ->where('ek.kurs_id', $kurs_id);
            })
            ->orderBy('users.isim')
            ->orderBy('users.soyisim')
            ->distinct() // aynı user birden fazla kayıtla eşleşirse tekille
            ->get([
                'egitmen.id as egitmen_id',
                'users.id as user_id',
                'users.isim',
                'users.soyisim',
            ]);


        return view(theme_view('admin', 'pages.kurs.ders_form'), compact('ders','bolumler', 'kurs_id', 'kurs', 'egitmenler'));
    }

    public function update(DersRequest $request, $kurs_id, $id)
    {
        $data = Ders::findOrFail($id);

        $data->update([
            'kurs_id' => $request->kurs_id,
            'bolum_id' => $request->bolum_id,
            'egitmen_id' => $request->egitmen_id,
            'demo' => $request->demo ?? 0,
            'baslik' => $request->baslik,
            'permalink' => Str::slug($request->baslik),
            'ozet' => $request->ozet,
            'icerik' => $request->icerik,
            'ders_suresi' => $request->ders_suresi,
            'video_kaynak_id' => $request->video_kaynak_id,
            'sira' => $request->sira ?? 1,
            'durum' => $request->durum ?? 0,
        ]);

        return redirect()->route(config('system.admin_prefix').'.ders.index', ['kurs_id' => $kurs_id])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kayıt başarıyla güncellendi.',
            ]);
    }

    public function delete(Request $request, $kurs_id, $id)
    {
        $kategori = Ders::findOrFail($id);
        $kategori->delete();
        return redirect()->route(config('system.admin_prefix').'.ders.index', ['kurs_id' => $kurs_id])->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Kayıt başarıyla silindi.',
        ]);
    }
}
