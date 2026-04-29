{{ Form::open(['route' => ['sales-orders.rd.store', $order->id], 'method' => 'post', 'id' => 'rd-form']) }}
@if($active_ci)
    <input type="hidden" name="ci_id" value="{{ $active_ci->id }}">
@endif

@php
    $tolerance = $order->pi->tolerance ?? 0;
@endphp
<input type="hidden" id="rd-tolerance" value="{{ $tolerance }}">

<div class="table-responsive">
    <table class="table table-bordered align-middle table-sm" id="rd-tankers-table">
        <thead class="bg-light text-center">
            <tr>
                <th width="80">{{ __('Tanker No') }}</th>
                <th>{{ __('Seller (CN)') }} <br><small>(G / T / N)</small></th>
                <th>{{ __('Loading Details') }} <br><small>(Gross / Tare / Net)</small></th>
                <th>{{ __('Discharge Details') }} <br><small>(Gross / Tare / Net)</small></th>
                <th width="60">{{ __('Diffs') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $tankers = ($active_ci && $active_ci->consignmentNote) ? $active_ci->consignmentNote->weightSlips : [];
                $savedData = $order->tankers_data ?? []; // This needs to be moved to CI model eventually
            @endphp
            @foreach($tankers as $index => $slip)
                @php
                    $row = $savedData[$index] ?? [];
                @endphp
                <tr class="tanker-row bg-white">
                    <td class="text-center fw-bold">
                        <input type="hidden" name="tankers[{{$index}}][tanker_id]" value="{{ $slip->tanker_id }}">
                        {{ $slip->tanker_id }}
                    </td>
                    <td class="bg-light-secondary">
                        <div class="d-flex gap-1 justify-content-center text-center">
                           <div class="border rounded p-1 bg-white" style="min-width:50px"><small class="d-block text-muted">G</small><strong>{{ number_format($slip->gross_weight, 3) }}</strong></div>
                           <div class="border rounded p-1 bg-white" style="min-width:50px"><small class="d-block text-muted">T</small><strong>{{ number_format($slip->tare_weight, 3) }}</strong></div>
                           <div class="border rounded p-1 bg-white border-primary" style="min-width:50px"><small class="d-block text-muted">N</small><strong class="text-primary">{{ number_format($slip->net_weight, 3) }}</strong></div>
                        </div>
                        <input type="hidden" class="seller-gross" value="{{ $slip->gross_weight }}">
                        <input type="hidden" class="seller-tare" value="{{ $slip->tare_weight }}">
                        <input type="hidden" class="seller-net" value="{{ $slip->net_weight }}" name="tankers[{{$index}}][seller_net]">
                    </td>
                    
                    {{-- Loading Inputs --}}
                    <td class="bg-light-primary">
                        <div class="row g-1">
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_gross]" class="form-control form-control-sm loading-gross" value="{{ $row['loading_gross'] ?? '' }}" placeholder="Gross">
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_tare]" class="form-control form-control-sm loading-tare" value="{{ $row['loading_tare'] ?? '' }}" placeholder="Tare">
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_net]" class="form-control-plaintext form-control-sm loading-net fw-bold text-primary" value="{{ $row['loading_net'] ?? '0.000' }}" readonly>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Discharge Inputs --}}
                    <td class="bg-light-success">
                        <div class="row g-1">
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_gross]" class="form-control form-control-sm discharge-gross" value="{{ $row['discharge_gross'] ?? '' }}" placeholder="Gross">
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_tare]" class="form-control form-control-sm discharge-tare" value="{{ $row['discharge_tare'] ?? '' }}" placeholder="Tare">
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_net]" class="form-control-plaintext form-control-sm discharge-net fw-bold text-success" value="{{ $row['discharge_net'] ?? '0.000' }}" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-primary show-diff-modal" data-bs-toggle="modal" data-bs-target="#diffModal" data-tanker="{{ $slip->tanker_id }}" 
                            data-sg="{{ $slip->gross_weight }}" data-st="{{ $slip->tare_weight }}" data-sn="{{ $slip->net_weight }}">
                            <i class="ti ti-eye"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-dark text-white fw-bold text-end">
            <tr>
                <td colspan="3" class="text-center">{{ __('GRAND TOTALS') }}</td>
                <td class="text-center">
                    <div class="small opacity-75">{{ __('Loading Net') }}</div>
                    <span id="total_loading_n" class="fs-6">0.000</span>
                </td>
                <td class="text-center">
                    <div class="small opacity-75">{{ __('Discharge Net') }}</div>
                    <span id="total_discharge_n" class="fs-6">0.000</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="text-end mt-3">
    <button type="submit" class="btn btn-success px-5 shadow">{{ __('Save Received Details') }}</button>
</div>
{{ Form::close() }}

{{-- Differences Modal (Shared) --}}
<div class="modal fade" id="diffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">{{ __('Tanker Differences Analysis') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center table-sm">
                    <thead class="bg-light"><tr><th>Segment</th><th>Gross Diff</th><th>Tare Diff</th><th>Net Diff</th></tr></thead>
                    <tbody>
                        <tr><th class="text-start">Seller vs Loading (S-L)</th><td class="diff-sl-g">0</td><td class="diff-sl-t">0</td><td class="diff-sl-n">0</td></tr>
                        <tr><th class="text-start">Loading vs Discharge (L-D)</th><td class="diff-ld-g">0</td><td class="diff-ld-t">0</td><td class="diff-ld-n">0</td></tr>
                        <tr><th class="text-start">Seller vs Discharge (S-D)</th><td class="diff-sd-g">0</td><td class="diff-sd-t">0</td><td class="diff-sd-n">0</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('script-page')
<script>
    $(document).ready(function() {
        $(document).on('click', '.show-diff-modal', function() {
            var btn = $(this);
            var row = btn.closest('.tanker-row');
            var modal = $('#diffModal');

            var sg = parseFloat(btn.data('sg')) || 0;
            var st = parseFloat(btn.data('st')) || 0;
            var sn = parseFloat(btn.data('sn')) || 0;

            var lg = parseFloat(row.find('.loading-gross').val()) || 0;
            var lt = parseFloat(row.find('.loading-tare').val()) || 0;
            var ln = lg - lt;

            var dg = parseFloat(row.find('.discharge-gross').val()) || 0;
            var dt = parseFloat(row.find('.discharge-tare').val()) || 0;
            var dn = dg - dt;

            modal.find('.diff-sl-g').text((sg - lg).toFixed(3));
            modal.find('.diff-sl-t').text((st - lt).toFixed(3));
            modal.find('.diff-sl-n').text((sn - ln).toFixed(3));
            modal.find('.diff-ld-g').text((lg - dg).toFixed(3));
            modal.find('.diff-ld-t').text((lt - dt).toFixed(3));
            modal.find('.diff-ld-n').text((ln - dn).toFixed(3));
            modal.find('.diff-sd-g').text((sg - dg).toFixed(3));
            modal.find('.diff-sd-t').text((st - dt).toFixed(3));
            modal.find('.diff-sd-n').text((sn - dn).toFixed(3));
        });

        function calculateRDDiffs() {
            var grandLN=0, grandDN=0;
            var tolerance = parseFloat($('#rd-tolerance').val()) || 0;
            var allWithinTolerance = true;

            $('.tanker-row').each(function() {
                var row = $(this);
                var sg = parseFloat(row.find('.seller-gross').val()) || 0;
                var st = parseFloat(row.find('.seller-tare').val()) || 0;
                var sn = parseFloat(row.find('.seller-net').val()) || 0;

                var lg = parseFloat(row.find('.loading-gross').val()) || 0;
                var lt = parseFloat(row.find('.loading-tare').val()) || 0;
                var ln = lg - lt;

                var dg = parseFloat(row.find('.discharge-gross').val()) || 0;
                var dt = parseFloat(row.find('.discharge-tare').val()) || 0;
                var dn = dg - dt;

                row.find('.loading-net').val(ln.toFixed(3));
                row.find('.discharge-net').val(dn.toFixed(3));
                grandLN += ln; grandDN += dn;

                // Validation logic
                var slDiff = sn > 0 ? Math.abs(sn - ln) / sn * 100 : 0;
                var ldDiff = ln > 0 ? Math.abs(ln - dn) / ln * 100 : 0;
                var sdDiff = sn > 0 ? Math.abs(sn - dn) / sn * 100 : 0;

                row.find('.loading-net, .discharge-net').removeClass('text-danger');

                if(tolerance > 0 && ln > 0 && dn > 0) { 
                    if(slDiff > tolerance) {
                        allWithinTolerance = false;
                        row.find('.loading-net').addClass('text-danger');
                    }
                    if(ldDiff > tolerance || sdDiff > tolerance) {
                        allWithinTolerance = false;
                        row.find('.discharge-net').addClass('text-danger');
                    }
                }
            });
            $('#total_loading_n').text(grandLN.toFixed(3));
            $('#total_discharge_n').text(grandDN.toFixed(3));

            if(allWithinTolerance || tolerance == 0) {
                $('#rd-form button[type="submit"]').prop('disabled', false).text('{{ __("Save Received Details") }}');
            } else {
                $('#rd-form button[type="submit"]').prop('disabled', true).text('{{ __("Tolerance Exceeded - Cannot Save") }}');
            }
        }

        $(document).on('keyup change', '.loading-gross, .loading-tare, .discharge-gross, .discharge-tare', calculateRDDiffs);
        calculateRDDiffs();
    });
</script>
@endpush
