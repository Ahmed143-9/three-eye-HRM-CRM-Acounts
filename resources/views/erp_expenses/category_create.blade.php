{{ Form::open(['route' => ['erp-expense-category.quick-store', $type], 'method' => 'post', 'id' => 'quick_category_form']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Category Name')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    $('#quick_category_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    var newOption = new Option(response.name, response.id, true, true);
                    $('#expense_category').append(newOption).trigger('change');
                    $('#commonModalOver').modal('hide');
                    show_toastr('Success', 'Category created successfully', 'success');
                }
            }
        });
    });
</script>
