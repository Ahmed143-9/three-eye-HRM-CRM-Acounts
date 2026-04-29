@extends('layouts.admin')

@section('page-title')
    {{ $title }} - {{ $order->order_number }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">{{ __('Sales Orders') }}</a></li>
    <li class="breadcrumb-item">{{ $order->order_number }}</li>
    <li class="breadcrumb-item">{{ $title }}</li>
@endsection

@push('css-page')
<style>
    .workflow-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3rem;
        position: relative;
    }
    .workflow-steps::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }
    .step-item {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1;
    }
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .step-item.active .step-icon {
        border-color: #584ed2;
        background: #584ed2;
        color: #fff;
        box-shadow: 0 0 0 5px rgba(88, 78, 210, 0.2);
    }
    .step-item.completed .step-icon {
        border-color: #28a745;
        background: #28a745;
        color: #fff;
    }
    .step-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
    }
    .step-item.active .step-label {
        color: #584ed2;
    }
    .card-workflow {
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
    }
    .card-workflow .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem;
    }
    .card-workflow .card-body {
        padding: 2rem;
    }
    .nav-buttons {
        margin-top: 2rem;
        display: flex;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Step Indicators -->
            <div class="workflow-steps">
                @php
                    $steps = [
                        'PO' => ['name' => __('PO'), 'route' => 'sales-orders.po'],
                        'PI' => ['name' => __('PI'), 'route' => 'sales-orders.pi'],
                        'LC' => ['name' => __('LC'), 'route' => 'sales-orders.lc'],
                        'CI' => ['name' => __('CI'), 'route' => 'sales-orders.ci'],
                        'PL' => ['name' => __('PL'), 'route' => 'sales-orders.pl'],
                        'CN' => ['name' => __('CN'), 'route' => 'sales-orders.cn'],
                        'RD' => ['name' => __('Received'), 'route' => 'sales-orders.rd'],
                    ];
                    $currentIdx = array_search($activeStep, array_keys($steps));
                    $totalSteps = count($steps);
                @endphp

                @foreach($steps as $key => $step)
                    @php
                        $loopIdx = array_search($key, array_keys($steps));
                        $isCompleted = false;
                        if($key == 'PO' && $order->po) $isCompleted = true;
                        if($key == 'PI' && $order->pi) $isCompleted = true;
                        if($key == 'LC' && $order->lc) $isCompleted = true;
                        if($key == 'CI' && $order->ci) $isCompleted = true;
                        if($key == 'PL' && $order->packingList) $isCompleted = true;
                        if($key == 'CN' && $order->consignmentNote) $isCompleted = true;
                        if($key == 'RD' && ($order->status == 'completed' || $order->status == 'finalized')) $isCompleted = true;
                        
                        $statusClass = '';
                        if ($key == $activeStep) $statusClass = 'active';
                        elseif ($isCompleted) $statusClass = 'completed';
                    @endphp
                    
                    <div class="step-item {{ $statusClass }}">
                        <div class="step-icon">
                            @if($isCompleted && $key != $activeStep)
                                <i class="ti ti-check"></i>
                            @else
                                {{ $loopIdx + 1 }}
                            @endif
                        </div>
                        <div class="step-label">{{ $step['name'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="card card-workflow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ $title }}</h4>
                        <p class="text-muted mb-0">{{ __('Step') }} {{ $currentIdx + 1 }} {{ __('of') }} {{ $totalSteps }}</p>
                    </div>
                    @yield('header-actions')
                </div>
                <div class="card-body">
                    @yield('workflow-content')
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    <div class="nav-buttons">
                        @php
                            $prevStep = null;
                            $keys = array_keys($steps);
                            if ($currentIdx > 0) $prevStep = $steps[$keys[$currentIdx - 1]];
                        @endphp

                        <div>
                            @if($prevStep)
                                <a href="{{ route($prevStep['route'], $order->id) }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i> {{ __('Previous:') }} {{ $prevStep['name'] }}
                                </a>
                            @endif
                        </div>
                        
                        <div>
                            @yield('footer-buttons')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('sales_orders.workflow_scripts')
@endsection
