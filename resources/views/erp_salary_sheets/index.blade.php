@extends('layouts.admin')
@section('page-title')
    {{ __('Salary Sheet Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expense Management') }}</li>
    <li class="breadcrumb-item">{{ __('Salary Sheet') }}</li>
@endsection

@push('css')
<style>
/* ── ERP Spreadsheet Styles ─────────────────────────────── */
.salary-spreadsheet {
    font-size: 0.82rem;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1500px;
}
.salary-spreadsheet thead th {
    background: #1a1f37;
    color: #fff;
    padding: 10px 8px;
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
    border-right: 1px solid #2e3556;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.salary-spreadsheet tbody td {
    padding: 7px 8px;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #f0f0f0;
    vertical-align: middle;
    white-space: nowrap;
}
.salary-spreadsheet tbody tr:hover {
    background: #f7f8ff;
}
.cell-editable {
    min-width: 90px;
    background: #fff9e6;
    border: 1px dashed #f0a500;
    border-radius: 4px;
    padding: 4px 7px;
    cursor: text;
    outline: none;
}
.cell-editable:focus {
    background: #fffde7;
    border: 1.5px solid #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
}
.cell-locked {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}
.badge-erp {
    font-size: 0.7rem;
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-draft    { background: #e2e3e5; color: #383d41; }
.status-pending  { background: #fff3cd; color: #856404; }
.status-approved { background: #cfe2ff; color: #084298; }
.status-paid     { background: #d1e7dd; color: #0f5132; }
.status-rejected { background: #f8d7da; color: #842029; }

.spreadsheet-wrap {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
}
.col-employee { min-width: 160px; font-weight: 600; }
.col-amount   { min-width: 100px; text-align: right; font-family: monospace; }
.col-final    { min-width: 110px; text-align: right; font-weight: 700; color: #0d6efd; font-family: monospace; }
</style>
@endpush

@section('action-btn')
<div class="header-actions float-end">
    {{-- HRM: Generate --}}
    @if($isHRM || Auth::user()->type === 'company')
    <form action="{{ route('salary-management.generate') }}" method="POST" class="d-inline-flex align-items-center gap-2 me-2">
        @csrf
        <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:160px">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i> {{ __('Generate Sheet') }}
        </button>
    </form>
    @endif

    {{-- Filter View --}}
    <form action="{{ route('salary-management.index') }}" method="GET" class="d-inline-flex align-items-center gap-2">
        <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:160px">
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="ti ti-filter"></i> {{ __('View') }}
        </button>
    </form>

    @if($isAdmin && $salarySheets->where('status', 'Pending Approval')->count() > 0)
    <form action="{{ route('salary-management.bulk-approve') }}" method="POST" class="d-inline ms-2">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <button type="submit" class="btn btn-sm btn-success">
            <i class="ti ti-checks"></i> {{ __('Bulk Approve Pending') }}
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Cards --}}
        @php
            $totalCount  = $salarySheets->count();
            $draftCount  = $salarySheets->where('status', 'Draft')->count();
            $pendingCount= $salarySheets->where('status', 'Pending Approval')->count();
            $approvedCount= $salarySheets->where('status', 'Approved')->count();
            $paidCount   = $salarySheets->where('status', 'Paid')->count();
            $totalFinal  = $salarySheets->sum('final_salary');
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md">
                <div class="card border-0 shadow-sm"><div class="card-body py-3 text-center">
                    <div class="text-muted small">{{ __('Draft') }}</div>
                    <div class="h4 mb-0 fw-bold">{{ $draftCount }}</div>
                </div></div>
            </div>
            <div class="col-md">
                <div class="card border-0 shadow-sm"><div class="card-body py-3 text-center">
                    <div class="text-muted small">{{ __('Pending Approval') }}</div>
                    <div class="h4 mb-0 fw-bold text-warning">{{ $pendingCount }}</div>
                </div></div>
            </div>
            <div class="col-md">
                <div class="card border-0 shadow-sm"><div class="card-body py-3 text-center">
                    <div class="text-muted small">{{ __('Approved') }}</div>
                    <div class="h4 mb-0 fw-bold text-info">{{ $approvedCount }}</div>
                </div></div>
            </div>
            <div class="col-md">
                <div class="card border-0 shadow-sm"><div class="card-body py-3 text-center">
                    <div class="text-muted small">{{ __('Paid') }}</div>
                    <div class="h4 mb-0 fw-bold text-success">{{ $paidCount }}</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white"><div class="card-body py-3 text-center">
                    <div class="small opacity-75">{{ __('Total Net Payable') }}</div>
                    <div class="h4 mb-0 fw-bold">{{ Auth::user()->priceFormat($totalFinal) }}</div>
                </div></div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="ti ti-layout-grid me-2 text-primary"></i>
                    {{ __('Salary Spreadsheet') }} — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                </h5>
                <div>
                    @if($isAdmin)
                        <span class="badge bg-soft-warning text-warning border border-warning px-3"><i class="ti ti-edit me-1"></i>{{ __('Admin Review Mode') }}</span>
                    @elseif($isHRM)
                        <span class="badge bg-soft-primary text-primary border border-primary px-3"><i class="ti ti-user me-1"></i>{{ __('HRM Input Mode') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($salarySheets->isEmpty())
                    <div class="text-center py-5">
                        <img src="{{ asset('assets/images/no-data.svg') }}" width="120" class="opacity-50">
                        <p class="text-muted mt-3">{{ __('No salary data found for this period.') }}</p>
                    </div>
                @else
                <div class="spreadsheet-wrap">
                    <table class="salary-spreadsheet" id="salarySpreadsheet">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="col-employee">{{ __('Employee') }}</th>
                                <th>{{ __('Dept/Desig') }}</th>
                                <th class="col-number text-center">{{ __('P/A/L/L') }}</th>
                                <th class="col-amount">{{ __('Work Hrs') }}</th>
                                <th class="col-amount">{{ __('Net Salary') }}</th>
                                <th class="col-amount text-success">{{ __('Payable') }} @if($isAdmin) <i class="ti ti-edit"></i> @endif</th>
                                <th class="col-amount text-danger">{{ __('Deduction') }} @if($isAdmin) <i class="ti ti-edit"></i> @endif</th>
                                <th class="col-amount text-warning">{{ __('Receivable') }} @if($isAdmin) <i class="ti ti-edit"></i> @endif</th>
                                <th class="col-final">{{ __('Final Salary') }}</th>
                                <th class="col-status text-center">{{ __('Status') }}</th>
                                <th class="col-action">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($salarySheets as $i => $sheet)
                            <tr data-id="{{ $sheet->id }}">
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="col-employee">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-soft-primary text-primary rounded-circle" style="width:30px;height:30px;font-size:0.7rem;display:flex;align-items:center;justify-content:center">
                                            {{ strtoupper(substr(optional($sheet->employee)->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ optional($sheet->employee)->name }}</div>
                                            <div class="text-muted small" style="font-size:0.65rem">{{ $sheet->serial_no }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">
                                    <div>{{ optional($sheet->department)->name }}</div>
                                    <div class="text-muted">{{ optional($sheet->designation)->name }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="text-success">{{ $sheet->present_days }}</span> /
                                    <span class="text-danger">{{ $sheet->absent_days }}</span> /
                                    <span class="text-warning">{{ $sheet->late_count }}</span> /
                                    <span class="text-info">{{ $sheet->leave_count }}</span>
                                </td>
                                <td class="col-amount">{{ number_format($sheet->working_hours, 1) }}h</td>
                                <td class="col-amount fw-bold">{{ Auth::user()->priceFormat($sheet->net_salary) }}</td>
                                
                                {{-- Editable Columns for Admin --}}
                                @php
                                    $isEditable = ($isAdmin && $sheet->status === 'Pending Approval');
                                    $cellClass  = $isEditable ? 'cell-editable' : 'cell-locked';
                                    $canEdit    = $isEditable ? 'true' : 'false';
                                @endphp

                                <td class="col-amount">
                                    <div class="{{ $cellClass }}" contenteditable="{{ $canEdit }}" data-field="payable_amount" data-id="{{ $sheet->id }}">
                                        {{ number_format($sheet->payable_amount, 2, '.', '') }}
                                    </div>
                                </td>
                                <td class="col-amount">
                                    <div class="{{ $cellClass }}" contenteditable="{{ $canEdit }}" data-field="deduction_amount" data-id="{{ $sheet->id }}">
                                        {{ number_format($sheet->deduction_amount, 2, '.', '') }}
                                    </div>
                                </td>
                                <td class="col-amount">
                                    <div class="{{ $cellClass }}" contenteditable="{{ $canEdit }}" data-field="receivable_amount" data-id="{{ $sheet->id }}">
                                        {{ number_format($sheet->receivable_amount, 2, '.', '') }}
                                    </div>
                                </td>

                                <td class="col-final" id="final-{{ $sheet->id }}">
                                    {{ Auth::user()->priceFormat($sheet->final_salary) }}
                                </td>

                                <td class="text-center">
                                    <span class="badge-erp status-{{ strtolower(str_replace(' ', '-', $sheet->status)) }}">
                                        {{ __($sheet->status) }}
                                    </span>
                                </td>

                                <td class="col-action">
                                    <div class="d-flex gap-1 justify-content-center">
                                        {{-- HRM: Submit --}}
                                        @if($isHRM && $sheet->status === 'Draft')
                                            <form action="{{ route('salary-management.submit-for-approval', $sheet->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-xs btn-primary py-1" title="{{ __('Submit for Approval') }}">
                                                    <i class="ti ti-send"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Admin: Approve/Reject --}}
                                        @if($isAdmin && $sheet->status === 'Pending Approval')
                                            <form action="{{ route('salary-management.approve', $sheet->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-xs btn-success py-1" title="{{ __('Approve') }}">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('salary-management.reject', $sheet->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-xs btn-danger py-1" title="{{ __('Reject') }}">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Accounts: Pay --}}
                                        @if($isAccounts && $sheet->status === 'Approved')
                                            <form action="{{ route('salary-management.mark-as-paid', $sheet->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-xs btn-dark py-1" title="{{ __('Process Payment') }}">
                                                    <i class="ti ti-credit-card"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Delete --}}
                                        @if(in_array($sheet->status, ['Draft', 'Rejected']))
                                            <form action="{{ route('salary-management.destroy', $sheet->id) }}" method="POST" onsubmit="return confirm('Delete?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-light py-1"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    if(!isAdmin) return;

    const CSRF = '{{ csrf_token() }}';
    const UPDATE_URL = '{{ url("expense-management/salary-management") }}';

    function formatPrice(val) {
        return '{{ Utility::settings()["site_currency_symbol"] ?? "$" }}' + parseFloat(val).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    document.querySelectorAll('.cell-editable').forEach(cell => {
        cell.addEventListener('blur', function() {
            const id = this.dataset.id;
            const field = this.dataset.field;
            const val = this.textContent.trim();

            fetch(`${UPDATE_URL}/${id}/update-row`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ [field]: val })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const finalCell = document.getElementById('final-' + id);
                    if(finalCell) finalCell.textContent = formatPrice(data.final_salary);
                    show_toastr('Success', data.message, 'success');
                } else {
                    show_toastr('Error', data.message, 'error');
                }
            })
            .catch(err => console.error(err));
        });

        cell.addEventListener('keydown', e => {
            if(e.key === 'Enter') { e.preventDefault(); cell.blur(); }
        });
    });
})();
</script>
@endpush

