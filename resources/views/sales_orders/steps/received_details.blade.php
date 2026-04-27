<h5>{{ __('Step 7: Received Details') }}</h5>
<hr>

{{ Form::open(['route' => ['sales-orders.rd.store', $order->id], 'method' => 'post']) }}
<div class="table-responsive">
    <table class="table table-bordered" id="rd-tankers-table">
        <thead class="bg-light">
            <tr>
                <th rowspan="2" class="align-middle">{{ __('Tanker No') }}</th>
                <th colspan="3" class="text-center">{{ __('Seller (CN)') }}</th>
                <th colspan="3" class="text-center">{{ __('Landing Details') }}</th>
                <th colspan="3" class="text-center">{{ __('Discharge Details') }}</th>
                <th rowspan="2" class="align-middle text-center">{{ __('Differences') }}</th>
            </tr>
            <tr>
                <th>{{ __('Net') }}</th>
                <th>{{ __('Port') }}</th>
                <th>{{ __('Landing Net') }}</th>
                <th>{{ __('Diff (S-L)') }}</th>
                <th>{{ __('Port') }}</th>
                <th>{{ __('Discharge Net') }}</th>
                <th>{{ __('Diff (L-D)') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $tankers = $order->consignmentNote ? $order->consignmentNote->weightSlips : [];
                $savedData = $order->tankers_data ?? [];
            @endphp
            @foreach($tankers as $index => $slip)
                @php
                    $row = $savedData[$index] ?? [];
                @endphp
                <tr class="tanker-row">
                    <td>
                        <input type="hidden" name="tankers[{{$index}}][tanker_id]" value="{{ $slip->tanker_id }}">
                        <strong>{{ $slip->tanker_id }}</strong>
                    </td>
                    <td>
                        <input type="number" step="0.001" class="form-control-plaintext seller-net" value="{{ $slip->net_weight }}" readonly name="tankers[{{$index}}][seller_net]">
                    </td>
                    <td>
                        <input type="text" name="tankers[{{$index}}][landing_port]" class="form-control" value="{{ $row['landing_port'] ?? '' }}" placeholder="Port of Landing">
                    </td>
                    <td>
                        <input type="number" step="0.001" name="tankers[{{$index}}][landing_net]" class="form-control landing-net" value="{{ $row['landing_net'] ?? '' }}">
                    </td>
                    <td class="text-center fw-bold text-danger">
                        <span class="diff-s-l">0.000</span>
                    </td>
                    <td>
                        <input type="text" name="tankers[{{$index}}][discharge_port]" class="form-control" value="{{ $row['discharge_port'] ?? '' }}" placeholder="Port of Discharge">
                    </td>
                    <td>
                        <input type="number" step="0.001" name="tankers[{{$index}}][discharge_net]" class="form-control discharge-net" value="{{ $row['discharge_net'] ?? '' }}">
                    </td>
                    <td class="text-center fw-bold text-primary">
                        <span class="diff-l-d">0.000</span>
                    </td>
                    <td class="text-center">
                        <div class="text-xs">
                            Total Diff (S-D): <span class="diff-total fw-bold text-dark">0.000</span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-light">
            <tr>
                <td class="fw-bold">{{ __('TOTALS') }}</td>
                <td class="fw-bold text-end" id="total_seller_net">0.000</td>
                <td></td>
                <td class="fw-bold text-end" id="total_landing_net">0.000</td>
                <td class="fw-bold text-center text-danger" id="total_diff_sl">0.000</td>
                <td></td>
                <td class="fw-bold text-end" id="total_discharge_net">0.000</td>
                <td class="fw-bold text-center text-primary" id="total_diff_ld">0.000</td>
                <td class="fw-bold text-center" id="total_diff_sd">0.000</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="text-end mt-3">
    <button type="submit" class="btn btn-success">{{ __('Finalize & Complete Order') }}</button>
</div>
{{ Form::close() }}

@push('script-page')
<script>
    $(document).ready(function() {
        function calculateRDDiffs() {
            var grandSeller = 0, grandLanding = 0, grandDischarge = 0;
            
            $('.tanker-row').each(function() {
                var row = $(this);
                var sNet = parseFloat(row.find('.seller-net').val()) || 0;
                var lNet = parseFloat(row.find('.landing-net').val()) || 0;
                var dNet = parseFloat(row.find('.discharge-net').val()) || 0;
                
                var diffSL = sNet - lNet;
                var diffLD = lNet - dNet;
                var diffSD = sNet - dNet;
                
                row.find('.diff-s-l').text(diffSL.toFixed(3));
                row.find('.diff-l-d').text(diffLD.toFixed(3));
                row.find('.diff-total').text(diffSD.toFixed(3));
                
                grandSeller += sNet;
                grandLanding += lNet;
                grandDischarge += dNet;
            });
            
            $('#total_seller_net').text(grandSeller.toFixed(3));
            $('#total_landing_net').text(grandLanding.toFixed(3));
            $('#total_discharge_net').text(grandDischarge.toFixed(3));
            $('#total_diff_sl').text((grandSeller - grandLanding).toFixed(3));
            $('#total_diff_ld').text((grandLanding - grandDischarge).toFixed(3));
            $('#total_diff_sd').text((grandSeller - grandDischarge).toFixed(3));
        }

        $(document).on('keyup change', '.landing-net, .discharge-net', calculateRDDiffs);
        calculateRDDiffs();
    });
</script>
@endpush
