@extends('layouts.admin')
@section('page-title')
    {{__('Company Asset Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Company Assets')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('Manage Assets')
            <a href="{{ route('company-assets.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i> {{__('Add Asset')}}
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Asset UID')}}</th>
                                <th>{{__('Asset Name')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Quantity')}}</th>
                                <th>{{__('Status')}}</th>
                                <th width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($assets as $asset)
                                <tr>
                                    <td>{{ $asset->asset_unique_number }}</td>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->description }}</td>
                                    <td>{{ $asset->quantity }}</td>
                                    <td>
                                        @if($asset->status == 'Available')
                                            <span class="badge bg-success">{{__('Available')}}</span>
                                        @elseif($asset->status == 'In Use')
                                            <span class="badge bg-info">{{__('In Use')}}</span>
                                        @elseif($asset->status == 'Out of Stock')
                                            <span class="badge bg-danger">{{__('Out of Stock')}}</span>
                                        @elseif($asset->status == 'Damaged')
                                            <span class="badge bg-warning">{{__('Damaged')}}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @can('Manage Assets')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('company-assets.edit',$asset->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('Manage Assets')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['company-assets.destroy', $asset->id],'id'=>'delete-form-'.$asset->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$asset->id}}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
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
@endsection
