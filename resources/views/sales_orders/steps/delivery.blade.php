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
            <div class="col-md-3">
                <label class="form-label small text-muted">{{ __('Inventory Item') }}</label>
                <select name="inventory_item_id" id="inventory_item_id" class="form-control form-control-sm select2">
                    <option value="">{{ __('Select Item (Optional)') }}</option>
                    @foreach($inventoryItems as $invItem)
                        <option value="{{ $invItem->id }}" data-unit="{{ $invItem->unit }}" {{ (isset($order->delivery) && $order->delivery->inventory_item_id == $invItem->id) ? 'selected' : '' }}>
                            {{ $invItem->name }} ({{ $invItem->unit }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Qty') }}</label>
                <input type="number" name="drum_qty" id="drum_qty" class="form-control form-control-sm" value="{{ isset($order->delivery) ? $order->delivery->drum_qty : '' }}" placeholder="e.g. 50">
                <span id="available_stock_display" class="text-info small d-block mt-1" style="font-size: 0.75rem;"></span>
            </div>
            <input type="hidden" name="drum_unit" id="drum_unit" value="{{ isset($order->delivery) ? $order->delivery->drum_unit : 'Drums' }}">
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Buying Price') }}</label>
                <input type="number" step="0.01" name="drum_buying_price" id="drum_buying_price" class="form-control form-control-sm" value="{{ isset($order->delivery) ? $order->delivery->drum_buying_price : '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">{{ __('Total Buying') }}</label>
                <input type="number" step="0.01" name="drum_buying_total" id="drum_buying_total" class="form-control form-control-sm bg-white" readonly value="{{ isset($order->delivery) ? $order->delivery->drum_buying_total : '' }}">
            </div>
            <div class="col-md-1">
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

        var isInitialLoad = true;

        $('#inventory_item_id').on('change', function() {
            var itemId = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var unit = selectedOption.data('unit') || 'pcs';
            $('#drum_unit').val(unit);

            if (itemId) {
                $.ajax({
                    url: '{{ url("inventory/item") }}/' + itemId + '/average-cost',
                    type: 'GET',
                    success: function(response) {
                        $('#available_stock_display').html('{{ __("Available:") }} ' + response.available_qty + ' ' + response.unit);
                        $('#available_stock_display').data('available', response.available_qty);
                        
                        if (!isInitialLoad || !$('#drum_buying_price').val()) {
                            $('#drum_buying_price').val(response.average_cost).trigger('input');
                        }
                        isInitialLoad = false;
                    },
                    error: function() {
                        $('#available_stock_display').html('<span class="text-danger">{{ __("Error loading stock") }}</span>');
                        isInitialLoad = false;
                    }
                });
            } else {
                $('#available_stock_display').html('');
                $('#available_stock_display').data('available', 0);
                isInitialLoad = false;
            }
        });

        if ($('#inventory_item_id').val()) {
            $('#inventory_item_id').trigger('change');
        } else {
            isInitialLoad = false;
        }

        $('#delivery-form').on('submit', function(e) {
            var itemId = $('#inventory_item_id').val();
            if (itemId) {
                var qty = parseFloat($('#drum_qty').val()) || 0;
                var available = parseFloat($('#available_stock_display').data('available')) || 0;
                
                @if(isset($order->delivery))
                    var previousQty = parseFloat('{{ $order->delivery->drum_qty }}') || 0;
                    var allowedMax = available + previousQty;
                @else
                    var allowedMax = available;
                @endif

                if (qty > allowedMax) {
                    e.preventDefault();
                    alert('{{ __("Error: Entered quantity exceeds available inventory stock!") }} (Max allowed: ' + allowedMax + ')');
                    return false;
                }
            }
        });

        // Initial calculation
        calculateDrumTotals();
        calculateUnits();
    });
</script>
@endpush
