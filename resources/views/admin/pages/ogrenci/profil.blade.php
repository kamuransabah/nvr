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
            <a href="{{ route(config('system.admin_prefix').'.ogrenci.add') }}" class="btn btn-sm btn-primary">Yeni Ekle</a>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
@endsection

@section('content')
    <!--begin::Navbar
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">

            <div class="d-flex flex-wrap flex-sm-nowrap">

                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-100px symbol-fixed position-relative">
                        <img src="{{ userAvatar($ogrenci->resim ?? null) }}" alt="avatar" />
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                    </div>
                </div>

                <div class="flex-grow-1">

                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">

                        <div class="d-flex flex-column">

                            <div class="d-flex align-items-center mb-2">
                                <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $ogrenci->isim.' '.$ogrenci->soyisim }}</a>
                                <a href="#">
                                    <i class="ki-outline ki-verify fs-1 text-{{ status()->get($ogrenci->durum) }}"></i>
                                </a>
                            </div>

                            <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                    <i class="ki-outline ki-profile-circle fs-4 me-1"></i>{{ $personel->isim.' '.$personel->soyisim }}</a>
                                <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                    <i class="ki-outline ki-geolocation fs-4 me-1"></i> {{ $ogrenci->il?->il }} / {{ $ogrenci->ilce?->ilce }}</a>
                                <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                    <i class="ki-outline ki-sms fs-4"></i>{{ $ogrenci->email }}</a>
                                <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                    <i class="ki-outline ki-phone fs-4"></i>{{ $ogrenci->telefon }}</a>
                            </div>

                        </div>

                        <div class="d-flex my-4">
                            <a href="#" class="btn btn-sm btn-outline btn-outline-primary btn-outline-dashed me-2">SMS</a>
                            <a href="#" class="btn btn-sm btn-outline btn-outline-warning btn-outline-dashed">E-Posta</a>
                        </div>

                    </div>

                </div>

            </div>


            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">

                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#"><i class="fas fa-user me-2"></i>Profil</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fas fa-book-open me-2"></i>Kurslar</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="far fa-credit-card me-2"></i>Finansal İşlemler</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-regular fa-file-lines me-2"></i>Belgeler</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-solid fa-check-to-slot me-2"></i>Sınavlar</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-solid fa-address-card me-2"></i>Profil Bilgileri</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-solid fa-user-pen me-2"></i>Notlar</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-solid fa-certificate me-2"></i>Sertifikalar</a>
                </li>


                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#"><i class="fa-solid fa-clock-rotate-left me-2"></i>Log Kayıtları</a>
                </li>

            </ul>

        </div>
    </div>
    end::Navbar-->
    <x-admin.ogrenci-navbar :ogrenci="$ogrenci" :personel="$personel" />

    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Profil Bilgileri</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <a href="{{ route(config('system.admin_prefix').'.ogrenci.edit', $ogrenci->id) }}" class="btn btn-sm btn-primary align-self-center">Profili Düzenle</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <div class="row">
                <div class="col-md-6 col-12">
                    <table class="table table-bordered table-row-dashed table-row-gray-300 gy-5">
                        <tbody>
                        <tr>
                            <td class="table-active">T.C. Kimlik No</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $ogrenci->tc_kimlik_no }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">E-Posta</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $ogrenci->email }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Telefon</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $ogrenci->telefon }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Adres</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $ogrenci->adres }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">İl / İlçe</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $ogrenci->il?->il.' '.$ogrenci->ilce?->ilce }}</span></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-12">
                    <table class="table table-bordered table-row-dashed table-row-gray-300 gy-5">
                        <tbody>
                        <tr>
                            <td class="table-active">Çağrı Merkezi</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ $personel->isim.' '.$personel->soyisim }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Kayıt Tarihi</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ Carbon::parse($ogrenci->created_at)->format('d.m.Y H:i') }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Doğum Tarihi</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ Carbon::parse($ogrenci->dogum_tarihi)->translatedFormat('d F Y') }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Kaynak</td>
                            <td><span class="badge badge-light-{{ status()->get($ogrenci->kayitKaynak?->key, 'class', 'ogrenci_kaynak') }} fs-6 fw-bold">{{ $ogrenci->kayitKaynak?->value }}</span></td>
                        </tr>
                        <tr>
                            <td class="table-active">Son Giriş Tarihi</td>
                            <td><span class="fw-bold fs-6 text-gray-800">{{ Carbon::parse($ogrenci->son_giris_tarihi)->format('d.m.Y H:i') }}</span></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!--begin::Notice-->
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                <!--begin::Icon-->
                <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                <!--end::Icon-->
                <!--begin::Wrapper-->
                <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold">Öğrenci Bilgi Mesajı!</h4>
                        <div class="fs-6 text-gray-700">Öğrenci ile alakalı bilgi mesajını buradan vereiliriz.</div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Notice-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::details View-->
@endsection

@section('js')


    <script src="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.js') }}"></script>


@endsection
