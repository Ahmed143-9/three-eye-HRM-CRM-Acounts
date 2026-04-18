{{ Form::open(['url' => 'billing', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
            {{ Form::number('amount', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Payment Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', ['unpaid' => __('Unpaid'), 'paid' => __('Paid (Recorded as paid)')], null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
        
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Billing Direction'), ['class' => 'form-label']) }}
            {{ Form::select('type', ['due_to_client' => __('Payable'), 'due_to_me' => __('Receivable')], null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>

        <div class="col-12 mt-3"><h6 class="text-primary"><i class="ti ti-arrow-up-right"></i> {{ __('From Details (Sender)') }}</h6><hr class="mt-0 mb-2"></div>
        <div class="form-group col-md-4">
            {{ Form::label('from_date', __('From Date'), ['class' => 'form-label']) }}
            {{ Form::date('from_date', current(explode(' ', now())), ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('from_bank_name', __('Bank Name'), ['class' => 'form-label']) }}
            {{ Form::text('from_bank_name', '', ['class' => 'form-control', 'placeholder' => __('e.g., Chase Bank')]) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('from_bank_number', __('Account Number'), ['class' => 'form-label']) }}
            {{ Form::text('from_bank_number', '', ['class' => 'form-control', 'placeholder' => __('e.g., 123456')]) }}
        </div>

        <div class="col-12 mt-3"><h6 class="text-primary"><i class="ti ti-arrow-down-right"></i> {{ __('To Details (Receiver)') }}</h6><hr class="mt-0 mb-2"></div>
        <div class="form-group col-md-4">
            {{ Form::label('to_date', __('To Date'), ['class' => 'form-label']) }}
            {{ Form::date('to_date', current(explode(' ', now())), ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('to_bank_name', __('Bank Name'), ['class' => 'form-label']) }}
            {{ Form::text('to_bank_name', '', ['class' => 'form-control', 'placeholder' => __('e.g., Bank of America')]) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('to_bank_number', __('Account Number'), ['class' => 'form-label']) }}
            {{ Form::text('to_bank_number', '', ['class' => 'form-control', 'placeholder' => __('e.g., 987654')]) }}
        </div>

        <div class="col-12 mt-3"><h6 class="text-primary"><i class="ti ti-align-left"></i> {{ __('Notes') }}</h6><hr class="mt-0 mb-2"></div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => '2', 'placeholder' => __('Any additional notes regarding this billing...')]) }}
        </div>

        <div class="col-12 mt-3"><h6 class="text-primary"><i class="ti ti-paperclip"></i> {{ __('File Attachment') }}</h6><hr class="mt-0 mb-2"></div>
        <div class="form-group col-md-12">
            {{ Form::label('attachment', __('Upload Banking Details / PDF / Image (Optional)'), ['class' => 'form-label']) }}
            {{ Form::file('attachment', ['class' => 'form-control', 'accept' => '.jpeg,.png,.jpg,.pdf,.doc,.docx']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
