<div id="breadcrumbs">
    <ol>
        <li class="nav-toggle{!! isset($jsCookies['navState']) ? ' collapsed' : '' !!}"><a href="#"><span class="glyphicon glyphicon-menu-hamburger"></span></a></li>
        @foreach (\Locales::createBreadcrumbsFromSlugs() as $breadcrumb)
            <li{!! $breadcrumb['last'] ? ' class="active"' : '' !!}><a href="{{ $breadcrumb['link'] }}">{{ $breadcrumb['name'] }}</a></li>
            @if (!$breadcrumb['last'])
                <li class="separator"><span class="glyphicon glyphicon-chevron-right"></span></li>
            @endif
        @endforeach
    </ol>
</div>
