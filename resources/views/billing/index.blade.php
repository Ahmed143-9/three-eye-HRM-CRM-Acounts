@extends('layouts.admin')
@section('page-title'){{ __('Billing Monitor') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Banking') }}</li>
    <li class="breadcrumb-item">{{ __('Billing Monitor') }}</li>
@endsection

@push('css')
<style>
/* ── Compact Billing Table ─────────────────────────────── */
.billing-table {
    font-size: 12px;
    width: 100%;
    table-layout: fixed;
}
.billing-table th,
.billing-table td {
    padding: 5px 7px !important;
    vertical-align: middle !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.billing-table thead th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    background: #f8f9fa;
}
/* Column widths */
.billing-table .col-uid   { width: 75px; }
.billing-table .col-inv   { width: 75px; }
.billing-table .col-dir   { width: 90px; }
.billing-table .col-name  { width: 110px; }
.billing-table .col-date  { width: 88px; }
.billing-table .col-type  { width: 80px; }
.billing-table .col-amt   { width: 90px; }
.billing-table .col-adj   { width: 80px; }
.billing-table .col-due   { width: 90px; }
.billing-table .col-stat  { width: 95px; }
.billing-table .col-ndd   { width: 95px; }
.billing-table .col-act   { width: 70px; }

.billing-table .badge {
    font-size: 10px;
    padding: 2px 6px;
}
.billing-table code {
    font-size: 10px;
}
.billing-table .action-icon {
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
}
/* Summary cards */
.billing-summary-card {
    border-radius: 10px;
    padding: 12px 16px;
}
</style>
@endpush

@section('content')
    {{-- Filter Row --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body py-2">
                    {{ Form::open(['route' => ['billing.index'], 'method' => 'GET', 'id' => 'billing_filter']) }}
                    <div class="row align-items-end g-2">
                        <div class="col-xl-3 col-md-4 col-sm-6">
                            {{ Form::label('type', __('Type'), ['class' => 'form-label mb-1 small']) }}
                            {{ Form::select('type', ['' => __('All Types'), 'payable' => __('Payable'), 'receivable' => __('Receivable')], request('type'), ['class' => 'form-control form-control-sm select']) }}
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6">
                            {{ Form::label('status', __('Status'), ['class' => 'form-label mb-1 small']) }}
                            {{ Form::select('status', ['' => __('All'), 'due' => __('Due'), 'partial paid' => __('Partial Paid'), 'paid' => __('Paid')], request('status'), ['class' => 'form-control form-control-sm select']) }}
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti ti-search me-1"></i>{{ __('Search') }}
                            </button>
                            <a href="{{ route('billing.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                                <i class="ti ti-x"></i>
                            </a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card border-start border-danger border-3 billing-summary-card mb-0">
                <div class="d-flex align-items-center">
                    <div class="theme-avtar bg-danger me-3">
                        <i class="ti ti-arrow-up-circle"></i>
                    </div>
                    <div>
                        <div class="small text-muted">{{ __('Total My Due (Payable Remaining)') }}</div>
                        <h5 class="mb-0 text-danger fw-bold">{{ \Auth::user()->priceFormat($totalMyDue) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-start border-success border-3 billing-summary-card mb-0">
                <div class="d-flex align-items-center">
                    <div class="theme-avtar bg-success me-3">
                        <i class="ti ti-arrow-down-circle"></i>
                    </div>
                    <div>
                        <div class="small text-muted">{{ __('Total Others Due to Me (Receivable Remaining)') }}</div>
                        <h5 class="mb-0 text-success fw-bold">{{ \Auth::user()->priceFormat($totalOthersDue) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="ti ti-table me-1"></i>{{ __('Billing Records') }}</h6>
                    <small class="text-muted fst-italic">{{ __('Invoices are read-only — use Add Payment to record entries.') }}</small>
                </div>
                <div class="card-body p-0">
                    <table class="table billing-table datatable mb-0">
                        <thead>
                            <tr>
                                <th class="col-uid">{{ __('Unique ID') }}</th>
                                <th class="col-inv">{{ __('Invoice') }}</th>
                                <th class="col-dir">{{ __('Direction') }}</th>
                                <th class="col-name">{{ __('Name') }}</th>
                                <th class="col-date">{{ __('Date') }}</th>
                                <th class="col-type">{{ __('Type') }}</th>
                                <th class="col-amt">{{ __('Total') }}</th>
                                <th class="col-amt">{{ __('Paid') }}</th>
                                <th class="col-adj">{{ __('Adjust.') }}</th>
                                <th class="col-due">{{ __('Due') }}</th>
                                <th class="col-stat">{{ __('Status') }}</th>
                                <th class="col-ndd">{{ __('Next Due') }}</th>
                                <th class="col-act text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($billings as $billing)
                                @php
                                    $isOverdue  = $billing->reminder_status === 'overdue';
                                    $isDueSoon  = $billing->reminder_status === 'due_soon';
                                    $rowClass   = $isOverdue ? 'table-danger' : ($isDueSoon ? 'table-warning' : '');

                                    // For Receivables: "Paid" becomes "Received"
                                    $isReceivable = ($billing->billing_type === 'Receivable');
                                    $paidLabel = $isReceivable ? 'Received' : 'Paid';

                                    $statusMap  = [
                                        'paid'         => ['cls' => 'bg-success',             'icon' => 'ti-circle-check', 'label' => $paidLabel],
                                        'partial paid' => ['cls' => 'bg-warning text-dark',    'icon' => 'ti-clock',        'label' => 'Partial'],
                                        'due'          => ['cls' => 'bg-danger',               'icon' => 'ti-alert-circle', 'label' => 'Due'],
                                    ];
                                    $s = $statusMap[$billing->current_status] ?? ['cls' => 'bg-secondary', 'icon' => 'ti-circle', 'label' => $billing->current_status];
                                    $dueDisplay = max(0, $billing->due_amount);

                                    // Edit route back to source module
                                    $editRoute = $isReceivable
                                        ? route('receivables.edit', $billing->id)
                                        : route('payables.edit', $billing->id);
                                        
                                    // Shortened fields: Prefix + 3 digits + ellipsis
                                    $uid_parts = explode('-', $billing->unique_id);
                                    $uid_short = (count($uid_parts) > 1 && strlen($uid_parts[1]) > 3) 
                                        ? $uid_parts[0] . '-' . substr($uid_parts[1], 0, 3) . '...' 
                                        : (strlen($billing->unique_id) > 8 ? substr($billing->unique_id, 0, 8) . '...' : $billing->unique_id);
                                        
                                    $inv_parts = explode('-', $billing->invoice_number);
                                    $inv_short = (count($inv_parts) > 1 && strlen($inv_parts[1]) > 3) 
                                        ? $inv_parts[0] . '-' . substr($inv_parts[1], 0, 3) . '...' 
                                        : (strlen($billing->invoice_number) > 8 ? substr($billing->invoice_number, 0, 8) . '...' : $billing->invoice_number);
                                    
                                    // Direction and Name
                                    $direction = $billing->billing_direction ? ucfirst($billing->billing_direction) : 'N/A';
                                    $partyName = method_exists($billing, 'getPartyName') ? $billing->getPartyName() : 'N/A';
                                    if ($partyName === '-') $partyName = 'N/A';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="col-uid">
                                        <div class="d-flex align-items-center">
                                            <code data-bs-toggle="tooltip" title="{{ $billing->unique_id }}" style="cursor:pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 50px; display: inline-block;">
                                                {{ $uid_short }}
                                            </code>
                                            <i class="ti ti-copy text-secondary ms-1 copy-btn" style="cursor:pointer; font-size: 14px;" data-val="{{ $billing->unique_id }}" data-bs-toggle="tooltip" title="{{ __('Copy') }}"></i>
                                        </div>
                                    </td>
                                    <td class="col-inv">
                                        <div class="d-flex align-items-center">
                                            <span data-bs-toggle="tooltip" title="{{ $billing->invoice_number }}" style="cursor:pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 50px; display: inline-block;" class="text-primary fw-semibold">
                                                {{ $inv_short }}
                                            </span>
                                            <i class="ti ti-copy text-secondary ms-1 copy-btn" style="cursor:pointer; font-size: 14px;" data-val="{{ $billing->invoice_number }}" data-bs-toggle="tooltip" title="{{ __('Copy') }}"></i>
                                        </div>
                                    </td>
                                    <td class="col-dir">{{ $direction }}</td>
                                    <td class="col-name" title="{{ $partyName }}">{{ $partyName }}</td>
                                    <td class="col-date">{{ \Auth::user()->dateFormat($billing->date) }}</td>
                                    <td class="col-type">
                                        @if($billing->billing_type == 'Payable')
                                            <span class="badge bg-danger"><i class="ti ti-arrow-up"></i> Pay</span>
                                        @else
                                            <span class="badge bg-success"><i class="ti ti-arrow-down"></i> Rec</span>
                                        @endif
                                    </td>
                                    <td class="col-amt">{{ \Auth::user()->priceFormat($billing->total_amount) }}</td>
                                    <td class="col-amt text-success fw-semibold">{{ \Auth::user()->priceFormat($billing->paid_amount) }}</td>
                                    <td class="col-adj">
                                        @if($billing->adjustment_amount > 0)
                                            <span class="text-warning fw-semibold" title="{{ __('Discount / Write-off') }}">
                                                {{ \Auth::user()->priceFormat($billing->adjustment_amount) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="col-due fw-bold {{ $dueDisplay > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \Auth::user()->priceFormat($dueDisplay) }}
                                    </td>
                                    <td class="col-stat">
                                        <span class="badge {{ $s['cls'] }}">
                                            <i class="ti {{ $s['icon'] }} me-1"></i>{{ $s['label'] }}
                                        </span>
                                    </td>
                                    <td class="col-ndd">
                                        @if($billing->next_due_date)
                                            {{ \Auth::user()->dateFormat($billing->next_due_date) }}
                                            @if($isOverdue)
                                                <i class="ti ti-alert-triangle text-danger" title="{{ __('Overdue') }}"></i>
                                            @elseif($isDueSoon)
                                                <i class="ti ti-clock text-warning" title="{{ __('Due Soon') }}"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="col-act text-center">
                                        {{-- View History (and edit payment entries from within) --}}
                                        <a href="#"
                                           data-url="{{ route('billing.view-payments', [$billing->billing_type, $billing->id]) }}"
                                           data-size="xl"
                                           data-ajax-popup="true"
                                           data-title="{{ $billing->invoice_number . ' — ' . __('Payment History') }}"
                                           class="action-icon text-secondary me-1"
                                           title="{{ __('History / Edit Payments') }}"
                                           data-bs-toggle="tooltip">
                                            <i class="ti ti-history"></i>
                                        </a>
                                        {{-- Add Payment / Receive Payment --}}
                                        @if($dueDisplay > 0)
                                            <a href="#"
                                               data-url="{{ route('billing.add-payment', [$billing->billing_type, $billing->id]) }}"
                                               data-size="lg"
                                               data-ajax-popup="true"
                                               data-title="{{ $isReceivable ? __('Receive Payment') : __('Add Payment') }}"
                                               class="action-icon text-primary"
                                               title="{{ $isReceivable ? __('Receive Payment') : __('Add Payment') }}"
                                               data-bs-toggle="tooltip">
                                                <i class="ti ti-plus-square"></i>
                                            </a>
                                        @else
                                            <i class="ti ti-circle-check text-success action-icon"
                                               title="{{ $isReceivable ? __('Fully Received') : __('Fully Paid') }}"
                                               data-bs-toggle="tooltip"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">
                                        <i class="ti ti-inbox d-block fs-4 mb-1"></i>{{ __('No records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    $(document).on('click', '.copy-btn', function() {
        var textToCopy = $(this).data('val');
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(textToCopy).select();
        document.execCommand("copy");
        tempInput.remove();
        
        var $icon = $(this);
        $icon.removeClass('ti-copy text-secondary').addClass('ti-check text-success');
        
        // Temporarily change tooltip text
        var originalTitle = $icon.attr('data-bs-original-title');
        $icon.attr('data-bs-original-title', '{{ __("Copied!") }}').tooltip('show');
        
        setTimeout(function() {
            $icon.removeClass('ti-check text-success').addClass('ti-copy text-secondary');
            $icon.attr('data-bs-original-title', originalTitle).tooltip('hide');
        }, 1500);
    });
});
</script>
@endpush
