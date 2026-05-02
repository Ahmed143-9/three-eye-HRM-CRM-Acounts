@extends('sales_orders.layout')

@php
    $activeStep = 'CN';
    $title = __('Consignment Note');
@endphp

@section('header-actions')
    @if($order->consignmentNote)
        <div class="d-flex gap-2">
            <a href="{{ route('sales-orders.cn.print', $order->id) }}" target="_blank" class="btn btn-sm btn-secondary">
                <i class="ti ti-printer me-1"></i>{{ __('Print') }}
            </a>
            <a href="{{ route('sales-orders.cn.download', $order->id) }}" class="btn btn-sm btn-info">
                <i class="ti ti-download me-1"></i>{{ __('PDF') }}
            </a>
        </div>
    @endif
@endsection

@section('workflow-content')
    @include('sales_orders.steps.cn')
@endsection

@section('footer-buttons')
    <button type="submit" form="workflow-form" class="btn btn-primary shadow-sm px-4">
        {{ __('Save & Proceed to Received') }} <i class="ti ti-arrow-right ms-1"></i>
    </button>
@endsection
