@extends('layouts.admin')
@section('page-title')
    {{__('Return Asset')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Return')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">{{__('Return Asset from Employee')}}</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>{{__('Asset:')}}</strong> {{ $assignment->asset->name }} ({{ $assignment->asset->asset_code }})<br>
                    <strong>{{__('Assigned To:')}}</strong> {{ $assignment->employee->name }}<br>
                    <strong>{{__('Assign Date:')}}</strong> {{ \Auth::user()->dateFormat($assignment->assign_date) }}
                </div>

                {{ Form::open(['route' => ['account-assets.return', $assignment->asset_id], 'method' => 'POST']) }}
                
                <div class="form-group mb-3">
                    {{ Form::label('return_date', __('Return Date'), ['class' => 'form-label']) }}
                    {{ Form::date('return_date', old('return_date', date('Y-m-d')), ['class' => 'form-control', 'required']) }}
                    @error('return_date')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('status', __('Asset Condition on Return'), ['class' => 'form-label']) }}
                    {{ Form::select('status', ['Returned' => 'Returned (Good Condition)', 'Damaged' => 'Damaged', 'Lost' => 'Lost'], old('status'), ['class' => 'form-control', 'required']) }}
                    @error('status')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label']) }}
                    {{ Form::textarea('remarks', old('remarks'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Describe the condition of the asset']) }}
                    <small class="text-muted">Note any damage, missing accessories, or other observations</small>
                    @error('remarks')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="alert alert-info mt-3">
                    <strong>{{__('Note:')}}</strong><br>
                    • If <strong>Returned</strong> - Asset will be marked as Available<br>
                    • If <strong>Damaged</strong> - Asset will be sent to Maintenance<br>
                    • If <strong>Lost</strong> - Asset will be marked as Lost
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x"></i> {{__('Cancel')}}
                    </a>
                    {{ Form::submit(__('Process Return'), ['class' => 'btn btn-warning']) }}
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
