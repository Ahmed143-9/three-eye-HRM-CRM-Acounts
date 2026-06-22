@extends('layouts.admin')

@section('page-title')
    {{ __('Inventory Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Inventory') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal" title="{{ __('Create New Item') }}">
            <i class="ti ti-plus"></i>
        </button>
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
                                    <th>{{ __('Item Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Total Purchased') }}</th>
                                    <th>{{ __('Total Used') }}</th>
                                    <th>{{ __('Available Stock') }}</th>
                                    <th>{{ __('Est. Stock Value') }}</th>
                                    <th>{{ __('Weighted Avg Cost') }}</th>
                                    <th width="150px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="font-style">{{ $item->name }}</td>
                                        <td class="font-style">{{ $item->type }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{{ number_format($item->total_purchased, 2) }}</td>
                                        <td>{{ number_format($item->total_used, 2) }}</td>
                                        <td>
                                            @if($item->total_available <= 0)
                                                <span class="badge bg-danger p-2 px-3 rounded">{{ __('Out of Stock') }}</span>
                                            @else
                                                <span class="badge bg-success p-2 px-3 rounded">{{ number_format($item->total_available, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->priceFormat($item->total_value) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($item->weighted_average_cost) }}</td>
                                        <td class="Action">
                                            <div class="action-btn bg-info ms-2">
                                                <a href="{{ route('inventory.show', $item->id) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
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

    <!-- Create Item Modal -->
    <div class="modal fade" id="createItemModal" tabindex="-1" aria-labelledby="createItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createItemModalLabel">{{ __('Create New Inventory Item') }}</h5>
                    <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                </div>
                {{ Form::open(['route' => 'inventory.store', 'method' => 'POST']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                {{ Form::label('name', __('Item Name'), ['class' => 'form-label']) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('e.g. Drums, IBC Containers, Packaging Materials'), 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                {{ Form::label('type', __('Item Type'), ['class' => 'form-label']) }}
                                {{ Form::text('type', 'Consumable', ['class' => 'form-control', 'placeholder' => __('e.g. Consumable, Raw Material'), 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                {{ Form::label('unit', __('Unit of Measure'), ['class' => 'form-label']) }}
                                {{ Form::text('unit', 'pcs', ['class' => 'form-control', 'placeholder' => __('e.g. pcs, bags, rolls'), 'required' => 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Item') }}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
