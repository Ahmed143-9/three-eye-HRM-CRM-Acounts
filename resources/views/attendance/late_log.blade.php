@extends('layouts.admin')
@section('page-title')
    {{ __('Late Attendance Updates') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendanceemployee.index') }}">{{ __('Attendance') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Late Updates') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        {{-- Summary card --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="ti ti-clock fs-3"></i>
                        <div>
                            <div class="h4 mb-0">{{ $totalLateCount }}</div>
                            <small>{{ __('Total Late Updates') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter form --}}
        <div class="card mb-3">
            <div class="card-body">
                {{ Form::open(['route' => 'attendance.late.log', 'method' => 'get']) }}
                <div class="row align-items-end">
                    <div class="col-md-3">
                        {{ Form::label('from_date', __('From Date'), ['class' => 'form-label']) }}
                        {{ Form::date('from_date', request('from_date'), ['class' => 'form-control']) }}
                    </div>
                    <div class="col-md-3">
                        {{ Form::label('to_date', __('To Date'), ['class' => 'form-label']) }}
                        {{ Form::date('to_date', request('to_date'), ['class' => 'form-control']) }}
                    </div>
                    <div class="col-md-3 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm me-1">
                            <i class="ti ti-search"></i> {{ __('Filter') }}
                        </button>
                        <a href="{{ route('attendance.late.log') }}" class="btn btn-danger btn-sm">
                            <i class="ti ti-refresh"></i> {{ __('Reset') }}
                        </a>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

        {{-- Data table --}}
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Attendance Date') }}</th>
                                <th>{{ __('Clock In') }}</th>
                                <th>{{ __('Clock Out') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Late Update Count') }}</th>
                                <th>{{ __('Last Modified') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lateRecords as $record)
                                <tr>
                                    <td>{{ !empty($record->employee) ? $record->employee->name : '—' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($record->date) }}</td>
                                    <td>{{ $record->clock_in != '00:00:00' ? \Auth::user()->timeFormat($record->clock_in) : '—' }}</td>
                                    <td>{{ $record->clock_out != '00:00:00' ? \Auth::user()->timeFormat($record->clock_out) : '—' }}</td>
                                    <td>
                                        <span class="badge {{ $record->status == 'Present' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $record->late_update_count }}</span>
                                    </td>
                                    <td>{{ $record->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('No late attendance updates found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $lateRecords->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
