@extends('layouts.admin')
@section('page-title')
    {{ __('Generate Salary Sheet') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('salary-management.index') }}">{{ __('Salary Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Generate') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-settings me-2"></i>{{ __('Select Payroll Parameters') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'salary-management.store', 'method' => 'post']) }}
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('month', __('Payroll Month'), ['class' => 'form-label fw-bold']) }}
                                {{ Form::month('month', date('Y-m'), ['class' => 'form-control', 'required']) }}
                                <small class="text-muted">{{ __('Select the month for which attendance data will be processed.') }}</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('department_id', __('Department (Optional)'), ['class' => 'form-label fw-bold']) }}
                                {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'placeholder' => __('All Departments')]) }}
                                <small class="text-muted">{{ __('Leave blank to generate salary for the entire company.') }}</small>
                            </div>
                        </div>
                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                                <i class="ti ti-refresh me-2"></i>{{ __('Generate Salary Rows') }}
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            
            <div class="alert alert-info mt-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="ti ti-info-circle h4 mb-0 me-3 text-info"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">{{ __('ERP Logic Note') }}</h6>
                        <p class="mb-0 small">
                            {{ __('Salary rows will be automatically calculated based on daily attendance, working hours, and CTC structure (60/20/10/10 breakdown) defined in Employee Setup.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
