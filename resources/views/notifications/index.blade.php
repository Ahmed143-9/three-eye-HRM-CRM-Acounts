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
                            <div class="list-group-item list-group-item-action @if(!$notification->is_read) bg-light-primary @endif border-0 mb-2 rounded shadow-sm">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon me-3 mt-1">
                                        @php
                                            $typeColors = [
                                                'expense_submitted' => 'bg-warning',
                                                'expense_approved' => 'bg-success',
                                                'expense_rejected' => 'bg-danger',
                                                'expense_on_hold' => 'bg-secondary',
                                                'expense_sent_back' => 'bg-warning',
                                                'expense_payment_ready' => 'bg-info',
                                                'expense_paid' => 'bg-success',
                                                'salary_sheet_submitted' => 'bg-warning',
                                                'salary_approved' => 'bg-success',
                                                'salary_rejected' => 'bg-danger',
                                                'transport_created' => 'bg-info',
                                                'sales_order_finalized' => 'bg-primary',
                                            ];
                                            $icon_class = $typeColors[$notification->type] ?? 'bg-primary';
                                            
                                            $icon = 'ti ti-bell';
                                            if($notification->related_model == 'ErpExpense') $icon = 'ti ti-report-money';
                                            elseif($notification->related_model == 'ErpSalarySheet') $icon = 'ti ti-cash';
                                            elseif($notification->related_model == 'Transport') $icon = 'ti ti-truck-delivery';
                                            elseif($notification->related_model == 'SalesOrder') $icon = 'ti ti-shopping-cart';
                                        @endphp
                                        <span class="avatar {{ $icon_class }} text-white rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                            <i class="{{ $icon }} fs-4"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1">{!! __($notification->title) !!}</h6>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="text-sm mb-2 text-muted">{{ __($notification->message) }}</p>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" class="btn btn-xs btn-outline-primary text-xs py-1 px-2">
                                                <i class="ti ti-eye"></i> {{ __('View Details') }}
                                            </a>
                                            @if(!$notification->is_read)
                                                <button type="button" class="btn btn-xs btn-link text-muted text-xs p-0 ms-2 mark-read-btn" data-id="{{ $notification->id }}">
                                                    {{ __('Mark as Read') }}
                                                </button>
                                            @endif
                                        </div>
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
