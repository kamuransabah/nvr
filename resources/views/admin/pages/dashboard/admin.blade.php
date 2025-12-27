@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Dashboard')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Dashboard</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Dashboard</li>
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
    <!--begin::Row-->
    <div class="row gx-5 gx-xl-10 mb-xl-10">
        <!--begin::Col-->
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-10">
            <!--begin::Card widget 4-->
            <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title d-flex flex-column">
                        <!--begin::Info-->
                        <div class="d-flex align-items-center">
                            <!--begin::Currency-->
                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start">₺</span>
                            <!--end::Currency-->
                            <!--begin::Amount-->
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">69,700</span>
                            <!--end::Amount-->
                            <!--begin::Badge-->
                            <span class="badge badge-light-success fs-base">
<i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>2.2%
                            </span>
                            <!--end::Badge-->
                        </div>
                        <!--end::Info-->
                        <!--begin::Subtitle-->
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Beklenen Kazanç</span>
                        <!--end::Subtitle-->
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body pt-2 pb-4 d-flex align-items-center">
                    <!--begin::Chart-->
                    <div class="d-flex flex-center me-5 pt-2">
                        <div data-kt-line="11" data-kt-size="70" id="kt_card_widget_4_chart" style="min-width: 70px; min-height: 70px"></div>
                    </div>
                    <!--end::Chart-->
                    <!--begin::Labels-->
                    <div class="d-flex flex-column content-justify-center w-100">
                        <!--begin::Label-->
                        <div class="d-flex fs-6 fw-semibold align-items-center">
                            <!--begin::Bullet-->
                            <div class="bullet w-8px h-6px rounded-2 bg-danger me-3"></div>
                            <!--end::Bullet-->
                            <!--begin::Label-->
                            <div class="text-gray-500 flex-grow-1 me-4">Ayakkabı</div>
                            <!--end::Label-->
                            <!--begin::Stats-->
                            <div class="fw-bolder text-gray-700 text-xxl-end">₺7,660</div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Label-->
                        <!--begin::Label-->
                        <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                            <!--begin::Bullet-->
                            <div class="bullet w-8px h-6px rounded-2 bg-primary me-3"></div>
                            <!--end::Bullet-->
                            <!--begin::Label-->
                            <div class="text-gray-500 flex-grow-1 me-4">Oyun</div>
                            <!--end::Label-->
                            <!--begin::Stats-->
                            <div class="fw-bolder text-gray-700 text-xxl-end">₺2,820</div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Label-->
                        <!--begin::Label-->
                        <div class="d-flex fs-6 fw-semibold align-items-center">
                            <!--begin::Bullet-->
                            <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #E4E6EF"></div>
                            <!--end::Bullet-->
                            <!--begin::Label-->
                            <div class="text-gray-500 flex-grow-1 me-4">Others</div>
                            <!--end::Label-->
                            <!--begin::Stats-->
                            <div class="fw-bolder text-gray-700 text-xxl-end">₺45,257</div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Labels-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card widget 4-->
            <!--begin::Card widget 5-->
            <div class="card card-flush h-md-50 mb-xl-10">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title d-flex flex-column">
                        <!--begin::Info-->
                        <div class="d-flex align-items-center">
                            <!--begin::Amount-->
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">1,836</span>
                            <!--end::Amount-->
                            <!--begin::Badge-->
                            <span class="badge badge-light-danger fs-base">
<i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i>2.2%</span>
                            <!--end::Badge-->
                        </div>
                        <!--end::Info-->
                        <!--begin::Subtitle-->
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Bu Ayki Siparişler</span>
                        <!--end::Subtitle-->
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end pt-0">
                    <!--begin::Progress-->
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-bolder fs-6 text-gray-900">1,048 to Goal</span>
                            <span class="fw-bold fs-6 text-gray-500">62%</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-success rounded">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="bg-success rounded h-8px" role="progressbar" style="width: 62%;"></div>
                        </div>
                    </div>
                    <!--end::Progress-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card widget 5-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-10">
            <!--begin::Card widget 6-->
            <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title d-flex flex-column">
                        <!--begin::Info-->
                        <div class="d-flex align-items-center">
                            <!--begin::Currency-->
                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start">₺</span>
                            <!--end::Currency-->
                            <!--begin::Amount-->
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">2,420</span>
                            <!--end::Amount-->
                            <!--begin::Badge-->
                            <span class="badge badge-light-success fs-base">
<i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>2.6%</span>
                            <!--end::Badge-->
                        </div>
                        <!--end::Info-->
                        <!--begin::Subtitle-->
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Günlük Ortalama Satış</span>
                        <!--end::Subtitle-->
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end px-0 pb-0">
                    <!--begin::Chart-->
                    <div class="w-100" id="kt_card_widget_6_chart" style="height: 80px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card widget 6-->
            <!--begin::Card widget 7-->
            <div class="card card-flush h-md-50 mb-xl-10">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title d-flex flex-column">
                        <!--begin::Amount-->
                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">6.3k</span>
                        <!--end::Amount-->
                        <!--begin::Subtitle-->
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Bu Ay Yeni Müşteri</span>
                        <!--end::Subtitle-->
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex flex-column justify-content-end pe-0">
                    <!--begin::Title-->
                    <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Bugünün Kahramanları</span>
                    <!--end::Title-->
                    <!--begin::Users group-->
                    <div class="symbol-group symbol-hover flex-nowrap">
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Alan Warden">
                            <span class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                        </div>
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Michael Eberon">
                            <img alt="Pic" src="{{ get_theme_assets_path('admin') }}/media/avatars/300-11.jpg"/>
                        </div>
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Susan Redwood">
                            <span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                        </div>
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Melody Macy">
                            <img alt="Pic" src="{{ get_theme_assets_path('admin') }}/media/avatars/300-2.jpg"/>
                        </div>
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Perry Matthew">
                            <span class="symbol-label bg-danger text-inverse-danger fw-bold">P</span>
                        </div>
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Barry Walter">
                            <img alt="Pic" src="{{ get_theme_assets_path('admin') }}/media/avatars/300-12.jpg"/>
                        </div>
                        <a class="symbol symbol-35px symbol-circle" data-bs-target="#kt_modal_view_users" data-bs-toggle="modal" href="#">
                            <span class="symbol-label bg-light text-gray-400 fs-8 fw-bold">+42</span>
                        </a>
                    </div>
                    <!--end::Users group-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card widget 7-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-0">
            <!--begin::Chart widget 3-->
            <div class="card card-flush overflow-hidden h-md-100">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Bu Ayın Satışları</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Tüm kanallardan kullanıcılar</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Menu-->
                        <button class="btn btn-icon btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-overflow="true" data-kt-menu-placement="bottom-end" data-kt-menu-trigger="click">
                            <i class="ki-outline ki-dots-square fs-1"></i>
                        </button>
                        <!--begin::Menu 2-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Hızlı İşlemler</div>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator mb-3 opacity-75"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Talep</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Müşteri</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3" data-kt-menu-placement="right-start" data-kt-menu-trigger="hover">
                                <!--begin::Menu item-->
                                <a class="menu-link px-3" href="#">
                                    <span class="menu-title">Yeni Grup</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <!--end::Menu item-->
                                <!--begin::Menu sub-->
                                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Yönetici Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Personel Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Üye Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu sub-->
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Kişi</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator mt-3 opacity-75"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content px-3 py-3">
                                    <a class="btn btn-primary btn-sm px-4" href="#">Rapor Oluştur</a>
                                </div>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu 2-->
                        <!--end::Menu-->
                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-between flex-column pb-1 px-0">
                    <!--begin::Statistics-->
                    <div class="px-9 mb-5">
                        <!--begin::Statistics-->
                        <div class="d-flex mb-2">
                            <span class="fs-4 fw-semibold text-gray-500 me-1">₺</span>
                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">14,094</span>
                        </div>
                        <!--end::Statistics-->
                        <!--begin::Description-->
                        <span class="fs-6 fw-semibold text-gray-500">Hedefe kalan ₺48.346</span>
                        <!--end::Description-->
                    </div>
                    <!--end::Statistics-->
                    <!--begin::Chart-->
                    <div class="min-h-auto ps-4 pe-6" id="kt_charts_widget_3" style="height: 300px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 3-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    <!--begin::Row-->
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-6 mb-xl-10">
            <!--begin::Table widget 2-->
            <div class="card h-md-100">
                <!--begin::Header-->
                <div class="card-header align-items-center border-0">
                    <!--begin::Title-->
                    <h3 class="fw-bold text-gray-900 m-0">Son Siparişler</h3>
                    <!--end::Title-->
                    <!--begin::Menu-->
                    <button class="btn btn-icon btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-overflow="true" data-kt-menu-placement="bottom-end" data-kt-menu-trigger="click">
                        <i class="ki-outline ki-dots-square fs-1"></i>
                    </button>
                    <!--begin::Menu 2-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Hızlı İşlemler</div>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator mb-3 opacity-75"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a class="menu-link px-3" href="#">Yeni Talep</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a class="menu-link px-3" href="#">Yeni Müşteri</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3" data-kt-menu-placement="right-start" data-kt-menu-trigger="hover">
                            <!--begin::Menu item-->
                            <a class="menu-link px-3" href="#">
                                <span class="menu-title">Yeni Grup</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <!--end::Menu item-->
                            <!--begin::Menu sub-->
                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3" href="#">Yönetici Grubu</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3" href="#">Personel Grubu</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a class="menu-link px-3" href="#">Üye Grubu</a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu sub-->
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <a class="menu-link px-3" href="#">Yeni Kişi</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator mt-3 opacity-75"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content px-3 py-3">
                                <a class="btn btn-primary btn-sm px-4" href="#">Rapor Oluştur</a>
                            </div>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu 2-->
                    <!--end::Menu-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-2">
                    <!--begin::Nav-->
                    <ul class="nav nav-pills nav-pills-custom mb-3">
                        <!--begin::Item-->
                        <li class="nav-item mb-3 me-3 me-lg-6">
                            <!--begin::Link-->
                            <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden active w-80px h-85px py-4" data-bs-toggle="pill" href="#kt_stats_widget_2_tab_1">
                                <!--begin::Icon-->
                                <div class="nav-icon">
                                    <img alt="" class="" src="{{ get_theme_assets_path('admin') }}/media/svg/products-categories/t-shirt.svg"/>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Subtitle-->
                                <span class="nav-text text-gray-700 fw-bold fs-6 lh-1">Tişört</span>
                                <!--end::Subtitle-->
                                <!--begin::Bullet-->
                                <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                                <!--end::Bullet-->
                            </a>
                            <!--end::Link-->
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item mb-3 me-3 me-lg-6">
                            <!--begin::Link-->
                            <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden w-80px h-85px py-4" data-bs-toggle="pill" href="#kt_stats_widget_2_tab_2">
                                <!--begin::Icon-->
                                <div class="nav-icon">
                                    <img alt="" class="" src="{{ get_theme_assets_path('admin') }}/media/svg/products-categories/gaming.svg"/>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Subtitle-->
                                <span class="nav-text text-gray-700 fw-bold fs-6 lh-1">Oyun</span>
                                <!--end::Subtitle-->
                                <!--begin::Bullet-->
                                <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                                <!--end::Bullet-->
                            </a>
                            <!--end::Link-->
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item mb-3 me-3 me-lg-6">
                            <!--begin::Link-->
                            <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden w-80px h-85px py-4" data-bs-toggle="pill" href="#kt_stats_widget_2_tab_3">
                                <!--begin::Icon-->
                                <div class="nav-icon">
                                    <img alt="" class="" src="{{ get_theme_assets_path('admin') }}/media/svg/products-categories/watch.svg"/>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Subtitle-->
                                <span class="nav-text text-gray-600 fw-bold fs-6 lh-1">Saat</span>
                                <!--end::Subtitle-->
                                <!--begin::Bullet-->
                                <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                                <!--end::Bullet-->
                            </a>
                            <!--end::Link-->
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item mb-3 me-3 me-lg-6">
                            <!--begin::Link-->
                            <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden w-80px h-85px py-4" data-bs-toggle="pill" href="#kt_stats_widget_2_tab_4">
                                <!--begin::Icon-->
                                <div class="nav-icon">
                                    <img alt="" class="nav-icon" src="{{ get_theme_assets_path('admin') }}/media/svg/products-categories/gloves.svg"/>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Subtitle-->
                                <span class="nav-text text-gray-600 fw-bold fs-6 lh-1">Eldiven</span>
                                <!--end::Subtitle-->
                                <!--begin::Bullet-->
                                <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                                <!--end::Bullet-->
                            </a>
                            <!--end::Link-->
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item mb-3">
                            <!--begin::Link-->
                            <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden w-80px h-85px py-4" data-bs-toggle="pill" href="#kt_stats_widget_2_tab_5">
                                <!--begin::Icon-->
                                <div class="nav-icon">
                                    <img alt="" class="nav-icon" src="{{ get_theme_assets_path('admin') }}/media/svg/products-categories/shoes.svg"/>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Subtitle-->
                                <span class="nav-text text-gray-600 fw-bold fs-6 lh-1">Ayakkabı</span>
                                <!--end::Subtitle-->
                                <!--begin::Bullet-->
                                <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                                <!--end::Bullet-->
                            </a>
                            <!--end::Link-->
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Nav-->
                    <!--begin::Tab Content-->
                    <div class="tab-content">
                        <!--begin::Tap pane-->
                        <div class="tab-pane fade show active" id="kt_stats_widget_2_tab_1">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 w-50px">ÜRÜN</th>
                                        <th class="min-w-125px"></th>
                                        <th class="text-end min-w-100px">ADET</th>
                                        <th class="pe-0 text-end min-w-100px">FİYAT</th>
                                        <th class="pe-0 text-end min-w-100px">TOPLAM FİYAT</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/210.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Fil 1802</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-2347</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x1</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺72.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺126.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/215.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Kırmızı Laga</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-1321</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x2</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺45.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺76.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/209.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">RiseUP</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-4312</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x3</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺84.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺168.00</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Tap pane-->
                        <!--begin::Tap pane-->
                        <div class="tab-pane fade" id="kt_stats_widget_2_tab_2">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 w-50px">ÜRÜN</th>
                                        <th class="min-w-125px"></th>
                                        <th class="text-end min-w-100px">ADET</th>
                                        <th class="pe-0 text-end min-w-100px">FİYAT</th>
                                        <th class="pe-0 text-end min-w-100px">TOPLAM FİYAT</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/197.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Fil 1802</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-4312</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x1</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺32.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺312.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/178.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Kırmızı Laga</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-3122</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x2</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺53.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺62.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/22.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">RiseUP</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-1142</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x3</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺74.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺139.00</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Tap pane-->
                        <!--begin::Tap pane-->
                        <div class="tab-pane fade" id="kt_stats_widget_2_tab_3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 w-50px">ÜRÜN</th>
                                        <th class="min-w-125px"></th>
                                        <th class="text-end min-w-100px">ADET</th>
                                        <th class="pe-0 text-end min-w-100px">FİYAT</th>
                                        <th class="pe-0 text-end min-w-100px">TOPLAM FİYAT</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/1.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Fil 1324</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-1523</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x1</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺43.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺231.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/24.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Kırmızı Laga</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-5314</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x2</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺71.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺53.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/71.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">RiseUP</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-4222</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x3</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺23.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺213.00</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Tap pane-->
                        <!--begin::Tap pane-->
                        <div class="tab-pane fade" id="kt_stats_widget_2_tab_4">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 w-50px">ÜRÜN</th>
                                        <th class="min-w-125px"></th>
                                        <th class="text-end min-w-100px">ADET</th>
                                        <th class="pe-0 text-end min-w-100px">FİYAT</th>
                                        <th class="pe-0 text-end min-w-100px">TOPLAM FİYAT</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/41.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Fil 2635</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-1523</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x1</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺65.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺163.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/63.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Kırmızı Laga</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-2745</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x2</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺64.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺73.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/59.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">RiseUP</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-5173</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x3</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺54.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺173.00</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Tap pane-->
                        <!--begin::Tap pane-->
                        <div class="tab-pane fade" id="kt_stats_widget_2_tab_5">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 w-50px">ÜRÜN</th>
                                        <th class="min-w-125px"></th>
                                        <th class="text-end min-w-100px">ADET</th>
                                        <th class="pe-0 text-end min-w-100px">FİYAT</th>
                                        <th class="pe-0 text-end min-w-100px">TOPLAM FİYAT</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/10.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Nike</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-2163</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x1</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺64.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺287.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/96.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Adidas</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-2162</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x2</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺76.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺51.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img alt="" class="w-50px ms-n1" src="{{ get_theme_assets_path('admin') }}/media/stock/ecommerce/13.png"/>
                                        </td>
                                        <td class="ps-0">
                                            <a class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6 text-start pe-0" href="apps/ecommerce/catalog/edit-product.html">Puma</a>
                                            <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Ürün: #XDG-1537</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block fs-6 ps-0 text-end">x3</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺27.00</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold d-block fs-6">₺167.00</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--end::Tap pane-->
                    </div>
                    <!--end::Tab Content-->
                </div>
                <!--end: Card Body-->
            </div>
            <!--end::Table widget 2-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-6 mb-5 mb-xl-10">
            <!--begin::Chart widget 4-->
            <div class="card card-flush overflow-hidden h-md-100">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">İndirimli Ürün Satışları</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Tüm kanallardan kullanıcılar</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Menu-->
                        <button class="btn btn-icon btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-overflow="true" data-kt-menu-placement="bottom-end" data-kt-menu-trigger="click">
                            <i class="ki-outline ki-dots-square fs-1"></i>
                        </button>
                        <!--begin::Menu 2-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Hızlı İşlemler</div>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator mb-3 opacity-75"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Talep</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Müşteri</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3" data-kt-menu-placement="right-start" data-kt-menu-trigger="hover">
                                <!--begin::Menu item-->
                                <a class="menu-link px-3" href="#">
                                    <span class="menu-title">Yeni Grup</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <!--end::Menu item-->
                                <!--begin::Menu sub-->
                                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Yönetici Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Personel Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a class="menu-link px-3" href="#">Üye Grubu</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu sub-->
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a class="menu-link px-3" href="#">Yeni Kişi</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu separator-->
                            <div class="separator mt-3 opacity-75"></div>
                            <!--end::Menu separator-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content px-3 py-3">
                                    <a class="btn btn-primary btn-sm px-4" href="#">Rapor Oluştur</a>
                                </div>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu 2-->
                        <!--end::Menu-->
                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-between flex-column pb-1 px-0">
                    <!--begin::Info-->
                    <div class="px-9 mb-5">
                        <!--begin::Statistics-->
                        <div class="d-flex align-items-center mb-2">
                            <!--begin::Currency-->
                            <span class="fs-4 fw-semibold text-gray-500 align-self-start me-1">₺</span>
                            <!--end::Currency-->
                            <!--begin::Value-->
                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">3,706</span>
                            <!--end::Value-->
                            <!--begin::Label-->
                            <span class="badge badge-light-success fs-base">
<i class="ki-outline ki-arrow-down fs-5 text-success ms-n1"></i>4.5%</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Statistics-->
                        <!--begin::Description-->
                        <span class="fs-6 fw-semibold text-gray-500">Bu Ay Toplam İndirimli Satış</span>
                        <!--end::Description-->
                    </div>
                    <!--end::Info-->
                    <!--begin::Chart-->
                    <div class="min-h-auto ps-4 pe-6" id="kt_charts_widget_4" style="height: 300px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 4-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    <!--begin::Row-->
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-4 mb-xl-10">
            <!--begin::Engage widget 1-->
            <div class="card h-md-100" dir="ltr">
                <!--begin::Body-->
                <div class="card-body d-flex flex-column flex-center">
                    <!--begin::Heading-->
                    <div class="mb-2">
                        <!--begin::Title-->
                        <h1 class="fw-semibold text-gray-800 text-center lh-lg">Denediniz mi
                            <br/>yeni
                            <span class="fw-bolder">eTicaret Uygulaması?</span></h1>
                        <!--end::Title-->
                        <!--begin::Illustration-->
                        <div class="py-10 text-center">
                            <img alt="" class="theme-light-show w-200px" src="{{ get_theme_assets_path('admin') }}/media/svg/illustrations/easy/2.svg"/>
                            <img alt="" class="theme-dark-show w-200px" src="{{ get_theme_assets_path('admin') }}/media/svg/illustrations/easy/2-dark.svg"/>
                        </div>
                        <!--end::Illustration-->
                    </div>
                    <!--end::Heading-->
                    <!--begin::Links-->
                    <div class="text-center mb-1">
                        <!--begin::Link-->
                        <a class="btn btn-sm btn-primary me-2" href="apps/ecommerce/sales/listing.html">Uygulamayı Görüntüle</a>
                        <!--end::Link-->
                        <!--begin::Link-->
                        <a class="btn btn-sm btn-light" href="apps/ecommerce/catalog/add-product.html">Yeni Ürün</a>
                        <!--end::Link-->
                    </div>
                    <!--end::Links-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Engage widget 1-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-8 mb-5 mb-xl-10">
            <!--begin::Table Widget 4-->
            <div class="card card-flush h-xl-100">
                <!--begin::Card header-->
                <div class="card-header pt-7">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Ürün Siparişleri</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Günde ort. 57 sipariş</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Actions-->
                    <div class="card-toolbar">
                        <!--begin::Filters-->
                        <div class="d-flex flex-stack flex-wrap gap-4">
                            <!--begin::Destination-->
                            <div class="d-flex align-items-center fw-bold">
                                <!--begin::Label-->
                                <div class="text-gray-500 fs-7 me-2">Kategori</div>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select class="form-select form-select-transparent text-graY-800 fs-base lh-1 fw-bold py-0 ps-3 w-auto" data-control="select2" data-dropdown-css-class="w-150px" data-hide-search="true" data-placeholder="Select an option">
                                    <option></option>
                                    <option selected="selected" value="Show All">Tümünü Göster</option>
                                    <option value="a">Kategori A</option>
                                    <option value="b">Kategori A</option>
                                </select>
                                <!--end::Select-->
                            </div>
                            <!--end::Destination-->
                            begin::Durum
                            <div class="d-flex align-items-center fw-bold">
                                <!--begin::Label-->
                                <div class="text-gray-500 fs-7 me-2">Durum</div>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select class="form-select form-select-transparent text-gray-900 fs-7 lh-1 fw-bold py-0 ps-3 w-auto" data-control="select2" data-dropdown-css-class="w-150px" data-hide-search="true" data-kt-table-widget-4="filter_status" data-placeholder="Select an option">
                                    <option></option>
                                    <option selected="selected" value="Show All">Tümünü Göster</option>
                                    <option value="Shipped">Kargolandı</option>
                                    <option value="Confirmed">Onaylandı</option>
                                    <option value="Rejected">Reddedildi</option>
                                    <option value="Pending">Beklemede</option>
                                </select>
                                <!--end::Select-->
                            </div>
                            end::Durum
                            begin::Ara
                            <div class="position-relative my-1">
                                <i class="ki-outline ki-magnifier fs-2 position-absolute top-50 translate-middle-y ms-4"></i>
                                <input class="form-control w-150px fs-7 ps-12" data-kt-table-widget-4="search" placeholder="Search" type="text"/>
                            </div>
                            end::Ara
                        </div>
                        <!--begin::Filters-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-2">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_table_widget_4_table">
                        <!--begin::Table head-->
                        <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Sipariş No</th>
                            <th class="text-end min-w-100px">Oluşturulma</th>
                            <th class="text-end min-w-125px">Müşteri</th>
                            <th class="text-end min-w-100px">Toplam</th>
                            <th class="text-end min-w-100px">Kâr</th>
                            <th class="text-end min-w-50px">Durum</th>
                            <th class="text-end"></th>
                        </tr>
                        <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-600">
                        <tr class="d-none" data-kt-table-widget-4="subtable_template">
                            <td colspan="2">
                                <div class="d-flex align-items-center gap-3">
                                    <a class="symbol symbol-50px bg-secondary bg-opacity-25 rounded" href="#">
                                        <img alt="" data-kt-src-path="assets/media/stock/ecommerce/" data-kt-table-widget-4="template_image" src=""/>
                                    </a>
                                    <div class="d-flex flex-column text-muted">
                                        <a class="text-gray-800 text-hover-primary fw-bold" data-kt-table-widget-4="template_name" href="#">Product name</a>
                                        <div class="fs-7" data-kt-table-widget-4="template_description">Product description</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="text-gray-800 fs-7">Maliyet</div>
                                <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_cost">1</div>
                            </td>
                            <td class="text-end">
                                <div class="text-gray-800 fs-7">Adet</div>
                                <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_qty">1</div>
                            </td>
                            <td class="text-end">
                                <div class="text-gray-800 fs-7">Toplam</div>
                                <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_total">name</div>
                            </td>
                            <td class="text-end">
                                <div class="text-gray-800 fs-7 me-3">Stokta</div>
                                <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_stock">32</div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#XGY-346</a>
                            </td>
                            <td class="text-end">7 dk önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Albert Flores</a>
                            </td>
                            <td class="text-end">₺630.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺86.70</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-warning">Beklemede</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#YHD-047</a>
                            </td>
                            <td class="text-end">52 dk önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Jenny Wilson</a>
                            </td>
                            <td class="text-end">₺25.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺4.20</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-primary">Onaylandı</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#SRR-678</a>
                            </td>
                            <td class="text-end">1 saat önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Robert Fox</a>
                            </td>
                            <td class="text-end">₺1,630.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺203.90</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-warning">Beklemede</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#PXF-534</a>
                            </td>
                            <td class="text-end">3 saat önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Cody Fisher</a>
                            </td>
                            <td class="text-end">₺119.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺12.00</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-success">Kargolandı</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#XGD-249</a>
                            </td>
                            <td class="text-end">2 gün önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Arlene McCoy</a>
                            </td>
                            <td class="text-end">₺660.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺52.26</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-success">Kargolandı</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#SKP-035</a>
                            </td>
                            <td class="text-end">2 gün önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Eleanor Pena</a>
                            </td>
                            <td class="text-end">₺290.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺29.00</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-danger">Reddedildi</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="text-gray-800 text-hover-primary" href="apps/ecommerce/catalog/edit-product.html">#SKP-567</a>
                            </td>
                            <td class="text-end">7 dk önce</td>
                            <td class="text-end">
                                <a class="text-gray-600 text-hover-primary" href="#">Dan Wilson</a>
                            </td>
                            <td class="text-end">₺590.00</td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bolder">₺50.00</span>
                            </td>
                            <td class="text-end">
                                <span class="badge py-3 px-4 fs-7 badge-light-success">Kargolandı</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row" type="button">
                                    <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                    <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Table Widget 4-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
@endsection

@section('js')
    <script src="{{ theme_asset('admin', 'plugins/custom/vis-timeline/vis-timeline.bundle.js') }}"></script>
    <script src="{{ theme_asset('admin', 'js/widgets.bundle.js') }}"></script>
    <script src="{{ theme_asset('admin', 'js/widgets.bundle.js') }}"></script>
    <script src="{{ theme_asset('admin', 'js/custom/apps/chat/chat.js') }}"></script>
@endsection
