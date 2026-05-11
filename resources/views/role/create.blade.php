@php
    $userPlan = \App\Models\Plan::getPlan(\Auth::user()->show_dashboard());
    
    $groups = [
        'HRM Permissions' => [
            'Manage Employees',
            'Manage Attendance',
            'Manage Payroll',
            'Manage Leaves',
            'Manage Transport',
            'Manage Assets',
            'Manage HR Setup',
        ],
        'Accounting Permissions' => [
            'Manage Payables & Receivables',
            'Manage Banking & Billing',
            'Manage Sales Orders',
            'Manage Purchases & Suppliers',
            'Manage Accounting Setup',
            'Manage Petty Cash',
        ],
        'Expense Management Permissions' => [
            'Submit Expenses',
            'Approve Expenses',
        ],
        'Administration Permissions' => [
            'Manage Users',
            'Manage Roles',
            'Manage System Settings',
        ],
    ];
@endphp

{{Form::open(array('url'=>'roles','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{Form::label('name',__('Role Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name'),'required' => 'required'))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3">{{__('Assign Permissions')}}</h5>
                </div>
                
                @foreach($groups as $groupName => $groupPermissions)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-none border">
                            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3 bg-light">
                                <h6 class="mb-0">{{ __($groupName) }}</h6>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input check-all" data-group="{{ Str::slug($groupName) }}">
                                    <label class="form-check-label text-sm">{{ __('Select All') }}</label>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    @foreach($groupPermissions as $permissionName)
                                        @php
                                            $id = array_search($permissionName, $permissions);
                                        @endphp
                                        @if($id)
                                            <div class="col-12 mb-2">
                                                <div class="form-check">
                                                    {{Form::checkbox('permissions[]', $id, false, ['class'=>'form-check-input permission-item group-' . Str::slug($groupName), 'id' =>'permission_'.$id])}}
                                                    {{Form::label('permission_'.$id, __($permissionName), ['class'=>'form-check-label text-sm'])}}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>

{{Form::close()}}

<script>
    $(document).ready(function () {
        $(".check-all").click(function(){
            var group = $(this).data('group');
            $('.group-' + group).prop('checked', this.checked);
        });
        
        $('.permission-item').click(function(){
            var group = $(this).attr('class').split(' ').find(c => c.startsWith('group-'));
            var groupName = group.replace('group-', '');
            var allChecked = $('.group-' + groupName).length === $('.group-' + groupName + ':checked').length;
            $('.check-all[data-group="' + groupName + '"]').prop('checked', allChecked);
        });
    });
</script>
