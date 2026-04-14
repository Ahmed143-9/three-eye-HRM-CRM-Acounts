{{Form::open(array('url'=>'leave/changeaction','method'=>'post'))}}
<div class="modal-body">

    {{-- Advance days warning --}}
    @if(!empty($leavetype) && $leavetype->min_advance_days > 0)
        @php
            $minDate   = \Carbon\Carbon::now()->addDays($leavetype->min_advance_days)->startOfDay();
            $startDate = \Carbon\Carbon::parse($leave->start_date)->startOfDay();
        @endphp
        @if($startDate->lt($minDate))
            <div class="alert alert-warning mb-3">
                <i class="ti ti-alert-triangle me-1"></i>
                {{ __('Warning: This :type leave requires at least :days days advance notice. The start date does not meet this requirement and cannot be approved.', ['type' => $leavetype->title, 'days' => $leavetype->min_advance_days]) }}
            </div>
        @endif
    @endif

    <div class="row">
        <div class="col-12">
            <table class="table modal-table">
                <tr role="row">
                    <th>{{__('Employee')}}</th>
                    <td>{{ !empty($employee->name)?$employee->name:'' }}</td>
                </tr>
                <tr>
                    <th>{{__('Leave Type ')}}</th>
                    <td>{{ !empty($leavetype->title)?$leavetype->title:'' }}</td>
                </tr>
                <tr>
                    <th>{{__('Appplied On')}}</th>
                    <td>{{\Auth::user()->dateFormat( $leave->applied_on) }}</td>
                </tr>
                <tr>
                    <th>{{__('Start Date')}}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->start_date) }}</td>
                </tr>
                <tr>
                    <th>{{__('End Date')}}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->end_date) }}</td>
                </tr>
                <tr>
                    <th>{{__('Leave Reason')}}</th>
                    <td>{{ !empty($leave->leave_reason)?$leave->leave_reason:'' }}</td>
                </tr>
                <tr>
                    <th>{{__('Status')}}</th>
                    <td>{{ !empty($leave->status)?$leave->status:'' }}</td>
                </tr>
                <tr>
                    <th>{{__('Attachments')}}</th>
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
                            <span class="text-muted">{{__('None')}}</span>
                        @endif
                    </td>
                </tr>
                <input type="hidden" value="{{ $leave->id }}" name="leave_id">
            </table>
        </div>
    </div>
</div>
{{-- @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'HR') --}}
<div class="modal-footer">
    <input type="submit" value="{{__('Approval')}}" class="btn btn-success" data-bs-dismiss="modal" name="status">
    <input type="submit" value="{{__('Reject')}}" class="btn btn-danger" name="status">
</div>
{{-- @endif --}}
{{Form::close()}}
