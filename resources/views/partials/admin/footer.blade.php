@php
    use App\Models\Utility;
    $setting = \App\Models\Utility::settings();
    $setting_arr = Utility::file_validate();
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


<!-- Warning Section Ends -->
<!-- Required Js -->

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

<!-- Apex Chart -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>

<script src="{{ asset('js/jscolor.js') }}"></script>

<script src="{{ asset('js/popper.min.js') }}"></script>


<script>
    var file_size = "{{ $setting_arr['max_size'] }}";
    var file_types = "{{ $setting_arr['types'] }}";
    var type_err = "{{ __('Invalid file type. Please select a valid file ('.$setting_arr['types'].').') }}";
    var size_err = "{{ __('File size exceeds the maximum limit of '. $setting_arr['max_size'] / 1024 .'MB.') }}";
</script>
<script>
    var site_currency_symbol_position = '{{ $setting['site_currency_symbol_position'] }}';
    var site_currency_symbol = '{{ $setting['site_currency_symbol'] }}';

</script>
<script src="{{ asset('js/custom.js') }}"></script>

@if($message = Session::get('success'))
    <script>
        show_toastr('success', '{!! $message !!}');
    </script>
@endif
@if($message = Session::get('error'))
    <script>
        show_toastr('error', '{!! $message !!}');
    </script>
@endif
@if($setting['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
@if(\Auth::check() && \Auth::user()->can('manage bill'))
{{-- ===== Transport Bill Notification Modal ===== --}}
<div class="modal fade" id="transportBillModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-body p-0">
        {{-- Colored header band --}}
        <div class="bg-primary text-white text-center py-4 px-3">
          <div style="font-size:3rem;line-height:1">🚛</div>
          <h5 class="fw-bold mt-2 mb-0">{{ __('New Transport Bill!') }}</h5>
        </div>
        {{-- Bill info filled dynamically --}}
        <div class="px-4 pt-3 pb-2" id="transport-notif-body">
          <p class="text-muted text-center mb-2">{{ __('A transport record is awaiting billing entry.') }}</p>
          <div id="transport-notif-list"></div>
        </div>
        <div class="d-flex gap-2 px-4 pb-4 justify-content-center">
          <a id="notif-pay-btn" href="{{ route('transport.bill.index') }}" class="btn btn-primary px-4">
            <i class="ti ti-credit-card me-1"></i>{{ __('Pay Bill') }}
          </a>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            {{ __('Later') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
    var modalEl       = document.getElementById('transportBillModal');
    var notifList     = document.getElementById('transport-notif-list');
    var bsModal       = null;
    var seenThisSession = [];   // IDs seen in this browser session (avoid flash re-showing)

    function check() {
        $.ajax({
            url: '{{ route("transport.bill.check") }}',
            method: 'GET',
            success: function(data) {
                if (!data || data.count === 0) return;

                // Filter out bills already seen this session
                var fresh = (data.bills || []).filter(function(b) {
                    return seenThisSession.indexOf(b.id) === -1;
                });
                if (fresh.length === 0) return;

                // Build bill list HTML
                var html = '';
                fresh.forEach(function(b) {
                    html += '<div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">';
                    html += '  <div>';
                    html += '    <div class="fw-semibold small">' + b.transport + '</div>';
                    html += '    <div class="text-muted" style="font-size:0.8rem">Client: ' + b.client + ' &nbsp;|&nbsp; Truck: ' + b.truck + '</div>';
                    html += '  </div>';
                    html += '  <a href="{{ route("transport.bill.pay", "") }}/' + b.id + '"';
                    html += '     class="btn btn-sm btn-outline-primary notif-pay-single" data-id="' + b.id + '">';
                    html += '    <i class="ti ti-credit-card"></i>';
                    html += '  </a>';
                    html += '</div>';
                });
                notifList.innerHTML = html;

                if (!bsModal) {
                    bsModal = new bootstrap.Modal(modalEl);
                }
                bsModal.show();
            },
            error: function() {
                // Silently fail — don't break the app
            }
        });
    }

    // Mark bill as seen and close modal
    $(document).on('click', '.notif-pay-single', function(e) {
        var id   = $(this).data('id');
        var href = $(this).attr('href');
        seenThisSession.push(id);
        if (bsModal) bsModal.hide();
        setTimeout(function() { window.location.href = href; }, 200);
        e.preventDefault();
    });

    // "Pay Bill" main button — marks first unseen as seen
    $('#notif-pay-btn').on('click', function() {
        var firstId = $('.notif-pay-single').first().data('id');
        if (firstId) {
            seenThisSession.push(firstId);
        }
    });

    // Poll: first check after 3 seconds, then every 2 minutes
    setTimeout(function() {
        check();
        setInterval(check, 120000);
    }, 3000);
})();
</script>
@endif

@stack('script-page')



@stack('old-datatable-js')



<script>




    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function () {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function (event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    if ($('#cust-theme-bg').length > 0) {
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function () {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });
    }




    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>


<script>
/* ── Sidebar Toggle Logic ─────────────────────────────────────── */
(function () {
    const body      = document.body;
    const btn       = document.getElementById('sidebar-toggle-btn');
    const icon      = document.getElementById('sidebar-toggle-icon');
    const COLLAPSED = 'sidebar-collapsed';
    const KEY       = 'sidebarCollapsed';

    if (localStorage.getItem(KEY) === 'true') {
        body.classList.add(COLLAPSED);
        if (icon) icon.classList.replace('ti-chevron-left', 'ti-chevron-right');
    }

    // Force the button to stay in document flow — beats any CSS !important
    if (btn) {
        btn.style.setProperty('position', 'relative', 'important');
        btn.style.setProperty('right', 'auto', 'important');
        btn.style.setProperty('top', 'auto', 'important');
        btn.style.setProperty('left', 'auto', 'important');
        btn.addEventListener('click', function () {
            const isCollapsed = body.classList.toggle(COLLAPSED);
            localStorage.setItem(KEY, isCollapsed);
            if (icon) {
                if (isCollapsed) {
                    icon.classList.replace('ti-chevron-left', 'ti-chevron-right');
                } else {
                    icon.classList.replace('ti-chevron-right', 'ti-chevron-left');
                }
            }
        });
    }
})();
</script>
