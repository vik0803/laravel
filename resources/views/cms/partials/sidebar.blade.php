<ul class="sidebar-tabs">
    <li{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}"><span class="glyphicon glyphicon-list"></span></a></li>
    <li{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}#sidebar-1"><span class="glyphicon glyphicon-cog"></span></a></li>
</ul>
<ul id="sidebar-0" class="sidebar{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' sidebar-active' : '' !!}">
    <li{!! $slug == 'dashboard' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('dashboard')) }}"><span class="glyphicon glyphicon-dashboard"></span>{{ trans('cms/nav.dashboard') }}</a></li>
    <li{!! $slug == 'pages' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('pages')) }}"><span class="glyphicon glyphicon-book"></span>{{ trans('cms/nav.pages') }}</a></li>
    <li class="dropdown{!! $slugs[0] == 'users' ? ' active' : '' !!}">
        <a class="dropdown-toggle" href="#"><span class="glyphicon glyphicon-user"></span>{{ trans('cms/nav.users') }}<span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-static dropdown-menu-slide{!! $slugs[0] == 'users' ? ' open' : '' !!}">
            <li{!! $slug == 'users/admins' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('users/admins')) }}">{{ trans('cms/nav.admins') }}</a></li>
            <li{!! $slug == 'users/operators' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('users/operators')) }}">{{ trans('cms/nav.operators') }}</a></li>
        </ul>
    </li>
</ul>
<ul id="sidebar-1" class="sidebar{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' sidebar-active' : '' !!}">
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 1</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 2</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 3</a></li>
</ul>
