@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Bölüm Yönetimi')
@section('css')

@endsection

@section('toolbar')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Bölümler</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ ucfirst($kurs->kurs_adi) }} Bölüm Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="row">
        <!-- Kategori Listeleme -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ ucfirst($kurs->kurs_adi) }} Bölüm Listesi</h3>
                </div>
                <div class="card-body">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatable">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>Sıra</th>
                            <th>Bölüm</th>
                            <th>Durum</th>
                            <th class="min-w-150px text-end">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bolumler as $bolum)
                        <tr>
                            <td><button type="button" disabled class="btn btn-sm btn-block btn-secondary">{{ $bolum->sira }}</button></td>
                            <td>{{ $bolum->bolum_adi }}</td>
                            <td><span class="badge badge-light-{{ status()->get($bolum->durum) }}">{{ status()->get($bolum->durum, 'text') }}</span></td>
                            <td class="text-end">
                                <a href="{{ route(config('system.admin_prefix').'.bolum.edit', ['kurs_id' => $kurs_id, 'id' => $bolum->id]) }}" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route(config('system.admin_prefix').'.bolum.delete', ['kurs_id' => $kurs_id, 'id' => $bolum->id]) }}"
                                      method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-icon btn-light-danger"
                                            data-bs-toggle="tooltip"
                                            title="Sil"
                                            data-confirm-delete="true">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kategori Ekleme / Düzenleme Formu -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($data) ? 'Bölüm Düzenle' : 'Yeni Bölüm Ekle' }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ isset($data) ? route(config('system.admin_prefix').'.bolum.update', ['kurs_id' => $kurs_id, 'id' => $data->id]) : route(config('system.admin_prefix').'.bolum.store', $kurs_id) }}" method="POST">
                        @csrf
                        @if(isset($data))
                            @method('PUT')
                        @endif
                        <div class="mb-10">
                            <label for="bolum_adi" class="required form-label">Bölüm Adı</label>
                            <input type="text" name="bolum_adi" id="bolum_adi" class="form-control @error('bolum') is-invalid @enderror" value="{{ old('bolum_adi', $data->bolum_adi ?? '') }}" required/>
                            @error('bolum_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10 ">
                            <label for="aciklama" class="required form-label">Açıklama</label>
                            <textarea name="aciklama" id="aciklama" rows="10" class="form-control @error('aciklama') is-invalid @enderror" required>{{ old('aciklama', $data->aciklama ?? '') }}</textarea>
                            @error('aciklama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label for="sira" class="required form-label">Sıra</label>
                            <input type="text" name="sira" id="sira" class="form-control @error('sira') is-invalid @enderror" value="{{ old('sira', $data->sira ?? '') }}" required/>
                            @error('sira')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-success me-3">{{ isset($data) ? 'Güncelle' : 'Ekle' }}</button>
                                @if(isset($data))
                                    <a href="{{ route(config('system.admin_prefix').'.bolum.index', $kurs_id) }}" class="btn btn-secondary">İptal</a>
                                @endif
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum"
                                    {{ isset($data) && $data->durum == 1 ? 'checked' : '' }}>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

@endsection
