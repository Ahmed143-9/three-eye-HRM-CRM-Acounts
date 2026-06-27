@extends('layouts.admin')

@section('page-title')
    {{__('Manage Daily Accomplishments')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Daily Accomplishments')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @if(\Auth::user()->can('create daily accomplishment') || \Auth::user()->type == 'Employee')
            <a href="#" data-url="{{ route('daily-accomplishments.create') }}" data-ajax-popup="true" data-title="{{__('Create New Daily Accomplishment')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                @if(\Auth::user()->type != 'Employee')
                                    <th>{{__('Employee')}}</th>
                                @endif
                                <th>{{__('Date')}}</th>
                                <th>{{__('Summary')}}</th>
                                <th>{{__('Challenges')}}</th>
                                <th>{{__('Hours Spent')}}</th>
                                @if(\Auth::user()->can('edit daily accomplishment') || \Auth::user()->can('delete daily accomplishment') || \Auth::user()->type == 'Employee')
                                    <th width="200px">{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($accomplishments as $accomplishment)
                                <tr>
                                    @if(\Auth::user()->type != 'Employee')
                                        <td>{{ !empty($accomplishment->employee)?$accomplishment->employee->name:'' }}</td>
                                    @endif
                                    <td>{{ \Auth::user()->dateFormat($accomplishment->date) }}</td>
                                    <td>{{ $accomplishment->summary }}</td>
                                    <td>{{ $accomplishment->challenges }}</td>
                                    <td>{{ $accomplishment->hours_spent }}</td>
                                    @if(\Auth::user()->can('edit daily accomplishment') || \Auth::user()->can('delete daily accomplishment') || \Auth::user()->type == 'Employee')
                                        <td class="Action">
                                            @if(\Auth::user()->can('edit daily accomplishment') || \Auth::user()->type == 'Employee')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center" data-url="{{ URL::to('daily-accomplishments/'.$accomplishment->id.'/edit') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Daily Accomplishment')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(\Auth::user()->can('delete daily accomplishment') || \Auth::user()->type == 'Employee')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['daily-accomplishments.destroy', $accomplishment->id],'id'=>'delete-form-'.$accomplishment->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endif
                                        </td>
                                    @endif
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
