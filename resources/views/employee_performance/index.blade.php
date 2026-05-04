@extends('layouts.admin')
@section('page-title') {{ __('Employee Performance') }} @endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Performance') }}</li>
@endsection

@push('css')
<style>
.cell-editable {
    background: #fff9e6;
    border: 1px dashed #f0a500;
    padding: 2px 5px;
    border-radius: 4px;
    cursor: text;
    min-width: 40px;
    display: inline-block;
}
.cell-editable:focus {
    background: #fff;
    border: 1px solid #f59e0b;
    outline: none;
}
</style>
@endpush

@section('action-btn')
<div class="d-flex gap-2 float-end">
    <form action="{{ route('employee-performance.generate') }}" method="POST" class="d-inline-flex align-items-center gap-2">
        @csrf
        <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:155px">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="ti ti-refresh"></i> {{ __('Generate Performance') }}
        </button>
    </form>
    <form action="{{ route('employee-performance.index') }}" method="GET" class="d-inline-flex align-items-center gap-2">
        <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width:155px">
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="ti ti-filter"></i> {{ __('View') }}
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="ti ti-user-check me-2 text-primary"></i>
                    {{ __('Performance Review') }} — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                </h5>
                <p class="text-muted small mb-0">{{ __('HRM can manually adjust metrics below before generating salary sheets.') }}</p>
            </div>
            <div class="card-body p-0">
                @if($performances->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">{{ __('No performance data found. Click Generate.') }}</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="perfTable">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Employee') }}</th>
                                <th class="text-center">{{ __('Present') }}</th>
                                <th class="text-center">{{ __('Absent') }}</th>
                                <th class="text-center">{{ __('Late') }}</th>
                                <th class="text-center">{{ __('Leave') }}</th>
                                <th class="text-end">{{ __('Work Hrs') }}</th>
                                <th class="text-end">{{ __('OT Hrs') }}</th>
                                <th class="text-end">{{ __('Payable') }}</th>
                                <th class="text-end">{{ __('Receivable') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($performances as $i => $perf)
                            <tr data-id="{{ $perf->id }}">
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ optional($perf->employee)->name }}</div>
                                    <div class="small text-muted">{{ optional($perf->department)->name }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="cell-editable" contenteditable="true" data-field="present_days" data-id="{{ $perf->id }}">
                                        {{ $perf->present_days }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="cell-editable" contenteditable="true" data-field="absent_days" data-id="{{ $perf->id }}">
                                        {{ $perf->absent_days }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="cell-editable" contenteditable="true" data-field="late_count" data-id="{{ $perf->id }}">
                                        {{ $perf->late_count }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="cell-editable" contenteditable="true" data-field="leave_count" data-id="{{ $perf->id }}">
                                        {{ $perf->leave_count }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="cell-editable" contenteditable="true" data-field="total_working_hours" data-id="{{ $perf->id }}">
                                        {{ number_format($perf->total_working_hours, 1, '.', '') }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="cell-editable" contenteditable="true" data-field="overtime_hours" data-id="{{ $perf->id }}">
                                        {{ number_format($perf->overtime_hours, 1, '.', '') }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <div class="cell-editable" contenteditable="true" data-field="payable_amount" data-id="{{ $perf->id }}">
                                        {{ number_format($perf->payable_amount, 2, '.', '') }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="cell-editable" contenteditable="true" data-field="receivable_amount" data-id="{{ $perf->id }}">
                                        {{ number_format($perf->receivable_amount, 2, '.', '') }}
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
$(document).on('blur', '.cell-editable', function() {
    const id = $(this).data('id');
    const field = $(this).data('field');
    const val = $(this).text().trim();

    $.ajax({
        url: `{{ url('expense-management/employee-performance') }}/${id}/update`,
        type: 'POST',
        data: {
            [field]: val,
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            if(data.success) {
                show_toastr('Success', data.message, 'success');
            }
        }
    });
});
</script>
@endpush
