@extends('layouts.admin')
@section('page-title')
    {{__('Employee Assets')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{ $employee->name }}</li>
@endsection
@php
    $assetImage=\App\Models\Utility::get_file('uploads/assets/');
@endphp

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Employee Info -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">{{__('Employee Information')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>{{__('Name:')}}</strong><br>
                        {{ $employee->name }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{__('Employee ID:')}}</strong><br>
                        {{ $employee->employee_id }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{__('Department:')}}</strong><br>
                        {{ $employee->department ? $employee->department->name : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assets -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">{{__('Currently Assigned Assets')}} ({{ $currentAssets->count() }})</h5>
            </div>
            <div class="card-body">
                @if($currentAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__('Asset Code')}}</th>
                                    <th>{{__('Asset Name')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Assign Date')}}</th>
                                    <th>{{__('Remarks')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentAssets as $assignment)
                                    <tr>
                                        <td><strong>{{ $assignment->asset->asset_code }}</strong></td>
                                        <td>
                                            @if($assignment->asset->image)
                                                <img src="{{ $assetImage . $assignment->asset->image }}" alt="" class="rounded me-2" width="40" height="40">
                                            @endif
                                            {{ $assignment->asset->name }}
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $assignment->asset->category }}</span></td>
                                        <td>{{ \Auth::user()->dateFormat($assignment->assign_date) }}</td>
                                        <td>{{ $assignment->remarks ?: '-' }}</td>
                                        <td>
                                            <a href="{{ route('account-assets.show', $assignment->asset_id) }}" class="btn btn-sm btn-info">
                                                <i class="ti ti-eye"></i> {{__('View')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-package fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{__('No assets currently assigned to this employee')}}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Past Assets -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">{{__('Past Assignments')}} ({{ $pastAssets->count() }})</h5>
            </div>
            <div class="card-body">
                @if($pastAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__('Asset Code')}}</th>
                                    <th>{{__('Asset Name')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Assign Date')}}</th>
                                    <th>{{__('Return Date')}}</th>
                                    <th>{{__('Return Status')}}</th>
                                    <th>{{__('Remarks')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pastAssets as $assignment)
                                    <tr>
                                        <td><strong>{{ $assignment->asset->asset_code }}</strong></td>
                                        <td>{{ $assignment->asset->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $assignment->asset->category }}</span></td>
                                        <td>{{ \Auth::user()->dateFormat($assignment->assign_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($assignment->return_date) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $assignment->status_color }}">{{ $assignment->status }}</span>
                                        </td>
                                        <td>{{ $assignment->remarks ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{__('No past assignments found')}}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> {{__('Back to Assets')}}
            </a>
        </div>
    </div>
</div>
@endsection
