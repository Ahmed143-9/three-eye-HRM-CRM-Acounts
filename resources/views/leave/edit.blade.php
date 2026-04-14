{{Form::model($leave,array('route' => array('leave.update', $leave->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate', 'files'=>true)) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['leave']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}

    @if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('employee_id',__('Employee') ,['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('employee_id',$employees,null,array('class'=>'form-control select','placeholder'=>__('Select Employee'), 'required' => 'required'))}}
                <div class="text-xs mt-1">
                    {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('leave_type_id',__('Leave Type'),['class'=>'form-label'])}}<x-required></x-required>
                <select name="leave_type_id" id="leave_type_id" class="form-control select" required>
                    <option value="">{{ __('Select Leave Type') }}</option>
                    @foreach($leavetypes as $lt)
                        <option value="{{ $lt->id }}" {{ $leave->leave_type_id == $lt->id ? 'selected' : '' }}>
                            {{ $lt->title }} ({{ $lt->days }})
                        </option>
                    @endforeach
                </select>
                <div class="text-xs mt-1">
                    {{ __('Create leave type here.') }} <a href="{{ route('leavetype.index') }}"><b>{{ __('Create leave type') }}</b></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('start_date',__('Start Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('start_date',null,array('class'=>'form-control','required' =>'required'))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('end_date',__('End Date'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::date('end_date',null,array('class'=>'form-control','required' =>'required'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('leave_reason',__('Leave Reason'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::textarea('leave_reason',null,array('class'=>'form-control','placeholder'=>__('Leave Reason'),'required' =>'required'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm text-right" data-ajax-popup-over="true" id="grammarCheck" data-url="{{ route('grammar',['grammar']) }}"
               data-bs-placement="top" data-title="{{ __('Grammar check with AI') }}">
                <i class="ti ti-rotate"></i> <span>{{__('Grammar check with AI')}}</span>
            </a>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('remark',__('Remark'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::textarea('remark',null,array('class'=>'form-control grammer_textarea','placeholder'=>__('Leave Remark'),'required' =>'required'))}}
            </div>
        </div>
    </div>

    {{-- Existing Attachments --}}
    @if($leave->attachments->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Existing Attachments') }}</label>
                <div class="list-group" id="existing-attachments-list">
                    @foreach($leave->attachments as $att)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2" id="attachment-item-{{ $att->id }}">
                        <span class="text-truncate me-2">
                            <i class="ti ti-paperclip me-1"></i>{{ $att->original_name }}
                        </span>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ route('leave.attachment.download', $att->id) }}"
                               class="btn btn-sm btn-outline-info" title="{{ __('Download') }}">
                                <i class="ti ti-download"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="deleteLeaveAttachment({{ $att->id }})"
                                    title="{{ __('Remove') }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Add New Attachments --}}
    <div class="row" id="attachment-section">
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="leave-attachments">
                    {{ __('Add Attachments') }}
                    <span id="attachment-required-badge" class="text-danger" style="display:none;">*</span>
                </label>
                <input type="file" name="attachments[]" class="form-control" id="leave-attachments" multiple>
                <small id="attachment-hint" class="text-muted"></small>
            </div>
        </div>
    </div>

    @role('Company')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('status',__('Status'))}}
                <select name="status" id="" class="form-control select2">
                    <option value="">{{__('Select Status')}}</option>
                    <option value="pending" @if($leave->status=='Pending') selected="" @endif>{{__('Pending')}}</option>
                    <option value="approval" @if($leave->status=='Approval') selected="" @endif>{{__('Approval')}}</option>
                    <option value="reject" @if($leave->status=='Reject') selected="" @endif>{{__('Reject')}}</option>
                </select>
            </div>
        </div>
    </div>
    @endrole

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
    {{Form::close()}}

<script>
    // AJAX attachment deletion — avoids nested-form HTML invalidity
    function deleteLeaveAttachment(id) {
        if (!confirm('{{ __("Remove this attachment?") }}')) return;

        fetch('/leave/attachment/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var item = document.getElementById('attachment-item-' + id);
                if (item) { item.remove(); }
            } else {
                alert(data.error || '{{ __("Failed to remove attachment.") }}');
            }
        })
        .catch(function() { alert('{{ __("Request failed.") }}'); });
    }

    // Build a map of leave type rules
    var leaveTypeRules = {
        @foreach($leavetypes as $lt)
        "{{ $lt->id }}": {
            is_attachment_required: {{ $lt->is_attachment_required ? 'true' : 'false' }},
            min_advance_days: {{ (int)$lt->min_advance_days }},
            existing_attachments: {{ $leave->attachments->count() }}
        },
        @endforeach
    };

    function updateAttachmentUI(leaveTypeId) {
        var rules = leaveTypeRules[leaveTypeId];
        var badge = document.getElementById('attachment-required-badge');
        var hint  = document.getElementById('attachment-hint');
        var input = document.getElementById('leave-attachments');

        if (rules && rules.is_attachment_required && rules.existing_attachments === 0) {
            badge.style.display = 'inline';
            hint.textContent    = '{{ __("A file attachment is mandatory for this leave type (e.g. medical certificate).") }}';
            input.setAttribute('required', 'required');
        } else {
            badge.style.display = 'none';
            input.removeAttribute('required');
            var hints = [];
            if (rules && rules.min_advance_days > 0) {
                hints.push('{{ __("Note:") }} ' + rules.min_advance_days + ' {{ __("days advance notice required for start date.") }}');
            }
            hint.textContent = hints.join(' ');
        }
    }

    document.getElementById('leave_type_id').addEventListener('change', function () {
        updateAttachmentUI(this.value);
    });

    var employee_id = "{{$employee_id}}";
    var leave_type_id = "{{isset($leave) ? $leave->leave_type_id : null}}";
    leaveCount(employee_id, leave_type_id);

    // Run on load to show/hide attachment hint for current type
    updateAttachmentUI(leave_type_id);
</script>
