@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Ders Yönetimi')


@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Ders</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ isset($ders) ? 'Ders Düzenle' : 'Yeni Ders Ekle' }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.ders.index', ['kurs_id' => $kurs_id]) }}" class="btn btn-sm btn-primary">Ders Listesi</a>
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

    <form action="{{ ($ders->exists)
    ? route(config('system.admin_prefix').'.ders.update', ['kurs_id' => $kurs_id, 'id' => $ders->id])
    : route(config('system.admin_prefix').'.ders.store', $kurs_id) }}" method="POST">
        @csrf
        @if(isset($ders))
            @method('PUT')
        @endif
        <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($ders) ? 'Ders Düzenle' : 'Yeni Ders Ekle' }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-10">
                        <label for="baslik" class="required form-label">Ders Adı</label>
                        <input type="text" name="baslik" id="baslik" class="form-control @error('baslik') is-invalid @enderror" value="{{ old('baslik', $ders->baslik ?? '') }}" required/>
                        @error('baslik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-10">
                        <label for="ozet" class="required form-label">Özet</label>
                        <textarea name="ozet" id="ozet" class="form-control @error('ozet') is-invalid @enderror" rows="5" required>{{ old('ozet', $ders->ozet ?? '') }}</textarea>
                        @error('ozet')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-10 " style="min-height: 500px;">
                        <label for="icerik" class="form-label">İçerik</label>
                        <textarea name="icerik" id="icerik" class="form-control @error('icerik') is-invalid @enderror">{{ old('icerik', $ders->icerik ?? '') }}</textarea>
                        @error('icerik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Video</h3>
                </div>
                <div class="card-body">
                    <div id="vimeo_wrapper" class="mb-10">
                        <label for="vimeo_select" class="form-label">Vimeo Videosu</label>
                        <select id="vimeo_select" class="form-select" style="width:100%" data-placeholder="Vimeo videosu ara…"></select>
                    </div>

                    <div class="mb-10">
                        <label for="video_kaynak_id" class="required form-label">Video ID</label>
                        <input type="text" name="video_kaynak_id" id="video_kaynak_id" class="form-control @error('video_kaynak_id') is-invalid @enderror" value="{{ old('video_kaynak_id', $ders->video_kaynak_id ?? '') }}" />
                        @error('video_kaynak_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-10">
                        <label class="form-label required" for="ders_suresi">Ders Süresi
                            <span class="ms-1"  data-bs-toggle="tooltip" title="Saniye cinsinden giriniz." >
	                            <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                            </span>
                        </label>
                        <input type="number" name="ders_suresi" id="ders_suresi" class="form-control" required
                               value="{{ old('ders_suresi', $ders->ders_suresi ?? 0) }}">
                    </div>
                    <div class="mb-10">
                        <div class="form-check form-switch form-check-custom form-check-warning form-check-solid">
                            <input class="form-check-input" name="demo" type="checkbox" value="1" {{ isset($ders) && $ders->demo == 1 ? 'checked' : '' }} id="demo_video"/>
                            <label class="form-check-label" for="demo_video">
                                Demo Video
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-10">
                <div class="card-header">
                    <h3 class="card-title">Özellikler</h3>
                </div>
                <div class="card-body">
                    <div class="mb-10">
                        <label for="egitmen" class="form-label">Eğitmen</label>
                        <select class="form-select" name="egitmen_id" id="egitmen">
                            <option value="">Eğitmen Seçiniz</option>
                            @foreach($egitmenler as $egitmen)
                                <option value="{{ $egitmen->user_id }}"
                                    @selected(old('egitmen_id', $ders->egitmen_id ?? '') == $egitmen->user_id)>
                                    {{ $egitmen->isim.' '.$egitmen->soyisim }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-10">
                        <label for="bolum_id" class="form-label required">Bölüm</label>
                        <select class="form-select" data-control="select2" data-placeholder="Bölüm Seçiniz" name="bolum_id" id="bolum_id" required>
                            <option></option>
                            @foreach($bolumler as $bolum)
                                <option value="{{ $bolum->id }}" {{ old('bolum_id', $ders->bolum_id ?? 0) == $bolum->id ? 'selected' : '' }}>
                                    {{ $bolum->bolum_adi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-10">
                        <label for="resim" class="required form-label">Resim</label>
                        <input type="file" name="resim" id="resim" class="form-control @error('resim') is-invalid @enderror" value="{{ old('resim', $ders->resim ?? '') }}" />
                        @error('resim')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($ders) && $ders->resim)
                            <div class="mb-3" id="image-container">
                                <img src="{{ asset('storage/' . config('upload.kurs.path') . '/' . $ders->resim) }}"
                                     alt="Resim" class="img-thumbnail" width="250">
                                <button type="button" class="btn btn-danger btn-sm mt-2"
                                        id="deleteImage"
                                        data-img-delete="true">
                                    Resmi Sil
                                </button>
                            </div>
                        @endif

                        <!-- Resim Silme İşlemi İçin Gizli Input -->
                        <input type="hidden" name="delete_resim" id="delete_resim" value="0">
                    </div>

                    <div class="mb-10">
                        <label for="dosya" class="required form-label">Dosya</label>
                        <input type="file" name="dosya" id="dosya" class="form-control @error('dosya') is-invalid @enderror" value="{{ old('dosya', $ders->dosya ?? '') }}" />
                        @error('dosya')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-10">
                        <label for="sira" class="required form-label">Sıra</label>
                        <input type="number" name="sira" id="sira" class="form-control @error('sira') is-invalid @enderror" value="{{ old('sira', $ders->sira ?? '') }}" min="0" max="1000" step="1"/>
                        @error('sira')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex flex-row justify-content-between">
                        <button type="submit" class="btn btn-primary">{{ isset($ders) ? 'Güncelle' : 'Ekle' }}</button>
                        <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum"
                                {{ isset($ders) && $ders->durum == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

@section('js')

    <script>
        $(function () {
            const $s = $('#vimeo_select');

            // ÇAKIŞMA ÖNLEMİ: varsa önce sök
            if ($s.hasClass('select2-hidden-accessible') || $s.data('select2')) {
                $s.select2('destroy');
            }

            $s.select2({
                theme: 'bootstrap5',
                width: '100%',
                dropdownParent: $(document.body),
                placeholder: $s.data('placeholder') || 'Vimeo videosu ara…',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '/api/vimeo/search',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        const term = (typeof params.term === 'string') ? params.term : '';
                        const page = (typeof params.page === 'number') ? params.page : 1;
                        return { q: term, page: page };
                    },
                    processResults: function (data) {
                        const results = Array.isArray(data?.results) ? data.results : [];
                        const pagination = data?.pagination || { more: false };
                        return { results, pagination };
                    }
                },
                // Listede küçük görsel
                templateResult: function (item) {
                    if (!item || !item.id || item.loading) return item?.text || '';
                    const img = item.thumb ? `<img src="${item.thumb}" class="me-2" style="width:28px;height:16px;object-fit:cover;border-radius:3px;">` : '';
                    return $(`<span>${img}${item.text || ''}</span>`);
                },
                // Seçimde sade metin
                templateSelection: function (item) { return item?.text || ''; },
                escapeMarkup: function (m) { return m; }
            }).on('select2:select', function (e) {
                $('#video_kaynak_id').val(e.params.data.id);
                $('#ders_suresi').val(e.params.data.duration);

            });
        });

        $('[data-control="select2"]').select2({
            theme: 'bootstrap5',
            width: '100%',
            allowClear: true
        });

    </script>


    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('icerik', {
            extraPlugins: 'uploadimage,image',
            removePlugins: 'easyimage',
            height: '500px',
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}'
        });
    </script>



@endsection
