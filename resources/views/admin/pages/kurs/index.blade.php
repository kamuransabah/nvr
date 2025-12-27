@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Kurs Yönetimi')
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
@endsection

@section('toolbar')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Kurslar</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Kurs Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <a href="{{ route(config('system.admin_prefix').'.kurs.add') }}" class="btn btn-sm btn-primary">Yeni Ekle</a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" id="kurs_adi" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-350px ps-12" placeholder="Kurs Adı">
                </div>
                <!--end::Search-->
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <div class="w-100 mw-250px">
                    @if ($kategoriler)
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Kategori" name="kategori_id" id="kategori_id">
                            <option></option>
                            @foreach ($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->isim }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="w-100 mw-250px">
                    <select id="durum" class="form-select form-select-solid">
                        <option value="0">Pasif</option>
                        <option value="1" selected>Aktif</option>
                    </select>
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatable">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>Kurs</th>
                    <th>Kategori</th>
                    <th>Durum</th>
                    <th class="min-w-150px text-end">İşlem</th>
                </tr>
                </thead>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
@endsection

@section('js')


    <script src="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                language: {
                    url: '{{ theme_asset('admin', 'plugins/custom/datatables/tr.json') }}',
                },
                ajax: {
                    url: '{{ route(config('system.admin_prefix').'.kurs.data') }}',
                    data: function (d) {
                        d.kurs_adi = $('#kurs_adi').val();
                        d.kategori_id = $('#kategori_id').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'kurs_adi', name: 'kurs_adi' },
                    { data: 'kategori', name: 'kategori.isim' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 4,
                        className: 'text-end'
                    }
                ]
            });

            $('#kurs_adi').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#kategori_id').on('change', function() {
                table.draw();
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#kurs_adi').val('');
                $('#kategori_id').val('');
                $('#durum').val('');
                table.draw();
            });

            $('#tarih_filtre').on('apply.daterangepicker cancel.daterangepicker', function() {
                table.draw();
            });

            $('#datatable').on('draw.dt', function () {
                // Metronic 8+: mevcut instanceları günceller veya yoksa oluşturur
                if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                    KTMenu.createInstances();
                } else if (typeof KTMenu !== 'undefined' && KTMenu.init) {
                    // bazı sürümlerde init adında
                    KTMenu.init();
                }
            });
        });
    </script>
@endsection
