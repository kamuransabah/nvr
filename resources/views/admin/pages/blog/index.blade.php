@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Blog Yönetimi')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Blog</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Blog Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Wrapper-->
                <div class="me-4">
                    <!--begin::Menu-->
                    <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <i class="ki-outline ki-filter fs-5 text-gray-500 me-1"></i>Filtrele</a>
                    <!--begin::Menu 1-->
                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_673c0fdbd0b14">
                        <!--begin::Header-->
                        <div class="px-7 py-5">
                            <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Menu separator-->
                        <div class="separator border-gray-200"></div>
                        <!--end::Menu separator-->

                    </div>
                    <!--end::Menu 1-->
                    <!--end::Menu-->
                </div>
                <!--end::Wrapper-->
                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.blog.add') }}" class="btn btn-sm btn-primary">Yeni Ekle</a>
                <!--end::Button-->
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
        <div class="card-header align-items-center py-5">
            <!--begin::Card toolbar-->
            <div class="card-toolbar flex-row-fluid justify-content-end">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div class="col-md-3">
                        <label class="form-label" for="baslik">Başlık</label>
                        <input type="text" class="form-control" name="baslik" id="baslik" placeholder="Anahtar Kelime">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="kategori_id">Kategori</label>
                        <select id="kategori_id" class="form-select text-capitalize">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->isim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tarih_filtre">Tarih</label>
                        <input type="text" id="tarih_filtre" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="durum">Durum</label>
                        <select id="durum" class="form-select text-capitalize">
                            <option value="">Tümü</option>
                            <option value="0">Pasif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="reset" class="btn btn-secondary mt-4">Sıfırla</button>
                    </div>

                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="blogs-table">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>Kategori</th>
                    <th>Tarih</th>
                    <th>Hit</th>
                    <th>Durum</th>
                    <th class="min-w-150px">İşlem</th>
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

            $('#tarih_filtre').daterangepicker({
                autoUpdateInput: false,
                ranges: {
                    'Bugün': [moment(), moment()],
                    'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Son 7 gün': [moment().subtract(6, 'days'), moment()],
                    'Son 30 gün': [moment().subtract(29, 'days'), moment()],
                    'Bu ay': [moment().startOf('month'), moment().endOf('month')],
                    'Geçen ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
            });

            $('#tarih_filtre').on('apply.daterangepicker', function(ev, picker) {

                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');

                console.log("Filtrelenen Tarih:", startDate, " - ", endDate);

                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.draw();
            });

            $('#tarih_filtre').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.draw();
            });

            var table = $('#blogs-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                language: {
                    url: '{{ theme_asset('admin', 'plugins/custom/datatables/tr.json') }}',
                },
                ajax: {
                    url: '{{ route(config('system.admin_prefix').'.blog.data') }}',
                    data: function (d) {
                        d.baslik = $('#baslik').val();
                        d.kategori_id = $('#kategori_id').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'baslik', name: 'baslik' },
                    { data: 'kategori', name: 'kategori.isim' },
                    { data: 'tarih', name: 'tarih' },
                    { data: 'hit', name: 'hit' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ]
            });

            $('#baslik').on('keyup', function() {
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
                $('#baslik').val('');
                $('#kategori_id').val('');
                $('#tarih_filtre').val('');
                $('#durum').val('');
                table.draw();
            });

            $('#tarih_filtre').on('apply.daterangepicker cancel.daterangepicker', function() {
                table.draw();
            });
        });
    </script>
@endsection
