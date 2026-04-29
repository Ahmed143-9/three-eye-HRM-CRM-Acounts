@extends('sales_orders.layout')

@php
    $activeStep = 'PL';
    $title = __('Packing List');
@endphp

@section('header-actions')
    @if($order->packingList)
        <div class="d-flex gap-2">
            <a href="{{ route('sales-orders.pl.print', $order->id) }}" target="_blank" class="btn btn-sm btn-secondary">
                <i class="ti ti-printer me-1"></i>{{ __('Print') }}
            </a>
            <a href="{{ route('sales-orders.pl.download', $order->id) }}" class="btn btn-sm btn-info">
                <i class="ti ti-download me-1"></i>{{ __('PDF') }}
            </a>
        </div>
    @endif
@endsection

@section('workflow-content')
    @include('sales_orders.steps.pl')
@endsection

@section('footer-buttons')
    <button type="submit" form="workflow-form" class="btn btn-primary shadow-sm px-4">
        {{ __('Save & Proceed to CN') }} <i class="ti ti-arrow-right ms-1"></i>
    </button>
@endsection
