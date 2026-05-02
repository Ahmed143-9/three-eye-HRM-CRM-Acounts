@extends('layouts.admin')
@section('page-title')
    {{__('Manage Sales Orders')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Sales Orders')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('sales-orders.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
            <i class="ti ti-plus"></i>
        </a>
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
                                <th>{{__('Order Number')}}</th>
                                <th>{{__('PI Number')}}</th>
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Current Step')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th width="150px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->pi ? $order->pi->pi_number : 'N/A' }}</td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>                                    <td><span class="badge bg-info p-2 px-3 rounded">{{ $order->current_step }}</span></td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ ucfirst($order->status) }}</span>
                                        @else
                                            <span class="badge bg-success p-2 px-3 rounded">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ Utility::dateFormat(Utility::settings(), $order->created_at) }}</td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn bg-primary ms-2">
                                                <a href="{{ route('sales-orders.show',$order->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('View Workflow')}}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
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
