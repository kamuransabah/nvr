<?php

namespace App\Http\Controllers\Crm;


use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MenuController extends Controller
{
    public function index(Request $request, $tur = null)
    {
        if (!$tur) {
            return redirect()->route(config('system.admin_prefix'));
        }

        $menuler = Menu::where('ust_id', 0)
            ->where('tur', $tur) // Filtreleme eklendi!
            ->with('children')
            ->orderBy('sira')
            ->get();

        return view(theme_view('admin', 'pages.modul.menu'), compact('menuler', 'tur'));
    }

    public function store(Request $request, $tur)
    {
        $request->validate([
            'isim' => 'required|string|max:250',
            'link' => 'required|string|max:250',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ]);

        $insertData = new Menu();
        $insertData->tur = $request->tur;
        $insertData->isim = $request->isim;
        $insertData->link = $request->link;
        $insertData->sira = $request->sira ?? 1;
        $insertData->durum = $request->durum ?? 0;

        $insertData->save();

        return redirect()->route(config('system.admin_prefix').'.menu.index', ['tur' => $tur])->with('success', 'Menu başarıyla eklendi.');
    }

    public function edit(Request $request, $tur, $id)
    {
        $data = Menu::findOrFail($id);
        $menuler = Menu::where('ust_id', 0)
            ->where('tur', $tur)
            ->with('children')
            ->orderBy('sira')
            ->get();

        return view(theme_view('admin', 'pages.modul.menu'), compact('data','menuler', 'tur'));
    }

    public function update(Request $request, $tur, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'isim' => 'required|string|max:250',
            'link' => 'required|string|max:250',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ]);

        $menu->update([
            'isim' => $request->isim,
            'link' => $request->link,
            'sira' => $request->sira ?? 1,
            'durum' => $request->durum ?? 1,
        ]);

        return redirect()->route(config('system.admin_prefix').'.menu.index', ['tur' => $tur])->with('success', 'Menü başarıyla güncellendi.');
    }

    public function delete(Request $request, $tur, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route(config('system.admin_prefix').'.menu.index', ['tur' => $tur])->with('success', 'Menü başarıyla silindi.');
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
            Menu::where('id', $item['id'])->update([
                'ust_id' => $item['ust_id'], // Üst menu ID'sini kaydediyoruz
                'sira' => $item['sira']
            ]);
        }

        return response()->json(['success' => true]);
    }


    private function updateCategoryOrder($items, $parentId = 0, $sira = 1)
    {
        foreach ($items as $index => $item) {
            Menu::where('id', $item['id'])->update([
                'ust_id' => $parentId,
                'sira' => $sira + $index // Sıra 1'den başlayacak
            ]);

            if (isset($item['children'])) {
                $this->updateCategoryOrder($item['children'], $item['id'], 1); // Alt elemanlar için de 1'den başlat
            }
        }
    }

}
