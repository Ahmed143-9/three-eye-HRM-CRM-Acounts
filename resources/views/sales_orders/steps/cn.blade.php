<h5>{{ __('Step 6: Consignment Note') }}</h5>
<hr>
<div class="row mb-3">
    <div class="col-md-3"><strong>{{ __('PO:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('PI:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('LC:') }}</strong> {{ $order->lc->lc_no ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('CI:') }}</strong> {{ $order->ci->ci_number ?? 'N/A' }}</div>
</div>

{{ Form::open(['route' => ['sales-orders.cn.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('file', __('Upload Consignment Note (PDF/Image)'), ['class' => 'form-label']) }}
            {{ Form::file('file', ['class' => 'form-control', $order->consignmentNote ? '' : 'required']) }}
            @if($order->consignmentNote && $order->consignmentNote->file_path)
                <div class="mt-2">
                    <a href="{{ asset($order->consignmentNote->file_path) }}" target="_blank" class="btn btn-sm btn-info">{{ __('View Uploaded Note') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

<h6 class="mt-4">{{ __('Tanker Details (Weight Slips)') }}</h6>
<div class="table-responsive">
    <table class="table" id="cn-weight-slips-table">
        <thead>
            <tr>
                <th>{{ __('Tanker Number') }}</th>
                <th>{{ __('Seller Gross') }}</th>
                <th>{{ __('Seller Tare') }}</th>
                <th>{{ __('Seller Net') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if($order->consignmentNote && $order->consignmentNote->weightSlips->count() > 0)
                @foreach($order->consignmentNote->weightSlips as $index => $slip)
                    <tr>
                        <td><input type="text" name="weight_slips[{{$index}}][tanker_id]" class="form-control" value="{{$slip->tanker_id}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][gross]" class="form-control w-gross" value="{{$slip->gross_weight}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][tare]" class="form-control w-tare" value="{{$slip->tare_weight}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][net]" class="form-control w-net" value="{{$slip->net_weight}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="weight_slips[0][tanker_id]" class="form-control" required></td>
                    <td><input type="number" step="0.001" name="weight_slips[0][gross]" class="form-control w-gross" required></td>
                    <td><input type="number" step="0.001" name="weight_slips[0][tare]" class="form-control w-tare" required></td>
                    <td><input type="number" step="0.001" name="weight_slips[0][net]" class="form-control w-net" readonly></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td><button type="button" class="btn btn-primary btn-sm add-cn-item"><i class="ti ti-plus"></i></button></td>
            </tr>
        </tfoot>
    </table>
</div>

@push('script-page')
<script>
    $(document).on('click', '.add-cn-item', function() {
        var index = $('#cn-weight-slips-table tbody tr').length;
        var html = `<tr>
            <td><input type="text" name="weight_slips[${index}][tanker_id]" class="form-control" required></td>
            <td><input type="number" step="0.001" name="weight_slips[${index}][gross]" class="form-control w-gross" required></td>
            <td><input type="number" step="0.001" name="weight_slips[${index}][tare]" class="form-control w-tare" required></td>
            <td><input type="number" step="0.001" name="weight_slips[${index}][net]" class="form-control w-net" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#cn-weight-slips-table tbody').append(html);
    });

    $(document).on('click', '.remove-cn-item', function() { $(this).closest('tr').remove(); });
</script>
@endpush

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->consignmentNote)
            <a href="{{ route('sales-orders.cn.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.cn.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Received Details') }}</button>
</div>
{{ Form::close() }}
