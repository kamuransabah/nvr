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

    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <div class="card-header cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Belgeler</h3>
            </div>
            <a href="#" class="btn btn-sm btn-primary align-self-center" data-bs-toggle="modal" data-bs-target="#belgeEkleModal">Yeni Belge Ekle</a>
        </div>

        <div class="card-body p-9">
            @if($belgeler->count() > 0)
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>Belge Türü</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
                </thead>
                <tbody>
                @forelse($belgeler as $belge)
                    <tr>


                        <td>
                            <div class="d-flex align-items-center ">
                                <div class="symbol symbol-30px me-5">
                                <span class="symbol-label bg-light-info">
                                    <i class="{{ fileicon($belge->belge) }} text-info"></i>
                                </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="{{ asset('storage/' . config('upload.belge.path') . '/' . $belge->belge) }}" target="_blank"
                                       class="text-gray-900 text-hover-primary fs-6 fw-bold">{{ $belge->belgeTuru->value }}</a>
                                </div>
                            </div>
                        </td>


                        <td>{{ Carbon::parse($belge->created_at)->format('d.m.Y') }}</td>
                        <td><span class="badge badge-{{ status()->get($belge->belgeDurum->key, 'class', 'belge_durum') }}">{{ $belge->belgeDurum->value }}</span></td>
                        <td class="text-end">
                            <a href="{{ asset('storage/' . config('upload.belge.path') . '/' . $belge->belge) }}" target="_blank" class="btn btn-sm btn-icon btn-light-info" data-bs-toggle="tooltip" title="Önizleme">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if ($belge->durum != 2)
                            <form action="{{ route(config('system.admin_prefix').'.ogrenci.belgeler.onayla', $belge->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-icon btn-light-success" data-bs-toggle="tooltip" title="Onayla">
                                    <i class="fa-solid fa-circle-check"></i>
                                </button>
                            </form>
                            @endif
                            @if ($belge->durum != 3)
                            <form action="{{ route(config('system.admin_prefix').'.ogrenci.belgeler.iptal', $belge->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-icon btn-light-dark" data-bs-toggle="tooltip" title="İptal Et">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route(config('system.admin_prefix').'.ogrenci.belgeler.sil', $belge->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil" data-confirm-delete="true">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Bu öğrenciye ait belge bulunamadı.</td></tr>
                @endforelse
                </tbody>
            </table>

            @else
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Belge Bulunamadı!</h4>
                            <div class="fs-6 text-gray-700">Bu öğrenciye ait belge bulunmamaktadır.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="belgeEkleModal" tabindex="-1" aria-labelledby="belgeEkleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route(config('system.admin_prefix') . '.ogrenci.belgeler.ekle') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ $ogrenci->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="belgeEkleModalLabel">Yeni Belge Yükle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tur" class="form-label">Belge Türü</label>
                            <select name="tur" class="form-select" required>
                                @foreach ($belgeTurleri as $tur)
                                    <option value="{{ $loop->index + 1 }}">{{ $tur->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="belge" class="form-label">Dosya Yükle</label>
                            <input type="file" name="belge" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="aciklama" class="form-label">Açıklama</label>
                            <textarea name="aciklama" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')

@endsection
