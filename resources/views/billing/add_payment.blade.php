{{ Form::open(['route' => ['billing.store-payment', $type, $billable->id], 'method' => 'POST']) }}
<div class="modal-body">
    {{-- Invoice Summary --}}
    <div class="alert alert-light border mb-3 p-2">
        <div class="row text-center">
            <div class="col-4">
                <small class="text-muted d-block">{{ __('Total') }}</small>
                <strong>{{ \Auth::user()->priceFormat($billable->total_amount) }}</strong>
            </div>
            <div class="col-4">
                <small class="text-muted d-block">{{ __('Paid') }}</small>
                <strong class="text-success">{{ \Auth::user()->priceFormat($billable->getTotalPaid()) }}</strong>
            </div>
            <div class="col-4">
                <small class="text-muted d-block">{{ __('Remaining Due') }}</small>
                <strong class="text-danger">{{ \Auth::user()->priceFormat($billable->getDueAmount()) }}</strong>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('amount', __('Payment Amount'), ['class' => 'form-label']) }}
                {{ Form::number('amount', 0, ['class' => 'form-control', 'id' => 'paymentAmount', 'required' => 'required', 'step' => '0.01', 'min' => '0']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Payment Date'), ['class' => 'form-label']) }}
                {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('payment_method', __('Payment Method (Optional)'), ['class' => 'form-label']) }}
                {{ Form::select('payment_method', ['' => __('-- Select --'), 'Cash' => 'Cash', 'Bank Transfer' => 'Bank Transfer', 'Cheque' => 'Cheque', 'Other' => 'Other'], null, ['class' => 'form-control select']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('next_due_date', __('Next Due Date (Optional)'), ['class' => 'form-label']) }}
                {{ Form::date('next_due_date', null, ['class' => 'form-control']) }}
            </div>
        </div>

        {{-- Adjustment / Discount / Write-off Section --}}
        <div class="col-md-12 mt-2">
            <div class="border rounded p-3 bg-light">
                <h6 class="mb-3"><i class="ti ti-adjustments me-1"></i>{{ __('Adjustment / Discount / Write-off (Optional)') }}</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('adjustment_amount', __('Adjustment Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('adjustment_amount', 0, ['class' => 'form-control', 'id' => 'adjustmentAmount', 'step' => '0.01', 'min' => '0']) }}
                            <small class="text-muted">{{ __('Amount waived, discounted or written off.') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('adjustment_reason', __('Adjustment Reason'), ['class' => 'form-label']) }}
                            {{ Form::text('adjustment_reason', null, ['class' => 'form-control', 'placeholder' => __('e.g. Discount, Write-off, Settlement')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-2">
            <div class="form-group">
                {{ Form::label('note', __('Note (Optional)'), ['class' => 'form-label']) }}
                {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Any additional notes...')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Save Payment') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
