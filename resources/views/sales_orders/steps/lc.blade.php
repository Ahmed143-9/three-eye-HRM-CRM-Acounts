<h5>{{ __('Step 3: Letter of Credit (LC)') }}</h5>
<hr>
<div class="alert alert-info py-2">
    {{ __('Linked PI: ') }} <strong>{{ optional($order->pi)->pi_number ?? 'N/A' }}</strong> ({{ optional($order->pi)->pi_date ?? '' }}) - 
    {{ __('PI Amount: ') }} <strong>{{ number_format(optional($order->pi)->amount ?? 0, 2) }} USD</strong>
</div>

{{ Form::open(['route' => ['sales-orders.lc.store', $order->id], 'method' => 'post']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('lc_no', __('System LC Number'), ['class' => 'form-label']) }}
            {{ Form::text('lc_no', $order->lc->lc_no ?? 'LC-' . time(), ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_lc_number', __('Client LC Number'), ['class' => 'form-label']) }}
            {{ Form::text('client_lc_number', $order->lc->client_lc_number ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('amount', __('LC Amount (USD)'), ['class' => 'form-label']) }}
            {{ Form::number('amount', $order->lc->amount ?? (optional($order->pi)->amount ?? 0), ['class' => 'form-control', 'step' => '0.01', 'id' => 'lc_amount', 'required' => 'required']) }}
            <input type="hidden" id="pi_amount" value="{{ optional($order->pi)->amount ?? 0 }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('lc_date', __('LC Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_date', $order->lc->lc_date ?? date('Y-m-d'), ['class' => 'form-control', 'required' => 'required', 'id' => 'lc_date']) }}
            <input type="hidden" id="pi_date" value="{{ optional($order->pi)->pi_date ?? '' }}">
            <input type="hidden" id="pi_validity" value="{{ optional($order->pi)->validity ?? 0 }}">
        </div>
    </div>

    <div class="col-md-12 d-none" id="amount_mismatch_warning">
        <div class="alert alert-warning py-2">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ __('Warning: LC Amount does not match the PI Amount.') }}
        </div>
    </div>

    <div class="col-md-12 d-none" id="validity_warning">
        <div class="alert alert-danger py-2">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ __('Warning: LC issued after PI validity expired.') }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border mb-3">
            <div class="card-header bg-light"><h6>{{ __('Seller Information') }}</h6></div>
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('seller_name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_name', $order->lc->seller_name ?? (optional($order->pi)->seller_name ?? ''), ['class' => 'form-control', 'id' => 'lc_seller_name']) }}
                    <input type="hidden" id="pi_seller_name" value="{{ optional($order->pi)->seller_name ?? '' }}">
                </div>
                <div class="form-group">
                    {{ Form::label('seller_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('seller_address', $order->lc->seller_address ?? (optional($order->pi)->seller_address ?? ''), ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_seller_address']) }}
                    <input type="hidden" id="pi_seller_address" value="{{ optional($order->pi)->seller_address ?? '' }}">
                </div>
                <div class="form-group">
                    {{ Form::label('seller_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('seller_mobile', $order->lc->seller_mobile ?? (optional($order->pi)->seller_mobile ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seller_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::email('seller_email', $order->lc->seller_email ?? (optional($order->pi)->seller_email ?? ''), ['class' => 'form-control']) }}
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
                    {{ Form::text('buyer_name', $order->lc->buyer_name ?? (optional($order->pi)->buyer_name ?? ''), ['class' => 'form-control', 'id' => 'lc_buyer_name']) }}
                    <input type="hidden" id="pi_buyer_name" value="{{ optional($order->pi)->buyer_name ?? '' }}">
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('buyer_address', $order->lc->buyer_address ?? (optional($order->pi)->buyer_address ?? ''), ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_buyer_address']) }}
                    <input type="hidden" id="pi_buyer_address" value="{{ optional($order->pi)->buyer_address ?? '' }}">
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_mobile', __('Mobile No'), ['class' => 'form-label']) }}
                    {{ Form::text('buyer_mobile', $order->lc->buyer_mobile ?? (optional($order->pi)->buyer_mobile ?? ''), ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('buyer_email', __('Email'), ['class' => 'form-label']) }}
                    {{ Form::email('buyer_email', $order->lc->buyer_email ?? (optional($order->pi)->buyer_email ?? ''), ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 d-none" id="seller_buyer_warning">
        <div class="alert alert-warning py-2">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ __('Warning: LC Seller or Buyer information does not match PI.') }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->lc->latest_shipment_date ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_validity_date', $order->lc->lc_validity_date ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

@push('script-page')
<script>
    $(document).ready(function() {
        function checkLCValidity() {
            var piDateStr = $('#pi_date').val();
            var validityDays = parseInt($('#pi_validity').val()) || 0;
            var lcDateStr = $('#lc_date').val();
            
            if (piDateStr && lcDateStr && validityDays > 0) {
                var piDate = new Date(piDateStr);
                var lcDate = new Date(lcDateStr);
                var expiryDate = new Date(piDate);
                expiryDate.setDate(expiryDate.getDate() + validityDays);
                
                if (lcDate > expiryDate) {
                    $('#validity_warning').removeClass('d-none');
                } else {
                    $('#validity_warning').addClass('d-none');
                }
            }
        }

        function checkAmountMismatch() {
            var piAmount = parseFloat($('#pi_amount').val());
            var lcAmount = parseFloat($('#lc_amount').val());
            if (piAmount && lcAmount && piAmount != lcAmount) {
                $('#amount_mismatch_warning').removeClass('d-none');
            } else {
                $('#amount_mismatch_warning').addClass('d-none');
            }
        }

        function checkSellerBuyerMismatch() {
            var piSeller = $('#pi_seller_name').val().trim();
            var lcSeller = $('#lc_seller_name').val().trim();
            var piBuyer = $('#pi_buyer_name').val().trim();
            var lcBuyer = $('#lc_buyer_name').val().trim();
            
            if ((piSeller && lcSeller && piSeller != lcSeller) || (piBuyer && lcBuyer && piBuyer != lcBuyer)) {
                $('#seller_buyer_warning').removeClass('d-none');
            } else {
                $('#seller_buyer_warning').addClass('d-none');
            }
        }

        $('#lc_amount').on('keyup change', checkAmountMismatch);
        $('#lc_date').on('change', checkLCValidity);
        $('#lc_seller_name, #lc_buyer_name').on('keyup change', checkSellerBuyerMismatch);

        checkAmountMismatch();
        checkLCValidity();
        checkSellerBuyerMismatch();
    });
</script>
@endpush

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->lc)
            <a href="{{ route('sales-orders.lc.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.lc.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to CI') }}</button>
</div>
{{ Form::close() }}
