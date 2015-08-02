<ul>
    <li class="active"><a href="#"><span class="glyphicon glyphicon-inbox"></span>Pages</a></li>
    <li><a href="#"><span class="glyphicon glyphicon-user"></span>Media</a></li>
    <li class="dropdown">
        <a class="dropdown-toggle" href="#"><span class="glyphicon glyphicon-cog"></span>Gallery <span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-static dropdown-menu-slide">
            <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}">{{ trans('cms/messages.profile') }}</a></li>
            <li class="active"><a href="{{ url(\Locales::getLocalizedURL('logout')) }}">{{ trans('cms/messages.messages') }}</a></li>
            <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}">{{ trans('cms/messages.settings') }}</a></li>
            <li><a href="{{ url(\Locales::getLocalizedURL('logout')) }}">{{ trans('cms/messages.logout') }}</a></li>
        </ul>
    </li>
    <li><a href="#"><span class="glyphicon glyphicon-user"></span>Media</a></li>
</ul>
