@extends('layouts.admin')

@section('page-title')
    {{ __('Petty Cash Details') }} - {{ date('F Y', strtotime($allocation->month . '-01')) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('petty-cash.index') }}">{{ __('Petty Cash') }}</a></li>
    <li class="breadcrumb-item">{{ __('Details') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('petty-cash.pdf', $allocation->id) }}" class="btn btn-sm btn-success" title="{{ __('Download PDF Report') }}">
            <i class="ti ti-download"></i> {{ __('Download Report') }}
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUsageModal" title="{{ __('Add Usage') }}">
            <i class="ti ti-plus"></i> {{ __('Add Usage') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Total Fund') }}</h6>
                    <h4 class="text-primary">{{ \Auth::user()->priceFormat($allocation->total_amount) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Used Amount') }}</h6>
                    <h4 class="text-danger">{{ \Auth::user()->priceFormat($allocation->used_amount) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Balance') }}</h6>
                    <h4 class="text-success">{{ \Auth::user()->priceFormat($allocation->total_amount - $allocation->used_amount) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Rollover from Prev. Month') }}</h6>
                    <h4 class="text-info">{{ \Auth::user()->priceFormat($allocation->rollover_amount) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Usage History') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Purpose') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Used By') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allocation->usages as $usage)
                                <tr>
                                    <td>{{ \Auth::user()->dateFormat($usage->date) }}</td>
                                    <td>{{ $usage->purpose }}</td>
                                    <td>{{ \Auth::user()->priceFormat($usage->amount) }}</td>
                                    <td>{{ !empty($usage->user) ? $usage->user->name : '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Usage Modal -->
    <div class="modal fade" id="addUsageModal" tabindex="-1" aria-labelledby="addUsageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUsageModalLabel">{{ __('Add Petty Cash Usage') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ Form::open(['route' => ['petty-cash.store-usage', $allocation->id], 'method' => 'post']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
                                {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
                                {{ Form::number('amount', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('purpose', __('Purpose'),['class'=>'form-label']) }}
                                {{ Form::textarea('purpose', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 3]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
