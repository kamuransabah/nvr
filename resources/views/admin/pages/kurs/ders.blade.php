@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Ders Yönetimi')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Dersler</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">{{ ucfirst($kurs->kurs_adi) }} Ders Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.ders.add', ['kurs_id' => $kurs_id]) }}" class="btn btn-sm btn-success">Ders Ekle</a>
                <!--end::Button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst($kurs->kurs_adi) }} Ders Listesi</h3>
        </div>
        <div class="card-body">
            <table class="table align-middle table-row-dashed" id="datatable">
                <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>Sıra</th>
                    <th>Ders</th>
                    <th class="min-w-150px">Tür</th>
                    <th>Ders Süresi</th>
                    <th>Durum</th>
                    <th class="min-w-150px text-end">İşlem</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dersler as $ders)
                <tr>
                    <td><button type="button" disabled class="btn btn-sm btn-block btn-secondary">{{ $ders->sira }}</button></td>
                    <td>
                        @if($ders->demo == 1)
                        <span class="badge badge-light-info me-1">Demo</span>
                        @endif
                        {{ $ders->baslik }}
                    </td>
                    <td>
                        @if(empty($ders->video_kaynak_id) && empty($ders->dosya) && !empty($ders->icerik))
                        <button type="button" class="btn btn-sm btn-primary"><i class="fa-solid fa-file-lines me-2"></i> Metin</button>
                        Metin
                        @else
                        <button type="button" class="btn btn-sm btn-info"><i class="fa-solid fa-video"></i> Video</button>
                        @endif
                    </td>
                    <td>{{ sureHesapla($ders->ders_suresi) }}</td>
                    <td><span class="badge badge-light-{{ status()->get($ders->durum) }}">{{ status()->get($ders->durum, 'text') }}</span></td>
                    <td class="text-end">
                        <a href="{{ route(config('system.admin_prefix').'.ders.edit', ['kurs_id' => $kurs_id, 'id' => $ders->id]) }}" class="btn btn-sm btn-icon btn-light-info me-1" data-bs-toggle="tooltip" title="Düzenle">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form action="{{ route(config('system.admin_prefix').'.ders.delete', ['kurs_id' => $kurs_id, 'id' => $ders->id]) }}"
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
@endsection

@section('js')

@endsection
