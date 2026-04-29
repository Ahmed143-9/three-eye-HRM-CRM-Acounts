<h5>{{ __('Step 3: Letter of Credit (LC)') }}</h5>
<hr>
<div class="alert alert-info py-2">
    {{ __('Linked PI: ') }} <strong>{{ optional($order->pi)->pi_number ?? 'N/A' }}</strong>
    ({{ optional($order->pi)->pi_date ?? '' }}) -
    {{ __('Amount: ') }} <strong>{{ number_format(optional($order->pi)->amount ?? 0, 2) }}</strong>
</div>

{{ Form::open(['route' => ['sales-orders.lc.store', $order->id], 'method' => 'post']) }}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_no', __('LC Number'), ['class' => 'form-label']) }}
            {{ Form::text('lc_no', $order->lc->lc_no ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('client_lc_number', __('Client LC Number'), ['class' => 'form-label']) }}
            {{ Form::text('client_lc_number', $order->lc->client_lc_number ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('amount', __('LC Amount'), ['class' => 'form-label']) }}
            {{ Form::number('amount', $order->lc->amount ?? null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'lc_amount']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_amount">{{ __('Warning: Does not match PI (') . number_format(optional($order->pi)->amount ?? 0, 2) . ')' }}</small>
            <input type="hidden" id="pi_amount" value="{{ optional($order->pi)->amount ?? 0 }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_date', __('LC Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_date', $order->lc->lc_date ?? null, ['class' => 'form-control', 'id' => 'lc_date']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_date">{{ __('Warning: Exceeds PI Validity') }}</small>
            <input type="hidden" id="pi_date" value="{{ optional($order->pi)->pi_date ?? '' }}">
            <input type="hidden" id="pi_validity" value="{{ optional($order->pi)->validity ?? 0 }}">
        </div>
    </div>
    <div class="col-md-4">
        <!-- <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->lc->latest_shipment_date ?? null, ['class' => 'form-control', 'id' => 'lc_shipment_date']) }}
            <small class="text-danger d-none mismatch-label" id="warning_shipment_date">{{ __('Warning: Does not match PI') }}</small>
            <input type="hidden" id="pi_shipment_date" value="{{ optional($order->pi)->latest_shipment_date ?? '' }}">
        </div> -->
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->lc->latest_shipment_date ?? null, ['class' => 'form-control']) }}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_validity_date', $order->lc->lc_validity_date ?? null, ['class' => 'form-control', 'id' => 'lc_validity_date']) }}
            {{-- This is specific to LC, but let's check against PI's notion if it exists --}}
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
            {{ Form::label('country_of_origin', __('Country of Origin'), ['class' => 'form-label']) }}
            {{ Form::text('country_of_origin', $order->lc->country_of_origin ?? null, ['class' => 'form-control', 'id' => 'lc_country_of_origin']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_country_of_origin">{{ __('Warning: Does not match PI (') . (optional($order->pi)->country_of_origin ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_country_of_origin" value="{{ optional($order->pi)->country_of_origin ?? '' }}">
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
            {{ Form::label('port_of_loading', __('Port of Loading'), ['class' => 'form-label']) }}
            {{ Form::text('port_of_loading', $order->lc->port_of_loading ?? null, ['class' => 'form-control', 'id' => 'lc_port_of_loading']) }}
            <small class="text-danger d-none mismatch-label"
                id="warning_port_of_loading">{{ __('Warning: Does not match PI (') . (optional($order->pi)->port_of_loading ?? 'N/A') . ')' }}</small>
            <input type="hidden" id="pi_port_of_loading" value="{{ optional($order->pi)->port_of_loading ?? '' }}">
        </div>
    </div>
    <div class="col-md-6">
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

@push('script-page')
    <script>
        $(document).ready(function () {
            function checkMismatch(inputId, piValueId, warningId, type = 'text') {
                var val = $('#' + inputId).val();
                var piVal = $('#' + piValueId).val();

                if (!val && !piVal) {
                    $('#' + warningId).addClass('d-none');
                    return;
                }

                var mismatch = false;
                if (type === 'number') {
                    mismatch = Math.abs(parseFloat(val) - parseFloat(piVal)) > 0.01;
                } else if (type === 'date_validity') {
                    var piDateStr = $('#pi_date').val();
                    var validityDays = parseInt($('#pi_validity').val()) || 0;
                    var lcDateStr = val;
                    if (piDateStr && lcDateStr && validityDays > 0) {
                        var piDate = new Date(piDateStr);
                        var lcDate = new Date(lcDateStr);
                        var expiryDate = new Date(piDate);
                        expiryDate.setDate(expiryDate.getDate() + validityDays);
                        mismatch = lcDate > expiryDate;
                    }
                } else if (type === 'date_match') {
                    mismatch = val !== piVal;
                } else {
                    mismatch = (val || '').trim().toLowerCase() !== (piVal || '').trim().toLowerCase();
                }

                if (mismatch) {
                    $('#' + warningId).removeClass('d-none');
                } else {
                    $('#' + warningId).addClass('d-none');
                }
            }

            $('#lc_amount').on('keyup change', function () { checkMismatch('lc_amount', 'pi_amount', 'warning_amount', 'number'); });
            $('#lc_date').on('change', function () { checkMismatch('lc_date', 'pi_date', 'warning_date', 'date_validity'); });
            $('#lc_shipment_date').on('change', function () { checkMismatch('lc_shipment_date', 'pi_shipment_date', 'warning_shipment_date', 'date_match'); });

            ['seller_name', 'seller_address', 'seller_mobile', 'seller_email',
                'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email',
                'lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge'
            ].forEach(function (field) {
                var inputId = field === 'lifting_time' ? 'lc_lifting_time' : 'lc_' + field;
                var piValueId = field === 'lifting_time' ? 'pi_lifting_time_val' : 'pi_' + field;
                $('#' + inputId).on('keyup change', function () {
                    checkMismatch(inputId, piValueId, 'warning_' + field);
                });
            });

            // Trigger checks
            $('#lc_amount').trigger('change');
            $('#lc_date').trigger('change');
            $('#lc_shipment_date').trigger('change');
            ['seller_name', 'seller_address', 'seller_mobile', 'seller_email',
                'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email',
                'lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge'
            ].forEach(function (field) {
                var inputId = field === 'lifting_time' ? 'lc_lifting_time' : 'lc_' + field;
                $('#' + inputId).trigger('change');
            });
        });
    </script>
@endpush

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->lc)
            <a href="{{ route('sales-orders.lc.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i
                    class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.lc.download', $order->id) }}" class="btn btn-info"><i
                    class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to CI') }}</button>
</div>
{{ Form::close() }}