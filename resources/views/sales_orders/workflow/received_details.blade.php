@extends('sales_orders.layout')

@php
    $activeStep = 'RD';
    $title = __('Received Details');
@endphp

@section('header-actions')
    {{-- No specific print for RD yet --}}
@endsection

@section('workflow-content')
    @include('sales_orders.steps.received_details')
@endsection

@section('footer-buttons')
    @if($order->status != 'finalized')
        <button type="submit" form="workflow-form" class="btn btn-success shadow-sm px-4">
            {{ __('Save Received Details') }} <i class="ti ti-device-floppy ms-1"></i>
        </button>
    @endif
@endsection
