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

<h6 class="mt-4">{{ __('Weight Slip Section') }}</h6>
<div class="table-responsive">
    <table class="table" id="cn-weight-slips-table">
        <thead>
            <tr>
                <th>{{ __('Tanker Number') }}</th>
                <th>{{ __('IN/OUT Number') }}</th>
                <th>{{ __('Gross Weight') }}</th>
                <th>{{ __('Tare Weight') }}</th>
                <th>{{ __('Net Weight') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(optional($order->ci)->tankers)
                @foreach($order->ci->tankers as $index => $tanker)
                    @php
                        $slip = $order->consignmentNote ? $order->consignmentNote->weightSlips->where('tanker_id', $tanker->id)->first() : null;
                    @endphp
                    <tr>
                        <td>
                            <input type="hidden" name="weight_slips[{{$index}}][tanker_id]" value="{{$tanker->id}}">
                            <strong>{{ $tanker->tanker_number }}</strong> ({{ $tanker->quantity_mt }} MT)
                        </td>
                        <td><input type="text" name="weight_slips[{{$index}}][in_out_number]" class="form-control" value="{{$slip->in_out_number ?? ''}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][gross]" class="form-control w-gross" value="{{$slip->gross_weight ?? ''}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][tare]" class="form-control w-tare" value="{{$slip->tare_weight ?? ''}}" required></td>
                        <td><input type="number" step="0.001" name="weight_slips[{{$index}}][net]" class="form-control w-net" value="{{$slip->net_weight ?? ''}}" readonly></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="text-end mt-3">
    <button type="submit" class="btn btn-primary">{{ __('Save & Proceed to TR') }}</button>
</div>
{{ Form::close() }}
