{{Form::open(array('url'=>'daily-accomplishments','method'=>'post'))}}
<div class="modal-body">
    <div class="row">
        @if(\Auth::user()->type != 'Employee')
            <div class="form-group col-md-12">
                {{ Form::label('employee_id', __('Employee'),['class'=>'form-label']) }}
                {{ Form::select('employee_id', $employees,null, array('class' => 'form-control select2','required'=>'required')) }}
            </div>
        @endif
        <div class="form-group col-md-12">
            {{Form::label('date',__('Date'),['class'=>'form-label'])}}
            {{Form::date('date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('hours_spent',__('Hours Spent'),['class'=>'form-label'])}}
            {{Form::number('hours_spent',null,array('class'=>'form-control','required'=>'required', 'step'=>'0.01'))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('summary',__('Summary'),['class'=>'form-label'])}}
            {{Form::textarea('summary',null,array('class'=>'form-control','required'=>'required','rows'=>3))}}
        </div>
        <div class="form-group col-md-12">
            {{Form::label('challenges',__('Challenges'),['class'=>'form-label'])}}
            {{Form::textarea('challenges',null,array('class'=>'form-control','rows'=>3))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
