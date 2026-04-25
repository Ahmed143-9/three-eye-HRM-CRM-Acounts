@extends('layouts.admin')
@section('page-title')
    {{__('Transport Bill — Cost Entry')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('transport.bill.index')}}">{{__('Transport Bill')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit Bill')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        {{-- Transport Summary Card --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-truck text-primary fs-5"></i>
                <h5 class="mb-0">{{__('Transport Details')}} — <span class="text-muted">{{ $transport->unique_id }}</span></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Client')}}</label>
                        <p class="fw-semibold mb-0">
                            {{ $transport->client_id > 0 ? ($transport->client ? $transport->client->name : '—') : ($transport->manual_client_name ?? '—') }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Driver Name')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->driver_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Contact Number')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->contact_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Truck Number')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->truck_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Starting Date')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->starting_date ? \Auth::user()->dateFormat($transport->starting_date) : '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('Delivery Date')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->delivery_date ? \Auth::user()->dateFormat($transport->delivery_date) : '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('LC (Letter of Credit)')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->lc ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">{{__('C.I (Commercial Invoice)')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->ci ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">{{__('Location / Address')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->location_address ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">{{__('Item Description')}}</label>
                        <p class="fw-semibold mb-0">{{ $transport->item_description ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bill Cost Entry --}}
        <form action="{{ route('transport.bill.update', $transport->id) }}" method="POST" id="bill-form">
            @csrf
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ti ti-receipt-2 me-2 text-warning"></i>{{__('Cost Line Items')}}</h5>
                    <button type="button" class="btn btn-sm btn-success" id="add-row">
                        <i class="ti ti-plus me-1"></i>{{__('Add Row')}}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{__('Description')}}</th>
                                    <th width="180px">{{__('Amount (৳)')}}</th>
                                    <th width="60px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                @if($payable && $payable->items->count() > 0)
                                    @foreach($payable->items as $i => $item)
                                    <tr class="item-row">
                                        <td class="row-num">{{ $i + 1 }}</td>
                                        <td>
                                            <input type="text" name="items[{{ $i }}][description]"
                                                class="form-control" value="{{ $item->order_details }}"
                                                placeholder="{{__('e.g. Delivery charge, Fuel cost...')}}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][amount]"
                                                class="form-control amount-input" value="{{ (int)$item->amount }}"
                                                min="0" step="1" placeholder="0" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="item-row">
                                        <td class="row-num">1</td>
                                        <td>
                                            <input type="text" name="items[0][description]"
                                                class="form-control"
                                                placeholder="{{__('e.g. Delivery charge, Fuel cost...')}}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][amount]"
                                                class="form-control amount-input"
                                                min="0" step="1" placeholder="0" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">{{__('Total Amount')}}</td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text fw-bold">৳</span>
                                            <input type="text" id="grand-total" class="form-control fw-bold bg-light"
                                                value="{{ $payable ? (int)$payable->total_amount : 0 }}" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('transport.bill.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{{__('Back')}}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>{{__('Save Bill')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    var rowIndex = $('#items-body tr').length;

    // Calculate total
    function calcTotal() {
        var total = 0;
        $('.amount-input').each(function() {
            var val = parseInt($(this).val()) || 0;
            total += val;
        });
        $('#grand-total').val(total);
    }

    // Re-number rows
    function reNumber() {
        $('#items-body tr').each(function(i) {
            $(this).find('.row-num').text(i + 1);
            // Update name indices
            $(this).find('input[name*="description"]').attr('name', 'items[' + i + '][description]');
            $(this).find('input[name*="amount"]').attr('name', 'items[' + i + '][amount]');
        });
        rowIndex = $('#items-body tr').length;
    }

    // Add row
    $('#add-row').on('click', function() {
        var newRow = `<tr class="item-row">
            <td class="row-num">${rowIndex + 1}</td>
            <td>
                <input type="text" name="items[${rowIndex}][description]"
                    class="form-control"
                    placeholder="{{ __('e.g. Delivery charge, Fuel cost...') }}" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][amount]"
                    class="form-control amount-input"
                    min="0" step="1" placeholder="0" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        </tr>`;
        $('#items-body').append(newRow);
        rowIndex++;
        calcTotal();
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if ($('#items-body tr').length > 1) {
            $(this).closest('tr').remove();
            reNumber();
            calcTotal();
        } else {
            $(this).closest('tr').find('input').val('');
            calcTotal();
        }
    });

    // Live total calculation
    $(document).on('input', '.amount-input', function() {
        calcTotal();
    });

    // Initial calc
    calcTotal();
});
</script>
@endpush
