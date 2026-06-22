@extends('layouts.print', ['title' => 'Purchase Order - ' . $order->id])

@php
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    $logo_src = $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png');
    
    $supplier = optional($order->buying)->supplier;
@endphp

@section('content')
<style>
    @media print {
        @page { size: A4; margin: 10mm; }
        body { font-size: 13px !important; color: #000; font-family: "Times New Roman", Times, serif; }
        .print-container { padding: 0 !important; border: none !important; width: 100%; max-width: 100%; margin: 0; box-shadow: none; }
        table { page-break-inside: avoid; }
        .header { display: none; } /* Hide the default header from layouts.print */
    }
    body { font-size: 13px; color: #000; font-family: "Times New Roman", Times, serif; }
    .header { display: none; } /* Hide the default header from layouts.print */
    .header-logo { text-align: center; margin-bottom: 10px; }
    .header-logo img { max-height: 80px; }
    .doc-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 16px; margin-bottom: 20px; }
    .po-meta { display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 5px; }
    table.parties { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1px solid #000; }
    table.parties td { width: 50%; border: 1px solid #000; vertical-align: top; padding: 5px; }
    .bold-title { font-weight: bold; }
    .content-body { margin-bottom: 15px; }
    .content-body p { margin: 5px 0; }
    table.products { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1px solid #000; }
    table.products th, table.products td { border: 1px solid #000; padding: 5px; text-align: left; }
    table.products th { font-weight: bold; }
    .terms-section { margin-bottom: 15px; }
    .terms-section p { margin: 2px 0; }
    .terms-section h6 { font-weight: bold; font-size: 13px; margin: 5px 0; }
    table.footer-sigs { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #000; }
    table.footer-sigs td { width: 50%; border: 1px solid #000; vertical-align: top; padding: 5px; height: 100px; }
</style>

<div class="header-logo">
    <img src="{{ $logo_src }}" alt="Company Logo">
</div>
<div class="doc-title">PURCHASE ORDER</div>

<div class="po-meta">
    <div>PO No.: {{ $order->po->po_number ?? ('PO-' . $order->id) }}</div>
    <div>PO Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</div>
</div>

<table class="parties">
    <tr>
        <td style="font-weight: bold;">Buyer</td>
        <td style="font-weight: bold;">Supplier</td>
    </tr>
    <tr>
        <td>
            <div style="font-weight: bold; margin-bottom: 5px;">{{ $order->po->client_name }}</div>
            <div>{!! nl2br(e($order->po->client_address)) !!}</div>
            @if($order->po->client_email)<div>Attention: {{ explode('@', $order->po->client_email)[0] }}</div>@endif
            @if($order->po->client_phone)<div>Mobile: {{ $order->po->client_phone }}</div>@endif
        </td>
        <td>
            @if($supplier)
                <div style="font-weight: bold; margin-bottom: 5px;">{{ $supplier->name }}</div>
                <div>{!! nl2br(e($supplier->head_office_address)) !!}</div>
                @if($supplier->contact_person_name)<div>Attention: {{ $supplier->contact_person_name }}</div>@endif
                @if($supplier->contact_person_number)<div>Mobile: {{ $supplier->contact_person_number }}</div>@endif
            @else
                <div>N/A</div>
            @endif
        </td>
    </tr>
</table>

<div class="content-body">
    <p style="font-weight: bold;">Subject: Purchase Order for Supply of {{ $order->po->items->first()->item_name ?? 'Products' }}</p>
    <p>Dear Sir,</p>
    <p>With reference to your price offer for supply of {{ $order->po->items->first()->item_name ?? 'Products' }} and based on the mutually agreed commercial terms and conditions, we are pleased to place the following Purchase Order:</p>
</div>

<div class="bold-title" style="margin-bottom: 5px;">Product Details</div>
<table class="products">
    <thead>
        <tr>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->po->items as $item)
        <tr>
            <td>
                <div style="font-weight: bold;">{{ $item->item_name }}</div>
                @if($item->description)<div>{{ $item->description }}</div>@endif
            </td>
            <td>{{ $item->quantity }} {{ $item->unit }}</td>
            <td>{{ $item->currency }} {{ $item->price }}/{{ $item->unit }}</td>
            <td>{{ $item->currency }} {{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="terms-section">
    <h6>Commercial Terms</h6>
    <p>Port of Loading: {{ $order->po->port_of_loading ?? 'Any Port in India' }}</p>
    <p>Port of Discharge: {{ $order->po->port_of_discharge ?? 'Tamabil, Bangladesh' }}</p>
    <p>Final Destination: {{ $order->po->final_destination ?? 'Tamabil, Bangladesh' }}</p>
    <p>Country of Origin: {{ $order->po->country_of_origin ?? 'India' }}</p>
    <p>Packing: {{ $order->po->packing ?? 'Road Tanker' }}</p>
    <p>Transport Mode: {{ $order->po->transport_mode ?? 'By Road' }}</p>
</div>

<div class="terms-section">
    <h6>General Terms & Conditions</h6>
    <div style="padding-left: 20px; white-space: pre-wrap;">{{ $order->po->terms_and_conditions ?? "1. Any amendment to this Purchase Order shall be valid only when accepted by both parties in writing.\n2. The supplier shall complete shipment of the ordered quantity within 30 (Thirty) days from the date of issuance of operative Letter of Credit (LC). Any anticipated delay in shipment must be communicated to the buyer in writing at least 7 (Seven) days prior to the scheduled shipment date.\n3. Any matter not specifically covered in this Purchase Order shall be settled through mutual discussion and agreement between the Buyer and Seller." }}</div>
</div>

<div style="margin-top: 10px; margin-bottom: 10px;">
    Kindly acknowledge receipt and acceptance of this Purchase Order.
</div>

<table class="footer-sigs" style="border: none;">
    <tr>
        <td style="border: none; text-align: center;">
            <div style="margin-top: 50px;">
                <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px;">{{ __('Prepared By') }}</div>
                <div>{{ $order->po->prepared_by ?? '' }}</div>
            </div>
        </td>
        <td style="border: none; text-align: center;">
            <div style="margin-top: 50px;">
                <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px;">{{ __('Issued By') }}</div>
                <div>{{ $order->po->issued_by ?? '' }}</div>
            </div>
        </td>
        <td style="border: none; text-align: center;">
            <div style="margin-top: 50px;">
                <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px;">{{ __('Acknowledged By') }}</div>
                <div>{{ $order->po->acknowledged_by ?? '' }}</div>
            </div>
        </td>
        <td style="border: none; text-align: center;">
            <div style="margin-top: 50px;">
                <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px;">{{ __('Accepted By Supplier') }}</div>
                <div>{{ $supplier->name ?? 'Supplier' }}</div>
            </div>
        </td>
    </tr>
</table>
@endsection
