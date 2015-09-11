<div id="fixed-header">
    <a class="mobile-logo" href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}">
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
                <li class="active">
                    <a href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}">
                    {{ \Locales::getNativeName() }}
                    @if (\Locales::getName() != \Locales::getNativeName())
                        <span class="sub-text">({{ \Locales::getName() }})</span>
                    @endif
                    </a>
                </li>
                @foreach (\Locales::getSupportedLocales() as $key => $value)
                    @if ($key != \Locales::get())
                        <li>
                            <a href="{{ url(\Locales::getLocaleURL($key)) }}">
                            {{ $value['native'] }}
                            @if ($value['name'] != $value['native'])
                                <span class="sub-text">({{ $value['name'] }})</span>
                            @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li{!! $slug == 'profile' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('profile')) }}"><span class="glyphicon glyphicon-user"></span>{{ trans('cms/nav.profile') }}</a></li>
                <li{!! $slug == 'messages' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('messages')) }}"><span class="glyphicon glyphicon-inbox"></span>{{ trans('cms/nav.messages') }}</a></li>
                <li{!! $slug == 'settings' ? ' class="active"' : '' !!}><a href="{{ url(\Locales::getLocalizedURL('settings')) }}"><span class="glyphicon glyphicon-cog"></span>{{ trans('cms/nav.settings') }}</a></li>
                <li class="divider"></li>
                <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}"><span class="glyphicon glyphicon-remove"></span>{{ trans('cms/nav.logout') }}</a></li>
            </ul>
        </li>
    </ul>
</div>
