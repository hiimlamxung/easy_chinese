<div id="header" class="">
    <div class="box-default-header">
        <div class="line-main box-web">
            <div class="logo header-left"> 
                <a href="{{ route('web.home') }}" class="navbar-brand">
                    <span>EASY</span>
                    <span>CHINESE</span>
                </a>
            </div>
            <div class="header-right">
                <div class="">
                    <span class="local"></span>
                    <img class="icon-weather"> 
                    <span class="temp"></span>
                </div>
            </div>
        </div>  
        <div class="line-small box-web">
            <div class="line-item header-left"> 
                <div class="line-item menu-item"> 
                    <a href="{{ route('web.home', ['hl' => $lang]) }}">
                        {{ trans('label.topic.home') }}
                    </a>                 
                </div>
            </div>
            <div class="header-right line-item">
                @if (Auth::guest())
                <div class="login line-item">
                    <a href="{{ route('web.user.login', [ 'hl' => $lang ]) }}">{{ trans('user.login') }}</a>
                    <span>|</span>
                </div>
                <div class="login line-item">
                    <a href="{{ route('web.user.register', [ 'hl' => $lang ]) }}">{{ trans('user.register') }}</a>
                    <span>|</span>
                </div>
                @else
                <div class="user line-item">
                    @if(Auth::user()->image)
                    <img class="avatar" src="{{ Auth::user()->image }}" onerror="this.src='{{ url('/frontend/images/ic_avatar.png') }}' ">
                    @else
                    <img class="avatar" src="{{ url('/frontend/images/ic_avatar.png') }}">
                    @endif
                    <span class="username user-setting">{{ Auth::user()->name }}</span>
                    <span>|</span>
                    <div class="box-user">
                        <a class="" href=" {{ route('web.user.profile') }}">
                            <div>{{ trans('user.profile') }}</div>
                        </a>
                        <a class="" href=" {{ route('web.user.logout') }}">
                            <span>{{ trans('user.logout') }}</span>
                        </a>
                    </div>
                </div>
                @endif
                <div class="setting line-item">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                </div>
                <div class="box-setting">
                    @include('frontend.includes.setting')
                </div>
            </div>
        </div>
        <div class="cover"></div>
        <div class="main-mobile box-mobile">
            <div class="f-left open-menu">
                <span><i class="fas fa-bars"></i></span>
            </div>
            <div class="f-right setting-icon">
                <span><i class="fas fa-cog"></i></span>
            </div>
        </div>
        <div class="menu-left box-mobile">
            <ul class="list-group">
                <li class="list-group-item text-logo">
                    <div class="logo header-left"> 
                        <a href="{{ route('web.news', ['type' => Session::get('type'), 'hl' => $lang ]) }}" class="navbar-brand">
                            <span>EASY</span>
                            <span>JAPANESE</span>
                        </a>
                    </div>
                    @if (Auth::guest())
                    <div class="right">
                        <a class="btn btn-primary btn-sm btn-login-mobi" href="{{ route('web.user.login') }}">{{ trans('user.login') }}</a>
                        <a class="btn btn-danger btn-sm btn-register-mobi" href="{{ route('web.user.register') }}">{{ trans('user.register') }}</a>
                    </div>
                    @else
                    <div class="f-right">
                        <a class="username" href=" {{ route('web.user.profile') }}">{{ Auth::user()->name }}</a>
                        <a class="btn btn-danger btn-sm btn-logout-mobi" href=" {{ route('web.user.logout') }}">{{ trans('user.logout') }}</a>
                    </div>
                    @endif
                </li>
                <li class="list-menu-item">
                  
                </li>
            </ul>
        </div>
    </div>
    <div class="setting-panel">
        <div class="setting-screen">
            <h3>Tùy chỉnh</h3>
            <hr>
            @include('frontend.includes.setting')
        </div>
    </div>
</div>
