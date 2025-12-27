@props(['id','active'=>'genel-bakis'])

@php
    $is = fn($key) => $active === $key ? 'active' : '';
@endphp

<div class="card mb-10 position-sticky top-0 z-1">
    <div class="card-body">
        <ul class="nav flex-wrap border-transparent fw-bold">
            <li class="nav-item my-1">
                <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ $is('genel-bakis') }}"
                   href="{{ route(config('system.admin_prefix').'.personel.profil', ['id'=>$id]) }}">
                    Genel Bakış
                </a>
            </li>
            <li class="nav-item my-1">
                <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ $is('satislar') }}"
                   href="{{ route(config('system.admin_prefix').'.personel.satislar', ['id'=>$id]) }}">
                    Satışlar
                </a>
            </li>
            <li class="nav-item my-1">
                <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ $is('performans') }}"
                   href="{{ route(config('system.admin_prefix').'.personel.performans', ['id'=>$id]) }}">
                    Performans
                </a>
            </li>
            <li class="nav-item my-1">
                <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ $is('gorusmeler') }}"
                   href="{{ route(config('system.admin_prefix').'.personel.gorusmeler', ['id'=>$id]) }}">
                    Görüşmeler
                </a>
            </li>
        </ul>
    </div>
</div>
