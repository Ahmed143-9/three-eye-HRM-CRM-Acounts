@extends('layouts.print', ['title' => 'Commercial Invoice - ' . $order->ci->ci_number])

@section('content')
<div class="header">
    <div class="logo">
        <h3>COMMERCIAL INVOICE</h3>
    </div>
    <div class="header-info">
        <p><strong>CI Number:</strong> {{ $order->ci->ci_number }}</p>
        <p><strong>CI Date:</strong> {{ $order->ci->ci_date }}</p>
        <p><strong>LC Number:</strong> {{ $order->lc->lc_no }}</p>
    </div>
</div>

<h5 class="section-title">Shipment Summary (Tankers)</h5>
<table>
    <thead>
        <tr>
            <th>Tanker Number</th>
            <th>Quantity (MT)</th>
            <th>CPT (USD)</th>
            <th>Total Amount (USD)</th>
        </tr>
    </thead>
    <tbody>
        @php $totalQty = 0; $totalAmt = 0; @endphp
        @foreach($order->ci->tankers as $tanker)
        <tr>
            <td>{{ $tanker->tanker_number }}</td>
            <td>{{ number_format($tanker->quantity_mt, 3) }}</td>
            <td>{{ number_format($tanker->cpt_usd, 2) }}</td>
            <td>{{ number_format($tanker->total_amount_usd, 2) }}</td>
        </tr>
        @php 
            $totalQty += $tanker->quantity_mt; 
            $totalAmt += $tanker->total_amount_usd; 
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>TOTAL</th>
            <th>{{ number_format($totalQty, 3) }} MT</th>
            <th>-</th>
            <th>USD {{ number_format($totalAmt, 2) }}</th>
        </tr>
    </tfoot>
</table>

<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <p><strong>Total Quantity in MT:</strong> {{ number_format($totalQty, 3) }} MT</p>
        <p><strong>Total Invoice Amount:</strong> USD {{ number_format($totalAmt, 2) }}</p>
    </div>
</div>

@php
    $controller = new \App\Http\Controllers\SalesOrderController();
    $amountInWords = $controller->numberToWords($totalAmt);
@endphp
<div class="mt-3">
    <p><strong>Amount in Words:</strong> USD {{ strtoupper($amountInWords) }} ONLY</p>
</div>

<div class="footer">
    <div class="signature-box">
        Authorized Signature
    </div>
</div>
@endsection
