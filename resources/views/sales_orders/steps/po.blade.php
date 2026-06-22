<h5>{{ __('Step 1: Purchase Order (PO)') }}</h5>
<hr>
{{ Form::open(['route' => ['sales-orders.po.store', $order->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('po_number', __('PO Number'), ['class' => 'form-label']) }}
            {{ Form::text('po_number', $order->po->po_number ?? 'PO-' . time(), ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('file', __('PO PDF Upload'), ['class' => 'form-label']) }}
            {{ Form::file('file', ['class' => 'form-control', 'accept' => '.pdf,image/*']) }}
            @if($order->po && $order->po->file_path)
                <div class="mt-2">
                    @php
                        $ext = pathinfo($order->po->file_path, PATHINFO_EXTENSION);
                    @endphp
                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ asset($order->po->file_path) }}" alt="PO Preview" class="img-thumbnail" style="max-height: 150px; display: block; margin-bottom: 10px;">
                    @else
                        <div class="alert alert-info py-1 px-2 d-inline-block text-sm mb-2">
                            <i class="ti ti-file-text me-1"></i> {{ __('Uploaded File: ') }} {{ basename($order->po->file_path) }}
                        </div>
                        <br>
                    @endif
                    <a href="{{ asset($order->po->file_path) }}" target="_blank" class="btn btn-sm btn-info"><i class="ti ti-eye me-1"></i>{{ __('View Uploaded PO Document') }}</a>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_name', __('Client Name'), ['class' => 'form-label']) }}
            {{ Form::text('client_name', $order->po->client_name ?? $order->customer->name, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_email', __('Client Email'), ['class' => 'form-label']) }}
            {{ Form::email('client_email', $order->po->client_email ?? $order->customer->contact_person_email, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('client_phone', __('Client Phone'), ['class' => 'form-label']) }}
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
            {{ Form::label('client_address', __('Client Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('client_address', $order->po->client_address ?? $order->customer->billing_address, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('hs_code', __('HS Code'), ['class' => 'form-label']) }}
            {{ Form::text('hs_code', $order->po->hs_code ?? '', ['class' => 'form-control']) }}
        </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('status', __('PO Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', ['Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected'], $order->po->status ?? 'Pending', ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('prepared_by', __('Prepared By'), ['class' => 'form-label']) }}
            {{ Form::text('prepared_by', $order->po->prepared_by ?? \Auth::user()->name, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('issued_by', __('Issued By'), ['class' => 'form-label']) }}
            {{ Form::text('issued_by', $order->po->issued_by ?? '', ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('acknowledged_by', __('Acknowledged By'), ['class' => 'form-label']) }}
            {{ Form::text('acknowledged_by', $order->po->acknowledged_by ?? '', ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<div class="d-flex align-items-center gap-2 mb-3">
    <i class="ti ti-list-details text-primary"></i>
    <h6 class="fw-semibold mb-0 text-dark">{{ __('Order Details') }}</h6>
</div>
<div class="table-responsive mt-3">
    <table class="table table-hover mb-0" id="po-items-table">
        <thead>
            <tr>
                <th width="20%">{{ __('Item') }}</th>
                <th width="20%">{{ __('Description') }}</th>
                <th width="10%">{{ __('QTY') }}</th>
                <th width="12%">{{ __('Unit') }}</th>
                <th width="12%">{{ __('Price Per Unit') }}</th>
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
                                    <option value="{{$val}}" {{ $item->unit == $val ? 'selected' : '' }}>{{$label}}</option>
                                @endforeach
                                <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="items[{{$index}}][price]" class="form-control price"
                                value="{{$item->price}}" required></td>
                        <td>
                            <select name="items[{{$index}}][currency]" class="form-control curr-select" required>
                                @foreach($currencies as $val => $label)
                                    <option value="{{$val}}" {{ ($item->currency ?? 'D.') == $val ? 'selected' : '' }}>{{$label}}
                                    </option>
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
                                <option value="{{$val}}" {{ $val == 'Pc' ? 'selected' : '' }}>{{$label}}</option>
                            @endforeach
                            <option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="items[0][price]" class="form-control price" required></td>
                    <td>
                        <select name="items[0][currency]" class="form-control curr-select" required>
                            @foreach($currencies as $val => $label)
                                <option value="{{$val}}" {{ $val == 'D.' ? 'selected' : '' }}>{{$label}}</option>
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
                <td colspan="6" class="text-end fw-bold align-middle">{{ __('Grand Total') }}:</td>
                <td class="fw-bold align-middle"><span
                        id="grand_total_display">{{ number_format($order->po->grand_total ?? 0, 2) }}</span></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm add-item" title="{{ __('Add Row') }}"
                        style="background-color: #6fd943; border-color: #6fd943; color: white;">
                        <i class="ti ti-plus"></i>
                    </button>
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

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($order->po)
            <a href="{{ route('sales-orders.po.print', $order->id) }}" target="_blank" class="btn btn-secondary"><i
                    class="ti ti-printer me-1"></i>{{ __('Print') }}</a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#poPreviewModal">
                <i class="ti ti-eye me-1"></i>{{ __('Preview & Download PDF') }}
            </button>
        @endif
    </div>

    <button type="submit" class="btn btn-success d-inline-flex align-items-center"
        style="background-color: #6fd943; border-color: #6fd943; padding: 10px 25px; font-weight: 600;">
        {{ __('Save & Proceed to PI') }}
        <i class="ti ti-chevron-right ms-2"></i>
    </button>
</div>
{{ Form::close() }}

<!-- PO Preview Modal -->
@if($order->po)
<div class="modal fade" id="poPreviewModal" tabindex="-1" aria-labelledby="poPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="poPreviewModalLabel"><i class="ti ti-file-text me-2"></i>{{ __('Preview & Edit Commercial Terms') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-primary mb-3">
            <small>{{ __('The details below will be included in the final generated Purchase Order PDF. Review and modify the commercial terms as needed before downloading.') }}</small>
        </div>
        <form id="poPreviewForm" action="{{ route('sales-orders.po.download', $order->id) }}" method="POST" target="_blank">
            @csrf
            
            <h6 class="mb-3 border-bottom pb-2">{{ __('Commercial Terms') }}</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Port of Loading') }}</label>
                    <input type="text" name="port_of_loading" class="form-control" value="{{ $order->po->port_of_loading ?? 'Any Port in India' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Port of Discharge') }}</label>
                    <input type="text" name="port_of_discharge" class="form-control" value="{{ $order->po->port_of_discharge ?? 'Tamabil, Bangladesh' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Final Destination') }}</label>
                    <input type="text" name="final_destination" class="form-control" value="{{ $order->po->final_destination ?? 'Tamabil, Bangladesh' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Country of Origin') }}</label>
                    <input type="text" name="country_of_origin" class="form-control" value="{{ $order->po->country_of_origin ?? 'India' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Packing') }}</label>
                    <input type="text" name="packing" class="form-control" value="{{ $order->po->packing ?? 'Road Tanker' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Transport Mode') }}</label>
                    <input type="text" name="transport_mode" class="form-control" value="{{ $order->po->transport_mode ?? 'By Road' }}">
                </div>
            </div>

            <h6 class="mb-3 border-bottom pb-2 mt-2">{{ __('General Terms & Conditions') }}</h6>
            <div class="mb-3">
                <textarea name="general_terms" class="form-control" rows="8">
{{ $order->po->terms_and_conditions ?? "1. Any amendment to this Purchase Order shall be valid only when accepted by both parties in writing.
2. The supplier shall complete shipment of the ordered quantity within 30 (Thirty) days from the date of issuance of operative Letter of Credit (LC). Any anticipated delay in shipment must be communicated to the buyer in writing at least 7 (Seven) days prior to the scheduled shipment date.
3. Any matter not specifically covered in this Purchase Order shall be settled through mutual discussion and agreement between the Buyer and Seller." }}
</textarea>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" class="btn btn-info" onclick="$('#poPreviewModal').modal('hide')"><i class="ti ti-download me-1"></i>{{ __('Download PDF') }}</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endif