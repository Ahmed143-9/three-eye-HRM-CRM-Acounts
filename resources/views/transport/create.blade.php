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
        
        <!-- Step 1: Basic Entry -->
        <div id="step-1" class="card">
            <div class="card-header">
                <h5>{{__('Step 1: Basic Entry')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('client_id', __('Client Name'), ['class' => 'form-label']) }}
                        {{ Form::select('client_id', $clients, $salesOrder ? $salesOrder->customer_id : null, ['class' => 'form-control select2', 'id' => 'client_id']) }}
                    </div>
                    <div class="form-group col-md-6 d-none" id="manual_client_div">
                        {{ Form::label('manual_client_name', __('Manual Client Name'), ['class' => 'form-label']) }}
                        {{ Form::text('manual_client_name', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-12">
                        {{ Form::label('location_address', __('Location / Address'), ['class' => 'form-label']) }}
                        <div class="input-group">
                            {{ Form::text('location_address', null, ['class' => 'form-control', 'id' => 'location_address']) }}
                            <button type="button" class="btn btn-primary" id="btn-select-location">{{__('Select Location')}}</button>
                        </div>
                        {{ Form::hidden('location_lat', null, ['id' => 'location_lat']) }}
                        {{ Form::hidden('location_lng', null, ['id' => 'location_lng']) }}
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
                    <div class="form-group col-md-6">
                        {{ Form::label('driver_name', __('Driver Name'), ['class' => 'form-label']) }}
                        {{ Form::text('driver_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('contact_number', __('Contact Number'), ['class' => 'form-label']) }}
                        {{ Form::text('contact_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('truck_number', __('Truck Number'), ['class' => 'form-label']) }}
                        {{ Form::text('truck_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('starting_date', __('Starting Date'), ['class' => 'form-label']) }}
                        {{ Form::date('starting_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-12">
                        {{ Form::label('item_description', __('Item (Goods/Description)'), ['class' => 'form-label']) }}
                        @php
                            $desc = null;
                            if($salesOrder && $salesOrder->po && $salesOrder->po->items) {
                                $desc = $salesOrder->po->items->map(function($i){
                                    return $i->item_name . " (" . number_format($i->quantity, 2) . " " . $i->unit . ")";
                                })->implode(", ");
                            }
                        @endphp
                        {{ Form::textarea('item_description', $desc, ['class' => 'form-control', 'rows' => 3, 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('delivery_date', __('Delivery Date (Optional)'), ['class' => 'form-label']) }}
                        {{ Form::date('delivery_date', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('lc', __('LC (Letter of Credit)'), ['class' => 'form-label']) }}
                        {{ Form::text('lc', $salesOrder ? (optional($salesOrder->lc)->lc_no) : null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('ci', __('C.I (Commercial Invoice)'), ['class' => 'form-label']) }}
                        {{ Form::text('ci', $salesOrder ? (optional($salesOrder->ci)->ci_number) : null, ['class' => 'form-control']) }}
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
    });
</script>
@endpush
