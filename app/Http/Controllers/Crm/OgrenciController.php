<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Adres_Il;
use App\Models\Belge;
use App\Models\DersLog;
use App\Models\Ogrenci;
use App\Models\OgrenciKursu;
use App\Models\OgrenciSinavi;
use App\Models\PersonelNot;
use App\Models\Sertifika;
use App\Models\Setting;
use App\Models\Siparis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageService;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;

class OgrenciController extends Controller
{
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index() {
        // Personeller
        $personeller = User::role('personel')->where('durum', 1)->get();

        return view(theme_view('admin', 'pages.ogrenci.index'), compact('personeller'));
    }

    public function getData(Request $request)
    {

        $result = Ogrenci::with(['personel:id,isim,soyisim'])
            ->join('users', 'ogrenciler.personel_id', '=', 'users.id')
            ->select('ogrenciler.id', 'ogrenciler.personel_id', 'ogrenciler.isim', 'ogrenciler.soyisim', 'ogrenciler.tc_kimlik_no', 'ogrenciler.email', 'ogrenciler.telefon', 'ogrenciler.cinsiyet', 'ogrenciler.profil_resmi', 'ogrenciler.created_at', 'ogrenciler.durum')
            ->orderBy('ogrenciler.id', 'desc');

        if($request->has('arama') && !empty($request->arama)) {
            $arama = $request->arama;

            if(preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                // Rakam, boşluk, - veya + içeriyorsa telefon
                $result->where('ogrenciler.telefon', 'like', '%' . $arama . '%');
            } elseif(filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                // Geçerli bir e-posta ise
                $result->where('ogrenciler.email', 'like', '%' . $arama . '%');
            } else {
                // İsim veya soyisim alanlarında arama
                $result->where(function($query) use ($arama) {
                    $query->where('ogrenciler.isim', 'like', '%' . $arama . '%')->orWhere('ogrenciler.soyisim', 'like', '%' . $arama . '%');
                });
            }
        }

        if($request->has('tc_no') & !empty($request->tc_no)) {
            $result->where('ogrenciler.tc_kimlik_no', 'like', '%' . $request->tc_no . '%');
        }

        // Personele göre filtreleme
        if ($request->has('personel_id') && !empty($request->personel_id) && is_numeric($request->personel_id)) {
            $result->where('ogrenciler.personel_id', $request->personel_id);
        }

        if ($request->has('ogrenci_id') && is_numeric($request->ogrenci_id)) {
            $result->where('ogrenciler.id', 'like', $request->ogrenci_id . '%');
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && !empty($request->durum)) {
            $result->where('ogrenciler.durum', $request->durum);
        }


        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $result->whereBetween('ogrenciler.created_at', [$baslangicTarihi, $bitisTarihi]);
        }

        /*
          { data: 'id', name: 'id' },
        { data: 'isim', name: 'isim' },
        { data: 'telefon', name: 'telefon' },
        { data: 'tarih', name: 'tarih' },
        { data: 'personel', name: 'personel' },
        { data: 'durum', name: 'durum', orderable: false, searchable: false },
        { data: 'islem', name: 'islem', orderable: false, searchable: false }
         * */


        return DataTables::of($result)
            ->addColumn('id', function ($data) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn(
                'isim', function ($data) {
                //return '<p class="text-wrap">'.$data->isim.' '.$data->soyisim.'</p>';
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol  symbol-40px symbol-circle ">
                            <img alt="'.$data->isim.' '.$data->soyisim.'" src="'.userAvatar($data->profil_resmi, 'ogrenci').'">
                        </div>
                        <!--begin::Details-->
                        <div class="ms-4">
                            <span class="fs-6 fw-bold text-gray-900 text-hover-primary mb-2">'.$data->isim.' '.$data->soyisim.'</span>
                            <div class="fw-semibold fs-7 text-muted">'.$data->email.'</div>
                        </div>
                        <!--end::Details-->
                    </div>';
            })
            ->addColumn(
                'telefon', function ($data) {
                return '<a href="https://web.whatsapp.com/send?phone='.sms_telefon($data->telefon).'" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success"><i class="fa-brands fa-whatsapp me-1"></i>'.$data->telefon.'</a>';
            })
            ->addColumn('tarih', function ($data) {
                return '<span class="badge badge-light-info">'.Carbon::parse($data->created_at)->format('d.m.Y').'</span>';
            })
            ->addColumn('personel', function ($data) {
                if ($data->personel_id == 0 || !$data->personel) {
                    return '---';
                }
                return $data->personel->isim . ' ' . $data->personel->soyisim;
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class').'">'.status()->get($data->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix') . '.ogrenci.profil', ['id' => $row->id]) . '" class="btn btn-sm btn-light-primary"><i class="far fa-address-card"></i> Profil</a>
                    <form action="' . route(config('system.admin_prefix').'.ogrenci.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'isim', 'telefon', 'tarih', 'personel', 'durum', 'islem'])
            ->make(true);
    }

    public function exportExcel(Request $request)
    {
        $fields = $request->input('fields', ['isim', 'soyisim', 'email', 'telefon']);

        $result = Ogrenci::with(['personel', 'il'])->select(array_merge(['id'], $fields));

        // Arama filtresi
        if ($request->filled('arama')) {
            $arama = $request->arama;
            if (preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                $result->where('telefon', 'like', '%' . $arama . '%');
            } elseif (filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                $result->where('email', 'like', '%' . $arama . '%');
            } else {
                $result->where(function ($q) use ($arama) {
                    $q->where('isim', 'like', '%' . $arama . '%')
                        ->orWhere('soyisim', 'like', '%' . $arama . '%');
                });
            }
        }

        if ($request->filled('tc_no')) {
            $result->where('tc_kimlik_no', 'like', '%' . $request->tc_no . '%');
        }

        if ($request->filled('personel_id') && is_numeric($request->personel_id)) {
            $result->where('personel_id', $request->personel_id);
        }

        if ($request->filled('ogrenci_id') && is_numeric($request->ogrenci_id)) {
            $result->where('id', $request->ogrenci_id);
        }

        if ($request->filled('durum')) {
            $result->where('durum', $request->durum);
        }

        $data = $result->get();

        // Başlık isimleri
        $fieldLabels = [
            'isim'         => 'İsim',
            'soyisim'      => 'Soyisim',
            'email'        => 'E-Posta',
            'telefon'      => 'Telefon',
            'tc_kimlik_no' => 'T.C. Kimlik No',
            'il_id'        => 'Şehir',
            'personel_id'  => 'Personel',
            'created_at'   => 'Kayıt Tarihi',
            'durum'        => 'Durum',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Başlık satırı
        $headers = ['ID'];
        foreach ($fields as $field) {
            $headers[] = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }
        $sheet->fromArray($headers, NULL, 'A1');

        // Satırlar
        $rows = [];
        foreach ($data as $item) {
            $row = [$item->id];
            foreach ($fields as $field) {
                switch ($field) {
                    case 'durum':
                        $value = status()->get($item->durum, 'text');
                        break;
                    case 'il_id':
                        $value = $item->il->isim ?? '-';
                        break;
                    case 'personel_id':
                        $value = $item->personel->isim ?? '-';
                        break;
                    case 'created_at':
                        $value = optional($item->created_at)->format('d.m.Y H:i');
                        break;
                    default:
                        $value = $item->$field;
                }
                $row[] = $value;
            }
            $rows[] = $row;
        }

        $sheet->fromArray($rows, NULL, 'A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'ogrenciler_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function profil($id) {
        /*
        $ogrenci = Ogrenci::with([
            'il',
            'ilce',
            'kayitKaynak'
        ])->findOrFail($id);
        $personel = User::findOrFail($ogrenci->personel_id);
        */


        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.profil'), $data);
    }

    public function kurslar($id) {

        $kurslar = OgrenciKursu::with('kurs', 'sinavTercihi')
            ->where('ogrenci_id', $id)
            ->orderByDesc('id')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        $sertifikaTuru = Setting::where('type', 'sertifika_belge_turu')->get();
        $sinavTercihi = Setting::where('type', 'sinav_tercihi')->get();

        return view(theme_view('admin', 'pages.ogrenci.kurslar'), array_merge($data, compact('kurslar', 'sertifikaTuru', 'sinavTercihi')));

    }

    public function kursGuncelle(Request $request, $id)
    {
        $data = $request->validate([
            'tarih_bitis' => 'nullable|date',
            'sinav_hakki' => 'nullable|integer',
            'sertifika_turu' => 'nullable|string|max:255',
            'sinav_tercihi' => 'nullable|string|max:255',
            'sozlesme' => 'required|in:0,1',
            'durum' => 'required|in:0,1',
        ]);

        OgrenciKursu::where('id', $id)->update($data);

        return back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Kurs bilgisi başarıyla güncellendi.'
        ]);
    }


    public function kursVerisi($id)
    {
        $veri = OgrenciKursu::findOrFail($id);

        return response()->json($veri);
    }


    public function delete($id)
    {
        $data = Ogrenci::findOrFail($id);
        //$data->delete();

        /*
         * Kurs, Satış Sınav vs.. var mı diye kontrol edilecek yoksa silinecek. VArsa eğer ne varsa onun uyarısı yapılacak
         * */

        //return redirect()->back()->with('success', 'Kayıt başarıyla silindi.');
        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Kayıt başarıyla silindi.',
        ]);
    }

    protected function loadOgrenciWithPersonel($id)
    {
        $ogrenci = Ogrenci::with(['il', 'ilce', 'kayitKaynak'])->findOrFail($id);
        $personel = User::findOrFail($ogrenci->personel_id);

        return compact('ogrenci', 'personel');
    }

    public function siparisler($id) {

        $siparisler = Siparis::with('urunler', 'odemeDurum', 'siparisDurum')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.siparisler'), array_merge($data, compact('siparisler')));
    }

    public function belgeler($id) {
        $belgeler = Belge::with('belgeTuru', 'belgeDurum')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $belgeTurleri = Setting::where('type', 'belge_turleri')->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.belgeler'), array_merge($data, compact('belgeler', 'belgeTurleri')));
    }

    public function belgeSil($id, Request $request)
    {
        $belge = Belge::findOrFail($id);

        // Fiziksel dosya varsa sil
        if ($belge->belge && file_exists(public_path('storage/' . $belge->belge))) {
            unlink(public_path('storage/' . $belge->belge));
        }

        $belge->delete();

        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Belge başarıyla silindi.'
        ]);
    }

    public function belgeDurumGuncelle($id, Request $request)
    {
        $belge = Belge::findOrFail($id);

        $yeniDurum = null;

        if ($request->route()->getName() === config('system.admin_prefix') . '.ogrenci.belgeler.onayla') {
            $yeniDurum = 2; // onaylandı
        } elseif ($request->route()->getName() === config('system.admin_prefix') . '.ogrenci.belgeler.iptal') {
            $yeniDurum = 3; // onaylanmadı
        }

        if ($yeniDurum) {
            $belge->durum = $yeniDurum;
            $belge->save();

            return back()->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Belge durumu güncellendi.'
            ]);
        }

        return back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'error',
            'message' => 'Geçersiz işlem.'
        ]);
    }

    public function belgeEkle(Request $request, ImageUploadService $imageUploadService)
    {
        $request->validate([
            'user_id' => 'required|exists:ogrenciler,id',
            'tur' => 'required|integer',
            'belge' => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx,doc,docx,zip,rar,csv',
            'aciklama' => 'nullable|string',
        ]);

        if ($request->hasFile('belge')) {
            $imagePaths = $imageUploadService->upload($request->file('belge'), 'belge');
            $path = $imagePaths['default'];
        }

        Belge::create([
            'user_id' => $request->user_id,
            'tur' => $request->tur,
            'belge' => $path,
            'aciklama' => $request->aciklama,
            'durum' => 2, // Direkt onaylandı olarak kaydedilecek
        ]);

        return back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Belge başarıyla yüklendi.'
        ]);
    }

    public function sinavlar($id) {
        $sinavlar = OgrenciSinavi::with('sinav.sinavTuru')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.sinavlar'), array_merge($data, compact('sinavlar')));
    }

    public function sinavEkle() {

    }

    public function sinavSil($id)
    {
        $ogrenciSinav = OgrenciSinavi::find($id);

        if (!$ogrenciSinav) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'library' => 'sweetalert',
                'message' => 'Sınav kaydı bulunamadı.'
            ]);
        }

        $ogrenciSinav->delete();

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'library' => 'sweetalert',
            'message' => 'Sınav kaydı başarıyla silindi.'
        ]);
    }

    public function sertifikalar(string $id) {
        $sertifikalar = Sertifika::with('sertifikaTuru', 'sertifikaBelgeTuru')
            ->where('ogrenci_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.sertifikalar'), array_merge($data, compact('sertifikalar')));
    }

    public function sertifikaEkle() {

    }

    public function sertifikaSil(string $id) {

    }

    public function Loglar(string $id) {
        $dersLoglari = DersLog::with('ders', 'kurs')
            ->where('ogrenci_id', $id)
            ->orderByDesc('id')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.loglar'), array_merge($data, compact('dersLoglari')));
    }

    public function logSil(string $id) {

    }

    public function notlar($id) {
        $notlar = PersonelNot::with('personel')
            ->where('item_id', $id)
            ->orderByDesc('id')
            ->get();

        $data = $this->loadOgrenciWithPersonel($id);

        return view(theme_view('admin', 'pages.ogrenci.notlar'), array_merge($data, compact('notlar')));
    }

    public function notSil(string $id) {
        $not = PersonelNot::findOrFail($id);

        $not->delete();

        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Not başarıyla silindi.'
        ]);
    }

    public function notEkle(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'icerik' => 'required|string',
        ]);

        PersonelNot::create([
            'item_id' => $request->item_id,
            'icerik' => $request->icerik,
            'personel_id' => auth()->id(),
            'type' => 'ogrenciler',
        ]);

        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Not başarıyla kaydedildi.'
        ]);
    }

    public function edit(string $id) {
        $profil = Ogrenci::with('personel')->findOrFail($id);

        $data = $this->loadOgrenciWithPersonel($id);

        $mezuniyet = Setting::where('type', 'mezuniyet')->get();
        $cinsiyet = Setting::where('type', 'cinsiyet')->get();
        $iller = Adres_Il::get();

        return view(theme_view('admin', 'pages.ogrenci.edit'), array_merge($data, compact('profil', 'mezuniyet', 'cinsiyet')));
    }

    public function update(Request $request, $id, imageUploadService $imageUploadService)
    {
        $ogrenci = Ogrenci::findOrFail($id);

        $request->validate([
            'isim' => 'required|string|max:255',
            'soyisim' => 'required|string|max:255',
            'email' => 'required|email|unique:ogrenciler,email,' . $id,
            'telefon' => 'nullable|string|max:20',
            'tc_kimlik_no' => 'nullable|string|max:20',
            'cinsiyet' => 'nullable|in:erkek,kadin',
            'dogum_tarihi' => 'nullable|date',
            'mezuniyet' => 'nullable|string|max:255',
            'meslek' => 'nullable|string|max:255',
            'adres' => 'nullable|string',
            'il_id' => 'nullable|integer|exists:adres_il,id',
            'ilce_id' => 'nullable|integer|exists:adres_ilce,id',
            'profil_resmi' => 'nullable|image',
        ]);

        // Alanları güncelle
        $ogrenci->isim = $request->isim;
        $ogrenci->soyisim = $request->soyisim;
        $ogrenci->email = $request->email;
        $ogrenci->telefon = $request->telefon;
        $ogrenci->tc_kimlik_no = $request->tc_kimlik_no;
        $ogrenci->cinsiyet = $request->cinsiyet;
        $ogrenci->dogum_tarihi = $request->dogum_tarihi;
        $ogrenci->mezuniyet = $request->mezuniyet;
        $ogrenci->meslek = $request->meslek;
        $ogrenci->adres = $request->adres;
        $ogrenci->il_id = $request->il_id;
        $ogrenci->ilce_id = $request->ilce_id;
        $ogrenci->durum = $request->durum;

        // Profil resmi silinsin mi kontrolü
        if ($request->filled('remove_avatar') && $request->remove_avatar == 1) {
            // Dosya varsa sil
            if ($ogrenci->profil_resmi && Storage::disk('public')->exists('upload/user/' . $ogrenci->profil_resmi)) {
                Storage::disk('public')->delete('upload/user/' . $ogrenci->profil_resmi);
            }

            $ogrenci->profil_resmi = null;
        }

        if ($request->hasFile('profil_resmi')) {
            $imagePaths = $imageUploadService->upload($request->file('profil_resmi'), 'ogrenci');
            $ogrenci->profil_resmi = $imagePaths['image'];
        }

        /*
        // Profil resmi güncelleme (varsa)
        if ($request->hasFile('profil_resmi')) {
            // Eski dosya silinir
            if ($ogrenci->profil_resmi && Storage::disk('public')->exists('upload/user/' . $ogrenci->profil_resmi)) {
                Storage::disk('public')->delete('upload/user/' . $ogrenci->profil_resmi);
            }

            // Yeni dosya kaydedilir
            $filename = Str::uuid() . '.' . $request->profil_resmi->getClientOriginalExtension();
            $request->profil_resmi->storeAs('upload/user', $filename, 'public');
            $ogrenci->profil_resmi = $filename;
        }
        */

        $ogrenci->save();

        return redirect()->back()->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Profil başarıyla güncellendi.',
        ]);
    }



}
