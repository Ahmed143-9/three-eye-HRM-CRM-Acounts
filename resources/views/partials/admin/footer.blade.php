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
                {{ date('Y') }} {{ $setting['footer_text'] ? $setting['footer_text'] : config('app.name', 'ERPGo') }}
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
    <!-- {{-- ===== Transport Bill Notification Modal ===== --}} -->
    @if($user->can('manage bill'))
        <div class="modal fade" id="transportBillModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-body p-0">
                        <div class="bg-primary text-white text-center py-4 px-3">
                            <div style="font-size:3rem;line-height:1">🚛</div>
                            <h5 class="fw-bold mt-2 mb-0">{{ __('New Transport Bill!') }}</h5>
                        </div>
                        <div class="px-4 pt-3 pb-2">
                            <p class="text-muted text-center mb-2">{{ __('A transport record is awaiting billing entry.') }}</p>
                            <div id="transport-notif-list"></div>
                        </div>
                        <div class="d-flex gap-2 px-4 pb-4 justify-content-center">
                            <a href="{{ route('transport.bill.index') }}"
                                class="btn btn-primary px-4">{{ __('Manage Bills') }}</a>
                            <button type="button" class="btn btn-outline-secondary bill-later-btn"
                                data-bs-dismiss="modal">{{ __('Later') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function () {
                var modalEl = document.getElementById('transportBillModal');
                var bsModal = null;
                var currentIds = [];

                function check() {
                    $.get('{{ route("transport.bill.check") }}', function (data) {
                        if (data && data.count > 0) {
                            var html = '';
                            currentIds = data.bills.map(function (b) { return b.id; });
                            data.bills.forEach(function (b) {
                                html += '<div class="border rounded p-2 mb-2 text-sm">';
                                html += '<div>Transport: <strong>' + b.transport + '</strong></div>';
                                html += '<div class="text-primary">Order: ' + b.order + '</div>';
                                html += '</div>';
                            });
                            $('#transport-notif-list').html(html);
                            if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
                            bsModal.show();
                        }
                    });
                }
                $(document).on('click', '.bill-later-btn', function () {
                    if (currentIds.length > 0) {
                        $.post('{{ route("transport.bill.mark-seen") }}', { ids: currentIds });
                    }
                });
                setTimeout(check, 3000);
            })();
        </script>
    @endif

    {{-- ===== Transport Request Notification Modal ===== --}}
    @if($user->can('manage employee'))
        <div class="modal fade" id="transportRequestModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-body p-0">
                        <div class="bg-info text-white text-center py-4 px-3">
                            <div style="font-size:3rem;line-height:1">📦</div>
                            <h5 class="fw-bold mt-2 mb-0">{{ __('New Transport Request!') }}</h5>
                        </div>
                        <div class="px-4 pt-3 pb-2">
                            <p class="text-muted text-center mb-2">
                                {{ __('New finalized sales orders are ready for transport.') }}</p>
                            <div id="transport-request-list"></div>
                        </div>
                        <div class="d-flex gap-2 px-4 pb-4 justify-content-center">
                            <a href="{{ route('transports.index') }}"
                                class="btn btn-info text-white px-4">{{ __('Manage Transports') }}</a>
                            <button type="button" class="btn btn-outline-secondary request-later-btn"
                                data-bs-dismiss="modal">{{ __('Later') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function () {
                var modalEl = document.getElementById('transportRequestModal');
                var bsModal = null;
                var currentReqIds = [];

                function checkRequests() {
                    $.get('{{ route("transport.request.check") }}', function (data) {
                        if (data && data.count > 0) {
                            var html = '';
                            currentReqIds = data.orders.map(function (o) { return o.id; });
                            data.orders.forEach(function (o) {
                                html += '<div class="border rounded p-2 mb-2 text-sm">';
                                html += '<div>Order: <strong>' + o.order_number + '</strong></div>';
                                html += '<div class="text-muted">Customer: ' + o.customer + '</div>';
                                html += '</div>';
                            });
                            $('#transport-request-list').html(html);
                            if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
                            bsModal.show();
                        }
                    });
                }
                $(document).on('click', '.request-later-btn', function () {
                    if (currentReqIds.length > 0) {
                        $.post('{{ route("transport.request.mark-seen") }}', { ids: currentReqIds });
                    }
                });
                setTimeout(checkRequests, 5000);
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