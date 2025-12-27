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
                <h3 class="fw-bold m-0">Siparişler</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <a href="#" class="btn btn-sm btn-primary align-self-center">Yeni Satış Ekle</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <div class="card-body p-9">
            @if($siparisler->count() > 0)
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>Sipariş No</th>
                    <th>Ürünler</th>
                    <th>Ödenecek Tutar</th>
                    <th>Ödeme Durumu</th>
                    <th>Ödeme Tarihi</th>
                    <th>Sipariş Durum</th>
                    <th>İşlem</th>
                </tr>
                </thead>
                <tbody>
                @forelse($siparisler as $siparis)
                    <tr>
                        <td><span class="badge badge-secondary badge-square badge-lg px-2">{{ $siparis->siparis_no }}</span></td>
                        <td>
                            @foreach($siparis->urunListesi() as $urun)
                            <div class="d-flex align-items-center">
                                <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url({{ $urun['resim'] }});"></span>
                                </a>
                                <div class="ms-5">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{ $urun['baslik'] }}</a>
                                </div>
                            </div>
                            @endforeach
                        </td>
                        <td><span class="text-gray-800 text-hover-primary fs-5 fw-bold">{{ number_format($siparis->odenecek_tutar, 2) }} TL</span></td>
                        <td><span class="badge badge-{{ status()->get($siparis->odemeDurum->key, 'class', 'odeme_durum') }}">{{ $siparis->odemeDurum->value }}</span></td>
                        <td>{{ $siparis->odeme_tarihi ? Carbon::parse($siparis->odeme_tarihi)->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge badge-secondary">{{ $siparis->siparisDurum->value }}</span></td>
                        <td class="text-end">
                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">İşlem
                                <i class="ki-outline ki-down fs-5 ms-1"></i></a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Detay</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Düzenle</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Sil</a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Bu öğrenciye ait sipariş bulunamadı.</td></tr>
                @endforelse
                </tbody>
            </table>

            @else
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Sipariş Bulunamadı!</h4>
                            <div class="fs-6 text-gray-700">Bu öğrenciye ait sipariş bulunmamaktadır.</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!--end::details View-->
@endsection

@section('js')

@endsection
