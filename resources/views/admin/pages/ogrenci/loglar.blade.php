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
                <h3 class="fw-bold m-0">Log Kayıtları</h3>
            </div>
        </div>

        <div class="card-body p-9">

            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Üyelik Logları</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Ders Logları</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="kt_tab_pane_4" role="tabpanel">
                    ...
                </div>
                <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                    <!-- start: ders-loglari -->
                    @if($dersLoglari->count() > 0)
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="ders_loglari">
                            <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>Kurs</th>
                                <th>Ders</th>
                                <th>Son İzleme</th>
                                <th>İzlediği Süre</th>
                                <th class="text-end">Nerede Kaldı</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($dersLoglari as $item)
                                <tr>
                                    <td>{{ $item->kurs->kurs_adi }}</td>
                                    <td>{{ $item->ders->baslik }}</td>
                                    <td>{{ Carbon::parse($item->son_izleme)->format('d.m.Y H:i') }}</td>
                                    <td><span class="badge badge-light-primary badge-lg">{{ logFormat($item->izledigi_sure) }}</span></td>
                                    <td><span class="badge badge-light-danger badge-lg">{{ gecenZaman($item->nerede_kaldi) }}</span></td>
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
                                    <h4 class="text-gray-900 fw-bold">Kayıt Bulunamadı!</h4>
                                    <div class="fs-6 text-gray-700">Bu öğrenciye ait log bulunmamaktadır.</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- end: ders-loglari -->
                </div>
            </div>

        </div>
    </div>

@endsection

@section('js')
<script>
    $("#ders_loglari").DataTable();
</script>
@endsection
