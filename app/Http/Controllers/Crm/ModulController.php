<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ModulController extends Controller
{
    public function iletisim() {
        //return view(config('system.admin_prefix').'.modul.iletisim');
        return view(theme_view('admin', 'pages.modul.iletisim'));
    }

    public function iletisimData(Request $request)
    {
        $result = (new Modul())->setTable('iletisim')->select('id', 'isim', 'soyisim', 'mesaj', 'cevap', 'created_at', 'durum')
            ->orderBy('id', 'desc');

        if ($request->has('isim') && !empty($request->isim)) {
            $result->where('isim', 'like', '%' . $request->isim . '%');
        }

        if ($request->has('soyisim') && !empty($request->soyisim)) {
            $result->where('soyisim', 'like', '%' . $request->soyisim . '%');
        }

        if ($request->has('durum') && $request->durum !== null) {
            $result->where('durum', $request->durum);
        }

        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $result->whereBetween('created_at', [$baslangicTarihi, $bitisTarihi]);
        }


        return DataTables::of($result)
            ->addColumn('id', function ($data) {
                return '<i class="fa-solid ' . (is_null($data->cevap) ? 'fa-envelope-open-text ' : 'fa-envelope text-primary') . '"></i>';

            })
            ->addColumn(
                'isim', function ($data) {
                return '<p class="text-wrap"><span class="fw-bold">'.$data->isim.' '.$data->soyisim.'</span></p>';
            })
            ->addColumn(
                'mesaj', function ($data) {
                return '<p class="text-gray-900">'.ozet($data->mesaj, 50).'</p>';
            })
            ->addColumn('tarih', function ($data) {
                return Carbon::parse($data->created_at)->diffForHumans();
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum).'">'.status()->get($data->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($data) {

                return '
                    <a href="' . route(config('system.admin_prefix').'.iletisim.reply', ['id' => $data->id]) . '" target="_blank" class="btn btn-sm btn-icon btn-light-primary me-1" data-bs-toggle="tooltip" title="Görüntüle">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>

                    <form action="' . route(config('system.admin_prefix').'.iletisim.delete', ['id' => $data->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'isim', 'mesaj', 'durum', 'islem'])
            ->make(true);
    }
}
