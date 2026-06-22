@php
    use App\Models\Utility;
    $setting = \App\Models\Utility::settings();
    $setting_arr = Utility::file_validate();
    $user = \Auth::user();
@endphp
<!-- [ Main Content ] end -->
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <p class="mb-0 text-muted"> &copy;
                {{ date('Y') }} {{ $setting['footer_text'] ? $setting['footer_text'] : 'ThreeEye' }}
            </p>
        </div>
    </div>
</footer>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/dash.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/jscolor.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>

<script>
    var file_size = "{{ $setting_arr['max_size'] }}";
    var file_types = "{{ $setting_arr['types'] }}";
    var type_err = "{{ __('Invalid file type. Please select a valid file (' . $setting_arr['types'] . ').') }}";
    var size_err = "{{ __('File size exceeds the maximum limit of ' . $setting_arr['max_size'] / 1024 . 'MB.') }}";

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
</script>
<script>
    var site_currency_symbol_position = '{{ $setting['site_currency_symbol_position'] }}';
    var site_currency_symbol = '{{ $setting['site_currency_symbol'] }}';
</script>
<script src="{{ asset('js/custom.js') }}"></script>

@if($message = Session::get('success'))
    <script>show_toastr('success', '{!! $message !!}');</script>
@endif
@if($message = Session::get('error'))
    <script>show_toastr('error', '{!! $message !!}');</script>
@endif
@if($setting['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif

@if(\Auth::check() && $user->type != 'admin' && $user->type != 'company')
    <!-- {{-- ===== Transport Bill Notification Logic ===== --}} -->
    @if($user->can('Manage Purchases & Suppliers'))
        <script>
            (function () {
                var currentIds = [];
                var shownBills = JSON.parse(localStorage.getItem('shown_transport_bills') || '[]');

                function check() {
                    $.get('{{ route("transport.bill.check") }}', function (data) {
                        if (data && data.count > 0) {
                            currentIds = data.bills.map(function (b) { return b.id; });
                            
                            var hasNew = false;
                            data.bills.forEach(function (b) {
                                if (!shownBills.includes(b.id)) {
                                    hasNew = true;
                                    shownBills.push(b.id);
                                    
                                    var title = '{{ __("New Transport Bill!") }}';
                                    var message = '{{ __("Transport") }}: ' + b.transport + '<br>{{ __("Order") }}: ' + b.order;
                                    var redirectUrl = '{{ route("transport.bill.index") }}';
                                    
                                    var toastHtml = '<b>' + title + '</b><br>' + message + '<br>' + 
                                                   '<a href="' + redirectUrl + '" class="btn btn-sm btn-primary mt-2 text-white toast-action-btn">{{ __("Manage Bills") }}</a>';
                                    
                                    show_toastr('info', toastHtml);
                                }
                            });
                            
                            if (hasNew) {
                                if(shownBills.length > 50) shownBills = shownBills.slice(-50);
                                localStorage.setItem('shown_transport_bills', JSON.stringify(shownBills));
                                
                                // Increment Bell Icon
                                var badge = $('#notification-badge');
                                var currentCount = parseInt(badge.text()) || 0;
                                badge.text(currentCount + data.count).removeClass('d-none');
                            }
                        }
                    });
                }
                
                // We'll mark them seen if they visit the page, but for now we'll just check periodically
                setTimeout(function loop() {
                    check();
                    setTimeout(loop, 15000); // Poll every 15s instead of 3s to be less intensive
                }, 3000);
            })();
        </script>
    @endif

    {{-- ===== Transport Request Notification Logic ===== --}}
    @if($user->can('Manage Employees'))
        <script>
            (function () {
                var currentReqIds = [];
                var shownReqs = JSON.parse(localStorage.getItem('shown_transport_requests') || '[]');

                function checkRequests() {
                    $.get('{{ route("transport.request.check") }}', function (data) {
                        if (data && data.count > 0) {
                            currentReqIds = data.orders.map(function (o) { return o.id; });
                            
                            var hasNew = false;
                            data.orders.forEach(function (o) {
                                if (!shownReqs.includes(o.id)) {
                                    hasNew = true;
                                    shownReqs.push(o.id);
                                    
                                    var title = '{{ __("New Transport Request!") }}';
                                    var message = '{{ __("Order") }}: ' + o.order_number + '<br>{{ __("Customer") }}: ' + o.customer;
                                    var redirectUrl = '{{ route("transports.index") }}';
                                    
                                    var toastHtml = '<b>' + title + '</b><br>' + message + '<br>' + 
                                                   '<a href="' + redirectUrl + '" class="btn btn-sm btn-info mt-2 text-white toast-action-btn">{{ __("Manage Transports") }}</a>';
                                    
                                    show_toastr('info', toastHtml);
                                }
                            });
                            
                            if (hasNew) {
                                if(shownReqs.length > 50) shownReqs = shownReqs.slice(-50);
                                localStorage.setItem('shown_transport_requests', JSON.stringify(shownReqs));
                                
                                // Increment Bell Icon
                                var badge = $('#notification-badge');
                                var currentCount = parseInt(badge.text()) || 0;
                                badge.text(currentCount + data.count).removeClass('d-none');
                            }
                        }
                    });
                }
                
                setTimeout(function loop() {
                    checkRequests();
                    setTimeout(loop, 15000);
                }, 5000);
            })();
        </script>
    @endif
@endif

@stack('script-page')
@stack('old-datatable-js')

<script>
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function () {
            document.querySelector(".pct-customizer").classList.toggle("active");
        });
    }
    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) { node.classList.remove(value); }
        }
    }
</script>

<script>
    /* ── Sidebar Toggle Logic ─────────────────────────────────────── */
    (function () {
        const body = document.body;
        const btn = document.getElementById('sidebar-toggle-btn');
        const icon = document.getElementById('sidebar-toggle-icon');
        const COLLAPSED = 'sidebar-collapsed';
        const KEY = 'sidebarCollapsed';

        if (localStorage.getItem(KEY) === 'true') {
            body.classList.add(COLLAPSED);
            if (icon) icon.classList.replace('ti-chevron-left', 'ti-chevron-right');
        }

        if (btn) {
            btn.style.setProperty('position', 'relative', 'important');
            btn.addEventListener('click', function () {
                const isCollapsed = body.classList.toggle(COLLAPSED);
                localStorage.setItem(KEY, isCollapsed);
                if (icon) {
                    if (isCollapsed) { icon.classList.replace('ti-chevron-left', 'ti-chevron-right'); }
                    else { icon.classList.replace('ti-chevron-right', 'ti-chevron-left'); }
                }
            });
        }
    })();
</script>