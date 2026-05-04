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
                                    {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2-tags', 'id' => 'department_id', 'placeholder' => 'Select or Type New Department', 'required' => 'required']) }}
                                        <div class="text-xs mt-1 text-primary">
                                            <i class="ti ti-info-circle"></i> {{ __('You can type a new department and press Enter to create it.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('designation_id', __('Designation'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::select('designation_id', $designations, null, ['class' => 'form-control select2-tags', 'id' => 'designation_id', 'placeholder' => 'Select or Type New Designation', 'required' => 'required']) }}
                                        <div class="text-xs mt-1 text-primary">
                                            <i class="ti ti-info-circle"></i> {{ __('You can type a new designation for the selected department.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('company_doj', __('Company Date Of Joining'), ['class' => '  form-label']) !!}<x-required></x-required>
                                    {{ Form::date('company_doj', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off' ,'placeholder'=>'Select company date of joining']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('joining_salary', __('Joining Salary'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::number('joining_salary', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter joining salary'), 'step' => '0.01']) !!}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card emp_details w-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Document') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
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
                                                        class="form-control file-validate @error('document') is-invalid @enderror"
                                                        @if ($document->is_required == 1) required @endif
                                                        name="document[{{ $document->id }}]" type="file"
                                                        id="document[{{ $document->id }}]"
                                                        data-filename="{{ $document->id . '_filename' }}">
                                                </label>
                                                <p class="{{ $document->id . '_filename' }}"></p>
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
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('profile_image', __('Profile Image'), ['class' => 'form-label']) !!}
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

            <!-- Emergency Contacts Section (Dynamic Repeater) -->
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
                                <!-- First Contact (Required) -->
                                <div class="card border mb-3 emergency-contact-card" id="contact-0">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ __('Emergency Contact') }} #1 <span class="badge bg-info ms-2">{{ __('Primary') }}</span></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                {!! Form::label('emergency_contacts[0][full_name]', __('Full Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][full_name]', null, ['class' => 'form-control', 'placeholder' => __('Enter full name'), 'required']) !!}
                                            </div>
                                            <div class="form-group col-md-4">
                                                {!! Form::label('emergency_contacts[0][relationship]', __('Relationship'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][relationship]', null, ['class' => 'form-control', 'placeholder' => __('Enter relationship'), 'required']) !!}
                                            </div>
                                            <div class="form-group col-md-4">
                                                {!! Form::label('emergency_contacts[0][contact_number]', __('Contact Number'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][contact_number]', null, ['class' => 'form-control', 'placeholder' => __('Enter contact number'), 'required']) !!}
                                            </div>
                                            <div class="form-group col-md-4">
                                                {!! Form::label('emergency_contacts[0][email]', __('Email Address'), ['class' => 'form-label']) !!}
                                                {!! Form::email('emergency_contacts[0][email]', null, ['class' => 'form-control', 'placeholder' => __('Enter email address')]) !!}
                                            </div>
                                            <div class="form-group col-md-8">
                                                {!! Form::label('emergency_contacts[0][address]', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::text('emergency_contacts[0][address]', null, ['class' => 'form-control', 'placeholder' => __('Enter address'), 'required']) !!}
                                            </div>
                                            <div class="form-group col-md-12">
                                                {!! Form::label('emergency_contacts[0][nid][]', __('NID Files (Image/PDF)'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                <div id="nid-files-container-0">
                                                    <div class="choose-files mb-2">
                                                        <label for="emergency_contacts_0_nid_0">
                                                            <div class="bg-primary document">
                                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file" class="form-control file file-validate d-none" name="nid_files[0][]" id="emergency_contacts_0_nid_0" accept=".jpg,.jpeg,.png,.pdf">
                                                            <p class="file-error text-danger"></p>
                                                        </label>
                                                        <span class="file-name-display ms-2"></span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNidFile(0)">
                                                    <i class="ti ti-plus"></i> {{ __('Add Another File') }}
                                                </button>
                                            </div>
                                        </div>
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
        </div>

        <div class="float-end">
            <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("employee.index")}}';" class="btn btn-secondary me-2">
            <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection

@push('script-page')
    <script>
        var contactCount = 1;

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
                                <label class="form-label">{{ __('NID Files (Image/PDF)') }}</label><x-required></x-required>
                                <div id="nid-files-container-${id}">
                                    <div class="choose-files mb-2">
                                        <label for="emergency_contacts_${id}_nid_0">
                                            <div class="bg-primary document">
                                                <i class="ti ti-upload"></i>{{ __('Choose file here') }}
                                            </div>
                                            <input type="file" class="form-control file file-validate d-none" name="nid_files[${id}][]" id="emergency_contacts_${id}_nid_0" accept=".jpg,.jpeg,.png,.pdf">
                                            <p class="file-error text-danger"></p>
                                        </label>
                                        <span class="file-name-display ms-2"></span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNidFile(${id})">
                                    <i class="ti ti-plus"></i> {{ __('Add Another File') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#emergency-contacts-container').append(html);
        }

        function removeEmergencyContact(id) {
            $(`#contact-${id}`).remove();
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
                        "type": type,
                        "name": name,
                        "department_id": department_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (data.success) {
                            show_toastr('Success', (type == 'department' ? 'Department' : 'Designation') + ' created: ' + data.name, 'success');
                            if (callback) callback(data.id);
                        } else {
                            show_toastr('Error', data.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        show_toastr('Error', 'Failed to create ' + type, 'error');
                    }
                });
            }

            function getDesignations(did) {
                $.ajax({
                    url: '{{ route('hrm-setup.designations-by-department') }}',
                    type: 'GET',
                    data: { "department_id": did },
                    success: function(data) {
                        $('#designation_id').empty();
                        $('#designation_id').append('<option value="">Select or Type New Designation</option>');
                        $.each(data, function (index, item) {
                            $('#designation_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                        $('#designation_id').trigger('change');
                    }
                });
            }
        });
    </script>
@endpush
