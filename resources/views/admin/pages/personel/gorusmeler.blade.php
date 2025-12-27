@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', $profil['isim'].' '.$profil['soyisim'].' - DATA GÖRÜŞME PERFORMANSI')
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
                <form method="get" class="d-flex align-items-center gap-3">
                    <input type="hidden" name="from" id="gm_from" value="{{ $from }}">
                    <input type="hidden" name="to" id="gm_to" value="{{ $to }}">

                    <a href="javascript:;" id="meetings_daterange" class="btn btn-light me-2">
                        <i class="bi bi-calendar-range me-2"></i>
                        <span class="text-muted">Tarih:</span>
                        <span id="meetings_daterange_text" class="fw-semibold"></span>
                    </a>
                </form>
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

            <x-admin.personel.navbar :id="$profil->id" active="gorusmeler" />

            <!-- start:content -->

            {{-- KPI Kartları (3’lü) --}}
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center fs-7 mb-2">
                                <i class="bi bi-telephone-forward fs-2 me-2 text-info mb-1"></i>
                                <div class="fw-bold fs-2">Toplam Görüşme</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                    <span class="fw-bold" style="font-size:clamp(28px,5vw,40px);line-height:1;">
                        {{ number_format($totalMeetings) }}
                    </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center fs-7 mb-2">
                                <i class="bi bi-bank fs-2 me-2 text-success mb-1"></i>
                                <div class="fw-bold fs-2">Kayıt</div>
                            </div>
                            <div class="d-flex align-items-center">
                    <span class="fw-bold" style="font-size:clamp(28px,5vw,40px);line-height:1;">
                        {{ number_format($countKayit) }}
                    </span>
                            </div>
                            <div class="text-muted fs-8 mt-2">Olumlu oranı: {{ $posRate }}%</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar4-event me-2 fs-2 text-primary mb-1"></i>
                                <div class="fw-bold fs-2">Randevu</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold" style="font-size:clamp(28px,5vw,40px);line-height:1;">
                                    {{ number_format($countRandevu) }}
                                </span>
                            </div>
                            <div class="text-muted fs-8 mt-2">Olumsuz oranı: {{ $negRate }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafikler --}}
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-12">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Günlük Görüşme Trendi</h3></div>
                        <div class="card-body"><canvas id="gm_daily" height="140"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Durum Dağılımı</h3></div>
                        <div class="card-body"><canvas id="gm_status" height="220"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header"><h3 class="card-title">Olumsuz Nedenler (Top 5)</h3></div>
                        <div class="card-body">
                            @if(count($negLabels))
                                <canvas id="gm_negative" height="220"></canvas>
                            @else
                                <div class="text-muted">Bu aralıkta olumsuz neden verisi yok.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Son 10 görüşme --}}
            <div class="card card-flush">
                <div class="card-header"><h3 class="card-title">Son Görüşmeler</h3></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed mb-0" style="table-layout:fixed;">
                            <colgroup>
                                <col style="width:24%;">
                                <col style="width:44%;">
                                <col style="width:12%;">
                                <col style="width:20%;">
                            </colgroup>
                            <thead>
                            <tr class="text-muted fw-semibold">
                                <th class="ps-9">İsim</th>
                                <th>Kurs</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($lastMeetings as $r)
                                @php
                                    // Etiket ve renk
                                    $durumEtiketi  = $durumMap[$r->durum] ?? (string)$r->durum;
                                    $badgeTone     = status()->get($r->durum, 'class', 'data') ?? 'secondary'; // ör: success|warning|danger...
                                    $badgeClass    = 'badge badge-light-' . $badgeTone;

                                    // Olumsuz ise neden (aynı hücrede parantez içinde)
                                    $isOlumsuz     = (mb_strtolower($durumEtiketi) === 'olumsuz');
                                    $olumsuzNedeni = $isOlumsuz && $r->olumsuz_id ? ($negMap[$r->olumsuz_id] ?? $r->olumsuz_id) : null;

                                    $dt = \Carbon\Carbon::parse($r->created_at)->locale('tr');
                                    // kısa ve tek parça: "10 dk önce", "1 saat önce", "3 ay önce"
                                    $relative = $dt->diffForHumans(null, false, true, 1);
                                    $absolute = $dt->format('d.m.Y H:i');
                                @endphp
                                <tr>
                                    <td class="ps-9">
                                        <div class="text-truncate" style="max-width: 220px;">{{ $r->isim ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 420px;">{{ $r->kurs_adi ?? '-' }}</div>
                                    </td>
                                    <td class="text-nowrap">
                                        <span data-bs-toggle="tooltip" title="{{ $absolute }}">{{ $relative }}</span>
                                    </td>
                                    <td>
                                        <span class="{{ $badgeClass }}">{{ $durumEtiketi }}
                                        @if($olumsuzNedeni)
                                            <small class="ms-1">({{ $olumsuzNedeni }})</small>
                                        @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-10">Bugün için görüşme bulunamadı</td></tr>
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
        (function(){
            // --- DATERANGE PICKER ---
            const from = moment("{{ $from }}", "YYYY-MM-DD");
            const to   = moment("{{ $to }}",   "YYYY-MM-DD");

            function updateDRText(s, e){
                document.getElementById('meetings_daterange_text').textContent = s.format('DD MMM YYYY') + ' — ' + e.format('DD MMM YYYY');
            }
            updateDRText(from, to);

            // Eğer Metronic'teki daterangepicker yüklüyse:
            if (typeof $ !== 'undefined' && typeof $.fn.daterangepicker === 'function') {
                $('#meetings_daterange').daterangepicker({
                    startDate: from,
                    endDate: to,
                    ranges: {
                        'Bugün': [moment(), moment()],
                        'Dün': [moment().subtract(1,'days'), moment().subtract(1,'days')],
                        'Bu Ay': [moment().startOf('month'), moment()],
                        'Geçen Ay': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')],
                        'Son 30 Gün': [moment().subtract(29,'days'), moment()]
                    },
                    locale: { format: 'DD.MM.YYYY', applyLabel:'Uygula', cancelLabel:'İptal' }
                }, function(start, end){
                    document.getElementById('gm_from').value = start.format('YYYY-MM-DD');
                    document.getElementById('gm_to').value   = end.format('YYYY-MM-DD');
                    document.querySelector('#meetings_daterange').closest('form').submit();
                });
            }

            // --- CHARTS (Chart.js) ---
            if (typeof Chart !== 'undefined') {
                // Günlük trend
                new Chart(document.getElementById('gm_daily').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($dailyLabels),
                        datasets: [{
                            label: 'Görüşme',
                            data: @json($dailyCounts),
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                // Durum dağılımı
                new Chart(document.getElementById('gm_status').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{ label: 'Adet', data: @json($statusCounts) }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
                    }
                });

                // Olumsuz nedenler (Top 5) — varsa
                @if(count($negLabels))
                new Chart(document.getElementById('gm_negative').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($negLabels),
                        datasets: [{ label: 'Adet', data: @json($negCounts) }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { precision:0 } } }
                    }
                });
                @endif
            } else {
                console.warn('Chart.js yüklü değil: grafikler çizilemedi.');
            }
        })();
    </script>

@endsection
