@extends('layouts.admin')
@section('page-title')
    {{__('Asset Categories Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account-assets.index')}}">{{__('Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Categories')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @if($categories->count() == 0)
            <form action="{{ route('asset-categories.setup-defaults') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success me-2">
                    <i class="ti ti-checklist"></i> {{__('Setup Default Categories')}}
                </button>
            </form>
        @endif
        @can('manage assets')
            <a href="{{ route('asset-categories.create') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i> {{__('Add Category')}}
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        @if($categories->count() == 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-category fa-5x text-muted mb-4"></i>
                    <h4 class="mb-3">{{__('Welcome to Asset Categories Setup')}}</h4>
                    <p class="text-muted mb-4">
                        {{__('Asset categories help you organize and classify your company assets. You can setup default categories or create your own.')}}
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="alert alert-info text-start">
                                <h6 class="mb-2">{{__('Default Categories Include:')}}</h6>
                                <ul class="mb-0">
                                    <li><strong>IT Equipment</strong> - Computers, laptops, monitors, etc.</li>
                                    <li><strong>Furniture</strong> - Desks, chairs, cabinets, etc.</li>
                                    <li><strong>Electronics</strong> - Phones, printers, cameras, etc.</li>
                                    <li><strong>Vehicles</strong> - Company cars, trucks, bikes, etc.</li>
                                    <li><strong>Machinery</strong> - Industrial machines and tools</li>
                                    <li><strong>Other</strong> - Miscellaneous assets</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <form action="{{ route('asset-categories.setup-defaults') }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="ti ti-checklist"></i> {{__('Setup Default Categories Now')}}
                            </button>
                        </form>
                        <a href="{{ route('asset-categories.create') }}" class="btn btn-primary btn-lg ms-2">
                            <i class="ti ti-plus"></i> {{__('Create Custom Category')}}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{__('Asset Categories')}}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{__('Icon')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Code')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Total Assets')}}</th>
                                    <th>{{__('Available')}}</th>
                                    <th>{{__('Assigned')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} fa-lg"></i>
                                            @else
                                                <i class="ti ti-package fa-lg"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $category->color }}">{{ $category->code }}</span>
                                        </td>
                                        <td>{{ Str::limit($category->description, 50) ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $category->assets_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $category->available_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->assigned_count }}</span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">{{__('Active')}}</span>
                                            @else
                                                <span class="badge bg-danger">{{__('Inactive')}}</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <span>
                                                @can('manage assets')
                                                    <a href="{{ route('asset-categories.edit', $category->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('manage assets')
                                                    @if($category->assets_count == 0)
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['asset-categories.destroy', $category->id],'id'=>'delete-form-'.$category->id, 'style'=>'display:inline']) !!}
                                                        <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$category->id}}').submit();">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endif
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

            <div class="text-end mt-3">
                <a href="{{ route('account-assets.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> {{__('Back to Assets')}}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
