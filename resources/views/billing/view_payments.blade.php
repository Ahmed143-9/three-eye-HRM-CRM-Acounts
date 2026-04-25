<div class="modal-body p-2">
    {{-- Invoice Summary Bar --}}
    @php
        $isReceivable = ($type === 'Receivable');
        $paidLabel = $isReceivable ? __('Received') : __('Paid');
    @endphp
    <div class="d-flex gap-2 mb-3">
        <div class="card flex-fill border-0 bg-light text-center p-2">
            <div class="small text-muted">{{ __('Invoice') }}</div>
            <strong>{{ $billable->invoice_number }}</strong>
        </div>
        <div class="card flex-fill border-0 bg-light text-center p-2">
            <div class="small text-muted">{{ __('Total') }}</div>
            <strong>{{ \Auth::user()->priceFormat($billable->total_amount) }}</strong>
        </div>
        <div class="card flex-fill border-0 bg-success bg-opacity-10 text-center p-2">
            <div class="small text-muted">{{ $paidLabel }}</div>
            <strong class="text-success">{{ \Auth::user()->priceFormat($billable->getTotalPaid()) }}</strong>
        </div>
        @if($billable->getTotalAdjustment() > 0)
        <div class="card flex-fill border-0 bg-warning bg-opacity-10 text-center p-2">
            <div class="small text-muted">{{ __('Adjustment') }}</div>
            <strong class="text-warning">{{ \Auth::user()->priceFormat($billable->getTotalAdjustment()) }}</strong>
        </div>
        @endif
        <div class="card flex-fill border-0 bg-danger bg-opacity-10 text-center p-2">
            <div class="small text-muted">{{ __('Remaining') }}</div>
            <strong class="text-danger">{{ \Auth::user()->priceFormat(max(0, $billable->getDueAmount())) }}</strong>
        </div>
    </div>

    {{-- Payment Entries Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0" style="font-size:12px;">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ $paidLabel }}</th>
                    <th>{{ __('Adjustment') }}</th>
                    <th>{{ __('Adj. Reason') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('Note') }}</th>
                    <th>{{ __('Next Due') }}</th>
                    <th class="text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments->sortBy('date') as $i => $payment)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Auth::user()->dateFormat($payment->date) }}</td>
                        <td class="text-success fw-semibold">
                            {{ $payment->amount > 0 ? \Auth::user()->priceFormat($payment->amount) : '—' }}
                        </td>
                        <td>
                            @if($payment->adjustment_amount > 0)
                                <span class="badge bg-warning text-dark">
                                    {{ \Auth::user()->priceFormat($payment->adjustment_amount) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $payment->adjustment_reason ?: '—' }}</td>
                        <td>{{ $payment->payment_method ?: '—' }}</td>
                        <td>{{ $payment->note ?: '—' }}</td>
                        <td>{{ $payment->next_due_date ? \Auth::user()->dateFormat($payment->next_due_date) : '—' }}</td>
                        <td class="text-center">
                            {{-- Edit Payment Entry --}}
                            <a href="#"
                               data-url="{{ route('billing.payment.edit', $payment->id) }}"
                               data-size="lg"
                               data-ajax-popup="true"
                               data-title="{{ __('Edit Payment Entry #') . $payment->id }}"
                               class="text-primary me-2"
                               title="{{ __('Edit') }}"
                               data-bs-toggle="tooltip">
                                <i class="ti ti-pencil"></i>
                            </a>
                            {{-- Delete Payment Entry --}}
                            {{ Form::open(['route' => ['billing.payment.delete', $payment->id], 'method' => 'DELETE', 'style' => 'display:inline']) }}
                                <button type="submit" class="btn btn-link text-danger p-0"
                                        title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                        onclick="return confirm('{{ __('Delete this payment entry?') }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">
                            {{ __('No payment entries yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-success fw-bold">
                    <td colspan="2">{{ __('Totals') }}</td>
                    <td>{{ \Auth::user()->priceFormat($billable->getTotalPaid()) }}</td>
                    <td>{{ $billable->getTotalAdjustment() > 0 ? \Auth::user()->priceFormat($billable->getTotalAdjustment()) : '—' }}</td>
                    <td colspan="4"></td>
                    <td></td>
                </tr>
                <tr class="{{ $billable->getDueAmount() <= 0 ? 'table-success' : 'table-danger' }} fw-bold">
                    <td colspan="2">{{ __('Remaining Due') }}</td>
                    <td colspan="7">{{ \Auth::user()->priceFormat(max(0, $billable->getDueAmount())) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="modal-footer">
    <a href="#"
       data-url="{{ route('billing.add-payment', [$type, $billable->id]) }}"
       data-size="lg"
       data-ajax-popup="true"
       data-title="{{ $isReceivable ? __('Receive Payment') : __('Add Payment') }}"
       class="btn btn-primary btn-sm me-auto">
        <i class="ti ti-plus me-1"></i>{{ $isReceivable ? __('Receive Payment') : __('Add Payment') }}
    </a>
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
