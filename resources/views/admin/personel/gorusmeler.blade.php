@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Data Görüşme Listesi')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Data Görüşmeler</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Data Görüşme Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Menu wrapper-->
                <div class="me-4">
                    <a href="{{ route(config('system.personel_prefix').'.data.index') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-list me-2"></i>Data Listesi</a>
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

            <table class="table table-row-bordered align-middle gy-5 gs-7" id="datatable">
                <thead>
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th>Data ID</th>
                    <th>İsim</th>
                    <th>Telefon</th>
                    <th>Ürün</th>
                    <th>Durum</th>
                    <th style="min-width: 220px;" class="text-end">Tarih</th>
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

        function initDTPopovers(ctx) {
            (ctx || document).querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                const inst = bootstrap.Popover.getInstance(el);
                if (inst) inst.dispose();
                bootstrap.Popover.getOrCreateInstance(el);
            });
        }

        $(document).ready(function () {

            // 1) DT'yi önce kur
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                language: {
                    url: '{{ theme_asset('admin', 'plugins/custom/datatables/tr.json') }}',
                },
                ajax: {
                    url: '{{ route(config('system.personel_prefix').'.data.gorusmelerdatatable') }}',
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
                    { targets: 0, width: '120px', className: 'text-start' }, // 1. sütun (ID)
                    { targets: -1, width: '220px', className: 'text-end' }   // son sütun (İşlem)
                ],
                // Popover'ları her çizimde yeniden bağla
                drawCallback: function () { initDTPopovers(document); }
            });

            // 2) Popover init helper
            function initDTPopovers(ctx) {
                (ctx || document).querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                    var inst = bootstrap.Popover.getInstance(el);
                    if (inst) inst.dispose();
                    bootstrap.Popover.getOrCreateInstance(el); // data-* ile ayarlar zaten HTML'de
                });
            }

            // Responsive child row açıldığında da bağla (opsiyonel ama iyi olur)
            $('#datatable')
                .on('responsive-display.dt', function () { initDTPopovers(document); })
                .on('draw.dt', function () { initDTPopovers(document); });

            // 3) Daterangepicker'i şimdi kur (DT kurulduktan sonra)
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

            $('#tarih_filtre').on('apply.daterangepicker', function (ev, picker) {
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate   = picker.endDate.format('YYYY-MM-DD');
                $(this).val(startDate + ' - ' + endDate);
                table.draw();
            });

            $('#tarih_filtre').on('cancel.daterangepicker', function () {
                $(this).val('');
                table.draw();
            });

            // 4) Diğer filtre bağları (DT zaten var)
            $('#arama').on('keyup', function () {
                if (this.value.length >= 3 || this.value.length === 0) table.draw();
            });

            $('#urun').on('change', function () { table.draw(); });

            $('#data_id').on('keyup', function () {
                if (this.value.length >= 3 || this.value.length === 0) table.draw();
            });

            $('#durum').on('change', function () { table.draw(); });

            $('#reset').on('click', function () {
                $('#arama').val('');
                $('#urun').val('').trigger('change');
                $('#data_id').val('');
                $('#tarih_filtre').val('');
                $('#durum').val('');
                $('select[data-control="select2"]').val('').trigger('change');
                table.draw();
            });

        });




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
