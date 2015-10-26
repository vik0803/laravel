<div id="fixed-header">
    <a class="mobile-logo" href="{{ \Locales::route() }}">
        {!! HTML::image(\App\Helpers\autover('/img/cms/logo-nav.png'), trans('cms/messages.altLogo')) !!}
        Vadenka.com
    </a>
    <ul>
        <li class="submenu">
            <a class="dropdown-toggle">
                {{ trans('cms/messages.changeLanguage') }}
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
            @foreach (\Locales::getLanguages() as $key => $language)
                <li{!! $language['active'] ? ' class="active"' : '' !!}>
                    <a href="{{ $language['link'] }}">
                        {{ $language['native'] }}@if ($language['name'])<span class="sub-text">({{ $language['name'] }})</span>@endif
                    </a>
                </li>
            @endforeach
            </ul>
        </li>
        <li class="submenu">
            <a class="dropdown-toggle">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu dropdown-menu-small dropdown-menu-right">
            @foreach (\Locales::getNavigation('header') as $nav)
                @if ($nav['divider-before'])<li class="divider"></li>@endif
                <li{!! $nav['active'] ? ' class="' . $nav['active'] . '"' : '' !!}>
                    <a href="{{ $nav['link'] }}">
                        @if ($nav['icon'])<span class="glyphicon glyphicon-{{ $nav['icon'] }}"></span>@endif{{ $nav['name'] }}
                    </a>
                </li>
                @if ($nav['divider-after'])<li class="divider"></li>@endif
            @endforeach
            </ul>
        </li>
    </ul>
</div>
