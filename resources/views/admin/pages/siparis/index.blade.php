@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Siparişler')
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
@endsection

@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Siparişler</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <li class="breadcrumb-item text-gray-900">Sipariş Listesi</li>
                </ul>
            </div>
        </div>
    </div>
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
                    <input type="text" id="siparis_no" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Sipariş No">
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
                    <input type="text" id="tarih_filtre" class="form-control form-control-solid" placeholder="Sipariş Tarihi" />
                </div>
                <div class="w-100 mw-250px">
                    <select class="form-select form-select-solid" id="durum" data-placeholder="Sipariş Durum">
                        <option value="">Tümü</option>
                        @foreach($siparisDurumlari as $item)
                            <option value="{{ $item->key }}">{{ $item->value }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="#" class="btn btn-sm btn-icon btn-secondary me-2" id="reset"  data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" title="Sıfırla"><i class="fa-solid fa-arrows-rotate"></i></a>
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
                    <th>Sipariş No</th>
                    <th>Üye</th>
                    <th>Tutar</th>
                    <th>Sipariş Tarihi</th>
                    <th>Personel</th>
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
                    url: '{{ route(config('system.admin_prefix').'.siparis.data') }}',
                    data: function (d) {
                        d.siparis_no = $('#siparis_no').val();
                        d.personel_id = $('#personel_id').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'siparis_no', name: 'siparis_no' },
                    { data: 'user', name: 'user' },
                    { data: 'tutar', name: 'tutar' },
                    { data: 'tarih', name: 'tarih' },
                    { data: 'personel', name: 'personel' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ]
            });

            $('#siparis_no').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#personel_id').on('change', function() {
                table.draw();
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#siparis_no').val('');
                $('#personel_id').val('');
                $('#tarih_filtre').val('');
                $('#durum').val('');
                table.draw();
            });

            $('#tarih_filtre').on('apply.daterangepicker cancel.daterangepicker', function() {
                table.draw();
            });

            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

@endsection
