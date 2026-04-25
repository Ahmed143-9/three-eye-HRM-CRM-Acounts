@extends('layouts.admin')
@section('page-title')
    {{__('Create Sales Order')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('sales-orders.index')}}">{{__('Sales Orders')}}</a></li>
    <li class="breadcrumb-item">{{__('Create')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['url' => 'sales-orders', 'method' => 'post']) }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('customer_id', __('Select Customer'), ['class' => 'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'required' => 'required', 'placeholder' => __('Select Customer')]) }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" onclick="location.href='{{ route('sales-orders.index') }}'">
                        <input type="submit" value="{{__('Create & Start Workflow')}}" class="btn btn-primary">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        // Re-initialize select2 if needed to ensure it opens downward
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
    });
</script>
<style>
    /* Force dropdown to go down */
    .select2-container--default .select2-selection--single {
        border-radius: 4px;
    }
    .card-body {
        min-height: 400px; /* Ensures space below for the dropdown */
    }
</style>
@endpush
