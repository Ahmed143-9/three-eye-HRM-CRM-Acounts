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
                                {{ Form::label('customer_id', __('Select Company'), ['class' => 'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['id' => 'customer_id', 'class' => 'form-control select2-customer', 'required' => 'required']) }}
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

@push('css-page')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2-customer to avoid conflict with global Choices.js (which targets .select2)
        $('.select2-customer').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
    });
</script>
<style>
    /* Ensure parent container has position relative */
    .form-group {
        position: relative !important;
        margin-bottom: 20px;
    }

    /* Force dropdown to always open downwards */
    .select2-container--default .select2-dropdown {
        top: 100% !important;
        left: 0 !important;
        position: absolute !important;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: none;
    }

    .select2-container--open .select2-dropdown {
        display: block !important;
    }

    /* Override Select2 upward positioning */
    .select2-container--above .select2-dropdown {
        top: 100% !important;
        border-top: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }

    /* Prevent parent containers from clipping the dropdown */
    .card-body {
        min-height: 500px; 
    }

    .card {
        overflow: visible !important;
    }

    /* Select2 selection styling for better alignment */
    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex;
        align-items: center;
        border: 1px solid #ced4da;
    }
</style>
@endpush
