<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>{{ __('Buying Details') }}</h5>
    </div>
    <div class="card-body">
        {{ Form::open(['route' => ['sales-orders.buying.store', $order->id], 'method' => 'POST']) }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('supplier_id', __('Select Supplier'), ['class' => 'form-label']) }}
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">{{ __('Select Supplier') }}</option>
                        @foreach($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ ($order->buying && $order->buying->supplier_id == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="buying-items-table">
                <thead>
                    <tr>
                        <th>{{ __('Item Name') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody id="buying-items-body">
                    @if($order->buying && $order->buying->items->count() > 0)
                        @foreach($order->buying->items as $index => $item)
                        <tr class="item-row">
                            <td><input type="text" name="items[{{ $index }}][item]" class="form-control" value="{{ $item->item_name }}" required></td>
                            <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}"></td>
                            <td><input type="number" name="items[{{ $index }}][qty]" class="form-control b-qty" value="{{ $item->quantity }}" required></td>
                            <td>
                                <select name="items[{{ $index }}][unit]" class="form-control b-unit" required>
                                    @foreach($units as $val => $label)
                                        <option value="{{ $val }}" {{ $item->unit == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                    <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control b-price" value="{{ $item->price }}" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][total]" class="form-control b-total" value="{{ $item->total }}" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-b-item"><i class="ti ti-trash"></i></button></td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="item-row">
                            <td><input type="text" name="items[0][item]" class="form-control" required></td>
                            <td><input type="text" name="items[0][description]" class="form-control"></td>
                            <td><input type="number" name="items[0][qty]" class="form-control b-qty" required></td>
                            <td>
                                <select name="items[0][unit]" class="form-control b-unit" required>
                                    @foreach($units as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                    <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="items[0][price]" class="form-control b-price" required></td>
                            <td><input type="number" step="0.01" name="items[0][total]" class="form-control b-total" value="0.00" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-b-item"><i class="ti ti-trash"></i></button></td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm add-b-item mb-3"><i class="ti ti-plus"></i> {{ __('Add Item') }}</button>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-6">
                <div class="form-group">
                    {{ Form::label('total_amount', __('Grand Total Amount'), ['class' => 'form-label']) }}
                    {{ Form::number('total_amount', $order->buying->total_amount ?? 0, ['class' => 'form-control', 'id' => 'buying_grand_total', 'readonly', 'step' => '0.01']) }}
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">{{ __('Save Buying Details') }}</button>
        {{ Form::close() }}
    </div>
</div>

@push('script-page')
<script>
    jQuery(document).ready(function($) {
        // Add Row
        $(document).on('click', '.add-b-item', function() {
            var index = $('#buying-items-body tr').length;
            var unitOptions = `@foreach($units as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach`;
            var html = `<tr class="item-row">
                <td><input type="text" name="items[${index}][item]" class="form-control" required></td>
                <td><input type="text" name="items[${index}][description]" class="form-control"></td>
                <td><input type="number" name="items[${index}][qty]" class="form-control b-qty" required></td>
                <td>
                    <select name="items[${index}][unit]" class="form-control b-unit" required>
                        ${unitOptions}
                        <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[${index}][price]" class="form-control b-price" required></td>
                <td><input type="number" step="0.01" name="items[${index}][total]" class="form-control b-total" value="0.00" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-b-item"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#buying-items-body').append(html);
        });

        // Remove Row
        $(document).on('click', '.remove-b-item', function() { 
            $(this).closest('tr').remove(); 
            calculateBuyingTotal(); 
        });
        
        // Calculate Row and Grand Total
        $(document).on('keyup change', '.b-qty, .b-price', function() {
            var tr = $(this).closest('tr');
            var qty = parseFloat(tr.find('.b-qty').val()) || 0;
            var price = parseFloat(tr.find('.b-price').val()) || 0;
            var total = qty * price;
            tr.find('.b-total').val(total.toFixed(2));
            calculateBuyingTotal();
        });

        function calculateBuyingTotal() {
            var grandTotal = 0;
            $('.b-total').each(function() { grandTotal += parseFloat($(this).val()) || 0; });
            $('#buying_grand_total').val(grandTotal.toFixed(2));
        }
    });
</script>
@endpush
