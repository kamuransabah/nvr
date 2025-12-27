<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Kurs;
use App\Models\Setting;
use App\Models\Sinav;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SinavController extends Controller
{
    public function index() {
        $kurslar = Kurs::orderBy('kurs_adi', 'asc')->get();
        $sinavTurleri = Setting::where('type', 'sinav_turu')->select('key', 'value')->get();
        return view(theme_view('admin', 'pages.sinav.index'), compact('kurslar', 'sinavTurleri'));
    }

    public function getData(Request $request)
    {

        $result = Sinav::with(['kurs', 'sinavTuru'])
            ->select('sinavlar.id', 'sinavlar.kurs_id', 'sinavlar.sinav_adi', 'sinavlar.tur', 'sinavlar.baslangic_tarihi', 'sinavlar.bitis_tarihi', 'sinavlar.sira', 'sinavlar.otosinav', 'sinavlar.created_at', 'sinavlar.durum')
            ->orderBy('sinavlar.sira')
            ->orderBy('sinavlar.id', 'desc');

        if($request->has('sinav_adi') & !empty($request->sinav_adi)) {
            $result->where('sinavlar.sinav_adi', 'like', '%' . $request->sinav_adi . '%');
        }

        if ($request->has('tur') && !empty($request->tur)) {
            $result->where('sinavlar.tur', $request->tur);
        }

        if ($request->has('kurs_id') && !empty($request->kurs_id)) {
            $result->where('sinavlar.kurs_id', $request->kurs_id);
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && !empty($request->durum)) {
            $result->where('sinavlar.durum', $request->durum);
        }


        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay();
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay();

            $result->whereDate('sinavlar.baslangic_tarihi', '>=', $baslangicTarihi)
                ->whereDate('sinavlar.bitis_tarihi', '<=', $bitisTarihi);
        }

        /*
          { data: 'id', name: 'id' },
        { data: 'sinav_adi', name: 'sinav_adi' },
        { data: 'kurs', name: 'kurs' },
        { data: 'tur', name: 'tur' },
        { data: 'tarih', name: 'tarih' },
        { data: 'durum', name: 'durum', orderable: false, searchable: false },
        { data: 'islem', name: 'islem', orderable: false, searchable: false }
         * */


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
