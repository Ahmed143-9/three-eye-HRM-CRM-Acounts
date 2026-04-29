<h5>{{ __('Step 2: Proforma Invoice (PI)') }}</h5>
<hr>
@if($order->po)
    <div class="card bg-light border-0 mb-3">
        <div class="card-body py-2">
            <h6>{{ __('Items from PO') }}</h6>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('QTY') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->po->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">{{ __('Grand Total') }}:</td>
                        <td class="text-end fw-bold">{{ number_format($order->po->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

{{ Form::open(['route' => ['sales-orders.pi.store', $order->id], 'method' => 'post']) }}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('pi_number', __('System PI Number'), ['class' => 'form-label']) }}
            {{ Form::text('pi_number', $order->pi->pi_number ?? 'PI-' . time(), ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('client_pi_number', __('Client PI Number'), ['class' => 'form-label']) }}
            {{ Form::text('client_pi_number', $order->pi->client_pi_number ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('pi_date', __('PI Date'), ['class' => 'form-label']) }}
            {{ Form::date('pi_date', $order->pi->pi_date ?? date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('validity', __('Validity (Days)'), ['class' => 'form-label']) }}
            {{ Form::number('validity', $order->pi->validity ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('amount', __('Total Amount (USD)'), ['class' => 'form-label']) }}
            {{ Form::number('amount', $order->pi->amount ?? (optional($order->po)->grand_total ?? 0), ['class' => 'form-control', 'step' => '0.01', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('hs_code', __('HS Code'), ['class' => 'form-label']) }}
            {{ Form::text('hs_code', $order->pi->hs_code ?? (optional($order->po)->hs_code ?? ''), ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border mb-3">
            <div class="card-header bg-light"><h6>{{ __('Seller Information') }}</h6></div>
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('seller_name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_name', $order->pi->seller_name ?? '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seller_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('seller_address', $order->pi->seller_address ?? '', ['class' => 'form-control', 'rows' => 2]) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seller_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_mobile', $order->pi->seller_mobile ?? '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seller_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::email('seller_email', $order->pi->seller_email ?? '', ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border mb-3">
            <div class="card-header bg-light"><h6>{{ __('Buyer Information') }}</h6></div>
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('buyer_name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_name', $order->pi->buyer_name ?? (optional($order->po)->client_name ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('buyer_address', $order->pi->buyer_address ?? (optional($order->po)->client_address ?? ''), ['class' => 'form-control', 'rows' => 2]) }}
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_mobile', $order->pi->buyer_mobile ?? (optional($order->po)->client_phone ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::email('buyer_email', $order->pi->buyer_email ?? (optional($order->po)->client_email ?? ''), ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lifting_time', __('Lifting Time'), ['class' => 'form-label']) }}
            {{ Form::text('lifting_time', $order->pi->lifting_time ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('country_of_origin', __('Country of Origin'), ['class' => 'form-label']) }}
            {{ Form::text('country_of_origin', $order->pi->country_of_origin ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('tolerance', __('Tolerance'), ['class' => 'form-label']) }}
            {{ Form::text('tolerance', $order->pi->tolerance ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('port_of_loading', __('Port of Loading'), ['class' => 'form-label']) }}
            {{ Form::text('port_of_loading', $order->pi->port_of_loading ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('port_of_discharge', __('Port of Discharge'), ['class' => 'form-label']) }}
            {{ Form::text('port_of_discharge', $order->pi->port_of_discharge ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('payment_terms', __('Payment Terms'), ['class' => 'form-label']) }}
            {{ Form::textarea('payment_terms', $order->pi->payment_terms ?? null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->pi)
            <a href="{{ route('sales-orders.pi.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.pi.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to LC') }}</button>
</div>
{{ Form::close() }}
