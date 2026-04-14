@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Employee') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('Employee') }}</a></li>
    <li class="breadcrumb-item">{{ $employeesId }}</li>
@endsection


@section('content')
    <div class="row">
        {{ Form::model($employee, ['route' => ['employee.update', $employee->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
        <div class="row">
            <div class="col-md-6 ">
                <div class="card emp_details">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                {!! Form::text('name', null, [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => __('Enter employee name'),
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <x-mobile label="{{ __('Phone') }}" name="phone" value="{{ $employee->phone }}" required
                                    placeholdeer="Enter employee phone"></x-mobile>

                            </div>
                            <div class="form-group col-md-6">

                                {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                {!! Form::date('dob', null, ['class' => 'form-control', 'required' => 'required', 'max' => date('Y-m-d')]) !!}

                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                <div class="d-flex radio-check">
                                    <div class="form-check form-check-inline form-group">
                                        <input type="radio" id="g_male" value="Male" name="gender"
                                            class="form-check-input" {{ $employee->gender == 'Male' ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                    </div>
                                    <div class="form-check form-check-inline form-group">
                                        <input type="radio" id="g_female" value="Female" name="gender"
                                            class="form-check-input" {{ $employee->gender == 'Female' ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                            {!! Form::textarea('address', null, [
                                'class' => 'form-control',
                                'rows' => 2,
                                'required' => 'required',
                                'placeholder' => __('Enter employee address'),
                            ]) !!}
                        </div>
                        @if (\Auth::user()->type == 'employee')
                            {!! Form::submit('Update', ['class' => 'btn-create btn-xs badge-blue radius-10px float-right']) !!}
                        @endif
                    </div>
                </div>
            </div>
            @if (\Auth::user()->type != 'Employee')
                <div class="col-md-6 d-flex">
                    <div class="card emp_details">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                @csrf
                                <div class="form-group col-md-12">
                                    {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                    {!! Form::text('employee_id', $employeesId, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('branch_id', $branches, $employee->branch_id, ['class' => 'form-control select', 'required' => 'required', 'id' => 'branch_id']) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create branch here.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Create branch') }}</b></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('department_id', $departments, null, ['class' => 'form-control select', 'required' => 'required', 'id' => 'department_id']) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create department here.') }} <a href="{{ route('department.index') }}"><b>{{ __('Create department') }}</b></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('designation_id', __('Designation'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('designation_id', $designations, null, ['class' => 'form-control select', 'required' => 'required', 'id' => 'designation_id']) }}
                                    <div class="text-xs mt-1">
                                        {{ __('Create designation here.') }} <a href="{{ route('designation.index') }}"><b>{{ __('Create designation') }}</b></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('company_doj', 'Company Date Of Joining', ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::date('company_doj', null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-6 d-flex">
                    <div class="employee-detail-wrap ">
                        <div class="card emp_details">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                            </div>
                            <div class="card-body employee-detail-edit-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Branch') }}</strong>
                                            <span>{{ !empty($employee->branch) ? $employee->branch->name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info font-style">
                                            <strong>{{ __('Department') }}</strong>
                                            <span>{{ !empty($employee->department) ? $employee->department->name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info font-style">
                                            <strong>{{ __('Designation') }}</strong>
                                            <span>{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Date Of Joining') }}</strong>
                                            <span>{{ \Auth::user()->dateFormat($employee->company_doj) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if (\Auth::user()->type != 'Employee')
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card emp_details w-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Document') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            @php
                                $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
                            @endphp

                            @foreach ($documents as $key => $document)
                                <div class="row">
                                    <div class="form-group col-12">
                                        <div class="float-left col-4">
                                            <label for="document" class="float-left pt-1 form-label">{{ $document->name }}
                                                @if ($document->is_required == 1)
                                                    <x-required></x-required>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-4">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                                value="{{ $document->id }}">
                                            <div class="choose-file">
                                                <label for="document[{{ $document->id }}]">
                                                    <input
                                                        class="form-control file-validate @if (!empty($employeedoc[$document->id])) float-left @endif @error('document') is-invalid @enderror "
                                                        @if ($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                                                        name="document[{{ $document->id }}]"
                                                        onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])"
                                                        type="file" data-filename="{{ $document->id . '_filename' }}">
                                                    <p id="" class="file-error text-danger"></p>
                                                </label>
                                                <p class="{{ $document->id . '_filename' }}"></p>

                                                @php
                                                    $logo = \App\Models\Utility::get_file('uploads/document/');
                                                @endphp
                                                <div class="choose-file-img">
                                                    <img id="{{ 'blah' . $key }}"
                                                        src="{{ isset($employeedoc[$document->id]) && !empty($employeedoc[$document->id]) ? $logo . '/' . $employeedoc[$document->id] : '' }}" />
                                                </div>

                                            </div>


                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card emp_details">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                    {!! Form::text('account_holder_name', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('Enter account holder name'),
                                    ]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                    {!! Form::number('account_number', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('Enter account number'),
                                    ]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                    {!! Form::text('bank_name', null, ['class' => 'form-control', 'placeholder' => __('Enter bank name')]) !!}

                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                                    {!! Form::text('bank_identifier_code', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('Enter bank identifier code'),
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                    {!! Form::text('branch_location', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('Enter branch location'),
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                                    {!! Form::text('tax_payer_id', null, ['class' => 'form-control', 'placeholder' => __('Enter tax payer id')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts Section -->
            <div class="row mt-3">
                <div class="col-md-6 d-flex">
                    <div class="card emp_details w-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Profile Image') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="form-group mb-0">
                                {!! Form::label('profile_image', __('Employee Photo'), ['class' => 'form-label']) !!}
                                @if(!empty($employee->profile_image))
                                    @php $empImagePath = \App\Models\Utility::get_file('uploads/employee/'); @endphp
                                    <div class="mb-2">
                                        <img src="{{ $empImagePath . '/' . $employee->profile_image }}" alt="{{ __('Current Photo') }}" style="max-width:120px; border-radius:8px; border:1px solid #dee2e6;">
                                        <p class="text-muted small mt-1">{{ __('Current photo. Upload a new one to replace.') }}</p>
                                    </div>
                                @endif
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
                    <div class="card emp_details w-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Social Media Links') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('facebook', __('Facebook'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-facebook"></i></span>
                                        {!! Form::url('facebook', null, ['class' => 'form-control', 'placeholder' => 'https://facebook.com/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('linkedin', __('LinkedIn'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-linkedin"></i></span>
                                        {!! Form::url('linkedin', null, ['class' => 'form-control', 'placeholder' => 'https://linkedin.com/in/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('twitter', __('Twitter / X'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-twitter"></i></span>
                                        {!! Form::url('twitter', null, ['class' => 'form-control', 'placeholder' => 'https://twitter.com/...']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('instagram', __('Instagram'), ['class' => 'form-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-brand-instagram"></i></span>
                                        {!! Form::url('instagram', null, ['class' => 'form-control', 'placeholder' => 'https://instagram.com/...']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card emp_details">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Employment Period Details') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('probation_period', __('Probation Period (Days)'), ['class' => 'form-label']) !!}
                                    {!! Form::number('probation_period', null, ['class' => 'form-control', 'placeholder' => __('e.g. 90'), 'min' => 0]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('notice_period', __('Notice Period (Days)'), ['class' => 'form-label']) !!}
                                    {!! Form::number('notice_period', null, ['class' => 'form-control', 'placeholder' => __('e.g. 30'), 'min' => 0]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts Section (original) -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card emp_details">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Emergency Contacts') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $primaryContact = $emergencyContacts->where('is_primary', true)->first();
                                    $secondaryContact = $emergencyContacts->where('is_primary', false)->first();
                                    $emergencyContactPath = \App\Models\Utility::get_file('uploads/emergency_contacts/');
                                @endphp

                                <!-- Emergency Contact 1 (Required) -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6>{{ __('Emergency Contact 1') }} <x-required></x-required></h6>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="emergency_contacts[0][id]" value="{{ $primaryContact ? $primaryContact->id : '' }}">
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][full_name]', __('Full Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][full_name]', $primaryContact ? $primaryContact->full_name : old('emergency_contacts.0.full_name'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.full_name') ? ' is-invalid' : ''), 'placeholder' => __('Enter full name'), 'required']) !!}
                                                @error('emergency_contacts.0.full_name')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][relationship]', __('Relationship'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][relationship]', $primaryContact ? $primaryContact->relationship : old('emergency_contacts.0.relationship'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.relationship') ? ' is-invalid' : ''), 'placeholder' => __('Enter relationship'), 'required']) !!}
                                                @error('emergency_contacts.0.relationship')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][contact_number]', __('Contact Number'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][contact_number]', $primaryContact ? $primaryContact->contact_number : old('emergency_contacts.0.contact_number'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.contact_number') ? ' is-invalid' : ''), 'placeholder' => __('Enter contact number'), 'required']) !!}
                                                @error('emergency_contacts.0.contact_number')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][email]', __('Email Address'), ['class' => 'form-label']) !!}
                                                {!! Form::email('emergency_contacts[0][email]', $primaryContact ? $primaryContact->email : old('emergency_contacts.0.email'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.email') ? ' is-invalid' : ''), 'placeholder' => __('Enter email address')]) !!}
                                                @error('emergency_contacts.0.email')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[0][address]', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::textarea('emergency_contacts[0][address]', $primaryContact ? $primaryContact->address : old('emergency_contacts.0.address'), ['class' => 'form-control' . ($errors->has('emergency_contacts.0.address') ? ' is-invalid' : ''), 'rows' => 2, 'placeholder' => __('Enter address'), 'required']) !!}
                                                @error('emergency_contacts.0.address')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group mb-0">
                                                {!! Form::label('emergency_contacts[0][nid][]', __('NID Files (Image/PDF)'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                
                                                <!-- Existing Files List -->
                                                @if($primaryContact && $primaryContact->files->count() > 0)
                                                    <div class="existing-files mb-3" id="existing-files-0">
                                                        <h6 class="text-muted mb-2">{{ __('Existing Files') }}</h6>
                                                        @foreach($primaryContact->files as $file)
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2 existing-file-item" data-file-id="{{ $file->id }}">
                                                                <span class="text-truncate" style="max-width: 200px;">
                                                                    <i class="ti ti-file me-1"></i>{{ $file->original_name ?? $file->file_name }}
                                                                </span>
                                                                <div>
                                                                    <a href="{{ $emergencyContactPath . '/' . $file->file_name }}" target="_blank" class="btn btn-sm btn-info me-1">
                                                                        <i class="ti ti-eye"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFile(0, {{ $file->id }})">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div id="removed-files-container-0"></div>
                                                    </div>
                                                @endif
                                                
                                                <!-- New File Uploads -->
                                                <div id="nid-files-container-0">
                                                    <div class="choose-files mb-2">
                                                        <label for="emergency_contacts_0_nid_0">
                                                            <div class="bg-primary document">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"
                                                                class="form-control file file-validate d-none @error('emergency_contacts.0.nid.*') is-invalid @enderror"
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
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact 2 (Optional) -->
                                <div class="col-md-6">
                                    <div class="card border" id="secondary-contact-card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ __('Emergency Contact 2') }} <small class="text-muted">({{ __('Optional') }})</small></h6>
                                            @if($secondaryContact)
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSecondaryContact()">
                                                    <i class="ti ti-trash"></i> {{ __('Remove') }}
                                                </button>
                                            @endif
                                        </div>
                                        <div class="card-body" id="secondary-contact-body" style="{{ !$secondaryContact ? 'opacity: 0.6;' : '' }}">
                                            @if($secondaryContact)
                                                <input type="hidden" name="emergency_contacts[1][id]" value="{{ $secondaryContact->id }}">
                                            @endif
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][full_name]', __('Full Name'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][full_name]', $secondaryContact ? $secondaryContact->full_name : old('emergency_contacts.1.full_name'), ['class' => 'form-control secondary-field' . ($errors->has('emergency_contacts.1.full_name') ? ' is-invalid' : ''), 'placeholder' => __('Enter full name'), $secondaryContact ? '' : 'disabled']) !!}
                                                @error('emergency_contacts.1.full_name')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][relationship]', __('Relationship'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][relationship]', $secondaryContact ? $secondaryContact->relationship : old('emergency_contacts.1.relationship'), ['class' => 'form-control secondary-field' . ($errors->has('emergency_contacts.1.relationship') ? ' is-invalid' : ''), 'placeholder' => __('Enter relationship'), $secondaryContact ? '' : 'disabled']) !!}
                                                @error('emergency_contacts.1.relationship')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][contact_number]', __('Contact Number'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emergency_contacts[1][contact_number]', $secondaryContact ? $secondaryContact->contact_number : old('emergency_contacts.1.contact_number'), ['class' => 'form-control secondary-field' . ($errors->has('emergency_contacts.1.contact_number') ? ' is-invalid' : ''), 'placeholder' => __('Enter contact number'), $secondaryContact ? '' : 'disabled']) !!}
                                                @error('emergency_contacts.1.contact_number')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][email]', __('Email Address'), ['class' => 'form-label']) !!}
                                                {!! Form::email('emergency_contacts[1][email]', $secondaryContact ? $secondaryContact->email : old('emergency_contacts.1.email'), ['class' => 'form-control secondary-field' . ($errors->has('emergency_contacts.1.email') ? ' is-invalid' : ''), 'placeholder' => __('Enter email address'), $secondaryContact ? '' : 'disabled']) !!}
                                                @error('emergency_contacts.1.email')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                {!! Form::label('emergency_contacts[1][address]', __('Address'), ['class' => 'form-label']) !!}
                                                {!! Form::textarea('emergency_contacts[1][address]', $secondaryContact ? $secondaryContact->address : old('emergency_contacts.1.address'), ['class' => 'form-control secondary-field' . ($errors->has('emergency_contacts.1.address') ? ' is-invalid' : ''), 'rows' => 2, 'placeholder' => __('Enter address'), $secondaryContact ? '' : 'disabled']) !!}
                                                @error('emergency_contacts.1.address')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group mb-0">
                                                {!! Form::label('emergency_contacts[1][nid][]', __('NID Files (Image/PDF)'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                @if($secondaryContact)<x-required></x-required>@endif
                                                
                                                <!-- Existing Files List -->
                                                @if($secondaryContact && $secondaryContact->files->count() > 0)
                                                    <div class="existing-files mb-3" id="existing-files-1">
                                                        <h6 class="text-muted mb-2">{{ __('Existing Files') }}</h6>
                                                        @foreach($secondaryContact->files as $file)
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2 existing-file-item" data-file-id="{{ $file->id }}">
                                                                <span class="text-truncate" style="max-width: 200px;">
                                                                    <i class="ti ti-file me-1"></i>{{ $file->original_name ?? $file->file_name }}
                                                                </span>
                                                                <div>
                                                                    <a href="{{ $emergencyContactPath . '/' . $file->file_name }}" target="_blank" class="btn btn-sm btn-info me-1">
                                                                        <i class="ti ti-eye"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFile(1, {{ $file->id }})">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div id="removed-files-container-1"></div>
                                                    </div>
                                                @endif
                                                
                                                <!-- New File Uploads -->
                                                <div id="nid-files-container-1">
                                                    <div class="choose-files mb-2">
                                                        <label for="emergency_contacts_1_nid_0">
                                                            <div class="bg-primary document">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"
                                                                class="form-control file file-validate d-none @error('emergency_contacts.1.nid.*') is-invalid @enderror"
                                                                name="emergency_contacts[1][nid][]" id="emergency_contacts_1_nid_0"
                                                                accept=".jpg,.jpeg,.png,.pdf"
                                                                {{ $secondaryContact ? '' : 'disabled' }}>
                                                            <p class="file-error text-danger"></p>
                                                        </label>
                                                        <span class="file-name-display ms-2"></span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-file-btn" style="display:none;" onclick="removeFileInput(this)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFileInput(1)" {{ $secondaryContact ? '' : 'disabled' }}>
                                                    <i class="ti ti-plus"></i> {{ __('Add Another File') }}
                                                </button>
                                                @error('emergency_contacts.1.nid.*')
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        @if(!$secondaryContact)
                                            <div class="card-footer text-center" id="add-secondary-btn">
                                                <button type="button" class="btn btn-outline-primary" onclick="addSecondaryContact()">
                                                    <i class="ti ti-plus"></i> {{ __('Add Secondary Contact') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="employee-detail-wrap">
                        <div class="card emp_details">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Document Detail') }}</h6>
                            </div>
                            <div class="card-body employee-detail-edit-body">
                                <div class="row">
                                    @php
                                        $employeedoc = $employee
                                            ->documents()
                                            ->pluck('document_value', __('document_id'));
                                    @endphp
                                    @foreach ($documents as $key => $document)
                                        <div class="col-md-12">
                                            <div class="info">
                                                <strong>{{ $document->name }}</strong>
                                                <span><a href="{{ !empty($employeedoc[$document->id]) ? asset(Storage::url('uploads/document')) . '/' . $employeedoc[$document->id] : '' }}"
                                                        target="_blank">{{ !empty($employeedoc[$document->id]) ? $employeedoc[$document->id] : '' }}</a></span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="employee-detail-wrap">
                        <div class="card emp_details">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                            </div>
                            <div class="card-body employee-detail-edit-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Account Holder Name') }}</strong>
                                            <span>{{ $employee->account_holder_name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info font-style">
                                            <strong>{{ __('Account Number') }}</strong>
                                            <span>{{ $employee->account_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info font-style">
                                            <strong>{{ __('Bank Name') }}</strong>
                                            <span>{{ $employee->bank_name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Bank Identifier Code') }}</strong>
                                            <span>{{ $employee->bank_identifier_code }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Branch Location') }}</strong>
                                            <span>{{ $employee->branch_location }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info">
                                            <strong>{{ __('Tax Payer Id') }}</strong>
                                            <span>{{ $employee->tax_payer_id }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (\Auth::user()->type != 'employee')
            <div class="float-end">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('employee.index') }}';" class="btn btn-secondary me-2">
                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ">
            </div>
        @endif
        {!! Form::close() !!}
    </div>
@endsection

@push('script-page')
    <script type="text/javascript">
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

        // Remove existing file
        function removeExistingFile(contactIndex, fileId) {
            if(confirm('{{ __("Are you sure you want to remove this file?") }}')) {
                // Get the file item
                var fileItem = $('.existing-file-item[data-file-id="' + fileId + '"]').first();
                
                // Add visual feedback - strikethrough and opacity
                fileItem.css({
                    'opacity': '0.5',
                    'text-decoration': 'line-through',
                    'background-color': '#f8d7da'
                });
                
                // Replace the delete button with a restore button
                var buttonContainer = fileItem.find('.btn-danger').parent();
                buttonContainer.html('<button type="button" class="btn btn-sm btn-success" onclick="restoreExistingFile(' + contactIndex + ', ' + fileId + ')">' +
                    '<i class="ti ti-refresh"></i> {{ __("Restore") }}</button>');
                
                // Add hidden input to track removed files
                var container = $('#removed-files-container-' + contactIndex);
                var inputName = 'emergency_contacts[' + contactIndex + '][remove_files][]';
                container.append('<input type="hidden" name="' + inputName + '" value="' + fileId + '" id="remove-file-input-' + fileId + '">');
            }
        }

        // Restore existing file
        function restoreExistingFile(contactIndex, fileId) {
            // Get the file item
            var fileItem = $('.existing-file-item[data-file-id="' + fileId + '"]').first();
            
            // Remove visual feedback
            fileItem.css({
                'opacity': '1',
                'text-decoration': 'none',
                'background-color': ''
            });
            
            // Replace the restore button with delete button
            var buttonContainer = fileItem.find('.btn-success').parent();
            buttonContainer.html('<a href="' + fileItem.find('a').attr('href') + '" target="_blank" class="btn btn-sm btn-info me-1">' +
                '<i class="ti ti-eye"></i></a>' +
                '<button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFile(' + contactIndex + ', ' + fileId + ')">' +
                '<i class="ti ti-trash"></i></button>');
            
            // Remove the hidden input
            $('#remove-file-input-' + fileId).remove();
        }

        // Add secondary contact
        function addSecondaryContact() {
            $('#secondary-contact-body').css('opacity', '1');
            $('.secondary-field').prop('disabled', false);
            $('#nid-files-container-1 input[type="file"]').prop('disabled', false);
            $('#secondary-contact-card .btn-outline-primary').prop('disabled', false);
            $('#add-secondary-btn').hide();
            
            // Add remove button to header
            var removeBtn = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSecondaryContact()">' +
                           '<i class="ti ti-trash"></i> {{ __("Remove") }}</button>';
            $('#secondary-contact-card .card-header').append(removeBtn);
        }

        // Remove secondary contact
        function removeSecondaryContact() {
            if(confirm('{{ __("Are you sure you want to remove the secondary emergency contact?") }}')) {
                // Clear all fields
                $('.secondary-field').val('').prop('disabled', true);
                $('#nid-files-container-1 input[type="file"]').prop('disabled', true).val('');
                $('#nid-files-container-1 .file-name-display').text('');
                $('#nid-files-container-1 .remove-file-btn').hide();
                $('#existing-files-1').hide();
                $('#secondary-contact-card .btn-outline-primary').prop('disabled', true);
                
                // Add hidden input to mark all existing files for removal
                $('#removed-files-container-1').append('<input type="hidden" name="emergency_contacts[1][remove_all]" value="1">');
                
                $('#secondary-contact-body').css('opacity', '0.6');
                $('#secondary-contact-card .card-header .btn-outline-danger').remove();
                $('#add-secondary-btn').show();
            }
        }

        $(document).on('change', '#branch_id', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        function getDepartment(branch_id) {
            var data = {
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('employee.getdepartment') }}',
                method: 'POST',
                data: data,
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append(
                        '<option value="" disabled>{{ __('Select Department') }}</option>');

                    $.each(data, function(key, value) {
                        var selected = '';
                        if (key == '{{ $employee->department_id }}') {
                            selected = 'selected';
                        }

                        $('#department_id').append('<option value="' + key + '"  ' + selected + '>' + value +
                            '</option>');
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
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            var d_id = $('#department_id').val();
            var designation_id = '{{ $employee->designation_id }}';
            getDepartment(b_id);
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
    </script>
@endpush
