@extends('layouts.admin')
@php
    $settings = Utility::settings();
@endphp
@section('page-title', __('Manage Payables'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Payable') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('payables.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
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
                                <th>{{ __('Unique ID') }}</th>
                                <th>{{ __('Invoice Number') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Billing Direction') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payables as $payable)
                                <tr>
                                    <td>{{ $payable->unique_id }}</td>
                                    <td>{{ $payable->invoice_number }}</td>
                                    <td>{{ Utility::getDateFormated($payable->date) }}</td>
                                    <td>{{ $payable->billing_direction }}</td>
                                    <td>{{ Utility::priceFormat($settings, $payable->total_amount) }}</td>
                                    <td class="Action">
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('payables.edit', $payable->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['payables.destroy', $payable->id], 'id' => 'delete-form-' . $payable->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="delete-form-{{ $payable->id }}">
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
