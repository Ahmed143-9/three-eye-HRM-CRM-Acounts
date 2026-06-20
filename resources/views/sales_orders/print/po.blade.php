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

<h5 class="section-title">Commercial Terms</h5>
<div class="mb-3" style="font-size: 14px;">
    <strong>Port of Loading:</strong> {{ $order->po->port_of_loading ?? 'Any Port in India' }}<br>
    <strong>Port of Discharge:</strong> {{ $order->po->port_of_discharge ?? 'Tamabil, Bangladesh' }}<br>
    <strong>Final Destination:</strong> {{ $order->po->final_destination ?? 'Tamabil, Bangladesh' }}<br>
    <strong>Country of Origin:</strong> {{ $order->po->country_of_origin ?? 'India' }}<br>
    <strong>Packing:</strong> {{ $order->po->packing ?? 'Road Tanker' }}<br>
    <strong>Transport Mode:</strong> {{ $order->po->transport_mode ?? 'By Road' }}
</div>

<h5 class="section-title">General Terms & Conditions</h5>
<div class="mb-4" style="font-size: 14px; white-space: pre-wrap;">
{{ $order->po->terms_and_conditions ?? "1. Any amendment to this Purchase Order shall be valid only when accepted by both parties in writing.
2. The supplier shall complete shipment of the ordered quantity within 30 (Thirty) days from the date of issuance of operative Letter of Credit (LC). Any anticipated delay in shipment must be communicated to the buyer in writing at least 7 (Seven) days prior to the scheduled shipment date.
3. Any matter not specifically covered in this Purchase Order shall be settled through mutual discussion and agreement between the Buyer and Seller." }}
</div>

<p class="mb-4">Kindly acknowledge receipt and acceptance of this Purchase Order.</p>

<div class="footer">
    <div class="signature-box">
        Authorized Signature
    </div>
    <div class="signature-box">
        Client Signature
    </div>
</div>
@endsection
