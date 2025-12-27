@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Öğrenciler')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Öğrenciler</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Öğrenci Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <a href="{{ route(config('system.admin_prefix').'.ogrenci.add') }}" class="btn btn-sm btn-primary">Yeni Ekle</a>
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
                    @if ($personeller->isNotEmpty())
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Personel" name="personel_id" id="personel_id">
                            <option></option>
                            @foreach ($personeller as $personel)
                                <option value="{{ $personel->id }}">{{ $personel->isim. ' '.$personel->soyisim }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="w-100 mw-250px">
                    <input type="text" id="tarih_filtre" class="form-control form-control-solid" placeholder="Kayıt Tarihi" />
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
                                    <div class="fs-5 text-gray-900 fw-bold">Öğrenci Filtreleme</div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Menu separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Menu separator-->
                                <!--begin::Form-->
                                <div class="px-7 py-5">
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold" for="ogrenci_id">Öğrenci ID:</label>
                                        <div>
                                            <input type="text" class="form-control form-select-solid" name="ogrenci_id" id="ogrenci_id">
                                        </div>
                                    </div>
                                    <div class="mb-10">
                                        <label class="form-label" for="tc_no">T.C. Kimlik No</label>
                                        <input type="text" class="form-control" name="tc_no" id="tc_no" placeholder="T.C. Kimlik No">
                                    </div>
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold">Durum:</label>
                                        <div>
                                            <select class="form-select form-select-solid" id="durum" data-placeholder="Data Durum">
                                                <option value="">Tümü</option>
                                                <option value="1">Aktif</option>
                                                <option value="0">Pasif</option>
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
                                <form id="exportForm" method="POST" action="{{ route(config('system.admin_prefix').'.ogrenci.export') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Öğrenci Export</h5>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="isim" value="{{ request('isim') }}">
                                        <input type="hidden" name="personel_id" value="{{ request('personel_id') }}">
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
                                                        <input class="form-check-input" type="checkbox" name="fields[]" value="email" checked id="export_eposta" />
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
                                                        <input class="form-check-input" type="checkbox" name="fields[]" value="tc_kimlik_no" checked id="export_tc_no" />
                                                        <label class="form-check-label" for="export_telefon">T.C. Kimlik No</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="fields[]" value="il_id" checked id="export_sehir" />
                                                        <label class="form-check-label" for="export_sehir">Şehir</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="fields[]" value="personel_id" checked id="export_personel" />
                                                        <label class="form-check-label" for="export_personel">Personel</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="fields[]" value="created_at" checked id="export_tarih" />
                                                        <label class="form-check-label" for="export_tarih">Kayıt Tarihi</label>
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
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="data-table">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>İsim Soyisim</th>
                    <th>Telefon</th>
                    <th>Üyelik Tarihi</th>
                    <th>Çağrı Merkezi</th>
                    <th>Durum</th>
                    <th class="min-w-100px">İşlem</th>
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

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                language: {
                    url: '{{ theme_asset('admin', 'plugins/custom/datatables/tr.json') }}',
                },
                ajax: {
                    url: '{{ route(config('system.admin_prefix').'.ogrenci.data') }}',
                    data: function (d) {
                        d.arama = $('#arama').val();
                        d.tc_no = $('#tc_no').val();
                        d.personel_id = $('#personel_id').val();
                        d.ogrenci_id = $('#ogrenci_id').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'isim', name: 'isim' },
                    { data: 'telefon', name: 'telefon' },
                    { data: 'tarih', name: 'tarih' },
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

            $('#tc_no').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#ogrenci_id').on('change', function() {
                table.draw();
            });

            $('#personel_id').on('change', function() {
                table.draw();
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#arama').val('');
                $('#tc_no').val('');
                $('#personel_id').val('');
                $('#tarih_filtre').val('');
                $('#durum').val('');
                table.draw();
            });

            $('#tarih_filtre').on('apply.daterangepicker cancel.daterangepicker', function() {
                table.draw();
            });
        });
    </script>

    <script>
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
            const table = $('#data-table').DataTable();

            const filters = {
                arama: $('#arama').val(),
                tc_no: $('#tc_no').val(),
                personel_id: $('#personel_id').val(),
                durum: $('#durum').val(),
                tarih_filtre: $('#tarih_filtre').val(),
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
