@extends('layouts.admin')
@section('page-title')
    {{ __('Expense Bills') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Accounting') }}</li>
    <li class="breadcrumb-item">{{ __('Expense Bills') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['expense-bills.index'], 'method' => 'GET', 'id' => 'expense_bill_filter']) }}
                        <div class="row align-items-end justify-content-end">
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}
                                {{ Form::select('employee', $employees, request('employee'), ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                {{ Form::label('type', __('Expense Type'), ['class' => 'form-label']) }}
                                {{ Form::select('type', ['purchase' => __('Purchase'), 'convenience' => __('Convenience'), 'utility' => __('Utility'), 'salary' => __('Salary')], request('type'), ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                {{ Form::label('payment_status', __('Payment Status'), ['class' => 'form-label']) }}
                                {{ Form::select('payment_status', ['Sent To Accounts' => __('Sent To Accounts'), 'Processing Payment' => __('Processing Payment'), 'Paid' => __('Paid')], request('payment_status'), ['class' => 'form-control select', 'placeholder' => __('All')]) }}
                            </div>
                            <div class="col-auto">
                                <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('expense_bill_filter').submit(); return false;">
                                    <i class="ti ti-search"></i>
                                </a>
                                <a href="{{ route('expense-bills.index') }}" class="btn btn-sm btn-danger">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Bill Serial') }}</th>
                                    <th>{{ __('Expense Type') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Approval Status') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                    <th>{{ __('Approved By') }}</th>
                                    <th>{{ __('Approved Date') }}</th>
                                    <th>{{ __('Attachment') }}</th>
                                    <th>{{ __('Payment Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->serial_no }}</td>
                                        <td><span class="badge bg-primary">{{ ucfirst($expense->type) }}</span></td>
                                        <td>{{ optional($expense->employee)->name ?? '-' }}</td>
                                        <td>{{ optional($expense->department)->name ?? '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                        <td>
                                            <span class="badge {{ $expense->status === 'Paid' ? 'bg-success' : 'bg-info' }}">
                                                {{ __($expense->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $pStatus = $expense->payment_status ?: 'Sent To Accounts';
                                                $pClass = $pStatus === 'Paid' ? 'bg-success' : ($pStatus === 'Processing Payment' ? 'bg-warning' : 'bg-secondary');
                                            @endphp
                                            <span class="badge {{ $pClass }}">{{ __($pStatus) }}</span>
                                        </td>
                                        <td>{{ optional($expense->approver)->name ?? '-' }}</td>
                                        <td>{{ $expense->approved_at ? \Auth::user()->dateFormat($expense->approved_at) : '-' }}</td>
                                        <td>
                                            @if($expense->attachment)
                                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset($expense->attachment) }}">
                                                    <i class="ti ti-paperclip"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="d-flex gap-1">
                                            @if($expense->accounting_bill_id)
                                                <a href="{{ route('bill.show', \Crypt::encrypt($expense->accounting_bill_id)) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ __('Verify Bill') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if($expense->status !== 'Paid' && $expense->status !== 'Rejected by Accounts')
                                                    <a href="#" class="btn btn-sm btn-success mark-as-paid-btn" 
                                                       data-url="{{ route('erp-expenses.mark-as-paid', $expense->id) }}"
                                                       data-bs-toggle="tooltip" title="{{ __('Mark As Paid') }}">
                                                        <i class="ti ti-cash"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger accountant-reject-btn" 
                                                       data-url="{{ route('erp-expenses.accountant-reject', $expense->id) }}"
                                                       data-bs-toggle="tooltip" title="{{ __('Reject') }}">
                                                        <i class="ti ti-x"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            <a href="{{ route('erp-expenses.print', [$expense->type, $expense->id]) }}" class="btn btn-sm btn-info" target="_blank" data-bs-toggle="tooltip" title="{{ __('Print bill') }}">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">{{ __('No approved expense bills found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('click', '.mark-as-paid-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            
            var comments = prompt('Any payment notes? (Optional):');
            if (comments !== null) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        comments: comments
                    },
                    success: function(data) {
                        if (data.success) {
                            show_toastr('{{ __('Success') }}', data.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        }
                    },
                    error: function(data) {
                        show_toastr('{{ __('Error') }}', '{{ __('Something went wrong') }}', 'error');
                    }
                });
            }
        });

        $(document).on('click', '.accountant-reject-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            
            var comments = prompt('{{ __('Please enter rejection reason:') }}');
            if (comments) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        comments: comments
                    },
                    success: function(data) {
                        if (data.success) {
                            show_toastr('{{ __('Success') }}', data.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        }
                    },
                    error: function(data) {
                        show_toastr('{{ __('Error') }}', '{{ __('Something went wrong') }}', 'error');
                    }
                });
            }
        });
    </script>
@endpush

