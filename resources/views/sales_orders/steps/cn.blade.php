<h5 class="fw-bold mb-0">{{ __('Step 6: Consignment Note') }}</h5>
<p class="text-muted mb-0" style="font-size:0.85rem;">{{ __('Step 6 of 7') }}</p>
<hr class="mt-2 mb-3">

{{-- Reference row --}}
<div class="row mb-3">
    <div class="col-md-3"><strong>{{ __('PO:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('PI:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('LC:') }}</strong> {{ $order->lc->lc_reference_no ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('CI:') }}</strong> {{ $order->ci->ci_number ?? 'N/A' }}</div>
</div>

{{ Form::open(['route' => ['sales-orders.cn.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<input type="hidden" name="ci_id" value="{{ $order->ci->id ?? '' }}">

@php
    $ciTankers = $order->ci ? $order->ci->tankers->pluck('tanker_number', 'tanker_number')->toArray() : [];
@endphp

{{-- Per-tanker image view (uploaded in CI) --}}
@if($order->ci && $order->ci->tankers->count() > 0)
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-transparent border-bottom py-2 px-4 d-flex align-items-center gap-2"
             style="border-left:4px solid #1565c0;">
            <i class="ti ti-camera text-primary"></i>
            <span class="fw-semibold text-dark">{{ __('Consignment Note Images (From CI)') }}</span>
        </div>
        <div class="card-body px-4 py-3">
            <div class="row g-3">
                @foreach($order->ci->tankers as $idx => $tanker)
                    <div class="col-md-3">
                        <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-light">
                            <span class="small fw-bold text-muted"><i class="ti ti-truck me-1"></i>{{ $tanker->tanker_number }}</span>
                            @if($tanker->file_path)
                                <a href="{{ asset($tanker->file_path) }}" target="_blank" class="btn btn-xs btn-primary shadow-sm"><i class="ti ti-eye"></i> {{ __('View') }}</a>
                            @else
                                <span class="badge bg-secondary-light text-muted" style="font-size:0.65rem;">{{ __('No File') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Weight Slips Table --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <i class="ti ti-list-check text-warning"></i>
    <h6 class="fw-semibold mb-0 text-dark">{{ __('Tanker Details (Sellers)') }}</h6>
</div>

<div class="table-responsive mt-3">
    <table class="table table-bordered" id="cn-weight-slips-table">
        <thead class="bg-light">
            <tr>
                <th>{{ __('Tanker Number') }}</th>
                <th>{{ __('Seller Gross') }}</th>
                <th>{{ __('Seller Tare') }}</th>
                <th>{{ __('Seller Net') }}</th>
                <th width="50px"></th>
            </tr>
        </thead>
        <tbody>
            @if($order->consignmentNote && $order->consignmentNote->weightSlips->count() > 0)
                @foreach($order->consignmentNote->weightSlips as $index => $slip)
                    <tr>
                        <td>
                            {{ Form::select("weight_slips[$index][tanker_id]", $ciTankers, $slip->tanker_id, ['class' => 'form-control select2 tanker-select', 'required' => 'required']) }}
                        </td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][gross]" class="form-control w-gross" value="{{$slip->gross_weight}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][tare]" class="form-control w-tare" value="{{$slip->tare_weight}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][net]" class="form-control w-net" value="{{$slip->net_weight}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        {{ Form::select("weight_slips[0][tanker_id]", $ciTankers, null, ['class' => 'form-control select2 tanker-select', 'placeholder' => __('Select Tanker'), 'required' => 'required']) }}
                    </td>
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
    $(document).ready(function() {
        var ciTankersOptions = @json($ciTankers);
        var maxTankers = Object.keys(ciTankersOptions).length;

        function getTankerSelect(index) {
            var options = '<option value="">{{ __("Select Tanker") }}</option>';
            $.each(ciTankersOptions, function(val, text) {
                options += '<option value="' + val + '">' + text + '</option>';
            });
            return '<select name="weight_slips[' + index + '][tanker_id]" class="form-control select2 tanker-select" required>' + options + '</select>';
        }

        $(document).on('click', '.add-cn-item', function() {
            var index = $('#cn-weight-slips-table tbody tr').length;
            if (maxTankers > 0 && index >= maxTankers) {
                alert("{{ __('Cannot add more rows than tankers in CI.') }}");
                return;
            }
            var html = '<tr>' +
                '<td>' + getTankerSelect(index) + '</td>' +
                '<td><input type="number" step="0.001" name="weight_slips[' + index + '][gross]" class="form-control w-gross" required></td>' +
                '<td><input type="number" step="0.001" name="weight_slips[' + index + '][tare]" class="form-control w-tare" required></td>' +
                '<td><input type="number" step="0.001" name="weight_slips[' + index + '][net]" class="form-control w-net" readonly></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>' +
                '</tr>';
            $('#cn-weight-slips-table tbody').append(html);
            if (typeof $.fn.select2 !== 'undefined') $('.select2').select2();
        });

        $(document).on('click', '.remove-cn-item', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('keyup change', '.w-gross, .w-tare', function() {
            var tr    = $(this).closest('tr');
            var gross = parseFloat(tr.find('.w-gross').val()) || 0;
            var tare  = parseFloat(tr.find('.w-tare').val()) || 0;
            tr.find('.w-net').val((gross - tare).toFixed(3));
        });

        if (typeof $.fn.select2 !== 'undefined') $('.select2').select2();
    });
</script>
@endpush

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->consignmentNote)
            <a href="{{ route('sales-orders.cn.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.cn.download', $order->id) }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-2"
            style="background-color:#6fd943;border-color:#6fd943;padding:10px 25px;font-weight:600;">
        {{ __('Save & Proceed to Received Details') }}
        <i class="ti ti-chevron-right"></i>
    </button>
</div>
{{ Form::close() }}
