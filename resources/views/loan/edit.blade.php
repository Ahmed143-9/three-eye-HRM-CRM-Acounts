{{Form::model($loan,array('route' => array('loan.update', $loan->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate', 'files'=>true)) }}
<div class="modal-body">
    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('loan_option', __('Loan Options'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('loan_option',$loan_options,null, array('class' => 'form-control select','required'=>'required')) }}
                    <div class="text-xs mt-1">
                        {{ __('Create loan option here.') }} <a href="{{ route('loanoption.index') }}"><b>{{ __('Create loan option') }}</b></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('type', $loans, null, ['class' => 'form-control select amount_type', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('amount', __('Loan Amount'),['class'=>'form-label amount_label']) }}<x-required></x-required>
                    {{ Form::number('amount',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Amount'))) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::textarea('reason',null, array('class' => 'form-control ','required'=>'required','rows' => 3, 'placeholder'=>__('Enter Reason'))) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
                    <small class="text-muted">({{ __('Max 5MB') }})</small>
                    @if(!empty($loan->attachment))
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <span class="text-muted small">{{ __('Current') }}:</span>
                            @php
                                $ext = strtolower(pathinfo($loan->attachment, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg','jpeg','png']);
                            @endphp
                            @if($isImage)
                                <a href="{{ route('loan.attachment.download', $loan->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye"></i> {{ __('View') }}
                                </a>
                            @endif
                            <a href="{{ route('loan.attachment.download', $loan->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ti ti-download"></i> {{ __('Download') }}
                            </a>
                            <span class="text-muted small">{{ __('Upload a new file below to replace it.') }}</span>
                        </div>
                    @endif
                    <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
