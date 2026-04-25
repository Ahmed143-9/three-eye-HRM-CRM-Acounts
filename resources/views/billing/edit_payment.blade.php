{{ Form::open(['route' => ['billing.payment.update', $payment->id], 'method' => 'POST', 'id' => 'edit-payment-form']) }}
<div class="modal-body">
    {{-- Current entry info --}}
    <div class="alert alert-light border mb-3 p-2 small">
        <strong>{{ __('Editing payment entry #') }}{{ $payment->id }}</strong>
        &nbsp;|&nbsp; {{ __('Invoice:') }}
        <strong>{{ optional($payment->billable)->invoice_number }}</strong>
    </div>

    <div class="row g-2">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('amount', __('Payment Amount'), ['class' => 'form-label']) }}
                {{ Form::number('amount', $payment->amount, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required' => true]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Payment Date'), ['class' => 'form-label']) }}
                {{ Form::date('date', $payment->date, ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('payment_method', __('Payment Method'), ['class' => 'form-label']) }}
                {{ Form::select('payment_method',
                    ['' => '-- Select --', 'Cash' => 'Cash', 'Bank Transfer' => 'Bank Transfer', 'Cheque' => 'Cheque', 'Other' => 'Other'],
                    $payment->payment_method,
                    ['class' => 'form-control select']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('next_due_date', __('Next Due Date'), ['class' => 'form-label']) }}
                {{ Form::date('next_due_date', $payment->next_due_date, ['class' => 'form-control']) }}
            </div>
        </div>

        {{-- Adjustment section --}}
        <div class="col-12 mt-1">
            <div class="border rounded p-3 bg-light">
                <h6 class="mb-2"><i class="ti ti-adjustments me-1"></i>{{ __('Adjustment / Discount / Write-off') }}</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        {{ Form::label('adjustment_amount', __('Adjustment Amount'), ['class' => 'form-label']) }}
                        {{ Form::number('adjustment_amount', $payment->adjustment_amount, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}
                        <small class="text-muted">{{ __('Amount waived, discounted, or written off.') }}</small>
                    </div>
                    <div class="col-md-6">
                        {{ Form::label('adjustment_reason', __('Adjustment Reason'), ['class' => 'form-label']) }}
                        {{ Form::text('adjustment_reason', $payment->adjustment_reason, ['class' => 'form-control', 'placeholder' => 'e.g. Discount, Write-off']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
                {{ Form::textarea('note', $payment->note, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{-- Delete entry trigger button --}}
    <button type="button" class="btn btn-sm btn-danger me-auto"
            onclick="if(confirm('{{ __('Delete this payment entry? This will affect the billing balance.') }}')) document.getElementById('delete-payment-{{ $payment->id }}').submit();">
        <i class="ti ti-trash me-1"></i>{{ __('Delete Entry') }}
    </button>

    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

{{-- Separate hidden Delete form --}}
{{ Form::open(['route' => ['billing.payment.delete', $payment->id], 'method' => 'DELETE', 'id' => 'delete-payment-' . $payment->id, 'style' => 'display:none;']) }}
{{ Form::close() }}

