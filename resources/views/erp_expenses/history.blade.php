@extends('layouts.admin')
@section('page-title')
    {{ __('Office Expense History') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expense Management') }}</li>
    <li class="breadcrumb-item">{{ __('History') }}</li>
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
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Approved By') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="100px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->serial_no }}</td>
                                        <td>{{ ucfirst($expense->type) }}</td>
                                        <td>{{ !empty($expense->category) ? $expense->category->name : '-' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($expense->date) }}</td>
                                        <td>{{ !empty($expense->employee) ? $expense->employee->name : '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                        <td>{{ !empty($expense->approver) ? $expense->approver->name : '-' }}</td>
                                        <td>
                                            @if($expense->status == 'Pending Approval')
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Approved')
                                                <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @elseif($expense->status == 'Paid')
                                                <span class="status_badge badge bg-success p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @else
                                                <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <div class="action-btn bg-info ms-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('erp-expenses.show', [$expense->type, $expense->id]) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('View') }}" data-title="{{ __('Expense Detail') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
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
