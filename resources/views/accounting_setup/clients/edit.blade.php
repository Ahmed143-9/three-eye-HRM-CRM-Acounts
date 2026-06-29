@extends('layouts.admin')
@section('page-title', __('Edit Client'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('accounting-clients.index') }}">{{ __('Clients') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('accounting-clients.update', $client->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Unique ID') }}</label>
                            <input type="text" class="form-control" name="unique_id" value="{{ $client->unique_id }}" readonly required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Company Name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ $client->name }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('TIN No') }}</label>
                            <input type="text" class="form-control" name="tin_no" value="{{ $client->tin_no }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('BIN Number') }}</label>
                            <input type="text" class="form-control" name="bin_number" value="{{ $client->bin_number }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('IRC No') }}</label>
                            <input type="text" class="form-control" name="irc_no" value="{{ $client->irc_no }}">
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Contact Person Details') }}</h5>
                        
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="contact_person_name" value="{{ $client->contact_person_name }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Number') }}</label>
                            <input type="text" class="form-control" name="contact_person_number" value="{{ $client->contact_person_number }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="contact_person_email" value="{{ $client->contact_person_email }}">
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('Address Details') }}</h5>

                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Head Office Address') }}</label>
                            <textarea class="form-control" name="head_office_address" rows="2">{{ $client->head_office_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Factory Address') }}</label>
                            <textarea class="form-control" name="factory_address" rows="2">{{ $client->factory_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Billing Address') }}</label>
                            <textarea class="form-control" name="billing_address" rows="2">{{ $client->billing_address }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Delivery Address') }}</label>
                            <textarea class="form-control" name="delivery_address" rows="2">{{ $client->delivery_address }}</textarea>
                        </div>

                        <hr class="my-4">
                        
                        <div class="form-group col-md-12">
                            <h5 class="mb-3">{{ __('Bank Details') }}</h5>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Bank Name') }}</label>
                            <input type="text" class="form-control" name="bank_name" value="{{ $client->bank_name }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Bank Branch') }}</label>
                            <input type="text" class="form-control" name="bank_branch" value="{{ $client->bank_branch }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Account Number') }}</label>
                            <input type="text" class="form-control" name="bank_account_number" value="{{ $client->bank_account_number }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('File Attachment') }}</label>
                            <div id="file_attachment_container">
                                @php
                                    $files = $client->file_attachment ? json_decode($client->file_attachment, true) : [];
                                    if(!is_array($files)) $files = $client->file_attachment ? [$client->file_attachment] : [];
                                @endphp
                                @if(is_array($files) && count($files) > 0)
                                    @foreach($files as $file)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" value="{{ basename($file) }}" readonly>
                                            <a href="{{ \App\Models\Utility::get_file($file) }}" target="_blank" class="btn btn-info"><i class="ti ti-eye"></i></a>
                                            @if($loop->last)
                                                <button type="button" class="btn btn-primary add-file-input"><i class="ti ti-plus"></i></button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="file" class="form-control" name="file_attachment[]">
                                        <button type="button" class="btn btn-primary add-file-input"><i class="ti ti-plus"></i></button>
                                    </div>
                                @endif
                            </div>
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" {{ $client->is_active ? 'checked' : '' }}>
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

<script>
    $(document).on('click', '.add-file-input', function() {
        var html = '<div class="input-group mb-2"><input type="file" name="file_attachment[]" class="form-control"><button type="button" class="btn btn-danger remove-file-input"><i class="ti ti-trash"></i></button></div>';
        $('#file_attachment_container').append(html);
    });
    $(document).on('click', '.remove-file-input', function() {
        $(this).closest('.input-group').remove();
    });
</script>
@endsection