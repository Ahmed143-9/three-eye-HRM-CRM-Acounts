{{ Form::open(['route' => ['sales-orders.pl.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
@if($active_ci)
    <input type="hidden" name="ci_id" value="{{ $active_ci->id }}">
@endif

<h6 class="fw-bold text-dark mt-4 mb-3">{{ __('Packing List Documentation') }}</h6>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('file', __('Upload Packing List for ') . ($active_ci->ci_number ?? ''), ['class' => 'form-label']) }}
            {{ Form::file('file', ['class' => 'form-control', ($active_ci && $active_ci->packingList) ? '' : 'required']) }}
            @if($active_ci && $active_ci->packingList && $active_ci->packingList->file_path)
                <div class="mt-2">
                    <a href="{{ asset($active_ci->packingList->file_path) }}" target="_blank" class="btn btn-sm btn-info">{{ __('View Uploaded File') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="table-responsive mt-3">
    <table class="table table-sm">
        <thead class="bg-light">
            <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Expected QTY') }}</th>
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
        @if($active_ci && $active_ci->packingList)
            <a href="{{ route('sales-orders.pl.print', $order->id) }}?ci_id={{ $active_ci->id }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.pl.download', $order->id) }}?ci_id={{ $active_ci->id }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Consignment Note') }}</button>
</div>
{{ Form::close() }}
