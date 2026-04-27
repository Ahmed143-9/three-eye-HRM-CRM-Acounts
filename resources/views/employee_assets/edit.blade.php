@extends('layouts.admin')
@section('page-title')
    {{__('Edit Employee Asset Assignment')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('employee-assets.index')}}">{{__('Employee Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($employee_asset, ['route' => ['employee-assets.update', $employee_asset->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('employee_id', __('Employee Name'),['class'=>'form-label']) }}
                            {{ Form::select('employee_id', $employees,null, array('class' => 'form-control select2','id'=>'employee_id','required'=>'required')) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('asset_id', __('Select Company Asset (Optional)'),['class'=>'form-label']) }}
                            {{ Form::select('asset_id', $company_assets,null, array('class' => 'form-control select2','id'=>'asset_id')) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('asset_name', __('Asset Name (If not in company list)'),['class'=>'form-label']) }}
                            {{ Form::text('asset_name', null, array('class' => 'form-control')) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('status', __('Status'),['class'=>'form-label']) }}
                            {{ Form::select('status', ['Handover' => 'Handover', 'Returned' => 'Returned', 'Lost' => 'Lost', 'Damaged' => 'Damaged', 'In Use' => 'In Use'],null, array('class' => 'form-control select2','id'=>'status','required'=>'required')) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                            {{ Form::textarea('description', null, array('class' => 'form-control', 'rows' => 3)) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('image', __('Image Upload'),['class'=>'form-label']) }}
                            <input type="file" name="image" class="form-control mb-2">
                            @if($employee_asset->image)
                                <img src="{{ asset('uploads/employee_assets/'.$employee_asset->image) }}" class="rounded" width="100">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" onclick="location.href='{{route('employee-assets.index')}}'">
                        <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@push('css-page')
<style>
    .choices__list--dropdown {
        top: 100% !important;
        bottom: auto !important;
    }
    .choices.is-flipped .choices__list--dropdown {
        top: 100% !important;
        bottom: auto !important;
        border-radius: 0 0 4px 4px !important;
        border-top: 1px solid #ddd !important;
    }
</style>
@endpush
@endsection
