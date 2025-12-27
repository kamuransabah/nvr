<?php

if (!function_exists('renderCategoryItem')) {
    function renderCategoryItem($kategori, $tur)
    {
        $routePrefix = config('system.admin_prefix') . '.kategori';

        $html = '<li class="sortable-item mb-2" data-id="' . $kategori->id . '">
                    <div class="sortable-content d-flex justify-content-between align-items-center p-2 border rounded">
                        <span class="drag-handle me-2" style="cursor: grab;"><i class="fa-solid fa-up-down-left-right text-body-secondary"></i></span>
                        <span class="category-name flex-grow-1">' . $kategori->isim . '</span>
                        <div class="d-flex">
                             <a href="' . route($routePrefix . '.edit', ['tur' => $tur, 'id' => $kategori->id]) . '" class="btn btn-icon btn-sm btn-light-info me-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="' . route($routePrefix . '.delete', ['tur' => $tur, 'id' => $kategori->id]) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-icon btn-sm btn-light-danger" data-confirm-delete="true">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>';

        // Alt kategoriler varsa göster, yoksa boş bir liste ekle (SortableJS çalışsın diye)
        $html .= '<ul class="sortable-list mt-2" style="list-style: none;">';
        if ($kategori->children->isNotEmpty()) {
            foreach ($kategori->children as $child) {
                $html .= renderCategoryItem($child, $tur);
            }
        }
        $html .= '</ul>';

        $html .= '</li>';
        return $html;
    }
}

if (!function_exists('renderMenuItem')) {
    function renderMenuItem($menu, $tur)
    {
        $routePrefix = config('system.admin_prefix') . '.menu';

        $html = '<li class="sortable-item mb-2" data-id="' . $menu->id . '">
                    <div class="sortable-content d-flex justify-content-between align-items-center p-2 border rounded">
                        <span class="drag-handle me-2" style="cursor: grab;"><i class="fa-solid fa-up-down-left-right text-body-secondary"></i></span>
                        <span class="menu-name flex-grow-1">' . $menu->isim . '</span>
                        <div class="btn-group">
                            <a href="' . route($routePrefix . '.edit', ['tur' => $tur, 'id' => $menu->id]) . '" class="btn btn-icon btn-sm btn-light-info me-1">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="' . route($routePrefix . '.delete', ['tur' => $tur, 'id' => $menu->id]) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-icon btn-sm btn-light-danger" data-confirm-delete="true">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>';

        // Eğer alt menüler varsa göster, yoksa boş bir liste ekle (SortableJS çalışsın diye)
        $html .= '<ul class="sortable-list mt-2" style="list-style: none;">';
        if ($menu->children->isNotEmpty()) {
            foreach ($menu->children as $child) {
                $html .= renderMenuItem($child, $tur);
            }
        }
        $html .= '</ul>';

        $html .= '</li>';
        return $html;
    }
}
