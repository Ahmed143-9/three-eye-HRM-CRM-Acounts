@php
    $users = \Auth::user();
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
    $languages = \App\Models\Utility::languages();

    $lang = isset($users->lang) ? $users->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
    $LangName = cache()->remember('full_language_data_' . $lang, now()->addHours(24), function () use ($lang) {
        return \App\Models\Language::languageData($lang);
    });

    $setting = \App\Models\Utility::settings();

    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count();
@endphp
@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg">
@else
    <header class="dash-header">
@endif
        <div class="header-wrapper">
            <div class="me-auto dash-mob-drp">
                <ul class="list-unstyled">
                    <li class="dash-h-item mob-hamburger">
                        <a href="#!" class="dash-head-link" id="mobile-collapse">
                            <div class="hamburger hamburger--arrowturn">
                                <div class="hamburger-box">
                                    <div class="hamburger-inner"></div>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li class="dropdown dash-h-item drp-company">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="theme-avtar">
                                <img src="{{ !empty(\Auth::user()->avatar) ? $profile . \Auth::user()->avatar : $profile . 'avatar.png'}}"
                                    class="img-fluid rounded border-2 border border-primary">
                            </span>
                            <span class="hide-mob ms-2">{{__('Hi, ')}}{{\Auth::user()->name }}!</span>
                            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown">

                            <a href="{{route('profile')}}" class="dropdown-item">
                                <i class="ti ti-user text-dark"></i><span>{{__('Profile')}}</span>
                            </a>

                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                                class="dropdown-item">
                                <i class="ti ti-power text-dark"></i><span>{{__('Logout')}}</span>
                            </a>

                            <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                                {{ csrf_field() }}
                            </form>

                        </div>
                    </li>

                </ul>
            </div>
            <div class="ms-auto">
                <ul class="list-unstyled">
                    @if(\Auth::user()->type == 'company')
                        @impersonating($guard = null)
                        <li class="dropdown dash-h-item drp-company">
                            <a class="btn btn-danger btn-sm" href="{{ route('exit.company') }}"><i class="ti ti-ban"></i>
                                {{ __('Exit Company Login') }}
                            </a>
                        </li>
                        @endImpersonating
                    @endif

                    {{-- @if( \Auth::user()->type !='client' && \Auth::user()->type !='super admin' )
                    <li class="dropdown dash-h-item drp-notification">
                        <a class="dash-head-link arrow-none me-0" href="{{ url('chats') }}" aria-haspopup="false"
                            aria-expanded="false">
                            <i class="ti ti-brand-hipchat"></i>
                            <span
                                class="bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep">
                                {{ $unseenCounter }}<span class="sr-only"></span>
                            </span>
                        </a>
                    </li>
                    @endif --}}

                    <li class="dropdown dash-h-item drp-notification">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false" id="notification-btn">
                            <i class="ti ti-bell"></i>
                            <span class="bg-danger dash-h-badge notification-count d-none" id="notification-badge">0</span>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification-dropdown-menu" style="min-width: 350px;">
                            <div class="noti-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="m-0">{{__('Notifications')}}</h6>
                                <a href="#" class="text-xs text-primary" onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">{{__('Mark all as read')}}</a>
                                <form id="mark-all-read-form" action="{{ route('notifications.markAllRead') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                            <div class="list-group list-group-flush notification-list-items" id="notification-list" style="max-height: 400px; overflow-y: auto;">
                                <!-- Notifications will be loaded here via AJAX -->
                                <div class="text-center p-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </div>
                            </div>
                            <div class="noti-footer p-2 text-center border-top">
                                <a href="{{ route('notifications.index') }}" class="text-primary text-sm">{{__('View All Notifications')}}</a>
                            </div>
                        </div>
                    </li>

                    <li class="dropdown dash-h-item drp-language">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-world nocolor"></i>
                            <span class="drp-text hide-mob">{{ucfirst($LangName->full_name)}}</span>
                            <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                            @foreach ($languages as $code => $language)
                                <a href="{{ route('change.language', $code) }}"
                                    class="dropdown-item {{ $lang == $code ? 'text-primary' : '' }}">
                                    <span>{{ucFirst($language)}}</span>
                                </a>
                            @endforeach

                            <h></h>
                            @if(\Auth::user()->type == 'super admin')
                                <a data-url="{{ route('create.language') }}" class="dropdown-item text-primary"
                                    data-ajax-popup="true" data-title="{{__('Create New Language')}}"
                                    style="cursor: pointer">
                                    {{ __('Create Language') }}
                                </a>
                                <a class="dropdown-item text-primary"
                                    href="{{route('manage.language', [isset($lang) ? $lang : 'english'])}}">{{ __('Manage Language') }}</a>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <script>
        $(document).ready(function() {
            var lastNotificationId = localStorage.getItem('last_notification_id') || 0;

            function fetchNotifications() {
                $.ajax({
                    url: '{{ route('notifications.latest') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#notification-list').html(response.html);
                        
                        // Badge count
                        if (response.unreadCount > 0) {
                            $('#notification-badge').text(response.unreadCount).removeClass('d-none');
                        } else {
                            $('#notification-badge').addClass('d-none');
                        }

                        // Popup Logic for new notifications
                        if (response.latestId > lastNotificationId) {
                            if (lastNotificationId != 0 && response.latestNotification) {
                                var type = response.latestNotification.type == 'expense_rejected' ? 'error' : 'success';
                                var title = response.latestNotification.title;
                                var message = response.latestNotification.message;
                                
                                // Show a more detailed toast if it's an expense
                                if(response.latestNotification.related_model == 'ErpExpense') {
                                    // Use the formatted HTML message if possible or just the basic text
                                    message = response.latestNotification.message;
                                }
                                
                                show_toastr(type, title + ': ' + message);
                            }
                            lastNotificationId = response.latestId;
                            localStorage.setItem('last_notification_id', lastNotificationId);
                        }
                    }
                });
            }

            // Fetch on load
            fetchNotifications();

            // Refresh every 30 seconds
            setInterval(fetchNotifications, 30000);

            // Fetch when dropdown is opened
            $('#notification-btn').on('click', function() {
                fetchNotifications();
            });
        });
    </script>