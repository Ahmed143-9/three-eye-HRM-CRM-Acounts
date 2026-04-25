@extends('layouts.admin')
@section('page-title')
    {{__('Edit Transport')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('transports.index')}}">{{__('Transport Management')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        {{ Form::model($transport, ['url' => route('transports.update', $transport->id), 'method' => 'PUT', 'id' => 'transport-edit-form']) }}

        <div class="card">
            <div class="card-header">
                <h5>{{__('Edit Transport')}} — <span class="text-muted">{{ $transport->unique_id }}</span></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Client --}}
                    <div class="form-group col-md-6">
                        {{ Form::label('client_id', __('Client Name'), ['class' => 'form-label']) }}
                        {{ Form::select('client_id', $clients, $transport->client_id > 0 ? $transport->client_id : 'others', ['class' => 'form-control select2', 'id' => 'client_id']) }}
                    </div>
                    <div class="form-group col-md-6 {{ $transport->client_id == 0 && $transport->manual_client_name ? '' : 'd-none' }}" id="manual_client_div">
                        {{ Form::label('manual_client_name', __('Manual Client Name'), ['class' => 'form-label']) }}
                        {{ Form::text('manual_client_name', $transport->manual_client_name, ['class' => 'form-control']) }}
                    </div>

                    {{-- Location --}}
                    <div class="form-group col-md-12">
                        {{ Form::label('location_address', __('Location / Address'), ['class' => 'form-label']) }}
                        <div class="input-group">
                            {{ Form::text('location_address', $transport->location_address, ['class' => 'form-control', 'id' => 'location_address']) }}
                            <button type="button" class="btn btn-primary" id="btn-select-location">{{__('Select on Map')}}</button>
                        </div>
                        {{ Form::hidden('location_lat', $transport->location_lat, ['id' => 'location_lat']) }}
                        {{ Form::hidden('location_lng', $transport->location_lng, ['id' => 'location_lng']) }}
                    </div>

                    {{-- Driver --}}
                    <div class="form-group col-md-6">
                        {{ Form::label('driver_name', __('Driver Name'), ['class' => 'form-label']) }}
                        {{ Form::text('driver_name', $transport->driver_name, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('contact_number', __('Contact Number'), ['class' => 'form-label']) }}
                        {{ Form::text('contact_number', $transport->contact_number, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('truck_number', __('Truck Number'), ['class' => 'form-label']) }}
                        {{ Form::text('truck_number', $transport->truck_number, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('starting_date', __('Starting Date'), ['class' => 'form-label']) }}
                        {{ Form::date('starting_date', $transport->starting_date, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-12">
                        {{ Form::label('item_description', __('Item / Goods Description'), ['class' => 'form-label']) }}
                        {{ Form::textarea('item_description', $transport->item_description, ['class' => 'form-control', 'rows' => 3, 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('delivery_date', __('Delivery Date (Optional)'), ['class' => 'form-label']) }}
                        {{ Form::date('delivery_date', $transport->delivery_date, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('lc', __('LC (Letter of Credit)'), ['class' => 'form-label']) }}
                        {{ Form::text('lc', $transport->lc, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('ci', __('C.I (Commercial Invoice)'), ['class' => 'form-label']) }}
                        {{ Form::text('ci', $transport->ci, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('transports.index') }}" class="btn btn-secondary me-2">{{__('Cancel')}}</a>
                {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>

{{-- Map Modal --}}
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('Select Location')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="map" style="height: 400px; width: 100%;"></div>
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
$(document).ready(function() {
    $('#client_id').on('change', function() {
        if ($(this).val() == 'others') {
            $('#manual_client_div').removeClass('d-none');
        } else {
            $('#manual_client_div').addClass('d-none');
        }
    });

    var map, marker;
    $('#btn-select-location').on('click', function() {
        $('#mapModal').modal('show');
        setTimeout(function() {
            if (!map) {
                var lat = $('#location_lat').val() || 23.8103;
                var lng = $('#location_lng').val() || 90.4125;
                map = L.map('map').setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                if ($('#location_lat').val()) {
                    marker = L.marker([lat, lng]).addTo(map);
                }
                map.on('click', function(e) {
                    if (marker) marker.setLatLng(e.latlng);
                    else marker = L.marker(e.latlng).addTo(map);
                    $('#location_lat').val(e.latlng.lat);
                    $('#location_lng').val(e.latlng.lng);
                });
            }
            map.invalidateSize();
        }, 400);
    });

    $('#btn-confirm-location').on('click', function() {
        var lat = $('#location_lat').val();
        var lng = $('#location_lng').val();
        if (lat && lng) {
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+lat+'&lon='+lng)
                .then(r => r.json()).then(data => {
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
