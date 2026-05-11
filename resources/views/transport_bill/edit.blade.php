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
            
            <div class="row">
                {{-- Left Side: Payable (Cost to us) --}}
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header d-flex align-items-center justify-content-between bg-light-danger">
                            <h5 class="mb-0 text-danger"><i class="ti ti-receipt-2 me-2"></i>{{__('Cost Line Items (Payable)')}}</h5>
                            <button type="button" class="btn btn-sm btn-danger" id="add-payable-row">
                                <i class="ti ti-plus me-1"></i>{{__('Add Row')}}
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="payable-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{__('Description')}}</th>
                                            <th width="150px">{{__('Amount')}}</th>
                                            <th width="40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="payable-body">
                                        @if($payable && $payable->items->count() > 0)
                                            @foreach($payable->items as $i => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $item->order_details }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $i }}][amount]" class="form-control form-control-sm payable-amount" value="{{ (int)$item->amount }}" min="0" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr class="item-row">
                                                <td><input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="e.g. Truck Rent" required></td>
                                                <td><input type="number" name="items[0][amount]" class="form-control form-control-sm payable-amount" value="0" min="0" required></td>
                                                <td><button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td class="text-end fw-bold">{{__('Total Cost')}}</td>
                                            <td colspan="2" class="fw-bold">৳ <span id="total-payable">0</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Receivable (Income from Client) --}}
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header d-flex align-items-center justify-content-between bg-light-success">
                            <h5 class="mb-0 text-success"><i class="ti ti-file-invoice me-2"></i>{{__('Client Billing (Receivable)')}}</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-receivable-row">
                                <i class="ti ti-plus me-1"></i>{{__('Add Row')}}
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="receivable-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{__('Description')}}</th>
                                            <th width="150px">{{__('Amount')}}</th>
                                            <th width="40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="receivable-body">
                                        @if($receivable && $receivable->items->count() > 0)
                                            @foreach($receivable->items as $i => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <input type="text" name="receivable_items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $item->order_details }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="receivable_items[{{ $i }}][amount]" class="form-control form-control-sm receivable-amount" value="{{ (int)$item->amount }}" min="0" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr class="item-row">
                                                <td><input type="text" name="receivable_items[0][description]" class="form-control form-control-sm" placeholder="e.g. Service Charge" required></td>
                                                <td><input type="number" name="receivable_items[0][amount]" class="form-control form-control-sm receivable-amount" value="0" min="0" required></td>
                                                <td><button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td class="text-end fw-bold">{{__('Total Income')}}</td>
                                            <td colspan="2" class="fw-bold">৳ <span id="total-receivable">0</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="card mb-4 bg-light">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-center gap-5 align-items-center">
                        <div class="text-center">
                            <span class="text-muted d-block small">{{__('TOTAL COST')}}</span>
                            <h4 class="text-danger mb-0">৳ <span id="summary-payable">0</span></h4>
                        </div>
                        <div class="fs-2 text-muted">-</div>
                        <div class="text-center">
                            <span class="text-muted d-block small">{{__('TOTAL INCOME')}}</span>
                            <h4 class="text-success mb-0">৳ <span id="summary-receivable">0</span></h4>
                        </div>
                        <div class="fs-2 text-muted">=</div>
                        <div class="text-center bg-white px-4 py-2 rounded shadow-sm">
                            <span class="text-muted d-block small fw-bold">{{__('NET PROFIT')}}</span>
                            <h3 class="mb-0" id="net-profit-text">৳ 0</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('transport.bill.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{{__('Back')}}
                    </a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="ti ti-device-floppy me-1"></i>{{__('Save & Finalize Bill')}}
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
    // Calculate totals and profit
    function updateCalculations() {
        var totalPayable = 0;
        $('.payable-amount').each(function() {
            totalPayable += parseInt($(this).val()) || 0;
        });
        $('#total-payable').text(totalPayable.toLocaleString());
        $('#summary-payable').text(totalPayable.toLocaleString());

        var totalReceivable = 0;
        $('.receivable-amount').each(function() {
            totalReceivable += parseInt($(this).val()) || 0;
        });
        $('#total-receivable').text(totalReceivable.toLocaleString());
        $('#summary-receivable').text(totalReceivable.toLocaleString());

        var netProfit = totalReceivable - totalPayable;
        var profitText = $('#net-profit-text');
        profitText.text('৳ ' + netProfit.toLocaleString());
        
        if (netProfit > 0) {
            profitText.removeClass('text-danger').addClass('text-success');
        } else if (netProfit < 0) {
            profitText.removeClass('text-success').addClass('text-danger');
        } else {
            profitText.removeClass('text-success text-danger');
        }
    }

    // Add Payable Row
    $('#add-payable-row').on('click', function() {
        var idx = $('#payable-body tr').length;
        var html = `<tr class="item-row">
            <td><input type="text" name="items[${idx}][description]" class="form-control form-control-sm" placeholder="Description" required></td>
            <td><input type="number" name="items[${idx}][amount]" class="form-control form-control-sm payable-amount" value="0" min="0" required></td>
            <td><button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#payable-body').append(html);
        updateCalculations();
    });

    // Add Receivable Row
    $('#add-receivable-row').on('click', function() {
        var idx = $('#receivable-body tr').length;
        var html = `<tr class="item-row">
            <td><input type="text" name="receivable_items[${idx}][description]" class="form-control form-control-sm" placeholder="Description" required></td>
            <td><input type="number" name="receivable_items[${idx}][amount]" class="form-control form-control-sm receivable-amount" value="0" min="0" required></td>
            <td><button type="button" class="btn btn-sm text-danger remove-row"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#receivable-body').append(html);
        updateCalculations();
    });

    // Remove Row
    $(document).on('click', '.remove-row', function() {
        var tbody = $(this).closest('tbody');
        if (tbody.find('tr').length > 1) {
            $(this).closest('tr').remove();
            
            // Re-index names
            tbody.find('tr').each(function(i) {
                $(this).find('input').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/\[\d+\]/, '[' + i + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
            updateCalculations();
        }
    });

    // Live Calculation
    $(document).on('input', '.payable-amount, .receivable-amount', function() {
        updateCalculations();
    });

    // Initial Calculation
    updateCalculations();
});
</script>
@endpush
