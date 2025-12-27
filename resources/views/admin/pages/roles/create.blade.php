@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Yetki Yönetimi')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Yetki</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Rol Ekle</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <a href="{{ route(config('system.admin_prefix').'.roles.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus fs-4 me-2"></i>Rol Listesi</a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="card card-flush">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <h3 class="card-title">Rol Ekle</h3>
        </div>
        <!--begin::Card body-->
        <div class="card-body pt-0">
            @include(theme_view('admin', 'pages.roles._form'), [
                'action' => route(config('system.admin_prefix').'.roles.store'),
                'method' => 'POST',
                'rolePermissions' => [],
            ])
            <!--end::Card body-->
        </div>
@endsection

@section('js')


@endsection
