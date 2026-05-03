@extends('layouts.admin')
@section('page-title')
    {{ __('Expense Approval Queue') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expense Management') }}</li>
    <li class="breadcrumb-item">{{ __('Approval Queue') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['erp-expenses.index', 'approvals'], 'method' => 'GET', 'id' => 'approval_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}
                                            {{ Form::select('employee', $employees, isset($_GET['employee']) ? $_GET['employee'] : '', ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                            {{ Form::select('department', $departments, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                                            {{ Form::select('type', $types, isset($_GET['type']) ? $_GET['type'] : '', ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                            {{ Form::select('status', $statuses, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('date', isset($_GET['date']) ? $_GET['date'] : '', ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('approval_filter').submit(); return false;" data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('erp-expenses.index', 'approvals') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="approval-queue-content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('General Expenses Queue') }}</h5>
                </div>
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
                                    <th>{{ __('Status') }}</th>
                                    <th width="150px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr id="expense-row-{{ $expense->id }}">
                                        <td>{{ $expense->serial_no }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst($expense->type) }}</span>
                                        </td>
                                        <td>{{ !empty($expense->category) ? $expense->category->name : '-' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($expense->date) }}</td>
                                        <td>{{ !empty($expense->employee) ? $expense->employee->name : '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-warning';
                                                if($expense->status == 'Approved') $statusClass = 'bg-info';
                                                elseif($expense->status == 'Paid') $statusClass = 'bg-success';
                                                elseif($expense->status == 'Rejected') $statusClass = 'bg-danger';
                                                elseif($expense->status == 'Hold') $statusClass = 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }} p-2 px-3 rounded">{{ __($expense->status) }}</span>
                                        </td>
                                        <td class="Action">
                                            <div class="action-btn bg-info ms-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" 
                                                   id="view-btn-{{ $expense->id }}"
                                                   data-url="{{ route('erp-expenses.show', ['type' => $expense->type ?? 'purchase', 'id' => $expense->id ?? 0]) }}" 
                                                   data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" 
                                                   title="{{ __('View & Process') }}" data-title="{{ __('Expense Detail') }}">
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

        @if(count($salarySheets) > 0 || isset($_GET['status']))
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Salary Approvals Queue') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Net Salary') }}</th>
                                    <th>{{ __('Deduction') }}</th>
                                    <th>{{ __('Final Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="150px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salarySheets as $salary)
                                    <tr>
                                        <td>{{ $salary->employee->name }}</td>
                                        <td>{{ $salary->month }}</td>
                                        <td>{{ \Auth::user()->priceFormat($salary->net_salary) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($salary->deduction_amount) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($salary->final_salary) }}</td>
                                        <td>
                                            @php
                                                $sStatusClass = 'bg-warning';
                                                if($salary->approval_status == 'Approved') $sStatusClass = 'bg-success';
                                                elseif($salary->approval_status == 'Rejected') $sStatusClass = 'bg-danger';
                                            @endphp
                                            <span class="badge {{ $sStatusClass }} p-2 px-3 rounded">{{ __($salary->approval_status) }}</span>
                                        </td>
                                        <td class="Action">
                                            @if($salary->approval_status == 'Pending')
                                            <div class="action-btn bg-success ms-2">
                                                {!! Form::open(['method' => 'POST', 'route' => ['salary-management.approve', $salary->id], 'id' => 'approve-salary-form-' . $salary->id]) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Approve') }}" data-confirm="{{ __('Are you sure?') }}" data-text="{{ __('This will approve the salary sheet.') }}" data-confirm-yes="approve-salary-form-{{ $salary->id }}">
                                                    <i class="ti ti-check text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            // Auto-open logic for notifications
            const urlParams = new URLSearchParams(window.location.search);
            const openId = urlParams.get('open_id');
            if (openId) {
                setTimeout(function() {
                    $('#view-btn-' + openId).trigger('click');
                }, 500);
            }

            // Optional: Auto-refresh every 5 minutes instead of 1 minute to avoid interrupting filters
            setInterval(function() {
                if (!$('.modal.show').length && !urlParams.has('status')) {
                    // location.reload();
                }
            }, 300000);
        });
    </script>
@endpush
