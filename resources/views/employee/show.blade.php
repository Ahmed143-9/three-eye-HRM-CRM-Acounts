@extends('layouts.admin')

@section('page-title')
    {{__('Employee')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('employee.index')}}">{{__('Employee')}}</a></li>
    <li class="breadcrumb-item">{{$employeesId}}</li>
@endsection

@section('action-btn')
    @if(!empty($employee))
        <div class="text-end">
            <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2 drp-languages">
                <ul class="list-unstyled mb-0">
                    <li class="dropdown dash-h-item status-drp">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                           role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="drp-text hide-mob text-primary"> {{__('Joining Letter')}}</span>
                            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                        </a>
                        <div class="dropdown-menu icon-dropdown dash-h-dropdown">
                            <a href="{{route('joiningletter.download.pdf',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('PDF')}}</a>

                            <a href="{{route('joininglatter.download.doc',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('DOC')}}</a>
                        </div>
                    </li>
                </ul>
                <ul class="list-unstyled mb-0">
                    <li class="dropdown dash-h-item status-drp">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                           role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="drp-text hide-mob text-primary"> {{__('Experience Certificate')}}</span>
                            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                        </a>
                        <div class="dropdown-menu icon-dropdown dash-h-dropdown">
                            <a href="{{route('exp.download.pdf',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('PDF')}}</a>

                            <a href="{{route('exp.download.doc',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('DOC')}}</a></a>
                        </div>
                    </li>
                </ul>
                <ul class="list-unstyled mb-0">
                    <li class="dropdown dash-h-item status-drp">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                           role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="drp-text hide-mob text-primary"> {{__('NOC')}}</span>
                            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                        </a>
                        <div class="dropdown-menu icon-dropdown dash-h-dropdown">
                            <a href="{{route('noc.download.pdf',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('PDF')}}</a>

                            <a href="{{route('noc.download.doc',$employee->id)}}" class=" btn-icon dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"  target="_blanks" ><i class="ti ti-download "></i>{{__('DOC')}}</a>
                        </div>
                    </li>
                </ul>
                @can('edit employee')
                <a href="{{route('employee.edit',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" data-bs-toggle="tooltip" title="{{__('Edit')}}"class="btn btn-sm btn-info">
                    <i class="ti ti-pencil"></i>
                </a>
            @endcan
            </div>
        </div>
    @endif
@endsection

@section('content')
    @if(!empty($employee))
    <div class="row gy-4">
        <div class="col-md-6 col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Personal Detail') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    @if(!empty($employee->profile_image))
                        @php $empImagePath = \App\Models\Utility::get_file('uploads/employee/'); @endphp
                        <div class="mb-3 text-center">
                            <img src="{{ $empImagePath . '/' . $employee->profile_image }}"
                                alt="{{ $employee->name }}"
                                style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6;">
                        </div>
                    @endif
                    <div class="row gy-2">
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('EmployeeId') }} : </strong>
                                <span>{{ $employeesId }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info font-style">
                                <strong class="font-bold">{{ __('Name') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->name : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info font-style">
                                <strong class="font-bold">{{ __('Email') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->email : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Date of Birth') }} :</strong>
                                <span>{{ \Auth::user()->dateFormat(!empty($employee) ? $employee->dob : '') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Phone') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->phone : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Address') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->address : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Salary Type') }} :</strong>
                                <span>{{ !empty($employee->salaryType) ? $employee->salaryType->name : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Basic Salary') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->salary : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Company Detail') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    
                    <div class="row gy-2">
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Department') }} :</strong>
                                <span>{{ !empty($employee->department) ? $employee->department->name : '' }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Designation') }} :</strong>
                                <span>{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Date Of Joining') }} :</strong>
                                <span>{{ \Auth::user()->dateFormat(!empty($employee) ? $employee->company_doj : '') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Joining Salary') }} :</strong>
                                <span>{{ \Auth::user()->priceFormat(!empty($employee) ? $employee->joining_salary : 0) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Probation Period') }} :</strong>
                                <span>{{ !empty($employee->probation_period) ? $employee->probation_period . ' ' . __('days') : '-' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Notice Period') }} :</strong>
                                <span>{{ !empty($employee->notice_period) ? $employee->notice_period . ' ' . __('days') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Document Detail') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    
                    <div class="row gy-2">
                        @php

                            $employeedoc = !empty($employee)
                                ? $employee->documents()->pluck('document_value', __('document_id'))
                                : [];
                        @endphp
                        @if (!$documents->isEmpty())
                            @foreach ($documents as $key => $document)
                                <div class="col-sm-6 col-12">
                                    <div class="info">
                                        <strong class="font-bold">{{ $document->name }} : </strong>
                                        <span><a href="{{ !empty($employeedoc[$document->id]) ? asset(Storage::url('uploads/document')) . '/' . $employeedoc[$document->id] : '' }}"
                                                target="_blank">{{ !empty($employeedoc[$document->id]) ? $employeedoc[$document->id] : '' }}</a></span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                No Document Type Added.!
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Bank Account Detail') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    
                    <div class="row gy-2">
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Account Holder Name') }} : </strong>
                                <span>{{ !empty($employee) ? $employee->account_holder_name : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Account Number') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->account_number : '' }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Bank Name') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->bank_name : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Bank Identifier Code') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->bank_identifier_code : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Branch Location') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->branch_location : '' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold">{{ __('Tax Payer Id') }} :</strong>
                                <span>{{ !empty($employee) ? $employee->tax_payer_id : '' }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Links Section -->
        <div class="col-md-6 col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Social Media Links') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    <div class="row gy-2">
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold"><i class="ti ti-brand-facebook me-1"></i>{{ __('Facebook') }} :</strong>
                                <span>
                                    @if(!empty($employee->facebook))
                                        <a href="{{ $employee->facebook }}" target="_blank" class="text-primary">{{ $employee->facebook }}</a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold"><i class="ti ti-brand-linkedin me-1"></i>{{ __('LinkedIn') }} :</strong>
                                <span>
                                    @if(!empty($employee->linkedin))
                                        <a href="{{ $employee->linkedin }}" target="_blank" class="text-primary">{{ $employee->linkedin }}</a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold"><i class="ti ti-brand-twitter me-1"></i>{{ __('Twitter / X') }} :</strong>
                                <span>
                                    @if(!empty($employee->twitter))
                                        <a href="{{ $employee->twitter }}" target="_blank" class="text-primary">{{ $employee->twitter }}</a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="info">
                                <strong class="font-bold"><i class="ti ti-brand-instagram me-1"></i>{{ __('Instagram') }} :</strong>
                                <span>
                                    @if(!empty($employee->instagram))
                                        <a href="{{ $employee->instagram }}" target="_blank" class="text-primary">{{ $employee->instagram }}</a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contacts Section -->
        <div class="col-12 mt-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Emergency Contacts') }}</h5>
                </div>
                <div class="card-body employee-detail-body">
                    @if($employee->emergencyContacts && $employee->emergencyContacts->count() > 0)
                        <div class="row">
                            @foreach($employee->emergencyContacts as $contact)
                                <div class="col-md-6 col-12 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                {{ $contact->is_primary ? __('Primary Contact') : __('Secondary Contact') }}
                                                @if($contact->is_primary)
                                                    <span class="badge bg-primary ms-2">{{ __('Required') }}</span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">{{ __('Optional') }}</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row gy-2">
                                                <div class="col-sm-6 col-12">
                                                    <div class="info">
                                                        <strong class="font-bold">{{ __('Full Name') }} : </strong>
                                                        <span>{{ $contact->full_name }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-12">
                                                    <div class="info">
                                                        <strong class="font-bold">{{ __('Relationship') }} : </strong>
                                                        <span>{{ $contact->relationship }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-12">
                                                    <div class="info">
                                                        <strong class="font-bold">{{ __('Contact Number') }} : </strong>
                                                        <span>{{ $contact->contact_number }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-12">
                                                    <div class="info">
                                                        <strong class="font-bold">{{ __('Email') }} : </strong>
                                                        <span>{{ $contact->email ?: '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="info">
                                                        <strong class="font-bold">{{ __('Address') }} : </strong>
                                                        <span>{{ $contact->address }}</span>
                                                    </div>
                                                </div>
                                                @if($contact->files && $contact->files->count() > 0)
                                                    <div class="col-12 mt-2">
                                                        <div class="info">
                                                            <strong class="font-bold">{{ __('NID Files') }} : </strong>
                                                            <div class="mt-1">
                                                                @foreach($contact->files as $file)
                                                                    <a href="{{ asset(Storage::url('uploads/emergency_contacts/' . $file->file_name)) }}"
                                                                       target="_blank"
                                                                       class="btn btn-sm btn-outline-primary me-2 mb-1">
                                                                        <i class="ti ti-file"></i> {{ __('View File') }} {{ $loop->iteration }}
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-user-off fs-3 mb-2 d-block"></i>
                            {{ __('No emergency contacts added.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        </div>
    @endif
@endsection
