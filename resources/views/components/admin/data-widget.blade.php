<div class="card card-xl-stretch mb-xl-8">
    <div class="card-body d-flex flex-column p-0">
        <div class="d-flex flex-stack flex-grow-1 card-p">
            <div class="d-flex flex-column me-2">
                <a href="#" class="text-gray-900 text-hover-{{ $color }} fw-bold fs-3">{{ $title }}</a>
                <span class="text-muted fw-semibold mt-1">{{ $subtitle }}</span>
            </div>
            <span class="symbol symbol-50px">
                <span class="symbol-label fs-5 fw-bold bg-light-{{ $color }} text-{{ $color }}">{{ $value }}</span>
            </span>
        </div>
        <div class="data-widget-chart card-rounded-bottom"
             data-kt-chart-color="{{ $color }}"
             data-kt-chart-key="{{ $key }}"
             style="height: 150px"></div>
    </div>
</div>
