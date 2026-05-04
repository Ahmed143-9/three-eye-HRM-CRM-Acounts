@extends('layouts.admin')
@section('page-title')
    {{ __('Accounting — Approved Salary Bills') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Accounting') }}</li>
    <li class="breadcrumb-item">{{ __('Approved Bills') }}</li>
@endsection

@section('action-btn')
<div class="float-end">
    <form action="{{ route('salary-management.accounting.approved-bills') }}" method="GET" class="d-inline-flex align-items-center gap-2">
        <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:160px">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="ti ti-filter"></i> {{ __('View Month') }}
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @php
            $totalAmount = $approvedSheets->sum('final_salary');
            $employeeCount = $approvedSheets->count();
        @endphp
        <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="opacity-75 small text-uppercase fw-bold">{{ __('Total Approved Amount') }}</div>
                    <div class="h3 mb-0 fw-bold">{{ Auth::user()->priceFormat($totalAmount) }}</div>
                </div>
                <div class="text-end">
                    <div class="opacity-75 small text-uppercase fw-bold">{{ __('Total Employees') }}</div>
                    <div class="h3 mb-0 fw-bold">{{ $employeeCount }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="ti ti-receipt-2 me-2 text-primary"></i>{{ __('Approved Salary Sheets') }} — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Approved By') }}</th>
                                <th>{{ __('Approved Date') }}</th>
                                <th class="text-end">{{ __('Amount') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedSheets as $sheet)
                                <tr>
                                    <td class="fw-bold">{{ $sheet->serial_no }}</td>
                                    <td>
                                        <div class="fw-bold">{{ optional($sheet->employee)->name }}</div>
                                        <div class="small text-muted">{{ optional($sheet->department)->name }} | {{ optional($sheet->designation)->name }}</div>
                                    </td>
                                    <td>{{ optional($sheet->approvedBy)->name }}</td>
                                    <td>{{ $sheet->approved_at ? \Carbon\Carbon::parse($sheet->approved_at)->format('d M, Y') : '-' }}</td>
                                    <td class="text-end fw-bold text-primary">{{ Auth::user()->priceFormat($sheet->final_salary) }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('salary-management.mark-as-paid', $sheet->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-dark px-3 py-1">
                                                <i class="ti ti-credit-card me-1"></i> {{ __('Pay Now') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        {{ __('No approved salary bills for this month.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
