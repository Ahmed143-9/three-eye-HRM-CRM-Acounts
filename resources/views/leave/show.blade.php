<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table">
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <td>{{ !empty($leave->employees) ? $leave->employees->name : '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Type') }}</th>
                    <td>{{ !empty($leave->leaveType) ? $leave->leaveType->title : '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Applied On') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Start Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->start_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('End Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->end_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Total Days') }}</th>
                    <td>{{ $leave->total_leave_days }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Reason') }}</th>
                    <td>{{ $leave->leave_reason }}</td>
                </tr>
                <tr>
                    <th>{{ __('Remark') }}</th>
                    <td>{{ $leave->remark }}</td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>
                        @if($leave->status == 'Pending')
                            <span class="badge bg-warning">{{ $leave->status }}</span>
                        @elseif($leave->status == 'Approved')
                            <span class="badge bg-success">{{ $leave->status }}</span>
                        @else
                            <span class="badge bg-danger">{{ $leave->status }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Attachments') }}</th>
                    <td>
                        @if($leave->attachments->count() > 0)
                            <div class="d-flex flex-column gap-1">
                                @foreach($leave->attachments as $att)
                                    <a href="{{ route('leave.attachment.download', $att->id) }}"
                                       class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1"
                                       style="width: fit-content;">
                                        <i class="ti ti-download"></i>
                                        <span>{{ $att->original_name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">{{ __('No attachments') }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
