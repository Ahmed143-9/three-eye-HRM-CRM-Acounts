{{ Form::open(['route' => ['sales-orders.delivery.store', $order->id], 'method' => 'post', 'id' => 'delivery-form']) }}
@if($active_ci)
    <input type="hidden" name="ci_id" value="{{ $active_ci->id }}">
@endif

<h6 class="fw-bold text-dark mt-4 mb-3">{{ __('Shipment Delivery Information') }}</h6>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('delivery_mode', __('Delivery Mode'), ['class' => 'form-label']) }}
            <select name="delivery_mode" id="delivery_mode" class="form-control" required>
                <option value="">{{ __('Select Delivery Mode') }}</option>
                <option value="Road" {{ (isset($active_ci->delivery) && $active_ci->delivery->delivery_mode == 'Road') ? 'selected' : '' }}>Road</option>
                <option value="Rail" {{ (isset($active_ci->delivery) && $active_ci->delivery->delivery_mode == 'Rail') ? 'selected' : '' }}>Rail</option>
                <option value="Sea" {{ (isset($active_ci->delivery) && $active_ci->delivery->delivery_mode == 'Sea') ? 'selected' : '' }}>Sea</option>
                <option value="ADD_NEW_MODE" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
            </select>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('packing_type', __('Packing Type'), ['class' => 'form-label']) }}
            <select name="packing_type" id="packing_type" class="form-control" required>
                <option value="">{{ __('Select Packing Type') }}</option>
                <option value="200 kg drum" data-val="200" {{ (optional(optional($active_ci)->delivery)->packing_type == '200 kg drum') ? 'selected' : '' }}>200 kg drum</option>
                <option value="1000 kg IBC" data-val="1000" {{ (optional(optional($active_ci)->delivery)->packing_type == '1000 kg IBC') ? 'selected' : '' }}>1000 kg IBC</option>
                <option value="ADD_NEW_PACKING" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
            </select>
            <input type="hidden" id="packing_weight" value="{{ (optional(optional($active_ci)->delivery)->packing_type) ? filter_var($active_ci->delivery->packing_type, FILTER_SANITIZE_NUMBER_INT) : '0' }}">
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card bg-light text-center p-3">
            <h6 class="text-muted small">{{ __('Shipment Qty (MT)') }}</h6>
            @php
                $totalMt = 0;
                if ($active_ci && $active_ci->tankers) {
                    $totalMt = $active_ci->tankers->sum('quantity_mt');
                }
            @endphp
            <h4 id="total_mt_display">{{ number_format($totalMt, 3) }}</h4>
            <input type="hidden" name="total_quantity_mt" id="total_quantity_mt" value="{{ $totalMt }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light text-center p-3">
            <h6 class="text-muted small">{{ __('Shipment Qty (KG)') }}</h6>
            <h4 id="total_kg_display">{{ number_format($totalMt * 1000, 3) }}</h4>
            <input type="hidden" name="total_quantity_kg" id="total_quantity_kg" value="{{ $totalMt * 1000 }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light text-center p-3">
            <h6 class="text-muted small">{{ __('Required Units') }}</h6>
            <h4 id="required_units_display">{{ (optional(optional($active_ci)->delivery)->required_units) ? number_format($active_ci->delivery->required_units, 2) : '0.00' }}</h4>
            <input type="hidden" name="required_units" id="required_units" value="{{ (optional(optional($active_ci)->delivery)->required_units) ? $active_ci->delivery->required_units : 0 }}">
        </div>
    </div>
</div>

{{-- Drum Billing Section --}}
<div class="card mt-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">{{ __('Drum / Packing Billing Details') }}</h6>
    </div>
    <div class="card-body bg-light">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('drum_qty', __('Billing Qty (Units)'), ['class' => 'form-label']) }}
                    {{ Form::number('drum_qty', optional(optional($active_ci)->delivery)->drum_qty ?? 0, ['class' => 'form-control drum-calc', 'id' => 'drum_qty', 'step' => '0.01']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('drum_unit', __('Unit'), ['class' => 'form-label']) }}
                    {{ Form::text('drum_unit', optional(optional($active_ci)->delivery)->drum_unit ?? 'Pcs', ['class' => 'form-control', 'placeholder' => 'e.g. Pcs']) }}
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 border-end">
                <h6 class="fw-bold text-primary mb-3">{{ __('Buying (Payable)') }}</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('drum_buying_price', __('Buying Rate'), ['class' => 'form-label']) }}
                            {{ Form::number('drum_buying_price', optional(optional($active_ci)->delivery)->drum_buying_price ?? 0, ['class' => 'form-control drum-calc', 'id' => 'drum_buying_price', 'step' => '0.01']) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('drum_buying_total', __('Total Buying'), ['class' => 'form-label']) }}
                            {{ Form::number('drum_buying_total', optional(optional($active_ci)->delivery)->drum_buying_total ?? 0, ['class' => 'form-control', 'id' => 'drum_buying_total', 'readonly']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-success mb-3">{{ __('Selling (Receivable)') }}</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('drum_selling_price', __('Selling Rate'), ['class' => 'form-label']) }}
                            {{ Form::number('drum_selling_price', optional(optional($active_ci)->delivery)->drum_selling_price ?? 0, ['class' => 'form-control drum-calc', 'id' => 'drum_selling_price', 'step' => '0.01']) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('drum_selling_total', __('Total Selling'), ['class' => 'form-label']) }}
                            {{ Form::number('drum_selling_total', optional(optional($active_ci)->delivery)->drum_selling_total ?? 0, ['class' => 'form-control', 'id' => 'drum_selling_total', 'readonly']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-end mt-4">
    @if(!optional($active_ci)->delivery)
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">{{ __('Create Delivery Order & Send to Transport') }}</button>
    @else
        <div class="alert alert-success d-inline-block text-start mb-0 me-3 py-2">
            <i class="ti ti-check"></i> {{ __('Delivery Order Created.') }}
        </div>
        <a href="{{ route('transports.create') }}?sales_order_id={{ $order->id }}&ci_id={{ $active_ci->id }}&client_id={{ $order->customer_id }}" class="btn btn-success btn-lg px-4 shadow">{{ __('Dispatch via Transport') }}</a>
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
            
            // Default Drum Qty to Required Units if it's 0
            if (parseFloat($('#drum_qty').val()) === 0) {
                $('#drum_qty').val(Math.ceil(requiredUnits)).trigger('change');
            }
        }

        $(document).on('keyup change', '.drum-calc', function() {
            var qty = parseFloat($('#drum_qty').val()) || 0;
            var bRate = parseFloat($('#drum_buying_price').val()) || 0;
            var sRate = parseFloat($('#drum_selling_price').val()) || 0;

            $('#drum_buying_total').val((qty * bRate).toFixed(2));
            $('#drum_selling_total').val((qty * sRate).toFixed(2));
        });

        $('#packing_type').on('change', function() {
            var select = $(this);
            var selectedOption = select.find('option:selected');
            
            if (select.val() === 'ADD_NEW_PACKING') {
                var newName = prompt("{{ __('Enter new packing type (e.g. 50 kg bag):') }}");
                if (newName) {
                    var numVal = newName.replace(/[^0-9.]/g, ''); 
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

        calculateUnits();
    });
</script>
@endpush
