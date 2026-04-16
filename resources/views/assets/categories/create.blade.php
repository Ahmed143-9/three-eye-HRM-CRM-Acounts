@extends('layouts.admin')
@section('page-title')
    {{__('Create Asset Category')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('asset-categories.index')}}">{{__('Categories')}}</a></li>
    <li class="breadcrumb-item">{{__('Create')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Add New Asset Category')}}</h5>
            </div>
            <div class="card-body">
                {{ Form::open(['route' => 'asset-categories.store', 'method' => 'POST']) }}
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', old('name'), ['class' => 'form-control', 'required', 'placeholder' => 'e.g., IT Equipment']) }}
                            <small class="text-muted">Enter a descriptive name for the category</small>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('code', __('Category Code'), ['class' => 'form-label']) }}
                            {{ Form::text('code', old('code'), ['class' => 'form-control', 'required', 'placeholder' => 'e.g., IT, FURN, ELEC', 'maxlength' => '10']) }}
                            <small class="text-muted">Short code (will be converted to uppercase)</small>
                            @error('code')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                    {{ Form::textarea('description', old('description'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Describe what types of assets belong in this category']) }}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('icon', __('Icon Class'), ['class' => 'form-label']) }}
                            {{ Form::text('icon', old('icon', 'ti ti-package'), ['class' => 'form-control', 'placeholder' => 'ti ti-package']) }}
                            <small class="text-muted">Tabler icon class (e.g., ti ti-device-laptop)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            {{ Form::label('color', __('Badge Color'), ['class' => 'form-label']) }}
                            {{ Form::select('color', [
                                'primary' => 'Blue',
                                'secondary' => 'Gray',
                                'success' => 'Green',
                                'danger' => 'Red',
                                'warning' => 'Yellow',
                                'info' => 'Cyan',
                                'dark' => 'Dark'
                            ], old('color', 'secondary'), ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        {{ Form::checkbox('is_active', true, true, ['class' => 'form-check-input', 'id' => 'is_active']) }}
                        {{ Form::label('is_active', __('Active'), ['class' => 'form-check-label']) }}
                    </div>
                    <small class="text-muted">Inactive categories won't appear in asset creation dropdowns</small>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('asset-categories.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x"></i> {{__('Cancel')}}
                    </a>
                    {{ Form::submit(__('Create Category'), ['class' => 'btn btn-primary']) }}
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
