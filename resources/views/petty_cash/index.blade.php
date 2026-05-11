@extends('layouts.admin')

@section('page-title')
    {{ __('Petty Cash Allocations') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Petty Cash') }}</li>
@endsection

@section('action-btn')
    @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin')
        <div class="float-end">
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#allocateModal" title="{{ __('Allocate Petty Cash') }}">
                <i class="ti ti-plus"></i> {{ __('Allocate Fund') }}
            </a>
        </div>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{ __('Month') }}</th>
                                <th>{{ __('Allocated Amount') }}</th>
                                <th>{{ __('Rollover Amount') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Used Amount') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Allocated By') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allocations as $allocation)
                                <tr>
                                    <td>{{ date('F Y', strtotime($allocation->month . '-01')) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($allocation->allocated_amount) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($allocation->rollover_amount) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($allocation->total_amount) }}</td>
                                    <td class="text-danger">{{ \Auth::user()->priceFormat($allocation->used_amount) }}</td>
                                    <td class="text-success">{{ \Auth::user()->priceFormat($allocation->total_amount - $allocation->used_amount) }}</td>
                                    <td>{{ !empty($allocation->admin) ? $allocation->admin->name : '-' }}</td>
                                    <td>
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('petty-cash.show', $allocation->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                                <i class="ti ti-eye text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn bg-success ms-2">
                                            <a href="{{ route('petty-cash.pdf', $allocation->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('Download Report') }}">
                                                <i class="ti ti-download text-white"></i>
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

    <!-- Allocate Modal -->
    <div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allocateModalLabel">{{ __('Allocate Petty Cash') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ Form::open(['route' => ['petty-cash.store'], 'method' => 'post']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('month', __('Month'),['class'=>'form-label']) }}
                                <input type="month" name="month" class="form-control" required value="{{ date('Y-m') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('allocated_amount', __('Amount'),['class'=>'form-label']) }}
                                {{ Form::number('allocated_amount', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    {{ Form::submit(__('Allocate'), ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
