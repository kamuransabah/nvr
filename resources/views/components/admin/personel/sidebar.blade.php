@props(['profil','personel'=>null,'class'=>''])
<!--begin::Sidebar-->
<div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10 {{ $class }}">
    <div class="card mb-5 mb-xl-8 position-sticky top-0">
        <div class="card-body pt-15">
            <div class="d-flex flex-center flex-column mb-5">
                <div class="symbol symbol-100px symbol-circle mb-7">
                    <img src="{{ userAvatar($profil->profil_resmi ?? null, 'personel') }}" alt="Avatar">
                </div>
                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                    {{ ($profil->isim ?? '').' '.($profil->soyisim ?? '') }}
                </a>
                <div class="fs-5 fw-semibold text-muted mb-6">Personel</div>
            </div>

            <div class="d-flex flex-stack fs-4 py-3">
                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_customer_view_details" aria-expanded="true">
                    Bilgiler <span class="ms-2 rotate-180"><i class="ki-outline ki-down fs-3"></i></span>
                </div>
                <span data-bs-toggle="tooltip" title="Düzenle">
          <a href="{{ route(config('system.admin_prefix').'.personel.edit', ['id' => $profil->id]) }}" class="btn btn-sm btn-light-primary">Bilgileri Düzenle</a>
        </span>
            </div>

            <div class="separator separator-dashed my-3"></div>

            <div id="kt_customer_view_details" class="collapse show">
                <div class="py-5 fs-6">
                    <div class="badge badge-light-{{ status()->get($profil->durum) }} d-inline">
                        {{ status()->get($profil->durum, 'text') }}
                    </div>

                    <div class="fw-bold mt-5">Telefon</div>
                    <div class="text-gray-600 mb-1">
                        {{ $profil->telefon ?? '—' }} <small class="badge badge-light-warning d-inline">Kişisel</small>
                    </div>
                    <div class="text-gray-600">
                        {{ $personel->sirket_telefon ?? '—' }} <small class="badge badge-light-info d-inline">Şirket</small>
                    </div>

                    <div class="fw-bold mt-5">E-Posta</div>
                    <div class="text-gray-600 mb-1">
                        {{ $profil->email ?? '—' }} <small class="badge badge-light-warning d-inline">Kişisel</small>
                    </div>
                    <div class="text-gray-600">
                        {{ $personel->sirket_email ?? '—' }} <small class="badge badge-light-info d-inline">Şirket</small>
                    </div>

                    <div class="fw-bold mt-5">Kayıt Tarihi</div>
                    <div class="text-gray-600">
                        {{ optional($profil->created_at)->format('d.m.Y H:i') }}
                    </div>

                    <div class="fw-bold mt-5">Son Giriş Tarihi</div>
                    <div class="text-gray-600">
                        {{ optional($profil->son_giris_tarihi)->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Sidebar-->
