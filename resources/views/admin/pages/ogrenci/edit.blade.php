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
            <a href="{{ route(config('system.admin_prefix').'.ogrenci.index') }}" class="btn btn-sm btn-primary">Öğrenciler</a>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
@endsection

@section('content')
    <x-admin.ogrenci-navbar :ogrenci="$ogrenci" :personel="$personel" />

    <!-- start:edit-profile -->
    <!--begin::Basic info-->
    <div class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Profil Bilgilerini Düzenle</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card header-->
        <!--begin::Content-->
        <div id="kt_account_settings_profile_details" class="collapse show">
            <form action="{{ route(config('system.admin_prefix').'.ogrenci.update', $profil->id) }}" class="form" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body border-top p-9">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Üyelik Resmi</label>
                        <div class="col-lg-8">
                            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('{{ userAvatar($profil->profil_resmi ?? null) }}')">
                                <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ userAvatar($profil->profil_resmi ?? null) }})"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Resmi Değiştir">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <input type="file" name="profil_resmi" />
                                    <input type="hidden" name="avatar_remove" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="İptal">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Resmi Sil">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                            </div>
                            <div class="form-text">Geçerli dosya türleri: png, jpg, jpeg.</div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">İsim Soyisim</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row">
                                    <input type="text" name="isim" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ old('isim', $profil->isim ?? '') }}" />
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <input type="text" name="soyisim" class="form-control form-control-lg form-control-solid" value="{{ old('soyisim', $profil->soyisim ?? '') }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">E-Posta</label>
                        <div class="col-lg-8 fv-row">
                            <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="{{ old('email', $profil->email ?? '') }}" />
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            <span class="required">Telefon Numarası</span>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <input type="tel" name="telefon" class="form-control form-control-lg form-control-solid" value="{{ old('telefon', $profil->telefon ?? '') }}" />
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">T.C. Kimlik No</label>
                        <div class="col-lg-8 fv-row">
                            <input type="number" name="tc_kimlik_no" class="form-control form-control-lg form-control-solid" value="{{ old('tc_kimlik_no', $profil->tc_kimlik_no ?? '') }}" />
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            <span>Mezuniyet / Meslek</span>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <div class="row">
                                <div class="col-lg-6 fv-row">
                                    <select name="mezuniyet" class="form-select form-select-solid form-select-lg fw-semibold">
                                        @foreach($mezuniyet as $mezun)
                                            <option value="{{ $mezun->key }}" {{ old('mezuniyet', $profil->mezuniyet ?? 0) == $mezun->id ? 'selected' : '' }}>
                                                {{ $mezun->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <input type="text" name="meslek" class="form-control form-control-lg form-control-solid" value="{{ old('meslek', $profil->meslek ?? '') }}" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            <span>Doğum Tarihi / Cinsiyet</span>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <div class="row">
                                <div class="col-lg-6 fv-row">
                                    <input type="date" name="dogum_tarihi" class="form-control form-control-lg form-control-solid" id="dogum_tarihi" value="{{ old('dogum_tarihi', $profil->dogum_tarihi ?? '') }}">
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <select name="cinsiyet" class="form-select form-select-solid form-select-lg fw-semibold">
                                        @foreach($cinsiyet as $item)
                                            <option value="{{ $item->key }}" {{ old('cinsiyet', $profil->cinsiyet ?? 0) == $item->id ? 'selected' : '' }}>
                                                {{ $item->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            <span>İl / İlçe</span>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <div class="row">
                                <div class="col-lg-6 fv-row">
                                    <input type="hidden" id="il-selected" value="{{ $profil->il_id ?? '' }}">
                                    <select name="il_id" id="il" class="form-select form-select-solid form-select-lg fw-semibold">
                                        <option value="">İl Seçiniz</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <input type="hidden" id="ilce-selected" value="{{ $profil->ilce_id ?? '' }}">
                                    <select name="ilce_id" id="ilce" class="form-select form-select-solid form-select-lg fw-semibold">
                                        <option value="">İlçe Seçiniz</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            <span>Adres</span>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="adres" class="form-control form-control-lg form-control-solid" rows="3">{{ old('adres', $profil->adres ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Üyelik Durumu</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum" {{ $profil->durum == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route(config('system.admin_prefix').'.ogrenci.profil', ['id' => $profil->id]) }}" class="btn btn-light btn-light-danger me-2">İptal</a>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Giriş Bilgileri</h3>
            </div>
        </div>
        <div id="kt_account_settings_signin_method" class="collapse show">
            <div class="card-body border-top p-9">
                <div class="d-flex flex-wrap align-items-center">
                    <!--begin::Label-->
                    <div id="kt_signin_email">
                        <div class="fs-6 fw-bold mb-1">E-Posta Adresi</div>
                        <div class="fw-semibold text-gray-600">{{ $profil->email }}</div>
                    </div>
                </div>
                <div class="separator separator-dashed my-6"></div>
                <div class="d-flex flex-wrap align-items-center mb-10">
                    <div id="kt_signin_password">
                        <div class="fs-6 fw-bold mb-1">Şifre</div>
                        <div class="fw-semibold text-gray-600">************</div>
                    </div>

                    <div class="ms-auto">
                        <span class="fs-6 fw-bold me-2">Şifre Gönder</span>
                        <button class="btn btn-icon btn-light btn-light-primary"><i class="fa-solid fa-comment-sms fs-1"></i></button>
                        <button class="btn btn-icon btn-light btn-light-info"><i class="fa-solid fa-envelope fs-1"></i></button>
                    </div>
                </div>
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                    <i class="ki-outline ki-shield-tick fs-2tx text-primary me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                        <div class="mb-3 mb-md-0 fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Kullanıcı Şifresi Güvenlik Nedeniyle İşlenemez</h4>
                            <div class="fs-6 text-gray-700 pe-7">Kullanıcı şifreleri güvenlik nedeniyle şifrelenmiş olarak saklanır ve görüntülenemez. Sistem politikası gereği şifre oluşturamaz veya değiştiremezsiniz.
                                <br>
                                Dilerseniz kullanıcıya e-posta veya SMS yoluyla şifre yenileme bağlantısı gönderebilirsiniz.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_deactivate" aria-expanded="true" aria-controls="kt_account_deactivate">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Hesabı Silin</h3>
            </div>
        </div>
        <div id="kt_account_settings_deactivate" class="collapse show">
            <form id="kt_account_deactivate_form" class="form">
                <div class="card-body border-top p-9">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                        <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Bu işlemin geri dönüşü yoktur.</h4>
                                <div class="fs-6 text-gray-700"> Hesabı sildiğinizde kullanıcı verileri sistemden tamamen silinir. İşlem yapmadan önce aşağıdaki bilgileri kontrol edin.
                                    <br />
                                    <br />
                                    Bu fonksiyon şuan aktif değildir. Üyelik silme işlemleri BETA sürecinden sonra aktif edilecektir.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-check-solid fv-row">
                        <input name="deactivate" class="form-check-input" type="checkbox" value="" id="deactivate" />
                        <label class="form-check-label fw-semibold ps-2 fs-6" for="deactivate">Bu işlemin yapılmasını onaylıyorum.</label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-danger fw-semibold">Hesabı Sil</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')


    <script src="{{ asset('common/js/adres-select.js') }}"></script>
    <script src="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.js') }}"></script>


@endsection
