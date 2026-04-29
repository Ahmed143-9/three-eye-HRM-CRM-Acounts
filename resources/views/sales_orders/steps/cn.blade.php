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

<h6 class="mt-4">{{ __('Tanker Details (Sellers)') }}</h6>
@php
    $ciTankers = $order->ci ? $order->ci->tankers->pluck('tanker_number', 'tanker_number')->toArray() : [];
@endphp

<div class="row mb-3" id="tanker-files-container">
    {{-- Populated by JS based on rows count --}}
</div>

<div class="table-responsive">
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

        function updateFiles() {
            var html = '';
            $('#cn-weight-slips-table tbody tr').each(function(idx) {
                var val = $(this).find('.tanker-select').val();
                html += `<div class="col-md-3 mb-2">
                    <label class="small text-muted">${val || 'Tanker ' + (idx+1)} File</label>
                    <input type="file" name="tanker_files[${idx}]" class="form-control form-control-sm">
                </div>`;
            });
            $('#tanker-files-container').html(html);
        }

        function getTankerSelect(index) {
            var options = '<option value="">{{ __("Select Tanker") }}</option>';
            $.each(ciTankersOptions, function(val, text) {
                options += `<option value="${val}">${text}</option>`;
            });
            return `<select name="weight_slips[${index}][tanker_id]" class="form-control select2 tanker-select" required>${options}</select>`;
        }

        $(document).on('change', '.tanker-select', updateFiles);
        $(document).on('click', '.add-cn-item', function() {
            var index = $('#cn-weight-slips-table tbody tr').length;
            if (index >= maxTankers) {
                alert("Cannot add more tankers than exist in CI.");
                return;
            }
            var tankerSelect = getTankerSelect(index);
            var html = `<tr>
                <td>${tankerSelect}</td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][gross]" class="form-control w-gross" required></td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][tare]" class="form-control w-tare" required></td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][net]" class="form-control w-net" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#cn-weight-slips-table tbody').append(html);
            if(typeof $.fn.select2 !== 'undefined') $('.select2').select2();
            updateFiles();
        });

        $(document).on('click', '.remove-cn-item', function() { 
            $(this).closest('tr').remove(); 
            updateFiles();
        });

        $(document).on('keyup change', '.w-gross, .w-tare', function() {
            var tr = $(this).closest('tr');
            var gross = parseFloat(tr.find('.w-gross').val()) || 0;
            var tare = parseFloat(tr.find('.w-tare').val()) || 0;
            var net = gross - tare;
            tr.find('.w-net').val(net.toFixed(3));
        });
        updateFiles();
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
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Received Details') }}</button>
</div>
{{ Form::close() }}
