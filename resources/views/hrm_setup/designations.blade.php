@extends('layouts.admin')
@section('page-title') {{ __('HRM Setup — Designations') }} @endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM Setup') }}</li>
    <li class="breadcrumb-item">{{ __('Designations') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header"><h6 class="mb-0">{{ __('Add Designation') }}</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('hrm-setup.designations.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Department') }}</label>
                        <select name="department_id" class="form-control select2" id="newDesigDept">
                            <option value="">{{ __('Select Department') }}</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Designation Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('e.g., Senior Engineer') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-plus me-1"></i> {{ __('Add Designation') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ __('All Designations') }}</h6>
                <a href="{{ route('hrm-setup.departments.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-building"></i> {{ __('Manage Departments') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($designations->isEmpty())
                    <div class="text-center py-4 text-muted">{{ __('No designations found.') }}</div>
                @else
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>{{ __('Department') }}</th><th>{{ __('Name') }}</th><th>{{ __('Actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($designations as $i => $desig)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ optional($desig->department)->name ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('hrm-setup.designations.update', $desig->id) }}" class="d-flex gap-2">
                                    @csrf @method('PUT')
                                    <select name="department_id" class="form-control form-control-sm" style="max-width:130px">
                                        <option value="">-</option>
                                        @foreach($departments as $dId => $dName)
                                            <option value="{{ $dId }}" {{ $desig->department_id == $dId ? 'selected' : '' }}>{{ $dName }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="name" value="{{ $desig->name }}" class="form-control form-control-sm" style="max-width:180px" required>
                                    <button class="btn btn-sm btn-outline-success"><i class="ti ti-check"></i></button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('hrm-setup.designations.destroy', $desig->id) }}"
                                    onsubmit="return confirm('{{ __('Delete designation?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
