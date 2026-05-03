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
                                    <span class="badge bg-{{ $log->status == 'Approved' ? 'primary' : ($log->status == 'Paid' ? 'success' : ($log->status == 'Rejected' ? 'danger' : 'warning')) }} text-xs">{{ $log->status }}</span>
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
    @if($expense->status == 'Pending Approval')
        <div class="modal-footer d-block">
            <div class="form-group mb-3 text-start">
                <label for="comments" class="form-label">{{ __('Review Comments') }}</label>
                <textarea id="review_comments" name="comments" class="form-control" rows="2" placeholder="{{ __('Enter comments for approval/rejection/hold...') }}"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <form action="{{ route('erp-expenses.reject', $expense->id) }}" method="POST" class="d-inline" id="reject-form">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror">
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
                    </form>
                    <form action="{{ route('erp-expenses.hold', $expense->id) }}" method="POST" class="d-inline" id="hold-form">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror">
                        <button type="submit" class="btn btn-warning btn-sm ms-2">{{ __('Hold') }}</button>
                    </form>
                    <form action="{{ route('erp-expenses.send-back', $expense->id) }}" method="POST" class="d-inline" id="send-back-form">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror">
                        <button type="submit" class="btn btn-secondary btn-sm ms-2">{{ __('Send Back') }}</button>
                    </form>
                </div>
                <div>
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('erp-expenses.approve', $expense->id) }}" method="POST" class="d-inline" id="approve-form">
                        @csrf
                        <input type="hidden" name="comments" class="comments-mirror">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Approve Bill') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            $(document).on('keyup', '#review_comments', function() {
                $('.comments-mirror').val($(this).val());
            });
        </script>
    @endif
@endif
