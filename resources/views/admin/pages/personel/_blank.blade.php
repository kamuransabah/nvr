@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', $profil['isim'].' '.$profil['soyisim'])
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/daterangepicker/daterangepicker.css') }}" />

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
            <a href="javascript:;" id="overview_daterange" class="btn btn-light">
                <i class="bi bi-calendar-range me-2"></i>
                <span class="text-muted">Tarih:</span>
                <span id="overview_daterange_text" class="fw-semibold"></span>
            </a>
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
        <div class="flex-lg-row-fluid ms-lg-15">

            <x-admin.personel.navbar :id="$profil->id" active="genel-bakis" />

            <!-- start:content -->

            <!-- end:content -->

        </div>
        <!--end::Content-->
    </div>
@endsection

@section('js')

    <script src="{{ asset('assets/plugins/custom/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/moment/locale/tr.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/apexcharts/apexcharts.min.js') }}"></script>

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
