<h5>{{ __('Step 1: Product Order Details') }}</h5>
<hr>
{{ Form::open(['route' => ['sales-orders.po.store', $order->id], 'method' => 'post', 'id' => 'workflow-form']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_name', __('Company Name'), ['class' => 'form-label']) }}
            {{ Form::text('client_name', $order->po->client_name ?? $order->customer->name, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_email', __('Email'), ['class' => 'form-label']) }}
            {{ Form::email('client_email', $order->po->client_email ?? $order->customer->contact_person_email, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_phone', __('Phone'), ['class' => 'form-label']) }}
            {{ Form::text('client_phone', $order->po->client_phone ?? $order->customer->contact_person_number, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('signature', __('Authorized Signature Details'), ['class' => 'form-label']) }}
            {{ Form::text('signature', $order->po->signature ?? $order->customer->contact_person_name, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('client_address', __('Company Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('client_address', $order->po->client_address ?? $order->customer->billing_address, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('hs_code', __('HS Code'), ['class' => 'form-label']) }}
            {{ Form::text('hs_code', $order->po->hs_code ?? '', ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<div class="order-section mb-4">
    <h6 class="section-title">{{ __('Order Details') }}</h6>
</div>
<div class="table-responsive">
    <table class="table table-hover mb-0" id="po-items-table">
        <thead>
            <tr>
                <th width="20%">{{ __('Item') }}</th>
                <th width="20%">{{ __('Description') }}</th>
                <th width="10%">{{ __('QTY') }}</th>
                <th width="12%">{{ __('Unit') }}</th>
                <th width="12%">{{ __('Price per Unit') }}</th>
                <th width="12%">{{ __('Unit') }}</th>
                <th width="12%">{{ __('Total') }}</th>
                <th width="2%"></th>
            </tr>
        </thead>
        <tbody>
            @if($order->po && $order->po->items->count() > 0)
                @foreach($order->po->items as $index => $item)
                    <tr>
                        <td><input type="text" name="items[{{$index}}][item]" class="form-control" value="{{$item->item_name}}"
                                required></td>
                        <td><input type="text" name="items[{{$index}}][description]" class="form-control"
                                value="{{$item->description}}"></td>
                        <td><input type="number" name="items[{{$index}}][qty]" class="form-control qty"
                                value="{{$item->quantity}}" required></td>
                        <td>
                            <select name="items[{{$index}}][unit]" class="form-control unit-select" required>
                                @foreach($units as $val => $label)
                                    <option value="{{$val}}" {{ $item->unit_id == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="items[{{$index}}][price]" class="form-control price"
                                value="{{$item->price_per_unit}}" required></td>
                        <td>
                            <select name="items[{{$index}}][currency]" class="form-control curr-select" required>
                                @foreach($currencies as $val => $label)
                                    <option value="{{$val}}" {{ ($item->currency_type ?? 'BDT') == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="items[{{$index}}][total]" class="form-control total"
                                value="{{$item->total}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="items[0][item]" class="form-control" required></td>
                    <td><input type="text" name="items[0][description]" class="form-control"></td>
                    <td><input type="number" name="items[0][qty]" class="form-control qty" required></td>
                    <td>
                        <select name="items[0][unit]" class="form-control unit-select" required>
                            @foreach($units as $val => $label)
                                <option value="{{$val}}">{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="items[0][price]" class="form-control price" required></td>
                    <td>
                        <select name="items[0][currency]" class="form-control curr-select" required>
                            @foreach($currencies as $val => $label)
                                <option value="{{$val}}" {{ $val == 'BDT' ? 'selected' : '' }}>{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="items[0][total]" class="form-control total" readonly></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold align-middle">{{ __('Grand Total') }}:</td>
                <td class="fw-bold align-middle"><span
                        id="grand_total_display">{{ number_format($order->po->grand_total ?? 0, 2) }}</span></td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm add-item" title="{{ __('Add Row') }}"><i
                            class="ti ti-plus"></i></button>
                    <input type="hidden" name="grand_total" id="grand_total" value="{{ $order->po->grand_total ?? 0 }}">
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="col-md-12 mt-4">
    <div class="form-group">
        {{ Form::label('terms_and_conditions', __('Terms and Conditions'), ['class' => 'form-label']) }}
        {{ Form::textarea('terms_and_conditions', $order->po->terms_and_conditions ?? null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter terms and conditions...')]) }}
    </div>
</div>

{{ Form::close() }}