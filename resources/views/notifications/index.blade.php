@extends('layouts.admin')
@section('page-title')
    {{ __('Notifications') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Notifications') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="event.preventDefault(); document.getElementById('mark-all-read-form-page').submit();">
            <i class="ti ti-checks"></i> {{ __('Mark All As Read') }}
        </a>
        <form id="mark-all-read-form-page" action="{{ route('notifications.markAllRead') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <div class="list-group-item list-group-item-action @if(!$notification->is_read) bg-light-primary @endif">
                                <div class="d-flex align-items-center">
                                    <div class="notification-icon me-3">
                                        @php
                                            $icon = 'ti ti-bell';
                                            $icon_class = 'bg-primary';
                                            if($notification->type == 'expense_submitted') { $icon = 'ti ti-report-money'; $icon_class = 'bg-warning'; }
                                            elseif($notification->type == 'expense_approved') { $icon = 'ti ti-check'; $icon_class = 'bg-success'; }
                                            elseif($notification->type == 'expense_rejected') { $icon = 'ti ti-x'; $icon_class = 'bg-danger'; }
                                        @endphp
                                        <span class="avatar {{ $icon_class }} text-white rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="{{ $icon }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1 text-sm">{!! __($notification->title) !!}</h6>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="text-sm mb-0 text-muted">{{ __($notification->message) }}</p>
                                        
                                        @if($notification->related_model == 'ErpExpense')
                                            <div class="mt-2">
                                                <a href="{{ route('erp-expenses.index', 'approvals') }}" class="btn btn-xs btn-primary text-xs py-1 px-2">{{ __('Go to Approvals') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4">
                                <i class="ti ti-notification-off fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">{{ __('No notifications yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
