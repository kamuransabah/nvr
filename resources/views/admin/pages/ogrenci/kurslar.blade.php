@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Öğrenci Profil - '.$ogrenci->isim.' '.$ogrenci->soyisim)
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
            <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Öğrenci Profil</h1>
            <!--end::Title-->
            <!--begin::Separator-->
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <!--end::Separator-->
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-900">{{ $ogrenci->isim.' '.$ogrenci->soyisim }}</li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-1">
            <a href="{{ route(config('system.admin_prefix').'.ogrenci.index') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-bars"></i> Öğrenciler</a>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
@endsection

@section('content')

    <x-admin.ogrenci-navbar :ogrenci="$ogrenci" :personel="$personel" />

    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Öğrenci Kursları</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <a href="#" class="btn btn-sm btn-primary align-self-center">Yeni Ekle</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <div class="card-body p-9">
            @if($kurslar->count() > 0)
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="pl-7 m-width-200px">
                        <span class="text-dark-75">Kurs</span>
                    </th>
                    <th>Tarih</th>
                    <th>Sınav Tercihi <br> Sertifika Türü</th>
                    <th>Sözleşme</th>
                    <th class="min-w-50px">Durum</th>
                    <th class="min-w-100px">İşlem</th>
                </tr>
                </thead>
                <tbody>
                @foreach($kurslar as $data)
                    <tr>
                        <td>
                            <div class="d-flex w-100">
                                <!--begin::Thumbnail-->
                                <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url({{ asset('storage/' . config('upload.kurs.path') . '/' . $data->kurs->resim) }});"></span>
                                </a>
                                <!--end::Thumbnail-->

                                <div class="ms-5 w-100">
                                    <!--begin::Title-->
                                    <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold mb-1" data-kt-ecommerce-category-filter="category_name">{{ $data->kurs->kurs_adi }}</a>
                                    <!--end::Title-->

                                    <div class="progress h-7px bg-secondary bg-opacity-50 mt-7">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ kursLog($data->kurs_id, $ogrenci->id) }}%" aria-valuenow="{{ kursLog($data->kurs_id, $ogrenci->id) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td>{{ Carbon::parse($data->baslangic_tarihi)->format('d.m.Y') }}</td>
                        <td>
                            <span class="badge badge-light-info">{{ $data->sinavTercihi?->value }}</span>
                            <span class="badge badge-light-primary">{{ $data->sertifikaTuru?->value }}</span>
                        </td>
                        <td><span class="badge badge-{{ status()->get($data->sozlesme, 'class', 'ogrenci_sozlesme') }}">{{ status()->get($data->sozlesme, 'text', 'ogrenci_sozlesme') }}</span></td>
                        <td><span class="badge badge-{{ status()->get($data->durum) }}">{{ status()->get($data->durum, 'text') }}</span></td>
                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-icon btn-light-info me-1 btn-duzenle-kurs"
                                    data-id="{{ $data->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <form action="{{ route(config('system.admin_prefix').'.ogrenci.kurslar.sil', $data->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu kurs kaydını silmek istediğinizden emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <!-- kurs düzenle modal-->
            <div class="modal fade" id="duzenleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="duzenleForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="duzenle-user_id">
                        <input type="hidden" name="kurs_id" id="duzenle-kurs_id">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Kurs Bilgisi Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body row">
                                <div class="col-md-6 mb-3">
                                    <label>Sınav Hakkı</label>
                                    <input type="number" name="sinav_hakki" class="form-control" id="duzenle-sinav_hakki">
                                </div>
                                <div class="col-12 mb-3">
                                    <label>Kurs Bitiş Tarihi</label>
                                    <input type="date" name="tarih_bitis" class="form-control" id="duzenle-tarih_bitis">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Sertifika Türü</label>
                                    <select name="sertifika_turu" class="form-control" id="duzenle-sertifika_turu">
                                        @foreach($sertifikaTuru as $item)
                                        <option value="{{ $item->key }}">{{ $item->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Sınav Tercihi</label>
                                    <select name="sinav_tercihi" class="form-control" id="duzenle-sinav_tercihi">
                                        @foreach($sinavTercihi as $item)
                                            <option value="{{ $item->key }}">{{ $item->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Sözleşme</label>
                                    <select name="sozlesme" class="form-control" id="duzenle-sozlesme">
                                        <option value="0">İmzalanmadı</option>
                                        <option value="1">İmzalandı</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Durum</label>
                                    <select name="durum" class="form-control" id="duzenle-durum">
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Kaydet</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end:: Kurs Düzenle Modal-->
            @else
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Kurs Bulunamadı!</h4>
                            <div class="fs-6 text-gray-700">Bu öğrenciye ait kurs bulunmamaktadır.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!--end::details View-->
@endsection

@section('js')
    <script>
        document.querySelectorAll('.btn-duzenle-kurs').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                fetch("{{ route(config('system.admin_prefix').'.ogrenci.kurslar.getir', '___ID___') }}".replace('___ID___', id))
                    .then(res => res.json())
                    .then(data => {
                        const form = document.getElementById('duzenleForm');
                        form.action = `{{ url(config('system.admin_prefix') . '/ogrenci/kurslar/guncelle') }}/${id}`;


                        // inputları doldur
                        document.getElementById('duzenle-user_id').value = data.ogrenci_id;
                        document.getElementById('duzenle-kurs_id').value = data.kurs_id;
                        document.getElementById('duzenle-sinav_hakki').value = data.sinav_hakki ?? '';
                        document.getElementById('duzenle-tarih_bitis').value = data.tarih_bitis ?? '';
                        document.getElementById('duzenle-sertifika_turu').value = data.sertifika_turu ?? 0;
                        document.getElementById('duzenle-sinav_tercihi').value = data.sinav_tercihi ?? 1;
                        document.getElementById('duzenle-sozlesme').value = data.sozlesme ?? 0;
                        document.getElementById('duzenle-durum').value = data.durum ?? 1;

                        // modalı aç
                        const modal = new bootstrap.Modal(document.getElementById('duzenleModal'));
                        modal.show();
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Kurs verisi alınamadı. Lütfen tekrar deneyin.'
                        });
                    });
            });
        });
    </script>

@endsection
