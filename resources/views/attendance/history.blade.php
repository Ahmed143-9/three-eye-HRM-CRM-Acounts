@extends('layouts.admin')
@section('page-title')
    {{ __('Analytics Dashboard') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('HRM') }}</li>
    <li class="breadcrumb-item text-primary fw-bold">{{ __('Performance History') }}</li>
@endsection

@section('content')
<div class="animate-fade-in">
    @php
        $total = $attendances->count();
        $pCount = $attendances->where('status', 'Present')->count();
        $aCount = $attendances->where('status', 'Absent')->count();
        $lCount = $attendances->where('status', 'Late')->count();
        $lvCount = $attendances->where('status', 'Leave')->count();
        $totalHrs = $attendances->sum('working_hours');
    @endphp

    <div class="row g-4">
        <div class="col-md-3">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-4 bg-soft-primary text-primary me-3" style="background:#eef2ff; color:#4f46e5;"><i class="ti ti-activity fs-2"></i></div>
                    <div class="small fw-bold text-muted text-uppercase letter-spacing-1">{{ __('Total Capacity') }}</div>
                </div>
                <div class="h2 mb-0 fw-bold">{{ $total }} <span class="fs-6 text-muted">{{ __('Logs') }}</span></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-4 bg-soft-success text-success me-3" style="background:#ecfdf5; color:#10b981;"><i class="ti ti-circle-check fs-2"></i></div>
                    <div class="small fw-bold text-muted text-uppercase letter-spacing-1">{{ __('Engagement') }}</div>
                </div>
                <div class="h2 mb-0 fw-bold text-success">{{ $total > 0 ? round(($pCount/$total)*100, 1) : 0 }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-4 bg-soft-warning text-warning me-3" style="background:#fffbeb; color:#f59e0b;"><i class="ti ti-clock-play fs-2"></i></div>
                    <div class="small fw-bold text-muted text-uppercase letter-spacing-1">{{ __('Late Index') }}</div>
                </div>
                <div class="h2 mb-0 fw-bold text-warning">{{ $lCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-card p-4 bg-slate-900 text-white" style="background:#0f172a;">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-4 bg-soft-light text-white me-3" style="background:rgba(255,255,255,0.1);"><i class="ti ti-chart-bar fs-2"></i></div>
                    <div class="small fw-bold text-white-50 text-uppercase letter-spacing-1">{{ __('Total Yield') }}</div>
                </div>
                <div class="h2 mb-0 fw-bold text-white">{{ number_format($totalHrs, 1) }} <span class="fs-6 opacity-50">Hrs</span></div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="glass-panel p-3 mb-4 d-flex align-items-center justify-content-between shadow-sm">
                {{ Form::open(['route' => ['attendanceemployee.history'], 'method' => 'get', 'class' => 'd-flex align-items-center gap-4 flex-grow-1']) }}
                    <div class="d-flex align-items-center gap-3">
                        <i class="ti ti-filter text-muted"></i>
                        {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'p-input bg-transparent border-0 fw-bold', 'style' => 'width:180px;']) }}
                        <div class="vr h-50 mx-2"></div>
                        {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'p-input bg-transparent border-0 fw-bold select2']) }}
                    </div>
                    <button type="submit" class="btn btn-indigo px-4 py-2 rounded-pill text-white shadow-sm" style="background:#4f46e5;">
                        <i class="ti ti-search me-1"></i> {{ __('Apply Filters') }}
                    </button>
                {{ Form::close() }}
                <div class="text-end pe-3">
                    <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 border border-info">{{ __('Live Analytics Enabled') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card p-0 overflow-hidden">
                <div class="table-responsive px-4 pt-4">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>{{ __('Timeline') }}</th>
                                <th>{{ __('Personnel Log') }}</th>
                                <th class="text-center">{{ __('HRM Status') }}</th>
                                <th class="text-center">{{ __('Session Bound') }}</th>
                                <th class="text-end">{{ __('Work Yield') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($attendance->date)->format('d M, Y') }}</div>
                                        <div class="small text-muted text-uppercase fw-bold letter-spacing-1">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 bg-light rounded-circle me-3 text-secondary" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i class="ti ti-user fs-5"></i></div>
                                            <div>
                                                <div class="fw-bold">{{ optional($attendance->employee)->name }}</div>
                                                <div class="small text-muted">{{ \Auth::user()->employeeIdFormat(optional($attendance->employee)->employee_id) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = 'p-badge-indigo';
                                            if($attendance->status == 'Absent') $badgeClass = 'p-badge-rose';
                                            if($attendance->status == 'Late') $badgeClass = 'p-badge-amber';
                                            if($attendance->status == 'Leave') $badgeClass = 'p-badge-emerald';
                                        @endphp
                                        <span class="p-badge {{ $badgeClass }}">
                                            {{ __($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="badge bg-light text-dark px-2 py-1">{{ $attendance->clock_in != '00:00:00' ? $attendance->clock_in : '--:--' }}</div>
                                            <i class="ti ti-arrow-narrow-right text-muted"></i>
                                            <div class="badge bg-light text-dark px-2 py-1">{{ $attendance->clock_out != '00:00:00' ? $attendance->clock_out : '--:--' }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="h5 mb-0 fw-bold text-indigo" style="color:#4f46e5;">{{ number_format($attendance->working_hours, 1) }} <small class="text-muted fw-normal">{{ __('Hrs') }}</small></div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="ti ti-database-off fs-1 mb-3 opacity-25"></i>
                                        <p class="fw-bold">{{ __('No historical logs found for the current filter.') }}</p>
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
