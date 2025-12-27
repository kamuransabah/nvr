<?php

namespace App\Http\Controllers\Crm;

use App\Models\Personel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Siparis;
use App\Models\Data;
use App\Models\DataGorusme;
use Illuminate\Support\Facades\Schema;

class PersonelController extends Controller
{
    public function index() {
        return view(theme_view('admin', 'pages.personel.index'));
    }

    public function getData(Request $request)
    {

        $result = User::select('users.id', 'users.name', 'users.isim', 'users.soyisim', 'users.telefon', 'users.email', 'users.durum', 'users.created_at');

        if($request->has('arama') && !empty($request->arama)) {
            $arama = $request->arama;

            if(preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                // Rakam, boşluk, - veya + içeriyorsa telefon
                $result->where('users.telefon', 'like', '%' . $arama . '%');
            } elseif(filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                // Geçerli bir e-posta ise
                $result->where('users.email', 'like', '%' . $arama . '%');
            } else {
                // İsim veya soyisim alanlarında arama
                $result->where(function($query) use ($arama) {
                    $query->where('users.isim', 'like', '%' . $arama . '%')->orWhere('users.soyisim', 'like', '%' . $arama . '%');
                });
            }
        }

        if($request->has('name') & !empty($request->name)) {
            $result->where('users.name', 'like', '%' . $request->name . '%');
        }

        // Duruma göre filtreleme
        if ($request->filled('durum')) {
            $result->where('users.durum', $request->durum);
        }

        // ID ye göre filtreleme
        if ($request->has('personel_id') && !empty($request->personel_id) && is_numeric($request->personel_id)) {
            $result->where('users.id', $request->personel_id);
        }


        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $result->whereBetween('users.created_at', [$baslangicTarihi, $bitisTarihi]);
        }

        return DataTables::of($result)
            ->addColumn('id', function ($data) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn(
                'isim', function ($data) {
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol  symbol-40px symbol-circle ">
                            <img alt="'.$data->isim.' '.$data->soyisim.'" src="'.userAvatar($data->profil_resmi, 'personel').'">
                        </div>
                        <div class="ms-4">
                            <span class="fs-6 fw-bold text-gray-900 text-hover-primary mb-2">'.$data->isim.' '.$data->soyisim.'</span>
                            <div class="fw-semibold fs-7 text-muted">'.$data->email.'</div>
                        </div>
                    </div>';
            })
            ->addColumn(
                'telefon', function ($data) {
                return '<a href="https://web.whatsapp.com/send?phone='.sms_telefon($data->telefon).'" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success"><i class="fa-brands fa-whatsapp me-1"></i>'.$data->telefon.'</a>';
            })
            ->addColumn('tarih', function ($data) {
                return '<span class="badge badge-light-info">'.Carbon::parse($data->created_at)->format('d.m.Y').'</span>';
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class').'">'.status()->get($data->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix') . '.personel.profil', ['id' => $row->id]) . '" class="btn btn-sm btn-light-primary"><i class="far fa-address-card"></i> Profil</a>
                    <form action="' . route(config('system.admin_prefix').'.personel.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'isim', 'telefon', 'tarih', 'durum', 'islem'])
            ->make(true);
    }

    public function profil($id) {

        $profil = User::findOrFail($id);
        $personel = Personel::where('user_id', $id)->first();

        return view(theme_view('admin', 'pages.personel.profil'), compact('profil', 'personel'));
    }

    public function metrics(Request $request, $id)
    {
        // Tarih aralığı (varsayılan: son 7 gün)
        $start = $request->filled('start')
            ? Carbon::parse($request->get('start'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay(); // SON 30 GÜN

        $end = $request->filled('end')
            ? Carbon::parse($request->get('end'))->endOfDay()
            : Carbon::now()->endOfDay();

        $days = $start->diffInDays($end) + 1;
        $prevStart = (clone $start)->subDays($days);
        $prevEnd   = (clone $start)->subDay()->endOfDay();

        // Satış filtresi: odeme_durum = 1 (ödendi)
        $paidStatus = 1;

        // ---- CURRENT ----
        $leadCount = Data::where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $salesQuery = Siparis::where('personel_id', $id)
            ->where('odeme_durum', $paidStatus)
            ->whereBetween('created_at', [$start, $end]);

        $revenue    = (clone $salesQuery)->sum('odenecek_tutar');
        $salesCount = (clone $salesQuery)->count();

        $callsCount = DataGorusme::where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $conversion = $leadCount > 0 ? round(($salesCount / $leadCount) * 100, 2) : 0;
        $avgTicket  = $salesCount > 0 ? round($revenue / $salesCount, 2) : 0;

        // ---- PREVIOUS ----
        $prevLeadCount = Data::where('personel_id', $id)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevSalesQuery = Siparis::where('personel_id', $id)
            ->where('odeme_durum', $paidStatus)
            ->whereBetween('created_at', [$prevStart, $prevEnd]);

        $prevRevenue     = (clone $prevSalesQuery)->sum('odenecek_tutar');
        $prevSalesCount  = (clone $prevSalesQuery)->count();

        $prevCallsCount = DataGorusme::where('personel_id', $id)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevConversion = $prevLeadCount > 0 ? round(($prevSalesCount / $prevLeadCount) * 100, 2) : 0;

        $delta = function ($now, $prev) {
            if ($prev == 0) return $now > 0 ? 100 : 0;
            return round((($now - $prev) / $prev) * 100, 2);
        };

        // ---- DAILY SERIES ----
        $leadDaily = Data::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $salesDaily = Siparis::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('personel_id', $id)
            ->where('odeme_durum', $paidStatus)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $labels = [];
        $leadSeries = [];
        $salesSeries = [];
        for ($i = 0; $i < $days; $i++) {
            $date = (clone $start)->addDays($i)->toDateString();
            $labels[]      = $date;
            $leadSeries[]  = (int)($leadDaily[$date] ?? 0);
            $salesSeries[] = (int)($salesDaily[$date] ?? 0);
        }

        // ---- RECENT ----
        $recentSales = Siparis::select('id', 'created_at', DB::raw('odenecek_tutar as tutar'))
            ->where('personel_id', $id)
            ->where('odeme_durum', $paidStatus)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // DataGorusme tablosunda 'sonuc' olmayabilir; personel_notu / kayit'tan gösterelim
        $recentCalls = DataGorusme::select('id', 'created_at', 'personel_notu')
            ->where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $courseTable = 'kurslar';
        $courseIdCol = 'id';
        $courseNameCol = 'kurs_adi';

        // En çok satılan kurslar (adet)
        $topCourseSales = DB::table('siparis_urunleri as su')
            ->join('siparisler as s', 's.id', '=', 'su.siparis_id')
            ->leftJoin($courseTable.' as k', 'k.'.$courseIdCol, '=', 'su.urun_id')
            ->where('s.personel_id', $id)
            ->where('s.odeme_durum', 1)
            ->whereBetween('s.created_at', [$start, $end])
            ->select('su.urun_id', DB::raw('COUNT(*) as adet'), DB::raw("COALESCE(k.$courseNameCol, CONCAT('Kurs #', su.urun_id)) as ad"))
            ->groupBy('su.urun_id', 'k.'.$courseNameCol)
            ->orderByDesc('adet')
            ->limit(5)
            ->get();

        // Kurslara göre lead sayısı
        $leadByCourse = Data::where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('kurs_id')
            ->select('kurs_id', DB::raw('COUNT(*) as adet'))
            ->groupBy('kurs_id')
            ->orderByDesc('adet')
            ->limit(5)
            ->get()
            ->map(function($r) use ($courseTable, $courseIdCol, $courseNameCol){
                $name = DB::table($courseTable)->where($courseIdCol, $r->kurs_id)->value($courseNameCol);
                return ['kurs_id'=>$r->kurs_id, 'ad'=>$name ?? ('Kurs #'.$r->kurs_id), 'adet'=>$r->adet];
            });

        // Kurslara göre görüşme sayısı
        $callsByCourse = DataGorusme::where('personel_id', $id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('kurs_id')
            ->select('kurs_id', DB::raw('COUNT(*) as adet'))
            ->groupBy('kurs_id')
            ->orderByDesc('adet')
            ->limit(5)
            ->get()
            ->map(function($r) use ($courseTable, $courseIdCol, $courseNameCol){
                $name = DB::table($courseTable)->where($courseIdCol, $r->kurs_id)->value($courseNameCol);
                return ['kurs_id'=>$r->kurs_id, 'ad'=>$name ?? ('Kurs #'.$r->kurs_id), 'adet'=>$r->adet];
            });

        $callsAvg = $days > 0 ? round($callsCount / $days, 2) : 0;

        return response()->json([
            'kpis' => [
                'salesCount' => ['value' => $salesCount, 'delta' => $delta($salesCount, $prevSalesCount)],
                'revenue'    => ['value' => (float) $revenue, 'delta' => $delta($revenue, $prevRevenue)],
                'conversion' => ['value' => $conversion, 'delta' => $delta($conversion, $prevConversion)],
                'callsCount' => ['value' => $callsCount, 'delta' => $delta($callsCount, $prevCallsCount)],
                'callsAvg'   => ['value' => $callsAvg],
                'avgTicket'  => ['value' => $avgTicket],
            ],
            'chart' => [
                'labels' => $labels,
                'leads'  => $leadSeries,
                'sales'  => $salesSeries,
            ],
            'funnel' => [
                'Data'    => $leadCount,
                'Görüşme' => $callsCount,
                'Satış'   => $salesCount,
            ],
            'recent' => [
                'sales' => $recentSales,
                'calls' => $recentCalls,
            ],
            'courses' => [
                'topSales' => $topCourseSales,
                'leadByCourse' => $leadByCourse,
                'callsByCourse' => $callsByCourse,
            ],
        ]);


    }

    public function satislar(Request $request, $id)
    {
        $profil   = User::findOrFail($id);                    // users.id
        $personel = Personel::where('user_id', $id)->first();  // personel.id olabilir

        // siparisler.personel_id hangi ID'yi tutuyor? (personel.id mi users.id mi)
        $query = Siparis::query();
        $byPersonelId = $personel ? (clone $query)->where('personel_id', $personel->id)->exists() : false;
        $byUserId     = (clone $query)->where('personel_id', $profil->id)->exists();

        if ($byPersonelId) {
            $base = Siparis::where('personel_id', $personel->id);
            $activePersonelId = $personel->id;
        } elseif ($byUserId) {
            $base = Siparis::where('personel_id', $profil->id);
            $activePersonelId = $profil->id;
        } else {
            $base = $personel
                ? Siparis::where('personel_id', $personel->id)
                : Siparis::where('personel_id', $profil->id);
            $activePersonelId = $personel ? $personel->id : $profil->id;
        }

        // ---- Tarih filtresi (daterangepicker) ----
        // GET ?from=YYYY-MM-DD&to=YYYY-MM-DD
        $fromRaw = $request->query('from');
        $toRaw   = $request->query('to');

        $from = null; $to = null;
        try { if ($fromRaw) $from = Carbon::parse($fromRaw)->startOfDay(); } catch (\Throwable $e) {}
        try { if ($toRaw)   $to   = Carbon::parse($toRaw)->endOfDay();   } catch (\Throwable $e) {}

        // COALESCE(odeme_tarihi, created_at) üstünden tarih filtresi helper
        $applyDate = function($q) use ($from, $to) {
            if ($from && $to) {
                $q->whereRaw("COALESCE(odeme_tarihi, created_at) BETWEEN ? AND ?", [$from, $to]);
            } elseif ($from) {
                $q->whereRaw("COALESCE(odeme_tarihi, created_at) >= ?", [$from]);
            } elseif ($to) {
                $q->whereRaw("COALESCE(odeme_tarihi, created_at) <= ?", [$to]);
            }
        };

        // KPI'lar tarih filtresine tabi
        $baseFiltered = (clone $base);
        $applyDate($baseFiltered);

        // ---- KPI'lar ----
        $totalOrders   = (clone $baseFiltered)->count();
        $totalRevenue  = (clone $baseFiltered)->sum('odenecek_tutar');
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // (İstersen ödeme oranını tamamen kaldırırız; şu an hesaplamıyoruz)
        $paidRate = null;

        // ---- Son 10 sipariş (tarih filtresiz genel görünüm, istersen $baseFiltered yap) ----
        // 1) Hangi ID tutuluyor? (personel.id mi, users.id mi) -> otomatik seç
        $pidUser = (int) $profil->id;
        $pidPers = $personel ? (int) $personel->id : null;

        $pid = null;
        if ($pidPers && DB::table('siparisler')->where('personel_id', $pidPers)->exists()) {
            $pid = $pidPers;
        } elseif (DB::table('siparisler')->where('personel_id', $pidUser)->exists()) {
            $pid = $pidUser;
        } else {
            $pid = $pidPers ?: $pidUser; // fallback
        }

        // 2) Son 10 sipariş ID’si (ödemeyi tercih ederek sırala)
        $orderIds = DB::table('siparisler as s')
            ->where('s.personel_id', $pid)
            ->orderByRaw('COALESCE(s.odeme_tarihi, s.created_at) DESC')
            ->limit(10)
            ->pluck('s.id');

        if ($orderIds->isEmpty()) {
            $lastOrders = collect(); // ekranda "kayıt yok" gösterir
        } else {
            // 3) Sipariş + öğrenci adı (ID sırasını koru)
            $idList = $orderIds->implode(',');
            $orders = DB::table('siparisler as s')
                ->leftJoin('ogrenciler as o', 'o.id', '=', 's.user_id') // sende böyle çalışıyordu
                ->whereIn('s.id', $orderIds)
                ->orderByRaw("FIELD(s.id, $idList)")
                ->get([
                    's.id','s.siparis_no','s.user_id','s.odenecek_tutar','s.satis_kaynak',
                    's.odeme_durum','s.odeme_tarihi','s.created_at',
                    DB::raw("CONCAT_WS(' ', o.isim, o.soyisim) AS ogrenci"),
                ]);

            // 4) Kurs adları (yalnızca kurs kalemleri: satis_turu=1)
            $courses = DB::table('siparis_urunleri as su')
                ->leftJoin('kurslar as k', 'k.id', '=', 'su.urun_id') // kurs_id ise burayı değiştir
                ->whereIn('su.siparis_id', $orderIds)
                ->where('su.satis_turu', 1)
                ->groupBy('su.siparis_id')
                ->selectRaw('
        su.siparis_id AS sid,
        GROUP_CONCAT(DISTINCT k.kurs_adi ORDER BY k.kurs_adi SEPARATOR ", ") AS kurslar
    ')
                ->pluck('kurslar', 'sid');

            // 5) Kurs isimlerini siparişlere iliştir
            $lastOrders = $orders->map(function ($r) use ($courses) {
                $r->kurslar = $courses[$r->id] ?? '-';
                return $r;
            });
        }

        // ---- 6 Aylık grafikler (her zaman son 6 ay) ----
        $start6 = Carbon::now()->startOfMonth()->subMonths(5);
        $end6   = Carbon::now()->endOfMonth();

        $monthly = (clone $base)
            ->whereRaw("COALESCE(odeme_tarihi, created_at) BETWEEN ? AND ?", [$start6, $end6])
            ->selectRaw("
            DATE_FORMAT(COALESCE(odeme_tarihi, created_at), '%Y-%m') as ym,
            COUNT(*) as adet,
            SUM(odenecek_tutar) as tutar
        ")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = []; $dataCount = []; $dataAmount = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $key = $m->format('Y-m');
            $labels[]     = $m->translatedFormat('M Y');
            $dataCount[]  = isset($monthly[$key]) ? (int)$monthly[$key]->adet : 0;
            $dataAmount[] = isset($monthly[$key]) ? (float)$monthly[$key]->tutar : 0.0;
        }

        // ---- Kurslara göre satışlar (sadece satis_turu=1 -> kurs) ----
        // Tablo ve kolon isimleri: siparis_urunleri.satis_turu, siparis_urunleri.kurs_id (veya urun_id)
        $courseChartEnabled = false;
        $courseLabels = [];
        $courseCounts = [];

        if (Schema::hasTable('siparis_urunleri')) {
            $courseCol = Schema::hasColumn('siparis_urunleri', 'kurs_id') ? 'kurs_id'
                : (Schema::hasColumn('siparis_urunleri', 'urun_id') ? 'urun_id' : null);

            if ($courseCol) {
                // Önce tarih filtresi uygulanmış sipariş ID'lerini al
                $filteredOrderIds = (clone $baseFiltered)->pluck('id');

                // Kurs isimleri için kurslar tablosuna join
                $joinCol = $courseCol; // kurs_id veya urun_id
                $detail = DB::table('siparis_urunleri as su')
                    ->join('siparisler as s', 's.id', '=', 'su.siparis_id')
                    ->when($filteredOrderIds->isNotEmpty(), function($q) use ($filteredOrderIds) {
                        $q->whereIn('su.siparis_id', $filteredOrderIds);
                    })
                    ->where('su.satis_turu', 1);

                // kurslar tablosu varsa isim çek
                if (Schema::hasTable('kurslar') && Schema::hasColumn('kurslar','kurs_adi')) {
                    $detail->leftJoin('kurslar as k', "k.id", "=", "su.$joinCol")
                        ->selectRaw("COALESCE(k.kurs_adi, CONCAT('Kurs #', su.$joinCol)) as kurs_adi, COUNT(*) as adet");
                    $groupSelect = "k.kurs_adi, su.$joinCol";
                } else {
                    $detail->selectRaw("CONCAT('Kurs #', su.$joinCol) as kurs_adi, COUNT(*) as adet");
                    $groupSelect = "su.$joinCol";
                }

                $topCourses = $detail
                    ->groupByRaw($groupSelect)
                    ->orderByDesc('adet')
                    ->limit(20)
                    ->get();

                if ($topCourses->count()) {
                    $courseChartEnabled = true;
                    foreach ($topCourses as $c) {
                        $courseLabels[] = $c->kurs_adi;
                        $courseCounts[] = (int)$c->adet;
                    }
                }
            }
        }

        // ---- Büyük sayı kısaltma (K/M/B) + tam TR formatı alt satırda ----
        $short = function ($num) {
            $abs = abs($num);
            if ($abs >= 1_000_000_000) return round($num / 1_000_000_000, 2).'B';
            if ($abs >= 1_000_000)     return round($num / 1_000_000, 2).'M';
            if ($abs >= 1_000)         return round($num / 1_000, 2).'K';
            return number_format($num, 0, ',', '.');
        };
        $totalRevenueShort  = $short($totalRevenue);
        $avgOrderValueShort = $short($avgOrderValue);
        $totalRevenueFull   = number_format($totalRevenue, 2, ',', '.').' ₺';
        $avgOrderValueFull  = number_format($avgOrderValue, 2, ',', '.').' ₺';

        return view(theme_view('admin', 'pages.personel.satislar'), compact(
                'profil',
                'personel',
                'activePersonelId',
                // KPI
                'totalOrders',
                'totalRevenue','totalRevenueShort','totalRevenueFull',
                'avgOrderValue','avgOrderValueShort','avgOrderValueFull',
                // Tablo
                'lastOrders',
                // 6 aylık
                'labels','dataCount','dataAmount',
                // Kurslara göre
                'courseChartEnabled','courseLabels','courseCounts'
            ) + [
                'from' => $from ? $from->toDateString() : null,
                'to'   => $to   ? $to->toDateString()   : null,
            ]);
    }

    /*
    public function performans(Request $request, $id)
    {
        $profil   = User::findOrFail($id);
        $personel = Personel::where('user_id', $id)->first(); // varsa

        // personel_id hangi ID? (personel.id mi users.id mi)
        $pidUser = (int) $profil->id;
        $pidPers = $personel ? (int) $personel->id : null;
        if ($pidPers && DB::table('siparisler')->where('personel_id', $pidPers)->exists()) {
            $pid = $pidPers;
        } elseif (DB::table('siparisler')->where('personel_id', $pidUser)->exists()) {
            $pid = $pidUser;
        } else {
            $pid = $pidPers ?: $pidUser;
        }

        $includeWeekends = $request->boolean('weekends', false); // default: hariç
        $paid = 1;
        $eff  = "COALESCE(s.odeme_tarihi, s.created_at)";

        // ---- Bugün (iş günü filtresi uygulanır) ----
        $today = Carbon::today();
        $todayBase = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->whereRaw("DATE($eff) = ?", [$today->toDateString()]);
        if (!$includeWeekends) {
            $todayBase->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)");
        }

        $todayPerson = (clone $todayBase)->where('s.personel_id', $pid)
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();
        $todayCnt = (int)($todayPerson->cnt ?? 0);
        $todayRev = (float)($todayPerson->rev ?? 0);

        // Ekip bugünkü kişi başı ortalama (aynı gün): toplam / distinct personel
        $teamToday = (clone $todayBase)
            ->selectRaw('COUNT(*) total_cnt, SUM(s.odenecek_tutar) total_rev, COUNT(DISTINCT s.personel_id) persons')
            ->first();
        $teamTodayPersons = max(1, (int)($teamToday->persons ?? 0));
        $teamTodayAvgCnt  = ($teamToday->total_cnt ?? 0) / $teamTodayPersons;
        $teamTodayAvgRev  = ($teamToday->total_rev ?? 0) / $teamTodayPersons;

        // ---- Son 20 iş günü benchmark (günlük kişi başı ort.) ----
        $lastDays = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->when(!$includeWeekends, fn($q) => $q->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)"))
            ->selectRaw("DATE($eff) d")->groupBy('d')
            ->orderBy('d', 'desc')->limit(20)->pluck('d')->toArray();

        $avgPersonCnt20 = 0; $avgPersonRev20 = 0;
        $avgTeamCnt20   = 0; $avgTeamRev20   = 0;

        if ($lastDays) {
            // Person: günlük adet/ciro ortalaması (bu 20 günde, aktif gün sayısına bölerek)
            $personDaily = DB::table('siparisler as s')
                ->where('s.odeme_durum', $paid)
                ->where('s.personel_id', $pid)
                ->whereIn(DB::raw("DATE($eff)"), $lastDays)
                ->selectRaw("DATE($eff) d, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
                ->groupBy('d')->get();
            $daysCount = max(1, $personDaily->count());
            $avgPersonCnt20 = $personDaily->sum('cnt') / $daysCount;
            $avgPersonRev20 = $personDaily->sum('rev') / $daysCount;

            // Ekip: günlük kişi başı ort. (her gün: toplam/kişi) -> günlerin ortalaması
            $teamDaily = DB::table('siparisler as s')
                ->where('s.odeme_durum', $paid)
                ->whereIn(DB::raw("DATE($eff)"), $lastDays)
                ->selectRaw("DATE($eff) d, COUNT(*) total_cnt, SUM(s.odenecek_tutar) total_rev, COUNT(DISTINCT s.personel_id) persons")
                ->groupBy('d')->get();
            $avgTeamCnt20 = $teamDaily->count() ? $teamDaily->map(fn($r) => $r->persons ? $r->total_cnt / $r->persons : 0)->avg() : 0;
            $avgTeamRev20 = $teamDaily->count() ? $teamDaily->map(fn($r) => $r->persons ? $r->total_rev / $r->persons : 0)->avg() : 0;
        }

        // ---- MTD (bu ay başından bugün) vs geçen yıl aynı dönem ----
        $s = Carbon::now()->startOfMonth(); $e = Carbon::today()->endOfDay();
        $sLy = $s->copy()->subYear(); $eLy = $e->copy()->subYear();

        $mtdBase = DB::table('siparisler as s')->where('s.odeme_durum', $paid);
        if (!$includeWeekends) $mtdBase->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)");
        $mtdPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();
        $mtdLyPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$sLy, $eLy])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();

        $mtdCnt = (int)($mtdPerson->cnt ?? 0);
        $mtdRev = (float)($mtdPerson->rev ?? 0);
        $mtdCntLy = (int)($mtdLyPerson->cnt ?? 0);
        $mtdRevLy = (float)($mtdLyPerson->rev ?? 0);

        // ---- Günlük trend (son 60 iş günü): person vs ekip kişi başı ort. ----
        $last60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->when(!$includeWeekends, fn($q) => $q->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)"))
            ->selectRaw("DATE($eff) d")->groupBy('d')
            ->orderBy('d', 'desc')->limit(60)->pluck('d')->toArray();
        sort($last60); // tarihe göre artan

        $person60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereIn(DB::raw("DATE($eff)"), $last60)
            ->selectRaw("DATE($eff) d, COUNT(*) cnt")->groupBy('d')->pluck('cnt','d');

        $team60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->whereIn(DB::raw("DATE($eff)"), $last60)
            ->selectRaw("DATE($eff) d, COUNT(*) total_cnt, COUNT(DISTINCT s.personel_id) persons")
            ->groupBy('d')->get()->keyBy('d');

        $dailyLabels = []; $dailyPerson = []; $dailyTeamAvg = [];
        foreach ($last60 as $d) {
            $dailyLabels[] = Carbon::parse($d)->format('d M');
            $p = (int)($person60[$d] ?? 0);
            $t = $team60[$d] ?? null;
            $avg = ($t && $t->persons) ? ($t->total_cnt / $t->persons) : 0;
            $dailyPerson[]  = $p;
            $dailyTeamAvg[] = round($avg, 2);
        }

        // ---- Aylık trend (son 12 ay) + geçen yıl aynı ay ----
        $start12 = Carbon::now()->startOfMonth()->subMonths(11);
        $months = [];
        for ($i=0; $i<12; $i++) {
            $m = $start12->copy()->addMonths($i);
            $months[] = $m->format('Y-m');
        }

        $person12 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$start12, Carbon::now()->endOfMonth()])
            ->selectRaw("DATE_FORMAT($eff, '%Y-%m') ym, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
            ->groupBy('ym')->get()->keyBy('ym');

        $person12Ly = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$start12->copy()->subYear(), Carbon::now()->endOfMonth()->copy()->subYear()])
            ->selectRaw("DATE_FORMAT($eff, '%Y-%m') ym, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
            ->groupBy('ym')->get()->keyBy('ym');

        $monthLabels = []; $monthCnt = []; $monthCntLy = [];
        foreach ($months as $ym) {
            $monthLabels[] = Carbon::createFromFormat('Y-m', $ym)->translatedFormat('M Y');
            $monthCnt[]   = (int)($person12[$ym]->cnt ?? 0);
            // geçen yıl aynı ay: anahtar (Y-1)-m
            [$y,$m] = explode('-', $ym);
            $ymLy = ($y-1).'-'.$m;
            $monthCntLy[] = (int)($person12Ly[$ymLy]->cnt ?? 0);
        }

        // ---- Sıralama (bugün & MTD) ----
        $rankTodayData = (clone $todayBase)
            ->selectRaw('s.personel_id, COUNT(*) cnt')->groupBy('s.personel_id')
            ->orderByDesc('cnt')->pluck('cnt','personel_id');
        $rankToday     = $rankTodayData->keys()->search($pid);
        $rankToday     = is_int($rankToday) ? ($rankToday+1) : null;
        $teamSizeToday = $rankTodayData->count();
        $teamAvgToday  = $teamSizeToday ? ($rankTodayData->sum() / $teamSizeToday) : 0;

        $mtdRankData = (clone $mtdBase)->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('s.personel_id, COUNT(*) cnt')->groupBy('s.personel_id')
            ->orderByDesc('cnt')->pluck('cnt','personel_id');
        $rankMTD     = ($i = $mtdRankData->keys()->search($pid)) !== false ? ($i+1) : null;
        $teamSizeMTD = $mtdRankData->count();
        $teamAvgMTD  = $teamSizeMTD ? ($mtdRankData->sum() / $teamSizeMTD) : 0;

        // Kısaltma yardımcıları
        $fmtMoney = fn($n) => number_format($n, 2, ',', '.').' ₺';
        $short = function($n){ $a=abs($n); return $a>=1e9?round($n/1e9,2).'B':($a>=1e6?round($n/1e6,2).'M':($a>=1e3?round($n/1e3,2).'K':(string)(int)$n)); };

        // Ekip MTD toplamları ve kişi başı ortalamalar (SATIŞ ve CİRO)
        $mtdTeam = (clone $mtdBase)
            ->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('COUNT(*) as total_cnt, SUM(s.odenecek_tutar) as total_rev, COUNT(DISTINCT s.personel_id) as persons')
            ->first();

        $teamMtdPersons  = max(1, (int)($mtdTeam->persons ?? 0));
        $teamMtdAvgCnt   = ($mtdTeam->total_cnt ?? 0) / $teamMtdPersons;         // kişi başı ort. satış
        $teamMtdAvgRev   = ($mtdTeam->total_rev ?? 0) / $teamMtdPersons;         // kişi başı ort. ciro

        // Ekip MTD kişi başı ort. GÖRÜŞME (opsiyonel: tablo varsa)
        $teamMtdAvgMeetings = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('gorusmeler')) {
            $meetBase = DB::table('gorusmeler as g')->whereBetween('g.created_at', [$s, $e]);
            if (!$includeWeekends) {
                $meetBase->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            }
            $meetTeam = (clone $meetBase)
                ->selectRaw('COUNT(*) as total_meet, COUNT(DISTINCT g.personel_id) as persons')
                ->first();
            $mPersons = max(1, (int)($meetTeam->persons ?? 0));
            $teamMtdAvgMeetings = ($meetTeam->total_meet ?? 0) / $mPersons;      // kişi başı ort. görüşme
        }

        // --- Geçen ay (aynı gün sayısına kadar) ---
        $todayDay = now()->day;
        $sPrev = now()->subMonthNoOverflow()->startOfMonth();
        $ePrev = $sPrev->copy()->addDays($todayDay - 1)->endOfDay();
        $ePrevCap = $sPrev->copy()->endOfMonth();
        if ($ePrev->gt($ePrevCap)) $ePrev = $ePrevCap;

        $mtdPrevPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$sPrev, $ePrev])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')
            ->first();

        $mtdPrevCnt = (int)($mtdPrevPerson->cnt ?? 0);
        $mtdPrevRev = (float)($mtdPrevPerson->rev ?? 0);

        // --- Şirket kişi başı Ortalama Satış Adedi (Bu Ay & Geçen Ay)
        //     (yalnızca ilgili dönemde EN AZ 10 görüşmesi olan personeller dahil)
        $teamAvgSalesThis = null;
        $teamAvgSalesPrev = null;

        if (\Illuminate\Support\Facades\Schema::hasTable('gorusmeler')) {
            // Bu Ay: yeterli görüşmesi olan personeller
            $meetCurr = DB::table('gorusmeler as g')
                ->whereBetween('g.created_at', [$s, $e]);
            if (!$includeWeekends) $meetCurr->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligibleCurr = $meetCurr
                ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');

            // Geçen Ay (aynı gün sayısına kadar)
            $meetPrev = DB::table('gorusmeler as g')
                ->whereBetween('g.created_at', [$sPrev, $ePrev]);
            if (!$includeWeekends) $meetPrev->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligiblePrev = $meetPrev
                ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');

            // Bu Ay: kişi başı satış adedi (paid)
            if ($eligibleCurr->isNotEmpty()) {
                $rows = DB::table('siparisler as s')
                    ->where('s.odeme_durum', $paid)
                    ->whereBetween(DB::raw($eff), [$s, $e])
                    ->whereIn('s.personel_id', $eligibleCurr)
                    ->selectRaw('s.personel_id, COUNT(*) cnt')
                    ->groupBy('s.personel_id')
                    ->get();
                $teamAvgSalesThis = $rows->avg('cnt') ?? 0;
            } else {
                $teamAvgSalesThis = 0;
            }

            // Geçen Ay: kişi başı satış adedi (paid)
            if ($eligiblePrev->isNotEmpty()) {
                $rowsPrev = DB::table('siparisler as s')
                    ->where('s.odeme_durum', $paid)
                    ->whereBetween(DB::raw($eff), [$sPrev, $ePrev])
                    ->whereIn('s.personel_id', $eligiblePrev)
                    ->selectRaw('s.personel_id, COUNT(*) cnt')
                    ->groupBy('s.personel_id')
                    ->get();
                $teamAvgSalesPrev = $rowsPrev->avg('cnt') ?? 0;
            } else {
                $teamAvgSalesPrev = 0;
            }
        } else {
            // gorusmeler tablosu yoksa 0 kabul et (ya da null bırakıp Blade'de gizleyebilirsin)
            $teamAvgSalesThis = 0;
            $teamAvgSalesPrev = 0;
        }

        // --- Performans Notu (0-100) ---

        // --- Performans Notu (0–100) — GEÇEN AY penceresi (tam ay) ---
        $prevStart = now()->subMonthNoOverflow()->startOfMonth();
        $prevEnd   = $prevStart->copy()->endOfMonth();
        $ps = $prevStart;  // score window start
        $pe = $prevEnd;    // score window end
        $perfLabel = $ps->translatedFormat('F Y'); // örn: "Ağustos 2025" (locale tr ise)

        $wSales = 0.3; $wRev = 0.6; $wMeet = 0.1;

        // Eligible set: ilgili pencerede ≥10 görüşmesi olan personeller
        $eligibleIds = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('gorusmeler')) {
            $meetQ = DB::table('gorusmeler as g')->whereBetween('g.created_at', [$ps, $pe]);
            if (!$includeWeekends) $meetQ->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligibleIds = $meetQ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');
        }
        if ($eligibleIds->isEmpty()) {
            // görüşme verisi yoksa/azsa satış yapanları baz al
            $eligibleIds = DB::table('siparisler as s')
                ->where('s.odeme_durum', 1)
                ->whereBetween(DB::raw($eff), [$ps, $pe])
                ->select('s.personel_id')->distinct()->pluck('personel_id');
        }
        if (!$eligibleIds->contains($pid)) $eligibleIds->push($pid);

        // Kişi bazlı satış/ciro (geçen ay)
        $rowsSales = DB::table('siparisler as s')
            ->where('s.odeme_durum', 1)
            ->whereBetween(DB::raw($eff), [$ps, $pe])
            ->whereIn('s.personel_id', $eligibleIds)
            ->selectRaw('s.personel_id, COUNT(*) cnt, SUM(s.odenecek_tutar) rev')
            ->groupBy('s.personel_id')->get()->keyBy('personel_id');

        // Kişi bazlı görüşme (geçen ay)
        $meets = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('gorusmeler')) {
            $meetQ2 = DB::table('gorusmeler as g')
                ->whereBetween('g.created_at', [$ps, $pe])
                ->whereIn('g.personel_id', $eligibleIds);
            if (!$includeWeekends) $meetQ2->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $meets = $meetQ2->selectRaw('g.personel_id, COUNT(*) m')
                ->groupBy('g.personel_id')->pluck('m','personel_id');
        }

        // Dağılımlar
        $salesDist = []; $revDist = []; $meetDist = [];
        foreach ($eligibleIds as $pidX) {
            $salesDist[] = (int)($rowsSales[$pidX]->cnt ?? 0);
            $revDist[]   = (float)($rowsSales[$pidX]->rev ?? 0);
            $meetDist[]  = (int)($meets[$pidX] ?? 0);
        }

        // Min–max normalizasyon (en kötü 0, en iyi 100)
        $minmax = function(array $arr, $val): float {
            if (!$arr) return 0.0;
            $min = min($arr); $max = max($arr);
            if ($max == $min) return 50.0; // herkes aynıysa orta puan
            return max(0.0, min(100.0, ($val - $min) / ($max - $min) * 100.0));
        };

        // Hedef kişi değerleri
        $mySales = (int)($rowsSales[$pid]->cnt ?? 0);
        $myRev   = (float)($rowsSales[$pid]->rev ?? 0);
        $myMeet  = (int)($meets[$pid] ?? 0);

        // Bileşen skorları (0..100)
        $sScore = $minmax($salesDist, $mySales);
        $rScore = $minmax($revDist,   $myRev);
        $mScore = $minmax($meetDist,  $myMeet);

        // Ağırlıklı toplam ve ekip ortalaması
        $perfScore = (int) round($wSales*$sScore + $wRev*$rScore + $wMeet*$mScore);

        $allScores = [];
        foreach ($eligibleIds as $pidX) {
            $ss = $minmax($salesDist, (int)($rowsSales[$pidX]->cnt ?? 0));
            $rs = $minmax($revDist,   (float)($rowsSales[$pidX]->rev ?? 0));
            $ms = $minmax($meetDist,  (int)($meets[$pidX] ?? 0));
            $allScores[] = $wSales*$ss + $wRev*$rs + $wMeet*$ms;
        }
        $perfScoreAvg = $allScores ? round(array_sum($allScores)/count($allScores), 1) : 50.0;


        return view(
            theme_view('admin','pages.personel.performans'),
            compact(
                'profil',
                'personel',
                'includeWeekends',
                // KPI
                'todayCnt','todayRev',
                'avgPersonCnt20','avgTeamCnt20',
                'avgPersonRev20','avgTeamRev20',
                'mtdCnt','mtdRev','mtdCntLy','mtdRevLy',
                // Trend
                'dailyLabels','dailyPerson','dailyTeamAvg',
                'monthLabels','monthCnt','monthCntLy',
                // Rank
                'rankToday','teamSizeToday','teamAvgToday',
                'rankMTD','teamSizeMTD','teamAvgMTD',
                // format helpers
                'fmtMoney','short',
                'teamTodayAvgCnt',
                'teamTodayAvgRev',
                'teamMtdAvgCnt','teamMtdAvgRev','teamMtdAvgMeetings',
                'mtdPrevCnt','mtdPrevRev',
                'teamAvgSalesThis','teamAvgSalesPrev',
                'perfScore','perfScoreAvg'
            )
        );
    }
    */

    public function performans(Request $request, $id)
    {
        $profil   = User::findOrFail($id);
        $personel = Personel::where('user_id', $id)->first(); // varsa

        // personel_id hangi ID? (personel.id mi users.id mi) — NULL güvenli
        $pidUser = (int) $profil->id;
        $pidPers = $personel ? (int) $personel->id : null;

        if ($pidPers && DB::table('siparisler')->where('personel_id', $pidPers)->exists()) {
            $pid = $pidPers;
        } elseif (DB::table('siparisler')->where('personel_id', $pidUser)->exists()) {
            $pid = $pidUser;
        } else {
            $pid = $pidPers ?: $pidUser;
        }

        $includeWeekends = $request->boolean('weekends', false); // default: hariç
        $paid = 1;
        $eff  = "COALESCE(s.odeme_tarihi, s.created_at)";

        // ---- Bugün (iş günü filtresi uygulanır) ----
        $today = Carbon::today();
        $todayBase = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->whereRaw("DATE($eff) = ?", [$today->toDateString()]);
        if (!$includeWeekends) {
            $todayBase->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)");
        }

        $todayPerson = (clone $todayBase)->where('s.personel_id', $pid)
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();
        $todayCnt = (int)($todayPerson->cnt ?? 0);
        $todayRev = (float)($todayPerson->rev ?? 0);

        // Ekip bugünkü kişi başı ortalama (aynı gün): toplam / distinct personel
        $teamToday = (clone $todayBase)
            ->selectRaw('COUNT(*) total_cnt, SUM(s.odenecek_tutar) total_rev, COUNT(DISTINCT s.personel_id) persons')
            ->first();
        $teamTodayPersons = max(1, (int)($teamToday->persons ?? 0));
        $teamTodayAvgCnt  = ($teamToday->total_cnt ?? 0) / $teamTodayPersons;
        $teamTodayAvgRev  = ($teamToday->total_rev ?? 0) / $teamTodayPersons;

        // ---- Son 20 iş günü benchmark (günlük kişi başı ort.) ----
        $lastDays = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->when(!$includeWeekends, fn($q) => $q->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)"))
            ->selectRaw("DATE($eff) d")->groupBy('d')
            ->orderBy('d', 'desc')->limit(20)->pluck('d')->toArray();

        $avgPersonCnt20 = 0; $avgPersonRev20 = 0;
        $avgTeamCnt20   = 0; $avgTeamRev20   = 0;

        if ($lastDays) {
            // Person: günlük adet/ciro ortalaması
            $personDaily = DB::table('siparisler as s')
                ->where('s.odeme_durum', $paid)
                ->where('s.personel_id', $pid)
                ->whereIn(DB::raw("DATE($eff)"), $lastDays)
                ->selectRaw("DATE($eff) d, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
                ->groupBy('d')->get();
            $daysCount = max(1, $personDaily->count());
            $avgPersonCnt20 = $personDaily->sum('cnt') / $daysCount;
            $avgPersonRev20 = $personDaily->sum('rev') / $daysCount;

            // Ekip: günlük kişi başı ort. (her gün: toplam/kişi) -> günlerin ortalaması
            $teamDaily = DB::table('siparisler as s')
                ->where('s.odeme_durum', $paid)
                ->whereIn(DB::raw("DATE($eff)"), $lastDays)
                ->selectRaw("DATE($eff) d, COUNT(*) total_cnt, SUM(s.odenecek_tutar) total_rev, COUNT(DISTINCT s.personel_id) persons")
                ->groupBy('d')->get();
            $avgTeamCnt20 = $teamDaily->count() ? $teamDaily->map(fn($r) => $r->persons ? $r->total_cnt / $r->persons : 0)->avg() : 0;
            $avgTeamRev20 = $teamDaily->count() ? $teamDaily->map(fn($r) => $r->persons ? $r->total_rev / $r->persons : 0)->avg() : 0;
        }

        // ---- MTD (bu ay başından bugün) vs geçen yıl aynı dönem ----
        $s = Carbon::now()->startOfMonth(); $e = Carbon::today()->endOfDay();
        $sLy = $s->copy()->subYear(); $eLy = $e->copy()->subYear();

        $mtdBase = DB::table('siparisler as s')->where('s.odeme_durum', $paid);
        if (!$includeWeekends) $mtdBase->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)");
        $mtdPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();
        $mtdLyPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$sLy, $eLy])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')->first();

        $mtdCnt = (int)($mtdPerson->cnt ?? 0);
        $mtdRev = (float)($mtdPerson->rev ?? 0);
        $mtdCntLy = (int)($mtdLyPerson->cnt ?? 0);
        $mtdRevLy = (float)($mtdLyPerson->rev ?? 0);

        // ---- Günlük trend (son 60 iş günü): person vs ekip kişi başı ort. ----
        $last60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->when(!$includeWeekends, fn($q) => $q->whereRaw("DAYOFWEEK($eff) NOT IN (1,7)"))
            ->selectRaw("DATE($eff) d")->groupBy('d')
            ->orderBy('d', 'desc')->limit(60)->pluck('d')->toArray();
        sort($last60); // tarihe göre artan

        $person60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereIn(DB::raw("DATE($eff)"), $last60)
            ->selectRaw("DATE($eff) d, COUNT(*) cnt")->groupBy('d')->pluck('cnt','d');

        $team60 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)
            ->whereIn(DB::raw("DATE($eff)"), $last60)
            ->selectRaw("DATE($eff) d, COUNT(*) total_cnt, COUNT(DISTINCT s.personel_id) persons")
            ->groupBy('d')->get()->keyBy('d');

        $dailyLabels = []; $dailyPerson = []; $dailyTeamAvg = [];
        foreach ($last60 as $d) {
            $dailyLabels[] = Carbon::parse($d)->format('d M');
            $p = (int)($person60[$d] ?? 0);
            $t = $team60[$d] ?? null;
            $avg = ($t && $t->persons) ? ($t->total_cnt / $t->persons) : 0;
            $dailyPerson[]  = $p;
            $dailyTeamAvg[] = round($avg, 2);
        }

        // ---- Aylık trend (son 12 ay) + geçen yıl aynı ay ----
        $start12 = Carbon::now()->startOfMonth()->subMonths(11);
        $months = [];
        for ($i=0; $i<12; $i++) {
            $cm = $start12->copy()->addMonths($i);
            $months[] = $cm->format('Y-m');
        }

        $person12 = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$start12, Carbon::now()->endOfMonth()])
            ->selectRaw("DATE_FORMAT($eff, '%Y-%m') ym, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
            ->groupBy('ym')->get()->keyBy('ym');

        $person12Ly = DB::table('siparisler as s')
            ->where('s.odeme_durum', $paid)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$start12->copy()->subYear(), Carbon::now()->endOfMonth()->copy()->subYear()])
            ->selectRaw("DATE_FORMAT($eff, '%Y-%m') ym, COUNT(*) cnt, SUM(s.odenecek_tutar) rev")
            ->groupBy('ym')->get()->keyBy('ym');

        $monthLabels = []; $monthCnt = []; $monthCntLy = [];
        foreach ($months as $ym) {
            $monthLabels[] = Carbon::createFromFormat('Y-m', $ym)->translatedFormat('M Y');
            $monthCnt[]   = (int)($person12[$ym]->cnt ?? 0);
            [$yy,$mm] = explode('-', $ym);
            $ymLy = ($yy-1).'-'.$mm;
            $monthCntLy[] = (int)($person12Ly[$ymLy]->cnt ?? 0);
        }

        // ---- Sıralama (bugün & MTD) ----
        $rankTodayData = (clone $todayBase)
            ->selectRaw('s.personel_id, COUNT(*) cnt')->groupBy('s.personel_id')
            ->orderByDesc('cnt')->pluck('cnt','personel_id');
        $rankToday     = $rankTodayData->keys()->search($pid);
        $rankToday     = is_int($rankToday) ? ($rankToday+1) : null;
        $teamSizeToday = $rankTodayData->count();
        $teamAvgToday  = $teamSizeToday ? ($rankTodayData->sum() / $teamSizeToday) : 0;

        $mtdRankData = (clone $mtdBase)->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('s.personel_id, COUNT(*) cnt')->groupBy('s.personel_id')
            ->orderByDesc('cnt')->pluck('cnt','personel_id');
        $rankMTD     = ($i = $mtdRankData->keys()->search($pid)) !== false ? ($i+1) : null;
        $teamSizeMTD = $mtdRankData->count();
        $teamAvgMTD  = $teamSizeMTD ? ($mtdRankData->sum() / $teamSizeMTD) : 0;

        // Kısaltma yardımcıları
        $fmtMoney = fn($n) => number_format($n, 2, ',', '.').' ₺';
        $short = function($n){ $a=abs($n); return $a>=1e9?round($n/1e9,2).'B':($a>=1e6?round($n/1e6,2).'M':($a>=1e3?round($n/1e3,2).'K':(string)(int)$n)); };

        // --------- Ekip MTD toplamları ve kişi başı ortalamalar (SATIŞ ve CİRO) ----------
        $mtdTeam = (clone $mtdBase)
            ->whereBetween(DB::raw($eff), [$s, $e])
            ->selectRaw('COUNT(*) as total_cnt, SUM(s.odenecek_tutar) as total_rev, COUNT(DISTINCT s.personel_id) as persons')
            ->first();

        $teamMtdPersons  = max(1, (int)($mtdTeam->persons ?? 0));
        $teamMtdAvgCnt   = ($mtdTeam->total_cnt ?? 0) / $teamMtdPersons;   // kişi başı ort. satış
        $teamMtdAvgRev   = ($mtdTeam->total_rev ?? 0) / $teamMtdPersons;   // kişi başı ort. ciro

        // --------- Ekip MTD kişi başı ort. GÖRÜŞME (data_gorusmeler) ----------
        $teamMtdAvgMeetings = 0.0;
        $meetingsEnabled = false;
        if (\Illuminate\Support\Facades\Schema::hasTable('data_gorusmeler')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','personel_id')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','created_at')) {

            $meetBase = DB::table('data_gorusmeler as g')->whereBetween('g.created_at', [$s, $e]);
            if (!$includeWeekends) {
                $meetBase->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            }
            $meetTeam = (clone $meetBase)
                ->selectRaw('COUNT(*) as total_meet, COUNT(DISTINCT g.personel_id) as persons')
                ->first();

            $mPersons = max(1, (int)($meetTeam->persons ?? 0));
            $teamMtdAvgMeetings = (float) (($meetTeam->total_meet ?? 0) / $mPersons);
            $meetingsEnabled = true;
        }

        // --- Geçen ay (aynı gün sayısına kadar) ---
        $todayDay = now()->day;
        $sPrev = now()->subMonthNoOverflow()->startOfMonth();
        $ePrev = $sPrev->copy()->addDays($todayDay - 1)->endOfDay();
        $ePrevCap = $sPrev->copy()->endOfMonth();
        if ($ePrev->gt($ePrevCap)) $ePrev = $ePrevCap;

        $mtdPrevPerson = (clone $mtdBase)->where('s.personel_id', $pid)
            ->whereBetween(DB::raw($eff), [$sPrev, $ePrev])
            ->selectRaw('COUNT(*) cnt, SUM(s.odenecek_tutar) rev')
            ->first();

        $mtdPrevCnt = (int)($mtdPrevPerson->cnt ?? 0);
        $mtdPrevRev = (float)($mtdPrevPerson->rev ?? 0);

        // --- Şirket kişi başı Ortalama Satış Adedi (Bu Ay & Geçen Ay) — eligible: ≥10 görüşme (data_gorusmeler)
        $teamAvgSalesThis = 0.0;
        $teamAvgSalesPrev = 0.0;

        if (\Illuminate\Support\Facades\Schema::hasTable('data_gorusmeler')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','personel_id')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','created_at')) {

            // Bu Ay eligible
            $meetCurr = DB::table('data_gorusmeler as g')->whereBetween('g.created_at', [$s, $e]);
            if (!$includeWeekends) $meetCurr->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligibleCurr = $meetCurr
                ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');

            // Geçen Ay eligible (aynı gün sayısına kadar)
            $meetPrev = DB::table('data_gorusmeler as g')->whereBetween('g.created_at', [$sPrev, $ePrev]);
            if (!$includeWeekends) $meetPrev->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligiblePrev = $meetPrev
                ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');

            // Bu Ay: kişi başı satış adedi
            if ($eligibleCurr->isNotEmpty()) {
                $rows = DB::table('siparisler as s')
                    ->where('s.odeme_durum', $paid)
                    ->whereBetween(DB::raw($eff), [$s, $e])
                    ->whereIn('s.personel_id', $eligibleCurr)
                    ->selectRaw('s.personel_id, COUNT(*) cnt')
                    ->groupBy('s.personel_id')
                    ->get();
                $teamAvgSalesThis = (float) ($rows->avg('cnt') ?? 0);
            }

            // Geçen Ay: kişi başı satış adedi
            if ($eligiblePrev->isNotEmpty()) {
                $rowsPrev = DB::table('siparisler as s')
                    ->where('s.odeme_durum', $paid)
                    ->whereBetween(DB::raw($eff), [$sPrev, $ePrev])
                    ->whereIn('s.personel_id', $eligiblePrev)
                    ->selectRaw('s.personel_id, COUNT(*) cnt')
                    ->groupBy('s.personel_id')
                    ->get();
                $teamAvgSalesPrev = (float) ($rowsPrev->avg('cnt') ?? 0);
            }
        }

        // --- Performans Notu (0–100) — GEÇEN AY (tam ay) ---
        $prevStart = now()->subMonthNoOverflow()->startOfMonth();
        $prevEnd   = $prevStart->copy()->endOfMonth();
        $ps = $prevStart;  // score window start
        $pe = $prevEnd;    // score window end
        $perfLabel = $ps->translatedFormat('F Y'); // örn: "Ağustos 2025"

        $wSales = 0.3; $wRev = 0.6; $wMeet = 0.1;

        // Eligible set (data_gorusmeler varsa ≥10 görüşme, yoksa satış yapanlar)
        $eligibleIds = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('data_gorusmeler')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','personel_id')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','created_at')) {

            $meetQ = DB::table('data_gorusmeler as g')->whereBetween('g.created_at', [$ps, $pe]);
            if (!$includeWeekends) $meetQ->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $eligibleIds = $meetQ->selectRaw('g.personel_id, COUNT(*) c')
                ->groupBy('g.personel_id')
                ->having('c', '>=', 10)
                ->pluck('g.personel_id');
        }
        if ($eligibleIds->isEmpty()) {
            $eligibleIds = DB::table('siparisler as s')
                ->where('s.odeme_durum', 1)
                ->whereBetween(DB::raw($eff), [$ps, $pe])
                ->select('s.personel_id')->distinct()->pluck('personel_id');
        }
        if (!$eligibleIds->contains($pid)) $eligibleIds->push($pid);

        // Kişi bazlı satış/ciro (geçen ay)
        $rowsSales = DB::table('siparisler as s')
            ->where('s.odeme_durum', 1)
            ->whereBetween(DB::raw($eff), [$ps, $pe])
            ->whereIn('s.personel_id', $eligibleIds)
            ->selectRaw('s.personel_id, COUNT(*) cnt, SUM(s.odenecek_tutar) rev')
            ->groupBy('s.personel_id')->get()->keyBy('personel_id');

        // Kişi bazlı görüşme (geçen ay)
        $meets = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('data_gorusmeler')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','personel_id')
            && \Illuminate\Support\Facades\Schema::hasColumn('data_gorusmeler','created_at')) {

            $meetQ2 = DB::table('data_gorusmeler as g')
                ->whereBetween('g.created_at', [$ps, $pe])
                ->whereIn('g.personel_id', $eligibleIds);
            if (!$includeWeekends) $meetQ2->whereRaw('DAYOFWEEK(g.created_at) NOT IN (1,7)');
            $meets = $meetQ2->selectRaw('g.personel_id, COUNT(*) m')
                ->groupBy('g.personel_id')->pluck('m','personel_id');
        }

        // Dağılımlar
        $salesDist = []; $revDist = []; $meetDist = [];
        foreach ($eligibleIds as $pidX) {
            $salesDist[] = (int)($rowsSales[$pidX]->cnt ?? 0);
            $revDist[]   = (float)($rowsSales[$pidX]->rev ?? 0);
            $meetDist[]  = (int)($meets[$pidX] ?? 0);
        }

        // Min–max normalizasyon
        $minmax = function(array $arr, $val): float {
            if (!$arr) return 0.0;
            $min = min($arr); $max = max($arr);
            if ($max == $min) return 50.0; // herkes aynıysa orta puan
            return max(0.0, min(100.0, ($val - $min) / ($max - $min) * 100.0));
        };

        // Hedef kişi
        $mySales = (int)($rowsSales[$pid]->cnt ?? 0);
        $myRev   = (float)($rowsSales[$pid]->rev ?? 0);
        $myMeet  = (int)($meets[$pid] ?? 0);

        // Bileşen skorları & toplam
        $sScore = $minmax($salesDist, $mySales);
        $rScore = $minmax($revDist,   $myRev);
        $mScore = $minmax($meetDist,  $myMeet);
        $perfScore = (int) round($wSales*$sScore + $wRev*$rScore + $wMeet*$mScore);

        // Ekip ortalama skor
        $allScores = [];
        foreach ($eligibleIds as $pidX) {
            $ss = $minmax($salesDist, (int)($rowsSales[$pidX]->cnt ?? 0));
            $rs = $minmax($revDist,   (float)($rowsSales[$pidX]->rev ?? 0));
            $ms = $minmax($meetDist,  (int)($meets[$pidX] ?? 0));
            $allScores[] = $wSales*$ss + $wRev*$rs + $wMeet*$ms;
        }
        $perfScoreAvg = $allScores ? round(array_sum($allScores)/count($allScores), 1) : 50.0;

        return view(
            theme_view('admin','pages.personel.performans'),
            compact(
                'profil',
                'personel',
                'includeWeekends',
                // KPI
                'todayCnt','todayRev',
                'avgPersonCnt20','avgTeamCnt20',
                'avgPersonRev20','avgTeamRev20',
                'mtdCnt','mtdRev','mtdCntLy','mtdRevLy',
                // Trend
                'dailyLabels','dailyPerson','dailyTeamAvg',
                'monthLabels','monthCnt','monthCntLy',
                // Rank
                'rankToday','teamSizeToday','teamAvgToday',
                'rankMTD','teamSizeMTD','teamAvgMTD',
                // format helpers
                'fmtMoney','short',
                'teamTodayAvgCnt','teamTodayAvgRev',
                'teamMtdAvgCnt','teamMtdAvgRev',
                'teamMtdAvgMeetings','meetingsEnabled',
                'mtdPrevCnt','mtdPrevRev',
                'teamAvgSalesThis','teamAvgSalesPrev',
                'perfScore','perfScoreAvg','perfLabel'
            )
        );
    }

    public function gorusmeler(Request $request, $id)
    {
        $profil   = \App\Models\User::findOrFail($id);
        $personel = \App\Models\Personel::where('user_id', $id)->first();

        // personel_id seçiminde güvenli fallback
        $pidUser = (int)$profil->id;
        $pidPers = $personel ? (int)$personel->id : null;
        if ($pidPers && DB::table('siparisler')->where('personel_id', $pidPers)->exists()) {
            $pid = $pidPers;
        } elseif (DB::table('siparisler')->where('personel_id', $pidUser)->exists()) {
            $pid = $pidUser;
        } else {
            $pid = $pidPers ?: $pidUser;
        }

        // Tarih aralığı (bu ay başı → bugün)
        $from = $request->filled('from') ? Carbon::parse($request->get('from'))->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->get('to'))->endOfDay()   : Carbon::today()->endOfDay();

        // Güvenlik: tablo/kolonlar var mı?
        if (!Schema::hasTable('data') ||
            !Schema::hasColumn('data','personel_id') ||
            !Schema::hasColumn('data','created_at') ||
            !Schema::hasColumn('data','durum')) {

            return view(theme_view('admin','pages.personel.gorusmeler'), [
                'profil' => $profil,
                'personel' => $personel,
                'includeWeekends' => $includeWeekends,
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
                // hepsi 0/boş
                'totalMeetings' => 0, 'countKayit' => 0, 'countRandevu' => 0, 'countOlumsuz' => 0,
                'posRate' => 0, 'negRate' => 0,
                'dailyLabels' => [], 'dailyCounts' => [],
                'statusLabels' => [], 'statusCounts' => [],
                'negLabels' => [], 'negCounts' => [],
                'lastMeetings' => collect(),
                'durumMap' => [], 'negMap' => [],
            ]);
        }

        // Settings: durum / olumsuz map'leri
        $durumMap = DB::table('settings')->where('type','data_durum')->pluck('value','key')->toArray();   // key => "Aktif"
        $negMap   = DB::table('settings')->where('type','data_olumsuz')->pluck('value','key')->toArray(); // key => "Fiyat Fazla"

        // Anahtarlar (Kayıt / Randevu / Olumsuz)
        $keyKayit   = DB::table('settings')->where(['type'=>'data_durum','value'=>'Kayıt'])->value('key');
        $keyRandevu = DB::table('settings')->where(['type'=>'data_durum','value'=>'Randevu'])->value('key');
        $keyOlumsuz = DB::table('settings')->where(['type'=>'data_durum','value'=>'Olumsuz'])->value('key');

        // Base query (DATA tablosu)
        $base = DB::table('data as g')
            ->where('g.personel_id', $pid)
            ->whereBetween('g.created_at', [$from, $to]);

        // KPI'lar
        $totalMeetings = (clone $base)->count();

        $countKayit   = $keyKayit   ? (clone $base)->where('g.durum', $keyKayit)->count()   : 0;
        $countRandevu = $keyRandevu ? (clone $base)->where('g.durum', $keyRandevu)->count() : 0;
        $countOlumsuz = $keyOlumsuz ? (clone $base)->where('g.durum', $keyOlumsuz)->count() : 0;

        $posRate = $totalMeetings ? round(100 * ($countKayit + $countRandevu) / $totalMeetings, 1) : 0;
        $negRate = $totalMeetings ? round(100 * $countOlumsuz / $totalMeetings, 1) : 0;

        // Günlük trend
        $dailyRaw = (clone $base)
            ->selectRaw('DATE(g.created_at) d, COUNT(*) c')
            ->groupBy('d')
            ->pluck('c','d');

        $dailyLabels = [];
        $dailyCounts = [];
        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $dailyLabels[] = $cursor->format('d M');
            $dailyCounts[] = (int)($dailyRaw[$key] ?? 0);
            $cursor->addDay();
        }

        // Durum dağılımı
        $statusRows = (clone $base)
            ->selectRaw('g.durum as k, COUNT(*) as c')
            ->groupBy('k')
            ->get();

        $statusLabels = [];
        $statusCounts = [];
        foreach ($statusRows as $r) {
            $statusLabels[] = $durumMap[$r->k] ?? (string)$r->k;
            $statusCounts[] = (int)$r->c;
        }

        // Olumsuz nedenler (Top 5)
        $negLabels = [];
        $negCounts = [];
        if ($keyOlumsuz) {
            $negRows = (clone $base)
                ->where('g.durum', $keyOlumsuz)
                ->whereNotNull('g.olumsuz_id')
                ->selectRaw('g.olumsuz_id as k, COUNT(*) c')
                ->groupBy('k')
                ->orderByDesc('c')
                ->limit(5)
                ->get();

            foreach ($negRows as $r) {
                $negLabels[] = $negMap[$r->k] ?? (string)$r->k;
                $negCounts[] = (int)$r->c;
            }
        }

        // Son 10 görüşme (BUGÜN) – isim, kurs, tarih, durum (+ olumsuz nedeni için olumsuz_id)
        $lastMeetings = DB::table('data as g')
            ->leftJoin('kurslar as k', 'k.id', '=', 'g.kurs_id')
            ->where('g.personel_id', isset($pidData) ? $pidData : $pid)
            ->orderBy('g.created_at', 'desc')
            ->limit(25)
            ->get([
                'g.id',
                'g.isim',
                'k.kurs_adi',
                'g.created_at',
                'g.durum',
                'g.olumsuz_id',
            ]);

        return view(theme_view('admin','pages.personel.gorusmeler'), [
            'profil' => $profil,
            'personel' => $personel,
            'from' => $from->toDateString(),
            'to'   => $to->toDateString(),

            // KPI
            'totalMeetings' => $totalMeetings,
            'countKayit' => $countKayit,
            'countRandevu' => $countRandevu,
            'countOlumsuz' => $countOlumsuz,
            'posRate' => $posRate,
            'negRate' => $negRate,

            // Grafik verileri
            'dailyLabels' => $dailyLabels,
            'dailyCounts' => $dailyCounts,
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            'negLabels' => $negLabels,
            'negCounts' => $negCounts,

            // Tablo ve map'ler
            'lastMeetings' => $lastMeetings,
            'durumMap' => $durumMap,
            'negMap'   => $negMap,
        ]);
    }
}
