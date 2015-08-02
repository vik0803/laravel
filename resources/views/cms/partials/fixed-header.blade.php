<div id="fixed-header">
    <div class="nav-toggle"><a href="#"><span class="glyphicon glyphicon-menu-hamburger"></span></a></div>
    <a href="{{ url(\Locales::getLocaleURL(\Locales::get())) }}">{!! HTML::image(\App\Helpers\autover('/img/cms/logo-mobile.png'), trans('cms/messages.altLogo'), ['class' => 'mobile-logo']) !!}</a>
    <ul>
        <li class="dropdown">
            <a class="dropdown-toggle">
                {!! HTML::image(\App\Helpers\autover('/img/cms/languages.png'), trans('cms/messages.changeLanguage')) !!}
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
                <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}"><span class="glyphicon glyphicon-user"></span>{{ trans('cms/messages.profile') }}</a></li>
                <li class="active"><a href="{{ url(\Locales::getLocalizedURL('logout')) }}"><span class="glyphicon glyphicon-inbox"></span>{{ trans('cms/messages.messages') }}</a></li>
                <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}"><span class="glyphicon glyphicon-cog"></span>{{ trans('cms/messages.settings') }}</a></li>
                <li class="divider"></li>
                <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}"><span class="glyphicon glyphicon-remove"></span>{{ trans('cms/messages.logout') }}</a></li>
            </ul>
        </li>
    </ul>
</div>
