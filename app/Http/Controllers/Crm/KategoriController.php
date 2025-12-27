<?php

namespace App\Http\Controllers\Crm;


use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class KategoriController extends Controller
{
    public function index(Request $request, $tur = null)
    {
        if (!$tur) {
            return redirect()->route(config('system.admin_prefix'));
        }

        $kategoriler = Kategori::where('ust_id', 0)
            ->where('tur', $tur) // Filtreleme eklendi!
            ->with('children')
            ->orderBy('sira')
            ->get();

        return view(theme_view('admin', 'pages.modul.kategori'), compact('kategoriler', 'tur'));
    }

    public function store(Request $request, $tur)
    {
        $request->validate([
            'isim' => 'required|string|max:250',
            'aciklama' => 'nullable|string|max:250',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ]);

        $insertData = new Kategori();
        $insertData->tur = $request->tur;
        $insertData->isim = $request->isim;
        $insertData->aciklama = $request->aciklama;
        $insertData->sira = $request->sira ?? 1;
        $insertData->permalink = Str::slug($request->isim);
        $insertData->durum = $request->durum ?? 0;

        $insertData->save();

        return redirect()->route(config('system.admin_prefix').'.kategori.index', ['tur' => $tur])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kayıt başarıyla eklendi.',
            ]);
    }

    public function edit(Request $request, $tur, $id)
    {
        $data = Kategori::findOrFail($id);
        $kategoriler = Kategori::where('ust_id', 0)
            ->where('tur', $tur)
            ->with('children')
            ->orderBy('sira')
            ->get();

        return view(theme_view('admin', 'pages.modul.kategori'), compact('data','kategoriler', 'tur'));
    }

    public function update(Request $request, $tur, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'isim' => 'required|string|max:250',
            'aciklama' => 'nullable|string|max:250',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ]);

        $kategori->update([
            'isim' => $request->isim,
            'aciklama' => $request->aciklama,
            'permalink' => Str::slug($request->isim),
            'sira' => $request->sira ?? 1,
            'durum' => $request->durum ?? 1,
        ]);

        return redirect()->route(config('system.admin_prefix').'.kategori.index', ['tur' => $tur])
            ->with('alert', [
                'library' => 'sweetalert',
                'type' => 'success',
                'message' => 'Kategori başarıyla güncellendi.',
            ]);
    }

    public function delete(Request $request, $tur, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        flashAlert('success', 'Kategori başarıyla silindi.', 'toastr');

        return redirect()->route(config('system.admin_prefix').'.kategori.index', ['tur' => $tur]);
    }

    private function buildTree($categories, $parentId = 0)
    {
        $branch = [];
        foreach ($categories as $category) {
            if ($category->ust_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                }
                $branch[] = $category;
            }
        }
        return $branch;
    }

    public function updateOrder(Request $request, $tur)
    {
        $data = $request->input('order');

        if (!$data) {
            return response()->json(['error' => 'Geçersiz veri'], 400);
        }

        foreach ($data as $item) {
            Kategori::where('id', $item['id'])->update([
                'ust_id' => $item['ust_id'], // Üst kategori ID'sini kaydediyoruz
                'sira' => $item['sira']
            ]);
        }

        return response()->json(['success' => true]);
    }


    private function updateCategoryOrder($items, $parentId = 0, $sira = 1)
    {
        foreach ($items as $index => $item) {
            Kategori::where('id', $item['id'])->update([
                'ust_id' => $parentId,
                'sira' => $sira + $index // Sıra 1'den başlayacak
            ]);

            if (isset($item['children'])) {
                $this->updateCategoryOrder($item['children'], $item['id'], 1); // Alt elemanlar için de 1'den başlat
            }
        }
    }

}
