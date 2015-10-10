<div id="fixed-header">
    <a class="mobile-logo" href="{{ \Locales::route() }}">
        {!! HTML::image(\App\Helpers\autover('/img/cms/logo-nav.png'), trans('cms/messages.altLogo')) !!}
        Vadenka.com
    </a>
    <ul>
        <li class="dropdown">
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
        <li class="dropdown">
            <a class="dropdown-toggle">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li{!! \Slug::isActiveClass('profile') !!}><a href="{{ \Locales::route('profile') }}"><span class="glyphicon glyphicon-user"></span>{{ trans('cms/nav.profile') }}</a></li>
                <li{!! \Slug::isActiveClass('messages') !!}><a href="{{ \Locales::route('messages') }}"><span class="glyphicon glyphicon-inbox"></span>{{ trans('cms/nav.messages') }}</a></li>
                <li{!! \Slug::isActiveClass('settings') !!}><a href="{{ \Locales::route('settings') }}"><span class="glyphicon glyphicon-cog"></span>{{ trans('cms/nav.settings') }}</a></li>
                <li class="divider"></li>
                <li><a href="{{ \Locales::route('logout') }}"><span class="glyphicon glyphicon-remove"></span>{{ trans('cms/nav.logout') }}</a></li>
            </ul>
        </li>
    </ul>
</div>
