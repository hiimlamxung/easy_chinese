@extends('frontend.layouts.master')

@section('title', 'Register')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="box-login">
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
            <form method="POST" action="{{ route('web.user.register') }}">
                {{ csrf_field() }}
                <div class="st-form-group">
                    <img alt="icon email" class="st_icon icon-14" src="{{ url('/frontend/images/ic_email.png') }}">
                    <input class="form-control input-data" name="email" value="{{ old('email') }}" placeholder="{{ trans('user.input_email') }}" type="text">
                </div>
                <div class="st-form-group">
                    <img class="st_icon icon-14" src="{{ url('/frontend/images/ic_lock.png') }}" alt="icon lock">
                    <input type="password" name="password" placeholder="{{ trans('user.input_password') }}" class="form-control input-data"/>
                </div>
                <div class="st-form-group">
                    <img class="st_icon icon-14" src="{{ url('/frontend/images/ic_lock.png') }}" alt="icon lock">
                    <input type="password" name="confirm_password" placeholder="{{ trans('user.retype_password') }}" class="form-control input-data"/>
                </div>
                <p>
                    <input _ngcontent-serverapp-c3="" class="check-accept" type="checkbox">
                    {{ trans('user.agree_term') }} <a href="https://eupgroup.net/privacy-easy-japanese/">{{ trans('user.term_of_use') }}</a> {{ trans('user.of_easy') }}
                </p>
                @if (isset($message))
                <p class="login-box-msg">{{ $message }}</p>
                @endif
                <div class="st-form-group">
                    <button class="btn-user btn-register" type="submit">{{ trans('user.create_account') }}</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

@endsection
