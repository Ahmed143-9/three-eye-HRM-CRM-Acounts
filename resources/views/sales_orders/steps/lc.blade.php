<h5>{{ __('Step 3: Letter of Credit (LC)') }}</h5>
<hr>
<div class="alert alert-info py-2">
    {{ __('Linked PI: ') }} <strong>{{ optional($order->pi)->pi_number ?? 'N/A' }}</strong>
    ({{ optional($order->pi)->pi_date ?? '' }}) -
    {{ __('Total QTY (from PI): ') }} <strong>{{ number_format($order->po->items->sum('quantity'), 3) }} {{ $order->po->items->first()->unit_id ?? '' }}</strong>
</div>

{{ Form::open(['route' => ['sales-orders.lc.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_reference_no', __('LC Reference No.'), ['class' => 'form-label']) }}
            {{ Form::text('lc_reference_no', $order->lc->lc_reference_no ?? 'LC-' . time(), ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('client_lc_no', __('Client LC No.'), ['class' => 'form-label']) }}
            {{ Form::text('client_lc_no', $order->lc->client_lc_no ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_qty', __('LC Amount (QTY)'), ['class' => 'form-label']) }}
            {{ Form::number('lc_qty', $order->lc->lc_qty ?? $order->po->items->sum('quantity'), ['class' => 'form-control', 'step' => '0.001', 'id' => 'lc_qty']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_qty">{{ __('Warning: Does not match PI Total QTY (') . number_format($order->po->items->sum('quantity'), 3) . ')' }}</small>
            <input type="hidden" id="pi_qty" value="{{ $order->po->items->sum('quantity') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('unit', __('Unit'), ['class' => 'form-label']) }}
            {{ Form::text('unit', $order->lc->unit ?? ($order->po->items->first()->unit_id ?? null), ['class' => 'form-control', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('date_of_issue', __('Date of Issue (LC)'), ['class' => 'form-label']) }}
            {{ Form::text('date_of_issue', isset($order->lc->date_of_issue) ? \Carbon\Carbon::parse($order->lc->date_of_issue)->format('m-d-Y') : null, ['class' => 'form-control datepicker', 'id' => 'date_of_issue', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-danger d-none mismatch-label mt-1" id="warning_date">{{ __('Warning: Exceeds PI Validity') }}</small>
            <input type="hidden" id="pi_date" value="{{ optional($order->pi)->pi_date ?? '' }}">
            <input type="hidden" id="pi_validity" value="{{ optional($order->pi)->validity ?? 0 }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_type', __('LC Type'), ['class' => 'form-label']) }}
            {{ Form::text('lc_type', $order->lc->lc_type ?? null, ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::text('lc_validity_date', isset($order->lc->lc_validity_date) ? \Carbon\Carbon::parse($order->lc->lc_validity_date)->format('m-d-Y') : null, ['class' => 'form-control datepicker', 'id' => 'lc_validity_date', 'placeholder' => 'MM-DD-YYYY']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lifting_time', __('Lifting Time'), ['class' => 'form-label']) }}
            {{ Form::text('lifting_time', $order->lc->lifting_time ?? null, ['class' => 'form-control', 'id' => 'lc_lifting_time']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_lifting_time">{{ __('Warning: Does not match PI (') . (optional($order->pi)->lifting_time ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_lifting_time_val" value="{{ optional($order->pi)->lifting_time ?? '' }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('tolerance', __('Tolerance'), ['class' => 'form-label']) }}
            {{ Form::text('tolerance', $order->lc->tolerance ?? null, ['class' => 'form-control', 'id' => 'lc_tolerance']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_tolerance">{{ __('Warning: Does not match PI (') . (optional($order->pi)->tolerance ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_tolerance" value="{{ optional($order->pi)->tolerance ?? '' }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('country_of_origin', __('Country of Origin'), ['class' => 'form-label']) }}
            {{ Form::text('country_of_origin', $order->lc->country_of_origin ?? null, ['class' => 'form-control', 'id' => 'lc_country_of_origin']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_country_of_origin">{{ __('Warning: Does not match PI (') . (optional($order->pi)->country_of_origin ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_country_of_origin" value="{{ optional($order->pi)->country_of_origin ?? '' }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('port_of_loading', __('Port of Loading'), ['class' => 'form-label']) }}
            {{ Form::text('port_of_loading', $order->lc->port_of_loading ?? null, ['class' => 'form-control', 'id' => 'lc_port_of_loading']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_port_of_loading">{{ __('Warning: Does not match PI (') . (optional($order->pi)->port_of_loading ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_port_of_loading" value="{{ optional($order->pi)->port_of_loading ?? '' }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('incoterm', __('Incoterms'), ['class' => 'form-label']) }}
            {{ Form::text('incoterm', $order->pi->incoterm ?? null, ['class' => 'form-control', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::text('latest_shipment_date', isset($order->lc->latest_shipment_date) ? \Carbon\Carbon::parse($order->lc->latest_shipment_date)->format('m-d-Y') : null, ['class' => 'form-control datepicker', 'id' => 'lc_shipment_date', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-danger d-none mismatch-label mt-1" id="warning_shipment_date">{{ __('Warning: Does not match PI') }}</small>
            <input type="hidden" id="pi_shipment_date" value="{{ optional($order->pi)->latest_shipment_date ?? '' }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('port_of_discharge', __('Port of Discharge'), ['class' => 'form-label']) }}
            {{ Form::text('port_of_discharge', $order->lc->port_of_discharge ?? null, ['class' => 'form-control', 'id' => 'lc_port_of_discharge']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_port_of_discharge">{{ __('Warning: Does not match PI (') . (optional($order->pi)->port_of_discharge ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_port_of_discharge" value="{{ optional($order->pi)->port_of_discharge ?? '' }}">
        </div>
    </div>
</div>

<h6 class="mt-4">{{ __('Party Details (Cross-Check)') }}</h6>
<div class="row">
    <div class="col-md-6 border-end">
        <h6>{{ __('Seller Details') }}</h6>
        <div class="form-group">
            {{ Form::label('seller_name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('seller_name', $order->lc->seller_name ?? null, ['class' => 'form-control', 'id' => 'lc_seller_name']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_seller_name">{{ __('Warning: Mismatch with PI') }}</small>
            <input type="hidden" id="pi_seller_name" value="{{ optional($order->pi)->seller_name ?? '' }}">
        </div>
        <div class="form-group">
            {{ Form::label('seller_address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('seller_address', $order->lc->seller_address ?? null, ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_seller_address']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_seller_address">{{ __('Warning: Mismatch with PI') }}</small>
            <input type="hidden" id="pi_seller_address" value="{{ optional($order->pi)->seller_address ?? '' }}">
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('seller_mobile', __('Mobile'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_mobile', $order->lc->seller_mobile ?? null, ['class' => 'form-control', 'id' => 'lc_seller_mobile']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_seller_mobile">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_seller_mobile" value="{{ optional($order->pi)->seller_mobile ?? '' }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('seller_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_email', $order->lc->seller_email ?? null, ['class' => 'form-control', 'id' => 'lc_seller_email']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_seller_email">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_seller_email" value="{{ optional($order->pi)->seller_email ?? '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h6>{{ __('Buyer Details') }}</h6>
        <div class="form-group">
            {{ Form::label('buyer_name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('buyer_name', $order->lc->buyer_name ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_name']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_buyer_name">{{ __('Warning: Mismatch with PI') }}</small>
            <input type="hidden" id="pi_buyer_name" value="{{ optional($order->pi)->buyer_name ?? '' }}">
        </div>
        <div class="form-group">
            {{ Form::label('buyer_address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('buyer_address', $order->lc->buyer_address ?? null, ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_buyer_address']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_buyer_address">{{ __('Warning: Mismatch with PI') }}</small>
            <input type="hidden" id="pi_buyer_address" value="{{ optional($order->pi)->buyer_address ?? '' }}">
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('buyer_mobile', __('Mobile'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_mobile', $order->lc->buyer_mobile ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_mobile']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_buyer_mobile">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_buyer_mobile" value="{{ optional($order->pi)->buyer_mobile ?? '' }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('buyer_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_email', $order->lc->buyer_email ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_email']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_buyer_email">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_buyer_email" value="{{ optional($order->pi)->buyer_email ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>



<div class="col-md-12 mt-4">
    <div class="form-group">
        {{ Form::label('terms_and_conditions', __('Terms and Conditions'), ['class' => 'form-label']) }}
        {{ Form::textarea('terms_and_conditions', $order->lc->terms_and_conditions ?? null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter terms and conditions...')]) }}
    </div>
</div>

{{ Form::close() }}