@extends('layouts.print', ['title' => 'Letter of Credit - ' . $order->lc->lc_no])

@section('content')
<div class="header">
    <div class="logo">
        <h3>LETTER OF CREDIT (LC)</h3>
    </div>
    <div class="header-info">
        <p><strong>LC Number:</strong> {{ $order->lc->lc_no }}</p>
        <p><strong>Client LC #:</strong> {{ $order->lc->client_lc_number ?? 'N/A' }}</p>
        <p><strong>LC Date:</strong> {{ $order->lc->lc_date }}</p>
        <p><strong>Total Amount:</strong> {{ $order->lc->amount }}</p>
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

<div class="footer">
    <div class="signature-box">
        Bank Official
    </div>
</div>
@endsection
