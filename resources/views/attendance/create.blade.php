{{Form::open(array('url'=>'attendanceemployee','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('employee_id',__('Employee'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('employee_id',$employees,null,array('class'=>'form-control select','placeholder'=>__('Select Employee'),'required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('date',__('Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('clock_in',__('Clock In'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::time('clock_in',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('clock_out',__('Clock Out'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::time('clock_out',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}

