@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Data Listesi')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Data</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Data Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Menu wrapper-->
                <div class="me-4">
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ekleModal"><i class="fa-solid fa-plus me-2"></i>Data Ekle</a>
                    <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#datayukleModal"><i class="fa-solid fa-arrow-up-from-bracket me-2"></i>Data Yükle</a>
                    <a href="#" class="btn btn-sm btn-instagram" data-bs-toggle="modal" data-bs-target="#personelAtaModal"><i class="fa-solid fa-paper-plane me-2"></i>Data Gönder</a>
                </div>
                <!--end::Dropdown wrapper-->

            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="modal fade" tabindex="-1" id="ekleModal" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <form id="ekleForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni Kayıt Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="kurs_id">Kurs</label>
                            <select name="kurs_id" id="kurs_id" class="form-select" required>
                                <option value="">Kurs seçin</option>
                                @foreach ($kurslar as $kurs)
                                    <option value="{{ $kurs->id }}">{{ $kurs->kurs_adi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="isim">İsim</label>
                            <input type="text" class="form-control" name="isim" required>
                        </div>
                        <div class="mb-3">
                            <label for="eposta">E-posta</label>
                            <input type="email" class="form-control" name="eposta" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefon">Telefon</label>
                            <input type="text" class="form-control" name="telefon" required>
                        </div>
                        <div class="mb-3">
                            <label for="sehir">Şehir</label>
                            <input type="text" class="form-control" name="sehir">
                        </div>
                        <div class="mb-3">
                            <label for="basvuru_tarihi">Başvuru Tarihi</label>
                            <input type="datetime-local" class="form-control" name="basvuru_tarihi" required>
                        </div>
                        <div class="alert alert-danger d-none" id="ekleError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Personel Atama Modal -->
    <div class="modal fade" id="personelAtaModal" tabindex="-1" aria-labelledby="personelAtaModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <form id="personelAtaForm" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Toplu Data Atama</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div id="personelAtaError" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="durumAta" class="form-label">Durum</label>
                            <select name="durum" id="durumAta" class="form-select" required>
                                <option value="">Durum seçin</option>
                                @foreach ($durumlar as $durum)
                                    <option value="{{ $durum->key }}">{{ $durum->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="personel_data" class="form-label">Data Kime Ait</label>
                            <select name="personel_data" id="personel_data" class="form-select" data-control="select2" data-placeholder="Seçiniz" required>
                                <option value="0">Boş Data</option>
                                @foreach ($personeller as $personel)
                                    <option value="{{ $personel->id }}">{{ $personel->isim.' '.$personel->soyisim }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kursAta" class="form-label">Kurs</label>
                            <select name="kurs_id" id="kursAta" class="form-select" required>
                                <option value="">Önce durum seçin</option>
                            </select>
                        </div>

                        <div class="separator my-10"></div>

                        <div class="row mb-3">
                            <div class="col-8">
                                <label for="personelAta" class="form-label">Atanacak Personel</label>
                                <select name="personel_id" id="personelAta" class="form-select" data-control="select2" data-placeholder="Personel Seçiniz" required>
                                    <option value="">Personel seçin</option>
                                    @foreach ($personeller as $personel)
                                        @if ($personel->durum == 1)
                                            <option value="{{ $personel->id }}">{{ $personel->isim.' '.$personel->soyisim }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="adetAta" class="form-label">Adet</label>
                                <input type="number" min="1" name="adet" id="adetAta" class="form-control" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Atamayı Yap</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toplu Data Yükleme Modal -->
    <div class="modal fade" id="datayukleModal" tabindex="-1" aria-labelledby="datayukleModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-fullscreen-md-down">
            <form id="excelUploadForm" action="{{ route(config('system.admin_prefix').'.data.topludatayukle') }}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Toplu Data Yükleme</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="upload_excel" class="form-label">Dosya</label>
                            <input type="file" name="upload_excel" accept=".xlsx,.xls" required class="form-control" id="upload_excel">
                            <div class="text-muted fs-7">Sadece .xls, .xlsx yükleyebilirsiniz.</div>
                        </div>
                        <div class="mb-3">
                            <label for="upload_kurs_id" class="form-label">Kurs</label>
                            <select name="upload_kurs_id" id="upload_kurs_id" class="form-select" data-control="select2" data-placeholder="Seçiniz" required>
                                <option value="">Kurs Seçin</option>
                                @foreach ($kurslar as $kurs)
                                    <option value="{{ $kurs->id }}">{{ $kurs->kurs_adi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Yükle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!--begin::Row-->
    <div class="row g-5 g-xl-8">
        @foreach ($stats as $stat)
            <div class="col-xl-3">
                <x-admin.data-widget
                    :title="$stat['title']"
                    :subtitle="$stat['subtitle']"
                    :value="$stat['value']"
                    :color="$stat['color']"
                    :key="$stat['key']"
                />
            </div>
        @endforeach
    </div>
    <!--end::Row-->

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
                    <input type="text" id="arama" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="İsim, Eposta veya Telefon">
                </div>
                <!--end::Search-->
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <div class="w-100 mw-250px">
                    <!--begin::Select2-->
                    @if ($kurslar->isNotEmpty())
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Kurs" name="kurs" id="kurs">
                            <option></option>
                            @foreach ($kurslar as $kurs)
                                <option value="{{ $kurs->id }}">{{ $kurs->kurs_adi }}</option>
                            @endforeach
                        </select>
                    @endif
                    <!--end::Select2-->
                </div>
                <div class="w-100 mw-250px">
                    <input type="text" id="tarih_filtre" class="form-control form-control-solid" placeholder="Tarih" />
                </div>
                <div class="filter-button">
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center py-1">
                        <!--begin::Wrapper-->
                        <div class="me-4">
                            <!--begin::Menu-->
                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                <i class="ki-outline ki-filter fs-5 text-gray-500 me-1"></i>Filtrele</a>
                            <!--begin::Menu 1-->
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_673c0fdc4887a">
                                <!--begin::Header-->
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-gray-900 fw-bold">Data Filtreleme</div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Menu separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Menu separator-->
                                <!--begin::Form-->
                                <div class="px-7 py-5">
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold">Data ID:</label>
                                        <div>
                                            <input type="text" class="form-control form-select-solid" name="data_id" id="data_id">
                                        </div>
                                    </div>
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold">Personel:</label>
                                        <div>
                                            @if ($personeller->isNotEmpty())
                                                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Personel" name="personel" id="personel">
                                                    <option></option>
                                                    @foreach ($personeller as $personel)
                                                        <option value="{{ $personel->id }}">{{ $personel->isim. ' '.$personel->soyisim }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold">Durum:</label>
                                        <div>
                                            <select class="form-select form-select-solid" id="durum" data-placeholder="Data Durum">
                                                <option value="">Tümü</option>
                                                @foreach($durumlar as $durum)
                                                    @if($durum->key == 3)
                                                        <optgroup label="Olumsuz">
                                                            @foreach($olumsuzlar as $nedeni)
                                                                <option value="olumsuz-{{ $nedeni->key }}">{{ $nedeni->value }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @else
                                                        <option value="{{ $durum->key }}">{{ $durum->value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Form-->
                            </div>
                            <!--end::Menu 1-->
                            <!--end::Menu-->
                        </div>
                        <!--end::Wrapper-->
                        <a href="#" class="btn btn-sm btn-icon btn-secondary me-2" id="reset"  data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" title="Sıfırla"><i class="fa-solid fa-arrows-rotate"></i></a>
                        <a href="#"
                           class="btn btn-sm btn-icon btn-success"
                           data-bs-toggle="tooltip"
                           data-bs-custom-class="tooltip-inverse"
                           title="Excel'e Aktar"
                           data-bs-target="#exportModal"
                           id="openExportModal">
                            <i class="fa-solid fa-file-excel fs-3"></i>
                        </a>
                    </div>
                    <!--end::Actions-->

                    <!-- start: modal-excelform -->
                    <div class="modal" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="exportForm" method="POST" action="{{ route(config('system.admin_prefix').'.data.export') }}">
                                    @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Data Export</h5>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="isim" value="{{ request('isim') }}">
                                    <input type="hidden" name="kurs_id" value="{{ request('kurs_id') }}">
                                    <input type="hidden" name="durum" value="{{ request('durum') }}">

                                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="isim" checked id="export_isim" />
                                                    <label class="form-check-label" for="export_isim">İsim</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="eposta" checked id="export_eposta" />
                                                    <label class="form-check-label" for="export_eposta">E-Posta</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="telefon" checked id="export_telefon" />
                                                    <label class="form-check-label" for="export_telefon">Telefon</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="sehir" checked id="export_sehir" />
                                                    <label class="form-check-label" for="export_sehir">Şehir</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="basvuru_tarihi" checked id="export_tarih" />
                                                    <label class="form-check-label" for="export_tarih">Başvuru Tarihi</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="fields[]" value="durum" checked id="export_durum" />
                                                    <label class="form-check-label" for="export_durum">Durum</label>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-file-excel"></i> Excel İndir
                                    </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end: modal-excelform -->

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
                    <th>İsim</th>
                    <th>Telefon</th>
                    <th>Kurs</th>
                    <th>Personel</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
                </thead>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>


    <div id="kt_drawer_example_basic"
        class="bg-white"
        data-kt-drawer="true"
        data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#kt_drawer_example_basic_button"
        data-kt-drawer-close="#kt_drawer_example_basic_close"
        data-kt-drawer-width="500px">

        <div class="card w-100 rounded-0">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 lh-1">Kurslara Göre Aktif Datalar</a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_example_basic_close">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>                </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-2">
                    <tbody>
                    @forelse ($kursStats as $kurs)
                        <tr>
                            <td>{{ ozet($kurs['adi'],40) }}</td>
                            <td><a href="#" class="btn btn-sm btn-icon btn-light-twitter btn-sm">{{ $kurs['adet'] }}</a></td>
                        </tr>
                    @empty
                        <td colspan="2">Kayıt bulunamadı.</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <!--end::Card body-->
        </div>

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
                    url: '{{ route(config('system.admin_prefix').'.data.datatable') }}',
                    data: function (d) {
                        d.arama = $('#arama').val();
                        d.kurs = $('#kurs').val();
                        d.data_id = $('#data_id').val();
                        d.personel = $('#personel').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'isim', name: 'isim' },
                    { data: 'telefon', name: 'telefon' },
                    { data: 'kurs', name: 'kurs' },
                    { data: 'personel', name: 'personel' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ]
            });

            $('#arama').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#kurs').on('change', function() {
                table.draw();
            });

            $('#data_id').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#personel').on('change', function() {
                table.draw();
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#arama').val('');
                $('#kurs').val('').trigger('change');
                $('#data_id').val('');
                $('#personel').val('').trigger('change');
                $('#tarih_filtre').val('');
                $('#durum').val('');
                $('select[data-control="select2"]').val('').trigger('change');
                table.draw();
            });

            $('#tarih_filtre').on('apply.daterangepicker cancel.daterangepicker', function() {
                table.draw();
            });
        });
    </script>


    <script>
        window.statisticsChartData = {
            @foreach ($stats as $stat)
            "{{ $stat['key'] }}": {
                data: @json($stat['data']),
                labels: @json($stat['labels'])
            },
            @endforeach
        };
    </script>

    <script>

        function data_widget() {
            var charts = document.querySelectorAll('.data-widget-chart');

            [].slice.call(charts).forEach(function (element) {
                var height = parseInt(KTUtil.css(element, 'height'));
                if (!element) return;

                var color = element.getAttribute('data-kt-chart-color');
                var key = element.getAttribute('data-kt-chart-key') ?? 'toplam';

                var labelColor = KTUtil.getCssVariableValue('--bs-gray-800');
                var baseColor = KTUtil.getCssVariableValue('--bs-' + color);
                var lightColor = KTUtil.getCssVariableValue('--bs-' + color + '-light');

                var chartData = window.statisticsChartData[key];

                chartData.data = Object.values(chartData.data);
                chartData.labels = Object.values(chartData.labels);

                if (!chartData || !Array.isArray(chartData.data)) {
                    console.warn('Chart verisi geçersiz:', key, chartData);
                    return;
                }

                var options = {
                    series: [{
                        name: 'Toplam',
                        data: chartData.data
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'area',
                        height: height,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        sparkline: { enabled: true }
                    },
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    fill: { type: 'solid', opacity: 0.3 },
                    stroke: {
                        curve: 'smooth',
                        show: true,
                        width: 3,
                        colors: [baseColor]
                    },
                    xaxis: {
                        categories: chartData.labels,
                        labels: {
                            show: true,
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            show: false,
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        }
                    },
                    tooltip: {
                        style: { fontSize: '12px' },
                        y: {
                            formatter: function (val) {
                                return val + ' data';
                            }
                        }
                    },
                    colors: [baseColor],
                    markers: {
                        colors: [baseColor],
                        strokeColor: [lightColor],
                        strokeWidth: 3
                    }
                };

                var chart = new ApexCharts(element, options);
                chart.render();
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            data_widget();
        });
    </script>

    <script type="text/javascript">
        $('#ekleForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const modal = $('#ekleModal');
            const errorDiv = $('#ekleError');

            $.ajax({
                url: '{{ route(config('system.admin_prefix').".data.dataekle") }}',
                method: 'POST',
                data: form.serialize(),
                success: function (res) {
                    modal.modal('hide');
                    form[0].reset();
                    $('#blogs-table').DataTable().draw();

                    // Başarılı ekleme SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: res.message || 'Kayıt başarıyla eklendi.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let response = xhr.responseJSON;
                    let errors = response?.errors || {};
                    let errorMessages = '';

                    if (Object.keys(errors).length > 0) {
                        errorMessages = Object.values(errors).map(e => `<div>${e}</div>`).join('');
                    } else if (response?.message) {
                        errorMessages = `<div>${response.message}</div>`;
                    } else {
                        errorMessages = `<div>Bilinmeyen bir hata oluştu.</div>`;
                    }

                    errorDiv.html(errorMessages).removeClass('d-none');

                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        html: errorMessages,
                        confirmButtonText: 'Tamam'
                    });
                }
            });
        });

        $('#durumAta, #personel_data').on('change', function () {
            const durum = $('#durumAta').val();
            const personel_id = $('#personel_data').val();
            const kursSelect = $('#kursAta');

            if (!durum || !personel_id) {
                kursSelect.html('<option value="">Önce durum ve personel seçin</option>');
                return;
            }

            kursSelect.html('<option value="">Yükleniyor...</option>');

            $.get('{{ route(config("system.admin_prefix") . ".data.kursListesi") }}', { durum, personel_id }, function (data) {
                if (data.length > 0) {
                    let html = '<option value="">Kurs seçin</option>';
                    data.forEach(k => {
                        html += `<option value="${k.id}">${k.ad}</option>`;
                    });
                    kursSelect.html(html);
                } else {
                    kursSelect.html('<option value="">Uygun kurs bulunamadı</option>');
                }
            });
        });



        $('#personelAtaForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const modal = $('#personelAtaModal');
            const errorDiv = $('#personelAtaError');

            $.ajax({
                url: '{{ route(config("system.admin_prefix") . ".data.personelAta") }}',
                method: 'POST',
                data: form.serialize(),
                success: function (res) {
                    modal.modal('hide');
                    form[0].reset();
                    $('#blogs-table').DataTable().draw(); // tabloyu güncelle

                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        html: `${res.updated} kayıt başarıyla <b>${res.to}</b> personeline aktarıldı.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let response = xhr.responseJSON;
                    let errorMessages = '';

                    if (response?.errors) {
                        errorMessages = Object.values(response.errors).map(e => `<div>${e}</div>`).join('');
                    } else {
                        errorMessages = `<div>${response.message || 'Bilinmeyen bir hata oluştu.'}</div>`;
                    }

                    errorDiv.html(errorMessages).removeClass('d-none');

                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        html: errorMessages,
                        confirmButtonText: 'Tamam'
                    });
                }
            });
        });

        $('#personelAtaModal').on('shown.bs.modal', function () {
            $('#personelAtaModal [data-control="select2"]').select2({
                dropdownParent: $('#personelAtaModal')
            });
        });

        $(document).ready(function () {
            $('[data-control="select2"]:not(#personelAtaModal [data-control="select2"])').select2();
        });

        $('#datayukleModal').on('shown.bs.modal', function () {
            $('#datayukleModal [data-control="select2"]').select2({
                dropdownParent: $('#datayukleModal')
            });
        });

        $(function () {
            // Tooltip'i aktif et
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Modal tetikleyici
            $('#openExportModal').on('click', function (e) {
                e.preventDefault(); // link davranışını engelle
                const modal = new bootstrap.Modal(document.getElementById('exportModal'));
                modal.show();
            });
        });

        $('#openExportModal').on('click', function (e) {
            e.preventDefault();

            // DataTables üzerindeki filtreleri al
            const table = $('#blogs-table').DataTable();

            const filters = {
                baslik: $('#baslik').val(),
                kurs: $('#kurs').val(),
                durum: $('#durum').val(),
                tarih_filtre: $('#tarih_filtre').val(),
                // diğer filtreleri burada ekleyebilirsin
            };

            // Modal formuna bu filtreleri ekle
            for (const key in filters) {
                if ($('#exportForm input[name="' + key + '"]').length === 0) {
                    $('#exportForm').append(
                        $('<input>').attr({
                            type: 'hidden',
                            name: key,
                            value: filters[key]
                        })
                    );
                } else {
                    $('#exportForm input[name="' + key + '"]').val(filters[key]);
                }
            }
        });

    </script>


@endsection
