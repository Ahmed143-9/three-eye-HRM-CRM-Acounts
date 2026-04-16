@extends('layouts.admin')
@section('page-title')
    {{__('Asset Management')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Assets')}}</li>
@endsection
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
    $assetImage=\App\Models\Utility::get_file('uploads/assets/');
@endphp

@section('action-btn')
    <div class="float-end">
        @can('manage assets')
            <a href="{{ route('asset-categories.index') }}" class="btn btn-sm btn-success me-2">
                <i class="ti ti-category"></i> {{__('Manage Categories')}}
            </a>
        @endcan
        @can('create assets')
            <a href="{{ route('account-assets.create') }}" class="btn btn-sm btn-primary me-2">
                <i class="ti ti-plus"></i> {{__('Add Asset')}}
            </a>
        @endcan
        @can('manage assets')
            <a href="{{ route('account-assets.requests') }}" class="btn btn-sm btn-info">
                <i class="ti ti-clipboard-list"></i> {{__('Asset Requests')}}
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{__('Total Assets')}}</h6>
                            <h2 class="mt-2 mb-0">{{ $totalAssets }}</h2>
                        </div>
                        <i class="ti ti-package fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{__('Available')}}</h6>
                            <h2 class="mt-2 mb-0">{{ $availableAssets }}</h2>
                        </div>
                        <i class="ti ti-circle-check fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{__('Assigned')}}</h6>
                            <h2 class="mt-2 mb-0">{{ $assignedAssets }}</h2>
                        </div>
                        <i class="ti ti-hand-grabbing fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{__('Maintenance')}}</h6>
                            <h2 class="mt-2 mb-0">{{ $maintenanceAssets }}</h2>
                        </div>
                        <i class="ti ti-settings fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{__('All Assets')}}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Asset Code')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Condition')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Purchase Date')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Assigned To')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($assets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->asset_code }}</strong></td>
                                    <td>
                                        @if($asset->image)
                                            <img src="{{ $assetImage . $asset->image }}" alt="{{ $asset->name }}" class="rounded me-2" width="40" height="40">
                                        @endif
                                        {{ $asset->name }}
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $asset->category }}</span></td>
                                    <td>{{ $asset->condition }}</td>
                                    <td>
                                        <span class="badge bg-{{ $asset->status_color }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td>{{ \Auth::user()->dateFormat($asset->purchase_date) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($asset->amount) }}</td>
                                    <td>
                                        @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                            <a href="{{ route('account-assets.employee-assets', $asset->currentAssignment->employee->id) }}">
                                                {{ $asset->currentAssignment->employee->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @can('manage assets')
                                                <a href="{{ route('account-assets.show', $asset->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{__('View')}}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            @endcan
                                            
                                            @if($asset->isAvailable())
                                                @can('assign assets')
                                                    <a href="{{ route('account-assets.assign-form', $asset->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="{{__('Assign')}}">
                                                        <i class="ti ti-user-plus"></i>
                                                    </a>
                                                @endcan
                                            @else
                                                @can('return assets')
                                                    <a href="{{ route('account-assets.return-form', $asset->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{__('Return')}}">
                                                        <i class="ti ti-user-minus"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            
                                            @can('manage assets')
                                                <a href="{{ route('account-assets.history', $asset->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('History')}}">
                                                    <i class="ti ti-clock-history"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('edit assets')
                                                <a href="{{ route('account-assets.edit', $asset->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('delete assets')
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['account-assets.destroy', $asset->id],'id'=>'delete-form-'.$asset->id, 'style'=>'display:inline']) !!}
                                                <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$asset->id}}').submit();">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            @endcan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
