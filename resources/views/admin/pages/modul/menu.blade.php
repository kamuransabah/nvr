@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Menü Yönetimi')
@section('css')

@endsection
@section('toolbar')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Menü Yönetimi</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ ucfirst($tur) }} Menü Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="row">
        <!-- Menü Listeleme -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ ucfirst($tur) }} Menü</h3>
                </div>
                <div class="card-body">
                    <ul id="sortable" class="sortable-list list-unstyled">
                        @foreach($menuler as $menu)
                            {!! renderMenuItem($menu, $tur) !!}
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Menü Ekleme / Düzenleme Formu -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($data) ? 'Menüyü Düzenle' : 'Yeni Menü Ekle' }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ isset($data) ? route(config('system.admin_prefix').'.menu.update', ['tur' => $tur, 'id' => $data->id]) : route(config('system.admin_prefix').'.menu.store', $tur) }}" method="POST">
                        @csrf
                        @if(isset($data))
                            @method('PUT')
                        @endif
                        <div class="mb-10">
                            <label for="isim" class="required form-label">Menü Adı</label>
                            <input type="text" name="isim" id="isim" class="form-control" value="{{ old('isim', $data->isim ?? '') }}" required/>
                            @error('isim')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label for="link" class="required form-label">Bağlantı</label>
                            <input type="text" name="link" id="link" class="form-control" value="{{ old('link', $data->link ?? '') }}" required/>
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-10">
                            <label for="sira" class="required form-label">Sıra</label>
                            <input type="text" name="sira" id="sira" class="form-control" value="{{ old('sira', $data->sira ?? '') }}" required/>
                            @error('sira')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-success me-3">{{ isset($data) ? 'Güncelle' : 'Ekle' }}</button>
                                @if(isset($data))
                                    <a href="{{ route(config('system.admin_prefix').'.menu.index', $tur) }}" class="btn btn-secondary" data-confirm-delete="true">İptal</a>
                                @endif
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum"
                                    {{ isset($sayfa) && $sayfa->durum == 1 ? 'checked' : '' }}>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <h4></h4>


        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateMenuOrder() {
                let order = [];

                function parseList(list, parentId = 0) {
                    list.forEach((item, index) => {
                        order.push({
                            id: item.getAttribute("data-id"),
                            ust_id: parentId, // Üst menü ID'si
                            sira: index + 1 // 1'den başlayan sıralama
                        });

                        let children = item.querySelector(".sortable-list");
                        if (children && children.children.length > 0) { // Eğer alt menü varsa
                            parseList([...children.children], item.getAttribute("data-id"));
                        }
                    });
                }

                let rootList = document.querySelector("#sortable");
                parseList([...rootList.children]);

                fetch("{{ route(config('system.admin_prefix').'.menu.updateOrder', ['tur' => $tur]) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ order: order })
                })
                    .then(response => response.json())
                    .then(data => console.log("Menü sıralama ve üst-id güncellendi!", data))
                    .catch(error => console.error("Hata:", error));
            }

            new Sortable(document.getElementById("sortable"), {
                handle: ".drag-handle",
                animation: 150,
                group: {
                    name: "nested",
                    pull: true,
                    put: true
                },
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onEnd: function() {
                    updateMenuOrder();
                }
            });

            document.querySelectorAll(".sortable-list").forEach(function(el) {
                new Sortable(el, {
                    group: "nested",
                    animation: 150,
                    handle: ".drag-handle",
                    onEnd: function() {
                        updateMenuOrder();
                    }
                });
            });
        });
    </script>


@endsection
