@extends('layouts.admin')
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
                            <button class="nav-link {{ $order->current_step == 'Received Details' ? 'active' : '' }} {{ !$order->consignmentNote ? 'disabled' : ($order->status == 'completed' ? 'text-success' : '') }}" id="pills-rd-tab" data-bs-toggle="pill" data-bs-target="#pills-rd" type="button" role="tab">
                                @if($order->status == 'completed') <i class="ti ti-circle-check me-1"></i> @endif {{ __('7. Received Details') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $order->status != 'completed' ? 'disabled' : '' }}" id="pills-delivery-tab" data-bs-toggle="pill" data-bs-target="#pills-delivery" type="button" role="tab">
                                {{ __('8. Delivery') }}
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
                        <div class="tab-pane fade" id="pills-delivery" role="tabpanel">
                            <div class="text-center p-5">
                                <h4 class="text-success"><i class="ti ti-circle-check fs-1"></i></h4>
                                <h3>{{ __('Ready for Delivery') }}</h3>
                                <p>{{ __('All sales documents are completed. You can now proceed to delivery.') }}</p>
                                <a href="{{ route('transports.create') }}?sales_order_id={{ $order->id }}" class="btn btn-primary">{{ __('Delivery') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    // Dynamic Grid Logic
    $(document).on('click', '.add-item', function() {
        var index = $('#po-items-table tbody tr').length;
        var html = `<tr>
            <td><input type="text" name="items[${index}][item]" class="form-control" required></td>
            <td><input type="text" name="items[${index}][description]" class="form-control"></td>
            <td><input type="number" name="items[${index}][qty]" class="form-control qty" required></td>
            <td><input type="text" name="items[${index}][unit]" class="form-control"></td>
            <td><input type="number" step="0.01" name="items[${index}][price]" class="form-control price" required></td>
            <td><input type="number" step="0.01" name="items[${index}][total]" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#po-items-table tbody').append(html);
    });

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
        $('#grand_total_display').text(grandTotal.toFixed(2));
    }

    $(document).on('click', '.add-tanker', function() {
        var index = $('#ci-tankers-table tbody tr').length;
        var html = `<tr>
            <td><input type="text" name="tankers[${index}][tanker_number]" class="form-control" required></td>
            <td><input type="number" step="0.001" name="tankers[${index}][qty_mt]" class="form-control t-qty" required></td>
            <td><input type="number" step="0.01" name="tankers[${index}][cpt_usd]" class="form-control t-cpt" required></td>
            <td><input type="number" step="0.01" name="tankers[${index}][total_amount]" class="form-control t-total" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-tanker"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#ci-tankers-table tbody').append(html);
    });

    $(document).on('click', '.remove-tanker', function() { $(this).closest('tr').remove(); });

    $(document).on('keyup change', '.t-qty, .t-cpt', function() {
        var tr = $(this).closest('tr');
        var total = (tr.find('.t-qty').val() || 0) * (tr.find('.t-cpt').val() || 0);
        tr.find('.t-total').val(total.toFixed(2));
    });

    $(document).on('keyup change', '.w-gross, .w-tare', function() {
        var tr = $(this).closest('tr');
        var net = (tr.find('.w-gross').val() || 0) - (tr.find('.w-tare').val() || 0);
        tr.find('.w-net').val(net.toFixed(3));
    });
</script>
@endpush
