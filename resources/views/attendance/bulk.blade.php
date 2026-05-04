@extends('layouts.admin')
@section('page-title')
    {{ __('Personnel Attendance Worksheet') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM') }}</li>
    <li class="breadcrumb-item text-primary fw-bold">{{ __('Daily Tracker') }}</li>
@endsection

@section('content')
<div class="animate-fade-in">
    <div class="row">
        <div class="col-12">
            <div class="glass-panel p-4 mb-4 shadow-sm border-0">
                {{ Form::open(['route' => ['attendanceemployee.bulkattendance'], 'method' => 'get', 'id' => 'att_filter']) }}
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Recording Date') }}</label>
                        {{ Form::date('date', isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'), ['class' => 'p-input w-100']) }}
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">{{ __('Department Focus') }}</label>
                        {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'p-input w-100 select2']) }}
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark px-4 py-2 rounded-pill">
                            <i class="ti ti-adjustments-horizontal me-2"></i> {{ __('Sync Personnel') }}
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="premium-card p-0">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold text-slate-800">{{ __('Real-time Attendance Entry') }}</h4>
                        <div class="p-badge p-badge-indigo">
                            <i class="ti ti-calendar-event"></i> {{ \Carbon\Carbon::parse(isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'))->format('l, d M Y') }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    {{ Form::open(['route' => ['attendanceemployee.bulkattendances'], 'method' => 'post']) }}
                    <div class="table-responsive px-4">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Identity') }}</th>
                                    <th>{{ __('HRM Metrics') }}</th>
                                    <th class="text-center">{{ __('Reporting Status') }}</th>
                                    <th class="text-center">{{ __('Schedule Log') }}</th>
                                    <th class="text-center">{{ __('Daily Yield') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    @php
                                        $att = $employee->present_status($employee->id, isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'));
                                        $status = $att ? $att->status : 'Present';
                                        $initials = collect(explode(' ', $employee->name))->map(fn($n) => $n[0])->take(2)->join('');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 bg-indigo-soft text-indigo rounded-circle me-3 fw-bold" style="background:#eef2ff; color:#4f46e5; width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $employee->name }}</div>
                                                    <div class="small text-muted">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="employee_id[]" value="{{ $employee->id }}">
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-slate-700">{{ optional($employee->department)->name }}</div>
                                            <div class="small text-muted">{{ optional($employee->designation)->name }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm bg-light p-1 rounded-pill" role="group">
                                                @foreach(['Present', 'Absent', 'Late', 'Leave'] as $st)
                                                    <input type="radio" class="btn-check status-trigger" name="status-{{ $employee->id }}" 
                                                        id="st-{{ $st }}-{{ $employee->id }}" value="{{ $st }}" 
                                                        {{ $status == $st ? 'checked' : '' }} data-id="{{ $employee->id }}">
                                                    <label class="btn btn-outline-white border-0 rounded-pill px-3 text-muted" for="st-{{ $st }}-{{ $employee->id }}">{{ __($st) }}</label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="time-box-{{ $employee->id }} {{ ($status == 'Present' || $status == 'Late') ? '' : 'd-none' }}">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <input type="time" name="in-{{ $employee->id }}" class="p-input py-1 px-2 time-calc" 
                                                        value="{{ ($att && $att->clock_in != '00:00:00') ? $att->clock_in : Utility::getValByName('company_start_time') }}"
                                                        data-id="{{ $employee->id }}" style="width:110px;">
                                                    <span class="text-muted">→</span>
                                                    <input type="time" name="out-{{ $employee->id }}" class="p-input py-1 px-2 time-calc" 
                                                        value="{{ ($att && $att->clock_out != '00:00:00') ? $att->clock_out : Utility::getValByName('company_end_time') }}"
                                                        data-id="{{ $employee->id }}" style="width:110px;">
                                                </div>
                                            </div>
                                            <div class="text-muted small timeline-empty-{{ $employee->id }} {{ ($status == 'Present' || $status == 'Late') ? 'd-none' : '' }}">
                                                <i class="ti ti-clock-off opacity-50"></i>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="p-badge p-badge-indigo fs-6 px-3 work-hrs-{{ $employee->id }}">
                                                {{ $att ? number_format($att->working_hours, 1) : '0.0' }}
                                            </div>
                                            <div class="small text-muted mt-1 fw-bold">{{ __('HOURS') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            {{ __('No personnel matched your criteria.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 text-end">
                        <input type="hidden" name="date" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">
                        <button type="submit" class="btn btn-indigo px-5 py-3 rounded-pill text-white shadow-lg" style="background:#4f46e5;">
                            <i class="ti ti-cloud-upload me-2"></i> {{ __('Commit Attendance') }}
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('script-page')
<script>
    $(document).on('change', '.status-trigger', function() {
        const id = this.dataset.id;
        const box = $(`.time-box-${id}`);
        const empty = $(`.timeline-empty-${id}`);
        if (this.value === 'Present' || this.value === 'Late') {
            box.removeClass('d-none').hide().fadeIn(400);
            empty.addClass('d-none');
        } else {
            box.fadeOut(400, function() { $(this).addClass('d-none'); });
            empty.removeClass('d-none').hide().fadeIn(400);
            $(`.work-hrs-${id}`).text('0.0');
        }
    });

    $(document).on('change', '.time-calc', function() {
        const id = this.dataset.id;
        calculate(id);
    });

    function calculate(id) {
        const i = $(`input[name="in-${id}"]`).val();
        const o = $(`input[name="out-${id}"]`).val();
        if (i && o) {
            const start = new Date(`2000-01-01 ${i}`);
            const end = new Date(`2000-01-01 ${o}`);
            if (end > start) {
                const h = (end - start) / 3600000;
                $(`.work-hrs-${id}`).text(h.toFixed(1));
            } else {
                $(`.work-hrs-${id}`).text('0.0');
            }
        }
    }
</script>
@endpush
@endsection
