@extends('layouts.admin')
@section('page-title')
    {{ __('Payroll Batch Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('salary-management.index') }}">{{ __('Salary Control') }}</a></li>
    <li class="breadcrumb-item text-primary fw-bold">{{ $batch->batch_no }}</li>
@endsection

@section('content')
<div class="animate-fade-in">
    <div class="row">
        <div class="col-12">
            <div class="glass-panel p-4 mb-4 d-flex justify-content-between align-items-center shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded-circle bg-white text-indigo me-4 shadow-sm" style="color:#4f46e5;"><i class="ti ti-receipt fs-2"></i></div>
                    <div>
                        <h3 class="mb-1 fw-bold text-slate-800">{{ __('Payroll Master Manifest') }}</h3>
                        <div class="d-flex gap-3 text-muted small fw-bold text-uppercase letter-spacing-1">
                            <span><i class="ti ti-hash me-1"></i>{{ $batch->batch_no }}</span>
                            <div class="vr"></div>
                            <span><i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($batch->month.'-01')->format('F Y') }}</span>
                            <div class="vr"></div>
                            <span><i class="ti ti-building me-1"></i>{{ $batch->department ? $batch->department->name : __('Company-wide') }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if($batch->status == 'Draft' && \Auth::user()->can('manage employee'))
                        <a href="{{ route('salary-management.submit', $batch->id) }}" class="btn btn-warning px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="ti ti-send me-1"></i> {{ __('Request Approval') }}
                        </a>
                    @endif

                    @if($batch->status == 'Pending Approval' && (\Auth::user()->type == 'company' || \Auth::user()->can('approve expense')))
                        <a href="{{ route('salary-management.approve', $batch->id) }}" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="ti ti-circle-check me-1"></i> {{ __('Final Approve') }}
                        </a>
                    @endif

                    @if($batch->status == 'Approved' && (\Auth::user()->can('manage bill') || \Auth::user()->type == 'company'))
                        <a href="{{ route('salary-management.pay', $batch->id) }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="background:#4f46e5;">
                            <i class="ti ti-wallet me-1"></i> {{ __('Process Payment') }}
                        </a>
                    @endif

                    @php
                        $statusPill = 'p-badge-amber';
                        if($batch->status == 'Approved') $statusPill = 'p-badge-emerald';
                        if($batch->status == 'Paid') $statusPill = 'p-badge-indigo';
                    @endphp
                    <div class="p-badge {{ $statusPill }} px-4 py-2 fs-6 rounded-pill">
                        {{ strtoupper($batch->status) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="premium-card p-0 overflow-hidden">
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="premium-table mb-0" style="border-spacing: 0;">
                        <thead style="position: sticky; top: 0; z-index: 100;">
                            <tr>
                                <th style="background: #0f172a;">{{ __('Sr. No.') }}</th>
                                <th style="position: sticky; left: 0; z-index: 110; background: #0f172a;">{{ __('Employee Name') }}</th>
                                <th class="text-end">{{ __('CTC (Main Salary)') }}</th>
                                <th class="text-end">{{ __('Basic (60%)') }}</th>
                                <th class="text-end">{{ __('HRA (20%)') }}</th>
                                <th class="text-end">{{ __('Conv. Allow (10%)') }}</th>
                                <th class="text-end">{{ __('Medi. Allow (10%)') }}</th>
                                <th class="text-center" style="background: #eef2ff; color:#4f46e5;">{{ __('Attendance') }}</th>
                                <th class="text-center" style="background: #eef2ff; color:#4f46e5;">{{ __('Leave') }}</th>
                                <th class="text-center" style="background: #eef2ff; color:#4f46e5;">{{ __('Working Hours') }}</th>
                                <th class="text-end" style="background: #eef2ff; color:#4f46e5;">{{ __('Total Salary') }}</th>
                                
                                @if($isAdmin || $batch->status != 'Draft')
                                    <th class="text-center" style="background: #fff1f2; color:#e11d48;">{{ __('Contri. to PF') }}</th>
                                    <th class="text-center" style="background: #fff1f2; color:#e11d48;">{{ __('Prof. Tax') }}</th>
                                    <th class="text-center" style="background: #fff1f2; color:#e11d48;">{{ __('TDS') }}</th>
                                    <th class="text-center" style="background: #fff1f2; color:#e11d48;">{{ __('Salary Advance') }}</th>
                                    <th class="text-center" style="background: #fff1f2; color:#e11d48;">{{ __('Salary Deduction') }}</th>
                                @endif

                                <th class="text-end pe-4" style="background: #f0fdf4; color:#166534;">{{ __('Net Disbursement') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batch->salarySheets as $index => $row)
                                <tr data-id="{{ $row->id }}">
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td style="position: sticky; left: 0; background: #fff; z-index: 10; border-right: 1px solid #f1f5f9;">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 bg-light rounded-circle me-3 text-secondary" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i class="ti ti-user fs-5"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $row->employee->name }}</div>
                                                <div class="small text-muted">{{ optional($row->designation)->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-muted">{{ number_format($row->net_salary, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row->basic_salary, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row->hra, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row->conveyance_allowance, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row->medical_allowance, 2) }}</td>
                                    <td class="text-center">
                                        <div class="p-badge p-badge-emerald px-2">{{ $row->present_days }}d</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="p-badge p-badge-rose px-2">{{ $row->leave_count }}d</div>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($row->working_hours, 1) }}</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($row->payable_amount, 2) }}</td>

                                    @if($isAdmin || $batch->status != 'Draft')
                                        @php $editable = ($batch->status == 'Pending Approval' && $isAdmin); @endphp
                                        <td class="text-center">
                                            <input type="number" step="0.01" class="p-input py-1 px-2 text-end deduction-trigger" name="pf_contribution" value="{{ $row->pf_contribution }}" {{ $editable ? '' : 'readonly' }} data-id="{{ $row->id }}" style="width:85px;">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" class="p-input py-1 px-2 text-end deduction-trigger" name="professional_tax" value="{{ $row->professional_tax }}" {{ $editable ? '' : 'readonly' }} data-id="{{ $row->id }}" style="width:85px;">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" class="p-input py-1 px-2 text-end deduction-trigger" name="tds" value="{{ $row->tds }}" {{ $editable ? '' : 'readonly' }} data-id="{{ $row->id }}" style="width:85px;">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" class="p-input py-1 px-2 text-end deduction-trigger" name="salary_advance" value="{{ $row->salary_advance }}" {{ $editable ? '' : 'readonly' }} data-id="{{ $row->id }}" style="width:85px;">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01" class="p-input py-1 px-2 text-end deduction-trigger" name="deduction_amount" value="{{ $row->deduction_amount }}" {{ $editable ? '' : 'readonly' }} data-id="{{ $row->id }}" style="width:85px;">
                                        </td>
                                    @endif

                                    <td class="text-end pe-4">
                                        <div class="h6 mb-0 fw-bold text-success final-val-{{ $row->id }}" style="color:#166534;">{{ number_format($row->final_salary, 2) }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-slate-900 text-white d-flex justify-content-between align-items-center" style="background:#0f172a;">
                    <div class="d-flex align-items-center gap-4">
                        <div>
                            <span class="small opacity-50 text-uppercase letter-spacing-1 fw-bold d-block mb-1">{{ __('Personnel Count') }}</span>
                            <h4 class="mb-0 fw-bold">{{ $batch->salarySheets->count() }} Employees</h4>
                        </div>
                        <div class="vr opacity-25"></div>
                        <div>
                            <span class="small opacity-50 text-uppercase letter-spacing-1 fw-bold d-block mb-1">{{ __('Processed At') }}</span>
                            <h4 class="mb-0 fw-bold">{{ $batch->created_at->format('d M, Y') }}</h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="small opacity-50 text-uppercase letter-spacing-1 fw-bold d-block mb-1">{{ __('Aggregate Net Disbursement') }}</span>
                        <h2 class="mb-0 fw-bold text-warning batch-total-display">{{ number_format($batch->total_net_payable, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script-page')
<script>
    @if($isAdmin && $batch->status == 'Pending Approval')
    $(document).on('change', '.deduction-trigger', function() {
        const id = $(this).data('id');
        const row = $(`tr[data-id="${id}"]`);
        const data = {
            _token: '{{ csrf_token() }}',
            pf_contribution: row.find('input[name="pf_contribution"]').val(),
            professional_tax: row.find('input[name="professional_tax"]').val(),
            tds: row.find('input[name="tds"]').val(),
            salary_advance: row.find('input[name="salary_advance"]').val(),
            deduction_amount: row.find('input[name="deduction_amount"]').val(),
        };

        $.ajax({
            url: '{{ url("salary-management/update-row") }}/' + id,
            method: 'POST',
            data: data,
            success: function(res) {
                if(res.success) {
                    $(`.final-val-${id}`).text(res.final_salary.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    show_toastr('Success', res.message, 'success');
                    // Refresh totals
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    });
    @endif
</script>
@endpush
@endsection
