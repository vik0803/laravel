<ul class="sidebar-tabs">
    <li{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}"><span class="glyphicon glyphicon-list"></span>{{ trans('cms/messages.menu') }}</a></li>
    <li{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}#sidebar-1"><span class="glyphicon glyphicon-cog"></span>{{ trans('cms/messages.settings') }}</a></li>
</ul>
<ul id="sidebar-0" class="slidedown-menu menu-static sidebar{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' sidebar-active' : '' !!}">
    @include('cms/partials.sidebar-menu-recursive')
</ul>
<ul id="sidebar-1" class="slidedown-menu menu-static sidebar{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' sidebar-active' : '' !!}">
    @include('cms/partials.sidebar-settings-recursive')
</ul>
