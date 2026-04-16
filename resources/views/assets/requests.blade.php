@extends('layouts.admin')
@section('page-title')
    {{__('Asset Requests')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Requests')}}</li>
@endsection
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('account-assets.index') }}" class="btn btn-sm btn-secondary">
            <i class="ti ti-arrow-left"></i> {{__('Back to Assets')}}
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Asset Assignment Requests')}}</h5>
            </div>
            <div class="card-body table-border-style">
                @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{__('Employee')}}</th>
                                    <th>{{__('Asset')}}</th>
                                    <th>{{__('Request Date')}}</th>
                                    <th>{{__('Reason')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Approved By')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>
                                            @if($request->employee)
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $profile . ($request->employee->user && $request->employee->user->avatar ? $request->employee->user->avatar : 'avatar.png') }}" 
                                                         alt="" class="avatar avatar-sm rounded-circle me-2">
                                                    {{ $request->employee->name }}
                                                </div>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->asset)
                                                <strong>{{ $request->asset->asset_code }}</strong><br>
                                                <small>{{ $request->asset->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($request->requested_date) }}</td>
                                        <td>{{ Str::limit($request->reason, 50) ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_color }}">{{ $request->status }}</span>
                                        </td>
                                        <td>{{ $request->approvedBy ? $request->approvedBy->name : '-' }}</td>
                                        <td>
                                            @if($request->status === 'Pending')
                                                <div class="btn-group" role="group">
                                                    {!! Form::open(['route' => ['account-assets.approve-request', $request->id], 'method' => 'POST', 'style' => 'display:inline']) !!}
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                        <i class="ti ti-check"></i> {{__('Approve')}}
                                                    </button>
                                                    {!! Form::close() !!}
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                        <i class="ti ti-x"></i> {{__('Reject')}}
                                                    </button>
                                                </div>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            {!! Form::open(['route' => ['account-assets.reject-request', $request->id], 'method' => 'POST']) !!}
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{__('Reject Asset Request')}}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">{{__('Rejection Reason')}} <span class="text-danger">*</span></label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Please provide a reason for rejection"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
                                                                <button type="submit" class="btn btn-danger">{{__('Reject Request')}}</button>
                                                            </div>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">{{__('No asset requests found')}}</h5>
                        <p class="text-muted">There are no pending or processed asset requests.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
