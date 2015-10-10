<ul class="sidebar-tabs">
    <li{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}"><span class="glyphicon glyphicon-list"></span></a></li>
    <li{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}#sidebar-1"><span class="glyphicon glyphicon-cog"></span></a></li>
</ul>
<ul id="sidebar-0" class="sidebar{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' sidebar-active' : '' !!}">
    <li{!! \Slug::isActiveClass(\Config::get('app.defaultAuthRoute')) !!}><a href="{{ \Locales::route() }}"><span class="glyphicon glyphicon-dashboard"></span>{{ trans('cms/nav.' . \Config::get('app.defaultAuthRoute')) }}</a></li>
    <li{!! \Slug::isActiveClass('pages') !!}><a href="{{ \Locales::route('pages') }}"><span class="glyphicon glyphicon-book"></span>{{ trans('cms/nav.pages') }}</a></li>
    <li class="dropdown {!! \Slug::isActive('users', 1) !!}">
        <a class="dropdown-toggle" href="#"><span class="glyphicon glyphicon-user"></span>{{ trans('cms/nav.users') }}<span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-static dropdown-menu-slide">
            <li{!! \Slug::isActiveClass('users/admins') !!}><a href="{{ \Locales::route('users/admins') }}">{{ trans('cms/nav.admins') }}</a></li>
            <li{!! \Slug::isActiveClass('users/operators') !!}><a href="{{ \Locales::route('users/operators') }}">{{ trans('cms/nav.operators') }}</a></li>
        </ul>
    </li>
</ul>
<ul id="sidebar-1" class="sidebar{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' sidebar-active' : '' !!}">
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 1</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 2</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 3</a></li>
</ul>
