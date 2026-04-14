    {{Form::open(array('url'=>'leavetype','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
    <div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('title',__('Leave Type'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Leave Type Name'),'required'=> 'required'))}}
                @error('title')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('days',__('Days Per Year'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::number('days',null,array('class'=>'form-control','placeholder'=>__('Enter Days / Year'),'required'=> 'required','min'=>'1'))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('min_advance_days',__('Minimum Advance Days'),['class'=>'form-label'])}}
                {{Form::number('min_advance_days',0,array('class'=>'form-control','placeholder'=>__('0 = No restriction'),'min'=>'0'))}}
                <small class="text-muted">{{ __('Minimum days in advance required before the leave start date (0 = no restriction).') }}</small>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_attachment_required" id="is_attachment_required" value="1">
                    <label class="form-check-label" for="is_attachment_required">{{ __('Require File Attachment') }}</label>
                </div>
                <small class="text-muted">{{ __('If enabled, employees must upload at least one file when applying for this leave type.') }}</small>
            </div>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
    {{Form::close()}}

