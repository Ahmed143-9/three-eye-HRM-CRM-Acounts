@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';
@endphp

@push('custom-scripts')
    @if ($settings['recaptcha_module'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@section('page-title')
    {{ __('Login') }}
@endsection

@if ($settings['cust_darklayout'] == 'on')
    <style>
        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }
    </style>
@endif

@php
    $languages = App\Models\Utility::languages();
@endphp

@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ $languages[$lang] }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('login', $code) }}" class="dropdown-item @if ($lang == $code) text-primary @endif">
                        <span>{{ Str::upper($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')

        <div class="card-body">
            {{ Form::open(['route' => 'login', 'method' => 'post', 'id' => 'loginForm', 'class' => 'm-0']) }}
            <div class="horizontal-login-form d-flex align-items-center" style="gap: 8px;">
                <div class="form-group mb-0">
                    {{ Form::text('email', null, ['class' => 'form-control form-control-sm', 'placeholder' => __('Email'), 'required' => 'required', 'style' => 'background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.4); color: white; width: 160px;']) }}
                </div>
                <div class="form-group mb-0">
                    {{ Form::password('password', ['class' => 'form-control form-control-sm', 'placeholder' => __('Password'), 'id' => 'input-password', 'required' => 'required', 'style' => 'background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.4); color: white; width: 160px;']) }}
                </div>
                <div class="form-group mb-0">
                    {{ Form::submit(__('LOGIN'), ['class' => 'btn btn-success btn-sm px-3 fw-bold', 'id' => 'saveBtn', 'style' => 'height: 31px; line-height: 1;']) }}
                </div>
            </div>
            {{ Form::close() }}
        </div>
@endsection

<script src="{{ asset('js/jquery.min.js') }}"></script>
@if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
    @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
        {!! NoCaptcha::renderJs() !!}
    @else
        <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
        <script>
            $(document).ready(function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', {
                        action: 'submit'
                    }).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                    });
                });
            });
        </script>
    @endif
@endif
