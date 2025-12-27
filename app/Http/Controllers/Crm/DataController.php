<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Kurs;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls as ReaderXls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xml;

class DataController extends Controller
{
    public function index() {

        $now = Carbon::now()->startOfMonth();
        $months = collect(range(1, 6))->map(fn($i) => $now->copy()->subMonths($i - 1)->format('Y-m')); // Son 6 ay (sonuncusu en güncel)

        $getMonthlyCounts = function ($durumlar = null) use ($months) {
            $query = Data::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
                ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months);

            if (!is_null($durumlar)) {
                $query->whereIn('durum', (array) $durumlar);
            }

            return $query->groupBy('month')->pluck('total', 'month')->toArray();
        };

        $buildData = function ($label, $durumlar, $color, $key) use ($months, $getMonthlyCounts) {
            $monthlyData = $getMonthlyCounts($durumlar);
            $monthsList = $months->reverse()->values(); // Geçmişten bugüne

            $chartLabels = $monthsList->slice(0, 5); // Son ay hariç 5 ay
            $chartData = $chartLabels->map(fn($m) => $monthlyData[$m] ?? 0);

            $latestMonth = $months->first(); // En güncel ay (örneğin 2025-05)
            $lastValue = $monthlyData[$latestMonth] ?? 0;

            return [
                'title' => $label,
                'subtitle' => 'Son 6 Aylık İstatistik',
                'color' => $color,
                'value' => $lastValue,
                'key' => $key,
                'labels' => $chartLabels->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('F'))->toArray(),
                'data' => $chartData->toArray(),
            ];
        };

        // kurs istatistikleri
        $topCourses = Data::selectRaw('kurs_id, COUNT(*) as total')
            ->where('personel_id', 0)
            ->whereIn('durum', [1, 2])
            ->whereNotNull('kurs_id')
            ->groupBy('kurs_id')
            ->orderByDesc('total')
            ->take(20)
            ->get()
            ->pluck('total', 'kurs_id');

        $kurslar = Kurs::whereIn('id', $topCourses->keys())
            ->pluck('kurs_adi', 'id');

        $kursStats = [];
        foreach ($topCourses as $kursId => $adet) {
            $kursStats[] = [
                'adi' => $kurslar[$kursId] ?? 'Bilinmeyen Kurs',
                'adet' => $adet
            ];
        }

        $durumlar = Setting::where('type', 'data_durum')->get();
        $olumsuzlar = Setting::where('type', 'data_olumsuz')->get();

        return view(theme_view('admin', 'pages.data.index'))->with([
            'kurslar' => Kurs::orderBy('kurs_adi')->get(['id', 'kurs_adi']),
            'personeller' => User::role('personel')->orderBy('isim')->get(['id', 'isim', 'soyisim', 'durum']),
            'kursStats' => $kursStats,
            'olumsuzlar' => $olumsuzlar,
            'durumlar' => $durumlar,
            'stats' => [
                $buildData('Toplam Data', null, 'primary', 'toplam'),
                $buildData('Yeni Data', [1, 2], 'info', 'yeni'),
                $buildData('Olumsuz Data', [3], 'danger', 'olumsuz'),
                $buildData('Kayıtlar', [5], 'success', 'kayit'),
            ]
        ]);
    }


    public function datatable(Request $request)
    {

        $result = Data::with([
            'kurs:id,kurs_adi',
            'olumsuzNedeni',
            'dataDurum',
            'personel:id,isim,soyisim'
        ])
            ->select('id', 'personel_id', 'data.kurs_id', 'isim', 'sehir', 'eposta', 'telefon', 'basvuru_tarihi', 'atama_tarihi', 'olumsuz_id', 'cevapsiz', 'durum')
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
        if ($request->has('kurs') && !empty($request->kurs)) {
            $result->where('kurs_id', $request->kurs);
        }

        // Personele göre filtreleme
        if ($request->has('personel') && !empty($request->personel)) {
            $result->where('personel_id', $request->personel);
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
            // Durum filtresi boşsa: personel_id = 0 ve durum 1-2 olsun
            $result->where('personel_id', 0)
                ->whereIn('durum', [1, 2]);
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
                'kurs', function ($data) {
                if ($data->kurs) {
                    return '<p class="text-gray-900">' . $data->kurs->kurs_adi . '</p>';
                } else {
                    return '<p class="text-gray-900">Kurs Yok</p>'; // veya istediğiniz bir mesaj
                }
            })
            ->addColumn('personel', function ($data) {
                if ($data->personel_id == 0 || !$data->personel) {
                    return '---';
                }
                return $data->personel->isim . ' ' . $data->personel->soyisim;
            })

            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class', 'data').'">'.$data->dataDurum?->value.'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <form action="' . route(config('system.admin_prefix').'.data.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'isim', 'telefon', 'kurs', 'personel', 'durum', 'islem'])
            ->make(true);
    }

    public function destroy($id)
    {
        $data = Data::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Kayıt başarıyla silindi.',
        ]);
    }

    public function dataekle(Request $request)
    {
        $validated = $request->validate([
            'kurs_id' => 'required|exists:kurslar,id',
            'isim' => 'required|string|max:255',
            'eposta' => 'required|email',
            'telefon' => 'required|string|max:20',
            'sehir' => 'nullable|string|max:100',
            'basvuru_tarihi' => 'required|date',
        ]);

        Data::create($validated);

        return response()->json(['message' => 'Kayıt eklendi']);
    }

    public function personelAta(Request $request)
    {
        $request->validate([
            'durum' => 'required|integer',
            'kurs_id' => 'required|exists:kurslar,id',
            'adet' => 'required|integer|min:1',
            'personel_id' => 'required|exists:users,id',
            'personel_data' => 'required|integer|min:0',
        ]);

        $adet = $request->adet;

        $query = Data::where('durum', $request->durum)
            ->where('kurs_id', $request->kurs_id)
            ->where('personel_id', $request->personel_data) // artık filtre değişken
            ->orderBy('id');

        $count = $query->count();

        $updated = $query->limit($adet)->update([
            'personel_id' => $request->personel_id,
            'durum' => 1,
        ]);

        $fromUser = User::find($request->personel_data);
        $toUser = User::find($request->personel_id);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'total' => $count,
            'from' => $fromUser ? $fromUser->isim . ' ' . $fromUser->soyisim : 'Boş Data',
            'to' => $toUser ? $toUser->isim . ' ' . $toUser->soyisim : 'Bilinmiyor',
        ]);
    }

    public function ajaxKursList(Request $request)
    {
        $request->validate([
            'durum' => 'required|integer',
            'personel_id' => 'required|integer',
        ]);

        $durum = $request->durum;
        $personelId = $request->personel_id;

        $kurslar = Data::select('kurs_id', \DB::raw('count(*) as adet'))
            ->where('durum', $durum)
            ->where('personel_id', $personelId)
            ->groupBy('kurs_id')
            ->get();

        // Kurs isimlerini getirmek için join yapıyoruz
        $result = $kurslar->map(function ($item) {
            $kurs = Kurs::find($item->kurs_id);
            return [
                'id' => $item->kurs_id,
                'ad' => $kurs ? $kurs->kurs_adi . ' (' . $item->adet . ')' : null,
            ];
        })->filter(fn ($i) => $i['ad']); // kurs silinmiş olabilir

        return response()->json($result->values());
    }

    public function exportExcel(Request $request)
    {
        $fields = $request->input('fields', ['isim', 'eposta', 'durum']); // varsayılan

        $result = Data::query();

        if ($request->filled('baslik')) {
            $result->where('baslik', 'like', '%' . $request->baslik . '%');
        }

        if ($request->filled('kurs')) {
            $result->where('kurs_id', $request->kurs);
        }

        if ($request->filled('durum')) {
            if (str_starts_with($request->durum, 'olumsuz-')) {
                $olumsuzId = (int) str_replace('olumsuz-', '', $request->durum);
                $result->where('durum', 3)->where('olumsuz_id', $olumsuzId);
            } else {
                $result->where('durum', $request->durum);
            }
        }


        $data = $result->get(array_merge(['id'], $fields));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Başlıklar
        $headers = ['id' => 'ID'];
        foreach ($fields as $field) {
            $headers[$field] = ucfirst(str_replace('_', ' ', $field));
        }
        $sheet->fromArray(array_values($headers), NULL, 'A1');

        // Veriler
        $rows = [];
        foreach ($data as $item) {
            $row = [$item->id];
            foreach ($fields as $field) {
                $row[] = ($field === 'durum') ? status()->get($item->durum, 'text') : $item->$field;
            }
            $rows[] = $row;
        }

        $sheet->fromArray($rows, NULL, 'A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'veriler_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
 /* xlsx yükleme
    public function topludatayukle(Request $request)
    {
        $request->validate([
            'upload_excel' => 'required|file|mimes:xlsx,xls',
            'upload_kurs_id' => 'required|integer|exists:kurslar,id',
        ]);

        $reader = IOFactory::createReaderForFile($request->file('upload_excel')->getRealPath());
        $spreadsheet = $reader->load($request->file('upload_excel')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $baslikHaritalari = [];
        $veriler = [];

        // Başlıkları çöz
        $headerRow = array_shift($rows);
        foreach ($headerRow as $col => $value) {
            $key = strtolower(trim($value));
            $baslikHaritalari[$key] = $col;
        }

        foreach ($rows as $row) {
            $isim    = trim($row[$baslikHaritalari['adı_soyadı'] ?? ''] ?? '');
            $eposta  = trim($row[$baslikHaritalari['e-posta'] ?? ''] ?? '');
            $telefon = trim($row[$baslikHaritalari['telefon_numarası'] ?? ''] ?? '');
            $created = trim($row[$baslikHaritalari['created_time'] ?? ''] ?? '');

            if (!$isim || !$eposta || !$telefon) {
                $veriler[] = [
                    'isim' => $isim,
                    'eposta' => $eposta,
                    'telefon' => $telefon,
                    'durum' => 'error',
                    'mesaj' => 'Eksik veri',
                ];
                continue;
            }

            $basvuruTarihi = now();
            if (!empty($created)) {
                try {
                    $basvuruTarihi = Carbon::parse($created);
                } catch (\Exception $e) {
                    // geçersiz tarih olursa şimdi al
                    $basvuruTarihi = now();
                }
            }

            $exists = Data::where('kurs_id', $request->upload_kurs_id)
                ->where('telefon', $telefon)
                ->exists();

            if ($exists) {
                $veriler[] = [
                    'isim' => $isim,
                    'eposta' => $eposta,
                    'telefon' => $telefon,
                    'durum' => 'error',
                    'mesaj' => 'Zaten kayıtlı',
                ];
                continue;
            }

            Data::create([
                'kurs_id' => $request->upload_kurs_id,
                'isim' => $isim,
                'eposta' => $eposta,
                'telefon' => $telefon,
                'basvuru_tarihi' => $basvuruTarihi,
                'durum' => 1,
            ]);

            $veriler[] = [
                'isim' => $isim,
                'eposta' => $eposta,
                'telefon' => $telefon,
                'durum' => 'success',
                'mesaj' => 'Eklendi',
            ];
        }

        return view(theme_view('admin', 'pages.data.toplu_yukle_sonuc'), compact('veriler'));
    }
        */

    public function topludatayukle(Request $request)
    {
        $request->validate([
            'upload_excel' => 'required|file|mimes:xlsx,xls,xml',
            'upload_kurs_id' => 'required|integer|exists:kurslar,id',
        ]);

        $file = $request->file('upload_excel');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        // Dosya türüne göre uygun reader sınıfını seç
        if ($mime === 'text/xml' || $extension === 'xml') {
            $reader = new Xml();
        } elseif ($extension === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new ReaderXlsx();
        }

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $baslikHaritalari = [];
        $veriler = [];

        // Başlıkları çöz
        $headerRow = array_shift($rows);
        foreach ($headerRow as $col => $value) {
            $key = strtolower(trim($value));
            $baslikHaritalari[$key] = $col;
        }

        foreach ($rows as $row) {
            $isim    = trim($row[$baslikHaritalari['adı_soyadı'] ?? ''] ?? '');
            $eposta  = trim($row[$baslikHaritalari['e-posta'] ?? ''] ?? '');
            $telefon = trim($row[$baslikHaritalari['telefon_numarası'] ?? ''] ?? '');
            $created = trim($row[$baslikHaritalari['created_time'] ?? ''] ?? '');

            if (!$isim || !$eposta || !$telefon) {
                $veriler[] = [
                    'isim' => $isim,
                    'eposta' => $eposta,
                    'telefon' => $telefon,
                    'durum' => 'error',
                    'mesaj' => 'Eksik veri',
                ];
                continue;
            }

            $basvuruTarihi = now();
            if (!empty($created)) {
                try {
                    $basvuruTarihi = Carbon::parse($created);
                } catch (\Exception $e) {
                    $basvuruTarihi = now();
                }
            }

            $exists = Data::where('kurs_id', $request->upload_kurs_id)
                ->where('telefon', $telefon)
                ->exists();

            if ($exists) {
                $veriler[] = [
                    'isim' => $isim,
                    'eposta' => $eposta,
                    'telefon' => $telefon,
                    'durum' => 'error',
                    'mesaj' => 'Zaten kayıtlı',
                ];
                continue;
            }

            Data::create([
                'kurs_id' => $request->upload_kurs_id,
                'isim' => $isim,
                'eposta' => $eposta,
                'telefon' => $telefon,
                'basvuru_tarihi' => $basvuruTarihi,
                'durum' => 1,
            ]);

            $veriler[] = [
                'isim' => $isim,
                'eposta' => $eposta,
                'telefon' => $telefon,
                'durum' => 'success',
                'mesaj' => 'Eklendi',
            ];
        }

        return view(theme_view('admin', 'pages.data.toplu_yukle_sonuc'), compact('veriler'));
    }




}
