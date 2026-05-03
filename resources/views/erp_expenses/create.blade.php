{{ Form::open(['route' => ['erp-expenses.store', $type], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('erp_expense_category_id', __('Category'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('erp_expense_category_id', ['' => 'Select Category'] + $categories->toArray(), null, ['class' => 'form-control select2', 'required' => 'required', 'id' => 'expense_category']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>

        @if($type == 'convenience')
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('billing_month', __('Billing Month'), ['class' => 'form-label']) }}
                    {{ Form::month('billing_month', date('Y-m'), ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-md-6 d-none" id="transport_box">
                <div class="form-group">
                    {{ Form::label('transport_id', __('Link Transport Sheet (Optional)'), ['class' => 'form-label']) }}
                    {{ Form::select('transport_id', ['' => 'Select Transport'] + $transports->toArray(), null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        @endif

        @if($type == 'purchase' || $type == 'convenience' || $type == 'utility' || $type == 'salary')
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('employee_id', $type == 'purchase' ? __('Buyer Name') : ($type == 'utility' ? __('Paid By') : ($type == 'convenience' ? __('Consignee Name') : __('Employee Name'))), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                    {{ Form::select('employee_id', ['' => 'Select Employee'] + $employees->toArray(), $sheet ? $sheet->employee_id : null, ['class' => 'form-control select2', 'id' => 'employee_id', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('designation', __('Designation'), ['class' => 'form-label']) }}
                    {{ Form::text('designation', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'designation']) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                    {{ Form::text('department', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'department']) }}
                </div>
            </div>
        @endif

        @if($type == 'salary')
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('net_salary', __('Net Salary'), ['class' => 'form-label']) }}
                    {{ Form::number('net_salary', $sheet ? $sheet->net_salary : 0, ['class' => 'form-control salary-calc', 'step' => '0.01', 'id' => 'net_salary']) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('deduction_amount', __('Deduction Amount'), ['class' => 'form-label']) }}
                    {{ Form::number('deduction_amount', $sheet ? $sheet->deduction_amount : 0, ['class' => 'form-control salary-calc', 'step' => '0.01', 'id' => 'deduction_amount']) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('amount', __('Final Salary (Auto Calculated)'), ['class' => 'form-label']) }}
                    {{ Form::number('amount', $sheet ? $sheet->final_salary : 0, ['class' => 'form-control', 'step' => '0.01', 'id' => 'final_salary', 'readonly' => 'readonly']) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('cause_of_deduction', __('Cause Of Deduction'), ['class' => 'form-label']) }}
                    {{ Form::text('cause_of_deduction', null, ['class' => 'form-control']) }}
                </div>
            </div>
            @if($sheet)
                {{ Form::hidden('erp_salary_sheet_id', $sheet->id) }}
            @endif
        @endif

        @if($type == 'utility')
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                    {{ Form::number('amount', 0, ['class' => 'form-control', 'step' => '0.01', 'required' => 'required']) }}
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Details / Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
                <div class="choose-files">
                    <label for="attachment">
                        <div class=" bg-primary attachment "> <i
                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                        </div>
                        <input type="file" class="form-control file" name="attachment" id="attachment"
                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" style="display: none;">
                        <img id="blah" alt="your image" width="100" src="" class="mt-2"/>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label']) }}
                {{ Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>

    @if($type == 'purchase' || $type == 'convenience')
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Product / Expense List') }}</h5>
            <div class="card repeater">
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a href="#" data-url="{{ route('erp-expense-unit.quick-create') }}" 
                                   data-ajax-popup-over="true" data-title="{{ __('Create New Unit') }}" 
                                   class="btn btn-info btn-sm text-white me-2">
                                    <i class="ti ti-plus"></i> {{ __('Add Unit') }}
                                </a>
                                <a href="#" data-repeater-create="" class="btn btn-primary btn-sm">
                                    <i class="ti ti-plus"></i> {{ __('Add item') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0" data-repeater-list="items" id="sortable-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Description / Product Name') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Unit Price / Rate') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                    <td width="30%">
                                        {{ Form::text('product_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Description')]) }}
                                    </td>
                                    <td width="15%">
                                        {{ Form::number('quantity', 1, ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'step' => '0.01']) }}
                                    </td>
                                    <td width="15%">
                                        {{ Form::select('unit', $units, null, ['class' => 'form-control unit select2-tags', 'placeholder' => __('Unit')]) }}
                                    </td>
                                    <td width="15%">
                                        {{ Form::number('unit_price', null, ['class' => 'form-control unit_price', 'required' => 'required', 'placeholder' => __('Price'), 'step' => '0.01']) }}
                                    </td>
                                    <td width="20%">
                                        {{ Form::number('amount', null, ['class' => 'form-control amount', 'required' => 'required', 'placeholder' => __('Amount'), 'readonly' => 'readonly', 'step' => '0.01']) }}
                                    </td>
                                    <td width="5%">
                                        <a href="#" class="ti ti-trash text-white text-white action-btn bg-danger p-2" data-repeater-delete></a>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                    <td class="text-end"><strong id="total_amount">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Employee Info Fetching
        $(document).on('change', '#employee_id', function() {
            var emp_id = $(this).val();
            if(emp_id) {
                $.ajax({
                    url: '{{ route('erp-expenses.employee-info') }}',
                    type: 'GET',
                    data: { employee_id: emp_id },
                    success: function(response) {
                        $('#designation').val(response.designation);
                        $('#department').val(response.department);
                    }
                });
            } else {
                $('#designation').val('');
                $('#department').val('');
            }
        });

        @if($sheet)
            $('#employee_id').trigger('change');
        @endif

        @if($type == 'convenience')
        // Show Transport Box if Transport Category Selected
        $(document).on('change', '#expense_category', function() {
            var selectedText = $(this).find('option:selected').text();
            if(selectedText.toLowerCase() === 'transport' || selectedText.toLowerCase() === 'transport bill') {
                $('#transport_box').removeClass('d-none');
            } else {
                $('#transport_box').addClass('d-none');
            }
        });
        $('#expense_category').trigger('change');
        @endif

        @if($type == 'salary')
        $(document).on('keyup change', '.salary-calc', function() {
            var net = parseFloat($('#net_salary').val()) || 0;
            var ded = parseFloat($('#deduction_amount').val()) || 0;
            $('#final_salary').val((net - ded).toFixed(2));
        });
        @endif

        @if($type == 'purchase' || $type == 'convenience')
        var selector = "body";
        if ($(".repeater").length) {
            var $repeater = $('.repeater').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                    // Re-initialize select2 tags for the new item
                    if ($(".select2-tags").length > 0) {
                        $('.select2-tags').select2({
                            tags: true,
                            dropdownParent: $("#commonModal")
                        });
                    }
                },
                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        setTimeout(function () {
                            calculateTotal();
                        }, 500);
                    }
                },
                isFirstItemUndeletable: true
            });
        }

        $(document).on('keyup change', '.quantity, .unit_price', function() {
            var parent = $(this).closest('tr');
            var qty = parseFloat(parent.find('.quantity').val()) || 0;
            var price = parseFloat(parent.find('.unit_price').val()) || 0;
            parent.find('.amount').val((qty * price).toFixed(2));
            calculateTotal();
        });

        function calculateTotal() {
            var total = 0;
            $('.amount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_amount').text(total.toFixed(2));
        }
        @endif
        if ($(".select2-tags").length > 0) {
            $('.select2-tags').select2({
                tags: true,
                dropdownParent: $("#commonModal")
            });
        }
    });
</script>
