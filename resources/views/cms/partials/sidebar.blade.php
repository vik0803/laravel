<ul class="sidebar-tabs">
    <li{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}"><span class="glyphicon glyphicon-list"></span></a></li>
    <li{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' class="sidebar-tab-active"' : '' !!}><a href="{{ \Request::url() }}#sidebar-1"><span class="glyphicon glyphicon-cog"></span></a></li>
</ul>
<ul id="sidebar-0" class="slidedown-menu menu-static sidebar{!! (!isset($jsCookies['sidebar']) || (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 0)) ? ' sidebar-active' : '' !!}">
    @include('cms/partials.sidebar-recursive')
</ul>
<ul id="sidebar-1" class="sidebar{!! (isset($jsCookies['sidebar']) && $jsCookies['sidebar'] == 1) ? ' sidebar-active' : '' !!}">
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 1</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 2</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-ok"></span>Test 3</a></li>
</ul>
