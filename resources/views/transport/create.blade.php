@extends('layouts.admin')
@section('page-title')
    {{__('Create Transport')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('transports.index')}}">{{__('Transport Management')}}</a></li>
    <li class="breadcrumb-item">{{__('Create')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        {{ Form::open(['url' => 'transports', 'method' => 'post', 'id' => 'transport-form']) }}
        {{ Form::hidden('sales_order_id', $salesOrder ? $salesOrder->id : null) }}
        {{ Form::hidden('ci_id', $activeCi ? $activeCi->id : null) }}
        
        <!-- Step 1: Basic Entry -->
        <div id="step-1" class="card">
            <div class="card-header">
                <h5>{{__('Step 1: Basic Entry')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($salesOrder)
                        <div class="col-md-12"><h6 class="mb-3 text-primary">{{ __('Sales Order & Client Information') }}</h6></div>
                        <div class="form-group col-md-3">
                            {{ Form::label('order_number', __('Order ID'), ['class' => 'form-label']) }}
                            {{ Form::text('order_number', $salesOrder->order_number ?? $salesOrder->id, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('client_name', __('Client Name'), ['class' => 'form-label']) }}
                            {{ Form::text('client_name', $salesOrder->customer->name ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
                            <input type="hidden" name="client_id" value="{{ $salesOrder->customer_id }}">
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('client_email', __('Client Email'), ['class' => 'form-label']) }}
                            {{ Form::text('client_email', $salesOrder->customer->contact_person_email ?? $salesOrder->customer->email ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('client_phone', __('Client Phone'), ['class' => 'form-label']) }}
                            {{ Form::text('client_phone', $salesOrder->customer->contact_person_number ?? $salesOrder->customer->contact ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('client_address', __('Client Address'), ['class' => 'form-label']) }}
                            {{ Form::text('client_address', $salesOrder->customer->billing_address ?? ($salesOrder->customer->shipping_address ?? ''), ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                    @else
                        <div class="form-group col-md-6">
                            {{ Form::label('client_id', __('Client Name'), ['class' => 'form-label']) }}
                            {{ Form::select('client_id', $clients, null, ['class' => 'form-control select2', 'id' => 'client_id']) }}
                        </div>
                        <div class="form-group col-md-6 d-none" id="manual_client_div">
                            {{ Form::label('manual_client_name', __('Manual Client Name'), ['class' => 'form-label']) }}
                            {{ Form::text('manual_client_name', null, ['class' => 'form-control']) }}
                        </div>
                    @endif
                    @if(!$salesOrder)
                        <div class="form-group col-md-12">
                            {{ Form::label('location_address', __('Location / Address'), ['class' => 'form-label']) }}
                            <div class="input-group">
                                {{ Form::text('location_address', null, ['class' => 'form-control', 'id' => 'location_address']) }}
                                <button type="button" class="btn btn-primary" id="btn-select-location">{{__('Select Location')}}</button>
                            </div>
                            {{ Form::hidden('location_lat', null, ['id' => 'location_lat']) }}
                            {{ Form::hidden('location_lng', null, ['id' => 'location_lng']) }}
                        </div>
                    @endif
                    @if($salesOrder)
                        <hr class="mt-3">
                        <div class="col-md-12"><h6 class="mb-3 text-primary">{{ __('Order Information') }}</h6></div>
                        <div class="form-group col-md-3">
                            {{ Form::label('po', __('PO (Purchase Order)'), ['class' => 'form-label']) }}
                            {{ Form::text('po', optional($salesOrder->po)->order_number, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('pi', __('PI (Proforma Invoice)'), ['class' => 'form-label']) }}
                            {{ Form::text('pi', optional($salesOrder->pi)->pi_number, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('lc', __('LC (Letter of Credit)'), ['class' => 'form-label']) }}
                            {{ Form::text('lc', optional($salesOrder->lc)->lc_no, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('ci', __('C.I (Commercial Invoice)'), ['class' => 'form-label']) }}
                            {{ Form::text('ci', $activeCi ? $activeCi->ci_number : (optional($salesOrder->ci)->ci_number), ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                    @else
                        <div class="form-group col-md-6">
                            {{ Form::label('lc', __('LC (Letter of Credit)'), ['class' => 'form-label']) }}
                            {{ Form::text('lc', null, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ci', __('C.I (Commercial Invoice)'), ['class' => 'form-label']) }}
                            {{ Form::text('ci', null, ['class' => 'form-control']) }}
                        </div>
                    @endif
                    
                    <hr class="mt-3">
                    <div class="col-md-12"><h6 class="mb-3 text-primary">{{ __('Transport Requirements') }}</h6></div>
                    
                    @php
                        $delivery = $activeCi ? $activeCi->delivery : $salesOrder->delivery;
                    @endphp
                    @if($salesOrder && $delivery)
                        <div class="form-group col-md-12">
                            <div class="alert alert-info py-2 mb-3">
                                <strong><i class="ti ti-info-circle"></i> {{ __('Operation Cargo Details (Specific Shipment):') }}</strong> 
                                <span class="ms-3">{{ __('Total Quantity:') }} <strong>{{ number_format($delivery->total_quantity_kg, 2) }} kg</strong></span>
                                <span class="ms-3">{{ __('Packing Type:') }} <strong>{{ $delivery->packing_type }}</strong></span>
                                <span class="ms-3">{{ __('Required Units (e.g. Drums):') }} <strong>{{ number_format($delivery->required_units, 0) }}</strong></span>
                            </div>
                        </div>
                    @endif

                    <div class="form-group col-md-6">
                        {{ Form::label('transport_type', __('Transport Type / Truck'), ['class' => 'form-label']) }}
                        <select name="transport_type" id="transport_type" class="form-control">
                            <option value="">{{__('Select Transport Type')}}</option>
                            <option value="Truck (75 Drums)" data-cap="75">Truck (75 Drums)</option>
                            <option value="Truck (80 Drums)" data-cap="80">Truck (80 Drums)</option>
                            <option value="Truck (100 Drums)" data-cap="100">Truck (100 Drums)</option>
                            <option value="ADD_NEW_TRANSPORT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                        <input type="hidden" id="truck_capacity" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('required_trucks', __('Required Trucks/Units'), ['class' => 'form-label']) }}
                        {{ Form::number('required_trucks', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'required_trucks', 'readonly' => 'readonly']) }}
                        @php
                            $delivery = $activeCi ? $activeCi->delivery : $salesOrder->delivery;
                            $required_units = $delivery ? $delivery->required_units : 0;
                        @endphp
                        <input type="hidden" id="total_required_units" value="{{ $required_units }}">
                        @if($required_units > 0)
                            <small class="text-success">{{ __('Calculating based on ') }} <strong>{{ $required_units }}</strong> {{ __(' units from Delivery Order.') }}</small>
                        @else
                            <small class="text-danger">{{ __('Warning: 0 units found in Delivery Order. Math will result in 0.') }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">{{__('Next Step')}}</button>
            </div>
        </div>

        <!-- Step 2: Transport Details -->
        <div id="step-2" class="card d-none">
            <div class="card-header">
                <h5>{{__('Step 2: Transport Details')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('item_description', __('Item (Goods/Description)'), ['class' => 'form-label fw-bold']) }}
                        @php
                            $desc = null;
                            if($salesOrder && $salesOrder->po && $salesOrder->po->items) {
                                $desc = $salesOrder->po->items->map(function($i){
                                    return $i->item_name . " (" . number_format($i->quantity, 2) . " " . $i->unit . ")";
                                })->implode(", ");
                            }
                        @endphp
                        {{ Form::textarea('item_description', $desc, ['class' => 'form-control', 'rows' => 2, 'required' => 'required']) }}
                    </div>

                    <div class="form-group col-md-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">{{ __('Drivers & Truck Assignments') }}</label>
                            <button type="button" class="btn btn-sm btn-primary add-driver-btn"><i class="ti ti-plus"></i> {{ __('Add Another Truck/Driver') }}</button>
                        </div>
                        <div id="drivers-container">
                            <div class="card bg-light driver-row mb-3">
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            {{ Form::label('drivers[0][name]', __('Driver Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('drivers[0][name]', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            {{ Form::label('drivers[0][contact]', __('Contact Number'), ['class' => 'form-label']) }}
                                            {{ Form::text('drivers[0][contact]', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            {{ Form::label('drivers[0][truck_number]', __('Truck Number'), ['class' => 'form-label']) }}
                                            {{ Form::text('drivers[0][truck_number]', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            {{ Form::label('drivers[0][starting_date]', __('Starting Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('drivers[0][starting_date]', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            {{ Form::label('drivers[0][delivery_date]', __('Delivery Date (Optional)'), ['class' => 'form-label']) }}
                                            {{ Form::date('drivers[0][delivery_date]', null, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-secondary" onclick="nextStep(1)">{{__('Previous')}}</button>
                {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel">{{__('Select Location')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="map" style="height: 400px; width: 100%;"></div>
        <p class="mt-2 text-muted">{{__('Click on the map to select a location.')}}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="button" class="btn btn-primary" id="btn-confirm-location">{{__('Confirm Location')}}</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('script-page')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function nextStep(step) {
        if (step == 2) {
            document.getElementById('step-1').classList.add('d-none');
            document.getElementById('step-2').classList.remove('d-none');
        } else {
            document.getElementById('step-2').classList.add('d-none');
            document.getElementById('step-1').classList.remove('d-none');
        }
    }

    $(document).ready(function() {
        $('#client_id').on('change', function() {
            if ($(this).val() == 'others') {
                $('#manual_client_div').removeClass('d-none');
            } else {
                $('#manual_client_div').addClass('d-none');
            }
        });

        // Leaflet Map Logic
        var map;
        var marker;
        
        $('#btn-select-location').on('click', function() {
            $('#mapModal').modal('show');
            
            setTimeout(function() {
                if (!map) {
                    map = L.map('map').setView([23.8103, 90.4125], 13); // Default to Dhaka
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    map.on('click', function(e) {
                        if (marker) {
                            marker.setLatLng(e.latlng);
                        } else {
                            marker = L.marker(e.latlng).addTo(map);
                        }
                        $('#location_lat').val(e.latlng.lat);
                        $('#location_lng').val(e.latlng.lng);
                    });
                }
                map.invalidateSize();
            }, 500);
        });

        $('#btn-confirm-location').on('click', function() {
            var lat = $('#location_lat').val();
            var lng = $('#location_lng').val();
            if (lat && lng) {
                // Reverse geocoding (simplified placeholder or fetch)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        $('#location_address').val(data.display_name);
                        $('#mapModal').modal('hide');
                    });
            } else {
                $('#mapModal').modal('hide');
            }
        });

        // Transport Type & Required Trucks Logic
        $(document).on('change', '#transport_type', function() {
            var select = $(this);
            var selectedOption = select.find('option:selected');
            
            if (select.val() === 'ADD_NEW_TRANSPORT') {
                var newName = prompt("{{ __('Enter new transport type (e.g. Truck (120 Drums)):') }}");
                if (newName) {
                    var numVal = newName.replace(/[^0-9.]/g, ''); 
                    if (!numVal) numVal = 0;
                    
                    var newOption = new Option(newName, newName, true, true);
                    $(newOption).attr('data-cap', numVal);
                    select.append(newOption).trigger('change');
                } else {
                    select.val('');
                }
            } else {
                // Use .attr('data-cap') instead of .data('cap') for dynamically added options
                var val = selectedOption.attr('data-cap');
                if (val === undefined || val === '') {
                    var textVal = selectedOption.val() || '';
                    val = textVal.replace(/[^0-9.]/g, '');
                }
                $('#truck_capacity').val(val || 0);
                calculateTrucks();
            }
        });

        function calculateTrucks() {
            var totalUnits = parseFloat($('#total_required_units').val()) || 0;
            var truckCapacity = parseFloat($('#truck_capacity').val()) || 0;
            var requiredTrucks = 0;
            
            if (truckCapacity > 0) {
                // Round up because you can't have a partial truck
                requiredTrucks = Math.ceil(totalUnits / truckCapacity);
            }
            
            $('#required_trucks').val(requiredTrucks);
            
            if (totalUnits <= 0) {
                console.warn("Total required units is 0. Check if the delivery order was saved correctly.");
            }
        }

        // Initial check
        if ($('#transport_type').val()) {
            $('#transport_type').trigger('change');
        }

        // Multiple Drivers Logic
        var driverCount = 1;
        $(document).on('click', '.add-driver-btn', function() {
            var newRow = `
            <div class="card bg-light driver-row mb-3">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">{{ __('Driver Name') }}</label>
                            <input class="form-control" required="required" name="drivers[${driverCount}][name]" type="text">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">{{ __('Contact Number') }}</label>
                            <input class="form-control" required="required" name="drivers[${driverCount}][contact]" type="text">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">{{ __('Truck Number') }}</label>
                            <input class="form-control" required="required" name="drivers[${driverCount}][truck_number]" type="text">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">{{ __('Starting Date') }}</label>
                            <input class="form-control" required="required" name="drivers[${driverCount}][starting_date]" type="date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">{{ __('Delivery Date (Optional)') }}</label>
                            <input class="form-control" name="drivers[${driverCount}][delivery_date]" type="date">
                        </div>
                        <div class="col-md-4 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-driver-btn w-100"><i class="ti ti-trash"></i> {{ __('Remove Truck') }}</button>
                        </div>
                    </div>
                </div>
            </div>`;
            $('#drivers-container').append(newRow);
            driverCount++;
        });

        $(document).on('click', '.remove-driver-btn', function() {
            $(this).closest('.driver-row').remove();
        });
    });
</script>
@endpush
