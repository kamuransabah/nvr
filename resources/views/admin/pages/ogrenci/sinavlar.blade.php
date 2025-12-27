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
                <h3 class="fw-bold m-0">Sınavlar</h3>
            </div>
            <a href="#" class="btn btn-sm btn-primary align-self-center" data-bs-toggle="modal" data-bs-target="#EkleModal">Yeni Sınav Ekle</a>
        </div>

        <div class="card-body p-9">
            @if($sinavlar->count() > 0)
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>Sınav</th>
                    <th>Kurs</th>
                    <th>Tarih</th>
                    <th>Sınav Türü</th>
                    <th>Doğru/Yalnış/Boş</th>
                    <th>Puan</th>
                    <th class="text-end">İşlem</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sinavlar as $item)
                    <tr>
                        <td>{{ $item->sinav->sinav_adi }}</td>
                        <td>{{ $item->kurs->kurs_adi }}</td>
                        <td>{{ Carbon::parse($item->created_at)->format('d.m.Y H:i') }}</td>
                        <td><span class="badge badge-{{ status()->get($item->sinav->sinavTuru->key, 'class', 'sinav_turu') }}">{{ $item->sinav->sinavTuru->value }}</span></td>
                        <td>
                            <span class="badge badge-success badge-square badge-lg">{{ $item->dogru_cevap }}</span>
                            <span class="badge badge-danger badge-square badge-lg">{{ $item->yalnis_cevap }}</span>
                            <span class="badge badge-warning badge-square badge-lg">{{ $item->bos_cevap }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->puan >= $item->kurs->gecme_notu ? 'success' : 'danger' }} badge-square badge-lg">{{ $item->puan }}</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route(config('system.admin_prefix').'.ogrenci.sinavlar.sil', $item->id) }}" method="POST" style="display: inline-block;">
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

@endsection

@section('js')

@endsection
