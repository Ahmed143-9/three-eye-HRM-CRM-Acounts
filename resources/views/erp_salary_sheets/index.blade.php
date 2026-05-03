@extends('layouts.admin')
@section('page-title')
    {{ __('Salary Sheet Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expense Management') }}</li>
    <li class="breadcrumb-item">{{ __('Salary Sheet') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <form action="{{ route('salary-management.generate') }}" method="POST" class="d-inline">
            @csrf
            <div class="input-group">
                <input type="month" name="month" value="{{ date('Y-m') }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="ti ti-rotate"></i> {{ __('Generate Monthly Sheet') }}
                </button>
            </div>
        </form>
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
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Net Salary') }}</th>
                                    <th>{{ __('Deduction') }}</th>
                                    <th>{{ __('Final Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salarySheets as $sheet)
                                    <tr>
                                        <td>{{ !empty($sheet->employee) ? $sheet->employee->name : '-' }}</td>
                                        <td>{{ $sheet->month }}</td>
                                        <td>{{ \Auth::user()->priceFormat($sheet->net_salary) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($sheet->deduction_amount) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($sheet->final_salary) }}</td>
                                        <td>
                                            @if($sheet->approval_status == 'Pending')
                                                <span class="badge bg-warning p-2 px-3 rounded">{{ __($sheet->approval_status) }}</span>
                                            @else
                                                <span class="badge bg-success p-2 px-3 rounded">{{ __($sheet->approval_status) }}</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            @if($sheet->approval_status == 'Pending')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" 
                                                       data-url="{{ route('erp-expenses.create', ['type' => 'salary', 'sheet_id' => $sheet->id]) }}" 
                                                       data-ajax-popup="true" data-size="lg" data-title="{{ __('Convert to Expense') }}">
                                                        <i class="ti ti-file-export text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['salary-management.destroy', $sheet->id], 'id' => 'delete-form-' . $sheet->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
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
    </div>
@endsection
