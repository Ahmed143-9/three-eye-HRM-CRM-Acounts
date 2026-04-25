@extends('layouts.admin')
@section('page-title')
    {{__('Transport Details')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('transports.index')}}">{{__('Transport Management')}}</a></li>
    <li class="breadcrumb-item">{{__('Details')}}</li>
@endsection

@section('action-btn')
<div class="float-end d-flex gap-2">
    <a href="{{ route('transports.edit', $transport->id) }}" class="btn btn-sm btn-info">
        <i class="ti ti-pencil me-1"></i>{{__('Edit')}}
    </a>
    <a href="{{ route('transports.index') }}" class="btn btn-sm btn-secondary">
        <i class="ti ti-arrow-left me-1"></i>{{__('Back')}}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-truck me-2 text-primary"></i>
                    {{ $transport->unique_id }}
                    <span class="badge ms-2 {{ $transport->status == 'pending' ? 'bg-warning' : 'bg-success' }}">
                        {{ ucfirst($transport->status) }}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">{{__('Client')}}</label>
                        <p class="mb-0 fw-bold">
                            {{ $transport->client_id > 0 ? ($transport->client ? $transport->client->name : '—') : ($transport->manual_client_name ?? '—') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">{{__('Location / Address')}}</label>
                        <p class="mb-0">{{ $transport->location_address ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('Driver Name')}}</label>
                        <p class="mb-0 fw-bold">{{ $transport->driver_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('Contact Number')}}</label>
                        <p class="mb-0">{{ $transport->contact_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('Truck Number')}}</label>
                        <p class="mb-0">{{ $transport->truck_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('Starting Date')}}</label>
                        <p class="mb-0">{{ $transport->starting_date ? \Auth::user()->dateFormat($transport->starting_date) : '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('Delivery Date')}}</label>
                        <p class="mb-0">
                            @if($transport->delivery_date)
                                {{ \Auth::user()->dateFormat($transport->delivery_date) }}
                            @else
                                <span class="text-muted fst-italic">{{__('Not set yet')}}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('LC (Letter of Credit)')}}</label>
                        <p class="mb-0">{{ $transport->lc ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">{{__('C.I (Commercial Invoice)')}}</label>
                        <p class="mb-0">{{ $transport->ci ?? '—' }}</p>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-semibold">{{__('Item / Goods Description')}}</label>
                        <p class="mb-0">{{ $transport->item_description ?? '—' }}</p>
                    </div>
                    @if($transport->payable_id > 0)
                    <div class="col-md-12">
                        <hr>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-label text-muted small fw-semibold">{{__('Accounting Bill ID')}}</label>
                                <p class="mb-0 fw-bold text-primary">
                                    {{ \App\Models\Payable::find($transport->payable_id)->unique_id ?? '—' }}
                                </p>
                            </div>
                            <a href="{{ route('transport.bill.edit', $transport->id) }}" class="btn btn-warning">
                                <i class="ti ti-credit-card me-1"></i>{{__('Manage Bill')}}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
