<div class="modal-body">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-none">
                <div class="card-header border-0 pb-0">
                    <h5 class="h6 mb-0">{{ __('Expense Information') }}</h5>
                </div>
                <div class="card-body pt-2">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">{{ __('Serial No') }}</label>
                            <p class="mb-0">{{ $expense->serial_no }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">{{ __('Category') }}</label>
                            <p class="mb-0">{{ $expense->category->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">{{ __('Date') }}</label>
                            <p class="mb-0">{{ \Auth::user()->dateFormat($expense->date) }}</p>
                        </div>
                        @if($expense->employee)
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">{{ $type == 'purchase' ? __('Buyer Name') : ($type == 'utility' ? __('Paid By') : ($type == 'convenience' ? __('Consignee Name') : __('Employee Name'))) }}</label>
                                <p class="mb-0">{{ $expense->employee->name }}</p>
                            </div>
                        @endif
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">{{ __('Description') }}</label>
                            <p class="mb-0">{{ $expense->description ?? '-' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">{{ __('Total Amount') }}</label>
                            <h5 class="text-primary">{{ \Auth::user()->priceFormat($expense->amount) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            @if($expense->items->count() > 0)
                <div class="card shadow-none mt-3">
                    <div class="card-header border-0 pb-0">
                        <h5 class="h6 mb-0">{{ __('Product / Expense List') }}</h5>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Qty') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expense->items as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ \Auth::user()->priceFormat($item->unit_price) }}</td>
                                            <td class="text-end">{{ \Auth::user()->priceFormat($item->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end font-weight-bold">{{ __('Total') }}</td>
                                        <td class="text-end font-weight-bold">{{ \Auth::user()->priceFormat($expense->items->sum('amount')) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-none">
                <div class="card-header border-0 pb-0">
                    <h5 class="h6 mb-0">{{ __('Status History') }}</h5>
                </div>
                <div class="card-body pt-2">
                    <ul class="list-group list-group-flush list-group-sm">
                                @foreach($expense->statusLogs as $log)
                            <li class="list-group-item px-0 border-0">
                                <div class="d-flex w-100 justify-content-between">
                                    @php
                                        $logBadge = 'warning';
                                        if ($log->status == 'Approved') $logBadge = 'primary';
                                        elseif ($log->status == 'Processing Payment') $logBadge = 'info';
                                        elseif ($log->status == 'Paid') $logBadge = 'success';
                                        elseif ($log->status == 'Rejected') $logBadge = 'danger';
                                        elseif ($log->status == 'Hold') $logBadge = 'secondary';
                                        elseif ($log->status == 'Sent Back') $logBadge = 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $logBadge }} text-xs">{{ $log->status }}</span>
                                    <small class="text-xs text-muted">{{ \Auth::user()->dateFormat($log->created_at) }}</small>
                                </div>
                                <p class="mb-0 text-xs mt-1">{{ $log->comments }}</p>
                                <small class="text-xs text-muted">{{ __('By') }}: {{ $log->user->name ?? 'System' }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($expense->attachment)
                <div class="card shadow-none mt-3">
                    <div class="card-body text-center p-2">
                        <a href="{{ asset($expense->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="ti ti-download"></i> {{ __('Download Attachment') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if(Auth::user()->can('approve expense') || Auth::user()->type == 'company')
    @if(in_array($expense->status, ['Pending Approval', 'Hold', 'Sent Back'], true))
        <div class="modal-footer d-block border-top bg-light sticky-bottom">
            <div class="form-group mb-3 text-start">
                <label for="review_comments" class="form-label">{{ __('Review comments / reason') }}</label>
                <textarea id="review_comments" name="comments" class="form-control" rows="2" placeholder="{{ __('Required for reject, hold, or send back; optional for approve') }}"></textarea>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('erp-expenses.reject', $expense->id) }}" method="POST" class="d-inline expense-approval-action-form" data-confirm="{{ __('Reject this expense?') }}">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror" value="">
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
                    </form>
                    <form action="{{ route('erp-expenses.hold', $expense->id) }}" method="POST" class="d-inline expense-approval-action-form" data-confirm="{{ __('Put this expense on hold?') }}">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror" value="">
                        <button type="submit" class="btn btn-warning btn-sm">{{ __('Hold') }}</button>
                    </form>
                    <form action="{{ route('erp-expenses.send-back', $expense->id) }}" method="POST" class="d-inline expense-approval-action-form" data-confirm="{{ __('Send back to creator for corrections?') }}">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror" value="">
                        <button type="submit" class="btn btn-secondary btn-sm">{{ __('Send Back') }}</button>
                    </form>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('erp-expenses.approve', $expense->id) }}" method="POST" class="d-inline expense-approval-action-form" data-confirm="{{ __('Approve and send to Accounts for payment?') }}">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror" value="">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Approve Bill') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            $(document).off('keyup.expAppr', '#review_comments').on('keyup.expAppr', '#review_comments', function () {
                $(this).closest('.modal').find('.comments-mirror').val($(this).val());
            });
            $(document).off('submit.expAppr', '.expense-approval-action-form').on('submit.expAppr', '.expense-approval-action-form', function (e) {
                e.preventDefault();
                var form = $(this);
                var msg = form.data('confirm');
                if (msg && !window.confirm(msg)) {
                    return false;
                }
                var comments = form.closest('.modal').find('#review_comments').val() || '';
                var act = form.attr('action') || '';
                if (act.indexOf('reject') !== -1 || act.indexOf('hold') !== -1 || act.indexOf('send-back') !== -1) {
                    if (!comments.trim()) {
                        show_toastr('{{ __('Error') }}', '{{ __('Please enter a comment or reason for this action.') }}', 'error');
                        return false;
                    }
                }
                form.find('.comments-mirror').val(comments);
                var btn = form.find('button[type="submit"]').prop('disabled', true);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).done(function (data) {
                    if (data.success) {
                        show_toastr('{{ __('Success') }}', data.message, 'success');
                        $('.modal.show').modal('hide');
                        if (typeof window.refreshExpenseApprovalQueue === 'function') {
                            window.refreshExpenseApprovalQueue();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        show_toastr('{{ __('Error') }}', data.message || '{{ __('Status update failed') }}', 'error');
                    }
                }).fail(function (xhr) {
                    var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Approval transaction failed') }}';
                    show_toastr('{{ __('Error') }}', m, 'error');
                }).always(function () {
                    btn.prop('disabled', false);
                });
                return false;
            });
        </script>
    @endif
@endif
