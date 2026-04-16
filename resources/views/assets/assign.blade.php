@extends('layouts.admin')
@section('page-title')
    {{__('Assign Asset')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Assign')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">{{__('Assign Asset to Employee')}}</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>{{__('Asset:')}}</strong> {{ $asset->name }} ({{ $asset->asset_code }})<br>
                    <strong>{{__('Category:')}}</strong> {{ $asset->category }}<br>
                    <strong>{{__('Condition:')}}</strong> {{ $asset->condition }}
                </div>

                {{ Form::open(['route' => ['account-assets.assign', $asset->id], 'method' => 'POST', 'files' => true]) }}
                
                <div class="form-group mb-3">
                    {{ Form::label('employee_id', __('Select Employee'), ['class' => 'form-label']) }}
                    {{ Form::select('employee_id', $employees, old('employee_id'), ['class' => 'form-control', 'required', 'placeholder' => '-- Select Employee --']) }}
                    @error('employee_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('assign_date', __('Assignment Date'), ['class' => 'form-label']) }}
                    {{ Form::date('assign_date', old('assign_date', date('Y-m-d')), ['class' => 'form-control', 'required']) }}
                    @error('assign_date')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label']) }}
                    {{ Form::textarea('remarks', old('remarks'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Any notes about this assignment']) }}
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('document', __('Assignment Document'), ['class' => 'form-label']) }}
                    {{ Form::file('document', ['class' => 'form-control', 'accept' => '.pdf,.doc,.docx,.jpeg,.png,.jpg']) }}
                    <small class="text-muted">Upload assignment letter or agreement (Optional)</small>
                    @error('document')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x"></i> {{__('Cancel')}}
                    </a>
                    {{ Form::submit(__('Assign Asset'), ['class' => 'btn btn-success']) }}
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
