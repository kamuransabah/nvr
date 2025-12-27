@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', $profil['isim'].' '.$profil['soyisim'].' - SATIŞLAR')
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
                <a href="javascript:;" id="overview_daterange" class="btn btn-light me-4">
                    <i class="bi bi-calendar-range me-2"></i>
                    <span class="text-muted">Tarih:</span>
                    <span id="overview_daterange_text" class="fw-semibold"></span>
                </a>
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

            <x-admin.personel.navbar :id="$profil->id" active="satislar" />

            <!-- start:content -->
            {{-- === KPI Kartları (3 yan yana) === --}}
            <div class="row g-5 g-xl-10 mb-5">

                {{-- Toplam Satış --}}
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-basket2 fs-2 me-2 text-info mb-1"></i>
                                <div class="text-muted">Toplam Satış</div>
                            </div>
                            <span class="fw-bold" style="font-size:clamp(28px, 5vw, 40px); line-height:1;">
                    {{ number_format($totalOrders ?? 0) }}
                </span>
                            <span class="text-muted fs-8 mt-2">adet</span>
                        </div>
                    </div>
                </div>

                {{-- Toplam Ciro --}}
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-cash-stack fs-2 me-2 text-success mb-1"></i>
                                <div class="text-muted">Ciro</div>
                            </div>
                            <span class="fw-bold" style="font-size:clamp(28px, 5vw, 40px); line-height:1;">
                    {{ $totalRevenueShort }} ₺
                </span>
                            <span class="text-muted fs-8 mt-2">{{ $totalRevenueFull }}</span>
                        </div>
                    </div>
                </div>

                {{-- Ortalama Sepet (yalnızca bu veri) --}}
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-percent fs-2 me-2 text-warning mb-1"></i>
                                <div class="text-muted">Ortalama Satış Tutarı</div>
                            </div>
                            <span class="fw-bold" style="font-size:clamp(24px, 4vw, 34px); line-height:1;">
                    {{ $avgOrderValueShort }} ₺
                </span>
                            <span class="text-muted fs-8 mt-2">{{ $avgOrderValueFull }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- === Grafikler: 6 Aylık Adet & Ciro (yan yana) === --}}
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Aylık Satış Adedi (Son 6 Ay)</h3></div>
                        <div class="card-body"><canvas id="monthlyOrdersChart" height="140"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Aylık Ciro (Son 6 Ay)</h3></div>
                        <div class="card-body"><canvas id="monthlyRevenueChart" height="140"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- === Kurslara göre satışlar (yatay bar, kurs isimleri solda) === --}}
            @if($courseChartEnabled)
                <div class="row g-5 g-xl-10 mb-5">
                    <div class="col-12">
                        <div class="card card-flush h-100">
                            <div class="card-header">
                                <h3 class="card-title">Kurslara Göre Satışlar
                                    @if($from || $to)
                                        <span class="text-muted fs-7 ms-2">(tarih filtresi uygulanmıştır)</span>
                                    @endif
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="coursesChart" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- === Son 10 Sipariş Tablosu === --}}
            {{-- Son 10 Sipariş (sade tablo) --}}
            {{-- Son 10 Sipariş (sade tablo) --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son 10 Sipariş</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr class="fw-semibold text-gray-600">
                                <th>Sipariş No</th>
                                <th>Öğrenci</th>
                                <th>Kurs</th>
                                <th>Tutar</th>
                                <th>Tarih</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($lastOrders ?? [] as $o)
                                <tr>
                                    <td>{{ $o->siparis_no }}</td>
                                    <td>
                                        <a href="{{ route(config('system.admin_prefix').'.ogrenci.profil', $o->user_id) }}" class="fw-semibold" target="_blank">
                                        {{ $o->ogrenci ?? trim(($o->isim ?? '').' '.($o->soyisim ?? '')) ?: '-' }}
                                        </a>
                                    </td>
                                    <td>{{ $o->kurslar ?? $o->kurs_adi ?? '-' }}</td>
                                    <td>{{ number_format($o->odenecek_tutar, 2, ',', '.') }} ₺</td>
                                    <td>
                                        @php $dt = $o->odeme_tarihi ?? $o->created_at; @endphp
                                        {{ $dt ? \Illuminate\Support\Carbon::parse($dt)->format('d.m.Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Kayıt bulunamadı.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- end:content -->

        </div>
        <!--end::Content-->
    </div>
@endsection

@section('js')
    <script>
        (function() {
            // --- Daterangepicker butonu (Genel Bakış ile uyumlu) ---
            // moment & daterangepicker yüklenmiş olmalı (profile sayfasındaki gibi bundle'dan)
            var elBtn   = document.getElementById('overview_daterange');
            var elText  = document.getElementById('overview_daterange_text');
            var fromPhp = @json($from); // "YYYY-MM-DD" veya null
            var toPhp   = @json($to);

            function setRangeText(start, end) {
                if (!window.moment || !start || !end) {
                    elText && (elText.textContent = (fromPhp && toPhp) ? (fromPhp + ' - ' + toPhp) : 'Tümü');
                    return;
                }
                var s = moment(start).format('DD MMM YYYY');
                var e = moment(end).format('DD MMM YYYY');
                elText && (elText.textContent = s + ' - ' + e);
            }

            // İlk metni bas
            setRangeText(fromPhp, toPhp);

            if (window.$ && $.fn.daterangepicker && elBtn) {
                var start = fromPhp ? moment(fromPhp) : moment().startOf('month');
                var end   = toPhp   ? moment(toPhp)   : moment().endOf('day');

                $(elBtn).daterangepicker({
                    startDate: start,
                    endDate: end,
                    opens: 'left',
                    autoApply: true,
                    locale: { format: 'YYYY-MM-DD', applyLabel: 'Uygula', cancelLabel: 'İptal' },
                    ranges: {
                        'Bugün': [moment(), moment()],
                        'Dün': [moment().subtract(1,'days'), moment().subtract(1,'days')],
                        'Bu Hafta': [moment().startOf('week'), moment().endOf('week')],
                        'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                        'Son 30 Gün': [moment().subtract(29,'days'), moment()],
                        'Geçen Ay': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                    }
                }, function(start, end) {
                    // Metni güncelle
                    setRangeText(start, end);

                    // URL paramlarını güncelle & sayfayı yenile
                    var url = new URL(window.location.href);
                    var qs  = url.searchParams;
                    qs.set('from', start.format('YYYY-MM-DD'));
                    qs.set('to',   end.format('YYYY-MM-DD'));
                    // sayfalama paramlarını temizlemek isteyebilirsin
                    qs.delete('page');
                    window.location.search = qs.toString();
                });
            }

            // --- Chart.js init ---
            const labels     = @json($labels ?? []);
            const dataCount  = @json($dataCount ?? []);
            const dataAmount = @json($dataAmount ?? []);

            // Aylık Adet (Bar)
            new Chart(document.getElementById('monthlyOrdersChart'), {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Sipariş Adedi', data: dataCount }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Aylık Ciro (Line)
            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'line',
                data: { labels: labels, datasets: [{ label: 'Ciro (₺)', data: dataAmount, tension: 0.35, fill: false }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Kurslara göre (yatay bar: kurs adları Y ekseninde, sayılar X ekseninde)
            @if($courseChartEnabled)
            new Chart(document.getElementById('coursesChart'), {
                type: 'bar',
                data: {
                    labels: @json($courseLabels),       // kurs adları
                    datasets: [{ label: 'Satış Adedi', data: @json($courseCounts) }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // yatay bar
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
            @endif
        })();
    </script>
@endsection
