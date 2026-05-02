@extends('layouts.print', ['title' => 'Consignment Note - ' . $order->id])

@section('content')
<div class="header">
    <div class="logo">
        <h3>CONSIGNMENT NOTE / WEIGHT SLIP</h3>
    </div>
    <div class="header-info">
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Date:</strong> {{ date('d M, Y') }}</p>
    </div>
</div>

<h5 class="section-title">Weight Slip Details</h5>
<table>
    <thead>
        <tr>
            <th>Tanker Number</th>
            <th>Gross Weight</th>
            <th>Tare Weight</th>
            <th>Net Weight</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->consignmentNote->weightSlips as $slip)
        <tr>
            <td>{{ $slip->tanker_id }}</td>
            <td>{{ number_format($slip->gross_weight, 3) }}</td>
            <td>{{ number_format($slip->tare_weight, 3) }}</td>
            <td>{{ number_format($slip->net_weight, 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <div class="signature-box">
        Weighbridge Official
    </div>
    <div class="signature-box">
        Driver Signature
    </div>
</div>
@endsection
