@extends('layouts.admin')
@section('page-title')
    {{__('Employee Asset Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Employee Assets')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create assets')
            <a href="{{ route('employee-assets.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i> {{__('Add Asset Assignment')}}
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
                                <th>{{__('Employee Name')}}</th>
                                <th>{{__('Asset Name')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Image')}}</th>
                                <th>{{__('Status')}}</th>
                                <th width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($assets as $asset)
                                <tr>
                                    <td>{{ !empty($asset->employee) ? $asset->employee->name : '-' }}</td>
                                    <td>{{ !empty($asset->asset) ? $asset->asset->name : $asset->asset_name }}</td>
                                    <td>{{ $asset->description }}</td>
                                    <td>
                                        @if($asset->image)
                                            <a href="{{ asset('uploads/employee_assets/'.$asset->image) }}" target="_blank">
                                                <img src="{{ asset('uploads/employee_assets/'.$asset->image) }}" class="rounded" width="50">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->status == 'Handover')
                                            <span class="badge bg-primary">{{__('Handover')}}</span>
                                        @elseif($asset->status == 'Returned')
                                            <span class="badge bg-success">{{__('Returned')}}</span>
                                        @elseif($asset->status == 'Lost')
                                            <span class="badge bg-danger">{{__('Lost')}}</span>
                                        @elseif($asset->status == 'Damaged')
                                            <span class="badge bg-warning">{{__('Damaged')}}</span>
                                        @elseif($asset->status == 'In Use')
                                            <span class="badge bg-info">{{__('In Use')}}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @can('edit assets')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('employee-assets.edit',$asset->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete assets')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['employee-assets.destroy', $asset->id],'id'=>'delete-form-'.$asset->id]) !!}
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
