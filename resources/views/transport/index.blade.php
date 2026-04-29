@extends('layouts.admin')
@section('page-title')
    {{__('Manage Transport')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Transport Management')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        <a href="{{ route('transports.create') }}"
            data-title="{{ __('Create New Transport') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    @if(count($pendingOrders) > 0)
        <div class="col-xl-12">
            <div class="card border border-primary">
                <div class="card-header">
                    <h5 class="text-primary">{{__('Pending Transport Requests (New Sales Orders)')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pendingOrders as $order)
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-body">
                                        <h6 class="mb-3">{{ $order->order_number }}</h6>
                                        <p class="text-muted text-sm mb-3">
                                            {{__('Customer')}}: {{ optional($order->customer)->name }}<br>
                                            {{__('Date')}}: {{ Auth::user()->dateFormat($order->created_at) }}
                                        </p>
                                        <a href="{{ route('transports.create') }}?sales_order_id={{ $order->id }}" class="btn btn-sm btn-primary w-100">
                                            <i class="ti ti-plus me-1"></i>{{__('Create Transport Order')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>{{__('Transport ID')}}</th>
                            <th>{{__('Client')}}</th>
                            <th>{{__('Driver')}}</th>
                            <th>{{__('Truck') }}</th>
                            <th>{{__('Starting Date') }}</th>
                            <th>{{__('Delivery Date') }}</th>
                            <th>{{__('Status') }}</th>
                            <th width="200px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($transports as $transport)
                            <tr>
                                <td class="Id">
                                    <a href="{{ route('transports.show', $transport->id) }}" class="btn btn-outline-primary">{{ $transport->unique_id }}</a>
                                </td>
                                <td>{{ $transport->client_id > 0 ? ($transport->client ? $transport->client->name : '-') : ($transport->manual_client_name ?? '-') }}</td>
                                <td>{{ $transport->driver_name }}</td>
                                <td>{{ $transport->truck_number }}</td>
                                <td>{{ Auth::user()->dateFormat($transport->starting_date) }}</td>
                                <td>{{ $transport->delivery_date ? Auth::user()->dateFormat($transport->delivery_date) : '-' }}</td>
                                <td>
                                    @if($transport->status == 'pending')
                                        <span class="badge bg-warning p-2 px-3 rounded">{{ ucfirst($transport->status) }}</span>
                                    @else
                                        <span class="badge bg-success p-2 px-3 rounded">{{ ucfirst($transport->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btn me-2">
                                        <a href="{{ route('transports.edit', $transport->id) }}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a>
                                    </div>
                                    <div class="action-btn">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['transports.destroy', $transport->id], 'id'=>'delete-form-'.$transport->id]) !!}
                                            <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$transport->id}}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                        {!! Form::close() !!}
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
