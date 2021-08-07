@extends('frontend.layouts.master')

@section('title', 'Login')

@section('main-content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="box-login">
            @if(Session::has('error'))
                {{ Session::get('error') }}
            @endif
            <p>{{ trans('user.fast_login') }}:</p>
            <div class="box-login-fast">
                <div class="with-fb">
                    <a href="{{ url('/login/facebook') }}">
                        <i class="fab fa-facebook-f"></i>
                        <p class="txt-fb">Facebook</p>
                    </a>
                </div>
                <div class="with-gg" id="google-sign-in-btn">
                    <a href="{{ url('/login/google') }}">
                        <i class="fab fa-google-plus-g"></i>
                        <p class="txt-gg">Google</p>
                    </a>
                </div>
            </div>
            <p>{{ trans('user.or') }}</p>
            <form method="POST" action="{{ route('web.user.login') }}">
                {{ csrf_field() }}
                <div class="st-form-group">
                    <img alt="icon email" class="st_icon icon-16" src="{{ url('/frontend/images/ic_email.png') }}">
                    <input class="form-control input-data" name="email" placeholder="{{ trans('user.input_email') }}" type="text">
                </div>
                <div class="st-form-group">
                    <img class="st_icon icon-16" src="{{ url('/frontend/images/ic_lock.png') }}" alt="icon lock">
                    <input type="password" name="password" placeholder="{{ trans('user.input_password') }}" class="form-control input-data"/>
                </div>
                <p>
                    <input _ngcontent-serverapp-c3="" class="check-remember" type="checkbox">
                    {{ trans('user.remember_me') }}
                </p>
                @if (isset($message))
                <p class="login-box-msg">{{ $message }}</p>
                @endif
                <div class="st-form-group">
                    <button class="btn-user btn-login" type="submit">{{ trans('user.login') }}</button>
                </div>
                <div class="forgot-password text-right">
                    <a href="{{ route('web.user.register') }}"><i class="fas fa-plus-circle"></i> {{ trans('user.create_new_account') }}</a>
                    {{-- <span>{{ trans('user.forgot_password') }}</span>
                    <a class="f-right" href="{{ route('web.user.register') }}"><i class="fas fa-plus-circle"></i> {{ trans('user.create_new_account') }}</a> --}}
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
@endsection
