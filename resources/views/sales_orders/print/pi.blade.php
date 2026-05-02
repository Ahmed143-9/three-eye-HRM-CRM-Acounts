@extends('layouts.print', ['title' => 'Proforma Invoice - ' . $order->pi->pi_number])

@section('content')
<div class="header">
    <div class="logo">
        <h3>PROFORMA INVOICE</h3>
    </div>
    <div class="header-info">
        <p><strong>PI Number:</strong> {{ $order->pi->pi_number }}</p>
        <p><strong>Client PI #:</strong> {{ $order->pi->client_pi_number ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $order->pi->pi_date }}</p>
        <p><strong>HS Code:</strong> {{ $order->pi->hs_code }}</p>
        <p><strong>Incoterms:</strong> {{ $order->pi->incoterm ?? 'N/A' }}</p>
    </div>
</div>

<div class="row" style="display: flex; gap: 20px;">
    <div style="flex: 1;">
        <h5 class="section-title">Seller Details</h5>
        <p>
            <strong>Name:</strong> {{ $order->pi->seller_name }}<br>
            <strong>Address:</strong> {{ $order->pi->seller_address }}<br>
            <strong>Mobile:</strong> {{ $order->pi->seller_mobile }}<br>
            <strong>Email:</strong> {{ $order->pi->seller_email }}
        </p>
    </div>
    <div style="flex: 1;">
        <h5 class="section-title">Buyer Details</h5>
        <p>
            <strong>Name:</strong> {{ $order->pi->buyer_name }}<br>
            <strong>Address:</strong> {{ $order->pi->buyer_address }}<br>
            <strong>Mobile:</strong> {{ $order->pi->buyer_mobile }}<br>
            <strong>Email:</strong> {{ $order->pi->buyer_email }}
        </p>
    </div>
</div>

<h5 class="section-title">Order Details</h5>
<table class="table-items">
    <thead>
        <tr>
            <th>Item</th>
            <th>Description</th>
            <th>QTY</th>
            <th>Unit</th>
            <th>Price per Unit</th>
            <th>Unit</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->po->items as $item)
        <tr>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit_id }}</td>
            <td>{{ number_format($item->price_per_unit, 2) }}</td>
            <td>{{ $item->currency_type }}</td>
            <td>{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right;">Grand Total</th>
            <th>{{ number_format($order->po->grand_total, 2) }}</th>
        </tr>
    </tfoot>
</table>

<div class="row" style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="flex: 1;">
        <h5 class="section-title">Banking Details</h5>
        <p>
            <strong>Bank Name:</strong> {{ $order->pi->bank_name }}<br>
            <strong>Account Name:</strong> {{ $order->pi->account_name }}<br>
            <strong>Branch:</strong> {{ $order->pi->branch_name }}<br>
            <strong>Account No.:</strong> {{ $order->pi->account_no }}<br>
            <strong>SWIFT Code:</strong> {{ $order->pi->swift_code }}
        </p>
    </div>
    <div style="flex: 1;">
        <h5 class="section-title">Key Conditions</h5>
        <table>
            <tr>
                <td><strong>Validity (Days):</strong> {{ $order->pi->validity }}</td>
                <td><strong>Lifting Time:</strong> {{ $order->pi->lifting_time }}</td>
            </tr>
            <tr>
                <td><strong>Payment Terms:</strong> {{ $order->pi->payment_terms }}</td>
                <td><strong>Tolerance:</strong> {{ $order->pi->tolerance }}</td>
            </tr>
            <tr>
                <td><strong>Port of Loading:</strong> {{ $order->pi->port_of_loading }}</td>
                <td><strong>Port of Discharge:</strong> {{ $order->pi->port_of_discharge }}</td>
            </tr>
            <tr>
                <td><strong>Country of Origin:</strong> {{ $order->pi->country_of_origin }}</td>
            </tr>
        </table>
    </div>
</div>

@php
    $controller = new \App\Http\Controllers\SalesOrderController();
    $amountInWords = $controller->numberToWords($order->po->grand_total);
@endphp
<div class="mt-3">
    <p><strong>Amount in Words:</strong> {{ strtoupper($amountInWords) }} ONLY</p>
</div>

@if($order->pi->terms_and_conditions)
    <div class="mt-4">
        <h5 class="section-title">{{ __('Terms and Conditions') }}</h5>
        <p style="white-space: pre-line;">{{ $order->pi->terms_and_conditions }}</p>
    </div>
@endif

<div class="footer">
    <div class="signature-box">
        Authorized Signature
    </div>
</div>
@endsection
