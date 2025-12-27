<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Bolum;
use App\Models\Kurs;
use App\Models\Setting;
use App\Models\Soru;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SoruController extends Controller
{
    public function index() {
        $kurslar = Kurs::orderBy('kurs_adi', 'asc')->get();
        $bolumler = Bolum::orderBy('bolum_adi', 'asc')->get();
        return view(theme_view('admin', 'pages.soru.index'), compact('kurslar', 'bolumler'));
    }

    public function getData(Request $request)
    {

        $result = Soru::with(['kurs', 'bolum'])
            ->select('sorular.id', 'sorular.kurs_id', 'sorular.bolum_id', 'sorular.soru', 'sorular.cevap', 'sorular.durum')
            ->orderBy('sorular.id', 'desc');

        if($request->has('soru') & !empty($request->soru)) {
            $result->where('sorular.soru', 'like', '%' . $request->soru . '%');
        }

        if ($request->has('bolum') && !empty($request->tur)) {
            $result->where('sorular.bolum', $request->tur);
        }

        if ($request->has('kurs_id') && !empty($request->kurs_id)) {
            $result->where('sorular.kurs_id', $request->kurs_id);
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && !empty($request->durum)) {
            $result->where('sorular.durum', $request->durum);
        }

        return DataTables::of($result)
            ->addColumn('id', function ($data) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn('kurs_id', function ($data) {
                return '<span>'.$data->kurs->kurs_adi.'</span>';
            })
            ->addColumn(
                'tur', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->tur, 'class', 'sinav_turu').'">'.$data->sinavTuru->value.'</span>';
            })
            ->addColumn('tarih', function ($data) {
                return '<div class="d-flex flex-column">
                        <span class="badge badge-light-secondary mb-1">
                            <small>Başlangıç: '.Carbon::parse($data->baslangic_tarihi)->format('d.m.Y').'</small>
                        </span>
                        <span class="badge badge-light-secondary">
                            <small>Bitiş: '.Carbon::parse($data->bitis_tarihi)->format('d.m.Y').'</small>
                        </span>
                        </div>';
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class').'">'.status()->get($data->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix').'.sinav.edit', ['id' => $row->id]) . '" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="' . route(config('system.admin_prefix').'.ogrenci.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'kurs_id', 'tur', 'tarih', 'durum', 'islem'])
            ->make(true);
    }
}
