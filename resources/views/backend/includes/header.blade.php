<div class="top_nav">
    <div class="nav_menu">
        <nav>
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>

        <ul class="nav navbar-nav navbar-right">
            <li class="">
                <a href="javascript:void();" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="{{ (!empty($guard->image)) ? $guard->image : url('images/img.jpg') }}" alt="">{{ $guard->username }}
                    <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="{{ route('backend.profile', ['admin' => $guard->id]) }}"> Profile</a></li>
                    <li><a href="{{route('backend.logout')}}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                </ul>
            </li>
        </ul>
        </nav>
    </div>
</div>