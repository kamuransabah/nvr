@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Data Listesi')
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
@endsection

@section('toolbar')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Data</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Data İstatistik</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Wrapper-->
                <div class="me-4">
                    <a  class="btn btn-sm btn-flex btn-light btn-active-primary fw-bold" data-bs-toggle="collapse" href="#filtrebox" role="button" aria-expanded="false" aria-controls="filtrebox">
                        <i class="ki-outline ki-filter fs-5 text-gray-500 me-1"></i>Filtrele
                    </a>
                </div>
                <!--end::Wrapper-->
                <!--begin::Button-->
                <a href="#" data-bs-toggle="modal" data-bs-target="#dataekle" class="btn btn-sm btn-primary">Data Ekle</a>

                <!--end::Button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')

    <!--begin::Chart widget 36-->
    <div class="card card-flush overflow-hidden mb-5">
        <!--begin::Header-->
        <div class="card-header pt-5">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">Performance</span>
                <span class="text-gray-500 mt-1 fw-semibold fs-6">1,046 Inbound Calls today</span>
            </h3>
            <!--end::Title-->
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" data-kt-daterangepicker-range="today" class="btn btn-sm btn-light d-flex align-items-center px-4">
                    <!--begin::Display range-->
                    <div class="text-gray-600 fw-bold">Loading date range...</div>
                    <!--end::Display range-->
                    <i class="ki-outline ki-calendar-8 text-gray-500 lh-0 fs-2 ms-2 me-0"></i>
                </div>
                <!--end::Daterangepicker-->
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->
        <!--begin::Card body-->
        <div class="card-body d-flex align-items-end p-0">
            <!--begin::Chart-->
            <div id="kt_charts_widget_36" class="min-h-auto w-100 ps-4 pe-6" style="height: 500px"></div>
            <!--end::Chart-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Chart widget 36-->

@endsection

@section('js')
    <script src="{{ theme_asset('admin', 'js/widgets.bundle.js') }}"></script>


    <script>
        "use strict";

        var KTChartsWidget36 = function () {
            var chart = {
                self: null,
                rendered: false
            };

            var initChart = function(chart) {
                var element = document.getElementById("kt_charts_widget_36");

                if (!element) {
                    return;
                }

                var height = parseInt(KTUtil.css(element, 'height'));
                var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
                var baseColors = {
                    primary: KTUtil.getCssVariableValue('--bs-primary'),
                    success: KTUtil.getCssVariableValue('--bs-success'),
                    warning: KTUtil.getCssVariableValue('--bs-warning'),
                    info: KTUtil.getCssVariableValue('--bs-info')
                };
                var lightColors = {
                    primary: KTUtil.getCssVariableValue('--bs-primary-light'),
                    success: KTUtil.getCssVariableValue('--bs-success-light'),
                    warning: KTUtil.getCssVariableValue('--bs-warning-light'),
                    info: KTUtil.getCssVariableValue('--bs-info-light')
                };

                var seriesData = @json($seriesData); // Laravel'den gelen veriyi JavaScript'e aktar

                var options = {
                    series: seriesData,
                    chart: {
                        fontFamily: 'inherit',
                        type: 'bar', // Grafik tipini 'bar' olarak değiştirin
                        height: height,
                        stacked: true, // Yığılmış sütun grafiği için 'true'
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: ['50%'],
                            borderRadius: 5
                        }
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'left',
                        markers: {
                            width: 10,
                            height: 10,
                            strokeWidth: 0,
                            radius: 12
                        },
                        itemMargin: {
                            horizontal: 10
                        },
                        fontSize: '12px',
                        fontFamily: 'inherit',
                        fontWeight: 600,
                        labels: {
                            colors: labelColor
                        }
                    },
                    dataLabels: {
                        enabled: false // İsteğe bağlı olarak sütunların üzerinde değerleri gösterebilirsiniz
                    },
                    stroke: {
                        show: false // Sütunların kenarlıklarını kaldırabilirsiniz
                    },
                    xaxis: {
                        categories: @json($categories), // Laravel'den gelen günleri aktar
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Veri Sayısı',
                            style: {
                                color: labelColor,
                                fontSize: '12px'
                            }
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    states: {
                        normal: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        hover: {
                            filter: {
                                type: 'lighten',
                                value: 0.15
                            }
                        },
                        active: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    },
                    colors: [baseColors.primary, baseColors.success, baseColors.warning, baseColors.info] // Durumlarınıza göre renkleri ayarlayın
                };

                chart.self = new ApexCharts(element, options);

                setTimeout(function() {
                    chart.self.render();
                    chart.rendered = true;
                }, 200);
            }

            return {
                init: function () {
                    initChart(chart);

                    KTThemeMode.on("kt.thememode.change", function() {
                        if (chart.rendered) {
                            chart.self.destroy();
                        }
                        initChart(chart);
                    });
                }
            };
        }();

        if (typeof module !== 'undefined') {
            module.exports = KTChartsWidget36;
        }

        KTUtil.onDOMContentLoaded(function() {
            KTChartsWidget36.init();
        });
    </script>

@endsection
