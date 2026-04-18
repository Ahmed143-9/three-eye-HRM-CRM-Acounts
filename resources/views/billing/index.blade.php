@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Billing') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Billing') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="#" data-url="{{ route('billing.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Create New Billing') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-1 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-warning">
                                    <i class="ti ti-report-money"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('My Due to Client') }}</small>
                                    <h6 class="m-0">{{ \Auth::user()->priceFormat($dueToClientTotal) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-1 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-danger">
                                    <i class="ti ti-report-money"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Client Due to Me') }}</small>
                                    <h6 class="m-0">{{ \Auth::user()->priceFormat($dueToMeTotal) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-1 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-success">
                                    <i class="ti ti-check"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Paid to Client') }}</small>
                                    <h6 class="m-0">{{ \Auth::user()->priceFormat($myPaidTotal) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-1 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-info">
                                    <i class="ti ti-wallet"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Received from Client') }}</small>
                                    <h6 class="m-0">{{ \Auth::user()->priceFormat($clientPaidTotal) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Direction') }}</th>
                                    <th>{{ __('From (Sender)') }}</th>
                                    <th>{{ __('To (Receiver)') }}</th>
                                    <th>{{ __('Attachment') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($billings as $billing)
                                    <tr>
                                        <td>
                                            @if($billing->status == 'paid')
                                                <span class="badge bg-success p-2 px-3 rounded">{{ __('Paid') }}</span>
                                            @else
                                                <span class="badge bg-danger p-2 px-3 rounded">{{ __('Unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ \Auth::user()->priceFormat($billing->amount) }}</strong></td>
                                        <td>
                                            @if($billing->type == 'due_to_client')
                                                <span class="badge bg-warning p-2 px-3 rounded">{{ __('Payable') }}</span>
                                            @else
                                                <span class="badge bg-primary p-2 px-3 rounded">{{ __('Receivable') }}</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($billing->from_date)
                                                <div class="text-sm text-muted"><i class="ti ti-calendar"></i> {{ \Auth::user()->dateFormat($billing->from_date) }}</div>
                                            @endif
                                            @if($billing->from_bank_name)
                                                <div class="text-sm"><strong>{{ $billing->from_bank_name }}</strong></div>
                                            @endif
                                            @if($billing->from_bank_number)
                                                <div class="text-sm">{{ $billing->from_bank_number }}</div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($billing->to_date)
                                                <div class="text-sm text-muted"><i class="ti ti-calendar"></i> {{ \Auth::user()->dateFormat($billing->to_date) }}</div>
                                            @endif
                                            @if($billing->to_bank_name)
                                                <div class="text-sm"><strong>{{ $billing->to_bank_name }}</strong></div>
                                            @endif
                                            @if($billing->to_bank_number)
                                                <div class="text-sm">{{ $billing->to_bank_number }}</div>
                                            @endif
                                            @if($billing->description)
                                                <div class="text-sm text-wrap text-muted mt-1" style="max-width: 250px;"><small>{{ \Illuminate\Support\Str::limit($billing->description, 50) }}</small></div>
                                            @endif
                                        </td>

                                        <td>
                                            @if(!empty($billing->attachment))
                                                <a href="{{ \App\Models\Utility::get_file($billing->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('View Attachment') }}">
                                                    <i class="ti ti-file text-primary"></i> {{ __('View') }}
                                                </a>
                                            @else
                                                <span class="text-muted text-sm">-</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <div class="action-btn bg-info ms-2">
                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('billing.edit', $billing->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Billing') }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['billing.destroy', $billing->id], 'id' => 'delete-form-' . $billing->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm bs-pass-para align-items-center" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-original-title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $billing->id }}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                {!! Form::close() !!}
                                            </div>
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
