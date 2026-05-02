{{ Form::open(['route' => ['sales-orders.cn.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
@if($active_ci)
    <input type="hidden" name="ci_id" value="{{ $active_ci->id }}">
@endif

@php
    $ciTankers = $active_ci ? $active_ci->tankers->pluck('tanker_number', 'tanker_number')->toArray() : [];
@endphp

@if(empty($ciTankers))
    <div class="alert alert-warning">
        <i class="ti ti-info-circle"></i> {{ __('No tankers found for this shipment. Please ensure you have added and SAVED tankers in the CI Details step before proceeding.') }}
    </div>
@endif

{{-- Per-tanker image uploads --}}
@if($active_ci && $active_ci->tankers->count() > 0)
<h6 class="fw-bold text-dark mt-4 mb-3">{{ __('Consignment Note Images (Per Tanker)') }}</h6>
<div class="row g-3">
                @foreach($active_ci->tankers as $idx => $tanker)
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">
                                <i class="ti ti-truck me-1 text-muted"></i>{{ $tanker->tanker_number }}
                            </label>
                            <input type="file"
                                   name="tanker_files[{{ $idx }}]"
                                   class="form-control"
                                   accept="image/*,application/pdf">
                            @if($tanker->file_path)
                                <div class="mt-1">
                                    <a href="{{ asset($tanker->file_path) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-outline-info">
                                        <i class="ti ti-eye me-1"></i>{{ __('View') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
@else
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('file', __('Upload Consignment Note for ') . ($active_ci->ci_number ?? ''), ['class' => 'form-label']) }}
                {{ Form::file('file', ['class' => 'form-control', ($active_ci && $active_ci->consignmentNote) ? '' : 'required']) }}
                @if($active_ci && $active_ci->consignmentNote && $active_ci->consignmentNote->file_path)
                    <div class="mt-2">
                        <a href="{{ asset($active_ci->consignmentNote->file_path) }}" target="_blank" class="btn btn-sm btn-info">{{ __('View Uploaded Note') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<h6 class="fw-bold text-dark mt-4 mb-3">{{ __('Tanker Weight Details (Sellers)') }}</h6>

<div class="table-responsive mt-3">
    <table class="table table-bordered table-sm" id="cn-weight-slips-table">
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
            @if($active_ci && $active_ci->consignmentNote && $active_ci->consignmentNote->weightSlips->count() > 0)
                @foreach($active_ci->consignmentNote->weightSlips as $index => $slip)
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

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($active_ci && $active_ci->consignmentNote)
            <a href="{{ route('sales-orders.cn.print', $order->id) }}?ci_id={{ $active_ci->id }}" target="_blank" class="btn btn-secondary"><i class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <a href="{{ route('sales-orders.cn.download', $order->id) }}?ci_id={{ $active_ci->id }}" class="btn btn-info"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</a>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to Received Details') }}</button>
</div>
{{ Form::close() }}

@push('script-page')
<script>
    $(document).ready(function() {
        var ciTankersOptions = @json($ciTankers);
        var maxTankers = Object.keys(ciTankersOptions).length;

        function initSelects() {
            if ($.fn.select2) {
                $('.tanker-select').select2({
                    dropdownParent: $('#step-cn-form')
                });
            }
        }
        initSelects();

        $(document).off('click', '.add-cn-item').on('click', '.add-cn-item', function() {
            var index = $('#cn-weight-slips-table tbody tr').length;
            if (index >= maxTankers && maxTankers > 0) {
                alert("Cannot add more tankers than exist in this Shipment Batch.");
                return;
            }
            
            var options = '<option value="">{{ __("Select Tanker") }}</option>';
            $.each(ciTankersOptions, function(val, text) {
                options += `<option value="${val}">${text}</option>`;
            });
            
            var html = `<tr>
                <td><select name="weight_slips[${index}][tanker_id]" class="form-control select2 tanker-select" required>${options}</select></td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][gross]" class="form-control w-gross" required></td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][tare]" class="form-control w-tare" required></td>
                <td><input type="number" step="0.001" name="weight_slips[${index}][net]" class="form-control w-net" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-cn-item"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#cn-weight-slips-table tbody').append(html);
            initSelects();
        });

        $(document).on('click', '.remove-cn-item', function() { 
            $(this).closest('tr').remove(); 
        });

        $(document).on('keyup change', '.w-gross, .w-tare', function() {
            var tr = $(this).closest('tr');
            var gross = parseFloat(tr.find('.w-gross').val()) || 0;
            var tare = parseFloat(tr.find('.w-tare').val()) || 0;
            var net = gross - tare;
            tr.find('.w-net').val(net.toFixed(3));
        });
    });
</script>
@endpush
