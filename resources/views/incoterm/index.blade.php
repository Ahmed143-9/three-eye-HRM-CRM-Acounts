@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Incoterms') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Incoterms') }}</li>
@endsection

@section('action-btn')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @include('layouts.account_setup')
        </div>

        <div class="col-12">
            <div class="mb-4 d-flex justify-content-end">
                @if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup'))
                    <a href="#" data-url="{{ route('incoterm.create') }}" data-ajax-popup="true"
                        data-title="{{ __('Create New Incoterm') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i>
                    </a>
                @endif
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body table-border-style">

                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Incoterm') }}</th>
                                            <th width="200px" class="flex-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-style">
                                        @foreach ($incoterms as $incoterm)
                                            <tr>
                                                <td>{{ $incoterm->name }}</td>
                                                <td class="Action">
                                                    <span>
                                                        @if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup'))
                                                            <div class="action-btn me-2">

                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm align-items-center bg-info"
                                                                    data-url="{{ URL::to('incoterm/' . $incoterm->id . '/edit') }}"
                                                                    data-ajax-popup="true" data-title="{{ __('Edit Incoterm') }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-original-title="{{ __('Edit') }}"><i
                                                                        class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                        
                                                            <div class="action-btn ">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['incoterm.destroy', $incoterm->id],
                                                                    'id' => 'delete-form-' . $incoterm->id,
                                                                ]) !!}

                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $incoterm->id }}').submit();"><i
                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endif
                                                    </span>
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
