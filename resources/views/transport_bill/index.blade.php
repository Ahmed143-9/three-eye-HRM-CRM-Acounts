@extends('layouts.admin')
@section('page-title')
    {{__('Transport Bills')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Transport Bill')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>{{__('Bill ID')}}</th>
                            <th>{{__('Transport ID')}}</th>
                            <th>{{__('Client')}}</th>
                            <th>{{__('Driver')}}</th>
                            <th>{{__('Truck No.')}}</th>
                            <th>{{__('Starting Date')}}</th>
                            <th>{{__('Total Amount')}}</th>
                            <th>{{__('Status')}}</th>
                            <th width="160px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($bills as $bill)
                            @php
                                $payable     = $bill->payable_id > 0 ? \App\Models\Payable::find($bill->payable_id) : null;
                                $totalAmount = $payable ? (int)$payable->total_amount : 0;
                                // Status is controlled directly on the transport record:
                                // pending = no amounts saved yet | paid = accountant saved billing
                                $billStatus  = $bill->status ?? 'pending';
                            @endphp
                            <tr>
                                <td>{{ $payable ? $payable->unique_id : '—' }}</td>
                                <td>{{ $bill->unique_id }}</td>
                                <td>
                                    {{ $bill->client_id > 0
                                        ? ($bill->client ? $bill->client->name : '—')
                                        : ($bill->manual_client_name ?? '—') }}
                                </td>
                                <td>{{ $bill->driver_name }}</td>
                                <td>{{ $bill->truck_number }}</td>
                                <td>{{ $bill->starting_date ? \Auth::user()->dateFormat($bill->starting_date) : '—' }}</td>
                                <td>
                                    @if($totalAmount > 0)
                                        ৳ {{ number_format($totalAmount) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($billStatus === 'paid')
                                        <span class="badge bg-success p-2 px-3 rounded">
                                            <i class="ti ti-circle-check me-1"></i>{{ __('Paid') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark p-2 px-3 rounded">
                                            <i class="ti ti-clock me-1"></i>{{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('transport.bill.edit', $bill->id) }}"
                                       class="btn btn-sm bg-info text-white"
                                       data-bs-toggle="tooltip" title="{{ __('Enter Costs') }}">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    @if($billStatus !== 'paid')
                                    <a href="{{ route('transport.bill.pay', $bill->id) }}"
                                       class="btn btn-sm btn-primary ms-1 pay-bill-btn"
                                       data-id="{{ $bill->id }}"
                                       data-bs-toggle="tooltip" title="{{ __('Pay Bill') }}">
                                        <i class="ti ti-credit-card"></i>
                                    </a>
                                    @endif
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

@push('script-page')
<script>
$(document).ready(function() {
    // When Pay Bill is clicked, mark as seen via AJAX then follow link
    $(document).on('click', '.pay-bill-btn', function(e) {
        var id   = $(this).data('id');
        var href = $(this).attr('href');
        $.ajax({
            url: '/transport-bills/' + id + '/seen',
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            complete: function() {
                window.location.href = href;
            }
        });
        e.preventDefault();
    });
});
</script>
@endpush
