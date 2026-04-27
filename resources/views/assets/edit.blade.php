@extends('layouts.admin')
@section('page-title')
    {{__('Edit Company Asset')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('company-assets.index')}}">{{__('Company Assets')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($asset, ['route' => ['company-assets.update', $asset->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Asset Name'),['class'=>'form-label']) }}
                            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}
                            {{ Form::number('quantity', null, array('class' => 'form-control','required'=>'required', 'min' => 0)) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('status', __('Status'),['class'=>'form-label']) }}
                            {{ Form::select('status', ['Available' => 'Available', 'In Use' => 'In Use', 'Out of Stock' => 'Out of Stock', 'Damaged' => 'Damaged'],null, array('class' => 'form-control select2','id'=>'status','required'=>'required')) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                            {{ Form::textarea('description', null, array('class' => 'form-control', 'rows' => 3)) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('image', __('Image Upload (Optional)'),['class'=>'form-label']) }}
                            <input type="file" name="image" class="form-control mb-2">
                            @php
                                $assetImage=\App\Models\Utility::get_file('uploads/assets/');
                            @endphp
                            @if($asset->image)
                                <img src="{{ $assetImage . $asset->image }}" class="rounded" width="100">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" onclick="location.href='{{route('company-assets.index')}}'">
                        <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
