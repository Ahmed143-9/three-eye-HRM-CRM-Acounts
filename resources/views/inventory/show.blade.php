@extends('layouts.admin')

@section('page-title')
    {{ __('Inventory Item Details') }} - {{ $item->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('Inventory') }}</a></li>
    <li class="breadcrumb-item">{{ $item->name }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary me-2">
            <i class="ti ti-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBatchModal" title="{{ __('Add Purchase Batch') }}">
            <i class="ti ti-plus me-1"></i>{{ __('Add Purchase Batch') }}
        </button>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Item Overview Cards -->
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6 class="text-muted">{{ __('Total Purchased') }}</h6>
                <h4>{{ number_format($item->batches->sum('quantity_purchased'), 2) }} {{ $item->unit }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6 class="text-muted">{{ __('Total Used') }}</h6>
                <h4>{{ number_format($item->batches->sum('quantity_purchased') - $item->batches->sum('quantity_available'), 2) }} {{ $item->unit }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 bg-light-success">
                <h6 class="text-muted text-success fw-bold">{{ __('Available Stock') }}</h6>
                <h4 class="text-success fw-bold">{{ number_format($item->batches->sum('quantity_available'), 2) }} {{ $item->unit }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6 class="text-muted">{{ __('Total Est. Value') }}</h6>
                <h4>{{ \Auth::user()->priceFormat($item->batches->sum(function($b) { return $b->quantity_available * $b->unit_cost; })) }}</h4>
            </div>
        </div>
    </div>

    <!-- Details Tabs -->
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="inventoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="batches-tab" data-bs-toggle="tab" data-bs-target="#batches" type="button" role="tab" aria-controls="batches" aria-selected="true">
                                <i class="ti ti-package me-2"></i>{{ __('Purchase Batches') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="usages-tab" data-bs-toggle="tab" data-bs-target="#usages" type="button" role="tab" aria-controls="usages" aria-selected="false">
                                <i class="ti ti-history me-2"></i>{{ __('Usage & Deduction History') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="inventoryTabsContent">
                        
                        <!-- Purchase Batches Tab -->
                        <div class="tab-pane fade show active" id="batches" role="tabpanel" aria-labelledby="batches-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Batch ID') }}</th>
                                            <th>{{ __('Purchase Date') }}</th>
                                            <th>{{ __('Supplier') }}</th>
                                            <th>{{ __('Qty Purchased') }}</th>
                                            <th>{{ __('Qty Available') }}</th>
                                            <th>{{ __('Unit Cost') }}</th>
                                            <th>{{ __('Total Cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($batches as $batch)
                                            <tr>
                                                <td>#{{ $batch->id }}</td>
                                                <td>{{ \Auth::user()->dateFormat($batch->purchase_date) }}</td>
                                                <td>{{ $batch->supplier ? $batch->supplier->name : __('Direct / Petty Purchase') }}</td>
                                                <td>{{ number_format($batch->quantity_purchased, 2) }} {{ $item->unit }}</td>
                                                <td>
                                                    @if($batch->quantity_available <= 0)
                                                        <span class="badge bg-secondary rounded">{{ __('Consumed') }}</span>
                                                    @else
                                                        <span class="badge bg-success rounded">{{ number_format($batch->quantity_available, 2) }} {{ $item->unit }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Auth::user()->priceFormat($batch->unit_cost) }}</td>
                                                <td>{{ \Auth::user()->priceFormat($batch->total_cost) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">{{ __('No purchase batches recorded yet.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Usage History Tab -->
                        <div class="tab-pane fade" id="usages" role="tabpanel" aria-labelledby="usages-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Sales Order #') }}</th>
                                            <th>{{ __('Client Name') }}</th>
                                            <th>{{ __('Quantity Consumed') }}</th>
                                            <th>{{ __('Batch Cost Rate') }}</th>
                                            <th>{{ __('Total Value (FIFO)') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($usages as $usage)
                                            <tr>
                                                <td>{{ \Auth::user()->dateFormat($usage->created_at) }}</td>
                                                <td>
                                                    @if($usage->order)
                                                        <a href="{{ route('sales-orders.show', $usage->sales_order_id) }}" class="text-primary fw-bold">
                                                            {{ $usage->order->order_number }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ ($usage->order && $usage->order->customer) ? $usage->order->customer->name : '-' }}</td>
                                                <td>{{ number_format($usage->quantity_used, 2) }} {{ $item->unit }}</td>
                                                <td>{{ \Auth::user()->priceFormat($usage->unit_cost) }}</td>
                                                <td>{{ \Auth::user()->priceFormat($usage->total_cost) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">{{ __('No usage history recorded yet.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Purchase Batch Modal -->
    <div class="modal fade" id="addBatchModal" tabindex="-1" aria-labelledby="addBatchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBatchModalLabel">{{ __('Record Purchase Batch') }} - {{ $item->name }}</h5>
                    <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                </div>
                {{ Form::open(['route' => ['inventory.batch.store', $item->id], 'method' => 'POST']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                {{ Form::label('purchase_date', __('Purchase Date'), ['class' => 'form-label']) }}
                                {{ Form::date('purchase_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                {{ Form::label('supplier_id', __('Supplier (From Accounting Setup)'), ['class' => 'form-label']) }}
                                <select name="supplier_id" id="supplier_id" class="form-control select2">
                                    <option value="">{{ __('Select Supplier (Optional / Direct Purchase)') }}</option>
                                    @foreach($suppliers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                {{ Form::label('quantity_purchased', __('Quantity Purchased'), ['class' => 'form-label']) }}
                                <div class="input-group input-group-merge">
                                    {{ Form::number('quantity_purchased', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0.01', 'placeholder' => 'e.g. 100', 'required' => 'required']) }}
                                    <span class="input-group-text">{{ $item->unit }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                {{ Form::label('unit_cost', __('Unit Cost Rate'), ['class' => 'form-label']) }}
                                {{ Form::number('unit_cost', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'placeholder' => 'e.g. 15.50', 'required' => 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Batch') }}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
