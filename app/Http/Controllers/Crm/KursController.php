<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Kurs;
use App\Models\Setting;
use App\Http\Requests\KursRequest;
use App\Services\ImageService;
use App\Services\ImageUploadService;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class KursController extends Controller
{
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $kategoriler = Kategori::where('tur', 'kurs')->get();

        return view(theme_view('admin', 'pages.kurs.index'), compact('kategoriler'));
    }


    public function getData(Request $request)
    {
        $kurslar = Kurs::with('kategori')
            ->select('kurslar.id', 'kurslar.kategori_id', 'kurslar.kurs_adi', 'kurslar.permalink', 'kurslar.sira', 'kurslar.durum')
            ->orderBy('sira');

        // Başlığa göre filtreleme
        if ($request->has('kurs_adi') && !empty($request->kurs_adi)) {
            $kurslar->where('kurs_adi', 'like', '%'.$request->kurs_adi.'%');
        }

        // Kategoriye göre filtreleme
        if ($request->has('kategori_id') && !empty($request->kategori_id)) {
            $kurslar->where('kategori_id', $request->kategori_id);
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && !empty($request->durum)) {
            $kurslar->where('durum', $request->durum);
        }

        if ($request->has('durum') && $request->durum !== null) {
            $kurslar->where('durum', $request->durum);
        }

        return DataTables::of($kurslar)
            ->addColumn('id', function ($data) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn(
                'kurs_adi', function ($data) {
                return '<p class="text-wrap">'.$data->kurs_adi.'</p>';
            })
            ->addColumn('kategori', function ($data) {
                return $data->kategori ? $data->kategori->isim : 'Kategori Yok';
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum).'">'.status()->get($data->durum, 'text').'</span>';
                //return '<span class="badge bg-'.status()->get($blog->durum).'-subtle text-'.status()->get($blog->durum).' fw-semibold fs-2 gap-1 d-inline-flex align-items-center">'.status()->get($blog->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                     <div class="position-relative d-inline-block">
                          <button type="button"
                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                            data-kt-menu-trigger="click"
                            data-kt-menu-attach="parent"
                            data-kt-menu-placement="bottom-start"
                            data-kt-menu-offset="0,6">
                            Eğitim İçeriği <i class="ki-outline ki-down fs-5 ms-1"></i>
                          </button>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-2" data-kt-menu="true" style="">
                            <div class="menu-item px-3">
                                <a href="'.route(config('system.admin_prefix').'.bolum.index', ['kurs_id' => $row->id]).'" target="_blank" class="menu-link px-3">
                                    Bölümler
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="'.route(config('system.admin_prefix').'.ders.index', ['kurs_id' => $row->id]).'" target="_blank" class="menu-link px-3">
                                    Dersler
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="'.route(config('system.admin_prefix').'.kurs.index', ['id' => $row->permalink]).'" target="_blank" class="btn btn-sm btn-icon btn-light-primary me-1" data-bs-toggle="tooltip" title="Görüntüle">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>

                    <a href="'.route(config('system.admin_prefix').'.kurs.edit', ['id' => $row->id]).'" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <form action="'.route(config('system.admin_prefix').'.kurs.delete', ['id' => $row->id]).'" method="POST" style="display:inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'kurs_adi', 'kategori', 'durum', 'islem'])
            ->make(true);
    }

    public function add()
    {
        $kategoriler = Kategori::where('tur', 'kurs')->get();
        $kurs = new Kurs();
        $belgeTurleri = Setting::where('type', 'belge_turleri')->get();
        $sertifikaTurleri = Setting::where('type', 'sertifika_turu')->get();
        return view(theme_view('admin', 'pages.kurs.form'), compact('kategoriler', 'kurs', 'belgeTurleri', 'sertifikaTurleri'));
    }

    public function edit($id)
    {
        $kurs = Kurs::findOrFail($id);
        $kategoriler = Kategori::where('tur', 'kurs')->get();
        $belgeTurleri = Setting::where('type', 'belge_turleri')->get();
        $sertifikaTurleri = Setting::where('type', 'sertifika_turu')->get();
        return view(theme_view('admin', 'pages.kurs.form'), compact('kategoriler', 'kurs', 'belgeTurleri', 'sertifikaTurleri'));
    }

    protected function normalizeMulti(?array $values): array {
        $values = array_map(fn($v)=>trim((string)$v),(array)($values??[]));
        $values = array_values(array_filter($values, fn($v)=>$v!==''));
        return array_values(array_unique($values));
    }
    protected function mapRepeater(array $rows, string $key): array {
        $out=[]; foreach($rows as $r){ $t=is_array($r)?($r[$key]??''):$r; $t=trim((string)$t); if($t!=='') $out[]=$t; }
        return $out;
    }

    public function store(KursRequest $request, ImageService $imageService)
    {
        $kurs = new Kurs();

        $kurs->kategori_id        = (int) $request->kategori_id;
        $kurs->kurs_adi           = $request->kurs_adi;
        $kurs->permalink          = Str::slug($request->kurs_adi);
        $kurs->ozet               = $request->ozet;
        $kurs->aciklama           = $request->aciklama;
        $kurs->gecme_notu         = $request->gecme_notu ?? 49;
        $kurs->kurs_puani         = $request->kurs_puani;
        $kurs->label              = $request->label;
        $kurs->fiyat              = $request->fiyat;
        $kurs->kdv_orani          = $request->kdv_orani ?? 20;
        $kurs->ucretsiz           = $request->ucretsiz ?? 'H';
        $kurs->egitim_suresi      = $request->egitim_suresi;
        $kurs->egitim_sureci      = $request->egitim_sureci;
        $kurs->sertifika_turu     = $request->sertifika_turu;
        $kurs->kitap_destegi      = $request->kitap_destegi;
        $kurs->sinav_basari_orani = $request->sinav_basari_orani;
        $kurs->ders_sayisi        = $request->ders_sayisi;
        $kurs->egitim_seviyesi    = $request->egitim_seviyesi;
        $kurs->gereksinimler      = $request->gereksinimler;
        $kurs->seo_title          = $request->seo_title;
        $kurs->seo_description    = $request->seo_description;
        $kurs->sira               = $request->sira ?? 0;
        $kurs->tur                = $request->tur ?? 0;
        $kurs->durum              = (int) ($request->durum ?? 1);

        // JSON alanlar
        $belgeler = $this->normalizeMulti($request->input('belgeler'));
        $ogren    = $this->mapRepeater((array)$request->input('neler_ogrenecegim', []), 'metin');

        $kurs->belgeler          = $belgeler ?: null;
        $kurs->neler_ogrenecegim = $ogren     ?: null;

        $kurs->ozellikler = collect($request->input('ozellikler', []))
            ->map(fn($i) => trim((string)($i['ozellik'] ?? '')))
            ->filter()
            ->values()
            ->all() ?: null;

        if ($request->hasFile('resim')) {
            $kurs->resim = $imageService->update($request->file('resim'), $kurs->resim, 'kurs');
        }

        if ($request->hasFile('sertifika_ornegi')) {
            $kurs->sertifika_ornegi = $imageService->update($request->file('sertifika_ornegi'), $kurs->sertifika_ornegi, 'kurs');
        }

        $kurs->save();

        return redirect()
            ->route(config('system.admin_prefix').'.kurs.index', $kurs->id)
            ->with('success', 'Yeni kurs başarıyla oluşturuldu.');
    }

    public function update(KursRequest $request, $id, ImageService $imageService)
    {
        $kurs = Kurs::findOrFail($id);

        // Basit alanlar
        $kurs->kategori_id        = (int) $request->kategori_id;
        $kurs->kurs_adi           = $request->kurs_adi;
        $kurs->permalink          = Str::slug($request->kurs_adi);
        $kurs->ozet               = $request->ozet;
        $kurs->aciklama           = $request->aciklama;
        $kurs->gecme_notu         = $request->gecme_notu ?? $kurs->gecme_notu;
        $kurs->kurs_puani         = $request->kurs_puani;
        $kurs->label              = $request->label;
        $kurs->fiyat              = $request->fiyat;
        $kurs->kdv_orani          = $request->kdv_orani ?? $kurs->kdv_orani;
        $kurs->ucretsiz           = $request->ucretsiz ?? $kurs->ucretsiz;
        $kurs->egitim_suresi      = $request->egitim_suresi;
        $kurs->egitim_sureci      = $request->egitim_sureci;
        $kurs->sertifika_turu     = $request->sertifika_turu;
        $kurs->kitap_destegi      = $request->kitap_destegi;
        $kurs->sinav_basari_orani = $request->sinav_basari_orani;
        $kurs->ders_sayisi        = $request->ders_sayisi;
        $kurs->egitim_seviyesi    = $request->egitim_seviyesi;
        $kurs->gereksinimler      = $request->gereksinimler;
        $kurs->seo_title          = $request->seo_title;
        $kurs->seo_description    = $request->seo_description;
        $kurs->sira               = $request->sira ?? $kurs->sira;
        $kurs->tur                = $request->tur ?? $kurs->tur;
        $kurs->durum              = (int) ($request->durum ?? $kurs->durum);

        // JSON alanlar
        $belgeler = $this->normalizeMulti($request->input('belgeler'));

        $ogren    = $this->mapRepeater((array)$request->input('neler_ogrenecegim', []), 'metin');

        $kurs->belgeler          = $belgeler ?: null;

        $kurs->neler_ogrenecegim = $ogren     ?: null;

        $kurs->ozellikler = collect($request->input('ozellikler', []))
            ->map(fn($i) => trim((string)($i['ozellik'] ?? '')))
            ->filter()
            ->values()
            ->all() ?: null;

        // Silme
        if ($request->delete_resim == "1" && !$request->hasFile('resim')) {
            if ($kurs->resim) { $imageService->delete($kurs->resim, 'kurs'); }
            $kurs->resim = null;
        }

        // Yeni yükleme
        if ($request->hasFile('resim')) {
            $kurs->resim = $imageService->update($request->file('resim'), $kurs->resim, 'kurs');
        }

        if ($request->hasFile('sertifika_ornegi')) {
            $kurs->sertifika_ornegi = $imageService->update($request->file('sertifika_ornegi'), $kurs->sertifika_ornegi, 'kurs');
        }

        $kurs->save();

        return redirect()->route(config('system.admin_prefix').'.kurs.index')
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kurs başarıyla güncellendi.',
            ]);
    }

    public function delete($id)
    {
        $kurs = Kurs::findOrFail($id);

        // Eğer kursa kayıtlı öğrenci varsa silmeye izin verme
        if (\DB::table('ogrenci_kurslari')->where('kurs_id', $kurs->id)->exists()) {
            return back()->with('alert', [
                'library' => 'sweetalert',
                'type' => 'error',
                'message' => 'Bu Kursa kayıtlı öğrenciler olduğu için silinemez.',
            ]);
        }

        // Soft delete
        $kurs->delete();

        return redirect()->route(config('system.admin_prefix').'.kurs.index')
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kurs başarıyla silindi.',
            ]);
    }

}
