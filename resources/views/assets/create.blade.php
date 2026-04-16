@extends('layouts.admin')
@section('page-title')
    {{__('Create Asset')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Create')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Add New Asset')}}</h5>
            </div>
            <div class="card-body">
                {{ Form::open(['route' => 'account-assets.store', 'method' => 'POST', 'files' => true]) }}
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('name', __('Asset Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', old('name'), ['class' => 'form-control', 'required', 'placeholder' => 'Enter asset name']) }}
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                            {{ Form::select('category', ['IT' => 'IT Equipment', 'Furniture' => 'Furniture', 'Electronics' => 'Electronics', 'Vehicles' => 'Vehicles', 'Machinery' => 'Machinery', 'Other' => 'Other'], old('category'), ['class' => 'form-control', 'required']) }}
                            @error('category')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('condition', __('Condition'), ['class' => 'form-label']) }}
                            {{ Form::select('condition', ['New' => 'New', 'Used' => 'Used', 'Damaged' => 'Damaged', 'Under Maintenance' => 'Under Maintenance'], old('condition', 'New'), ['class' => 'form-control', 'required']) }}
                            @error('condition')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                            {{ Form::select('status', ['Available' => 'Available', 'Assigned' => 'Assigned', 'Lost' => 'Lost', 'Maintenance' => 'Maintenance'], old('status', 'Available'), ['class' => 'form-control', 'required']) }}
                            @error('status')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('purchase_date', __('Purchase Date'), ['class' => 'form-label']) }}
                            {{ Form::date('purchase_date', old('purchase_date', date('Y-m-d')), ['class' => 'form-control', 'required']) }}
                            @error('purchase_date')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('amount', __('Purchase Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('amount', old('amount'), ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required', 'placeholder' => '0.00']) }}
                            @error('amount')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('supported_date', __('Warranty/Support Until'), ['class' => 'form-label']) }}
                            {{ Form::date('supported_date', old('supported_date'), ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="mb-3">{{__('Additional Details')}}</h6>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('manufacturer', __('Manufacturer'), ['class' => 'form-label']) }}
                            {{ Form::text('manufacturer', old('manufacturer'), ['class' => 'form-control', 'placeholder' => 'Enter manufacturer']) }}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('model_number', __('Model Number'), ['class' => 'form-label']) }}
                            {{ Form::text('model_number', old('model_number'), ['class' => 'form-control', 'placeholder' => 'Enter model number']) }}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            {{ Form::label('serial_number', __('Serial Number'), ['class' => 'form-label']) }}
                            {{ Form::text('serial_number', old('serial_number'), ['class' => 'form-control', 'placeholder' => 'Enter serial number']) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('location', __('Location'), ['class' => 'form-label']) }}
                            {{ Form::text('location', old('location'), ['class' => 'form-control', 'placeholder' => 'e.g., Office Floor 2, Room 201']) }}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('warranty_until', __('Warranty Until'), ['class' => 'form-label']) }}
                            {{ Form::date('warranty_until', old('warranty_until'), ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                    {{ Form::textarea('description', old('description'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Enter asset description']) }}
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('image', __('Asset Image'), ['class' => 'form-label']) }}
                    {{ Form::file('image', ['class' => 'form-control', 'accept' => 'image/*']) }}
                    <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF (Max 2MB)</small>
                    @error('image')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x"></i> {{__('Cancel')}}
                    </a>
                    {{ Form::submit(__('Create Asset'), ['class' => 'btn btn-primary']) }}
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
