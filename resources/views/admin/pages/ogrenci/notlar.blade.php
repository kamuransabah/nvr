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
                <h3 class="fw-bold m-0">Öğrenci Notları</h3>
            </div>
            <a href="#" class="btn btn-sm btn-primary align-self-center" data-bs-toggle="modal" data-bs-target="#notEkleModal">Yeni Not Ekle</a>
        </div>

        <div class="card-body p-9">
            @if($notlar->count() > 0)
            <div class="mb-10">
                @foreach($notlar as $not)
                    <div class="d-flex mb-10 position-relative">
                        <i class="ki-duotone ki-file-added fs-2x me-5 ms-n1 mt-2 text-success">
                            <span class="path1"></span><span class="path2"></span>
                        </i>

                        <div class="d-flex flex-column p-3 bg-light-secondary w-100 rounded">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="me-3">
                                    <span class="text-gray-900 fs-4 fw-semibold">{!! nl2br(e($not->icerik)) !!}</span><br>
                                    <span class="text-muted fw-semibold fs-6">
                                        {{ $not->personel->isim.' '.$not->personel->soyisim }} tarafından oluşturuldu
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-light-primary mb-2">
                                        {{ Carbon::parse($not->created_at)->format('d.m.Y H:i') }}
                                    </span>

                                    <!-- Silme Formu -->
                                    <form action="{{ route(config('system.admin_prefix').'.ogrenci.notlar.sil', $not->id) }}" method="POST" onsubmit="return confirm('Bu notu silmek istediğinizden emin misiniz?')" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Sil" data-confirm-delete="true">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            @else
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Not Bulunamadı!</h4>
                            <div class="fs-6 text-gray-700">Bu öğrenciye ait personel notu bulunamadı.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="notEkleModal" tabindex="-1" aria-labelledby="notEkleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route(config('system.admin_prefix').'.ogrenci.notlar.ekle') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $ogrenci->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notEkleModalLabel">Yeni Not Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="icerik" class="form-label">Not İçeriği</label>
                            <textarea name="icerik" id="icerik" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('js')

@endsection
