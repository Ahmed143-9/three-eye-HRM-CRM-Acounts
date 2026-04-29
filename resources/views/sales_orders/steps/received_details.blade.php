<h5>{{ __('Step 7: Received Details') }}</h5>
<hr>

{{ Form::open(['route' => ['sales-orders.rd.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}
<div class="table-responsive">
    <table class="table table-bordered align-middle" id="rd-tankers-table">
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
                $tankers = $order->consignmentNote ? $order->consignmentNote->weightSlips : [];
                $savedData = $order->tankers_data ?? [];
            @endphp
            @foreach($tankers as $index => $slip)
                @php
                    $row = $savedData[$index] ?? [];
                @endphp
                <tr class="tanker-row bg-white">
                    <td class="text-center fw-bold fs-5">
                        <input type="hidden" name="tankers[{{$index}}][tanker_id]" value="{{ $slip->tanker_id }}">
                        {{ $slip->tanker_id }}
                    </td>
                    <td class="bg-light-secondary">
                        <div class="d-flex gap-1 justify-content-center text-center">
                           <div class="border rounded p-1 bg-white" style="min-width:60px"><small class="d-block text-muted">G</small><strong>{{ number_format($slip->gross_weight, 3) }}</strong></div>
                           <div class="border rounded p-1 bg-white" style="min-width:60px"><small class="d-block text-muted">T</small><strong>{{ number_format($slip->tare_weight, 3) }}</strong></div>
                           <div class="border rounded p-1 bg-white border-primary" style="min-width:60px"><small class="d-block text-muted">N</small><strong class="text-primary">{{ number_format($slip->net_weight, 3) }}</strong></div>
                        </div>
                        <input type="hidden" class="seller-gross" value="{{ $slip->gross_weight }}">
                        <input type="hidden" class="seller-tare" value="{{ $slip->tare_weight }}">
                        <input type="hidden" class="seller-net" value="{{ $slip->net_weight }}" name="tankers[{{$index}}][seller_net]">
                    </td>
                    
                    {{-- Loading Inputs --}}
                    <td class="bg-light-primary">
                        <div class="row g-1">
                            <div class="col-4">
                                <label class="small text-muted mb-0">G</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_gross]" class="form-control loading-gross" value="{{ $row['loading_gross'] ?? '' }}">
                            </div>
                            <div class="col-4">
                                <label class="small text-muted mb-0">T</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_tare]" class="form-control loading-tare" value="{{ $row['loading_tare'] ?? '' }}">
                            </div>
                            <div class="col-4">
                                <label class="small text-muted mb-0">N</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][loading_net]" class="form-control-plaintext loading-net fw-bold text-primary" value="{{ $row['loading_net'] ?? '0.000' }}" readonly>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Discharge Inputs --}}
                    <td class="bg-light-success">
                        <div class="row g-1">
                            <div class="col-4">
                                <label class="small text-muted mb-0">G</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_gross]" class="form-control discharge-gross" value="{{ $row['discharge_gross'] ?? '' }}">
                            </div>
                            <div class="col-4">
                                <label class="small text-muted mb-0">T</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_tare]" class="form-control discharge-tare" value="{{ $row['discharge_tare'] ?? '' }}">
                            </div>
                            <div class="col-4">
                                <label class="small text-muted mb-0">N</label>
                                <input type="number" step="0.001" name="tankers[{{$index}}][discharge_net]" class="form-control-plaintext discharge-net fw-bold text-success" value="{{ $row['discharge_net'] ?? '0.000' }}" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary show-diff-modal" data-bs-toggle="modal" data-bs-target="#diffModal" data-tanker="{{ $slip->tanker_id }}" 
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
                    <span id="total_loading_n" class="fs-5">0.000</span>
                </td>
                <td class="text-center">
                    <div class="small opacity-75">{{ __('Discharge Net') }}</div>
                    <span id="total_discharge_n" class="fs-5">0.000</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- Differences Modal --}}
<div class="modal fade" id="diffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Tanker Differences Analysis') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center">
                    <thead><tr><th>Segment</th><th>Gross Diff</th><th>Tare Diff</th><th>Net Diff</th></tr></thead>
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

{{ Form::close() }}

@if($order->status != 'finalized')
    <div class="mt-5 border-top pt-4 text-center">
        <h5>{{ __('Finalize Sales Order') }}</h5>
        <p class="text-muted">{{ __('Once finalized, you will no longer be able to edit any details in this workflow.') }}</p>
        {{ Form::open(['route' => ['sales-orders.finalize', $order->id], 'method' => 'post']) }}
        <button type="submit" class="btn btn-danger btn-lg px-5 shadow-sm">{{ __('Finalize Order') }} <i class="ti ti-lock ms-1"></i></button>
        {{ Form::close() }}
    </div>
@else
    <div class="alert alert-success mt-4">
        <i class="ti ti-circle-check me-1"></i>{{ __('This order is finalized and locked.') }}
    </div>
@endif

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
            $('.tanker-row').each(function() {
                var row = $(this);
                var ln = (parseFloat(row.find('.loading-gross').val()) - parseFloat(row.find('.loading-tare').val())) || 0;
                var dn = (parseFloat(row.find('.discharge-gross').val()) - parseFloat(row.find('.discharge-tare').val())) || 0;
                row.find('.loading-net').val(ln.toFixed(3));
                row.find('.discharge-net').val(dn.toFixed(3));
                grandLN += ln; grandDN += dn;
            });
            $('#total_loading_n').text(grandLN.toFixed(3));
            $('#total_discharge_n').text(grandDN.toFixed(3));
        }

        $(document).on('keyup change', '.loading-gross, .loading-tare, .discharge-gross, .discharge-tare', calculateRDDiffs);
        calculateRDDiffs();
    });
</script>
@endpush
