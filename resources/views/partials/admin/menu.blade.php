@php
    use App\Models\Utility;
    $setting = \App\Models\Utility::settings();
    $logo = \App\Models\Utility::get_file('uploads/logo');

    $company_logo = $setting['company_logo_dark'] ?? '';
    $company_logos = $setting['company_logo_light'] ?? '';
    $company_small_logo = $setting['company_small_logo'] ?? '';

    $emailTemplate = \App\Models\EmailTemplate::emailTemplateData();
    $lang = Auth::user()->lang ?? 'en';

    // Safe plan retrieval with null check
    $planId = \Auth::user()->show_dashboard();
    $userPlan = $planId ? \App\Models\Plan::getPlan($planId) : null;

    // PERFORMANCE: Cache all user permissions once instead of 227 DB queries
    $userPermissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();
    $hasPermission = function($perm) use ($userPermissions) {
        return in_array($perm, $userPermissions);
    };
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="dash-sidebar light-sidebar transprent-bg">
    @else
        <nav class="dash-sidebar light-sidebar ">
@endif
<div class="navbar-wrapper">
    <div class="m-header main-logo" style="display:flex; align-items:center; justify-content:space-between; padding-right:8px;">
        <a href="#" class="b-brand">
            @if ($setting['cust_darklayout'] && $setting['cust_darklayout'] == 'on')
                <img src="{{ $logo . '/' . (isset($company_logos) && !empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time() }}"
                    alt="{{ config('app.name', 'ERPGo-SaaS') }}" class="logo logo-lg">
            @else
                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-light.png') . '?' . time() }}"
                    alt="{{ config('app.name', 'ERPGo-SaaS') }}" class="logo logo-lg">
            @endif
        </a>
        {{-- Toggle button — inline right side of logo --}}
        <button id="sidebar-toggle-btn" class="sidebar-toggle-btn" title="Toggle Sidebar">
            <i class="ti ti-chevron-left" id="sidebar-toggle-icon"></i>
        </button>
    </div>
    <div class="navbar-content">
        @if (\Auth::user()->type != 'client')
            <ul class="dash-navbar">
                <!--------------------- Start Dashboard ----------------------------------->
                <li class="dash-item {{ Request::segment(1) == 'account-dashboard' || Request::segment(1) == null ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-home"></i></span>
                        <span class="dash-mtext">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <!--------------------- End Dashboard ----------------------------------->


                <!--------------------- End Dashboard ----------------------------------->


                <!--------------------- Start HRM ----------------------------------->

                @if (!empty($userPlan) && $userPlan->hrm == 1 && \Auth::user()->type != 'accountant')
                    @if (Gate::check('manage employee') ||
                        Gate::check('manage set salary') || Gate::check('manage pay slip') ||
                        Gate::check('manage leave') || Gate::check('manage attendance') ||
                        Gate::check('create attendance') || Gate::check('manage indicator') ||
                        Gate::check('manage appraisal') || Gate::check('manage goal tracking') ||
                        Gate::check('manage training') || Gate::check('manage trainer') ||
                        Gate::check('manage job') || Gate::check('create job') ||
                        Gate::check('manage job application') || Gate::check('manage custom question') ||
                        Gate::check('manage job onBoard') || Gate::check('show interview schedule') ||
                        Gate::check('show career') || Gate::check('manage award') ||
                        Gate::check('manage transfer') || Gate::check('manage resignation') ||
                        Gate::check('manage travel') || Gate::check('manage promotion') ||
                        Gate::check('manage complaint') || Gate::check('manage warning') ||
                        Gate::check('manage termination') || Gate::check('manage announcement') ||
                        Gate::check('manage holiday') || Gate::check('manage event') ||
                        Gate::check('manage meeting') || Gate::check('manage assets') ||
                        Gate::check('manage document') || Gate::check('manage company policy') ||
                        Gate::check('manage branch') || Gate::check('manage department') ||
                        Gate::check('manage designation') || Gate::check('manage leave type') ||
                        Gate::check('manage document type') || Gate::check('manage payslip type') ||
                        Gate::check('manage allowance option') || Gate::check('manage loan option') ||
                        Gate::check('manage deduction option') || Gate::check('manage goal type') ||
                        Gate::check('manage training type') || Gate::check('manage award type') ||
                        Gate::check('manage termination type') || Gate::check('manage job category') ||
                        Gate::check('manage job stage') || Gate::check('manage performance type') ||
                        Gate::check('manage competencies'))

                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'holiday-calender' ||
                            Request::segment(1) == 'leavetype' || Request::segment(1) == 'leave' ||
                            Request::segment(1) == 'attendanceemployee' || Request::segment(1) == 'bulkattendance' ||
                            Request::segment(1) == 'indicator' || Request::segment(1) == 'appraisal' ||
                            Request::segment(1) == 'goaltracking' || Request::segment(1) == 'trainer' ||
                            Request::segment(1) == 'account-assets' || Request::segment(1) == 'leavetype' ||
                            Request::segment(1) == 'document-upload' ||
                            Request::segment(1) == 'document' || Request::segment(1) == 'performanceType' ||
                            Request::segment(1) == 'branch' || Request::segment(1) == 'department' ||
                            Request::segment(1) == 'designation' || Request::segment(1) == 'employee' ||
                            Request::segment(1) == 'leave_requests' || Request::segment(1) == 'holidays' ||
                            Request::segment(1) == 'policies' || Request::segment(1) == 'leave_calender' ||
                            Request::segment(1) == 'award' || Request::segment(1) == 'transfer' ||
                            Request::segment(1) == 'resignation' || Request::segment(1) == 'training' ||
                            Request::segment(1) == 'travel' || Request::segment(1) == 'promotion' ||
                            Request::segment(1) == 'complaint' || Request::segment(1) == 'warning' ||
                            Request::segment(1) == 'termination' || Request::segment(1) == 'announcement' ||
                            Request::segment(1) == 'job' || Request::segment(1) == 'job-application' ||
                            Request::segment(1) == 'candidates-job-applications' || Request::segment(1) == 'job-onboard' ||
                            // Request::segment(1) == 'custom-question' || Request::segment(1) == 'interview-schedule' ||
                            Request::segment(1) == 'career' || Request::segment(1) == 'holiday' ||
                            Request::segment(1) == 'setsalary' || Request::segment(1) == 'payslip' ||
                            Request::segment(1) == 'paysliptype' || Request::segment(1) == 'company-policy' ||
                            Request::segment(1) == 'job-stage' || Request::segment(1) == 'job-category' ||
                            Request::segment(1) == 'terminationtype' || Request::segment(1) == 'awardtype' ||
                            Request::segment(1) == 'trainingtype' || Request::segment(1) == 'goaltype' ||
                            Request::segment(1) == 'allowanceoption' || Request::segment(1) == 'competencies' ||
                            Request::segment(1) == 'loanoption' || Request::segment(1) == 'deductionoption'
                                ? 'active'
                                : '' }}">
                            <a href="#!" class="dash-link ">
                                <span class="dash-micon">
                                    <i class="ti ti-user"></i>
                                </span>
                                <span class="dash-mtext">
                                    {{ __('HRM System') }}
                                </span>
                                <span class="dash-arrow">
                                    <i data-feather="chevron-right"></i>
                                </span>
                            </a>
                            <ul class="dash-submenu">
                                @can('manage employee')
                                    <li
                                        class="dash-item  {{ Request::segment(1) == 'employee' ? 'active dash-trigger' : '' }}   ">
                                        @if (\Auth::user()->type == 'Employee')
                                            @php
                                                $employee = App\Models\Employee::where(
                                                    'user_id',
                                                    \Auth::user()->id,
                                                )->first();
                                            @endphp
                                            <a class="dash-link"
                                                href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ __('Employee') }}</a>
                                        @else
                                            <a href="{{ route('employee.index') }}" class="dash-link">
                                                {{ __('Employee Setup') }}
                                            </a>
                                        @endif
                                    </li>
                                @endcan



                                @if (Gate::check('manage leave') || Gate::check('manage attendance'))
                                    <li
                                        class="dash-item dash-hasmenu  {{ Request::segment(1) == 'leave' || Request::segment(1) == 'attendanceemployee' ? 'active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('Leave Management Setup') }}<span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            @can('manage leave')
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'leave.index' ? 'active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('leave.index') }}">{{ __('Manage Leave') }}</a>
                                                </li>
                                            @endcan
                                            @can('manage attendance')
                                                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'attendanceemployee' ? 'active dash-trigger' : '' }}"
                                                    href="#navbar-attendance" data-toggle="collapse" role="button"
                                                    aria-expanded="{{ Request::segment(1) == 'attendanceemployee' ? 'true' : 'false' }}">
                                                    <a class="dash-link" href="#">{{ __('Attendance') }}<span
                                                            class="dash-arrow"><i
                                                                data-feather="chevron-right"></i></span></a>
                                                    <ul class="dash-submenu">
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'attendanceemployee.index' ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('attendanceemployee.index') }}">{{ __('Mark Attendance') }}</a>
                                                        </li>
                                                        @can('create attendance')
                                                            <li
                                                                class="dash-item {{ Request::route()->getName() == 'attendanceemployee.bulkattendance' ? 'active' : '' }}">
                                                                <a class="dash-link"
                                                                    href="{{ route('attendanceemployee.bulkattendance') }}">{{ __('Bulk Attendance') }}</a>
                                                            </li>
                                                        @endcan
                                                        @can('manage attendance')
                                                            <li
                                                                class="dash-item {{ Request::route()->getName() == 'attendance.late.log' ? 'active' : '' }}">
                                                                <a class="dash-link"
                                                                    href="{{ route('attendance.late.log') }}">{{ __('Late Updates') }}</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endif





                                <li class="dash-item {{ Request::segment(1) == 'transports' ? 'active' : '' }}">
                                    <a href="{{ route('transports.index') }}" class="dash-link">
                                        <span class="dash-micon"><i class="ti ti-truck"></i></span>
                                        <span class="dash-mtext">{{ __('Transport Management') }}</span>
                                    </a>
                                </li>






                                @if (\Auth::user()->type == 'HR' || \Auth::user()->type == 'hr')
                                    <li class="dash-item {{ request()->is('employee-assets*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('employee-assets.index') }}">{{ __('Employee Asset Setup') }}</a>
                                    </li>
                                    <li class="dash-item {{ request()->is('company-assets*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('company-assets.index') }}">{{ __('Company Asset Setup') }}</a>
                                    </li>

                                    <li
                                        class="dash-item {{ Request::segment(1) == 'leavetype' ||
                                        Request::segment(1) == 'document' ||
                                        Request::segment(1) == 'performanceType' ||
                                        Request::segment(1) == 'branch' ||
                                        Request::segment(1) == 'department' ||
                                        Request::segment(1) == 'designation' ||
                                        Request::segment(1) == 'job-stage' ||
                                        Request::segment(1) == 'competencies' ||
                                        Request::segment(1) == 'job-category' ||
                                        Request::segment(1) == 'terminationtype' ||
                                        Request::segment(1) == 'awardtype' ||
                                        Request::segment(1) == 'trainingtype' ||
                                        Request::segment(1) == 'goaltype' ||
                                        Request::segment(1) == 'paysliptype' ||
                                        Request::segment(1) == 'allowanceoption' ||
                                        Request::segment(1) == 'loanoption' ||
                                        Request::segment(1) == 'deductionoption'
                                            ? 'active dash-trigger'
                                            : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('branch.index') }}">{{ __('HRM System Setup') }}</a>
                                    </li>
                                @endif


                            </ul>
                        </li>
                    @endif
                @endif

                <!--------------------- End HRM ----------------------------------->

                <!--------------------- Start Account ----------------------------------->

                @if (!empty($userPlan) && $userPlan->account == 1)
                    @if (Gate::check('manage budget plan') || Gate::check('income vs expense report') ||
                            Gate::check('manage goal') || Gate::check('manage constant tax') ||
                            Gate::check('manage constant category') || Gate::check('manage constant unit') ||
                            Gate::check('manage constant custom field') ||
                            Gate::check('manage customer') || Gate::check('manage vender') ||
                            Gate::check('manage proposal') || Gate::check('manage bank account') ||
                            Gate::check('manage bank transfer') || Gate::check('manage invoice') ||
                            Gate::check('manage revenue') || Gate::check('manage credit note') ||
                            Gate::check('manage bill') || Gate::check('manage payment') ||
                            Gate::check('manage debit note') || Gate::check('manage chart of account') ||
                            Gate::check('manage journal entry') || Gate::check('balance sheet report') ||
                            Gate::check('ledger report') || Gate::check('trial balance report') )
                        <li
                            class="dash-item dash-hasmenu

                                        Request::segment(1) == 'customer' || Request::segment(1) == 'vender' ||
                                        Request::segment(1) == 'proposal' || Request::segment(1) == 'bank-account' ||
                                        Request::segment(1) == 'bank-transfer' || Request::segment(1) == 'invoice' ||
                                        Request::segment(1) == 'revenue' || Request::segment(1) == 'credit-note' ||
                                        Request::segment(1) == 'taxes' || Request::segment(1) == 'product-category' ||
                                        Request::segment(1) == 'product-unit' || Request::segment(1) == 'payment-method' ||
                                        Request::segment(1) == 'custom-field' || Request::segment(1) == 'chart-of-account-type' ||
                                        (Request::segment(1) == 'transaction' && Request::segment(2) != 'ledger' &&
                                            Request::segment(2) != 'balance-sheet-report' && Request::segment(2) != 'trial-balance') ||
                                        Request::segment(1) == 'goal' || Request::segment(1) == 'budget' ||
                                        Request::segment(1) == 'chart-of-account' || Request::segment(1) == 'journal-entry' ||
                                        Request::segment(2) == 'ledger' || Request::segment(2) == 'balance-sheet' ||
                                        Request::segment(2) == 'trial-balance' || Request::segment(2) == 'profit-loss' ||
                                        Request::segment(1) == 'bill' || Request::segment(1) == 'expense' || Request::segment(1) == 'billing' ||
                                        Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note' || (Request::route()->getName() == 'report.balance.sheet') || (Request::route()->getName() == 'trial-balance-report') ? ' active'
                                            : '' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-box"></i></span><span
                                    class="dash-mtext">{{ __('Accounting System ') }}
                                </span><span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">

                                <li
                                    class="dash-item {{ Request::segment(1) == 'payables' ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('payables.index') }}">
                                        <span class="dash-mtext">{{ __('Payable') }}</span>
                                    </a>
                                </li>
                                <li
                                    class="dash-item {{ Request::segment(1) == 'receivables' ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('receivables.index') }}">
                                        <span class="dash-mtext">{{ __('Receivable') }}</span>
                                    </a>
                                </li>

                                @if (Gate::check('manage bank account') || Gate::check('manage bank transfer'))
                                    <li
                                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer' || Request::segment(1) == 'billing' || Request::segment(1) == 'transport-bills' ? 'active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('Banking') }}<span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            <li
                                                class="dash-item {{ Request::route()->getName() == 'billing.index' || Request::route()->getName() == 'billing.create' || Request::route()->getName() == 'billing.edit' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('billing.index') }}">{{ __('Billing') }}</a>
                                            </li>
                                            <li
                                                class="dash-item {{ Request::route()->getName() == 'transport.bill.index' || Request::segment(1) == 'transport-bills' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('transport.bill.index') }}">{{ __('Transport Bill') }}</a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if (Gate::check('manage customer') || Gate::check('manage proposal') || Gate::check('manage invoice'))
                                    <li class="dash-item {{ Request::segment(1) == 'sales-orders' ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('sales-orders.index') }}">
                                            <span class="dash-mtext">{{ __('Sales Orders') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Gate::check('manage vender') ||
                                        Gate::check('manage bill') ||
                                        Gate::check('manage payment') ||
                                        Gate::check('manage debit note'))
                                    <li
                                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'bill' || Request::segment(1) == 'vender' || Request::segment(1) == 'expense' || Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note' ? 'active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('Purchases') }}<span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            @if (Gate::check('manage vender'))
                                                <li
                                                    class="dash-item {{ Request::segment(1) == 'vender' ? 'active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('vender.index') }}">{{ __('Suppiler') }}</a>
                                                </li>
                                            @endif
                                            @can('manage bill')
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                    href="{{ route('bill.index') }}">{{ __('Bill') }}</a>
                                                </li>
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'expense.index' || Request::route()->getName() == 'expense.create' || Request::route()->getName() == 'expense.edit' || Request::route()->getName() == 'expense.show' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('expense.index') }}">{{ __('Expense') }}</a>
                                                </li>
                                            @endcan
                                            @can('manage payment')
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'payment.index' || Request::route()->getName() == 'payment.create' || Request::route()->getName() == 'payment.edit' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('payment.index') }}">{{ __('Payment') }}</a>
                                                </li>
                                            @endcan
                                            @can('manage debit note')
                                                <li
                                                    class="dash-item  {{ Request::route()->getName() == 'debit.note' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('custom-debit.note') }}">{{ __('Debit Note') }}</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endif


                                @if (\Auth::user()->type != 'HR' && \Auth::user()->type != 'hr' && (
                                    Gate::check('manage accounting client') ||
                                        Gate::check('manage accounting supplier') ||
                                        Gate::check('manage accounting consultant') ||
                                        Gate::check('manage bank account') ||
                                        \Auth::user()->type == 'company'))
                                    <li
                                        class="dash-item {{ Request::segment(1) == 'accounting-clients' || Request::segment(1) == 'suppliers' || Request::segment(1) == 'consultants' ? 'active dash-trigger' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('accounting-clients.index') }}">{{ __('Accounting Setup') }}</a>
                                    </li>
                                @endif



                            </ul>
                        </li>
                    @endif
                @endif

                <!--------------------- End Account ----------------------------------->

                <!--------------------- Start CRM ----------------------------------->
                {{-- CRM System Removed --}}
                <!--------------------- End CRM ----------------------------------->

                <!--------------------- Start Project [DISABLED] ----------------------------------->
                <!--------------------- End Project [DISABLED] ----------------------------------->



                <!--------------------- Start User Managaement System ----------------------------------->

                @if (\Auth::user()->type != 'super admin' && (Gate::check('manage user') || Gate::check('manage role')))
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'users' ||
                        Request::segment(1) == 'roles' ||

                        Request::segment(1) == 'userlogs'
                            ? ' active dash-trigger'
                            : '' }}">

                        <a href="#!" class="dash-link "><span class="dash-micon"><i
                                    class="ti ti-users"></i></span><span
                                class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage user')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'user.userlog' ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('users.index') }}">{{ __('User') }}</a>
                                </li>
                            @endcan
                            @can('manage role')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit' ? ' active' : '' }} ">
                                    <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Role') }}</a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endif

                <!--------------------- End User Managaement System----------------------------------->


                <!--------------------- Start Products System ----------------------------------->
                {{-- Products System Removed --}}
                <!--------------------- End Products System [DISABLED] ----------------------------------->

                @if (\Auth::user()->type != 'super admin')
                    {{-- Support System Removed --}}
                    {{-- <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'zoom-meeting' || Request::segment(1) == 'zoom-meeting-calender' ? 'active' : '' }}">
                        <a href="{{ route('zoom-meeting.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-user-check"></i></span><span
                                class="dash-mtext">{{ __('Zoom Meeting') }}</span>
                        </a>
                    </li> --}}
                    {{-- <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'chats' ? 'active' : '' }}">
                        <a href="{{ url('chats') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-message-circle"></i></span><span
                                class="dash-mtext">{{ __('Messenger') }}</span>
                        </a>
                    </li> --}}
                @endif

                {{-- Notification Template Disabled
                @if (\Auth::user()->type == 'company')
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'notification_templates' ? 'active' : '' }}">
                        <a href="{{ route('notification-templates.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-notification"></i></span><span
                                class="dash-mtext">{{ __('Notification Template') }}</span>
                        </a>
                    </li>
                @endif
                --}}

                <!--------------------- Start System Setup ----------------------------------->

                @if (\Auth::user()->type != 'super admin')
                    @if (Gate::check('manage company plan') || Gate::check('manage order') || Gate::check('manage company settings'))
                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings' ||
                            Request::segment(1) == 'plans' ||
                            Request::segment(1) == 'stripe' ||
                            Request::segment(1) == 'order'
                                ? ' active dash-trigger'
                                : '' }}">
                            <a href="#!" class="dash-link">
                                <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                    class="dash-mtext">{{ __('Settings') }}</span>
                                <span class="dash-arrow">
                                    <i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                @if (Gate::check('manage company settings'))
                                    <li
                                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings' ? ' active' : '' }}">
                                        <a href="{{ route('settings') }}"
                                            class="dash-link">{{ __('System Settings') }}</a>
                                    </li>
                                @endif
                                {{-- @if (Gate::check('manage company plan'))
                                    <li
                                        class="dash-item{{ Request::route()->getName() == 'plans.index' || Request::route()->getName() == 'stripe' ? ' active' : '' }}">
                                        <a href="{{ route('plans.index') }}"
                                            class="dash-link">{{ __('Setup Subscription Plan') }}</a>
                                    </li>
                                @endif
                                <li
                                    class="dash-item{{ Request::route()->getName() == 'referral-program.company' ? ' active' : '' }}">
                                    <a href="{{ route('referral-program.company') }}"
                                        class="dash-link">{{ __('Referral Program') }}</a>
                                </li>

                                @if (Gate::check('manage order') && Auth::user()->type == 'company')
                                    <li class="dash-item {{ Request::segment(1) == 'order' ? 'active' : '' }}">
                                        <a href="{{ route('order.index') }}" class="dash-link">{{ __('Order') }}</a>
                                    </li>
                                @endif --}}
                            </ul>
                        </li>
                    @endif
                @endif




                <!--------------------- End System Setup ----------------------------------->
            </ul>
        @endif
        @if (\Auth::user()->type == 'client')
            <ul class="dash-navbar">
                @if (Gate::check('manage client dashboard'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' ? ' active' : '' }}">
                        <a href="{{ route('client.dashboard.view') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage deal'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'deals' ? ' active' : '' }}">
                        <a href="{{ route('deals.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-rocket"></i></span><span
                                class="dash-mtext">{{ __('Deals') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage contract'))
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'contract.index' || Request::route()->getName() == 'contract.show' ? 'active' : '' }}">
                        <a href="{{ route('contract.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-rocket"></i></span><span
                                class="dash-mtext">{{ __('Contract') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project'))
                    <li class="dash-item dash-hasmenu  {{ Request::segment(1) == 'projects' ? ' active' : '' }}">
                        <a href="{{ route('projects.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-share"></i></span><span
                                class="dash-mtext">{{ __('Project') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage project'))
                    <li
                        class="dash-item  {{ Request::route()->getName() == 'project_report.index' || Request::route()->getName() == 'project_report.show' ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('project_report.index') }}">
                            <span class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                                class="dash-mtext">{{ __('Project Report') }}</span>
                        </a>
                    </li>
                @endif

                @if (Gate::check('manage project task'))
                    <li class="dash-item dash-hasmenu  {{ Request::segment(1) == 'taskboard' ? ' active' : '' }}">
                        <a href="{{ route('taskBoard.view', 'list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-list-check"></i></span><span
                                class="dash-mtext">{{ __('Tasks') }}</span>
                        </a>
                    </li>
                @endif

                @if (Gate::check('manage bug report'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'bugs-report' ? ' active' : '' }}">
                        <a href="{{ route('bugs.view', 'list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-bug"></i></span><span
                                class="dash-mtext">{{ __('Bugs') }}</span>
                        </a>
                    </li>
                @endif

                {{-- @if (Gate::check('manage timesheet'))
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'timesheet-list' ? ' active' : '' }}">
                        <a href="{{ route('timesheet.list') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-clock"></i></span><span
                                class="dash-mtext">{{ __('Timesheet') }}</span>
                        </a>
                    </li>
                @endif --}}

                @if (Gate::check('manage project task'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'calendar' ? ' active' : '' }}">
                        <a href="{{ route('task.calendar', ['all']) }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-calendar"></i></span><span
                                class="dash-mtext">{{ __('Task Calender') }}</span>
                        </a>
                    </li>
                @endif

                {{-- Support System Removed --}}
            </ul>
        @endif
        @if (\Auth::user()->type == 'super admin')
            <ul class="dash-navbar">
                @if (Gate::check('manage super admin dashboard'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' ? ' active' : '' }}">
                        <a href="{{ route('client.dashboard.view') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif


                @can('manage user')
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' ? ' active' : '' }}">
                        <a href="{{ route('users.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-users"></i></span><span
                                class="dash-mtext">{{ __('Companies') }}</span>
                        </a>
                    </li>
                @endcan

                @if (Gate::check('manage plan'))
                    <li class="dash-item dash-hasmenu  {{ Request::segment(1) == 'plans' ? 'active' : '' }}">
                        <a href="{{ route('plans.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-trophy"></i></span><span
                                class="dash-mtext">{{ __('Plan') }}</span>
                        </a>
                    </li>
                @endif
                @if (\Auth::user()->type == 'super admin')
                    <li class="dash-item dash-hasmenu {{ request()->is('plan_request*') ? 'active' : '' }}">
                        <a href="{{ route('plan_request.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-arrow-up-right-circle"></i></span><span
                                class="dash-mtext">{{ __('Plan Request') }}</span>
                        </a>
                    </li>
                @endif

                <li class="dash-item dash-hasmenu  {{ Request::segment(1) == '' ? 'active' : '' }}">
                    <a href="{{ route('referral-program.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-discount-2"></i></span><span
                            class="dash-mtext">{{ __('Referral Program') }}</span>
                    </a>
                </li>


                @if (Gate::check('manage coupon'))
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'coupons' ? 'active' : '' }}">
                        <a href="{{ route('coupons.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                class="dash-mtext">{{ __('Coupon') }}</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('manage order'))
                    <li class="dash-item dash-hasmenu  {{ Request::segment(1) == 'orders' ? 'active' : '' }}">
                        <a href="{{ route('order.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-shopping-cart-plus"></i></span><span
                                class="dash-mtext">{{ __('Order') }}</span>
                        </a>
                    </li>
                @endif
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'email_template' || Request::route()->getName() == 'manage.email.language' ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="{{ route('email_template.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-template"></i></span>
                        <span class="dash-mtext">{{ __('Email Template') }}</span>
                    </a>
                </li>

                @if (\Auth::user()->type == 'super admin')
                    @include('landingpage::menu.landingpage')
                @endif

                @if (Gate::check('manage system settings'))
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'systems.index' ? ' active' : '' }}">
                        <a href="{{ route('systems.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span>
                        </a>
                    </li>
                @endif

            </ul>
        @endif


    </div>
</div>
</nav> 
