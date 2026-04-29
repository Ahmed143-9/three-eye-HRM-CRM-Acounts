<h5>{{ __('Step 2: Proforma Invoice (PI)') }}</h5>
<hr>
@if($order->po)
    <div class="card bg-light border-0 mb-3">
        <div class="card-body">
            <h6>{{ __('Product Order Details') }} ({{ __('Read-only') }})</h6>
            <div class="table-responsive">
                <table class="table table-sm datatable">
                    <thead>
                        <tr>
                            <th width="20%">{{ __('Item') }}</th>
                            <th width="20%">{{ __('Description') }}</th>
                            <th width="10%">{{ __('QTY') }}</th>
                            <th width="12%">{{ __('Unit') }}</th>
                            <th width="12%">{{ __('Price per Unit') }}</th>
                            <th width="12%">{{ __('Unit') }}</th>
                            <th width="12%">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->po->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit_id }}</td>
                                <td>{{ number_format($item->price_per_unit, 2) }}</td>
                                <td>{{ $item->currency_type }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold">{{ __('Grand Total') }}:</td>
                            <td class="fw-bold">{{ number_format($order->po->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif

{{ Form::open(['route' => ['sales-orders.pi.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('pi_number', __('PI Reference No.'), ['class' => 'form-label']) }}
            {{ Form::text('pi_number', $order->pi->pi_number ?? 'PI-' . time(), ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('client_pi_number', __('Client PI No.'), ['class' => 'form-label']) }}
            {{ Form::text('client_pi_number', $order->pi->client_pi_number ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('pi_date', __('PI Date'), ['class' => 'form-label']) }}
            {{ Form::text('pi_date', isset($order->pi->pi_date) ? \Carbon\Carbon::parse($order->pi->pi_date)->format('m-d-Y') : date('m-d-Y'), ['class' => 'form-control date-format-input', 'required' => 'required', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-muted">{{ __('Format: Month-Day-Year (MM-DD-YYYY)') }}</small>
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
            {{ Form::label('incoterm', __('Incoterms'), ['class' => 'form-label']) }}
            {{ Form::select('incoterm', ['CFR' => 'CFR', 'FOB' => 'FOB', 'CIF' => 'CIF'], $order->pi->incoterm ?? null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Incoterm')]) }}
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

    <div class="col-md-12">
        <hr class="my-4">
        <h5 class="mb-3">{{ __('Banking Details') }}</h5>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) }}
            {{ Form::text('bank_name', $order->pi->bank_name ?? $order->customer->bank_name, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('account_name', __('Account Name'), ['class' => 'form-label']) }}
            {{ Form::text('account_name', $order->pi->account_name ?? $order->customer->account_name, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('branch_name', __('Branch'), ['class' => 'form-label']) }}
            {{ Form::text('branch_name', $order->pi->branch_name ?? $order->customer->branch_name, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('account_no', __('Account No.'), ['class' => 'form-label']) }}
            {{ Form::text('account_no', $order->pi->account_no ?? $order->customer->account_no, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('swift_code', __('SWIFT Code'), ['class' => 'form-label']) }}
            {{ Form::text('swift_code', $order->pi->swift_code ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-12 mt-4">
        <div class="form-group">
            {{ Form::label('terms_and_conditions', __('Terms and Conditions'), ['class' => 'form-label']) }}
            {{ Form::textarea('terms_and_conditions', $order->pi->terms_and_conditions ?? null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter terms and conditions...')]) }}
        </div>
    </div>
</div>

{{ Form::close() }}
