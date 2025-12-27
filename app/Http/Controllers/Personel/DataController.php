<?php

namespace App\Http\Controllers\Personel;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Setting;
use App\Models\Urun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DataController extends Controller
{
    public function index()
    {
        $personelId = auth()->id();

        $now = Carbon::now()->startOfMonth();
        $months = collect(range(1, 6))
            ->map(fn($i) => $now->copy()->subMonths($i - 1)->format('Y-m'));

        // Tüm aylık istatistiklerde personel filtresi
        $getMonthlyCounts = function ($durumlar = null) use ($months, $personelId) {
            $query = Data::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
                ->where('personel_id', $personelId)                // <<< EKLENDİ
                ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months);

            if (!is_null($durumlar)) {
                $query->whereIn('durum', (array) $durumlar);
            }

            return $query->groupBy('month')->pluck('total', 'month')->toArray();
        };

        $buildData = function ($label, $durumlar, $color, $key) use ($months, $getMonthlyCounts) {
            $monthlyData = $getMonthlyCounts($durumlar);
            $monthsList  = $months->reverse()->values();

            $chartLabels = $monthsList->slice(0, 5);
            $chartData   = $chartLabels->map(fn($m) => $monthlyData[$m] ?? 0);

            $latestMonth = $months->first();
            $lastValue   = $monthlyData[$latestMonth] ?? 0;

            return [
                'title'    => $label,
                'subtitle' => 'Son 6 Aylık İstatistik',
                'color'    => $color,
                'value'    => $lastValue,     // en güncel ayın değeri
                'key'      => $key,
                'labels'   => $chartLabels->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('F'))->toArray(),
                'data'     => $chartData->toArray(),
            ];
        };

        // Ürün istatistikleri (zaten personel filtreliydi)
        $topCourses = Data::selectRaw('urun_id, COUNT(*) as total')
            ->where('personel_id', $personelId)                    // <<< VAR
            ->whereIn('durum', [1, 2])
            ->whereNotNull('urun_id')
            ->groupBy('urun_id')
            ->orderByDesc('total')
            ->take(20)
            ->pluck('total', 'urun_id');

        $urunler = Urun::whereIn('id', $topCourses->keys())->pluck('urun_adi', 'id');

        $urunStats = [];
        foreach ($topCourses as $urunId => $adet) {
            $urunStats[] = ['adi' => $urunler[$urunId] ?? 'Bilinmeyen Ürün', 'adet' => $adet];
        }

        $durumlar   = Setting::where('type', 'data_durum')->get();
        $olumsuzlar = Setting::where('type', 'data_olumsuz')->get();

        return view(theme_view('admin', 'personel.data'))->with([
            'urunler'   => Urun::orderBy('urun_adi')->get(['id', 'urun_adi']),
            'urunStats' => $urunStats,
            'olumsuzlar'=> $olumsuzlar,
            'durumlar'  => $durumlar,
            'stats'     => [
                $buildData('Toplam Data',  null,   'primary', 'toplam'),
                $buildData('Yeni Data',    [1, 2], 'info',    'yeni'),
                $buildData('Olumsuz Data', [3],    'danger',  'olumsuz'),
                $buildData('Kayıtlar',     [5],    'success', 'kayit'),
            ],
        ]);
    }

    public function datatable(Request $request)
    {

        $result = Data::with([
            'urun:id,urun_adi',
            'olumsuzNedeni',
            'dataDurum'
        ])->select('id', 'personel_id', 'data.urun_id', 'isim', 'sehir', 'eposta', 'telefon', 'basvuru_tarihi', 'atama_tarihi', 'olumsuz_id', 'cevapsiz', 'durum')
            ->where('personel_id', Auth::id())
            ->orderBy('data.id', 'desc');

        if ($request->has('arama') && !empty($request->arama)) {
            $arama = $request->arama;

            if (preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                // Rakam, boşluk, - veya + içeriyorsa telefon
                $result->where('telefon', 'like', '%' . $arama . '%');
            } elseif (filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                // Geçerli bir e-posta ise
                $result->where('eposta', 'like', '%' . $arama . '%');
            } else {
                // Diğer tüm durumlar isme göre aranır
                $result->where('isim', 'like', '%' . $arama . '%');
            }
        }

        // Kategoriye göre filtreleme
        if ($request->has('urun') && !empty($request->urun)) {
            $result->where('urun_id', $request->urun);
        }

        if ($request->has('data_id') && is_numeric($request->data_id)) {
            $result->where('id', 'like', $request->data_id . '%');
        }

        // Duruma göre filtreleme
        if ($request->filled('durum')) {
            // Eğer olumsuz durumdan biri seçilmişse
            if (str_starts_with($request->durum, 'olumsuz-')) {
                $olumsuzId = (int) str_replace('olumsuz-', '', $request->durum);
                $result->where('durum', 3)->where('olumsuz_id', $olumsuzId);
            } else {
                $result->where('durum', (int)$request->durum);
            }
        } else {
            // Durum filtresi boşsa: durum 1-2 olsun
            $result->whereIn('durum', [1, 2]);
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
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn(
                'isim', function ($data) {
                return '<p class="text-wrap">'.$data->isim.'</p>';
            })
            ->addColumn(
                'telefon', function ($data) {
                return '<a href="https://web.whatsapp.com/send?phone='.sms_telefon($data->telefon).'" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success"><i class="fa-brands fa-whatsapp me-1"></i>'.$data->telefon.'</a>';
            })
            ->addColumn(
                'urun', function ($data) {
                if ($data->urun) {
                    return '<p class="text-gray-900">' . $data->urun->urun_adi . '</p>';
                } else {
                    return '<p class="text-gray-900">Ürün Yok</p>';
                }
            })

            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class', 'data').'">'.$data->dataDurum?->value.'</span>';
            })
            ->addColumn('islem', function ($row) {

                $cevapsiz = '';
                if($row->cevapsiz > 0) {
                    $cevapsiz = '<button type="button" class="btn btn-sm btn-light-warning"><i class="fa-solid fa-phone-slash me-2"></i>'.$row->cevapsiz.'</button>';
                }

                return $cevapsiz.'
                    <a href="'.route(config('system.personel_prefix').'.data.gorusmeyap', ['data_id' => $row->id]).'" class="btn btn-sm btn-light-primary"><i class="fa-solid fa-phone me-3"></i>Görüşme Yap</a>
                    <form action="' . route(config('system.admin_prefix').'.data.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'isim', 'telefon', 'urun', 'durum', 'islem'])
            ->make(true);
    }
}
