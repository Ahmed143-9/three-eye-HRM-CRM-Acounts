<h5 class="fw-bold mb-0">{{ __('Step 4: Commercial Invoice (CI)') }}</h5>
<p class="text-muted mb-0" style="font-size:0.85rem;">{{ __('Step 4 of 7') }}</p>
<hr class="mt-2 mb-3">

@php
    $totalOrderQty = $order->po && $order->po->items ? $order->po->items->sum('quantity') : 0;
    $deliveredQty  = $order->ci && $order->ci->tankers ? $order->ci->tankers->sum('quantity_mt') : 0;
    $remainingQty  = $totalOrderQty - $deliveredQty;
    // PO defaults for pre-filling new rows
    $poUnit     = optional(optional($order->po)->items->first())->unit ?? 'MT';
    $poCurrency = optional(optional($order->po)->items->first())->currency ?? 'USD';
@endphp

{{-- Delivery Tracking Matrix --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">{{ __('Delivery Tracking Matrix (Based on PO Total)') }}</h6>
            </div>
            <div class="card-body bg-light">
                <div class="row text-center">
                    <div class="col-md-4 border-end">
                        <p class="text-muted mb-1">{{ __('Total Order Qty') }}</p>
                        <h4 class="text-primary" id="matrix_total">{{ number_format($totalOrderQty, 3) }} {{ $poUnit }}</h4>
                        <input type="hidden" id="matrix_total_val" value="{{ $totalOrderQty }}">
                    </div>
                    <div class="col-md-4 border-end">
                        <p class="text-muted mb-1">{{ __('Delivered / Shipped') }}</p>
                        <h4 class="text-success" id="matrix_delivered">{{ number_format($deliveredQty, 3) }} {{ $poUnit }}</h4>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">{{ __('Remaining Qty') }}</p>
                        <h4 class="{{ $remainingQty >= 0 ? 'text-warning' : 'text-danger' }}" id="matrix_remaining">{{ number_format($remainingQty, 3) }} {{ $poUnit }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reference row --}}
<div class="row mb-3">
    <div class="col-md-4">
        <strong>{{ __('PO Ref:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('PI Ref:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}
        ({{ $order->pi->pi_date ?? '' }})
    </div>
    <div class="col-md-4">
        <strong>{{ __('LC Ref:') }}</strong> {{ $order->lc->lc_reference_no ?? 'N/A' }} ({{ $order->lc->lc_date ?? '' }})
    </div>
</div>

{{ Form::open(['route' => ['sales-orders.ci.store', $order->id], 'method' => 'post']) }}
<input type="hidden" name="ci_id" value="{{ $order->ci->id ?? '' }}">

{{-- CI Header Fields --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
         style="border-left:4px solid #1565c0;">
        <i class="ti ti-file-invoice text-primary"></i>
        <span class="fw-semibold text-dark">{{ __('CI Details') }}</span>
    </div>
    <div class="card-body px-4 py-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('ci_number', __('CI Number'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::text('ci_number', $order->ci->ci_number ?? 'CI-' . time(), ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('ci_date', __('CI Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('ci_date', $order->ci->ci_date ?? date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('lc_validity_date', $order->ci->lc_validity_date ?? optional($order->lc)->lc_validity_date, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label fw-semibold']) }}
                    {{ Form::date('latest_shipment_date', $order->ci->latest_shipment_date ?? optional($order->lc)->latest_shipment_date, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tanker Details --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <i class="ti ti-truck text-warning"></i>
    <h6 class="fw-semibold mb-0 text-dark">{{ __('Tanker Details for this Shipment') }}</h6>
</div>

<div class="table-responsive mt-3">
    <table class="table table-sm table-hover align-middle" id="ci-tankers-table">
        <thead class="bg-light">
            <tr>
                <th width="20%">{{ __('Tanker Number') }}</th>
                <th width="13%">{{ __('QTY') }}</th>
                <th width="13%">{{ __('Unit') }}</th>
                <th width="13%">{{ __('Price') }}</th>
                <th width="13%">{{ __('Currency') }}</th>
                <th width="13%">{{ __('Total Amount') }}</th>
                <th width="5%"></th>
            </tr>
        </thead>
        <tbody>
            @if($order->ci && $order->ci->tankers->count() > 0)
                @foreach($order->ci->tankers as $index => $tanker)
                    <tr>
                        <td><input type="text" name="tankers[{{$index}}][tanker_number]" class="form-control form-control-sm" value="{{$tanker->tanker_number}}" required></td>
                        <td><input type="number" step="0.001" name="tankers[{{$index}}][qty_mt]" class="form-control form-control-sm t-qty" value="{{$tanker->quantity_mt}}" required></td>
                        <td>
                            <select name="tankers[{{$index}}][quantity_unit]" class="form-control form-control-sm unit-select" required>
                                @foreach($units as $val => $label)
                                    <option value="{{$val}}" {{ ($tanker->quantity_unit ?? $poUnit) == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="tankers[{{$index}}][cpt_usd]" class="form-control form-control-sm t-cpt" value="{{$tanker->cpt_usd}}" required></td>
                        <td>
                            <select name="tankers[{{$index}}][currency]" class="form-control form-control-sm curr-select" required>
                                @foreach($currencies as $val => $label)
                                    <option value="{{$val}}" {{ ($tanker->currency ?? $poCurrency) == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="tankers[{{$index}}][total_amount]" class="form-control form-control-sm t-total" value="{{$tanker->total_amount_usd}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-xs remove-tanker"><i class="ti ti-trash"></i></button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="tankers[0][tanker_number]" class="form-control form-control-sm" required></td>
                    <td><input type="number" step="0.001" name="tankers[0][qty_mt]" class="form-control form-control-sm t-qty" required></td>
                    <td>
                        <select name="tankers[0][quantity_unit]" class="form-control form-control-sm unit-select" required>
                            @foreach($units as $val => $label)
                                <option value="{{$val}}" {{ $val == $poUnit ? 'selected' : '' }}>{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="tankers[0][cpt_usd]" class="form-control form-control-sm t-cpt" required></td>
                    <td>
                        <select name="tankers[0][currency]" class="form-control form-control-sm curr-select" required>
                            @foreach($currencies as $val => $label)
                                <option value="{{$val}}" {{ $val == $poCurrency ? 'selected' : '' }}>{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="tankers[0][total_amount]" class="form-control form-control-sm t-total" readonly></td>
                    <td><input type="file" name="tankers[0][file]" class="form-control form-control-sm" accept="image/*,application/pdf"></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="table-active">
                <td class="fw-bold">{{ __('TOTALS') }}</td>
                <td class="fw-bold"><span id="ci_total_qty">0.000</span></td>
                <td colspan="3"></td>
                <td class="fw-bold"><span id="ci_total_amount">0.00</span></td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm add-tanker"><i class="ti ti-plus"></i></button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->ci)
            <a href="{{ route('sales-orders.ci.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.ci.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-2"
            style="background-color:#6fd943;border-color:#6fd943;padding:10px 25px;font-weight:600;">
        {{ __('Save & Proceed to Packing List') }}
        <i class="ti ti-chevron-right"></i>
    </button>
</div>
{{ Form::close() }}

@push('script-page')
<script>
    // Data for adding new rows — PO defaults
    var poDefaultUnit     = @json($poUnit);
    var poDefaultCurrency = @json($poCurrency);
    var allUnits      = @json($units);
    var allCurrencies = @json($currencies);

    function buildUnitOptions(selected) {
        var html = '';
        $.each(allUnits, function(val, label) {
            html += '<option value="' + val + '"' + (val == selected ? ' selected' : '') + '>' + label + '</option>';
        });
        html += '<option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __("Add New") }}</option>';
        return html;
    }
    function buildCurrOptions(selected) {
        var html = '';
        $.each(allCurrencies, function(val, label) {
            html += '<option value="' + val + '"' + (val == selected ? ' selected' : '') + '>' + label + '</option>';
        });
        html += '<option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __("Add New") }}</option>';
        return html;
    }

    $(document).ready(function () {
        function recalcTotals() {
            var totalQty = 0, totalAmt = 0;
            $('#ci-tankers-table tbody tr').each(function () {
                var qty = parseFloat($(this).find('.t-qty').val()) || 0;
                var cpt = parseFloat($(this).find('.t-cpt').val()) || 0;
                var tot = qty * cpt;
                $(this).find('.t-total').val(tot.toFixed(2));
                totalQty += qty;
                totalAmt += tot;
            });
            $('#ci_total_qty').text(totalQty.toFixed(3));
            $('#ci_total_amount').text(totalAmt.toFixed(2));
        }

        $(document).on('keyup change', '.t-qty, .t-cpt', recalcTotals);
        recalcTotals();

        $(document).on('click', '.add-tanker', function () {
            var index = $('#ci-tankers-table tbody tr').length;
            var row = '<tr>' +
                '<td><input type="text" name="tankers[' + index + '][tanker_number]" class="form-control form-control-sm" required></td>' +
                '<td><input type="number" step="0.001" name="tankers[' + index + '][qty_mt]" class="form-control form-control-sm t-qty" required></td>' +
                '<td><select name="tankers[' + index + '][quantity_unit]" class="form-control form-control-sm unit-select" required>' + buildUnitOptions(poDefaultUnit) + '</select></td>' +
                '<td><input type="number" step="0.01" name="tankers[' + index + '][cpt_usd]" class="form-control form-control-sm t-cpt" required></td>' +
                '<td><select name="tankers[' + index + '][currency]" class="form-control form-control-sm curr-select" required>' + buildCurrOptions(poDefaultCurrency) + '</select></td>' +
                '<td><input type="number" step="0.01" name="tankers[' + index + '][total_amount]" class="form-control form-control-sm t-total" readonly></td>' +
                '<td><button type="button" class="btn btn-danger btn-xs remove-tanker"><i class="ti ti-trash"></i></button></td>' +
                '</tr>';
            $('#ci-tankers-table tbody').append(row);
        });

        $(document).on('click', '.remove-tanker', function () {
            $(this).closest('tr').remove();
            recalcTotals();
        });
    });
</script>
@endpush
