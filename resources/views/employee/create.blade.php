@extends('layouts.admin')

@section('page-title')
    {{ __('Create Employee') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ url('employee') }}">{{ __('Employee') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create Employee') }}</li>
@endsection


@section('content')
<div class="row">
    {{ Form::open(['route' => ['employee.store'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class'=>'needs-validation', 'novalidate']) }}
    <div class="">
        <div class="">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Personal Detail') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'required' => 'required' ,'placeholder'=>__('Enter employee name')]) !!}
                                </div>
                                <div class="col-md-6">
                                    <x-mobile label="{{__('Phone')}}" name="phone" value="{{old('phone')}}" required placeholder="Enter employee phone"></x-mobile>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {{ Form::date('dob', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off','placeholder'=>'Select Date of Birth', 'max' => date('Y-m-d')]) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label' , 'required' => 'required' ]) !!}<x-required></x-required>
                                        <div class="d-flex radio-check">
                                            <div class="form-check form-check-inline form-group">
                                                <input type="radio" id="g_male" value="Male" name="gender"
                                                    class="form-check-input" checked>
                                                <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                            </div>
                                            <div class="form-check form-check-inline form-group">
                                                <input type="radio" id="g_female" value="Female" name="gender"
                                                    class="form-check-input" >
                                                <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'required' => 'required' ,'placeholder'=>'Enter employee email']) !!}
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                {!! Form::textarea('address', old('address'), ['class' => 'form-control', 'rows' => 2 ,'placeholder'=>__('Enter employee address') , 'required' => 'required']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Company Detail') }}</h5>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                @csrf
                                <div class="form-group ">
                                    {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                    {!! Form::text('employee_id', $employeesId, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::select('branch_id', $branches, null, ['class' => 'form-control ', 'id' => 'branch_id', 'placeholder' => 'Select Branch','required' => 'required']) }}
                                        <div class="text-xs mt-1">
                                            {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('department_id', __('Select Department'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::select('department_id', $departments, null, ['class' => 'form-control ', 'id' => 'department_id' , 'placeholder' => 'Select Department','required' => 'required']) }}
                                        <div class="text-xs mt-1">
                                            {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}<x-required></x-required>

                                    <div class="form-icon-user">
                                        {{ Form::select('designation_id', $designations, null, ['class' => 'form-control ', 'id' => 'designation_id' , 'placeholder' => 'Select Designation','required' => 'required']) }}
                                        <div class="text-xs mt-1">
                                            {{ __('Create designation here.') }} <a href="{{ route('designation.index') }}"><b>{{ __('Create designation') }}</b></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('company_doj', __('Company Date Of Joining'), ['class' => '  form-label']) !!}<x-required></x-required>
                                    {{ Form::date('company_doj', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off' ,'placeholder'=>'Select company date of joining']) }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card em-card w-100">
                        <div class="card-header">
                            <h5>{{ __('Document') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            @foreach ($documents as $key => $document)
                                <div class="row">
                                    <div class="form-group col-12 d-flex">
                                        <div class="float-left col-4">
                                            <label for="document"
                                                class="float-left form-label">{{ $document->name }}
                                                @if ($document->is_required == 1)
                                                    <x-required></x-required>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                                value="{{ $document->id }}">
                                            <div class="choose-files">
                                                <label for="document[{{ $document->id }}]">
                                                    <div class=" bg-primary document "> <i
                                                            class="ti ti-upload "></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file"
                                                        class="form-control file file-validate d-none @error('document') is-invalid @enderror"
                                                        @if ($document->is_required == 1) required @endif
                                                        name="document[{{ $document->id }}]" id="document[{{ $document->id }}]"
                                                        data-filename="{{ $document->id . '_filename' }}" onchange="document.getElementById('{{'blah'.$key}}').src = window.URL.createObjectURL(this.files[0])">
                                                    <p id="" class="file-error text-danger"></p>
                                                </label>
                                                <img id="{{'blah'.$key}}" src=""  width="50%" />

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Bank Account Detail') }}</h5>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                    {!! Form::text('account_holder_name', old('account_holder_name'), ['class' => 'form-control' ,'placeholder'=>__('Enter account holder name')]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                    {!! Form::number('account_number', old('account_number'), ['class' => 'form-control' ,'placeholder'=>__('Enter account number')]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                    {!! Form::text('bank_name', old('bank_name'), ['class' => 'form-control'  ,'placeholder'=>__('Enter bank name')]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                                    {!! Form::text('bank_identifier_code', old('bank_identifier_code'), ['class' => 'form-control' ,'placeholder'=>__('Enter bank identifier code')]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                    {!! Form::text('branch_location', old('branch_location'), ['class' => 'form-control' ,'placeholder'=>__('Enter branch location')]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                                    {!! Form::text('tax_payer_id', old('tax_payer_id'), ['class' => 'form-control' ,'placeholder'=>__('Enter tax payer id')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Image, Social Media & Employment Details Section -->
            <div class="row mt-3">
                <div class="col-md-6 d-flex">
                    <div class="card em-card w-100">
                        <div class="card-header">
                            <h5>{{ __('Profile Image') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                {!! Form::label('profile_image', __('Employee Photo'), ['class' => 'form-label']) !!}
                                <div class="choose-files">
                                    <label for="profile_image">
                                        <div class="bg-primary document">
                                            <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                        </div>
                                        <input type="file" class="form-control file d-none" name="profile_image" id="profile_image"
                                            accept=".jpg,.jpeg,.png"
                                            onchange="document.getElementById('profile_image_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('profile_image_preview').style.display='block';">
                                    </label>
                                    <img id="profile_image_preview" src="" style="display:none; max-width:150px; margin-top:8px;" />
                                </div>
                                <small class="text-muted">{{ __('Accepted: jpg, jpeg, png. Max: 2MB') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card em-card w-100">
                        <div class="card-header">
                            <h5>{{ __('Social Media Links') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('facebook', __('Facebook'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-facebook"></i></span>
                                        {!! Form::url('facebook', old('facebook'), ['class' => 'form-control', 'placeholder' => 'https://facebook.com/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('linkedin', __('LinkedIn'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-linkedin"></i></span>
                                        {!! Form::url('linkedin', old('linkedin'), ['class' => 'form-control', 'placeholder' => 'https://linkedin.com/in/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('twitter', __('Twitter / X'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-twitter"></i></span>
                                        {!! Form::url('twitter', old('twitter'), ['class' => 'form-control', 'placeholder' => 'https://twitter.com/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('instagram', __('Instagram'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-instagram"></i></span>
                                        {!! Form::url('instagram', old('instagram'), ['class' => 'form-control', 'placeholder' => 'https://instagram.com/...']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Employment Period Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('probation_period', __('Probation Period (Days)'), ['class' => 'form-label']) !!}
                                    {!! Form::number('probation_period', old('probation_period'), ['class' => 'form-control', 'placeholder' => __('e.g. 90'), 'min' => 0]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('notice_period', __('Notice Period (Days)'), ['class' => 'form-label']) !!}
                                    {!! Form::number('notice_period', old('notice_period'), ['class' => 'form-control', 'placeholder' => __('e.g. 30'), 'min' => 0]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts Section -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Emergency Contacts') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Emergency Contact 1 (Required) -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6>{{ __('Emergency Contact 1') }} <x-required></x-required></h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][full_name]', __('Full Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][full_name]', old('emergency_contacts.0.full_name'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.full_name') ? ' is-invalid' : ''), 'placeholder' => __('Enter full name'), 'required']) !!}
                                                @error('emergency_contacts.0.full_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][relationship]', __('Relationship'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][relationship]', old('emergency_contacts.0.relationship'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.relationship') ? ' is-invalid' : ''), 'placeholder' => __('Enter relationship'), 'required']) !!}
                                                @error('emergency_contacts.0.relationship')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][contact_number]', __('Contact Number'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][contact_number]', old('emergency_contacts.0.contact_number'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.contact_number') ? ' is-invalid' : ''), 'placeholder' => __('Enter contact number'), 'required']) !!}
                                                @error('emergency_contacts.0.contact_number')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][email]', __('Email Address'), ['class' => 'form-label']) !!}
                                                {!! Form::email('emergency_contacts[0][email]', old('emergency_contacts.0.email'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.email') ? ' is-invalid' : ''), 'placeholder' => __('Enter email address')]) !!}
                                                @error('emergency_contacts.0.email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][address]', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::textarea('emergency_contacts[0][address]', old('emergency_contacts.0.address'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.address') ? ' is-invalid' : ''), 'rows' => 2, 'placeholder' => __('Enter address'), 'required']) !!}
                                                @error('emergency_contacts.0.address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group mb-0">
                                                {!! Form::label('emergency_contacts[0][nid][]', __('NID Files (Image/PDF)'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                <div id="nid-files-container-0">
                                                    <div class="choose-files mb-2">
                                                        <label for="emergency_contacts_0_nid_0">
                                                            <div class="bg-primary document">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"
                                                                class="form-control file file-validate d-none @error('emergency_contacts.0.nid') is-invalid @enderror"
                                                                name="emergency_contacts[0][nid][]" id="emergency_contacts_0_nid_0"
                                                                accept=".jpg,.jpeg,.png,.pdf">
                                                            <p class="file-error text-danger"></p>
                                                        </label>
                                                        <span class="file-name-display ms-2"></span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-file-btn" style="display:none;" onclick="removeFileInput(this)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFileInput(0)">
                                                    <i class="ti ti-plus"></i> {{ __('Add Another File') }}
                                                </button>
                                                @error('emergency_contacts.0.nid.*')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact 2 (Optional) -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6>{{ __('Emergency Contact 2') }} <small class="text-muted">({{ __('Optional') }})</small></h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][full_name]', __('Full Name'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][full_name]', old('emergency_contacts.1.full_name'), ['class' => 'form-control' . ($errors->has('emergency_contacts.1.full_name') ? ' is-invalid' : ''), 'placeholder' => __('Enter full name')]) !!}
                                                @error('emergency_contacts.1.full_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][relationship]', __('Relationship'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][relationship]', old('emergency_contacts.1.relationship'), ['class' => 'form-control' . ($errors->has('emergency_contacts.1.relationship') ? ' is-invalid' : ''), 'placeholder' => __('Enter relationship')]) !!}
                                                @error('emergency_contacts.1.relationship')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][contact_number]', __('Contact Number'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][contact_number]', old('emergency_contacts.1.contact_number'), ['class' => 'form-control' . ($errors->has('emergency_contacts.1.contact_number') ? ' is-invalid' : ''), 'placeholder' => __('Enter contact number')]) !!}
                                                @error('emergency_contacts.1.contact_number')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][email]', __('Email Address'), ['class' => 'form-label']) !!}
                                                {!! Form::email('emergency_contacts[1][email]', old('emergency_contacts.1.email'), ['class' => 'form-control' . ($errors->has('emergency_contacts.1.email') ? ' is-invalid' : ''), 'placeholder' => __('Enter email address')]) !!}
                                                @error('emergency_contacts.1.email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][address]', __('Address'), ['class' => 'form-label']) !!}
                                                {!! Form::textarea('emergency_contacts[1][address]', old('emergency_contacts.1.address'), ['class' => 'form-control' . ($errors->has('emergency_contacts.1.address') ? ' is-invalid' : ''), 'rows' => 2, 'placeholder' => __('Enter address')]) !!}
                                                @error('emergency_contacts.1.address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group mb-0">
                                                {!! Form::label('emergency_contacts[1][nid][]', __('NID Files (Image/PDF)'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                <div id="nid-files-container-1">
                                                    <div class="choose-files mb-2">
                                                        <label for="emergency_contacts_1_nid_0">
                                                            <div class="bg-primary document">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"
                                                                class="form-control file file-validate d-none @error('emergency_contacts.1.nid') is-invalid @enderror"
                                                                name="emergency_contacts[1][nid][]" id="emergency_contacts_1_nid_0"
                                                                accept=".jpg,.jpeg,.png,.pdf">
                                                            <p class="file-error text-danger"></p>
                                                        </label>
                                                        <span class="file-name-display ms-2"></span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-file-btn" style="display:none;" onclick="removeFileInput(this)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFileInput(1)">
                                                    <i class="ti ti-plus"></i> {{ __('Add Another File') }}
                                                </button>
                                                @error('emergency_contacts.1.nid.*')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="float-end">
            <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("employee.index")}}';" class="btn btn-secondary me-2">
            <button type="submit" class="btn  btn-primary">{{ 'Create' }}</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@endsection

@push('script-page')
    <script>
        // File input change handler
        $(document).on('change', 'input[type="file"]', function(e) {
            var file = e.target.files[0];
            if(file) {
                $(this).closest('.choose-files').find('.file-name-display').text(file.name);
                $(this).closest('.choose-files').find('.remove-file-btn').show();
            }
        });

        // Add new file input
        function addFileInput(contactIndex) {
            var container = $('#nid-files-container-' + contactIndex);
            var fileCount = container.find('.choose-files').length;
            var newInputId = 'emergency_contacts_' + contactIndex + '_nid_' + fileCount;
            
            var html = '<div class="choose-files mb-2">' +
                '<label for="' + newInputId + '">' +
                    '<div class="bg-primary document">' +
                        '<i class="ti ti-upload"></i>{{ __("Choose file here") }}' +
                    '</div>' +
                    '<input type="file" class="form-control file file-validate d-none" name="emergency_contacts[' + contactIndex + '][nid][]" id="' + newInputId + '" accept=".jpg,.jpeg,.png,.pdf">' +
                    '<p class="file-error text-danger"></p>' +
                '</label>' +
                '<span class="file-name-display ms-2"></span>' +
                '<button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-file-btn" style="display:none;" onclick="removeFileInput(this)">' +
                    '<i class="ti ti-trash"></i>' +
                '</button>' +
            '</div>';
            
            container.append(html);
        }

        // Remove file input
        function removeFileInput(btn) {
            var container = $(btn).closest('.choose-files');
            var allContainers = container.parent().find('.choose-files');
            
            // If there's only one input, just clear it
            if(allContainers.length === 1) {
                container.find('input[type="file"]').val('');
                container.find('.file-name-display').text('');
                $(btn).hide();
            } else {
                container.remove();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            var d_id = $('#department_id').val();
            getDepartment(b_id);
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=branch_id]', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDepartment(bid) {
            $.ajax({
                url: '{{ route('employee.getdepartment') }}',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append('<option value="">Select Department</option>');
                    $.each(data, function (key, value) {
                        $('#department_id').append('<option value="' + key + '"  >' + value + '</option>');
                    });
                }
            });
        }

        function getDesignation(did) {

            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append('<option value="">Select Designation</option>');
                    $.each(data, function (key, value) {
                        $('#designation_id').append('<option value="' + key + '"  >' + value + '</option>');
                    });
                }


            });
        }
    </script>
@endpush
