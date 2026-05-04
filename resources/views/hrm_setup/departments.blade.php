@extends('layouts.admin')
@section('page-title') {{ __('HRM Setup — Departments') }} @endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM Setup') }}</li>
    <li class="breadcrumb-item">{{ __('Departments') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header"><h6 class="mb-0">{{ __('Add Department') }}</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('hrm-setup.departments.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Department Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('e.g., Human Resources') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-plus me-1"></i> {{ __('Add Department') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ __('All Departments') }}</h6>
                <a href="{{ route('hrm-setup.designations.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-briefcase"></i> {{ __('Manage Designations') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($departments->isEmpty())
                    <div class="text-center py-4 text-muted">{{ __('No departments found.') }}</div>
                @else
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>{{ __('Name') }}</th><th>{{ __('Actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($departments as $i => $dept)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <form method="POST" action="{{ route('hrm-setup.departments.update', $dept->id) }}" class="d-flex gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $dept->name }}" class="form-control form-control-sm" style="max-width:220px" required>
                                    <button class="btn btn-sm btn-outline-success"><i class="ti ti-check"></i></button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('hrm-setup.departments.destroy', $dept->id) }}"
                                    onsubmit="return confirm('{{ __('Delete department?') }}')">
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
