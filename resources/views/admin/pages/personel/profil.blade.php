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

            <x-admin.personel.navbar :id="$profil->id" active="genel-bakis" />

            <!-- start:content -->
            <!-- begin: Genel Bakış -->
            {{-- KPI Row: responsive 2x2 / 4x1 --}}
            <div class="row g-5">
                <!-- Satış Adedi -->
                <div class="col-12 col-md-6 col-xxl-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-basket2 fs-2 me-2 text-primary"></i>
                                <div class="text-muted">Satış Adedi</div>
                            </div>
                            <div class="fs-2hx fw-bold" id="kpi_salesCount">0</div>
                            <div class="text-muted" id="kpi_salesCount_delta_wrap">
                                <span id="kpi_salesCount_delta">0%</span> önceki döneme göre
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ciro -->
                <div class="col-12 col-md-6 col-xxl-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-stack fs-2 me-2 text-success"></i>
                                <div class="text-muted">Ciro</div>
                            </div>
                            <div class="fs-2hx fw-bold" id="kpi_revenue">₺0</div>
                            <div class="text-muted" id="kpi_revenue_delta_wrap">
                                <span id="kpi_revenue_delta">0%</span> önceki döneme göre
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dönüşüm Oranı -->
                <div class="col-12 col-md-6 col-xxl-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-graph-up fs-2 me-2 text-info"></i>
                                <div class="text-muted">Dönüşüm Oranı</div>
                            </div>
                            <div class="fs-2hx fw-bold" id="kpi_conversion">0%</div>
                            <div class="text-muted" id="kpi_conversion_delta_wrap">
                                <span id="kpi_conversion_delta">0%</span> önceki döneme göre
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Trend: tam satır --}}
            <div class="row g-5 mt-1 mb-10">
                <div class="col-12">
                    <div class="card card-flush h-100">
                        <div class="card-header"><div class="card-title">Günlük Trend (Data & Satış)</div></div>
                        <div class="card-body"><div id="chart_trend" style="height:360px;"></div></div>
                    </div>
                </div>
            </div>

            <!-- start:görüşme istatistikleri -->
            <div class="row">
                <div class="col-xl-4 col-12">
                    <div class="card card-flush">
                        <div class="card-body">
                            <div class="text-muted">Görüşme Sayısı</div>
                            <div class="fs-2hx fw-bold" id="kpi_callsCount">0</div>
                            <div class="text-muted"><span id="kpi_callsCount_delta">0%</span> önceki döneme göre</div>
                        </div>
                    </div>
                    <div class="card card-flush">
                        <div class="card-body">
                            <div class="text-muted">Günlük Ortalama Görüşme Sayısı</div>
                            <div class="fs-2hx fw-bold" id="kpi_callsCountAvarange">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-12">
                    <div class="card card-flush">
                        <div class="card-header"><div class="card-title">Kurslara Göre Görüşme Sayısı</div></div>
                        <div class="card-body"><div id="chart_calls_by_course" style="height:280px;"></div></div>
                    </div>
                </div>
            </div>
            <!-- end:görüşme istatistikleri -->

            {{-- Alt satır: Funnel + Kurs özetleri --}}
            <div class="row g-5 mt-1">
                <div class="col-lg-4">
                    <div class="card card-flush h-100">
                        <div class="card-header"><div class="card-title">Genel Bakış</div></div>
                        <div class="card-body">
                            <div id="chart_funnel"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card card-flush">
                        <div class="card-header"><div class="card-title">Kurslara Göre Data Sayısı</div></div>
                        <div class="card-body"><div id="chart_lead_by_course" style="height:280px;"></div></div>
                    </div>
                </div>
            </div>
            <div class="row g-5 mt-1">
                <div class="col-lg-4">
                    <div class="card card-flush">
                        <div class="card-body">
                            <div class="text-muted">Ortalama Satış Tutarı</div>
                            <div class="fs-2hx fw-bold" id="kpi_avgTicket">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card card-flush">
                        <div class="card-header"><div class="card-title">En Çok Satılan Kurslar (Adet)</div></div>
                        <div class="card-body"><div id="chart_top_course_sales" style="height:320px;"></div></div>
                    </div>
                </div>
            </div>
            <!-- end: Genel Bakış -->

            <!-- end:content -->


        </div>
        <!--end::Content-->
    </div>
@endsection

@section('js')


    <script>
        (function(){

            moment.locale('tr');
            const personelId = {{ (int)($profil->id) }};
            const metricsUrl = "{{ route(config('system.admin_prefix').'.personel.metrics', ['id' => $profil->id]) }}";

            const $btn  = document.getElementById('overview_daterange');
            const $text = document.getElementById('overview_daterange_text');

            const initialStart = moment().subtract(29,'day').startOf('day');
            const initialEnd   = moment().endOf('day');
            $text.textContent  = initialStart.format('YYYY-MM-DD') + ' - ' + initialEnd.format('YYYY-MM-DD');

            function fmtMoney(v){ return new Intl.NumberFormat('tr-TR',{style:'currency',currency:'TRY',maximumFractionDigits:0}).format(v||0); }
            function fmtPercent(v){ return (v??0) + '%'; }

            let trendChart, funnelChart, topCourseChart, leadByCourseChart, callsByCourseChart;

            // Line: Trend
            function renderTrend(labels, leads, sales){
                const el = document.getElementById('chart_trend');
                if(!trendChart){
                    trendChart = new ApexCharts(el, {
                        chart:{ type:'line', height:360, toolbar:{show:false} },
                        stroke:{ width:3 },
                        xaxis:{
                            categories: labels,
                            labels: {
                                formatter: function(value) {
                                    return moment(value).format('DD MMM'); // örn: 10 Ağu
                                }
                            }
                        },
                        series:[
                            { name:'Data',  data: leads },
                            { name:'Satış', data: sales },
                        ]
                    });
                    trendChart.render();
                }else{
                    trendChart.updateOptions({ xaxis:{ categories: labels } });
                    trendChart.updateSeries([{name:'Data',data:leads},{name:'Satış',data:sales}]);
                }
            }

            // Donut: Funnel
            function renderFunnel(funnel){
                const el = document.getElementById('chart_funnel');
                const labels = Object.keys(funnel);
                const values = Object.values(funnel);
                if(!funnelChart){
                    funnelChart = new ApexCharts(el, {
                        chart:{ type:'donut', height:320 },
                        labels, series: values, legend:{ position:'bottom' }
                    });
                    funnelChart.render();
                }else{
                    funnelChart.updateOptions({ labels });
                    funnelChart.updateSeries(values);
                }
            }

            // HBar helper
            function renderHBar(elId, labels, values, instance, h=280){
                const el = document.getElementById(elId);
                if(!instance){
                    const chart = new ApexCharts(el, {
                        chart:{ type:'bar', height:h, toolbar:{show:false} },
                        plotOptions:{ bar:{ horizontal:true, barHeight:'60%' } },
                        dataLabels:{ enabled:true },
                        xaxis:{ categories: labels },
                        series:[{ name:'Adet', data: values }],
                    });
                    chart.render();
                    return chart;
                } else {
                    instance.updateOptions({ xaxis:{ categories: labels } });
                    instance.updateSeries([{ name:'Adet', data: values }]);
                    return instance;
                }
            }

            function renderRecent(containerId, items, formatter){
                const el = document.getElementById(containerId);
                if(!el) return;
                if(!items || !items.length){ el.innerHTML = '<div class="text-muted">Kayıt yok.</div>'; return; }
                el.innerHTML = items.map(formatter).join('');
            }

            function loadMetrics(start, end){
                const url = metricsUrl + '?start='+encodeURIComponent(start)+'&end='+encodeURIComponent(end);

                fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest'} })
                    .then(r => r.json())
                    .then(d => {
                        // KPIs (değerler)
                        document.getElementById('kpi_salesCount').textContent = d?.kpis?.salesCount?.value ?? 0;
                        document.getElementById('kpi_revenue').textContent    = fmtMoney(d?.kpis?.revenue?.value ?? 0);
                        document.getElementById('kpi_conversion').textContent = fmtPercent(d?.kpis?.conversion?.value ?? 0);
                        document.getElementById('kpi_callsCount').textContent = d?.kpis?.callsCount?.value ?? 0;
                        document.getElementById('kpi_avgTicket').textContent  = fmtMoney(d?.kpis?.avgTicket?.value ?? 0);

                        // KPIs (delta'lar: renk + ok ikonu)
                        updateDelta('kpi_salesCount_delta', d?.kpis?.salesCount?.delta ?? 0);
                        updateDelta('kpi_revenue_delta',    d?.kpis?.revenue?.delta ?? 0);
                        updateDelta('kpi_conversion_delta', d?.kpis?.conversion?.delta ?? 0);
                        updateDelta('kpi_callsCount_delta', d?.kpis?.callsCount?.delta ?? 0);

                        // Günlük Ortalama Görüşme
                        const elCallsAvg = document.getElementById('kpi_callsCountAvarange');
                        if (elCallsAvg) elCallsAvg.textContent = d?.kpis?.callsAvg?.value ?? 0;

                        // Charts
                        renderTrend(d?.chart?.labels ?? [], d?.chart?.leads ?? [], d?.chart?.sales ?? []);
                        renderFunnel(d?.funnel ?? {});

                        // Courses (Top Sales)
                        const sLabels = (d?.courses?.topSales ?? []).map(i => i.ad);
                        const sValues = (d?.courses?.topSales ?? []).map(i => i.adet);
                        topCourseChart = renderHBar('chart_top_course_sales', sLabels, sValues, topCourseChart, 320);

                        // Courses (Lead)
                        const lLabels = (d?.courses?.leadByCourse ?? []).map(i => i.ad);
                        const lValues = (d?.courses?.leadByCourse ?? []).map(i => i.adet);
                        leadByCourseChart = renderHBar('chart_lead_by_course', lLabels, lValues, leadByCourseChart, 280);

                        // Courses (Calls)
                        const cLabels = (d?.courses?.callsByCourse ?? []).map(i => i.ad);
                        const cValues = (d?.courses?.callsByCourse ?? []).map(i => i.adet);
                        callsByCourseChart = renderHBar('chart_calls_by_course', cLabels, cValues, callsByCourseChart, 280);
                    })
                    .catch(err => {
                        console.error('metrics fetch error:', err);
                    });
            }


            // Date range picker
            $($btn).daterangepicker({
                startDate: initialStart,
                endDate: initialEnd,
                opens: 'left',
                locale: { format:'YYYY-MM-DD' },
                ranges:{
                    'Bugün':[moment(),moment()],
                    'Dün':[moment().subtract(1,'day'),moment().subtract(1,'day')],
                    'Son 7 Gün':[moment().subtract(6,'day'),moment()],
                    'Son 30 Gün':[moment().subtract(29,'day'),moment()],
                    'Bu Ay':[moment().startOf('month'),moment().endOf('month')],
                    'Geçen Ay':[moment().subtract(1,'month').startOf('month'),moment().subtract(1,'month').endOf('month')]
                }
            }, function(start, end){
                $text.textContent = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                loadMetrics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            });

            // İlk yükleme
            $text.textContent = initialStart.format('YYYY-MM-DD') + ' - ' + initialEnd.format('YYYY-MM-DD');
            loadMetrics(initialStart.format('YYYY-MM-DD'), initialEnd.format('YYYY-MM-DD'));
        })();

        function updateDelta(idBase, value) {
            const wrap = document.getElementById(idBase + '_wrap');
            if (!wrap) return;

            const abs = Math.abs(Number(value || 0)).toFixed(2); // örn: 23.36
            let cls = 'text-secondary', icon = 'bi-dash';

            if (value > 0) { cls = 'text-success'; icon = 'bi-arrow-up-short'; }
            else if (value < 0) { cls = 'text-danger'; icon = 'bi-arrow-down-short'; }

            wrap.innerHTML = `
              <span class="${cls}">
                <i class="bi ${icon} me-1"></i>
                <span id="${idBase}">${abs}%</span>
              </span>
              <span class="text-muted ms-1">önceki döneme göre</span>
            `;
        }

        // kullanımı:
        updateDelta('kpi_salesCount_delta', d.kpis.salesCount.delta);
        updateDelta('kpi_revenue_delta', d.kpis.revenue.delta);
        updateDelta('kpi_conversion_delta', d.kpis.conversion.delta);
    </script>



@endsection
