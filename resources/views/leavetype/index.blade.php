@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Leave Type') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Leave Type') }}</li>
@endsection


@section('action-btn')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @include('layouts.hrm_setup')
        </div>
        <div class="col-12">
            <div class="my-3 d-flex justify-content-end">
                @can('create leave type')
                    <a href="#" data-url="{{ route('leavetype.create') }}" data-ajax-popup="true"
                        data-title="{{ __('Create New Leave Type') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Leave Type') }}</th>
                                            <th>{{ __('Days / Year') }}</th>
                                            <th>{{ __('Attachment Required') }}</th>
                                            <th>{{ __('Min Advance Days') }}</th>
                                            <th width="200px">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-style">
                                        @foreach ($leavetypes as $leavetype)
                                            <tr>
                                                <td>
                                                    {{ $leavetype->title }}
                                                    @if($leavetype->is_default)
                                                        <span class="badge bg-secondary ms-1" title="{{ __('Default type — cannot be deleted') }}">{{ __('Default') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $leavetype->days }}</td>
                                                <td>
                                                    @if($leavetype->is_attachment_required)
                                                        <span class="badge bg-danger">{{ __('Required') }}</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ __('Optional') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($leavetype->min_advance_days > 0)
                                                        {{ $leavetype->min_advance_days }} {{ __('days') }}
                                                    @else
                                                        <span class="text-muted">{{ __('None') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('edit leave type')
                                                        <div class="action-btn me-2">
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                                data-url="{{ URL::to('leavetype/' . $leavetype->id . '/edit') }}"
                                                                data-ajax-popup="true" data-title="{{ __('Edit Leave Type') }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan

                                                    @if(!$leavetype->is_default)
                                                        @can('delete leave type')
                                                            <div class="action-btn ">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['leavetype.destroy', $leavetype->id],
                                                                    'id' => 'delete-form-' . $leavetype->id,
                                                                ]) !!}
                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $leavetype->id }}').submit();">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    @endif

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
        </div>
    </div>
@endsection
