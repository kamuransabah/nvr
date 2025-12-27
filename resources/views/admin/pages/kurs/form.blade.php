@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Kurs Yönetimi')
@section('css')

@endsection

@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Kurs</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ isset($kurs) ? 'Kurs Düzenle' : 'Yeni Kurs Ekle' }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.kurs.index') }}" class="btn btn-sm btn-primary">Kurs Listesi</a>
                <!--end::Button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>

@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <!--begin::Alert-->
        <div class="alert alert-danger d-flex align-items-center p-5">
            <!--begin::Icon-->
            <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column">
                <!--begin::Title-->
                <h4 class="mb-1 text-dark">Hata: İşlem Başarısız</h4>
                <!--end::Title-->

                <ul  class="list-unstyled my-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <!--end::Wrapper-->
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-light"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
        <!--end::Alert-->
    @endif

    <form action="{{ ($kurs->exists)
    ? route(config('system.admin_prefix').'.kurs.update', $kurs->id)
    : route(config('system.admin_prefix').'.kurs.store') }}"
          method="POST" enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
        @csrf
        @if($kurs->exists)
            @method('PUT')
        @endif
        <!--begin::Aside column-->
        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
            <!--begin::Thumbnail settings-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Resim</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body text-center pt-0">
                    <div class="mb-10">

                        @if(isset($kurs) && $kurs->resim)
                            <div class="mb-3" id="image-container">
                                <img src="{{ asset('storage/'.config('upload.kurs.path') . '/' . $kurs->resim) }}"
                                     alt="Resim" class="img-thumbnail" width="250">
                                <button type="button" class="btn btn-danger btn-sm mt-2"
                                        id="deleteImage"
                                        data-img-delete="true">
                                    Resmi Sil
                                </button>
                            </div>
                        @else
                            <div class="mb-3" id="image-container">
                                <img src="{{ theme_asset('admin', 'media/svg/files/blank-image.svg') }}"
                                     alt="Resim" class="img-thumbnail" width="250">
                            </div>
                        @endif
                        <input type="file" name="resim" id="resim" class="form-control @error('resim') is-invalid @enderror" value="{{ old('resim', $kurs->resim ?? '') }}" />
                        @error('resim')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror


                        <!-- Resim Silme İşlemi İçin Gizli Input -->
                        <input type="hidden" name="delete_resim" id="delete_resim" value="0">
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Thumbnail settings-->
            <!--begin::Status-->
            @php
                $durumVal = (int) old('durum', $kurs->durum ?? 0); // ekle=0, düzenle=model
                $kitapDestegiVal = (int) old('kitap_destegi', $kurs->kitap_destegi ?? 0); // ekle=0, düzenle=model
            @endphp

            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title"><h2>Durum</h2></div>
                    <div class="card-toolbar">
                        <div class="rounded-circle bg-{{ status()->get($durumVal) }} w-15px h-15px"></div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <select class="form-select" name="durum" data-control="select2" data-hide-search="true">
                        @foreach([0,1] as $v)
                            <option value="{{ $v }}" @selected($durumVal === $v)>
                                {{ status()->get($v, 'text') }}
                            </option>
                        @endforeach
                    </select>
                    @error('durum')<div class="text-danger fs-7">{{ $message }}</div>@enderror
                </div>
            </div>

            <!--end::Status-->
            <!--begin::Category & tags-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Kategori</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <label class="form-label">Kategoriler</label>
                    <select class="form-select @error('kategori_id') is-invalid @enderror"  id="kategori_id" name="kategori_id" required>
                        <option>Seçiniz</option>
                        @foreach($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $kurs->kategori_id ?? 0) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->isim }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <a href="{{ route(config('system.admin_prefix').'.kategori.index', ['tur' => 'kurs']) }}" class="btn btn-light-primary btn-sm my-5">
                        <i class="ki-outline ki-plus fs-2"></i>Yeni Kategori Ekle
                    </a>

                    <label class="form-label d-block">Etiket</label>
                    <input type="text" name="label" class="form-control" value="{{ old('label', $kurs->label ?? '') }}">

                </div>
                <!--end::Card body-->
            </div>
            <!--end::Category & tags-->
            <!--begin::Fiyat Bilgisi-->
            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Fiyat Bilgisi</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    {{-- Ücretsiz mi? --}}
                    <div class="mb-5">
                        <label class="form-label">Ücretsiz mi?</label>
                        @php $ucretsiz = old('ucretsiz', $kurs->ucretsiz ?? 'H'); @endphp
                        <select name="ucretsiz" id="ucretsizSelect" class="form-select form-select-solid">
                            <option value="H" @selected($ucretsiz==='H')>Hayır</option>
                            <option value="E" @selected($ucretsiz==='E')>Evet</option>
                        </select>
                    </div>

                    {{-- Fiyat & KDV --}}
                    <div id="fiyatKdvFields" style="{{ $ucretsiz==='E' ? 'display:none' : '' }}">
                        <div class="row">
                            <div class="col-6 mb-5">
                                <label class="form-label">Fiyat</label>
                                <input type="number" name="fiyat" class="form-control form-control-solid"
                                       value="{{ old('fiyat', $kurs->fiyat ?? '0') }}" min="0" step="1">
                            </div>
                            <div class="col-6 mb-5">
                                <label class="form-label">KDV Oranı (%)</label>
                                <input type="number" name="kdv_orani" class="form-control" min="0" max="100" step="1"
                                       value="{{ old('kdv_orani', $kurs->kdv_orani ?? $kurs->default_kdv_orani) }}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--end::Fiyat Bilgisi-->
            <!--begin::Template settings-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Sertifika</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="mb-10">
                        <label for="sertifika_ornegi" class="required form-label">Sertifika Örneği</label>
                        <input type="file" name="sertifika_ornegi" id="sertifika_ornegi" class="form-control @error('sertifika_ornegi') is-invalid @enderror" value="{{ old('sertifika_ornegi', $kurs->sertifika_ornegi ?? '') }}" />
                        @error('sertifika_ornegi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($kurs) && $kurs->sertifika_ornegi)
                            <div class="mb-3" id="image-container">
                                <img src="{{ asset('storage/' . config('upload.kurs.path') . '/' . $kurs->sertifika_ornegi) }}"
                                     alt="Sertifika Örneği" class="img-thumbnail" width="250">
                                <button type="button" class="btn btn-danger btn-sm mt-2"
                                        id="deleteImage"
                                        data-img-delete="true">
                                    Resmi Sil
                                </button>
                            </div>
                        @endif

                        <!-- Sertifika Örneği Silme İşlemi İçin Gizli Input -->
                        <input type="hidden" name="delete_sertifika_ornegi" id="delete_sertifika_ornegi" value="0">
                    </div>

                    <div class="mb-10">
                        <label for="sertifika_turu" class="form-label required">Sertifika Türü</label>
                        <select class="form-select @error('sertifika_turu') is-invalid @enderror" id="sertifika_turu" name="sertifika_turu" required>
                            <option>Seçiniz</option>
                            @foreach($sertifikaTurleri as $item)
                                <option value="{{ $item->key }}" {{ old('sertifika_turu', $kurs->sertifika_turu ?? 0) == $item->key ? 'selected' : '' }}>
                                    {{ $item->value }}
                                </option>
                            @endforeach
                        </select>
                        @error('sertifika_turu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Template settings-->
        </div>
        <!--end::Aside column-->
        <!--begin::Main column-->
        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <!--begin:::Tabs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#genel">Genel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#ozellikler">Özellikler</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#seo">SEO</a>
                </li>
            </ul>
            <!--end:::Tabs-->
            <!--begin::Tab content-->
            <div class="tab-content">
                <!--begin::Tab pane-->
                <div class="tab-pane fade show active" id="genel" role="tab-panel">
                    <div class="d-flex flex-column gap-7 gap-lg-10">
                        <!--begin::General options-->
                        <div class="card card-flush py-4">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Genel</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="mb-10 fv-row">
                                    <label class="required form-label">Kurs Adı</label>
                                    <input type="text" name="kurs_adi" id="kurs_adi" class="form-control @error('kurs_adi') is-invalid @enderror" value="{{ old('kurs_adi', $kurs->kurs_adi ?? '') }}" required/>
                                    @error('kurs_adi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-10">
                                    <label for="ozet" class="required form-label">Kısa Açıklama</label>
                                    <textarea name="ozet" id="ozet" class="form-control @error('ozet') is-invalid @enderror" rows="5" required>{{ old('ozet', $kurs->ozet ?? '') }}</textarea>
                                    @error('ozet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!--end::General options-->

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Detay</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="mb-10 " style="min-height: 500px;">
                                    <label for="aciklama" class="required form-label">İçerik</label>
                                    <textarea name="aciklama" id="aciklama" class="form-control @error('aciklama') is-invalid @enderror" required>{{ old('aciklama', $kurs->aciklama ?? '') }}</textarea>
                                    @error('aciklama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Özellikler</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <!--begin::Repeater-->
                                @php
                                    $ozellikler = old('ozellikler', $kurs->ozellikler ?? []);
                                @endphp

                                <!--begin::Repeater-->
                                <div id="ozellikler_repeater">
                                    <div class="form-group">
                                        <div data-repeater-list="ozellikler">

                                            @forelse($ozellikler as $item)
                                                <div data-repeater-item>
                                                    <div class="form-group row align-items-center">
                                                        <div class="col-md-10">
                                                            <label class="form-label">Özellik:</label>
                                                            <textarea name="ozellik" class="form-control mb-2 mb-md-0"
                                                                      rows="2" placeholder="Özellik giriniz">{{ $item }}</textarea>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <a href="javascript:;" data-repeater-delete
                                                               class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                                                <i class="fa-solid fa-trash-can"></i> Sil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div data-repeater-item>
                                                    <div class="form-group row align-items-center">
                                                        <div class="col-md-10">
                                                            <label class="form-label">Özellik:</label>
                                                            <textarea name="ozellik" class="form-control mb-2 mb-md-0"
                                                                      rows="2" placeholder="Özellik giriniz"></textarea>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <a href="javascript:;" data-repeater-delete
                                                               class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                                                <i class="fa-solid fa-trash-can"></i> Sil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse

                                        </div>
                                    </div>

                                    <div class="form-group mt-5">
                                        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                                            <i class="ki-duotone ki-plus fs-3"></i> Özellik Ekle
                                        </a>
                                    </div>
                                </div>

                                <!--end::Repeater-->
                            </div>
                            <!--end::Card header-->
                        </div>
                        <!--end::Pricing-->
                    </div>
                </div>
                <!--end::Tab pane-->
                <!--begin::Tab pane-->
                <div class="tab-pane fade" id="ozellikler" role="tab-panel">
                    <div class="d-flex flex-column gap-7 gap-lg-10">
                        <!--begin::Inventory-->
                        <div class="card card-flush py-4">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Kurs Özellikleri</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <div class="row mb-10">
                                    <div class="col-6">
                                        <label class="form-label" for="kurs_puani">Kurs Puanı</label>
                                        <input type="number" name="kurs_puani" class="form-control @error('kurs_puani') is-invalid @enderror" min="0" max="100" step="1"
                                               value="{{ old('kurs_puani', $kurs->kurs_puani ?? 100) }}">
                                        @error('kurs_puani')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="gecme_notu">Geçme Notu</label>
                                        <input type="number" name="gecme_notu" class="form-control @error('gecme_notu') is-invalid @enderror" min="0" max="100" step="1"
                                               value="{{ old('gecme_notu', $kurs->gecme_notu ?? 0) }}">
                                        @error('gecme_notu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-6">
                                        <label class="form-label" for="egitim_suresi">Eğitim Süresi</label>
                                        <input type="text" name="egitim_suresi" class="form-control  @error('egitim_suresi') is-invalid @enderror" value="{{ old('egitim_suresi', $kurs->egitim_suresi ?? '') }}">
                                        @error('egitim_suresi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="egitim_sureci">Eğitim Süreci</label>
                                        <input type="text" name="egitim_sureci" class="form-control  @error('egitim_sureci') is-invalid @enderror" value="{{ old('egitim_sureci', $kurs->egitim_sureci ?? '') }}">
                                        @error('egitim_sureci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-6">
                                        <label class="form-label" for="kitap_destegi">Kitap Desteği</label>
                                        <select class="form-select @error('kitap_destegi') is-invalid @enderror" name="kitap_destegi" data-control="select2" data-hide-search="true">
                                            @foreach([0,1] as $v)
                                                <option value="{{ $v }}" @selected($kitapDestegiVal === $v)"> {{ status()->get($v, 'text', 'kitap_destegi') }} </option>
                                            @endforeach
                                        </select>
                                        @error('kitap_destegi')<div class="text-danger fs-7">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="ders_sayisi">Ders Sayısı</label>
                                        <input type="number" name="ders_sayisi" class="form-control" min="0" max="100" step="1"
                                               value="{{ old('ders_sayisi', $kurs->ders_sayisi ?? 0) }}">
                                    </div>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label">Gerekli Belgeler</label>
                                    @php $seciliBelgeler = old('belgeler', $kurs->belgeler ?? []); @endphp
                                    <select name="belgeler[]" class="form-select @error('belgeler') is-invalid @enderror" data-control="select2" data-close-on-select="false" data-placeholder="Belge Seçiniz" data-allow-clear="true" multiple="multiple">
                                        @foreach($belgeTurleri as $b)
                                            <option value="{{ $b->key }}" {{ in_array($b->key, $seciliBelgeler ?? []) ? 'selected' : '' }}>
                                                {{ $b->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('belgeler')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!--end::Card header-->
                        </div>
                        <!--end::Inventory-->
                        <!--begin::Neler Öğreneceğim-->
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Neler Öğreneceğim</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                @php
                                    // validasyon hatasında kullanıcı girdilerini koru
                                    $ogrenilecekler = old('neler_ogrenecegim', $kurs->neler_ogrenecegim ?? []);
                                @endphp

                                <div id="neler_ogrenecegim_repeater" class="mb-10">
                                    <label class="form-label d-block">Neler Öğreneceğim</label>

                                    <div data-repeater-list="neler_ogrenecegim">
                                        @forelse($ogrenilecekler as $item)
                                            <div data-repeater-item>
                                                <div class="row g-3 align-items-start mb-3">
                                                    <div class="col-md-10">
                                                        <textarea name="metin" class="form-control" rows="2" placeholder="Öğe yazın">{{ is_array($item) ? ($item['metin'] ?? '') : $item }}</textarea>
                                                    </div>
                                                    <div class="col-md-2 d-flex gap-2">
                                                        <a href="javascript:;" data-repeater-delete class="btn btn-light-danger">
                                                            <i class="fa-solid fa-trash-can"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div data-repeater-item>
                                                <div class="row g-3 align-items-start mb-3">
                                                    <div class="col-md-10">
                                                        <textarea name="metin" class="form-control" rows="2" placeholder="Öğe yazın"></textarea>
                                                    </div>
                                                    <div class="col-md-2 d-flex gap-2">
                                                        <a href="javascript:;" data-repeater-delete class="btn btn-light-danger">
                                                            <i class="fa-solid fa-trash-can"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>

                                    <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                                        Ekle
                                    </a>
                                </div>

                            </div>
                        </div>
                        <!--end::Neler Öğreneceğim-->
                        <!--begin::Kurs İçeriği-->
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Kurs İçeriği</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="alert alert-warning d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-shield-tick fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-dark">Güncelleme Bilgilendirmesi</h4>
                                        <span>Kurs içeriği yeni bir güncellemeye kadar <strong>Dersler</strong> bölümünden yayınlanacak.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Kurs İçeriği-->
                    </div>
                </div>
                <!--end::Tab pane-->
                <!-- start:tab-seo -->
                <div class="tab-pane fade" id="seo" role="tab-panel">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>SEO Ayarları</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="alert alert-primary d-flex align-items-center p-5">
                                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
                                <div class="d-flex flex-column">
                                    <span>SEO ayarlarının boş bırakılması durumunda içerik başlık ve özet kısmı kullanılır.</span>
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label" for="seo_title">Title</label>
                                <input type="text" name="seo_title" id="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $kurs->seo_title ?? '') }}" />
                                @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">En iyi görünürlük için 50-60 karakter kullanın.</div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label" for="seo_description">Description</label>
                                <input type="text" name="seo_description" id="seo_description" class="form-control @error('seo_description') is-invalid @enderror" value="{{ old('seo_description', $kurs->seo_description ?? '') }}" />
                                @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">Metnin tamamının görünmesi için 150-160 karakteri aşmayın.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end:tab-seo -->
            </div>
            <!--end::Tab content-->
            <div class="d-flex justify-content-end">
                <a href="{{ route(config('system.admin_prefix').'.kurs.index') }}" class="btn btn-light me-5">İptal</a>
                <button type="submit" class="btn btn-primary">{{ $kurs->exists ? 'Güncelle' : 'Ekle' }}</button>
            </div>
        </div>
        <!--end::Main column-->
</form>
@endsection

@section('js')
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('aciklama', {
            extraPlugins: 'uploadimage,image',
            removePlugins: 'easyimage',
            height: '500px',
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}'
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const sel = document.getElementById('ucretsizSelect');
            const box = document.getElementById('fiyatKdvFields');
            if (sel && box) {
                sel.addEventListener('change', () => {
                box.style.display = sel.value === 'E' ? 'none' : '';
            });
            }
        });
    </script>

    <script src="{{ theme_asset('admin', 'plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var $rep = $('#ozellikler_repeater');
            if (!$rep.length) return;

            if ($rep.data('repeater-initialized')) return;
            $rep.data('repeater-initialized', true);

            $rep.repeater({
                initEmpty: false,
                show: function () { $(this).slideDown(); },
                hide: function (deleteElement) { $(this).slideUp(deleteElement); }
            });
        });

        $('#neler_ogrenecegim_repeater').repeater({
            initEmpty: false,
            show: function () { $(this).slideDown(); },
            hide: function (deleteElement) { $(this).slideUp(deleteElement); }
        });

    </script>
@endsection
