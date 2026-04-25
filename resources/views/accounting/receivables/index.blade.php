@extends('layouts.admin')
@php
    $settings = Utility::settings();
@endphp
@section('page-title', __('Manage Receivables'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Receivable') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('receivables.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
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
                            @foreach($receivables as $receivable)
                                <tr>
                                    <td>{{ $receivable->unique_id }}</td>
                                    <td>{{ $receivable->invoice_number }}</td>
                                    <td>{{ Utility::getDateFormated($receivable->date) }}</td>
                                    <td>{{ $receivable->billing_direction }}</td>
                                    <td>{{ Utility::priceFormat($settings, $receivable->total_amount) }}</td>
                                    <td class="Action">
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('receivables.edit', $receivable->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['receivables.destroy', $receivable->id], 'id' => 'delete-form-' . $receivable->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="delete-form-{{ $receivable->id }}">
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
