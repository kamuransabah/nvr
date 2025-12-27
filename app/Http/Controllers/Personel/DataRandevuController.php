<?php

namespace App\Http\Controllers\Personel;

use App\Http\Controllers\Controller;
use App\Models\DataGorusme;
use App\Models\DataRandevu;
use App\Models\Urun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DataRandevuController extends Controller
{
    public function index() {
        $personelId = auth()->id();

        $urunler = Urun::orderBy('urun_adi')->get(['id', 'urun_adi']);

        return view(theme_view('admin', 'personel.randevular'), compact('urunler'));
    }

    public function datatable(Request $request) {
        // İlişkiler:
        // data: id, isim, telefon, eposta, urun_id, olumsuz_id, durum, basvuru_tarihi, atama_tarihi
        // data.urun: urun_adi
        // data.dataDurum: value
        $result = DataRandevu::with([
            'data:id,urun_id,isim,eposta,telefon,basvuru_tarihi,cevapsiz,durum',
            'data.urun:id,urun_adi',
        ])->select('id','personel_id','data_id','urun_id','randevu_tarihi','durum','created_at')->where('personel_id',Auth::id())->where('randevu_tarihi', '>=', Carbon::now())->orderBy('randevu_tarihi');

        // Arama (isim / telefon / eposta) -> data ilişkisi üzerinden
        if($request->filled('arama')) {
            $arama = $request->arama;

            $result->whereHas('data', function($q) use ($arama) {
                if(preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                    $q->where('telefon', 'like', '%'.$arama.'%');
                } elseif(filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                    $q->where('eposta', 'like', '%'.$arama.'%');
                } else {
                    $q->where('isim', 'like', '%'.$arama.'%');
                }
            });
        }

        // Ürüne göre filtre (data.urun_id)
        if($request->filled('urun')) {
            $urunId = (int)$request->urun;
            $result->whereHas('data', fn($q) => $q->where('urun_id', $urunId));
        }

        // data_id filtre (başlangıç eşleşmesi)
        if($request->filled('data_id') && is_numeric($request->data_id)) {
            $needle = $request->data_id.'%';
            $result->whereHas('data', fn($q) => $q->where('id', 'like', $needle));
        }

        // Tarih aralığı (görüşme tarihi) -> data_gorusmeler.created_at
        if($request->filled('tarih_filtre')) {
            // Beklenen format: "YYYY-MM-DD - YYYY-MM-DD"
            // Randevular için saat ekleme yapılabilir.
            [
                $start,
                $end
            ] = array_pad(explode(' - ', $request->tarih_filtre), 2, null);
            if($start && $end) {
                $baslangicTarihi = Carbon::parse($start)->startOfDay();
                $bitisTarihi = Carbon::parse($end)->endOfDay();
                $result->whereBetween('randevu_tarihi', [
                    $baslangicTarihi,
                    $bitisTarihi
                ]);
            }
        }

        return DataTables::of($result)->addColumn('id', function($row) {
            return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$row->data_id.'</button>';
        })->addColumn('isim', function($row) {
            return '<p class="text-wrap">'.e($row->data->isim.' '.$row->data->soyisim ?? '—').'</p>';
        })->addColumn('telefon', function($row) {
            $tel = $row->data->telefon ?? '';
            return $tel ? '<a href="https://web.whatsapp.com/send?phone='.sms_telefon($tel).'" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success"><i class="fa-brands fa-whatsapp me-1"></i>'.e($tel).'</a>' : '<span class="text-muted">—</span>';
        })->addColumn('urun', function($row) {
            $urunAdi = $row->data->urun->urun_adi ?? null;
            return '<p class="text-gray-900">'.($urunAdi ? e($urunAdi) : 'Ürün Yok').'</p>';
        })->addColumn('gorusme_tarihi', function($row) {
            return Carbon::parse($row->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY');
        })->addColumn('randevu_tarihi', function($row) {
            return '<span class="btn btn-sm '.($row->randevu_tarihi > now() ? 'btn-light-info' : 'btn-light-danger').'">'.Carbon::parse($row->randevu_tarihi)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm').'</span>';
        })->addColumn('islem', function ($row) {

            $cevapsiz = '';
            if($row->data->cevapsiz > 0) {
                $cevapsiz = '<button type="button" class="btn btn-sm btn-light-warning"><i class="fa-solid fa-phone-slash me-2"></i>'.$row->data->cevapsiz.'</button>';
            }

            return $cevapsiz.'
                    <a href="'.route(config('system.personel_prefix').'.data.gorusmeyap', ['data_id' => $row->data->id]).'" class="btn btn-icon btn-sm btn-light-primary" data-bs-toggle="tooltip" title="Görüşme Yap"><i class="fa-solid fa-phone"></i></a>
                    <form action="' . route(config('system.admin_prefix').'.data.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

        })->rawColumns([
            'id',
            'isim',
            'telefon',
            'urun',
            'gorusme_tarihi',
            'randevu_tarihi',
            'islem'
        ])->make(true);
    }
}
