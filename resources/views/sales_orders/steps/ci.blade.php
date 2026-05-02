<h5>{{ __('Step 4: Commercial Invoice (CI)') }}</h5>
<hr>

@php
    $totalOrderQty = $order->po && $order->po->items ? $order->po->items->sum('quantity') : 0;
    $deliveredQty = $order->ci && $order->ci->tankers ? $order->ci->tankers->sum('quantity_mt') : 0;
    $remainingQty = $totalOrderQty - $deliveredQty;
@endphp
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
                        <h4 class="text-primary" id="matrix_total">{{ number_format($totalOrderQty, 3) }} MT</h4>
                        <input type="hidden" id="matrix_total_val" value="{{ $totalOrderQty }}">
                    </div>
                    <div class="col-md-4 border-end">
                        <p class="text-muted mb-1">{{ __('Delivered / Shipped') }}</p>
                        <h4 class="text-success" id="matrix_delivered">{{ number_format($deliveredQty, 3) }} MT</h4>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">{{ __('Remaining Qty') }}</p>
                        <h4 class="{{ $remainingQty >= 0 ? 'text-warning' : 'text-danger' }}" id="matrix_remaining">{{ number_format($remainingQty, 3) }} MT</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3 text-sm">
    <div class="col-md-4">
        <strong>{{ __('PO Ref:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('PI Ref:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}
        ({{ $order->pi->pi_date ?? '' }})
    </div>
    <div class="col-md-4">
        <strong>{{ __('LC Ref:') }}</strong> {{ $order->lc->lc_no ?? 'N/A' }} ({{ $order->lc->lc_date ?? '' }})
    </div>
</div>

{{ Form::open(['route' => ['sales-orders.ci.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('ci_number', __('CI Number'), ['class' => 'form-label']) }}
            {{ Form::text('ci_number', $order->ci->ci_number ?? 'CI-' . time(), ['class' => 'form-control form-control-sm', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('ci_date', __('CI Date'), ['class' => 'form-label']) }}
            {{ Form::text('ci_date', isset($order->ci->ci_date) ? \Carbon\Carbon::parse($order->ci->ci_date)->format('m-d-Y') : date('m-d-Y'), ['class' => 'form-control form-control-sm date-format-input', 'required' => 'required', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-muted text-xs">{{ __('Format: MM-DD-YYYY') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::text('lc_validity_date', isset($order->ci->lc_validity_date) ? \Carbon\Carbon::parse($order->ci->lc_validity_date)->format('m-d-Y') : (isset($order->lc->lc_validity_date) ? \Carbon\Carbon::parse($order->lc->lc_validity_date)->format('m-d-Y') : null), ['class' => 'form-control form-control-sm date-format-input', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-muted text-xs">{{ __('Format: MM-DD-YYYY') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::text('latest_shipment_date', isset($order->ci->latest_shipment_date) ? \Carbon\Carbon::parse($order->ci->latest_shipment_date)->format('m-d-Y') : (isset($order->lc->latest_shipment_date) ? \Carbon\Carbon::parse($order->lc->latest_shipment_date)->format('m-d-Y') : null), ['class' => 'form-control form-control-sm date-format-input', 'placeholder' => 'MM-DD-YYYY']) }}
            <small class="text-muted text-xs">{{ __('Format: MM-DD-YYYY') }}</small>
        </div>
    </div>
</div>

<h6 class="mt-3">{{ __('Tanker Details') }}</h6>
<div class="table-responsive mt-3">
    <table class="table table-sm table-hover align-middle" id="ci-tankers-table">
        <thead class="bg-light">
            <tr>
                <th width="20%">{{ __('Tanker Number') }}</th>
                <th width="15%">{{ __('QTY') }}</th>
                <th width="15%">{{ __('Unit') }}</th>
                <th width="15%">{{ __('Price') }}</th>
                <th width="15%">{{ __('Currency') }}</th>
                <th width="15%">{{ __('Total Amount') }}</th>
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
                                    <option value="{{$val}}" {{ ($tanker->quantity_unit ?? 'MT') == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="tankers[{{$index}}][cpt_usd]" class="form-control form-control-sm t-cpt" value="{{$tanker->cpt_usd}}" required></td>
                        <td>
                            <select name="tankers[{{$index}}][currency]" class="form-control form-control-sm curr-select" required>
                                @foreach($currencies as $val => $label)
                                    <option value="{{$val}}" {{ ($tanker->currency ?? 'USD') == $val ? 'selected' : '' }}>{{$label}}</option>
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
                                <option value="{{$val}}">{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="tankers[0][cpt_usd]" class="form-control form-control-sm t-cpt" required></td>
                    <td>
                        <select name="tankers[0][currency]" class="form-control form-control-sm curr-select" required>
                            @foreach($currencies as $val => $label)
                                <option value="{{$val}}">{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="tankers[0][total_amount]" class="form-control form-control-sm t-total" readonly></td>
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

</div>
{{ Form::close() }}
