{{Form::open(array('url'=>'vender','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">

    <h5 class="sub-title mb-3">{{__('Basic Info')}}</h5>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('name',__('Name'),array('class'=>'form-label')) }}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','required'=>'required' , 'placeholder'=>__('Enter Name')))}}

            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                <x-mobile label="{{__('Contact')}}" name="contact" value="{{old('contact')}}" required placeholder="Enter Contact"></x-mobile>

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('contact_person',__('Contact Person'),array('class'=>'form-label')) }}
                {{Form::text('contact_person',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Contact Person')))}}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                <x-mobile label="{{__('Contact Person Number')}}" name="contact_person_number" value="{{old('contact_person_number')}}" required placeholder="Enter Contact Person Number"></x-mobile>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{Form::label('billing_name',__('Billing Name'),array('class'=>'form-label')) }}
                {{Form::text('billing_name',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Billing Name')))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('billing_address',__('Address'),array('class'=>'form-label')) }}
                {{Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>3 , 'placeholder' => __('Enter Address')))}}
            </div>
        </div>
    </div>
    
    @if(!$customFields->isEmpty())
        @include('customFields.formBuilder')
    @endif
</div>

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}
