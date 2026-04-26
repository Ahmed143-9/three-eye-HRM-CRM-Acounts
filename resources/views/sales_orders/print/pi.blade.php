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

<h5 class="section-title">Terms & Conditions</h5>
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
        <td><strong>Total Amount:</strong> {{ $order->pi->amount }}</td>
    </tr>
</table>

<div class="footer">
    <div class="signature-box">
        Authorized Signature
    </div>
</div>
@endsection
