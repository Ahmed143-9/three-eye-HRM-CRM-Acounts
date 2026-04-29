<h5>{{ __('Step 4: Commercial Invoice (CI)') }}</h5>
<hr>
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

{{ Form::open(['route' => ['sales-orders.ci.store', $order->id], 'method' => 'post']) }}
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
            {{ Form::date('ci_date', $order->ci->ci_date ?? date('Y-m-d'), ['class' => 'form-control form-control-sm', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_validity_date', $order->ci->lc_validity_date ?? (optional($order->lc)->lc_validity_date), ['class' => 'form-control form-control-sm']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->ci->latest_shipment_date ?? (optional($order->lc)->latest_shipment_date), ['class' => 'form-control form-control-sm']) }}
        </div>
    </div>
</div>

<h6 class="mt-3">{{ __('Tanker Details') }}</h6>
<div class="table-responsive">
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

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->ci)
            <a href="{{ route('sales-orders.ci.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.ci.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Packing List') }}</button>
</div>
{{ Form::close() }}
