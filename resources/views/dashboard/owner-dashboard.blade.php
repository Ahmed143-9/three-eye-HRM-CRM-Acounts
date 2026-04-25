@extends('layouts.admin')
@section('page-title')
    {{__('Owner Dashboard')}}
@endsection

@push('script-page')
    <script>
        (function () {
            var options = {
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 3,
                    curve: 'smooth'
                },
                series: [{
                    name: "{{__('Income')}}",
                    data: {!! json_encode($incExpBarChartData['income']) !!}
                }, {
                    name: "{{__('Expense')}}",
                    data: {!! json_encode($incExpBarChartData['expense']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($incExpBarChartData['month']) !!},
                },
                colors: ['#6fd943', '#ff3a6e'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
            };
            var chart = new ApexCharts(document.querySelector("#owner-income-expense-chart"), options);
            chart.render();
        })();

        (function () {
            var options = {
                chart: {
                    height: 250,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: [{{ $activeEmployees }}, {{ $inactiveEmployees }}],
                colors: ['#007bff', '#6c757d'],
                labels: ["{{__('Active')}}", "{{__('Inactive')}}"],
                legend: {
                    show: true,
                    position: 'bottom'
                }
            };
            var chart = new ApexCharts(document.querySelector("#employee-distribution-chart"), options);
            chart.render();
        })();
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Owner Insights')}}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Welcome Header -->
        <div class="col-sm-12 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="h3 mb-1 fw-bold text-primary">{{ __('Welcome Back, ') }} {{ Auth::user()->name }}!</h2>
                    <p class="text-muted mb-0">{{ __('Here is what is happening with your business today.') }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $statusColor }} p-2 px-3 rounded-pill shadow-sm">
                        <i class="ti ti-activity me-1"></i> {{ __('Status: ') }} {{ __($status) }}
                    </span>
                    <div class="text-muted small mt-1">{{ date('l, d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="col-xxl-12">
            <div class="row gy-4 mb-4">
                <!-- Total Income -->
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm border-0 h-100 overflow-hidden">
                        <div class="card-body position-relative">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                                    <i class="ti ti-trending-up fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0 small uppercase fw-bold">{{ __('Total Income') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ \Auth::user()->priceFormat($income) }}</h3>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expense -->
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm border-0 h-100 overflow-hidden">
                        <div class="card-body position-relative">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-3">
                                    <i class="ti ti-trending-down fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0 small uppercase fw-bold">{{ __('Total Expense') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ \Auth::user()->priceFormat($expense) }}</h3>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit/Loss -->
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm border-0 h-100 overflow-hidden">
                        <div class="card-body position-relative">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-{{ $profit >= 0 ? 'success' : 'danger' }} bg-opacity-10 p-3 rounded-3 text-{{ $profit >= 0 ? 'success' : 'danger' }} me-3">
                                    <i class="ti ti-{{ $profit >= 0 ? 'wallet' : 'alert-triangle' }} fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0 small uppercase fw-bold">{{ __('Profit / Loss') }}</h6>
                                    <h3 class="mb-0 fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ \Auth::user()->priceFormat($profit) }}
                                    </h3>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-{{ $profit >= 0 ? 'success' : 'danger' }}" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm border-0 h-100 overflow-hidden">
                        <div class="card-body position-relative">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning me-3">
                                    <i class="ti ti-clock fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0 small uppercase fw-bold">{{ __('Pending Payments') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ \Auth::user()->priceFormat($pendingPayments) }}</h3>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="col-xxl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">{{ __('Financial Overview') }}</h5>
                        <div class="text-muted small">{{ __('Monthly Income vs Expense') }}</div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="owner-income-expense-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">{{ __('HRM Insights') }}</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div id="employee-distribution-chart"></div>
                    <div class="mt-4 pt-2 border-top">
                        <div class="row">
                            <div class="col-6 border-end">
                                <h4 class="mb-0 fw-bold text-primary">{{ $totalEmployees }}</h4>
                                <p class="text-muted small mb-0">{{ __('Total Staff') }}</p>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0 fw-bold text-success">{{ $activeEmployees }}</h4>
                                <p class="text-muted small mb-0">{{ __('Active Now') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card { transition: transform 0.2s ease-in-out; }
        .card:hover { transform: translateY(-5px); }
        .uppercase { text-transform: uppercase; letter-spacing: 1px; }
        .bg-opacity-10 { background-color: rgba(var(--bs-primary-rgb), 0.1); }
    </style>
@endsection
