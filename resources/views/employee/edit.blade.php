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
                                    {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('department_id', $departments, $employee->department_id, ['class' => 'form-control select2-tags', 'id' => 'department_id', 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('designation_id', __('Designation'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div id="designation_div">
                                        {{ Form::select('designation_id', $designations, $employee->designation_id, ['class' => 'form-control select2-tags', 'id' => 'designation_id', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('company_doj', 'Company Date Of Joining', ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::date('company_doj', null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('joining_salary', __('Joining Salary'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::number('joining_salary', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter joining salary'), 'step' => '0.01']) !!}
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
                                    <div class="col-md-6">
                                        <div class="info font-style">
                                            <strong>{{ __('Joining Salary') }}</strong>
                                            <span>{{ \Auth::user()->priceFormat($employee->joining_salary) }}</span>
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
                                $employeedoc = $employee->documents()->pluck('document_value', 'document_id');
                                $logo = \App\Models\Utility::get_file('uploads/document/');
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
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                                value="{{ $document->id }}">
                                            <div class="choose-file">
                                                <label for="document[{{ $document->id }}]">
                                                    <input
                                                        class="form-control file-validate @if (!empty($employeedoc[$document->id])) float-left @endif @error('document') is-invalid @enderror "
                                                        @if ($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                                                        name="document[{{ $document->id }}]"
                                                        type="file" data-filename="{{ $document->id . '_filename' }}">
                                                    <p id="" class="file-error text-danger"></p>
                                                </label>
                                                <p class="{{ $document->id . '_filename' }}"></p>
                                                <div class="choose-file-img">
                                                    @if(isset($employeedoc[$document->id]) && !empty($employeedoc[$document->id]))
                                                        <a href="{{ $logo . '/' . $employeedoc[$document->id] }}" target="_blank">
                                                            <i class="ti ti-download text-primary" style="font-size: 20px;"></i>
                                                        </a>
                                                    @endif
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
                    <div class="card emp_details w-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Profile & Social') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('profile_image', __('Profile Image'), ['class' => 'form-label']) !!}
                                    @if(!empty($employee->profile_image))
                                        @php $empImagePath = \App\Models\Utility::get_file('uploads/employee/'); @endphp
                                        <div class="mb-2">
                                            <img src="{{ $empImagePath . '/' . $employee->profile_image }}" alt="{{ __('Current Photo') }}" style="max-width:100px; border-radius:8px; border:1px solid #dee2e6;">
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
                                        <img id="profile_image_preview" src="" style="display:none; max-width:150px; margin-top:8px; border-radius: 8px;" />
                                    </div>
                                </div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts Section (Dynamic Repeater for Edit) -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card emp_details">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ __('Emergency Contacts') }} <x-required></x-required></h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addEmergencyContact()">
                                <i class="ti ti-plus"></i> {{ __('Add More') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="emergency-contacts-container">
                                @php
                                    $emergencyContactPath = \App\Models\Utility::get_file('uploads/emergency_contacts/');
                                @endphp
                                @foreach($emergencyContacts as $index => $contact)
                                    <div class="card border mb-3 emergency-contact-card" id="contact-{{ $index }}">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ __('Emergency Contact') }} #{{ $index + 1 }} @if($contact->is_primary) <span class="badge bg-info ms-2">{{ __('Primary') }}</span> @endif</h6>
                                            @if(!$contact->is_primary)
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeEmergencyContact({{ $index }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="emergency_contacts[{{ $index }}][id]" value="{{ $contact->id }}">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">{{ __('Full Name') }}</label><x-required></x-required>
                                                    <input type="text" name="emergency_contacts[{{ $index }}][full_name]" class="form-control" value="{{ $contact->full_name }}" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">{{ __('Relationship') }}</label><x-required></x-required>
                                                    <input type="text" name="emergency_contacts[{{ $index }}][relationship]" class="form-control" value="{{ $contact->relationship }}" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">{{ __('Contact Number') }}</label><x-required></x-required>
                                                    <input type="text" name="emergency_contacts[{{ $index }}][contact_number]" class="form-control" value="{{ $contact->contact_number }}" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">{{ __('Email Address') }}</label>
                                                    <input type="email" name="emergency_contacts[{{ $index }}][email]" class="form-control" value="{{ $contact->email }}">
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <label class="form-label">{{ __('Address') }}</label><x-required></x-required>
                                                    <input type="text" name="emergency_contacts[{{ $index }}][address]" class="form-control" value="{{ $contact->address }}" required>
                                                </div>
                                                
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">{{ __('NID Files') }}</label>
                                                    <div class="existing-files mb-2">
                                                        @foreach($contact->files as $file)
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2 existing-file-item" data-file-id="{{ $file->id }}">
                                                                <span><i class="ti ti-file me-1"></i>{{ $file->original_name }}</span>
                                                                <div>
                                                                    <a href="{{ $emergencyContactPath . '/' . $file->file_name }}" target="_blank" class="btn btn-sm btn-info me-1"><i class="ti ti-eye"></i></a>
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFile({{ $index }}, {{ $file->id }}, this)"><i class="ti ti-trash"></i></button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div id="removed-files-container-{{ $index }}"></div>
                                                    </div>

                                                    <div id="nid-files-container-{{ $index }}">
                                                        <!-- New file inputs will be appended here -->
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNidFile({{ $index }})">
                                                        <i class="ti ti-plus"></i> {{ __('Add File') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                        <div class="card-body">
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
    <script>
        var contactCount = {{ count($emergencyContacts) }};

        function addEmergencyContact() {
            var id = contactCount++;
            var html = `
                <div class="card border mb-3 emergency-contact-card" id="contact-${id}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ __('Emergency Contact') }} #${id+1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeEmergencyContact(${id})">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ __('Full Name') }}</label><x-required></x-required>
                                <input type="text" name="emergency_contacts[${id}][full_name]" class="form-control" placeholder="{{ __('Enter full name') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ __('Relationship') }}</label><x-required></x-required>
                                <input type="text" name="emergency_contacts[${id}][relationship]" class="form-control" placeholder="{{ __('Enter relationship') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ __('Contact Number') }}</label><x-required></x-required>
                                <input type="text" name="emergency_contacts[${id}][contact_number]" class="form-control" placeholder="{{ __('Enter contact number') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ __('Email Address') }}</label>
                                <input type="email" name="emergency_contacts[${id}][email]" class="form-control" placeholder="{{ __('Enter email address') }}">
                            </div>
                            <div class="form-group col-md-8">
                                <label class="form-label">{{ __('Address') }}</label><x-required></x-required>
                                <input type="text" name="emergency_contacts[${id}][address]" class="form-control" placeholder="{{ __('Enter address') }}" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{ __('NID Files (Image/PDF)') }}</label>
                                <div id="nid-files-container-${id}"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNidFile(${id})">
                                    <i class="ti ti-plus"></i> {{ __('Add File') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#emergency-contacts-container').append(html);
        }

        function removeEmergencyContact(id) {
            var $card = $(`#contact-${id}`);
            var contactId = $card.find('input[name*="[id]"]').val();
            if (contactId) {
                if (confirm('{{ __("Are you sure you want to remove this contact and all their files?") }}')) {
                    $('#emergency-contacts-container').append(`<input type="hidden" name="remove_contacts[]" value="${contactId}">`);
                    $card.remove();
                }
            } else {
                $card.remove();
            }
        }

        function addNidFile(contactId) {
            var container = $(`#nid-files-container-${contactId}`);
            var fileId = container.find('.choose-files').length;
            var inputId = `emergency_contacts_${contactId}_nid_${fileId}`;
            
            var html = `
                <div class="choose-files mb-2">
                    <label for="${inputId}">
                        <div class="bg-primary document">
                            <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                        </div>
                        <input type="file" class="form-control file file-validate d-none" name="nid_files[${contactId}][]" id="${inputId}" accept=".jpg,.jpeg,.png,.pdf">
                        <p class="file-error text-danger"></p>
                    </label>
                    <span class="file-name-display ms-2"></span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="$(this).parent().remove()">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            `;
            container.append(html);
        }

        function removeExistingFile(contactIndex, fileId, btn) {
            if(confirm('{{ __("Are you sure you want to remove this file?") }}')) {
                var $item = $(btn).closest('.existing-file-item');
                $item.css({'opacity': '0.5', 'text-decoration': 'line-through', 'background-color': '#f8d7da'});
                $(btn).replaceWith(`<button type="button" class="btn btn-sm btn-success" onclick="restoreExistingFile(${contactIndex}, ${fileId}, this)"><i class="ti ti-refresh"></i></button>`);
                $(`#removed-files-container-${contactIndex}`).append(`<input type="hidden" name="emergency_contacts[${contactIndex}][remove_files][]" value="${fileId}" id="remove-file-input-${fileId}">`);
            }
        }

        function restoreExistingFile(contactIndex, fileId, btn) {
            var $item = $(btn).closest('.existing-file-item');
            $item.css({'opacity': '1', 'text-decoration': 'none', 'background-color': ''});
            $(btn).replaceWith(`<button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFile(${contactIndex}, ${fileId}, this)"><i class="ti ti-trash"></i></button>`);
            $(`#remove-file-input-${fileId}`).remove();
        }

        $(document).on('change', 'input[type="file"]', function(e) {
            if(e.target.files.length > 0) {
                var fileName = e.target.files[0].name;
                $(this).closest('.choose-files').find('.file-name-display').text(fileName);
            }
        });

        $(document).ready(function() {
            $('.select2-tags').select2({
                tags: true,
                width: '100%',
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                }
            });

            $(document).on('change', '#department_id', function() {
                var department_id = $(this).val();
                if (isNaN(department_id) && department_id != '') {
                    quickCreateHRM('department', department_id, null, function(newId) {
                        $('#department_id').val(newId).trigger('change');
                    });
                    return;
                }
                getDesignations(department_id);
            });

            $(document).on('change', '#designation_id', function() {
                var designation_id = $(this).val();
                var department_id = $('#department_id').val();
                if (isNaN(designation_id) && designation_id != '') {
                    quickCreateHRM('designation', designation_id, department_id, function(newId) {
                        var newOption = new Option(designation_id, newId, true, true);
                        $('#designation_id').append(newOption).trigger('change');
                    });
                }
            });

            function quickCreateHRM(type, name, department_id, callback) {
                $.ajax({
                    url: '{{ route('hrm-setup.quick-create') }}',
                    type: 'POST',
                    data: {
                        "type": type, "name": name, "department_id": department_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (data.success) {
                            show_toastr('Success', (type == 'department' ? 'Department' : 'Designation') + ' created', 'success');
                            if (callback) callback(data.id);
                        } else {
                            show_toastr('Error', data.message, 'error');
                        }
                    },
                    error: function() { show_toastr('Error', 'Server error', 'error'); }
                });
            }

            function getDesignations(did) {
                var currentDesigId = '{{ $employee->designation_id }}';
                $.ajax({
                    url: '{{ route('hrm-setup.designations-by-department') }}',
                    type: 'GET',
                    data: { "department_id": did },
                    success: function(data) {
                        $('#designation_id').empty();
                        $('#designation_id').append('<option value="">Select Designation</option>');
                        $.each(data, function (index, item) {
                            var selected = (item.id == currentDesigId) ? 'selected' : '';
                            $('#designation_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                        });
                        $('#designation_id').trigger('change');
                    }
                });
            }
        });
    </script>
@endpush
