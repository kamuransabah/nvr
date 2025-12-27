@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Sipariş Detay - '.$siparis->siparis_no)
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
@endsection

@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Siparişler</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <li class="breadcrumb-item text-gray-900">Sipariş Detay</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')

    <div class="d-flex flex-column gap-7 gap-lg-10">
        <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
            <!--begin:::Tabs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-lg-n2 me-auto">
                <!--begin:::Tab item-->
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#siparis_detay">Sipariş Detayı</a>
                </li>
                <!--end:::Tab item-->
                <!--begin:::Tab item-->
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#siparis_gecmis">Sipariş Hareketleri</a>
                </li>
                <!--end:::Tab item-->
            </ul>
            <!--end:::Tabs-->
            <!--begin::Button-->
            <a href="apps/ecommerce/sales/listing.html" class="btn btn-icon btn-light btn-active-secondary btn-sm ms-auto me-lg-n7">
                <i class="ki-outline ki-left fs-2"></i>
            </a>
            <!--end::Button-->
            <!--begin::Button-->
            <a href="{{ route(config('system.admin_prefix').'.siparis.edit', ['id' => $siparis->id]) }}" class="btn btn-primary btn-sm me-lg-n7" data-bs-toggle="tooltip" title="Düzenle">
                <i class="fa-solid fa-edit"></i> Siparişi Düzenle</a>
            <!--end::Button-->

            <form action="{{ route(config('system.admin_prefix').'.siparis.delete', ['id' => $siparis->id]) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Sil" data-confirm-delete="true">
                    <i class="fa-solid fa-trash-can"></i> Siparişi Sil
                </button>
            </form>
        </div>
        <!--begin::Order summary-->
        <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
            <!--begin::Order details-->
            <div class="card card-flush py-4 flex-row-fluid">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Sipariş Detay</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                            <tbody class="fw-semibold text-gray-600">
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-calendar fs-2 me-2"></i>Oluşturma Tarihi</div>
                                </td>
                                <td class="fw-bold text-end">{{ \Carbon\Carbon::parse($siparis->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-wallet fs-2 me-2"></i>Ödeme Yöntemi</div>
                                </td>
                                <td class="fw-bold text-end">
                                    {{ $siparis->odemeTuru->value ?? '--' }}
                                    <i class="fa-solid fa-wallet w-50px ms-2"></i>
                                </td>

                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-truck fs-2 me-2"></i>Kargo Yöntemi</div>
                                </td>
                                <td class="fw-bold text-end">{{ status()->get($siparis->kargo_turu, 'text', 'kargo_turu') }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Order details-->
            <!--begin::Customer details-->
            <div class="card card-flush py-4 flex-row-fluid">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Müşteri Bilgileri</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                            <tbody class="fw-semibold text-gray-600">
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-profile-circle fs-2 me-2"></i>Üye</div>
                                </td>
                                <td class="fw-bold text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-25px overflow-hidden me-3">
                                            <a href="{{ route(config('system.admin_prefix').'.ogrenci.profil', $siparis->user_id) }}">
                                                <div class="symbol-label">
                                                    <img src="{{ userAvatar($siparis->uye->profil_resmi, 'ogrenci') }}" alt="Üye" class="w-100" />
                                                </div>
                                            </a>
                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Name-->
                                        <a href="{{ route(config('system.admin_prefix').'.ogrenci.profil', $siparis->user_id) }}" class="text-gray-600 text-hover-primary">{{ $siparis->uye->isim.' '.$siparis->uye->soyisim }}</a>
                                        <!--end::Name-->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-sms fs-2 me-2"></i>E-Posta</div>
                                </td>
                                <td class="fw-bold text-end">
                                    <span class="text-gray-600 text-hover-primary">{{ $siparis->uye->email }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-phone fs-2 me-2"></i>Telefon</div>
                                </td>
                                <td class="fw-bold text-end">{{ $siparis->uye->telefon }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Customer details-->
            <!--begin::Documents-->
            <div class="card card-flush py-4 flex-row-fluid">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Belgeler</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                            <tbody class="fw-semibold text-gray-600">
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-devices fs-2 me-2"></i>Sipariş No
                                    </div>
                                </td>
                                <td class="fw-bold text-end">
                                    <a href="#" class="text-gray-600 text-hover-primary">{{ $siparis->siparis_no }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-truck fs-2 me-2"></i>Kargo Kodu
                                        <span class="ms-1" data-bs-toggle="tooltip" title="View the shipping manifest generated by this order.">
                                            <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="fw-bold text-end">
                                    <a href="#" class="text-gray-600 text-hover-primary">
                                        {{ $siparis->kargo_turu == 0
                                            ? status()->get($siparis->kargo_turu, 'text', 'kargo_turu')
                                            : ($siparis->kargo->kargo_kodu ?? '-') }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-discount fs-2 me-2"></i>İndirim Kodu
                                    </div>
                                </td>
                                <td class="fw-bold text-end"><i class="fa-solid fa-ban"></i></td>
                            </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Documents-->
        </div>
        <!--end::Order summary-->


        <!--begin::Tab content-->
        <div class="tab-content">
            <!--begin::Tab pane-->
            <div class="tab-pane fade show active" id="siparis_detay" role="tab-panel">
                <!--begin::Orders-->
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                        <!--begin::Payment address-->
                        <div class="card card-flush py-4 flex-row-fluid position-relative">
                            <!--begin::Background-->
                            <div class="position-absolute top-0 end-0 bottom-0 opacity-10 d-flex align-items-center me-5">
                                <i class="ki-solid ki-two-credit-cart" style="font-size: 14em"></i>
                            </div>
                            <!--end::Background-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Fatura Adresi</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                {{ $siparis->uye->adres ?? 'Adres bilgisi bulunamadı' }}
                                <br />{{ $siparis->uye->il?->il ?? '--' }}
                                <br />{{ $siparis->uye->ilce?->ilce ?? '--' }}
                            </div>
                        </div>
                        <!--end::Payment address-->
                        <!--begin::Shipping address-->
                        <div class="card card-flush py-4 flex-row-fluid position-relative">
                            <!--begin::Background-->
                            <div class="position-absolute top-0 end-0 bottom-0 opacity-10 d-flex align-items-center me-5">
                                <i class="ki-solid ki-delivery" style="font-size: 13em"></i>
                            </div>
                            <!--end::Background-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Teslimat Adresi</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                {{ $siparis->uye->adres ?? 'Adres bilgisi bulunamadı' }}
                                <br />{{ $siparis->uye->il?->il ?? '--' }}
                                <br />{{ $siparis->uye->ilce?->ilce ?? '--' }}
                            </div>
                        </div>
                        <!--end::Shipping address-->
                    </div>
                    <!--begin::Product List-->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Satın Alınan Ürünler</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            @if($siparis->urunListesi() && count($siparis->urunListesi()) > 0)
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                    <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-175px">Ürün</th>
                                        <th class="min-w-70px text-end">Adet</th>
                                        <th class="min-w-100px text-end">Birim Fiyat</th>
                                        <th class="min-w-70px text-end">KDV</th>
                                        <th class="min-w-100px text-end">Toplam</th>
                                    </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">

                                    @foreach($siparis->urunListesi() as  $urun)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="" class="symbol symbol-50px">
                                                        <span class="symbol-label" style="background-image:url({{ $urun['resim'] }});"></span>
                                                    </a>
                                                    <div class="ms-5">
                                                        <a href="#" class="fw-bold text-gray-600 text-hover-primary">{{ $urun['baslik'] }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $urun['adet'] }}</td>
                                            <td class="text-end">₺ {{ $urun['birim_fiyat'] }}</td>
                                            <td class="text-end"><small>(%{{ $urun['kdv'] }})</small> ₺ {{ kdv($urun['toplam'], $urun['kdv'], 'hesapla') }}</td>
                                            <td class="text-end">₺ {{ $urun['toplam'] }}</td>
                                        </tr>
                                    @endforeach


                                    <tr>
                                        <td colspan="4" class="text-end">Ara Toplam</td>
                                        <td class="text-end">₺ {{ number_format($siparis->toplam_siparis_tutari, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">İskonto</td>
                                        <td class="text-end">₺ 0</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">Kargo Bedeli</td>
                                        <td class="text-end">₺ 0</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="fs-3 text-gray-900 text-end">Toplam Tutar</td>
                                        <td class="text-gray-900 fs-3 fw-bolder text-end">₺ {{ $urun['toplam'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!--end::Product List-->
                </div>
            </div>
            <!--end::Tab pane-->
            <!--begin::Tab pane-->
            <div class="tab-pane fade" id="siparis_gecmis" role="tab-panel">
                <!--begin::Orders-->
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <!--begin::Order history-->
                    <div class="card card-flush py-4 flex-row-fluid">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Sipariş Hareketleri</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @if($siparis->gecmis && count($siparis->gecmis) > 0)
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-100px">Tarih</th>
                                            <th class="min-w-175px">Personel</th>
                                            <th class="min-w-70px">Durum</th>
                                            <th>Personel Notu</th>
                                        </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                        @foreach($siparis->gecmis as $gecmis)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($gecmis->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm') }}</td>
                                            <td>{{ $gecmis->personel->isim.' '.$gecmis->personel->soyisim }}</td>
                                            <td><span class="badge badge-light-secondary">{{ $gecmis->siparisDurum->value }}</span></td>
                                            <td>{{ $gecmis->personel_notu }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                veri yok
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Order history-->
                    <!--begin::Order data-->
                    <div class="card card-flush py-4 flex-row-fluid">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Sipariş Data Bilgileri</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                                    <tbody class="fw-semibold text-gray-600">
                                    <tr>
                                        <td class="text-muted">IP Adresi</td>
                                        <td class="fw-bold text-end">{{ $siparis->ip_adresi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Ödeme Yapan IP Adresi</td>
                                        <td class="fw-bold text-end">{{ $siparis->ip_adresi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">User Agent</td>
                                        <td class="fw-bold text-end">{{ $siparis->user_agent }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Personel Notu</td>
                                        <td class="fw-bold text-end">{{ $siparis->personel_notu }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!--end::Table-->
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Order data-->
                </div>
                <!--end::Orders-->
            </div>
            <!--end::Tab pane-->
        </div>
        <!--end::Tab content-->
    </div>

@endsection

@section('js')

@endsection
