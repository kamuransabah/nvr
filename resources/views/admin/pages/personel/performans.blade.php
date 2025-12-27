@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', $profil['isim'].' '.$profil['soyisim'].' - PROFİL')
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
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Personeller</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Personel Detay</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <a href="{{ route(config('system.admin_prefix').'.personel.index') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-bars"></i> Personeller</a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="d-flex flex-column flex-xl-row">
        <!--begin::Sidebar-->
        <x-admin.personel.sidebar :profil="$profil" :personel="$personel ?? null" class="me-lg-10" />
        <!--end::Sidebar-->
        <!--begin::Content-->
        <div class="flex-lg-row-fluid">

            <x-admin.personel.navbar :id="$profil->id" active="performans" />

            <!-- start:content -->
            <!-- start:content -->

            {{-- Hafta sonu toggle (aynı kalsın) --}}
            <div class="d-flex justify-content-end mb-6">
                <form method="get">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="weekends" value="1" id="weekendsToggle" {{ $includeWeekends ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label" for="weekendsToggle">Hafta sonlarını dahil et</label>
                    </div>
                </form>
            </div>

            {{-- KPI Kartları (3’lü) --}}
            <div class="row g-5 g-xl-10 mb-5">

                {{-- Bu Ay Satış Adedi --}}
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-gray-600 fw-semibold fs-7 mb-2">Bu Ay Satış Adedi (ödenen)</div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold" style="font-size:clamp(28px,5vw,40px);line-height:1;">
                                    {{ number_format($mtdCnt) }}
                                </span>
                                @php
                                    $d = $mtdCnt - $mtdPrevCnt;
                                    $p = $mtdPrevCnt ? round(($d / $mtdPrevCnt) * 100, 1) : null;
                                @endphp
                                @if(!is_null($p) && $p != 0)
                                    <span class="ms-3">
                                        @if($p > 0)
                                            <i class="bi bi-arrow-up text-success"></i>
                                            <span class="text-success">{{ $p }}%</span>
                                        @else
                                            <i class="bi bi-arrow-down text-danger"></i>
                                            <span class="text-danger">{{ abs($p) }}%</span>
                                        @endif
                        </span>
                                @endif
                            </div>
                            <div class="text-muted fs-8 mt-2">Geçen Ay: {{ number_format($mtdPrevCnt) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Bu Ay Ciro --}}
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-gray-600 fw-semibold fs-7 mb-2">Bu Ay Ciro</div>
                            <div class="d-flex align-items-center">
                    <span class="fw-bold" style="font-size:clamp(28px,5vw,40px);line-height:1;">
                        {{ $short($mtdRev) }} ₺
                    </span>
                                @php
                                    $d = $mtdRev - $mtdPrevRev;
                                    $p = $mtdPrevRev ? round(($d / $mtdPrevRev) * 100, 1) : null;
                                @endphp
                                @if(!is_null($p) && $p != 0)
                                    <span class="ms-3">
                            @if($p > 0)
                                            <i class="bi bi-arrow-up text-success"></i>
                                            <span class="text-success">{{ $p }}%</span>
                                        @else
                                            <i class="bi bi-arrow-down text-danger"></i>
                                            <span class="text-danger">{{ abs($p) }}%</span>
                                        @endif
                        </span>
                                @endif
                            </div>
                            <div class="text-muted fs-8 mt-2">Geçen Ay: {{ $fmtMoney($mtdPrevRev) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Performans Notu (0–100) --}}
                <div class="col-xl-4 col-md-6">
                    @php
                        $above = ($perfScore ?? 0) >= ($perfScoreAvg ?? 50);
                        $colorText  = $above ? 'text-success' : 'text-danger';
                        $badgeClass = $above ? 'badge-light-success' : 'badge-light-danger';
                        $icon       = $above ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
                    @endphp
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-gray-600 fw-semibold fs-7 mb-2">Performans Notu (0–100)</div>
                            <div class="d-flex align-items-center">
                <span class="fw-bold {{ $colorText }}" style="font-size:clamp(28px,5vw,42px);line-height:1;">
                    {{ $perfScore ?? 0 }}
                </span>
                                <span class="ms-3 badge {{ $badgeClass }}">
                    <i class="{{ $icon }}"></i>
                    @php
                        $delta = ($perfScoreAvg ?? 0) ? round(($perfScore - $perfScoreAvg), 1) : 0;
                    @endphp
                                    {{ $delta >= 0 ? '+' : '' }}{{ $delta }}
                </span>
                            </div>
                            <div class="text-muted fs-8 mt-2">
                                Şirket ortalaması: {{ number_format($perfScoreAvg ?? 50, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bu Ay – Ekip Karşılaştırma (kişi başı ortalamalar) --}}
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-12">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h3 class="card-title">Bu Ay – Ekip Karşılaştırma (Kişi Başı Ortalama)</h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row gx-5">
                                @if(!is_null($teamMtdAvgMeetings))
                                    <div class="col-xl-4 col-md-6">
                                        <div class="d-flex align-items-center p-4 rounded bg-light">
                                            <div class="symbol symbol-40px me-4">
                                                <span class="symbol-label bg-white">
                                                    <i class="bi bi-people text-info fs-3"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-gray-600 fs-7">Ortalama Görüşme</div>
                                                <div class="fw-bold fs-3">{{ number_format($teamMtdAvgMeetings, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-xl-4 col-md-6">
                                    <div class="d-flex align-items-center p-4 rounded bg-light">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-white">
                                                <i class="bi bi-basket text-primary fs-3"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 fs-7">Ortalama Satış</div>
                                            <div class="fw-bold fs-3">{{ number_format($teamMtdAvgCnt, 2) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="d-flex align-items-center p-4 rounded bg-light">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-white">
                                                <i class="bi bi-currency-exchange text-success fs-3"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 fs-7">Ortalama Ciro</div>
                                            <div class="fw-bold fs-3">{{ number_format($teamMtdAvgRev, 2, ',', '.') }} ₺</div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- row --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafikler tam genişlik --}}
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-12">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Günlük Trend (son 60 iş günü)</h3></div>
                        <div class="card-body"><canvas id="dailyTrend" height="300"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-12">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Aylık Satış Adedi (YoY karşılaştırmalı)</h3></div>
                        <div class="card-body"><canvas id="monthlyTrend" height="300"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- end:content -->

            <!-- start:bilgilendirme mesajı -->
            {{-- Yönetici Notları (sade açıklamalar) --}}
            <div class="card card-flush mt-10">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Yönetici Notları
                    </h3>
                </div>
                <div class="card-body pt-0">
                    {{-- Kısa özet --}}
                    <div class="alert alert-primary d-flex align-items-center p-5 mb-7">
                        <i class="bi bi-info-circle fs-2hx me-4"></i>
                        <div>
                            <div class="fw-semibold mb-1">Bu sayfa satış performansını hızlı ve anlaşılır şekilde sunar.</div>
                            <div class="text-gray-700">Yalnızca tahsilatı tamamlanmış satışlar dikkate alınır. İptal/iade edilenler dahil edilmez.</div>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="bi bi-calendar-range fs-2 me-3 text-primary"></i>
                                <div>
                                    <div class="fw-bold mb-1">Tarih mantığı</div>
                                    <ul class="text-gray-700 mb-0">
                                        <li><strong>Bu Ay</strong>: Ayın başından bugüne kadar olan tahsilatlar.</li>
                                        <li><strong>Geçen Ay</strong>: Bir önceki ayın tamamı.</li>
                                        <li><strong>Günlük Trend</strong>: Son 60 <em>iş günü</em>. Üstteki anahtarla hafta sonlarını dahil/hariç edebilirsiniz.</li>
                                        <li><strong>YoY</strong>: Geçen yılın aynı ayıyla kıyas.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="bi bi-trophy fs-2 me-3 text-success"></i>
                                <div>
                                    <div class="fw-bold mb-1">Performans notu (0–100)</div>
                                    <ul class="text-gray-700 mb-0">
                                        <li>Varsayılan olarak <strong>geçen aya</strong> göre hesaplanır.</li>
                                        <li>Üç ölçüye bakar: <em>Satış adedi</em>, <em>Ciro</em>, <em>Görüşme sayısı</em>.</li>
                                        <li>Ağırlıklar: Satış %30 · Ciro %60 · Görüşme %10.</li>
                                        <li>Adil kıyas için, ilgili dönemde <strong>en az 10 görüşmesi olan</strong> personellerle karşılaştırılır.</li>
                                        <li>Yüksek not, ekibe göre güçlü bir performans demektir.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="bi bi-graph-up-arrow fs-2 me-3 text-success"></i>
                                <div>
                                    <div class="fw-bold mb-1">Oklar ve renkler</div>
                                    <ul class="text-gray-700 mb-0">
                                        <li><span class="text-success"><i class="bi bi-arrow-up"></i> Yeşil</span>: Geçen aya göre artış.</li>
                                        <li><span class="text-danger"><i class="bi bi-arrow-down"></i> Kırmızı</span>: Geçen aya göre düşüş.</li>
                                        <li>Yüzdeler değişimin oranını gösterir; kârlılık bilgisi vermez.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="bi bi-people fs-2 me-3 text-info"></i>
                                <div>
                                    <div class="fw-bold mb-1">Ekip ortalamaları</div>
                                    <ul class="text-gray-700 mb-0">
                                        <li>“Ortalama” değerler, aktif personelin kişi başı performansıdır.</li>
                                        <li>Çok düşük aktivite (10 görüşmenin altında) ortalamaya dahil edilmez.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex">
                                <i class="bi bi-question-circle fs-2 me-3 text-warning"></i>
                                <div>
                                    <div class="fw-bold mb-1">Sık sorulan</div>
                                    <ul class="text-gray-700 mb-0">
                                        <li><strong>Sayılar neden değişebilir?</strong> Hafta sonu ayarı veya geç/erken tahsilatlar etkileyebilir.</li>
                                        <li><strong>Veriler ne kadar güncel?</strong> Tahsilat kaydı yapıldığında anlık olarak yansır.</li>
                                        <li><strong>Görüşme verisi yoksa?</strong> Performans notu, satış ve ciro ölçülerinden hesaplanır.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> {{-- /row --}}
                </div>
            </div>

            <!-- end:bilgilendirme mesajı -->


        </div>
        <!--end::Content-->
    </div>
@endsection

@section('js')

    <script>
        (function() {
            // Günlük Trend
            const dailyLabels = @json($dailyLabels);
            const dailyPerson = @json($dailyPerson);
            const dailyTeam   = @json($dailyTeamAvg);

            new Chart(document.getElementById('dailyTrend'), {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [
                        { label: 'Personel (adet)', data: dailyPerson, tension: 0.3, fill: false },
                        { label: 'Ekip kişi başı ort. (adet)', data: dailyTeam, tension: 0.3, fill: false }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Aylık Trend (YoY)
            const monthLabels = @json($monthLabels);
            const monthCnt    = @json($monthCnt);
            const monthCntLy  = @json($monthCntLy);

            new Chart(document.getElementById('monthlyTrend'), {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [
                        { label: 'Bu yıl (adet)', data: monthCnt },
                        { label: 'Geçen yıl aynı ay (adet)', data: monthCntLy }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        })();
    </script>

@endsection
