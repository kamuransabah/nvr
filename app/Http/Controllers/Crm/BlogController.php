<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\Kategori;
use App\Models\Kurs;
use App\Services\ImageService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;


class BlogController extends Controller
{
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $kategoriler = Kategori::where('tur', 'blog')->get();

        return view(theme_view('admin', 'pages.blog.index'), compact('kategoriler'));
    }


    public function getBlogs(Request $request)
    {
        $blogs = Blog::with('kategori')->select('blog.id', 'blog.kategori_id', 'blog.baslik', 'blog.permalink', 'blog.created_at', 'blog.hit', 'blog.durum')
            ->orderBy('id', 'desc');

        // Başlığa göre filtreleme
        if ($request->has('baslik') && !empty($request->baslik)) {
            $blogs->where('baslik', 'like', '%' . $request->baslik . '%');
        }

        // Kategoriye göre filtreleme
        if ($request->has('kategori_id') && !empty($request->kategori_id)) {
            $blogs->where('kategori_id', $request->kategori_id);
        }

        // Duruma göre filtreleme
        if ($request->has('durum') && !empty($request->durum)) {
            $blogs->where('durum', $request->durum);
        }

        if ($request->has('durum') && $request->durum !== null) {
            $blogs->where('durum', $request->durum);
        }

        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $blogs->whereBetween('created_at', [$baslangicTarihi, $bitisTarihi]);
        }


        return DataTables::of($blogs)
            ->addColumn('id', function ($blog) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$blog->id.'</button>';
            })
            ->addColumn(
                'baslik', function ($blog) {
                return '<p class="text-wrap">'.$blog->baslik.'</p>';
            })
            ->addColumn('kategori', function ($blog) {
                return $blog->kategori ? $blog->kategori->isim : 'Kategori Yok';
            })
            ->addColumn('tarih', function ($blog) {
                //return Carbon::parse($blog->tarih)->diffForHumans();
                return Carbon::parse($blog->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY');
            })
            ->addColumn('hit', function ($blog) {
                return '<button type="button" disabled class="btn btn-sm btn-secondary">
                    <i class="ki-duotone ki-chart-simple-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    '.$blog->hit.'
                    </button>';
            })
            ->addColumn('durum', function ($blog) {
                return '<span class="badge badge-light-'.status()->get($blog->durum).'">'.status()->get($blog->durum, 'text').'</span>';
                //return '<span class="badge bg-'.status()->get($blog->durum).'-subtle text-'.status()->get($blog->durum).' fw-semibold fs-2 gap-1 d-inline-flex align-items-center">'.status()->get($blog->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {

                return '
                    <a href="' . route(config('system.admin_prefix').'.blog.index', ['id' => $row->permalink]) . '" target="_blank" class="btn btn-sm btn-icon btn-light-primary me-1" data-bs-toggle="tooltip" title="Görüntüle">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>

                    <a href="' . route(config('system.admin_prefix').'.blog.edit', ['id' => $row->id]) . '" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <form action="' . route(config('system.admin_prefix').'.blog.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil"  data-confirm-delete="true">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>';

            })
            ->rawColumns(['id', 'baslik', 'hit', 'durum', 'islem'])
            ->make(true);
    }

    public function add()
    {
        $kategoriler = Kategori::where('tur', 'blog')->get();
        $kurslar = Kurs::all();
        return view(theme_view('admin', 'pages.blog.form'), compact('kategoriler', 'kurslar'));
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $kategoriler = Kategori::where('tur', 'blog')->get();
        $kurslar = Kurs::all();
        return view(theme_view('admin', 'pages.blog.form'), compact('kategoriler', 'blog', 'kurslar'));
    }

    public function store(BlogRequest $request, ImageUploadService $imageUploadService)
    {
        $blog = new Blog();
        $blog->kategori_id = $request->kategori_id;
        $blog->baslik = $request->baslik;
        $blog->ozet = $request->ozet;
        $blog->icerik = $request->icerik;
        $blog->permalink = Str::slug($request->baslik);
        $blog->durum = $request->durum ?? 0;
        $blog->kurs_id = json_encode($request->kurs_id ?? []);
        $blog->yayin_tarihi = Carbon::createFromFormat('Y-m-d H:i', $request->yayin_tarihi);

        if ($request->hasFile('resim')) {
            $imagePaths = $imageUploadService->upload($request->file('resim'), 'blog');
            $blog->resim = $imagePaths['image'];
        }

        $blog->save();

        return redirect()->route(config('system.admin_prefix').'.blog.index')->with('success', 'Blog başarıyla eklendi.');
    }

    public function update(BlogRequest $request, $id, ImageService $imageService)
    {
        // Güncellenecek Blog'u bul
        $blog = Blog::findOrFail($id);

        // Blog verilerini güncelle
        $blog->kategori_id = $request->kategori_id;
        $blog->baslik = $request->baslik;
        $blog->ozet = $request->ozet;
        $blog->icerik = $request->icerik;
        $blog->permalink = Str::slug($request->baslik);
        $blog->durum = $request->durum ?? 0;
        $blog->kurs_id = json_encode($request->kurs_id ?? []);
        $blog->yayin_tarihi = Carbon::createFromFormat('Y-m-d H:i', $request->yayin_tarihi);

        // Eğer `delete_resim` seçilmişse, mevcut resmi tamamen sil
        if ($request->delete_resim == "1" && !$request->hasFile('resim')) {
            if ($blog->resim) {
                $imageService->delete($blog->resim, 'blog'); // Eski resmi klasörden sil
            }
            $blog->resim = null; // Veritabanındaki resim alanını boşalt
        }

        // Eğer yeni bir resim yüklenmişse, önce eskiyi sil ve yeni resmi ekle
        if ($request->hasFile('resim')) {
            $blog->resim = $imageService->update($request->file('resim'), $blog->resim, 'blog');
        }


        // Blog'u kaydet
        $blog->save();

        return redirect()->route(config('system.admin_prefix').'.blog.index')
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Blog başarıyla güncellendi.',
            ]);
    }


    public function delete($id, ImageService $imageService)
    {
        $blog = Blog::findOrFail($id);

        // Eğer resim varsa, dosyayı sil
        if ($blog->resim) {
            $imageService->delete($blog->resim, 'blog');
        }

        // Blog kaydını veritabanından sil
        $blog->delete();

        return redirect()->route(config('system.admin_prefix').'.blog.index')->with('alert', [
            'library' => 'sweetalert',
            'type' => 'success',
            'message' => 'Blog başarıyla silindi.',
        ]);
    }


}
