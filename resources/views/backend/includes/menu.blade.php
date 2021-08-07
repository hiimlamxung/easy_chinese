<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>Administrator</h3>
        <ul class="nav side-menu">
            <!-- <li class="{{ Request::is('auth') ? 'active' : '' }}"><a><i class="fa fa-users"></i> Authentication <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li class="{{ Request::is('profile') ? 'active' : '' }}">
                        <a href="{{ route('backend.profile', ['admin' => $guard->id]) }}">Profile</a>
                    </li>
                </ul>
            </li> -->
            <li class="{{ Request::is('news') ? 'active' : '' }}"><a><i class="fa fa-book"></i> Báo <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li class="{{ Request::is('create') ? 'active' : '' }}">
                        <a href="{{ route('admin.news.create') }}">Thêm báo mới</a>
                    </li>
                    <li class="{{ Request::is('manager') ? 'active' : '' }}">
                        <a href="{{ route('admin.news.manager', ['module' => 'new']) }}">Quản lý bài báo</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('statistical') ? 'active' : '' }}"><a><i class="fa fa-line-chart"></i> Thống kê <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @if (in_array(Auth::guard('admin')->user()->role, [1,2]))
                    <li class="{{ Request::is('ctv') ? 'active' : '' }}">
                        <a href="{{ route('admin.statistical.ctv') }}">CTV</a>
                    </li>
                    @endif
                    @if (in_array(Auth::guard('admin')->user()->role, [1,3]))
                    <li class="{{ Request::is('manager') ? 'active' : '' }}">
                        <a href="{{ route('admin.statistical.censorship') }}">Kiểm duyệt viên</a>
                    </li>
                    @endif
                </ul>
            </li>
            @if (in_array(Auth::guard('admin')->user()->role, [1]))
            <li class="{{ Request::is('user') ? 'active' : '' }}"><a><i class="fa fa-user"></i> User <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li class="{{ Request::is('user') ? 'active' : '' }}">
                        <a href="{{ route('admin.user') }}">CTV</a>
                    </li>
                    <li class="{{ Request::is('list') ? 'active' : '' }}">
                        <a href="{{ route('admin.user.list') }}">User</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('sale') ? 'active' : '' }}">
                <a href="{{ route('admin.saleoff') }}">
                    <i class="fa fa-bolt" aria-hidden="true"></i> Sale off
                </a>
            </li>
            @endif

            @if( Auth::guard('admin')->user()->role == 1)
            <li class="{{Request::is('code') ? 'active' : ''}}"><a><i class="fa fa-code"></i> Code <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li class="{{Request::is('code') ? 'active' : ''}}">
                        <a href="{{route('admin.code.list')}}">Danh sách</a>
                    </li>
                    <li class="{{Request::is('pick') ? 'active' : ''}}">
                        <a href="{{route('admin.code.pick')}}">Lấy mã</a>
                    </li>
                    <li class="{{Request::is('generate') ? 'active' : ''}}">
                        <a href="{{route('admin.code.generate')}}">Tạo mã</a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>
        
</div>