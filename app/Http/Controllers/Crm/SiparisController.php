<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Siparis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SiparisController extends Controller
{
    public function index() {
        $personeller = User::role('personel')->get();
        $siparisDurumlari = Setting::where('type', 'siparis_durum')->get();
        return view(theme_view('admin', 'pages.siparis.index'), compact('personeller', 'siparisDurumlari'));
    }

    public function getData(Request $request)
    {
        $siparisler = Siparis::with('uye', 'kaynak', 'siparisDurum', 'personel')->select('siparisler.id', 'siparisler.siparis_no', 'siparisler.user_id', 'siparisler.personel_id', 'siparisler.toplam_tutar', 'siparisler.satis_kaynak', 'siparisler.created_at', 'siparisler.updated_at', 'siparisler.durum')
            ->orderBy('siparisler.id', 'desc');

        // Sipariş no'ya göre filtreleme
        if ($request->has('siparis_no') && !empty($request->siparis_no)) {
            $siparisler->where('siparis_no', 'like', '%' . $request->siparis_no . '%');
        }

        // Personele göre filtreleme
        if ($request->has('personel_id') && !empty($request->personel_id)) {
            $siparisler->where('personel_id', $request->personel_id);
        }

        // Duruma göre filtreleme
        if ($request->filled('durum')) {
            $siparisler->where('durum', $request->durum);
        }

        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $siparisler->whereBetween('created_at', [$baslangicTarihi, $bitisTarihi]);
        }

        return DataTables::of($siparisler)
            ->addColumn('siparis_no', function ($siparis) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$siparis->siparis_no.'</button>';
            })
            ->addColumn('user', function ($siparis) {
                $uye = $siparis->uye;
                if(!$uye) {
                    return '---';
                } else {
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol  symbol-40px symbol-circle ">
                            <img alt="'.$siparis->uye->isim.' '.$siparis->uye->soyisim.'" src="'.userAvatar($siparis->uye->profil_resmi, 'ogrenci').'">
                        </div>
                        <!--begin::Details-->
                        <div class="ms-4">
                            <span class="fs-6 fw-bold text-gray-900 text-hover-primary mb-2">'.$siparis->uye->isim.' '.$siparis->uye->soyisim.'</span>
                            <div class="fw-semibold fs-7 text-muted">'.$siparis->uye->email.'</div>
                        </div>
                        <!--end::Details-->
                    </div>';
                }
            })
            ->addColumn('tutar', function ($siparis) {
                return '₺ '.$siparis->toplam_tutar;
            })
            ->addColumn('tarih', function ($siparis) {
                //return Carbon::parse($siparis->tarih)->diffForHumans();
                return Carbon::parse($siparis->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm');
            })
            ->addColumn('personel', function ($siparis) {
                $personel = $siparis->personel;
                if(!$personel) {
                    return '---';
                } else {
                    return '<a href="#" class="text-gray-800 text-hover-primary mb-1">'.$siparis->personel->isim.' '.$siparis->personel->soyisim.'</a>';
                }
            })
            ->addColumn('durum', function ($siparis) {
                return '<span class="badge badge-light-primary">'.$siparis->siparisDurum->value.'</span>';
                //return '<span class="badge bg-'.status()->get($siparis->durum).'-subtle text-'.status()->get($siparis->durum).' fw-semibold fs-2 gap-1 d-inline-flex align-items-center">'.status()->get($siparis->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix').'.siparis.detay', ['id' => $row->id]) . '" target="_blank" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Görüntüle">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>

                    <a href="' . route(config('system.admin_prefix').'.siparis.edit', ['id' => $row->id]) . '" target="_blank" class="btn btn-sm btn-icon btn-light-primary me-1" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-edit"></i>
                    </a>

                    <form action="' . route(config('system.admin_prefix').'.siparis.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['siparis_no', 'user', 'tutar', 'tarih', 'personel', 'durum', 'islem'])
            ->make(true);
    }

    public function detay($id) {
        $siparis = Siparis::with('uye', 'kaynak', 'siparisDurum', 'personel', 'odemeTuru', 'gecmis')
            ->findOrFail($id);
        return view(theme_view('admin', 'pages.siparis.detay'), compact('siparis'));
    }

    public function edit() {

    }

    public function delete($id)
    {
        $siparis = Siparis::with('urunler')->findOrFail($id);

        // 1. Ödeme kontrolü
        if ($siparis->odeme_durum == 1) {
            return back()->with('alert', [
                'library' => 'sweetalert',
                'type' => 'error',
                'message' => 'Bu sipariş için ödeme yapılmış. Silme işlemi gerçekleştirilemez.'
            ]);
        }

        // 2. Kurs kontrolü (satis_turu == 1)
        $kursUrunleri = $siparis->urunler->where('satis_turu', 1);

        if ($kursUrunleri->isNotEmpty()) {
            return back()->with('alert', [
                'library' => 'sweetalert',
                'type' => 'error',
                'message' => 'Bu siparişte kurs satışı mevcut. Önce öğrenci kurslarını silmelisiniz.'
            ]);
        }

        // 3. Silme işlemi
        $siparis->urunler()->delete(); // sipariş ürünlerini sil
        $siparis->delete();            // siparişi sil

        return redirect()->route('siparis.index')->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Sipariş başarıyla silindi.'
        ]);
    }

}
