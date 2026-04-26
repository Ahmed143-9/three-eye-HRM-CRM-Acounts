<h5>{{ __('Step 5: Packing List') }}</h5>
<hr>
<div class="row mb-3">
    <div class="col-md-4"><strong>{{ __('PO:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}</div>
    <div class="col-md-4"><strong>{{ __('PI:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}</div>
    <div class="col-md-4"><strong>{{ __('LC:') }}</strong> {{ $order->lc->lc_no ?? 'N/A' }}</div>
</div>

{{ Form::open(['route' => ['sales-orders.pl.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('file', __('Upload Packing List (PDF/Image)'), ['class' => 'form-label']) }}
            {{ Form::file('file', ['class' => 'form-control', $order->packingList ? '' : 'required']) }}
            @if($order->packingList && $order->packingList->file_path)
                <div class="mt-2">
                    <a href="{{ asset($order->packingList->file_path) }}" target="_blank" class="btn btn-sm btn-info">{{ __('View Uploaded File') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="table-responsive mt-3">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('QTY') }}</th>
                <th>{{ __('Unit') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(optional($order->po)->items)
                @foreach($order->po->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->packingList)
            <a href="{{ route('sales-orders.pl.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.pl.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Consignment Note') }}</button>
</div>
{{ Form::close() }}
