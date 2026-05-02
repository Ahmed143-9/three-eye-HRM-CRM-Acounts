@extends('layouts.print', ['title' => 'Letter of Credit - ' . $order->lc->lc_no])

@section('content')
<div class="header">
    <div class="logo">
        <h3>LETTER OF CREDIT (LC)</h3>
    </div>
    <div class="header-info">
        <p><strong>LC Reference No.:</strong> {{ $order->lc->lc_reference_no }}</p>
        <p><strong>Client LC No.:</strong> {{ $order->lc->client_lc_no ?? 'N/A' }}</p>
        <p><strong>Date of Issue:</strong> {{ $order->lc->date_of_issue }}</p>
        <p><strong>LC Type:</strong> {{ $order->lc->lc_type ?? 'N/A' }}</p>
        <p><strong>Incoterms:</strong> {{ $order->lc->incoterm ?? 'N/A' }}</p>
        <p><strong>LC Amount (QTY):</strong> {{ number_format($order->lc->lc_qty, 3) }} {{ $order->lc->unit }}</p>
    </div>
</div>

<div class="row" style="display: flex; gap: 20px;">
    <div style="flex: 1;">
        <h5 class="section-title">Seller Details (Beneficiary)</h5>
        <p>
            <strong>Name:</strong> {{ $order->lc->seller_name }}<br>
            <strong>Address:</strong> {{ $order->lc->seller_address }}<br>
            <strong>Mobile:</strong> {{ $order->lc->seller_mobile }}<br>
            <strong>Email:</strong> {{ $order->lc->seller_email }}
        </p>
    </div>
    <div style="flex: 1;">
        <h5 class="section-title">Buyer Details (Applicant)</h5>
        <p>
            <strong>Name:</strong> {{ $order->lc->buyer_name }}<br>
            <strong>Address:</strong> {{ $order->lc->buyer_address }}<br>
            <strong>Mobile:</strong> {{ $order->lc->buyer_mobile }}<br>
            <strong>Email:</strong> {{ $order->lc->buyer_email }}
        </p>
    </div>
</div>

<h5 class="section-title">LC Important Dates</h5>
<table>
    <tr>
        <th>Latest Shipment Date</th>
        <td>{{ $order->lc->latest_shipment_date }}</td>
    </tr>
    <tr>
        <th>LC Validity Date</th>
        <td>{{ $order->lc->lc_validity_date }}</td>
    </tr>
</table>

@if($order->lc->terms_and_conditions)
    <div class="mt-4">
        <h5 class="section-title">{{ __('Terms and Conditions') }}</h5>
        <p style="white-space: pre-line;">{{ $order->lc->terms_and_conditions }}</p>
    </div>
@endif

<div class="footer">
    <div class="signature-box">
        Bank Official
    </div>
</div>
@endsection
