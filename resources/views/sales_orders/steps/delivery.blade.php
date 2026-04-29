<h5>{{ __('Step 8: Delivery') }}</h5>
<hr>

{{ Form::open(['route' => ['sales-orders.delivery.store', $order->id], 'method' => 'post', 'id' => 'delivery-form']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('delivery_mode', __('Delivery Mode'), ['class' => 'form-label']) }}
            <select name="delivery_mode" id="delivery_mode" class="form-control select2" required>
                <option value="">{{ __('Select Delivery Mode') }}</option>
                <option value="Road" {{ (isset($order->delivery) && $order->delivery->delivery_mode == 'Road') ? 'selected' : '' }}>Road</option>
                <option value="Rail" {{ (isset($order->delivery) && $order->delivery->delivery_mode == 'Rail') ? 'selected' : '' }}>Rail</option>
                <option value="Sea" {{ (isset($order->delivery) && $order->delivery->delivery_mode == 'Sea') ? 'selected' : '' }}>Sea</option>
                <option value="ADD_NEW_MODE" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
            </select>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('packing_type', __('Packing Type'), ['class' => 'form-label']) }}
            <select name="packing_type" id="packing_type" class="form-control select2" required>
                <option value="">{{ __('Select Packing Type') }}</option>
                <option value="200 kg drum" data-val="200" {{ (isset($order->delivery) && $order->delivery->packing_type == '200 kg drum') ? 'selected' : '' }}>200 kg drum</option>
                <option value="1000 kg IBC" data-val="1000" {{ (isset($order->delivery) && $order->delivery->packing_type == '1000 kg IBC') ? 'selected' : '' }}>1000 kg IBC</option>
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

        // Initial calculation
        calculateUnits();
    });
</script>
@endpush
