@if(empty($order->po))
    <div class="alert alert-warning">DEBUG: $order->po is empty. Data not loaded in controller?</div>
@else
    <div class="alert alert-success">DEBUG: $order->po exists (ID: {{ $order->po->id }}). Rendering...</div>
@endif

<h5>{{ __('Step 2: Proforma Invoice (PI)') }}</h5>
<p class="text-muted mb-0">{{ __('Step 2 of 7') }}</p>
<hr>

@if($order->po)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3 mt-2">
                <i class="ti ti-clipboard-list text-primary"></i>
                <h6 class="fw-semibold mb-0 text-dark">{{ __('Product Order Details (Read-only)') }}</h6>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('ITEM') }}</th>
                            <th>{{ __('DESCRIPTION') }}</th>
                            <th>{{ __('QTY') }}</th>
                            <th>{{ __('UNIT') }}</th>
                            <th>{{ __('PRICE PER UNIT') }}</th>
                            <th>{{ __('UNIT') }}</th>
                            <th>{{ __('TOTAL') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->po->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->currency ?? 'D.' }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<h6 class="fw-bold mb-3">{{ __('Step 2: Proforma Invoice (PI)') }}</h6>

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
            {{ Form::text('client_pi_number', $order->pi->client_pi_number ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('pi_date', __('PI Date'), ['class' => 'form-label']) }}
            {{ Form::text('pi_date', isset($order->pi->pi_date) ? \Carbon\Carbon::parse($order->pi->pi_date)->format('m-d-Y') : date('m-d-Y'), ['class' => 'form-control datepicker', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-muted">{{ __('Format: Month-Day-Year (MM-DD-YYYY)') }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('validity', __('Validity (Days)'), ['class' => 'form-label']) }}
            {{ Form::number('validity', $order->pi->validity ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('incoterm', __('Incoterms'), ['class' => 'form-label']) }}
            @php
                $incoterms = [
                    'CFR' => 'CFR - Cost and Freight',
                    'FOB' => 'FOB - Free On Board',
                    'CIF' => 'CIF - Cost, Insurance, and Freight',
                ];
            @endphp
            {{ Form::select('incoterm', ['' => __('Select Incoterms')] + $incoterms, $order->pi->incoterm ?? null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('hs_code', __('HS Code'), ['class' => 'form-label']) }}
            {{ Form::text('hs_code', $order->pi->hs_code ?? (optional($order->po)->hs_code ?? ''), ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border mb-3">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3 text-warning">{{ __('Seller Information') }}</h6>
                <div class="form-group mb-2">
                    {{ Form::label('seller_name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_name', $order->pi->seller_name ?? '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group mb-2">
                    {{ Form::label('seller_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('seller_address', $order->pi->seller_address ?? '', ['class' => 'form-control', 'rows' => 2]) }}
                </div>
                <div class="form-group mb-2">
                    {{ Form::label('seller_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_mobile', $order->pi->seller_mobile ?? '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group mb-0">
                    {{ Form::label('seller_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::email('seller_email', $order->pi->seller_email ?? '', ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border mb-3">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3 text-warning">{{ __('Buyer Information') }}</h6>
                <div class="form-group mb-2">
                    {{ Form::label('buyer_name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_name', $order->pi->buyer_name ?? (optional($order->po)->client_name ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group mb-2">
                    {{ Form::label('buyer_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('buyer_address', $order->pi->buyer_address ?? (optional($order->po)->client_address ?? ''), ['class' => 'form-control', 'rows' => 2]) }}
                </div>
                <div class="form-group mb-2">
                    {{ Form::label('buyer_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_mobile', $order->pi->buyer_mobile ?? (optional($order->po)->client_phone ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group mb-0">
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

    <div class="col-12 mt-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="ti ti-building-bank text-primary"></i>
            <h6 class="fw-semibold mb-0 text-dark">{{ __('Banking Details') }}</h6>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) }}
                    {{ Form::text('bank_name', $order->pi->bank_name ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('account_name', __('Account name'), ['class' => 'form-label']) }}
                    {{ Form::text('account_name', $order->pi->account_name ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                    {{ Form::text('branch', $order->pi->branch ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('account_no', __('Account No.'), ['class' => 'form-label']) }}
                    {{ Form::text('account_no', $order->pi->account_no ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('swift_code', __('SWIFT Code'), ['class' => 'form-label']) }}
                    {{ Form::text('swift_code', $order->pi->swift_code ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mt-3">
        <div class="form-group">
            {{ Form::label('terms_and_conditions', __('Terms and Conditions'), ['class' => 'form-label']) }}
            {{ Form::textarea('terms_and_conditions', $order->pi->terms_and_conditions ?? null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter terms and conditions...')]) }}
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <button type="button" class="btn btn-outline-secondary" onclick="switchWorkflowStep('pills-po-tab')">
        <i class="ti ti-chevron-left me-1"></i>{{ __('Previous: PO') }}
    </button>
    <button type="submit" class="btn btn-success d-inline-flex align-items-center"
        style="background-color: #6fd943; border-color: #6fd943; padding: 10px 25px; font-weight: 600;">
        {{ __('Save & Proceed to LC') }}
        <i class="ti ti-chevron-right ms-2"></i>
    </button>
</div>
{{ Form::close() }}