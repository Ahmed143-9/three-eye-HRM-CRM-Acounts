<div class="card shadow-none">
    <div class="card-body p-0">
        @php
            $totalOrderQty = $order->po && $order->po->items ? $order->po->items->sum('quantity') : 0;
            $deliveredQty = $order->cis->flatMap->tankers->sum('quantity_mt');
            $remainingQty = $totalOrderQty - $deliveredQty;
            
            // If creating a new CI, don't pull from session
            $active_ci_id = request()->new_ci ? null : (request()->ci_id ?? session('active_ci_id'));
            $active_ci = $active_ci_id ? $order->cis->find($active_ci_id) : null;
        @endphp

        <!-- 1. Summary Matrix -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-white">{{ __('Partial Delivery Matrix (Container View)') }}</h6>
                        <span class="badge bg-light text-dark">{{ __('Total Order: ') }} {{ number_format($totalOrderQty, 3) }} MT</span>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row text-center">
                            <div class="col-md-4 border-end">
                                <p class="text-muted mb-1">{{ __('Total Shipped (Across all CIs)') }}</p>
                                <h4 class="text-success">{{ number_format($deliveredQty, 3) }} MT</h4>
                            </div>
                            <div class="col-md-4 border-end">
                                <p class="text-muted mb-1">{{ __('Remaining Balance') }}</p>
                                <h4 class="{{ $remainingQty > 0 ? 'text-warning' : ($remainingQty < 0 ? 'text-danger' : 'text-primary') }}">
                                    {{ number_format($remainingQty, 3) }} MT
                                </h4>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">{{ __('Completion Status') }}</p>
                                <div class="progress mt-2" style="height: 10px;">
                                    @php $percent = $totalOrderQty > 0 ? min(100, ($deliveredQty / $totalOrderQty) * 100) : 0; @endphp
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                                <small>{{ number_format($percent, 1) }}% {{ __('Delivered') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 2. CI List Sidebar -->
            <div class="col-md-3 border-end">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>{{ __('Shipment Batches') }}</h6>
                    <a href="{{ route('sales-orders.show', $order->id) }}?new_ci=1" class="btn btn-sm btn-primary py-1 px-2">
                        <i class="ti ti-plus"></i>
                    </a>
                </div>
                <div class="list-group list-group-flush border-top border-bottom mb-3" style="max-height: 400px; overflow-y: auto;">
                    @forelse($order->cis as $ci)
                        <a href="{{ route('sales-orders.show', $order->id) }}?ci_id={{ $ci->id }}" 
                           class="list-group-item list-group-item-action {{ $active_ci_id == $ci->id ? 'active' : '' }} d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-sm">{{ $ci->ci_number }}</div>
                                <small class="{{ $active_ci_id == $ci->id ? 'text-white-50' : 'text-muted' }}">{{ $ci->ci_date }}</small>
                            </div>
                            <span class="badge rounded-pill bg-{{ $ci->delivery ? 'success' : 'warning' }} px-2">
                                {{ number_format($ci->tankers->sum('quantity_mt'), 2) }}
                            </span>
                        </a>
                    @empty
                        <div class="p-3 text-center text-muted small">
                            {{ __('No shipments created yet.') }}
                        </div>
                    @endforelse
                </div>
                @if(request()->new_ci)
                    <div class="alert alert-info p-2 text-xs">
                        <i class="ti ti-info-circle"></i> {{ __('Creating new partial delivery flow...') }}
                    </div>
                @endif
            </div>

            <!-- 3. Active Shipment Flow -->
            <div class="col-md-9">
                @if($active_ci || request()->new_ci)
                    <div class="card border-0 shadow-none">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 mb-3 rounded">
                            <h6 class="mb-0">
                                @if(request()->new_ci)
                                    <i class="ti ti-plus text-primary"></i> {{ __('New Shipment Batch') }}
                                @else
                                    <i class="ti ti-package text-success"></i> {{ __('Shipment:') }} {{ $active_ci->ci_number }}
                                @endif
                            </h6>
                            @if($active_ci)
                                <div class="badge bg-{{ $active_ci->delivery ? 'success' : 'warning' }}">
                                    {{ $active_ci->delivery ? __('Ready for Transport') : __('Processing...') }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Sub-Steps Tabs for CI -->
                        <ul class="nav nav-tabs mb-3" id="ci-steps-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link {{ !session('jump_to_pl') && !session('jump_to_cn') && !session('jump_to_rd') && !session('jump_to_delivery') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#step-ci-form">{{ __('1. CI Details') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $active_ci ? '' : 'disabled' }} {{ session('jump_to_pl') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#step-pl-form">{{ __('2. Packing List') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $active_ci ? '' : 'disabled' }} {{ session('jump_to_cn') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#step-cn-form">{{ __('3. Consignment') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $active_ci ? '' : 'disabled' }} {{ session('jump_to_rd') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#step-rd-form">{{ __('4. Received') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $active_ci ? '' : 'disabled' }} {{ session('jump_to_delivery') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#step-delivery-form">{{ __('5. Delivery') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content border p-3 rounded">
                            <div class="tab-pane fade {{ !session('jump_to_pl') && !session('jump_to_cn') && !session('jump_to_rd') && !session('jump_to_delivery') ? 'show active' : '' }}" id="step-ci-form">
                                @include('sales_orders.shipments.steps.ci')
                            </div>
                            <div class="tab-pane fade {{ session('jump_to_pl') ? 'show active' : '' }}" id="step-pl-form">
                                @include('sales_orders.shipments.steps.pl')
                            </div>
                            <div class="tab-pane fade {{ session('jump_to_cn') ? 'show active' : '' }}" id="step-cn-form">
                                @include('sales_orders.shipments.steps.cn')
                            </div>
                            <div class="tab-pane fade {{ session('jump_to_rd') ? 'show active' : '' }}" id="step-rd-form">
                                @include('sales_orders.shipments.steps.received_details')
                            </div>
                            <div class="tab-pane fade {{ session('jump_to_delivery') ? 'show active' : '' }}" id="step-delivery-form">
                                @include('sales_orders.shipments.steps.delivery')
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center p-5 bg-light rounded border border-dashed">
                        <i class="ti ti-package fs-1 text-muted opacity-25"></i>
                        <h5 class="mt-3 text-muted">{{ __('Select a shipment or create a new one') }}</h5>
                        <p class="text-sm text-muted">{{ __('Use the sidebar to manage partial deliveries for this container order.') }}</p>
                        <a href="{{ route('sales-orders.show', $order->id) }}?new_ci=1" class="btn btn-primary mt-2">
                            <i class="ti ti-plus me-1"></i> {{ __('New Partial Delivery') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
