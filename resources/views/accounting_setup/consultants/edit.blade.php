@extends('layouts.admin')
@section('page-title', __('Edit Consultant'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consultants.index') }}">{{ __('Consultants') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('consultants.update', $consultant->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Unique ID') }}</label>
                            <input type="text" class="form-control" name="unique_id" value="{{ $consultant->unique_id }}" readonly required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Consultant Name / Company Name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ $consultant->name }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('TIN No') }}</label>
                            <input type="text" class="form-control" name="tin_no" value="{{ $consultant->tin_no }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('BIN Number') }}</label>
                            <input type="text" class="form-control" name="bin_number" value="{{ $consultant->bin_number }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('IRC No') }}</label>
                            <input type="text" class="form-control" name="irc_no" value="{{ $consultant->irc_no }}">
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Contact Person Details') }}</h5>
                        
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="contact_person_name" value="{{ $consultant->contact_person_name }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Number') }}</label>
                            <input type="text" class="form-control" name="contact_person_number" value="{{ $consultant->contact_person_number }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="contact_person_email" value="{{ $consultant->contact_person_email }}">
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Address Details') }}</h5>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Head Office Address') }}</label>
                            <textarea class="form-control" name="head_office_address" rows="2">{{ $consultant->head_office_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Factory Address') }}</label>
                            <textarea class="form-control" name="factory_address" rows="2">{{ $consultant->factory_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Billing Address') }}</label>
                            <textarea class="form-control" name="billing_address" rows="2">{{ $consultant->billing_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Delivery Address') }}</label>
                            <textarea class="form-control" name="delivery_address" rows="2">{{ $consultant->delivery_address }}</textarea>
                        </div>

                        <hr class="my-4">
                        
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Bank Details') }}</label>
                            <textarea class="form-control" name="bank_details" rows="3">{{ $consultant->bank_details }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('File Attachment') }}</label>
                            <input type="file" class="form-control" name="file_attachment">
                            @if($consultant->file_attachment)
                                <a href="{{ Storage::url($consultant->file_attachment) }}" target="_blank" class="text-primary mt-2 d-block">View Current Attachment</a>
                            @endif
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" {{ $consultant->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection