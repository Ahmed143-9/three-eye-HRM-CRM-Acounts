<h5>{{ __('Step 4: Commercial Invoice (CI)') }}</h5>
<hr>
<div class="row mb-3">
    <div class="col-md-4">
        <strong>{{ __('PO Reference:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('PI Reference:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }} ({{ $order->pi->pi_date ?? '' }})
    </div>
    <div class="col-md-4">
        <strong>{{ __('LC Reference:') }}</strong> {{ $order->lc->lc_no ?? 'N/A' }} ({{ $order->lc->lc_date ?? '' }})
    </div>
</div>

{{ Form::open(['route' => ['sales-orders.ci.store', $order->id], 'method' => 'post']) }}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('ci_number', __('CI Number'), ['class' => 'form-label']) }}
            {{ Form::text('ci_number', $order->ci->ci_number ?? 'CI-' . time(), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('ci_date', __('CI Date'), ['class' => 'form-label']) }}
            {{ Form::date('ci_date', $order->ci->ci_date ?? date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('lc_validity_date', __('LC Validity Date'), ['class' => 'form-label']) }}
            {{ Form::date('lc_validity_date', $order->ci->lc_validity_date ?? (optional($order->lc)->lc_validity_date), ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('latest_shipment_date', __('Latest Shipment Date'), ['class' => 'form-label']) }}
            {{ Form::date('latest_shipment_date', $order->ci->latest_shipment_date ?? (optional($order->lc)->latest_shipment_date), ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<h6 class="mt-4">{{ __('Tanker Details') }}</h6>
<div class="table-responsive">
    <table class="table" id="ci-tankers-table">
        <thead>
            <tr>
                <th>{{ __('Tanker Number') }}</th>
                <th>{{ __('QTY (MT)') }}</th>
                <th>{{ __('CPT (USD)') }}</th>
                <th>{{ __('Total Amount (USD)') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if($order->ci && $order->ci->tankers->count() > 0)
                @foreach($order->ci->tankers as $index => $tanker)
                    <tr>
                        <td><input type="text" name="tankers[{{$index}}][tanker_number]" class="form-control" value="{{$tanker->tanker_number}}" required></td>
                        <td><input type="number" step="0.001" name="tankers[{{$index}}][qty_mt]" class="form-control t-qty" value="{{$tanker->quantity_mt}}" required></td>
                        <td><input type="number" step="0.01" name="tankers[{{$index}}][cpt_usd]" class="form-control t-cpt" value="{{$tanker->cpt_usd}}" required></td>
                        <td><input type="number" step="0.01" name="tankers[{{$index}}][total_amount]" class="form-control t-total" value="{{$tanker->total_amount_usd}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-tanker"><i class="ti ti-trash"></i></button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="tankers[0][tanker_number]" class="form-control" required></td>
                    <td><input type="number" step="0.001" name="tankers[0][qty_mt]" class="form-control t-qty" required></td>
                    <td><input type="number" step="0.01" name="tankers[0][cpt_usd]" class="form-control t-cpt" required></td>
                    <td><input type="number" step="0.01" name="tankers[0][total_amount]" class="form-control t-total" readonly></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="table-active">
                <td class="fw-bold">{{ __('TOTALS') }}</td>
                <td class="fw-bold"><span id="ci_total_qty">0.000</span> MT</td>
                <td></td>
                <td class="fw-bold"><span id="ci_total_amount">0.00</span> USD</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm add-tanker"><i class="ti ti-plus"></i></button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@push('script-page')
<script>
    $(document).ready(function() {
        function calculateCITotals() {
            var totalQty = 0;
            var totalAmount = 0;
            $('.t-qty').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            $('.t-total').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });
            $('#ci_total_qty').text(totalQty.toFixed(3));
            $('#ci_total_amount').text(totalAmount.toFixed(2));
        }

        $(document).on('keyup change', '.t-qty, .t-cpt', function() {
            var row = $(this).closest('tr');
            var qty = parseFloat(row.find('.t-qty').val()) || 0;
            var cpt = parseFloat(row.find('.t-cpt').val()) || 0;
            var total = qty * cpt;
            row.find('.t-total').val(total.toFixed(2));
            calculateCITotals();
        });

        calculateCITotals();
    });
</script>
@endpush

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->ci)
            <a href="{{ route('sales-orders.ci.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.ci.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Packing List') }}</button>
</div>
{{ Form::close() }}
