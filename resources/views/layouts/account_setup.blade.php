<div class="col-lg-3">
    <div class="card sticky-top" style="top:30px">
        <div class="list-group list-group-flush" id="useradd-sidenav">

            <a href="{{ route('accounting-clients.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'accounting-clients.index' || Request::segment(1) == 'accounting-clients' ) ? 'active' : '' }}">{{__('Client')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

            <a href="{{ route('suppliers.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'suppliers.index' || Request::segment(1) == 'suppliers' ) ? 'active' : '' }}">{{__('Supplier')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

            <a href="{{ route('consultants.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'consultants.index' || Request::segment(1) == 'consultants' ) ? 'active' : '' }}">{{__('Consultant')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        </div>
    </div>
</div>
