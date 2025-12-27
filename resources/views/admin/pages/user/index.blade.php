@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Kullanıcılar')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Kullanıcılar</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Kullanıcı Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <button type="button" id="btnAddUser" class="btn btn-sm btn-primary"><i class="bi bi-person-add fs-4 me-2"></i> Yeni Kullanıcı Ekle</button>
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
                    <select id="yetki" name="yetki" class="form-select form-select-solid" data-control="select2" data-placeholder="Yetki Seçiniz">
                        <option value="">Tümü</option>
                        <option value="none">Yetkisi olmayanlar</option>
                        @foreach($roles as $role_id => $role_name)
                            <option value="{{ $role_name }}">{{ ucfirst($role_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-100 mw-100px">
                    <select class="form-select form-select-solid" id="durum" data-placeholder="Durum">
                        <option value="">Tümü</option>
                        <option value="1" selected>Aktif</option>
                        <option value="0">Pasif</option>
                    </select>
                </div>
                <div class="w-100 mw-250px">
                    <input type="text" id="tarih_filtre" class="form-control form-control-solid" placeholder="Kayıt Tarihi" />
                </div>
                <div class="filter-button">
                    <div class="d-flex align-items-center py-1">
                        <a href="#" class="btn btn-sm btn-icon btn-secondary me-2" id="reset"  data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" title="Sıfırla"><i class="fa-solid fa-arrows-rotate"></i></a>
                    </div>
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
                    <th>Yetki</th>
                    <th>Kayıt Tarihi</th>
                    <th>Durum</th>
                    <th class="min-w-100px">İşlem</th>
                </tr>
                </thead>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-body p-0" id="userModalContent">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')


    <script src="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

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
                    url: '{{ route(config('system.admin_prefix').'.user.data') }}',
                    data: function (d) {
                        d.arama = $('#arama').val();
                        d.name = $('#name').val();
                        d.yetki = $('#yetki').val();
                        d.tarih_filtre = $('#tarih_filtre').val();
                        d.durum = $('#durum').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'isim', name: 'isim' },
                    { data: 'yetki', name: 'yetki' },
                    { data: 'tarih', name: 'tarih' },
                    { data: 'durum', name: 'durum', orderable: false, searchable: false },
                    { data: 'islem', name: 'islem', orderable: false, searchable: false }
                ],
                drawCallback: function(settings) {
                    // Dinamik yüklenen elementler için tooltip init
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            $('#arama').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    table.draw();
                }
            });

            $('#yetki').on('change', function() {
                table.draw();
            });

            $('#durum').on('change', function() {
                table.draw();
            });

            $('#reset').on('click', function() {
                $('#arama').val('');
                $('#yetki').val('');
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


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal   = $('#userModal');
            const content = $('#userModalContent');

            // (Opsiyonel) DataTables kullanıyorsan tablo referansını al:
            const table = window.usersTable || null; // yoksa null bırak

            // 3.1 – Ekle: modal formu backend'den al
            $('#btnAddUser').on('click', function () {
                content.html('<div class="p-5 text-center">Yükleniyor...</div>');
                modal.modal('show');
                $.get('{{ route(config("system.admin_prefix").".user.modal.add") }}', function (html) {
                    content.html(html);
                    initModalJs();
                });
            });

            // 3.2 – Düzenle: modal formu backend'den al
            $(document).on('click', '.js-edit', function () {
                const id = $(this).data('id');
                content.html('<div class="p-5 text-center">Yükleniyor...</div>');
                modal.modal('show');
                $.get('{{ route(config("system.admin_prefix").".user.modal.edit", ":id") }}'.replace(':id', id), function (html) {
                    content.html(html);
                    initModalJs();
                });
            });

            // 3.3 – Arşivle
            $(document).on('click', '.js-archive', function () {
                const id = $(this).data('id');
                if (!confirm('Bu kullanıcı arşivlenecek. Devam edilsin mi?')) return;

                $.ajax({
                    url: '{{ route(config("system.admin_prefix").".user.delete", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function (resp) {
                        toast('success', resp?.message || 'Kullanıcı arşivlendi.');
                        if (table?.ajax) table.ajax.reload(null, false);
                        else location.reload(); // DataTables yoksa sayfayı yenile
                    },
                    error: ajaxError
                });
            });

            // 3.4 – Geri Yükle
            $(document).on('click', '.js-restore', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: '{{ route(config("system.admin_prefix").".user.restore", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: { _method: 'PATCH', _token: '{{ csrf_token() }}' },
                    success: function (resp) {
                        toast('success', resp?.message || 'Kullanıcı geri yüklendi.');
                        if (table?.ajax) table.ajax.reload(null, false);
                        else location.reload();
                    },
                    error: ajaxError
                });
            });

            // initModalJs içinde submit handler'ı BÖYLE olsun
            function initModalJs() {
                // select2 (varsa)
                if (window.$ && $.fn.select2) {
                    $('[data-control="select2"]').select2({ width: '100%', dropdownParent: $('#userModal') });
                }

                $('#userForm').off('submit').on('submit', function (e) {
                    e.preventDefault();

                    const $form  = $(this);
                    const action = $form.attr('action');

                    // FormData: dosya + diğer alanlar
                    const fd = new FormData($form[0]);

                    // (Güvenlik) _method yoksa (create) eklemeyiz; edit'te Blade zaten ekliyor.
                    // _token de formda @csrf ile zaten var; yine de yoksa ekleyebiliriz:
                    if (!fd.has('_token')) fd.append('_token', '{{ csrf_token() }}');

                    // Hata kutusunu temizle
                    $('#formErrors').addClass('d-none').empty();

                    $.ajax({
                        url: action,
                        type: 'POST',              // <-- HER ZAMAN POST
                        data: fd,
                        processData: false,        // <-- FormData için şart
                        contentType: false,        // <-- FormData için şart
                        success: function (resp) {
                            // Toast / SweetAlert
                            if (window.Swal) {
                                Swal.fire({ icon:'success', title: resp?.message || 'İşlem başarılı.', timer: 1500, showConfirmButton: false });
                            }
                            // Modal kapat + tablo yenile (varsa)
                            $('#userModal').modal('hide');
                            if (window.usersTable?.ajax) window.usersTable.ajax.reload(null, false);
                            else location.reload();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errs = xhr.responseJSON?.errors || {};
                                let html = '<div><strong>Formda hatalar var:</strong></div><ul class="mb-0 mt-1">';
                                Object.keys(errs).forEach(k => errs[k].forEach(m => html += `<li>${m}</li>`));
                                html += '</ul>';
                                $('#formErrors').removeClass('d-none').html(html);
                            } else {
                                const msg = xhr?.responseJSON?.message || 'İşlem başarısız.';
                                if (window.Swal) Swal.fire({ icon:'error', title: msg });
                                else alert(msg);
                            }
                        }
                    });
                });
            }


            // 3.6 – Yardımcılar
            function toast(type, message) {
                if (window.Swal) {
                    Swal.fire({ icon: type || 'info', title: message || '', timer: 1500, showConfirmButton: false });
                } else {
                    alert(message || '');
                }
            }
            function ajaxError(xhr) {
                const msg = xhr?.responseJSON?.message || 'İşlem başarısız.';
                toast('error', msg);
            }
        });
    </script>


@endsection
