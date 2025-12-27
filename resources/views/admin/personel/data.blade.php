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
                            <label for="urun_id">Ürün</label>
                            <select name="urun_id" id="urun_id" class="form-select" required>
                                <option value="">Ürün seçin</option>
                                @foreach ($urunler as $urun)
                                    <option value="{{ $urun->id }}">{{ $urun->urun_adi }}</option>
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
                    @if ($urunler->isNotEmpty())
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Ürün" name="urun" id="urun">
                            <option></option>
                            @foreach ($urunler as $urun)
                                <option value="{{ $urun->id }}">{{ $urun->urun_adi }}</option>
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

                    </div>
                    <!--end::Actions-->
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
                    <th>İsim</th>
                    <th>Telefon</th>
                    <th>Ürün</th>
                    <th>Durum</th>
                    <th style="min-width: 280px;" class="text-end">İşlem</th>
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
                        <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 lh-1">Ürünlere Göre Aktif Datalar</a>
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
                    @forelse ($urunStats as $urun)
                        <tr>
                            <td>{{ ozet($urun['adi'],40) }}</td>
                            <td><a href="#" class="btn btn-sm btn-icon btn-light-twitter btn-sm">{{ $urun['adet'] }}</a></td>
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

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                language: {
                    url: '{{ theme_asset('admin', 'plugins/custom/datatables/tr.json') }}',
                },
                ajax: {
                    url: '{{ route(config('system.personel_prefix').'.data.datatable') }}',
                    data: function (d) {
                        d.arama = $('#arama').val();
                        d.urun = $('#urun').val();
                        d.data_id = $('#data_id').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'isim', name: 'isim' },
                    { data: 'telefon', name: 'telefon' },
                    { data: 'urun', name: 'urun' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { targets: -1, width: '280px', className: 'text-end' } // son kolon
                ],
            });

            $('#arama').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#urun').on('change', function() {
                table.draw();
            });

            $('#data_id').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#arama').val('');
                $('#urun').val('').trigger('change');
                $('#data_id').val('');
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

        function data_widget() {
            var charts = document.querySelectorAll('.data-widget-chart');

            [].slice.call(charts).forEach(function (el) {
                if (!el) return;

                // Güvenli yükseklik (fallback 80)
                var height = parseInt(KTUtil.css(el, 'height'));
                if (isNaN(height) || height <= 0) height = 80;

                var color = el.getAttribute('data-kt-chart-color') || 'primary';
                var key = el.getAttribute('data-kt-chart-key') || 'toplam';

                var labelColor = KTUtil.getCssVariableValue('--bs-gray-800');
                var baseColor  = KTUtil.getCssVariableValue('--bs-' + color);
                var lightColor = KTUtil.getCssVariableValue('--bs-' + color + '-light');

                var chartData = (window.statisticsChartData || {})[key];

                // ÖNCE var mı kontrol et
                if (!chartData || !Array.isArray(chartData.data) || !Array.isArray(chartData.labels)) {
                    console.warn('Chart verisi geçersiz ya da bulunamadı:', key, chartData);
                    return;
                }

                // Orijinalleri bozmadan local kopya
                var seriesData = chartData.data.slice();
                var categories = chartData.labels.slice();

                var options = {
                    series: [{ name: 'Toplam', data: seriesData }],
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
                    stroke: { curve: 'smooth', show: true, width: 3, colors: [baseColor] },
                    xaxis: {
                        categories: categories,
                        labels: { show: true, style: { colors: labelColor, fontSize: '12px' } },
                        axisBorder: { show: false }, axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        labels: { show: false, style: { colors: labelColor, fontSize: '12px' } }
                    },
                    tooltip: { style: { fontSize: '12px' }, y: { formatter: val => val + ' data' } },
                    colors: [baseColor],
                    markers: { colors: [baseColor], strokeColor: [lightColor], strokeWidth: 3 }
                };

                var chart = new ApexCharts(el, options);
                chart.render();
            });
        }

        document.addEventListener('DOMContentLoaded', data_widget);
    </script>


    <script type="text/javascript">
        $('#ekleForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const modal = $('#ekleModal');
            const errorDiv = $('#ekleError');

            $.ajax({
                url: '{{ route(config('system.personel_prefix').".data.dataekle") }}',
                method: 'POST',
                data: form.serialize(),
                success: function (res) {
                    modal.modal('hide');
                    form[0].reset();
                    $('#datatable').DataTable().draw();

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

        $(document).ready(function () {
            $('[data-control="select2"]:not(#personelAtaModal [data-control="select2"])').select2();
        });


        $(function () {
            // Tooltip'i aktif et
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

    </script>


@endsection
