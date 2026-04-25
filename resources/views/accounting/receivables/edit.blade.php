@extends('layouts.admin')
@php
    $settings = Utility::settings();
@endphp
@section('page-title', __('Edit Receivable'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('receivables.index') }}">{{ __('Receivable') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@push('css-page')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('action-btn')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <form method="POST" action="{{ route('receivables.update', $receivable->id) }}">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Unique ID') }}</label>
                            <input type="text" class="form-control" name="unique_id" value="{{ $receivable->unique_id }}" readonly required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Invoice Number') }}</label>
                            <input type="text" class="form-control" name="invoice_number" value="{{ $receivable->invoice_number }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="date" value="{{ $receivable->date }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Billing Direction') }}</label>
                            <select class="form-control select2" name="billing_direction" id="billing_direction" required>
                                <option value="">{{ __('Select Direction') }}</option>
                                <option value="Client" {{ $receivable->billing_direction == 'Client' ? 'selected' : '' }}>{{ __('Client') }}</option>
                                <option value="Supplier" {{ $receivable->billing_direction == 'Supplier' ? 'selected' : '' }}>{{ __('Supplier') }}</option>
                                <option value="Consultant" {{ $receivable->billing_direction == 'Consultant' ? 'selected' : '' }}>{{ __('Consultant') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="entity_div">
                            <label class="form-label" id="entity_label">{{ __($receivable->billing_direction) }}</label>
                            <select class="form-control select2" name="entity_id" id="entity_id" required>
                                <option value="">{{ __('Select') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Billing Address') }}</label>
                            <textarea class="form-control" name="billing_address" id="billing_address" rows="2">{{ $receivable->billing_address }}</textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select class="form-control" name="status" required>
                                <option value="unpaid" {{ $receivable->status == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                <option value="partial" {{ $receivable->status == 'partial' ? 'selected' : '' }}>{{ __('Partial') }}</option>
                                <option value="paid" {{ $receivable->status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ __('Items') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items_table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">{{ __('Serial') }}</th>
                                    <th>{{ __('Order Details') }}</th>
                                    <th style="width: 120px;">{{ __('Qty') }}</th>
                                    <th style="width: 150px;">{{ __('Rate') }}</th>
                                    <th style="width: 150px;">{{ __('Amount') }}</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receivable->items as $index => $item)
                                    <tr data-id="{{ $index + 1 }}">
                                        <td><input type="text" class="form-control" name="items[{{ $index }}][serial]" value="{{ $item->serial }}"></td>
                                        <td><textarea class="form-control" name="items[{{ $index }}][order_details]" rows="1">{{ $item->order_details }}</textarea></td>
                                        <td><input type="number" class="form-control qty" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" step="0.01"></td>
                                        <td><input type="number" class="form-control rate" name="items[{{ $index }}][rate]" value="{{ $item->rate }}" step="0.01"></td>
                                        <td><input type="number" class="form-control amount" name="items[{{ $index }}][amount]" value="{{ $item->amount }}" step="0.01" readonly></td>
                                        <td>
                                            @if($index > 0)
                                                <button type="button" class="btn btn-sm btn-danger remove_row"><i class="ti ti-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Total Amount') }}</strong></td>
                                    <td><input type="number" class="form-control" name="total_amount" id="total_amount" value="{{ $receivable->total_amount }}" step="0.01" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-primary" id="add_row"><i class="ti ti-plus"></i></button></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const allSuppliers = @json($suppliers);
        const allClients = @json($clients);
        const allConsultants = @json($consultants);
        const selectedEntityId = "{{ $receivable->entity_id }}";

        function updateEntities(direction, selectedId = null) {
            const entitySelect = $('#entity_id');
            const entityDiv = $('#entity_div');
            const entityLabel = $('#entity_label');

            if (direction) {
                entityDiv.show();
                entityLabel.text(direction);
                
                let dataArray = [];
                if (direction === 'Supplier') dataArray = allSuppliers;
                else if (direction === 'Client') dataArray = allClients;
                else if (direction === 'Consultant') dataArray = allConsultants;

                entitySelect.empty().append('<option value="">{{ __("Select") }}</option>');

                if (dataArray.length > 0) {
                    $.each(dataArray, function(i, item) {
                        const selected = item.id == selectedId ? 'selected' : '';
                        entitySelect.append('<option value="' + item.id + '" data-address="' + (item.billing_address || '') + '" ' + selected + '>' + item.name + '</option>');
                    });
                } else {
                    entitySelect.append('<option value="">{{ __("No data available") }}</option>');
                }

                // Refresh Select2
                if (entitySelect.hasClass('select2-hidden-accessible')) {
                    entitySelect.select2('destroy');
                }
                entitySelect.select2({
                    width: '100%'
                });
            } else {
                entityDiv.hide();
                entitySelect.empty().append('<option value="">{{ __("Select") }}</option>');
            }
        }

        // Initialize with existing data
        updateEntities($('#billing_direction').val(), selectedEntityId);

        $('#billing_direction').on('change', function() {
            updateEntities($(this).val());
            $('#billing_address').val('');
        });

        $('#entity_id').on('change', function() {
            const address = $(this).find(':selected').data('address');
            $('#billing_address').val(address || '');
        });

        let rowId = {{ $receivable->items->count() }};
        $('#add_row').on('click', function() {
            const newRow = `
                <tr data-id="${rowId + 1}">
                    <td><input type="text" class="form-control" name="items[${rowId}][serial]" value="${rowId + 1}"></td>
                    <td><textarea class="form-control" name="items[${rowId}][order_details]" rows="1"></textarea></td>
                    <td><input type="number" class="form-control qty" name="items[${rowId}][qty]" value="0" step="0.01"></td>
                    <td><input type="number" class="form-control rate" name="items[${rowId}][rate]" value="0" step="0.01"></td>
                    <td><input type="number" class="form-control amount" name="items[${rowId}][amount]" value="0" step="0.01" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove_row"><i class="ti ti-trash"></i></button></td>
                </tr>
            `;
            $('#items_table tbody').append(newRow);
            rowId++;
        });

        $(document).on('click', '.remove_row', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $(document).on('input', '.qty, .rate', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const rate = parseFloat(row.find('.rate').val()) || 0;
            const amount = (qty * rate).toFixed(2);
            row.find('.amount').val(amount);
            calculateTotal();
        });

        function calculateTotal() {
            let total = 0;
            $('.amount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_amount').val(total.toFixed(2));
        }
    });
</script>
@endpush
