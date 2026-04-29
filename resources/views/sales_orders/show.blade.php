@extends('layouts.admin')
@push('css-page')
<style>
    /* Prevent dropdown text overlap with arrow icon */
    .form-control.select2-hidden-accessible + .select2-container .select2-selection--single,
    select.form-control {
        padding-right: 2rem !important;
        position: relative;
    }
    
    /* Ensure table rows in PO and CI don't wrap/break */
    #po-items-table tr td, #ci-tankers-table tr td {
        vertical-align: middle;
        padding: 0.5rem;
    }
    #po-items-table input, #ci-tankers-table input, #ci-tankers-table select {
        min-width: 80px;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush
@section('page-title')
    {{__('Sales Order Workflow')}} - {{ $order->order_number }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('sales-orders.index')}}">{{__('Sales Orders')}}</a></li>
    <li class="breadcrumb-item">{{ $order->order_number }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'PO' ? 'active' : '' }} {{ $order->po ? 'text-success' : '' }}" id="pills-po-tab" data-bs-toggle="pill" data-bs-target="#pills-po" type="button" role="tab">
                                @if($order->po) <i class="ti ti-circle-check me-1"></i> @endif {{ __('1. PO') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'PI' ? 'active' : '' }} {{ !$order->po ? 'disabled' : ($order->pi ? 'text-success' : '') }}" id="pills-pi-tab" data-bs-toggle="pill" data-bs-target="#pills-pi" type="button" role="tab">
                                @if($order->pi) <i class="ti ti-circle-check me-1"></i> @endif {{ __('2. PI') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'LC' ? 'active' : '' }} {{ !$order->pi ? 'disabled' : ($order->lc ? 'text-success' : '') }}" id="pills-lc-tab" data-bs-toggle="pill" data-bs-target="#pills-lc" type="button" role="tab">
                                @if($order->lc) <i class="ti ti-circle-check me-1"></i> @endif {{ __('3. LC') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'CI' ? 'active' : '' }} {{ !$order->lc ? 'disabled' : ($order->ci ? 'text-success' : '') }}" id="pills-ci-tab" data-bs-toggle="pill" data-bs-target="#pills-ci" type="button" role="tab">
                                @if($order->ci) <i class="ti ti-circle-check me-1"></i> @endif {{ __('4. CI') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'Packing List' ? 'active' : '' }} {{ !$order->ci ? 'disabled' : ($order->packingList ? 'text-success' : '') }}" id="pills-pl-tab" data-bs-toggle="pill" data-bs-target="#pills-pl" type="button" role="tab">
                                @if($order->packingList) <i class="ti ti-circle-check me-1"></i> @endif {{ __('5. PL') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'Consignment Note' ? 'active' : '' }} {{ !$order->packingList ? 'disabled' : ($order->consignmentNote ? 'text-success' : '') }}" id="pills-cn-tab" data-bs-toggle="pill" data-bs-target="#pills-cn" type="button" role="tab">
                                @if($order->consignmentNote) <i class="ti ti-circle-check me-1"></i> @endif {{ __('6. CN') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->current_step == 'Received Details' ? 'active' : '' }} {{ !$order->consignmentNote ? 'disabled' : ($order->status == 'completed' || $order->status == 'finalized' ? 'text-success' : '') }}" id="pills-rd-tab" data-bs-toggle="pill" data-bs-target="#pills-rd" type="button" role="tab">
                                @if($order->status == 'completed' || $order->status == 'finalized') <i class="ti ti-circle-check me-1"></i> @endif {{ __('7. Received Details') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ ($order->status != 'completed' && $order->status != 'finalized') ? 'disabled' : '' }} {{ $order->status == 'finalized' ? 'text-success' : '' }}" id="pills-delivery-tab" data-bs-toggle="pill" data-bs-target="#pills-delivery" type="button" role="tab">
                                @if($order->status == 'finalized') <i class="ti ti-circle-check me-1"></i> @endif {{ __('8. Delivery') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade {{ $order->current_step == 'PO' ? 'show active' : '' }}" id="pills-po" role="tabpanel">
                            @include('sales_orders.steps.po')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'PI' ? 'show active' : '' }}" id="pills-pi" role="tabpanel">
                            @include('sales_orders.steps.pi')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'LC' ? 'show active' : '' }}" id="pills-lc" role="tabpanel">
                            @include('sales_orders.steps.lc')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'CI' ? 'show active' : '' }}" id="pills-ci" role="tabpanel">
                            @include('sales_orders.steps.ci')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'Packing List' ? 'show active' : '' }}" id="pills-pl" role="tabpanel">
                            @include('sales_orders.steps.pl')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'Consignment Note' ? 'show active' : '' }}" id="pills-cn" role="tabpanel">
                            @include('sales_orders.steps.cn')
                        </div>
                        <div class="tab-pane fade {{ $order->current_step == 'Received Details' ? 'show active' : '' }}" id="pills-rd" role="tabpanel">
                            @include('sales_orders.steps.received_details')
                        </div>
                        <div class="tab-pane fade {{ $order->status == 'finalized' || session('jump_to_delivery') ? 'show active' : '' }}" id="pills-delivery" role="tabpanel">
                            <div class="text-center p-5">
                                @if($order->status == 'completed')
                                    <h4 class="text-warning"><i class="ti ti-info-circle fs-1"></i></h4>
                                    <h3>{{ __('Finalize Sales Order') }}</h3>
                                    <p>{{ __('Please confirm to finalize this order and send it for transport management.') }}</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#finalizeModal">
                                        {{ __('Finalize Order') }}
                                    </button>
                                @elseif($order->status == 'finalized')
                                    <h4 class="text-success"><i class="ti ti-circle-check fs-1"></i></h4>
                                    <h3>{{ __('Order Finalized') }}</h3>
                                    <p>{{ __('This sales order has been finalized and sent to transport management.') }}</p>
                                    <a href="{{ route('transports.create') }}?sales_order_id={{ $order->id }}" class="btn btn-primary">{{ __('Create Transport Request') }}</a>
                                @else
                                    <h4 class="text-muted"><i class="ti ti-lock fs-1"></i></h4>
                                    <h3>{{ __('Waiting for Received Details') }}</h3>
                                    <p>{{ __('Please complete the previous steps to enable delivery.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Finalize Modal -->
    <div class="modal fade" id="finalizeModal" tabindex="-1" aria-labelledby="finalizeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizeModalLabel">{{ __('Confirm Finalization') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to finalize this sales order? This will notify the transport management team.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    {{ Form::open(['route' => ['sales-orders.finalize', $order->id], 'method' => 'post']) }}
                        <button type="submit" class="btn btn-primary">{{ __('Confirm & Finalize') }}</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        var units = @json($units);
        var currencies = @json($currencies);

        function getUnitOptions(selected = '') {
            var options = '';
            $.each(units, function(k, v) {
                options += `<option value="${k}" ${k == selected ? 'selected' : ''}>${v}</option>`;
            });
            options += `<option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>`;
            return options;
        }

        function getCurrencyOptions(selected = '') {
            var options = '';
            $.each(currencies, function(k, v) {
                options += `<option value="${k}" ${k == selected ? 'selected' : ''}>${v}</option>`;
            });
            options += `<option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>`;
            return options;
        }

        // PO Logic
        $(document).on('click', '.add-item', function() {
            var index = $('#po-items-table tbody tr').length;
            var html = `<tr>
                <td><input type="text" name="items[${index}][item]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="items[${index}][description]" class="form-control form-control-sm"></td>
                <td><input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty" required></td>
                <td><input type="text" name="items[${index}][unit]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="items[${index}][price]" class="form-control form-control-sm price" required></td>
                <td><input type="number" step="0.01" name="items[${index}][total]" class="form-control form-control-sm total" readonly></td>
                <td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#po-items-table tbody').append(html);
        });

        // CI Logic
        $(document).on('click', '.add-tanker', function() {
            var index = $('#ci-tankers-table tbody tr').length;
            var html = `<tr>
                <td><input type="text" name="tankers[${index}][tanker_number]" class="form-control form-control-sm" required></td>
                <td><input type="number" step="0.001" name="tankers[${index}][qty_mt]" class="form-control form-control-sm t-qty" required></td>
                <td>
                    <select name="tankers[${index}][quantity_unit]" class="form-control form-control-sm unit-select" required>
                        ${getUnitOptions('MT')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="tankers[${index}][cpt_usd]" class="form-control form-control-sm t-cpt" required></td>
                <td>
                    <select name="tankers[${index}][currency]" class="form-control form-control-sm curr-select" required>
                        ${getCurrencyOptions('USD')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="tankers[${index}][total_amount]" class="form-control form-control-sm t-total" readonly></td>
                <td><button type="button" class="btn btn-danger btn-xs remove-tanker"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#ci-tankers-table tbody').append(html);
        });

        // Calculation logic
        $(document).on('click', '.remove-item', function() { $(this).closest('tr').remove(); calculateGrandTotal(); });
        $(document).on('keyup change', '.qty, .price', function() {
            var tr = $(this).closest('tr');
            var total = (tr.find('.qty').val() || 0) * (tr.find('.price').val() || 0);
            tr.find('.total').val(total.toFixed(2));
            calculateGrandTotal();
        });
        function calculateGrandTotal() {
            var grandTotal = 0;
            $('.total').each(function() { grandTotal += parseFloat($(this).val() || 0); });
            $('#grand_total').val(grandTotal.toFixed(2));
            $('#grand_total_display').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }

        $(document).on('click', '.remove-tanker', function() { $(this).closest('tr').remove(); calculateCITotals(); });
        $(document).on('keyup change', '.t-qty, .t-cpt', function() {
            var tr = $(this).closest('tr');
            var total = (tr.find('.t-qty').val() || 0) * (tr.find('.t-cpt').val() || 0);
            tr.find('.t-total').val(total.toFixed(2));
            calculateCITotals();
        });
        function calculateCITotals() {
            var tQty = 0; var tAmt = 0;
            $('.t-qty').each(function() { tQty += parseFloat($(this).val() || 0); });
            $('.t-total').each(function() { tAmt += parseFloat($(this).val() || 0); });
            $('#ci_total_qty').text(tQty.toFixed(3));
            $('#ci_total_amount').text(tAmt.toFixed(2));
        }
        calculateCITotals();

        // AJAX for Add New
        $(document).on('change', '.unit-select', function() {
            var select = $(this);
            if (select.val() === 'ADD_NEW_UNIT') {
                var newName = prompt("{{ __('Enter new unit name (e.g. Box, Drum):') }}");
                if (newName) {
                    $.ajax({
                        url: '{{ route("sales-orders.add-unit") }}',
                        method: 'POST',
                        data: { name: newName, _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                units[res.data.name] = res.data.name;
                                $('.unit-select').each(function() {
                                    var s = $(this);
                                    var current = s.val();
                                    s.html(getUnitOptions(current === 'ADD_NEW_UNIT' ? res.data.name : current));
                                });
                                show_toastr('Success', 'Unit added successfully', 'success');
                            }
                        }
                    });
                } else { select.val('MT'); }
            }
        });

        $(document).on('change', '.curr-select', function() {
            var select = $(this);
            if (select.val() === 'ADD_NEW_CURR') {
                var newCode = prompt("{{ __('Enter new currency code (e.g. AED, INR):') }}");
                if (newCode) {
                    $.ajax({
                        url: '{{ route("sales-orders.add-currency") }}',
                        method: 'POST',
                        data: { code: newCode.toUpperCase(), _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                currencies[res.data.code] = res.data.code;
                                $('.curr-select').each(function() {
                                    var s = $(this);
                                    var current = s.val();
                                    s.html(getCurrencyOptions(current === 'ADD_NEW_CURR' ? res.data.code : current));
                                });
                                show_toastr('Success', 'Currency added successfully', 'success');
                            }
                        }
                    });
                } else { select.val('USD'); }
            }
        });

        $(document).on('keyup change', '.w-gross, .w-tare', function() {
            var tr = $(this).closest('tr');
            var net = (parseFloat(tr.find('.w-gross').val()) || 0) - (parseFloat(tr.find('.w-tare').val()) || 0);
            tr.find('.w-net').val(net.toFixed(3));
        });
    });
</script>
@endpush
