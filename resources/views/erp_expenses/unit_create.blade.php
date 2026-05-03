{{ Form::open(['route' => 'erp-expense-unit.quick-store', 'method' => 'post', 'id' => 'quick_unit_form']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Unit Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Unit Name')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    $('#quick_unit_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Update all unit selects in the repeater
                    var newOption = new Option(response.name, response.name, true, true);
                    $('.unit').each(function() {
                        $(this).append(new Option(response.name, response.name)).trigger('change');
                    });
                    
                    // Specifically set it for the most recently active one if possible, 
                    // but usually just adding it to the list is enough.
                    
                    $('#commonModalOver').modal('hide');
                    show_toastr('Success', 'Unit created successfully', 'success');
                }
            }
        });
    });
</script>
