<h5>{{ __('Step 6: Consignment Note') }}</h5>
<hr>
<div class="row mb-3">
    <div class="col-md-3"><strong>{{ __('PO:') }}</strong> {{ $order->po->order_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('PI:') }}</strong> {{ $order->pi->pi_number ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('LC:') }}</strong> {{ $order->lc->lc_no ?? 'N/A' }}</div>
    <div class="col-md-3"><strong>{{ __('CI:') }}</strong> {{ $order->ci->ci_number ?? 'N/A' }}</div>
</div>

{{ Form::open(['route' => ['sales-orders.cn.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'workflow-form']) }}
<!-- <div class="row">
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
</div> -->

<div class="row mt-4">
    @if($order->ci && $order->ci->tankers->count() > 0)
        @foreach($order->ci->tankers as $index => $tanker)
            @php
                $slip = $order->consignmentNote ? $order->consignmentNote->weightSlips->where('tanker_id', $tanker->tanker_number)->first() : null;
            @endphp
            <div class="col-md-6 mb-4">
                <div class="card shadow-none border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">{{ __('Tanker:') }} <span class="text-primary">{{ $tanker->tanker_number }}</span></h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="weight_slips[{{$index}}][tanker_id]" value="{{ $tanker->tanker_number }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    {{ Form::label("weight_slips[$index][gross]", __('Seller Gross'), ['class' => 'form-label small']) }}
                                    <input type="number" step="0.001" name="weight_slips[{{$index}}][gross]" class="form-control form-control-sm w-gross" value="{{ $slip->gross_weight ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    {{ Form::label("weight_slips[$index][tare]", __('Seller Tare'), ['class' => 'form-label small']) }}
                                    <input type="number" step="0.001" name="weight_slips[{{$index}}][tare]" class="form-control form-control-sm w-tare" value="{{ $slip->tare_weight ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    {{ Form::label("weight_slips[$index][net]", __('Seller Net'), ['class' => 'form-label small']) }}
                                    <input type="number" step="0.001" name="weight_slips[{{$index}}][net]" class="form-control form-control-sm w-net" value="{{ $slip->net_weight ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    {{ Form::label("tanker_files[$index]", __('Weight Slip Image'), ['class' => 'form-label small']) }}
                                    <input type="file" name="tanker_files[{{$index}}]" class="form-control form-control-sm">
                                    @if($slip && $slip->file_path)
                                        <div class="mt-2">
                                            <a href="{{ asset($slip->file_path) }}" target="_blank" class="btn btn-xs btn-info text-xs py-1">
                                                <i class="ti ti-eye me-1"></i>{{ __('View Current Image') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12 text-center py-4">
            <div class="alert alert-warning">
                <i class="ti ti-info-circle me-1"></i>{{ __('No tankers found in CI. Please add tankers in the CI step first.') }}
            </div>
        </div>
    @endif
</div>

@push('script-page')
<script>
    $(document).ready(function() {
        $(document).on('keyup change', '.w-gross, .w-tare', function() {
            var card = $(this).closest('.card-body');
            var gross = parseFloat(card.find('.w-gross').val()) || 0;
            var tare = parseFloat(card.find('.w-tare').val()) || 0;
            var net = gross - tare;
            card.find('.w-net').val(net.toFixed(3));
        });
    });
</script>
@endpush

{{ Form::close() }}
