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
            {{ Form::label('lc_no', __('LC No'), ['class' => 'form-label']) }}
            {{ Form::text('lc_no', $order->lc->lc_no ?? null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('amount', __('LC Amount (USD)'), ['class' => 'form-label']) }}
            {{ Form::number('amount', $order->lc->amount ?? (optional($order->pi)->amount ?? 0), ['class' => 'form-control', 'step' => '0.01', 'id' => 'lc_amount', 'required' => 'required']) }}
            <input type="hidden" id="pi_amount" value="{{ optional($order->pi)->amount ?? 0 }}">
        </div>
    </div>
    <div class="col-md-12 d-none" id="mismatch_warning">
        <div class="alert alert-warning py-2">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ __('Warning: LC Amount does not match the PI Amount.') }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_date', __('LC Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_date', $order->lc->lc_date ?? date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->lc->latest_shipment_date ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_validity_date', $order->lc->lc_validity_date ?? null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

@push('script-page')
<script>
    $(document).ready(function() {
        function checkMismatch() {
            var piAmount = parseFloat($('#pi_amount').val());
            var lcAmount = parseFloat($('#lc_amount').val());
            if (piAmount && lcAmount && piAmount != lcAmount) {
                $('#mismatch_warning').removeClass('d-none');
            } else {
                $('#mismatch_warning').addClass('d-none');
            }
        }
        $('#lc_amount').on('keyup change', checkMismatch);
        checkMismatch();
    });
</script>
@endpush

<div class="text-end mt-3">
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to CI') }}</button>
</div>
{{ Form::close() }}
