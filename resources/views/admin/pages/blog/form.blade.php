@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Blog Yönetimi')
@section('css')

@endsection

@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Blog</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ isset($blog) ? 'Blog Düzenle' : 'Yeni Blog Ekle' }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.blog.index') }}" class="btn btn-sm btn-primary">Blog Listesi</a>
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

    <form action="{{ isset($blog) ? route(config('system.admin_prefix').'.blog.update', $blog->id) : route(config('system.admin_prefix').'.blog.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($blog))
        @method('PUT')
    @endif
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ isset($blog) ? 'Blog Düzenle' : 'Yeni Blog Ekle' }}</h3>
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1">Genel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2">Seo Ayarları</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab_1" role="tabpanel">
                        <div class="mb-10">
                            <label for="baslik" class="required form-label">Başlık</label>
                            <input type="text" name="baslik" id="baslik" class="form-control" value="{{ old('baslik', $blog->baslik ?? '') }}" required/>
                            @error('baslik')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label for="ozet" class="required form-label">Özet</label>
                            <textarea name="ozet" id="ozet" class="form-control" rows="5" required>{{ old('ozet', $blog->ozet ?? '') }}</textarea>
                            @error('ozet')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10 " style="min-height: 500px;">
                            <label for="icerik" class="required form-label">İçerik</label>
                            <textarea name="icerik" id="icerik" class="form-control" required>{{ old('icerik', $blog->icerik ?? '') }}</textarea>
                            @error('icerik')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_2" role="tabpanel">
                        <div class="mb-10">
                            <label for="seo_title" class="required form-label">Başlık</label>
                            <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $blog->seo_title ?? '') }}" />
                            <div class="text-muted fs-7">Seo açısından 50-70 karakter arasında başlık kullanınız.</div>
                            @error('seo_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label for="seo_description" class="required form-label">Açıklama</label>
                            <textarea name="seo_description" id="seo_description" class="form-control">{{ old('seo_description', $blog->seo_description ?? '') }}</textarea>
                            <div class="text-muted fs-7">Seo açısından 50-160 karakter arasında açıklama kullanınız.</div>
                            @error('seo_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Özellikler</h3>
            </div>
            <div class="card-body">
                <div class="mb-10">
                    <label for="resim" class="required form-label">Resim</label>
                    <input type="file" name="resim" id="resim" class="form-control" value="{{ old('resim', $blog->resim ?? '') }}" />
                    @error('resim')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($blog) && $blog->resim)
                        <div class="mb-3" id="image-container">
                            <img src="{{ asset('storage/' . config('upload.blog.path') . '/' . $blog->resim) }}"
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
                    <label for="kategori_id" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori_id" name="kategori_id" required>
                        <option>Seçiniz</option>
                        @foreach($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $blog->kategori_id ?? 0) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->isim }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-10">
                    <label for="kurs_id" class="form-label">Bağlı Kurslar</label>
                    <select class="form-select select2 @error('kurs_id') is-invalid @enderror" id="kurs_id" name="kurs_id[]" multiple="multiple">
                        @foreach($kurslar as $kurs)
                            <option value="{{ $kurs->id }}"
                                    @if(isset($blog) && in_array($kurs->id, json_decode($blog->kurs_id ?? '[]', true))) selected @endif>
                                {{ $kurs->kurs_adi }}
                            </option>
                        @endforeach
                    </select>
                    @error('kurs_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="yayin_tarihi" class="form-label">Yayın Tarihi</label>
                    <input type="text" class="form-control @error('yayin_tarihi') is-invalid @enderror"
                           id="yayin_tarihi" name="yayin_tarihi"
                           value="{{ old('yayin_tarihi', isset($blog) ? date('Y-m-d H:i', strtotime($blog->yayin_tarihi)) : date('Y-m-d H:i')) }}">
                    @error('yayin_tarihi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-row justify-content-between">
                    <button type="submit" class="btn btn-primary">{{ isset($blog) ? 'Güncelle' : 'Ekle' }}</button>
                    <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum"
                            {{ isset($blog) && $blog->durum == 1 ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('js')
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

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Lütfen seçim yapın',
                closeOnSelect: false
            });
        });

        $("#yayin_tarihi").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            locale:"tr",
        });

    </script>

@endsection
