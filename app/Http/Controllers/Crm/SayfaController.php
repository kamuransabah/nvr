<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\SayfaRequest;
use App\Models\Sayfa;
use App\Models\Kategori;
use App\Services\ImageService;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SayfaController extends Controller
{
    public function index() {
        $kategoriler = Kategori::where('tur', 'sayfa')->get();
        return view(theme_view('admin', 'pages.sayfa.index'), compact('kategoriler'));
    }

    public function getData(Request $request)
    {


        // Başlığa göre filtreleme
        if ($request->has('baslik') && !empty($request->baslik)) {
            $sayfalar->where('baslik', 'like', '%' . $request->baslik . '%');
        }

        // Kategoriye göre filtreleme
        if ($request->has('kategori_id') && !empty($request->kategori_id)) {
            $sayfalar->where('kategori_id', $request->kategori_id);
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && $request->durum !== null) {
            $sayfalar->where('durum', $request->durum);
        }

        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $sayfalar->whereBetween('created_at', [$baslangicTarihi, $bitisTarihi]);
        }

        $sayfalar = Sayfa::with('kategori')->select('sayfa.id', 'sayfa.kategori_id', 'sayfa.baslik', 'sayfa.permalink', 'sayfa.created_at', 'sayfa.durum')
            ->orderBy('id', 'desc');

        return DataTables::of($sayfalar)
            ->addColumn('id', function ($sayfa) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$sayfa->id.'</button>';
            })
            ->addColumn(
                'baslik', function ($sayfa) {
                return '<p class="text-wrap">'.$sayfa->baslik.'</p>';
            })
            ->addColumn('kategori', function ($sayfa) {
                return $sayfa->kategori ? $sayfa->kategori->isim : 'Kategori Yok';
            })
            ->addColumn('tarih', function ($sayfa) {
                //return Carbon::parse($sayfa->tarih)->diffForHumans();
                return Carbon::parse($sayfa->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY');
            })
            ->addColumn('durum', function ($sayfa) {
                return '<span class="badge badge-light-'.status()->get($sayfa->durum).'">'.status()->get($sayfa->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix').'.sayfa.index', ['id' => $row->permalink]) . '" target="_blank" class="btn btn-sm btn-icon btn-light-primary me-1" data-bs-toggle="tooltip" title="Görüntüle">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>

                    <a href="' . route(config('system.admin_prefix').'.sayfa.edit', ['id' => $row->id]) . '" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <form action="' . route(config('system.admin_prefix').'.sayfa.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'baslik', 'durum', 'islem'])
            ->make(true);
    }

    public function add()
    {
        $kategoriler = Kategori::where('tur', 'sayfa')->get();
        return view(theme_view('admin', 'pages.sayfa.form'), compact('kategoriler'));
    }

    public function edit($id)
    {
        $sayfa = Sayfa::findOrFail($id);
        $kategoriler = Kategori::where('tur', 'sayfa')->get();
        return view(theme_view('admin', 'pages.sayfa.form'), compact('kategoriler', 'sayfa'));
    }

    public function store(SayfaRequest $request, ImageUploadService $imageUploadService)
    {
        $sayfa = new Sayfa();
        $sayfa->kategori_id = $request->kategori_id;
        $sayfa->baslik = $request->baslik;
        $sayfa->icerik = $request->icerik;
        $sayfa->permalink = Str::slug($request->baslik);
        $sayfa->durum = $request->durum ?? 0;

        if ($request->hasFile('resim')) {
            $imagePaths = $imageUploadService->upload($request->file('resim'), 'sayfa');
            $sayfa->resim = $imagePaths['image'];
        }

        $sayfa->save();

        return redirect()->route(config('system.admin_prefix').'.sayfa.index')->with('success', 'Sayfa başarıyla eklendi.');
    }

    public function update(SayfaRequest $request, $id, ImageService $imageService)
    {
        // Güncellenecek Sayfa'u bul
        $sayfa = Sayfa::findOrFail($id);

        // Sayfa verilerini güncelle
        $sayfa->kategori_id = $request->kategori_id;
        $sayfa->baslik = $request->baslik;
        $sayfa->icerik = $request->icerik;
        $sayfa->permalink = Str::slug($request->baslik);
        $sayfa->durum = $request->durum ?? 0;

        // Eğer `delete_resim` seçilmişse, mevcut resmi tamamen sil
        if ($request->delete_resim == "1" && !$request->hasFile('resim')) {
            if ($sayfa->resim) {
                $imageService->delete($sayfa->resim, 'sayfa'); // Eski resmi klasörden sil
            }
            $sayfa->resim = null; // Veritabanındaki resim alanını boşalt
        }

        // Eğer yeni bir resim yüklenmişse, önce eskiyi sil ve yeni resmi ekle
        if ($request->hasFile('resim')) {
            $sayfa->resim = $imageService->update($request->file('resim'), $sayfa->resim, 'sayfa');
        }


        // Sayfa'u kaydet
        $sayfa->save();

        return redirect()->route(config('system.admin_prefix').'.sayfa.index')
            ->with('success', 'Sayfa başarıyla güncellendi.');
    }


    public function delete($id, ImageService $imageService)
    {
        $sayfa = Sayfa::findOrFail($id);

        // Eğer resim varsa, dosyayı sil
        if ($sayfa->resim) {
            $imageService->delete($sayfa->resim, 'sayfa');
        }

        // Sayfa kaydını veritabanından sil
        $sayfa->delete();

        return redirect()->route(config('system.admin_prefix').'.sayfa.index')->with('success', 'Sayfa başarıyla silindi.');
    }
}
