@extends('layouts.admin')
@section('page-title')
    {{__('Asset Details')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{ $asset->name }}</li>
@endsection
@php
    $assetImage=\App\Models\Utility::get_file('uploads/assets/');
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('action-btn')
    <div class="float-end">
        @if($asset->isAvailable())
            @can('assign assets')
                <a href="{{ route('account-assets.assign-form', $asset->id) }}" class="btn btn-sm btn-success">
                    <i class="ti ti-user-plus"></i> {{__('Assign Asset')}}
                </a>
            @endcan
        @else
            @can('return assets')
                <a href="{{ route('account-assets.return-form', $asset->id) }}" class="btn btn-sm btn-warning">
                    <i class="ti ti-user-minus"></i> {{__('Return Asset')}}
                </a>
            @endcan
        @endif
        @can('edit assets')
            <a href="{{ route('account-assets.edit', $asset->id) }}" class="btn btn-sm btn-info">
                <i class="ti ti-pencil"></i> {{__('Edit')}}
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Asset Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Asset Information')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        @if($asset->image)
                            <img src="{{ $assetImage . $asset->image }}" alt="{{ $asset->name }}" class="img-fluid rounded" style="max-height: 300px;">
                        @else
                            <div class="bg-light rounded p-5">
                                <i class="ti ti-package fa-5x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">{{__('Asset Code')}}</th>
                                <td><span class="badge bg-primary">{{ $asset->asset_code }}</span></td>
                            </tr>
                            <tr>
                                <th>{{__('Name')}}</th>
                                <td><strong>{{ $asset->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>{{__('Category')}}</th>
                                <td><span class="badge bg-secondary">{{ $asset->category }}</span></td>
                            </tr>
                            <tr>
                                <th>{{__('Condition')}}</th>
                                <td>{{ $asset->condition }}</td>
                            </tr>
                            <tr>
                                <th>{{__('Status')}}</th>
                                <td><span class="badge bg-{{ $asset->status_color }}">{{ $asset->status }}</span></td>
                            </tr>
                            <tr>
                                <th>{{__('Purchase Date')}}</th>
                                <td>{{ \Auth::user()->dateFormat($asset->purchase_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{__('Purchase Amount')}}</th>
                                <td>{{ \Auth::user()->priceFormat($asset->amount) }}</td>
                            </tr>
                            @if($asset->warranty_until)
                            <tr>
                                <th>{{__('Warranty Until')}}</th>
                                <td>{{ \Auth::user()->dateFormat($asset->warranty_until) }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($asset->manufacturer || $asset->model_number || $asset->serial_number || $asset->location)
                <hr>
                <h6 class="mb-3">{{__('Additional Details')}}</h6>
                <div class="row">
                    @if($asset->manufacturer)
                    <div class="col-md-3">
                        <strong>{{__('Manufacturer:')}}</strong><br>
                        {{ $asset->manufacturer }}
                    </div>
                    @endif
                    @if($asset->model_number)
                    <div class="col-md-3">
                        <strong>{{__('Model Number:')}}</strong><br>
                        {{ $asset->model_number }}
                    </div>
                    @endif
                    @if($asset->serial_number)
                    <div class="col-md-3">
                        <strong>{{__('Serial Number:')}}</strong><br>
                        {{ $asset->serial_number }}
                    </div>
                    @endif
                    @if($asset->location)
                    <div class="col-md-3">
                        <strong>{{__('Location:')}}</strong><br>
                        {{ $asset->location }}
                    </div>
                    @endif
                </div>
                @endif

                @if($asset->description)
                <hr>
                <h6 class="mb-3">{{__('Description')}}</h6>
                <p>{{ $asset->description }}</p>
                @endif
            </div>
        </div>

        <!-- Current Assignment -->
        @if($asset->currentAssignment && $asset->currentAssignment->employee)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">{{__('Currently Assigned To')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{__('Employee:')}}</strong><br>
                        <a href="{{ route('account-assets.employee-assets', $asset->currentAssignment->employee->id) }}">
                            {{ $asset->currentAssignment->employee->name }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <strong>{{__('Assign Date:')}}</strong><br>
                        {{ \Auth::user()->dateFormat($asset->currentAssignment->assign_date) }}
                    </div>
                </div>
                @if($asset->currentAssignment->remarks)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>{{__('Remarks:')}}</strong><br>
                        {{ $asset->currentAssignment->remarks }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Assignment History -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{__('Assignment History')}}</h5>
                    <a href="{{ route('account-assets.history', $asset->id) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-clock-history"></i> {{__('View Full History')}}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($asset->employeeAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__('Employee')}}</th>
                                    <th>{{__('Assign Date')}}</th>
                                    <th>{{__('Return Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Remarks')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asset->employeeAssets->take(5) as $assignment)
                                    <tr>
                                        <td>
                                            @if($assignment->employee)
                                                {{ $assignment->employee->name }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($assignment->assign_date) }}</td>
                                        <td>
                                            @if($assignment->return_date)
                                                {{ \Auth::user()->dateFormat($assignment->return_date) }}
                                            @else
                                                <span class="badge bg-info">{{__('Current')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $assignment->status_color }}">{{ $assignment->status }}</span>
                                        </td>
                                        <td>{{ Str::limit($assignment->remarks, 50) ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{__('No assignment history found')}}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> {{__('Back to Assets List')}}
            </a>
        </div>
    </div>
</div>
@endsection
