<?php

namespace App\Http\Controllers\Personel;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataGorusme;
use App\Models\DataOlumsuz;
use App\Models\DataRandevu;
use App\Models\Satis;
use App\Models\Setting;
use App\Models\SigortaSirketi;
use App\Models\Teklif;
use App\Models\Urun;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DataGorusmelerController extends Controller {
    public function __construct(ImageUploadService $imageService) {
        $this->imageService = $imageService;
    }

    public function index() {
        $durumlar = Setting::where('type', 'data_durum')->get();
        $olumsuzlar = Setting::where('type', 'data_olumsuz')->get();
        $urunler = Urun::orderBy('urun_adi')->get([
            'id',
            'urun_adi'
        ]);

        return view(theme_view('admin', 'personel.gorusmeler'))->with([
            'urunler'    => $urunler,
            'olumsuzlar' => $olumsuzlar,
            'durumlar'   => $durumlar,
        ]);
    }

    public function datatable(Request $request) {
        // İlişkiler:
        // data: id, isim, telefon, eposta, urun_id, sehir, olumsuz_id, durum, basvuru_tarihi, atama_tarihi
        // data.urun: urun_adi
        // data.dataDurum: value
        $result = DataGorusme::with([
            'data:id,urun_id,isim,sehir,eposta,telefon,basvuru_tarihi,atama_tarihi,olumsuz_id,durum',
            'data.urun:id,urun_adi',
            'data.dataDurum',
        ])->select('id', 'personel_id', 'data_id', 'urun_id', 'olumsuz_id', 'randevu_id', 'kayit', 'personel_notu', 'created_at')->where('personel_id', Auth::id())->orderByDesc('id');

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

        // Durum filtre (data.durum / olumsuz_id)
        if($request->filled('durum')) {
            if(str_starts_with($request->durum, 'olumsuz-')) {
                $olumsuzId = (int)str_replace('olumsuz-', '', $request->durum);
                $result->whereHas('data', function($q) use ($olumsuzId) {
                    $q->where('durum', 3)->where('olumsuz_id', $olumsuzId);
                });
            } else {
                $durum = (int)$request->durum;
                $result->whereHas('data', fn($q) => $q->where('durum', $durum));
            }
        }

        // Tarih aralığı (görüşme tarihi) -> data_gorusmeler.created_at
        if($request->filled('tarih_filtre')) {
            // Beklenen format: "YYYY-MM-DD - YYYY-MM-DD"
            [
                $start,
                $end
            ] = array_pad(explode(' - ', $request->tarih_filtre), 2, null);
            if($start && $end) {
                $baslangicTarihi = Carbon::parse($start)->startOfDay();
                $bitisTarihi = Carbon::parse($end)->endOfDay();
                $result->whereBetween('created_at', [
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
        })->addColumn('durum', function($row) {

            $durum = $row->data->durum ?? null;
            $etiket = $row->data->dataDurum->value ?? '—';
            $personelNotu = trim($row->personel_notu ?? '');

            $buton = '';
            if($personelNotu !== '') {
                $buton = '<a href="#"
                            class="btn btn-icon btn-sm btn-light-primary personel-notu"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-html="true"
                            data-bs-container="body"
                            title="Personel Notu"
                            data-bs-content="'.e(nl2br($personelNotu)).'">
                            <i class="fa-solid fa-clipboard"></i>
                        </a>';
            }

            return $buton.' <span class="btn btn-sm btn-light-'.status()->get($durum, 'class', 'data').'">'.e($etiket).'</span>';
        })->addColumn('islem', function($row) {
            return Carbon::parse($row->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY');
        })->rawColumns([
            'id',
            'isim',
            'telefon',
            'urun',
            'durum',
            'islem'
        ])->make(true);
    }

    public function gorusmeyap($data_id) {
        $data_bilgileri = Data::with([
            'urun:id,urun_adi',
            'dataKaynak'
        ])->findOrFail($data_id);
        $durumlar = Setting::where('type', 'data_durum')->get();
        $olumsuzlar = Setting::where('type', 'data_olumsuz')->get();
        $urunler = Urun::orderBy('urun_adi')->get([
            'id',
            'urun_adi'
        ]);
        $olumsuzNedenler = DataOlumsuz::orderBy('isim')->get([
            'id',
            'isim'
        ]);
        $sigortaSirketleri = SigortaSirketi::where('durum', 1)->orderBy('ad')->get([
            'id',
            'ad'
        ]);

        $gecmis = DataGorusme::with([
            'personel',
            'randevu:id,randevu_tarihi',
            'olumsuzNedeni',
        ])->where('data_id', $data_id)->latest('id')->paginate(100);

        return view(theme_view('admin', 'personel.gorusmeyap'), compact('data_bilgileri', 'durumlar', 'olumsuzlar', 'urunler', 'olumsuzNedenler', 'sigortaSirketleri', 'gecmis'));
    }

    public function store(Request $req, $data_id, ImageUploadService $imageUploadService) {
        $data = Data::findOrFail($data_id);

        // 1) CEVAPSIZ: sadece sayaç artır ve listeye dön
        if($req->boolean('cevapsiz')) {
            $data->increment('cevapsiz');

            // (İstersen görüşme kaydı atma. Şu an atmıyoruz.)
            return redirect()->route(config('system.personel_prefix').'.data.index')->with('success', 'Cevapsız olarak işaretlendi.');
        }

        // 5|6|3 bekleniyor
        $req->validate([
            'sonuc'          => [
                'required',
                'in:1,2,3,4,5,6'
            ],
            'personel_notu'  => [
                'nullable',
                'string',
                'max:65535'
            ],
            'randevu_tarihi' => [
                'nullable',
                'date'
            ],
            'olumsuz_id'     => [
                'nullable',
                'integer'
            ],
        ]);

        // 2) İşlemleri tamamla
        DB::transaction(function() use ($imageUploadService, $req, $data) {

            $sonuc = (int)$req->sonuc; // 5 Kayıt, 6 Randevu, 3 Olumsuz
            $randevuId = null;
            $olumsuzId = null;
            $kayitFlag = null;

            if($sonuc === 6) { // Randevu
                $req->validate([
                    'randevu_tarihi' => [
                        'required',
                        'date'
                    ]
                ]);

                $rv = DataRandevu::create([
                    'personel_id'    => Auth::id(),
                    'data_id'        => $data->id,
                    'urun_id'        => $req->input('urun_id') ?: $data->urun_id,
                    'randevu_tarihi' => $req->randevu_tarihi,
                    'durum'          => 0,
                ]);
                $randevuId = $rv->id;

                $data->durum = 6;    // RANDEVU
                $data->olumsuz_id = 0;
                $data->save();
            } elseif($sonuc === 3) { // Olumsuz
                $req->validate([
                    'olumsuz_id' => [
                        'required',
                        'integer'
                    ]
                ]);
                $olumsuzId = (int)$req->olumsuz_id;

                $data->durum = 3;    // OLUMSUZ
                $data->olumsuz_id = $olumsuzId;
                $data->save();
            } elseif($sonuc === 5) { // SATIŞ
                // müşteri oluşturma/parçası sende mevcut (password vb.) — aynen bırak
                // (musteri_id'yi satislar’da şimdilik null bırakıyoruz)
                $email = $data->eposta ?: ('lead-'.$data->id.'@placeholder.local');
                $musteri = User::where('email', $email)->first();
                if(!$musteri) {
                    (new User())->forceFill([
                        'email'      => $email,
                        'name'       => $data->isim,
                        'isim'       => $data->isim,
                        'soyisim'    => '',
                        'telefon'    => $data->telefon,
                        'password'   => Hash::make(Str::random(16)),
                        'user_type'  => 'musteri',
                        'durum'      => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->save();
                    $musteri = User::where('email', $email)->first();
                }

                // Satış özel alanları
                $req->validate([
                    'satis_urun_id'    => [
                        'required',
                        'integer'
                    ],
                    'satis_tutari'     => [
                        'required',
                        'numeric',
                        'min:0'
                    ],
                    'sirket_id'        => [
                        'required',
                        'integer',
                        'exists:sigorta_sirketleri,id'
                    ],
                    'police_dosya'     => [
                        'required',
                        'file',
                        'mimes:pdf',
                        'max:20480'
                    ],
                    'police_baslangic' => [
                        'required',
                        'date'
                    ],
                    'police_bitis'     => [
                        'required',
                        'date',
                        'after_or_equal:police_baslangic'
                    ],
                ]);

                // PDF'i kaydet
                if($req->hasFile('police_dosya')) {
                    $paths = $imageUploadService->upload($req->file('police_dosya'), 'police');
                    $policePath = $paths['image'];
                } else {
                    $policePath = null;
                }

                Satis::create([
                    'personel_id'      => Auth::id(),
                    'musteri_id'       => $musteri?->id,
                    'data_id'          => $data->id,
                    'urun_id'          => $req->input('satis_urun_id'),
                    'kaynak'           => 1,
                    // Settings tablosunda 1=data
                    'tutar'            => $req->satis_tutari,
                    'tarih'            => now(),
                    'sirket_id'        => $req->sirket_id,
                    'police_baslangic' => $req->police_baslangic,
                    'police_bitis'     => $req->police_bitis,
                    'police_dosya'     => $policePath,
                ]);

                // Data ürününü seçilen ürüne güncelle (A ürünü arandı ama B satıldı vb.)
                $data->urun_id = $req->input('satis_urun_id');
                $data->durum = 5;
                $data->olumsuz_id = 0;
                $data->save();

                $kayitFlag = 1;
            } elseif($sonuc === 2) {
                $req->validate([
                    'teklif_urun_id' => [
                        'required',
                        'integer'
                    ],
                    'teklif_tutari'  => [
                        'required',
                        'numeric',
                        'min:0'
                    ],
                    'son_tarih'      => [
                        'required',
                        'date'
                    ],
                    'dosya'          => [
                        'required',
                        'file',
                        'mimes:pdf',
                        'max:20480'
                    ],
                ]);

                // PDF'i kaydet
                if($req->hasFile('dosya')) {
                    $imagePaths = $imageUploadService->upload($req->file('dosya'), 'teklif');
                    $path = $imagePaths['image'];
                } else {
                    $path = null;
                }

                Teklif::create([
                    'personel_id'   => Auth::id(),
                    'musteri_id'    => null,
                    // bu ekranda müşteri yazmıyoruz
                    'urun_id'       => $req->input('teklif_urun_id'),
                    'data_id'       => $data->id,
                    'teklif_tutari' => $req->teklif_tutari,
                    'son_tarih'     => $req->son_tarih,
                    'dosya'         => $path,
                ]);

                $data->urun_id = $req->input('teklif_urun_id');
                $data->durum = 2;
                $data->olumsuz_id = 0;
                $data->save();
            }


            // 1) Aday ürünü belirle
            $urunIdCandidate =
                $req->input('satis_urun_id')    // satışta seçilen
                ?? $req->input('teklif_urun_id')// teklifte seçilen
                ?? $req->input('urun_id')       // (varsa) formdan gelen genel
                ?? $data->urun_id;              // lead üzerindeki

            // 2) 0 veya geçersiz ise null yap
            if (empty($urunIdCandidate) || (int)$urunIdCandidate === 0 || !Urun::whereKey($urunIdCandidate)->exists()) {
                $urunIdCandidate = null;
            }

            // 3) Görüşme kaydı
            DataGorusme::create([
                'personel_id'   => Auth::id(),
                'data_id'       => $data->id,
                'urun_id'       => $urunIdCandidate,   // ← artık güvenli
                'olumsuz_id'    => $olumsuzId,
                'randevu_id'    => $randevuId,
                'kayit'         => $kayitFlag,
                'personel_notu' => $req->input('personel_notu'),
            ]);
        });

        // 3) Transaction bitti -> güvenle yönlendir -> alert verelim.
        return redirect()->route(config('system.personel_prefix').'.data.index')->with('alert', [
            'library' => 'sweetalert',
            'type'    => 'success',
            'message' => 'Görüşme başarıyla kaydedildi.',
        ]);
    }


    public function cevapsiz($data_id) {
        $data = Data::findOrFail($data_id);
        $data->increment('cevapsiz');

        return redirect()->route(config('system.personel_prefix').'.data.index')->with('success', 'Cevapsız olarak işaretlendi.');
    }
}
