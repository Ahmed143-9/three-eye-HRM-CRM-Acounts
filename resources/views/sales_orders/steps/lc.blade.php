{{-- Step Header --}}
<div class="d-flex align-items-center justify-content-between mb-1">
    <div>
        <h5 class="fw-bold mb-0">{{ __('Step 3: Letter of Credit (LC)') }}</h5>
        <p class="text-muted mb-0" style="font-size:0.85rem;">{{ __('Step 3 of 7') }}</p>
    </div>
    <span class="badge rounded-pill px-3 py-2"
          style="background-color:#e8f5e9;color:#2e7d32;font-size:0.8rem;font-weight:600;letter-spacing:0.03em;">
        <i class="ti ti-file-certificate me-1"></i>{{ __('LC Details') }}
    </span>
</div>
<hr class="mt-2 mb-3">

{{-- Linked PI Info Alert --}}
@php
    $poItems = ($order->po && $order->po->items) ? $order->po->items : collect();
    $poQty   = $poItems->sum('quantity');
    $poUnit  = $poItems->first()->unit ?? '';
    $piQty   = optional($order->pi)->quantity ?? $poQty;

    // System-generated LC Reference Number
    $lc_ref = $order->lc->lc_reference_no ?? null;
    if (!$lc_ref) {
        $cName = $order->customer->name ?? 'LC';
        $prefix = strtoupper(substr(trim($cName), 0, 2));
        $lc_ref = $prefix . '-LC-' . time();
    }
@endphp

<div class="alert mb-4 py-3 px-4 d-flex flex-wrap align-items-center gap-3 shadow-sm border-0 rounded-3"
     style="background:linear-gradient(135deg,#e3f2fd 0%,#e8f5e9 100%);color:#1a237e;">
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle d-flex align-items-center justify-content-center"
              style="width:34px;height:34px;background:#1565c0;color:#fff;flex-shrink:0;">
            <i class="ti ti-link" style="font-size:1rem;"></i>
        </span>
        <div>
            <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">{{ __('Linked PI') }}</div>
            <div class="fw-bold" style="font-size:0.95rem;">{{ optional($order->pi)->pi_number ?? 'N/A' }}</div>
        </div>
    </div>
    <div class="vr d-none d-md-block opacity-25"></div>
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle d-flex align-items-center justify-content-center"
              style="width:34px;height:34px;background:#2e7d32;color:#fff;flex-shrink:0;">
            <i class="ti ti-calendar" style="font-size:1rem;"></i>
        </span>
        <div>
            <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">{{ __('PI Date') }}</div>
            <div class="fw-bold" style="font-size:0.95rem;">{{ optional($order->pi)->pi_date ?? '—' }}</div>
        </div>
    </div>
    <div class="vr d-none d-md-block opacity-25"></div>
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle d-flex align-items-center justify-content-center"
              style="width:34px;height:34px;background:#6a1b9a;color:#fff;flex-shrink:0;">
            <i class="ti ti-weight" style="font-size:1rem;"></i>
        </span>
        <div>
            <div class="text-muted" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">{{ __('PO Qty') }}</div>
            <div class="fw-bold" style="font-size:0.95rem;">{{ number_format($poQty, 2) }} {{ $poUnit }}</div>
        </div>
    </div>
</div>

{{ Form::open(['route' => ['sales-orders.lc.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}

{{-- LC Core Fields Card --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
         style="border-left:4px solid #1565c0;">
        <i class="ti ti-id-badge text-primary"></i>
        <span class="fw-semibold text-dark">{{ __('LC Core Information') }}</span>
    </div>
    <div class="card-body px-4 py-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lc_reference_no', __('LC Reference No.'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('lc_reference_no', $lc_ref, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter LC reference number')]) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('client_lc_no', __('Client LC No.'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('client_lc_no', $order->lc->client_lc_no ?? null, ['class' => 'form-control', 'placeholder' => __('Client LC reference')]) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lc_type', __('LC Type'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('lc_type', $order->lc->lc_type ?? null, ['class' => 'form-control', 'placeholder' => __('e.g. Irrevocable, Sight')]) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lc_qty', __('LC Quantity'), ['class' => 'form-label fw-semibold']) }}
                    <div class="input-group">
                        {{ Form::number('lc_qty', $order->lc->lc_qty ?? $poQty, ['class' => 'form-control', 'step' => '0.01', 'id' => 'lc_qty', 'placeholder' => '0.00']) }}
                        <span class="input-group-text text-muted">{{ $poUnit }}</span>
                    </div>
                    <small class="text-danger d-none mismatch-label"
                        id="warning_qty">{{ __('Warning: Does not match PI quantity (') . number_format($piQty, 2) . ')' }}</small>
                    <input type="hidden" id="pi_qty" value="{{ $piQty }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('unit', __('Unit (from PO)'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('unit', $order->lc->unit ?? $poUnit, ['class' => 'form-control bg-light', 'readonly' => 'readonly']) }}
                    <small class="text-muted">{{ __('Auto-filled from PO items — not editable') }}</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lc_date', __('LC Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('lc_date', $order->lc->lc_date ?? null, ['class' => 'form-control', 'id' => 'lc_date']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_date">{{ __('Warning: Exceeds PI Validity') }}</small>
                    <input type="hidden" id="pi_date" value="{{ optional($order->pi)->pi_date ?? '' }}">
                    <input type="hidden" id="pi_validity" value="{{ optional($order->pi)->validity ?? 0 }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('latest_shipment_date', $order->lc->latest_shipment_date ?? null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('lc_validity_date', $order->lc->lc_validity_date ?? null, ['class' => 'form-control', 'id' => 'lc_validity_date']) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Trade & Shipment Terms Card --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
         style="border-left:4px solid #f57f17;">
        <i class="ti ti-ship text-warning"></i>
        <span class="fw-semibold text-dark">{{ __('Trade & Shipment Terms') }}</span>
    </div>
    <div class="card-body px-4 py-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('lifting_time', __('Lifting Time'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('lifting_time', $order->lc->lifting_time ?? null, ['class' => 'form-control', 'id' => 'lc_lifting_time', 'placeholder' => __('e.g. 30 days')]) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_lifting_time">{{ __('Warning: Does not match PI (') . (optional($order->pi)->lifting_time ?? 'N/A') . ')' }}</small>
                    <input type="hidden" id="pi_lifting_time_val" value="{{ optional($order->pi)->lifting_time ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('country_of_origin', __('Country of Origin'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('country_of_origin', $order->lc->country_of_origin ?? null, ['class' => 'form-control', 'id' => 'lc_country_of_origin']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_country_of_origin">{{ __('Warning: Does not match PI (') . (optional($order->pi)->country_of_origin ?? 'N/A') . ')' }}</small>
                    <input type="hidden" id="pi_country_of_origin" value="{{ optional($order->pi)->country_of_origin ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('tolerance', __('Tolerance'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('tolerance', $order->lc->tolerance ?? null, ['class' => 'form-control', 'id' => 'lc_tolerance', 'placeholder' => __('e.g. ±5%')]) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_tolerance">{{ __('Warning: Does not match PI (') . (optional($order->pi)->tolerance ?? 'N/A') . ')' }}</small>
                    <input type="hidden" id="pi_tolerance" value="{{ optional($order->pi)->tolerance ?? '' }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('port_of_loading', __('Port of Loading'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('port_of_loading', $order->lc->port_of_loading ?? null, ['class' => 'form-control', 'id' => 'lc_port_of_loading']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_port_of_loading">{{ __('Warning: Does not match PI (') . (optional($order->pi)->port_of_loading ?? 'N/A') . ')' }}</small>
                    <input type="hidden" id="pi_port_of_loading" value="{{ optional($order->pi)->port_of_loading ?? '' }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('port_of_discharge', __('Port of Discharge'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('port_of_discharge', $order->lc->port_of_discharge ?? null, ['class' => 'form-control', 'id' => 'lc_port_of_discharge']) }}
                    <small class="text-danger d-none mismatch-label"
                        id="warning_port_of_discharge">{{ __('Warning: Does not match PI (') . (optional($order->pi)->port_of_discharge ?? 'N/A') . ')' }}</small>
                    <input type="hidden" id="pi_port_of_discharge" value="{{ optional($order->pi)->port_of_discharge ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Seller & Buyer Cards --}}
<div class="row g-3 mb-4">
    {{-- Seller Card --}}
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0 rounded-3">
            <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
                 style="border-left:4px solid #e65100;">
                <i class="ti ti-building-store" style="color:#e65100;"></i>
                <span class="fw-semibold text-dark">{{ __('Seller Information') }}</span>
                <span class="ms-auto badge rounded-pill"
                      style="background:#fff3e0;color:#e65100;font-size:0.72rem;font-weight:600;">{{ __('Cross-Check') }}</span>
            </div>
            <div class="card-body px-4 py-3">
                <div class="form-group mb-3">
                    {{ Form::label('seller_name', __('Name'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('seller_name', $order->lc->seller_name ?? null, ['class' => 'form-control', 'id' => 'lc_seller_name']) }}
                    <small class="text-danger d-none mismatch-label" id="warning_seller_name">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_seller_name" value="{{ optional($order->pi)->seller_name ?? '' }}">
                </div>
                <div class="form-group mb-3">
                    {{ Form::label('seller_address', __('Address'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::textarea('seller_address', $order->lc->seller_address ?? null, ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_seller_address']) }}
                    <small class="text-danger d-none mismatch-label" id="warning_seller_address">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_seller_address" value="{{ optional($order->pi)->seller_address ?? '' }}">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-group">
                            {{ Form::label('seller_mobile', __('Mobile'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::text('seller_mobile', $order->lc->seller_mobile ?? null, ['class' => 'form-control', 'id' => 'lc_seller_mobile']) }}
                            <small class="text-danger d-none mismatch-label" id="warning_seller_mobile">{{ __('Warning: Mismatch with PI') }}</small>
                            <input type="hidden" id="pi_seller_mobile" value="{{ optional($order->pi)->seller_mobile ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            {{ Form::label('seller_email', __('Email'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::text('seller_email', $order->lc->seller_email ?? null, ['class' => 'form-control', 'id' => 'lc_seller_email']) }}
                            <small class="text-danger d-none mismatch-label" id="warning_seller_email">{{ __('Warning: Mismatch with PI') }}</small>
                            <input type="hidden" id="pi_seller_email" value="{{ optional($order->pi)->seller_email ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Buyer Card --}}
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0 rounded-3">
            <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
                 style="border-left:4px solid #1565c0;">
                <i class="ti ti-user-circle" style="color:#1565c0;"></i>
                <span class="fw-semibold text-dark">{{ __('Buyer Information') }}</span>
                <span class="ms-auto badge rounded-pill"
                      style="background:#e3f2fd;color:#1565c0;font-size:0.72rem;font-weight:600;">{{ __('Cross-Check') }}</span>
            </div>
            <div class="card-body px-4 py-3">
                <div class="form-group mb-3">
                    {{ Form::label('buyer_name', __('Name'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('buyer_name', $order->lc->buyer_name ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_name']) }}
                    <small class="text-danger d-none mismatch-label" id="warning_buyer_name">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_buyer_name" value="{{ optional($order->pi)->buyer_name ?? '' }}">
                </div>
                <div class="form-group mb-3">
                    {{ Form::label('buyer_address', __('Address'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::textarea('buyer_address', $order->lc->buyer_address ?? null, ['class' => 'form-control', 'rows' => 2, 'id' => 'lc_buyer_address']) }}
                    <small class="text-danger d-none mismatch-label" id="warning_buyer_address">{{ __('Warning: Mismatch with PI') }}</small>
                    <input type="hidden" id="pi_buyer_address" value="{{ optional($order->pi)->buyer_address ?? '' }}">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-group">
                            {{ Form::label('buyer_mobile', __('Mobile'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::text('buyer_mobile', $order->lc->buyer_mobile ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_mobile']) }}
                            <small class="text-danger d-none mismatch-label" id="warning_buyer_mobile">{{ __('Warning: Mismatch with PI') }}</small>
                            <input type="hidden" id="pi_buyer_mobile" value="{{ optional($order->pi)->buyer_mobile ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            {{ Form::label('buyer_email', __('Email'), ['class' => 'form-label fw-semibold']) }}
                            {{ Form::text('buyer_email', $order->lc->buyer_email ?? null, ['class' => 'form-control', 'id' => 'lc_buyer_email']) }}
                            <small class="text-danger d-none mismatch-label" id="warning_buyer_email">{{ __('Warning: Mismatch with PI') }}</small>
                            <input type="hidden" id="pi_buyer_email" value="{{ optional($order->pi)->buyer_email ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Terms & Conditions Card --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
         style="border-left:4px solid #5c6bc0;">
        <i class="ti ti-notes" style="color:#5c6bc0;"></i>
        <span class="fw-semibold text-dark">{{ __('Terms & Conditions') }}</span>
    </div>
    <div class="card-body px-4 py-3">
        <div class="form-group mb-0">
            {{ Form::label('terms_and_conditions', __('Terms and Conditions'), ['class' => 'form-label fw-semibold']) }}
            {{ Form::textarea('terms_and_conditions', $order->lc->terms_and_conditions ?? null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter terms and conditions...')]) }}
        </div>
    </div>
</div>

@push('script-page')
<script>
    $(document).ready(function () {
        function checkMismatch(inputId, piValueId, warningId, type) {
            type = type || 'text';
            var val   = $('#' + inputId).val();
            var piVal = $('#' + piValueId).val();

            if (!val && !piVal) {
                $('#' + warningId).addClass('d-none');
                return;
            }

            var mismatch = false;
            if (type === 'number') {
                mismatch = Math.abs(parseFloat(val) - parseFloat(piVal)) > 0.01;
            } else if (type === 'date_validity') {
                var piDateStr    = $('#pi_date').val();
                var validityDays = parseInt($('#pi_validity').val()) || 0;
                if (piDateStr && val && validityDays > 0) {
                    var piDate     = new Date(piDateStr);
                    var lcDate     = new Date(val);
                    var expiryDate = new Date(piDate);
                    expiryDate.setDate(expiryDate.getDate() + validityDays);
                    mismatch = lcDate > expiryDate;
                }
            } else if (type === 'date_match') {
                mismatch = val !== piVal;
            } else {
                mismatch = (val || '').trim().toLowerCase() !== (piVal || '').trim().toLowerCase();
            }

            $('#' + warningId).toggleClass('d-none', !mismatch);
        }

        // Quantity vs PI quantity
        $('#lc_qty').on('keyup change', function () {
            checkMismatch('lc_qty', 'pi_qty', 'warning_qty', 'number');
        });

        // Date vs PI validity
        $('#lc_date').on('change', function () {
            checkMismatch('lc_date', 'pi_date', 'warning_date', 'date_validity');
        });

        // Shipment date match (if referenced elsewhere)
        $('#lc_shipment_date').on('change', function () {
            checkMismatch('lc_shipment_date', 'pi_shipment_date', 'warning_shipment_date', 'date_match');
        });

        // Text field comparisons
        var textFields = [
            'seller_name', 'seller_address', 'seller_mobile', 'seller_email',
            'buyer_name',  'buyer_address',  'buyer_mobile',  'buyer_email',
            'lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge'
        ];

        textFields.forEach(function (field) {
            var inputId  = (field === 'lifting_time') ? 'lc_lifting_time' : 'lc_' + field;
            var piValId  = (field === 'lifting_time') ? 'pi_lifting_time_val' : 'pi_' + field;
            $('#' + inputId).on('keyup change', function () {
                checkMismatch(inputId, piValId, 'warning_' + field);
            });
        });

        // Trigger all checks on page load
        $('#lc_qty').trigger('change');
        $('#lc_date').trigger('change');
        $('#lc_shipment_date').trigger('change');
        textFields.forEach(function (field) {
            var inputId = (field === 'lifting_time') ? 'lc_lifting_time' : 'lc_' + field;
            $('#' + inputId).trigger('change');
        });
    });
</script>
@endpush

{{-- Navigation Buttons --}}
<div class="d-flex justify-content-between align-items-center mt-3">
    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1"
            onclick="switchWorkflowStep('pills-pi-tab')">
        <i class="ti ti-chevron-left"></i>{{ __('Previous: PI') }}
    </button>
    <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-2"
            style="background-color:#6fd943;border-color:#6fd943;padding:10px 25px;font-weight:600;">
        {{ __('Save & Proceed to CI') }}
        <i class="ti ti-chevron-right"></i>
    </button>
</div>
{{ Form::close() }}