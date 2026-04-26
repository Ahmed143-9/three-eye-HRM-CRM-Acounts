@extends('layouts.print', ['title' => 'Packing List - ' . $order->id])

@section('content')
<div class="header">
    <div class="logo">
        <h3>PACKING LIST</h3>
    </div>
    <div class="header-info">
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Date:</strong> {{ date('d M, Y') }}</p>
    </div>
</div>

<div style="text-align: center; padding: 50px; border: 2px dashed #ccc; margin-top: 30px;">
    <h4>Packing List Document</h4>
    <p>This is a placeholder for the uploaded Packing List document.</p>
    @if($order->packingList && $order->packingList->file_path)
        <p><strong>Attached File:</strong> {{ basename($order->packingList->file_path) }}</p>
        <a href="{{ asset($order->packingList->file_path) }}" class="btn btn-sm btn-primary no-print">View Full Document</a>
    @else
        <p class="text-danger">No document uploaded yet.</p>
    @endif
</div>

<div class="footer">
    <div class="signature-box">
        Store Keeper
    </div>
</div>
@endsection
