@extends('layouts.print', ['title' => 'Purchase Order - ' . $order->id])

@section('content')
<div class="header">
    <div class="logo">
        <h3>PURCHASE ORDER</h3>
    </div>
    <div class="header-info">
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Date:</strong> {{ date('d M, Y') }}</p>
        <p><strong>HS Code:</strong> {{ $order->po->hs_code ?? 'N/A' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <h5 class="section-title">Client Information</h5>
        <p>
            <strong>Name:</strong> {{ $order->po->client_name }}<br>
            <strong>Address:</strong> {{ $order->po->client_address }}<br>
            <strong>Email:</strong> {{ $order->po->client_email }}<br>
            <strong>Phone:</strong> {{ $order->po->client_phone }}
        </p>
    </div>
</div>

<h5 class="section-title">Items Section (Supplier)</h5>
<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->po->items as $item)
        <tr>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="text-align: right;">Grand Total</th>
            <th>{{ $order->po->grand_total }}</th>
        </tr>
    </tfoot>
</table>

@php
    $controller = new \App\Http\Controllers\SalesOrderController();
    $amountInWords = $controller->numberToWords($order->po->grand_total);
@endphp
<div class="mt-3">
    <p><strong>Amount in Words:</strong> {{ strtoupper($amountInWords) }} ONLY</p>
</div>

<div class="footer">
    <div class="signature-box">
        Authorized Signature
    </div>
    <div class="signature-box">
        Client Signature
    </div>
</div>
@endsection
