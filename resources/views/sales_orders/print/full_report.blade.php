@extends('layouts.admin')
@section('page-title')
    {{__('Full Order Report')}} - {{ $order->order_number }}
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="#" onclick="window.print();" class="btn btn-sm btn-primary">
            <i class="ti ti-printer"></i> {{__('Print / Download PDF')}}
        </a>
    </div>
@endsection

@section('content')
    <div id="printable-report" class="bg-white p-5 rounded shadow">
        {{-- Header --}}
        <div class="row mb-5 border-bottom pb-3">
            <div class="col-6">
                <h3 class="fw-bold">{{ __('FULL ORDER SUMMARY') }}</h3>
                <h5 class="text-muted">{{ $order->order_number }}</h5>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0"><strong>{{ __('Generated On:') }}</strong> {{ date('d M, Y') }}</p>
                <p class="mb-0"><strong>{{ __('Status:') }}</strong> {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        {{-- Section 1: Buying & PO --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 mb-2">{{ __('1. Buying Details') }}</h6>
                @if($order->buying)
                    <p class="mb-1"><strong>{{ __('Supplier:') }}</strong> {{ $order->buying->supplier_name }}</p>
                    <p class="mb-1"><strong>{{ __('Total Amount:') }}</strong> {{ number_format($order->buying->total_amount, 2) }}</p>
                @else
                    <p class="text-muted italic">{{ __('No buying data.') }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 mb-2">{{ __('2. Purchase Order (PO)') }}</h6>
                @if($order->po)
                    <p class="mb-1"><strong>{{ __('Customer:') }}</strong> {{ $order->customer->name }}</p>
                    <p class="mb-1"><strong>{{ __('Grand Total:') }}</strong> {{ number_format($order->po->grand_total, 2) }}</p>
                    <p class="mb-1"><strong>{{ __('Items:') }}</strong> 
                        @foreach($order->po->items as $item)
                            {{ $item->item_name }} ({{ $item->quantity }} {{ $item->unit }}){{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </p>
                @else
                    <p class="text-muted italic">{{ __('No PO data.') }}</p>
                @endif
            </div>
        </div>

        {{-- Section 2: PI & LC --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 mb-2">{{ __('3. Proforma Invoice (PI)') }}</h6>
                @if($order->pi)
                    <p class="mb-1"><strong>{{ __('PI Number:') }}</strong> {{ $order->pi->pi_number }}</p>
                    <p class="mb-1"><strong>{{ __('PI Date:') }}</strong> {{ $order->pi->pi_date }}</p>
                    <p class="mb-1"><strong>{{ __('Incoterm:') }}</strong> {{ $order->pi->incoterm }}</p>
                @else
                    <p class="text-muted italic">{{ __('No PI data.') }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 mb-2">{{ __('4. Letter of Credit (LC)') }}</h6>
                @if($order->lc)
                    <p class="mb-1"><strong>{{ __('LC Ref:') }}</strong> {{ $order->lc->lc_reference_no }}</p>
                    <p class="mb-1"><strong>{{ __('LC Date:') }}</strong> {{ $order->lc->lc_date }}</p>
                    <p class="mb-1"><strong>{{ __('Latest Shipment:') }}</strong> {{ $order->lc->latest_shipment_date }}</p>
                @else
                    <p class="text-muted italic">{{ __('No LC data.') }}</p>
                @endif
            </div>
        </div>

        {{-- Section 3: Shipment Batches --}}
        <h6 class="fw-bold border-bottom pb-1 mb-3">{{ __('5. Shipment Batches (Deliveries)') }}</h6>
        @php
            $totalOrdered = $order->po ? $order->po->items->sum('quantity') : 0;
            $totalDelivered = $order->cis->flatMap->tankers->sum('quantity_mt');
        @endphp
        
        <div class="alert alert-info py-2 mb-3">
            <strong>{{ __('Shipment Progress:') }}</strong> {{ number_format($totalDelivered, 3) }} / {{ number_format($totalOrdered, 3) }} MT 
            ({{ $totalOrdered > 0 ? number_format(($totalDelivered/$totalOrdered)*100, 1) : 0 }}%)
        </div>

        @forelse($order->cis as $ci)
            <div class="card mb-3 border">
                <div class="card-header bg-light py-2">
                    <strong>{{ __('Shipment Batch:') }} {{ $ci->ci_number }}</strong> ({{ $ci->ci_date }})
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1 small"><strong>{{ __('Tankers:') }}</strong> 
                                @foreach($ci->tankers as $t)
                                    {{ $t->tanker_number }} ({{ $t->quantity_mt }} MT){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 small"><strong>{{ __('Consignment:') }}</strong> 
                                {{ $ci->consignmentNote ? __('Uploaded') : __('Pending') }}
                                @if($ci->consignmentNote)
                                    - {{ $ci->consignmentNote->weightSlips->count() }} {{ __('Weight Slips') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 small"><strong>{{ __('Delivery:') }}</strong> 
                                @if($ci->delivery)
                                    {{ $ci->delivery->delivery_mode }} | {{ $ci->delivery->packing_type }} | {{ $ci->delivery->drum_qty }} {{ $ci->delivery->drum_unit }}
                                @else
                                    {{ __('Not Created') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted italic">{{ __('No shipments created yet.') }}</p>
        @endforelse

        {{-- Footer --}}
        <div class="mt-5 text-center text-muted small border-top pt-3">
            <p>{{ __('This is a system generated report for internal auditing and record keeping.') }}</p>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-report, #printable-report * { visibility: visible; }
            #printable-report { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
            .action-btn, .breadcrumb, .navbar, .footer { display: none !important; }
        }
    </style>
@endsection
