<h5 class="fw-bold mb-0">{{ __('Step 8: Delivery') }}</h5>
<p class="text-muted mb-0" style="font-size:0.85rem;">{{ __('Final Step') }}</p>
<hr class="mt-2 mb-3">

{{ Form::open(['route' => ['sales-orders.delivery.store', $order->id], 'method' => 'post', 'id' => 'delivery-form']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            @php
                $existingModes = \App\Models\SalesDelivery::select('delivery_mode')->distinct()->pluck('delivery_mode')->filter()->toArray();
                $defaultModes = ['Road', 'Rail', 'Sea'];
                $allModes = array_unique(array_merge($defaultModes, $existingModes));
                
                $existingPacking = \App\Models\SalesDelivery::select('packing_type')->distinct()->pluck('packing_type')->filter()->toArray();
                $defaultPacking = ['200 kg drum', '1000 kg IBC'];
                $allPacking = array_unique(array_merge($defaultPacking, $existingPacking));
            @endphp
            {{ Form::label('delivery_mode', __('Delivery Mode'), ['class' => 'form-label']) }}
            <select name="delivery_mode" id="delivery_mode" class="form-control select2" required>
                <option value="">{{ __('Select Delivery Mode') }}</option>
                @foreach($allModes as $mode)
                    <option value="{{ $mode }}" {{ (isset($order->delivery) && $order->delivery->delivery_mode == $mode) ? 'selected' : '' }}>{{ $mode }}</option>
                @endforeach
                <option value="ADD_NEW_MODE" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
            </select>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('packing_type', __('Packing Type'), ['class' => 'form-label']) }}
            <select name="packing_type" id="packing_type" class="form-control select2" required>
                <option value="">{{ __('Select Packing Type') }}</option>
                @foreach($allPacking as $packing)
                    @php 
                        $numVal = filter_var($packing, FILTER_SANITIZE_NUMBER_INT);
                        $numVal = $numVal ? $numVal : '0';
                    @endphp
                    <option value="{{ $packing }}" data-val="{{ $numVal }}" {{ (isset($order->delivery) && $order->delivery->packing_type == $packing) ? 'selected' : '' }}>{{ $packing }}</option>
                @endforeach
                <option value="ADD_NEW_PACKING" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
            </select>
            <input type="hidden" id="packing_weight" value="{{ isset($order->delivery) ? filter_var($order->delivery->packing_type, FILTER_SANITIZE_NUMBER_INT) : '0' }}">
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card bg-light-primary text-center p-3">
            <h6 class="text-muted">{{ __('Total Quantity (MT)') }}</h6>
            @php
                $totalMt = 0;
                if ($order->ci && $order->ci->tankers) {
                    $totalMt = $order->ci->tankers->sum('quantity_mt');
                }
            @endphp
            <h4 id="total_mt_display">{{ number_format($totalMt, 3) }}</h4>
            <input type="hidden" name="total_quantity_mt" id="total_quantity_mt" value="{{ $totalMt }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light-info text-center p-3">
            <h6 class="text-muted">{{ __('Total Quantity (KG)') }}</h6>
            <h4 id="total_kg_display">{{ number_format($totalMt * 1000, 3) }}</h4>
            <input type="hidden" name="total_quantity_kg" id="total_quantity_kg" value="{{ $totalMt * 1000 }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light-success text-center p-3">
            <h6 class="text-muted">{{ __('Required Units') }}</h6>
            <h4 id="required_units_display">{{ isset($order->delivery) ? number_format($order->delivery->required_units, 2) : '0.00' }}</h4>
            <input type="hidden" name="required_units" id="required_units" value="{{ isset($order->delivery) ? $order->delivery->required_units : 0 }}">
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white border-bottom pb-0">
        <h6 class="fw-bold mb-0 text-dark"><i class="ti ti-package me-2 text-primary"></i>{{ __('Additional Packaging / Drum Billing') }}</h6>
    </div>
    <div class="card-body bg-light-secondary rounded-bottom p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Qty') }}</label>
                <input type="number" name="drum_qty" id="drum_qty" class="form-control form-control-sm" value="{{ isset($order->delivery) ? $order->delivery->drum_qty : '' }}" placeholder="e.g. 50">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Unit') }}</label>
                <select name="drum_unit" class="form-control form-control-sm select2">
                    <option value="Drums" {{ (isset($order->delivery) && $order->delivery->drum_unit == 'Drums') ? 'selected' : '' }}>Drums</option>
                    <option value="IBCs" {{ (isset($order->delivery) && $order->delivery->drum_unit == 'IBCs') ? 'selected' : '' }}>IBCs</option>
                    <option value="Bags" {{ (isset($order->delivery) && $order->delivery->drum_unit == 'Bags') ? 'selected' : '' }}>Bags</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Buying Price') }}</label>
                <input type="number" step="0.01" name="drum_buying_price" id="drum_buying_price" class="form-control form-control-sm" value="{{ isset($order->delivery) ? $order->delivery->drum_buying_price : '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Total Buying') }}</label>
                <input type="number" step="0.01" name="drum_buying_total" id="drum_buying_total" class="form-control form-control-sm bg-white" readonly value="{{ isset($order->delivery) ? $order->delivery->drum_buying_total : '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold text-success">{{ __('Selling Price') }}</label>
                <input type="number" step="0.01" name="drum_selling_price" id="drum_selling_price" class="form-control form-control-sm border-success" value="{{ isset($order->delivery) ? $order->delivery->drum_selling_price : '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold text-success">{{ __('Total Selling') }}</label>
                <input type="number" step="0.01" name="drum_selling_total" id="drum_selling_total" class="form-control form-control-sm bg-white border-success" readonly value="{{ isset($order->delivery) ? $order->delivery->drum_selling_total : '' }}">
            </div>
        </div>
        <div class="text-muted small mt-2">
            <i class="ti ti-info-circle"></i> {{ __('If packaging (like drums) is billable to the client, enter the details above to generate an Accounts Receivable entry.') }}
        </div>
    </div>
</div>

<div class="text-end mt-4">
    @if($order->status == 'completed')
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">{{ __('Create Delivery Order & Send to Transport') }}</button>
    @elseif($order->status == 'finalized')
        <div class="alert alert-success d-inline-block text-start mb-0 me-3">
            <i class="ti ti-check"></i> {{ __('Delivery Order Created and Finalized.') }}
        </div>
        <a href="{{ route('transports.create') }}?sales_order_id={{ $order->id }}" class="btn btn-success btn-lg px-4 shadow">{{ __('Proceed to Transport Request') }}</a>
    @endif
</div>
{{ Form::close() }}

@push('script-page')
<script>
    $(document).ready(function() {
        function calculateUnits() {
            var totalKg = parseFloat($('#total_quantity_kg').val()) || 0;
            var packingWeight = parseFloat($('#packing_weight').val()) || 0;
            var requiredUnits = 0;
            
            if (packingWeight > 0) {
                requiredUnits = totalKg / packingWeight;
            }
            
            $('#required_units').val(requiredUnits.toFixed(2));
            $('#required_units_display').text(requiredUnits.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        $('#packing_type').on('change', function() {
            var select = $(this);
            var selectedOption = select.find('option:selected');
            
            if (select.val() === 'ADD_NEW_PACKING') {
                var newName = prompt("{{ __('Enter new packing type (e.g. 50 kg bag):') }}");
                if (newName) {
                    var numVal = newName.replace(/[^0-9.]/g, ''); // Extract number
                    if (!numVal) numVal = 0;
                    
                    var newOption = new Option(newName, newName, true, true);
                    $(newOption).attr('data-val', numVal);
                    select.append(newOption).trigger('change');
                } else {
                    select.val('');
                }
            } else {
                var val = selectedOption.data('val') || selectedOption.val().replace(/[^0-9.]/g, '');
                $('#packing_weight').val(val);
                calculateUnits();
            }
        });

        $('#delivery_mode').on('change', function() {
            var select = $(this);
            if (select.val() === 'ADD_NEW_MODE') {
                var newName = prompt("{{ __('Enter new delivery mode (e.g. Air):') }}");
                if (newName) {
                    var newOption = new Option(newName, newName, true, true);
                    select.append(newOption).trigger('change');
                } else {
                    select.val('');
                }
            }
        });

        function calculateDrumTotals() {
            var qty = parseFloat($('#drum_qty').val()) || 0;
            var buy = parseFloat($('#drum_buying_price').val()) || 0;
            var sell = parseFloat($('#drum_selling_price').val()) || 0;
            $('#drum_buying_total').val((qty * buy).toFixed(2));
            $('#drum_selling_total').val((qty * sell).toFixed(2));
        }

        $('#drum_qty, #drum_buying_price, #drum_selling_price').on('input', calculateDrumTotals);

        // Initial calculation
        calculateDrumTotals();
        calculateUnits();
    });
</script>
@endpush
