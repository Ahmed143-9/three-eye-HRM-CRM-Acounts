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
        @page { size: A4; margin: 5mm 15mm; }
        body { font-size: 10px !important; color: #000; font-family: "Times New Roman", Times, serif; }
        .print-container { padding: 0 !important; border: none !important; width: 100%; max-width: 100%; margin: 0; box-shadow: none; }
        table { page-break-inside: avoid; }
        .header { display: none; }
    }
    body { font-size: 10px; color: #000; font-family: "Times New Roman", Times, serif; }
    .header { display: none; }
    .header-logo { text-align: center; margin-bottom: 15px; }
    .header-logo img { max-height: 70px; }
    
    .doc-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 14px; margin-bottom: 5px; }
    
    .po-meta { display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 2px; }
    
    table.parties { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1.5px solid #000; }
    table.parties th { border: 1px solid #000; padding: 5px; text-align: left; font-weight: bold; }
    table.parties td { width: 50%; border: 1px solid #000; vertical-align: top; padding: 5px; }
    
    .content-body { margin-bottom: 10px; line-height: 1.3; }
    .content-body p { margin: 3px 0; }
    
    .bold-title { font-weight: bold; margin-bottom: 2px; }
    
    table.products { width: 100%; border-collapse: collapse; margin-bottom: 10px; border: 1.5px solid #000; }
    table.products th { border: 1px solid #000; padding: 4px; text-align: left; font-weight: bold; }
    table.products td { border: 1px solid #000; padding: 4px; text-align: left; }
    
    .commercial-terms { width: 100%; margin-bottom: 10px; line-height: 1.3; }
    .commercial-terms td { vertical-align: top; width: 50%; }
    
    .general-terms { margin-bottom: 10px; font-size: 9.5px; }
    .general-terms ol { padding-left: 20px; margin: 0; }
    .general-terms li { margin-bottom: 2px; text-align: justify; }
    
    table.footer-sigs { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1.5px solid #000; }
    table.footer-sigs th, table.footer-sigs td { border: 1px solid #000; padding: 3px 4px; text-align: left; vertical-align: top; }
    table.footer-sigs th { font-weight: bold; }
    .sig-field { display: flex; }
    .sig-label { width: 70px; font-weight: bold; }
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
        <th>Buyer</th>
        <th>Supplier</th>
    </tr>
    <tr>
        <td>
            @php $customer = optional($order->buying)->customer ?? $order->customer; @endphp
            <div style="font-weight: bold;">{{ $customer->name ?? $order->po->client_name }}</div>
            <div>{!! nl2br(e($customer->billing_address ?? $order->po->client_address)) !!}</div>
            @if($customer && $customer->contact_person_name)<div><strong>Attention:</strong> {{ $customer->contact_person_name }}</div>
            @elseif($order->po->client_email)<div><strong>Attention:</strong> {{ explode('@', $order->po->client_email)[0] }}</div>@endif
            
            @if($customer && $customer->contact_person_email)<div>Email: {{ $customer->contact_person_email }}</div>
            @elseif($order->po->client_email)<div>Email: {{ $order->po->client_email }}</div>@endif
            
            @if($customer && $customer->contact_person_number)<div>Mobile: {{ $customer->contact_person_number }}</div>
            @elseif($order->po->client_phone)<div>Mobile: {{ $order->po->client_phone }}</div>@endif
        </td>
        <td>
            @if($supplier)
                <div style="font-weight: bold;">{{ $supplier->name }}</div>
                <div>{!! nl2br(e($supplier->head_office_address ?? $supplier->billing_address)) !!}</div>
                @if($supplier->contact_person_name)<div><strong>Attention:</strong> {{ $supplier->contact_person_name }}</div>@endif
                @if($supplier->contact_person_email)<div>Email: {{ $supplier->contact_person_email }}</div>@endif
                @if($supplier->contact_person_number)<div>Mobile: {{ $supplier->contact_person_number }}</div>@endif
            @else
                <div>N/A</div>
            @endif
        </td>
    </tr>
</table>

<div class="content-body">
    <div style="font-weight: bold;">Subject: Purchase Order for Supply of {{ $order->po->items->first()->item_name ?? 'Products' }}</div>
    <p>Dear Sir,</p>
    <p>With reference to your price offer for supply of {{ $order->po->items->first()->item_name ?? 'Products' }} and based on the mutually agreed commercial terms and conditions, we are pleased to place the following Purchase Order:</p>
</div>

<div class="bold-title">Product Details</div>
<table class="products">
    <thead>
        <tr>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Freight</th>
            <th>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->po->items as $item)
        <tr>
            <td><strong style="font-weight: bold;">{{ $item->item_name }}</strong></td>
            <td>{{ $item->quantity }} {{ $item->unit }}</td>
            <td>{{ $item->currency }} {{ $item->price }}</td>
            <td>{{ $item->freight ? $item->currency . ' ' . $item->freight : '' }}</td>
            <td>{{ $item->currency }} {{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="bold-title">Commercial Terms</div>
<table class="commercial-terms">
    <tr>
        <td>Port of Loading: {{ $order->po->port_of_loading ?? 'Any Port in India' }}</td>
        <td>Port of Discharge: {{ $order->po->port_of_discharge ?? 'Tamabil, Bangladesh' }}</td>
    </tr>
    <tr>
        <td>Final Destination: {{ $order->po->final_destination ?? 'Tamabil, Bangladesh' }}</td>
        <td>Country of Origin: {{ $order->po->country_of_origin ?? 'India' }}</td>
    </tr>
    <tr>
        <td>Packing: {{ $order->po->packing ?? 'Road Tanker' }}</td>
        <td>Transport Mode: {{ $order->po->transport_mode ?? 'By Road' }}</td>
    </tr>
</table>

<div class="general-terms">
    <div class="bold-title">General Terms & Conditions</div>
    @php
        $terms = $order->po->terms_and_conditions ?? "1. Any amendment to this Purchase Order shall be valid only when accepted by both parties in writing.\n2. The supplier shall complete shipment of the ordered quantity within 30 (Thirty) days from the date of issuance of operative Letter of Credit (LC). Any anticipated delay in shipment must be communicated to the buyer in writing at least 7 (Seven) days prior to the scheduled shipment date.\n3. Any matter not specifically covered in this Purchase Order shall be settled through mutual discussion and agreement between the Buyer and Seller. Kindly acknowledge receipt and acceptance of this Purchase Order.";
        $termsList = explode("\n", $terms);
    @endphp
    <ol>
        @foreach($termsList as $term)
            @if(trim($term) != '')
                @php
                    $cleanTerm = preg_replace('/^\d+\.\s*/', '', trim($term));
                @endphp
                <li>{{ $cleanTerm }}</li>
            @endif
        @endforeach
    </ol>
</div>

<table class="footer-sigs">
    <tr>
        <th>Acknowledged by</th>
        <th>Accepted by</th>
        <th>Prepared by</th>
    </tr>
    <tr>
        <th style="font-weight: bold;">{{ $customer->name ?? $order->po->client_name }}</th>
        <th style="font-weight: bold;">{{ $supplier->name ?? 'Supplier' }}</th>
        <th style="font-weight: bold;">{{ \App\Models\Utility::getValByName('company_name') ?? 'Three Eye' }}</th>
    </tr>
    <tr>
        <td><div class="sig-field"><div class="sig-label">Signatory</div><div>: {{ $customer && $customer->contact_person_name ? $customer->contact_person_name : ($order->po->client_email ? explode('@', $order->po->client_email)[0] : '') }}</div></div></td>
        <td><div class="sig-field"><div class="sig-label">Signatory</div><div>: {{ $supplier && $supplier->contact_person_name ? $supplier->contact_person_name : '' }}</div></div></td>
        <td><div class="sig-field"><div class="sig-label">Signatory</div><div>: Kazi Gulshan Ara</div></div></td>
    </tr>
    <tr>
        <td><div class="sig-field"><div class="sig-label">Designation</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Designation</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Designation</div><div>: Managing Director</div></div></td>
    </tr>
    <tr>
        <td><div class="sig-field"><div class="sig-label">Signature</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Signature</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Signature</div><div>: .....................................</div></div></td>
    </tr>
    <tr>
        <td><div class="sig-field"><div class="sig-label">Date</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Date</div><div>: </div></div></td>
        <td><div class="sig-field"><div class="sig-label">Date</div><div>: {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</div></div></td>
    </tr>
</table>
@endsection
