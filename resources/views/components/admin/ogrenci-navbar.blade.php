<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-100px symbol-fixed position-relative">
                    <img src="{{ userAvatar($ogrenci->profil_resmi ?? null) }}" alt="avatar" />
                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $ogrenci->isim.' '.$ogrenci->soyisim }}</a>
                            <a href="#">
                                <i class="ki-outline ki-verify fs-1 text-{{ status()->get($ogrenci->durum) }}"></i>
                            </a>
                        </div>
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-profile-circle fs-4 me-1"></i>{{ $personel->isim.' '.$personel->soyisim }}</a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-geolocation fs-4 me-1"></i> {{ $ogrenci->il?->il }} / {{ $ogrenci->ilce?->ilce }}</a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-sms fs-4"></i>{{ $ogrenci->email }}</a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                <i class="ki-outline ki-phone fs-4"></i>{{ $ogrenci->telefon }}</a>
                        </div>
                    </div>
                    <div class="d-flex my-4">
                        <a href="#" class="btn btn-sm btn-outline btn-outline-primary btn-outline-dashed me-2">SMS</a>
                        <a href="#" class="btn btn-sm btn-outline btn-outline-warning btn-outline-dashed">E-Posta</a>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">

            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/profil/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.profil', ['id' => $ogrenci->id]) }}"><i
                        class="fas fa-user me-2"></i>Profil</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/kurslar/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.kurslar.index', ['id' => $ogrenci->id]) }}"><i
                        class="fas fa-book-open me-2"></i>Kurslar</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/siparisler/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.siparisler.index', ['id' => $ogrenci->id]) }}"><i
                        class="far fa-credit-card me-2"></i>Siparişler</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/belgeler/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.belgeler.index', ['id' => $ogrenci->id]) }}"><i
                        class="fa-regular fa-file-lines me-2"></i>Belgeler</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/sinavlar/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.sinavlar.index', ['id' => $ogrenci->id]) }}"><i class="fa-solid fa-check-to-slot me-2"></i>Sınavlar</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/sertifikalar/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.sertifikalar.index', ['id' => $ogrenci->id]) }}"><i class="fa-solid fa-certificate me-2"></i>Sertifikalar</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/loglar/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.loglar.index', ['id' => $ogrenci->id]) }}"><i class="fa-solid fa-clock-rotate-left me-2"></i>Log Kayıtları</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5  {{ request()->is(config('system.admin_prefix').'/ogrenci/notlar/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.notlar.index', ['id' => $ogrenci->id]) }}"><i class="fa-solid fa-user-pen me-2"></i>Personel Notları</a>
            </li>

            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->is(config('system.admin_prefix').'/ogrenci/edit/*') ? 'active' : '' }}"
                   href="{{ route(config('system.admin_prefix').'.ogrenci.edit', $ogrenci->id) }}"><i class="fa-solid fa-address-card me-2"></i>Profil Bilgileri</a>
            </li>
        </ul>

    </div>
</div>
