<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <ul class="nav side-menu">
            <li style="border-bottom: 0.1em solid #666666">
                <a href="{{ route('dashboard', app()->getLocale()) }}">
                    <i class="fa fa-dashboard"></i>{{ __('menu.dashboard') }}
                </a>
            </li>

            <li style="border-bottom: 0.1em solid #666666">
                <a href="{{ route('users.index', app()->getLocale()) }}">
                    <i class="fa fa-list"></i>Manage Admin Users
                </a>
            </li>

            <li style="border-bottom: 0.1em solid #666666">
                <a href="{{ route('customers.index', app()->getLocale()) }}">
                    <i class="fa fa-list"></i>Manage Users
                </a>
            </li>
        </ul>
    </div>
</div>
