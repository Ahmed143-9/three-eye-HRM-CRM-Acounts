@extends('layouts.admin')
@section('page-title')
    {{ __('Expense Management') }} - {{ ucfirst($type) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expense Management') }}</li>
    <li class="breadcrumb-item">{{ ucfirst($type) }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @if(Gate::check('create expense') || \Auth::user()->type == 'company')
            <a href="#" data-url="{{ route('erp-expenses.create', $type) }}" data-ajax-popup="true" data-size="xl" data-title="{{ __('Create New Expense') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Serial No') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->serial_no }}</td>
                                        <td>{{ !empty($expense->category) ? $expense->category->name : '-' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($expense->date) }}</td>
                                        <td>{{ !empty($expense->employee) ? $expense->employee->name : '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                         <td>
                                            @if($expense->status == 'Pending Approval')
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Approved')
                                                <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Paid')
                                                <span class="status_badge badge bg-success p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Hold')
                                                <span class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Rejected')
                                                <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @else
                                                <span class="status_badge badge bg-info p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @endif
                                         </td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('erp-expenses.show', [$type, $expense->id]) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('View') }}" data-title="{{ __('Expense Detail') }}">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>

                                                @if($expense->status != 'Approved' && $expense->status != 'Paid')
                                                    @if(Gate::check('edit expense') || \Auth::user()->type == 'company')
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('erp-expenses.edit', [$type, $expense->id]) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Expense') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    @if(Gate::check('delete expense') || \Auth::user()->type == 'company')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['erp-expenses.destroy', $type, $expense->id], 'id' => 'delete-form-' . $expense->id]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i class="ti ti-trash text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('erp-expenses.print', [$type, $expense->id]) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('Print') }}" target="_blank">
                                                        <i class="ti ti-printer text-white"></i>
                                                    </a>
                                                </div>
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
