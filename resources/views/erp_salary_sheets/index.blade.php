@extends('layouts.admin')
@section('page-title')
    {{ __('Payroll Command Center') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM') }}</li>
    <li class="breadcrumb-item text-primary fw-bold">{{ __('Salary Control') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create expense')
            <a href="{{ route('salary-management.create') }}" class="btn btn-indigo px-4 py-2 rounded-pill text-white shadow-sm" style="background:#4f46e5;">
                <i class="ti ti-plus me-1"></i> {{ __('Initialize New Batch') }}
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="animate-fade-in">
    <div class="row g-4">
        @forelse ($batches as $batch)
            <div class="col-xl-4 col-md-6">
                <div class="premium-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="p-badge p-badge-indigo mb-2">
                                <i class="ti ti-hash"></i> {{ $batch->batch_no }}
                            </div>
                            <h4 class="mb-0 fw-bold text-slate-800">{{ \Carbon\Carbon::parse($batch->month . '-01')->format('F Y') }}</h4>
                        </div>
                        @php
                            $statusBadge = 'p-badge-amber';
                            if($batch->status == 'Approved') $statusBadge = 'p-badge-emerald';
                            if($batch->status == 'Paid') $statusBadge = 'p-badge-indigo';
                        @endphp
                        <div class="p-badge {{ $statusBadge }} rounded-pill px-3">
                            {{ strtoupper($batch->status) }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="small fw-bold text-muted text-uppercase letter-spacing-1 mb-2">{{ __('Functional Scope') }}</div>
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-light rounded-circle me-3"><i class="ti ti-building-community text-primary"></i></div>
                            <div class="fw-bold text-dark">{{ $batch->department ? $batch->department->name : __('Global Operations') }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4">
                                <div class="small text-muted mb-1">{{ __('Net Payable') }}</div>
                                <div class="fw-bold text-indigo" style="color:#4f46e5;">{{ number_format($batch->total_net_payable, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4">
                                <div class="small text-muted mb-1">{{ __('Records') }}</div>
                                <div class="fw-bold text-dark">{{ $batch->salarySheets->count() }} Personnel</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('salary-management.show', $batch->id) }}" class="btn btn-dark w-100 rounded-pill py-2 shadow-sm">
                            <i class="ti ti-external-link me-1"></i> {{ __('View Manifest') }}
                        </a>
                        @if($batch->status == 'Draft')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['salary-management.destroy', $batch->id], 'class' => 'w-auto']) !!}
                                <button type="submit" class="btn btn-soft-danger px-3 py-2 rounded-pill bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Discard Batch') }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="premium-card p-5 text-center">
                    <i class="ti ti-receipt-off fs-1 text-muted mb-4 opacity-25"></i>
                    <h3 class="text-slate-700 fw-bold">{{ __('No Payroll Batches Found') }}</h3>
                    <p class="text-muted">{{ __('Start the monthly payroll cycle by initializing a new batch.') }}</p>
                    <a href="{{ route('salary-management.create') }}" class="btn btn-indigo px-5 py-3 rounded-pill text-white mt-4 shadow-lg" style="background:#4f46e5;">
                        <i class="ti ti-plus me-1"></i> {{ __('Create First Batch') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
