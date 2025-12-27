@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Data Yükleme Sonuç Sayfası')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Data</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Toplu Data Yükleme</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">

                <!--begin::Button-->
                <a href="{{ route(config('system.admin_prefix').'.data.index') }}" class="btn btn-sm btn-primary">Data Listesi</a>

                <!--end::Button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')

    <div class="card card-flush overflow-hidden mb-5">
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">Toplu Data Yükleme</span>
            </h3>
        </div>
        <!--begin::Card body-->
        <div class="card-body">
            <table class="table table-rounded table-striped border gy-7 gs-7">
                <thead>
                <tr>
                    <th>#</th>
                    <th>İsim</th>
                    <th>Eposta</th>
                    <th>Telefon</th>
                    <th>Sonuç</th>
                    <th>Mesaj</th>
                </tr>
                </thead>
                <tbody>
                @foreach($veriler as $index => $veri)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $veri['isim'] }}</td>
                        <td>{{ $veri['eposta'] }}</td>
                        <td>{{ $veri['telefon'] }}</td>
                        <td>
                            @if($veri['durum'] === 'success')
                                <i class="fa-solid fa-check text-success"></i>
                            @else
                                <i class="fa-solid fa-xmark text-danger"></i>
                            @endif
                        </td>
                        <td>{{ $veri['mesaj'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>



        </div>
        <!--end::Card body-->
    </div>

@endsection

@section('js')
    <script src="{{ theme_asset('admin', 'js/widgets.bundle.js') }}"></script>
@endsection
