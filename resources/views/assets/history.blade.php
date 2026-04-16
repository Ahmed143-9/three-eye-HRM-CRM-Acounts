@extends('layouts.admin')
@section('page-title')
    {{__('Asset History')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{ $asset->name }}</li>
    <li class="breadcrumb-item">{{__('History')}}</li>
@endsection
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Asset Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Asset Details')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>{{__('Asset Code:')}}</strong><br>
                        <span class="badge bg-primary">{{ $asset->asset_code }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>{{__('Name:')}}</strong><br>
                        {{ $asset->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>{{__('Category:')}}</strong><br>
                        <span class="badge bg-secondary">{{ $asset->category }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>{{__('Status:')}}</strong><br>
                        <span class="badge bg-{{ $asset->status_color }}">{{ $asset->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Assignment History')}}</h5>
            </div>
            <div class="card-body">
                @if($history->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__('Employee')}}</th>
                                    <th>{{__('Assign Date')}}</th>
                                    <th>{{__('Return Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Assigned By')}}</th>
                                    <th>{{__('Remarks')}}</th>
                                    <th>{{__('Document')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $record)
                                    <tr>
                                        <td>
                                            @if($record->employee)
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $profile . ($record->employee->user && $record->employee->user->avatar ? $record->employee->user->avatar : 'avatar.png') }}" 
                                                         alt="" class="avatar avatar-sm rounded-circle me-2">
                                                    {{ $record->employee->name }}
                                                </div>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($record->assign_date) }}</td>
                                        <td>
                                            @if($record->return_date)
                                                {{ \Auth::user()->dateFormat($record->return_date) }}
                                            @else
                                                <span class="badge bg-info">{{__('Currently Assigned')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $record->status_color }}">{{ $record->status }}</span>
                                        </td>
                                        <td>{{ $record->assignedBy ? $record->assignedBy->name : '-' }}</td>
                                        <td>{{ $record->remarks ?: '-' }}</td>
                                        <td>
                                            @if($record->document)
                                                <a href="{{ asset('uploads/asset_documents/' . $record->document) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="ti ti-download"></i> {{__('View')}}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">{{__('No assignment history found')}}</h5>
                        <p class="text-muted">This asset has not been assigned to any employee yet.</p>
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
