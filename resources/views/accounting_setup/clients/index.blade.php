@extends('layouts.admin')
@section('page-title', __('Manage Clients'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Clients') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('accounting-clients.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Unique ID') }}</th>
                                <th>{{ __('Company Name') }}</th>
                                <th>{{ __('Contact Person') }}</th>
                                <th>{{ __('Contact Email') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $item)
                                <tr>
                                    <td>{{ $item->unique_id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->contact_person_name }}<br><small>{{ $item->contact_person_number }}</small></td>
                                    <td>{{ $item->contact_person_email }}</td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('accounting-clients.edit', $item->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                            <form method="POST" action="{{ route('accounting-clients.destroy', $item->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                </button>
                                            </form>
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