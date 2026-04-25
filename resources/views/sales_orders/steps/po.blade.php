<h5>{{ __('Step 1: Purchase Order (PO)') }}</h5>
<hr>
{{ Form::open(['route' => ['sales-orders.po.store', $order->id], 'method' => 'post']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_name', __('Client Name'), ['class' => 'form-label']) }}
            {{ Form::text('client_name', $order->po->client_name ?? $order->customer->name, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_email', __('Client Email'), ['class' => 'form-label']) }}
            {{ Form::email('client_email', $order->po->client_email ?? $order->customer->email, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_phone', __('Client Phone'), ['class' => 'form-label']) }}
            {{ Form::text('client_phone', $order->po->client_phone ?? $order->customer->contact, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('signature', __('Authorized Signature Details'), ['class' => 'form-label']) }}
            {{ Form::text('signature', $order->po->signature ?? '', ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('client_address', __('Client Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('client_address', $order->po->client_address ?? $order->customer->billing_address, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
    </div>
</div>

<h6 class="mt-4">{{ __('Items Section') }}</h6>
<div class="table-responsive">
    <table class="table" id="po-items-table">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Description') }}</th>
                <th width="100">{{ __('QTY') }}</th>
                <th width="100">{{ __('Unit') }}</th>
                <th width="150">{{ __('Price') }}</th>
                <th width="150">{{ __('Total') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if($order->po && $order->po->items->count() > 0)
                @foreach($order->po->items as $index => $item)
                    <tr>
                        <td><input type="text" name="items[{{$index}}][item]" class="form-control" value="{{$item->item_name}}" required></td>
                        <td><input type="text" name="items[{{$index}}][description]" class="form-control" value="{{$item->description}}"></td>
                        <td><input type="number" name="items[{{$index}}][qty]" class="form-control qty" value="{{$item->quantity}}" required></td>
                        <td><input type="text" name="items[{{$index}}][unit]" class="form-control" value="{{$item->unit}}"></td>
                        <td><input type="number" step="0.01" name="items[{{$index}}][price]" class="form-control price" value="{{$item->price}}" required></td>
                        <td><input type="number" step="0.01" name="items[{{$index}}][total]" class="form-control total" value="{{$item->total}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="items[0][item]" class="form-control" required></td>
                    <td><input type="text" name="items[0][description]" class="form-control"></td>
                    <td><input type="number" name="items[0][qty]" class="form-control qty" required></td>
                    <td><input type="text" name="items[0][unit]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="items[0][price]" class="form-control price" required></td>
                    <td><input type="number" step="0.01" name="items[0][total]" class="form-control total" readonly></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">{{ __('Grand Total') }}:</td>
                <td class="fw-bold"><span id="grand_total_display">{{ number_format($order->po->grand_total ?? 0, 2) }}</span></td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm add-item"><i class="ti ti-plus"></i></button>
                    <input type="hidden" name="grand_total" id="grand_total" value="{{ $order->po->grand_total ?? 0 }}">
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="text-end mt-3">
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to PI') }}</button>
</div>
{{ Form::close() }}
