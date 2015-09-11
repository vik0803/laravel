<div id="breadcrumbs">
    <ol>
        <li class="nav-toggle{!! isset($jsCookies['navState']) ? ' collapsed' : '' !!}"><a href="#"><span class="glyphicon glyphicon-menu-hamburger"></span></a></li>
        <?php
        $tempTotal = count($slugs) - 1;
        $tempSlugs = ''
        ?>
        @for ($i = 0; $i <= $tempTotal; $i++)
            <?php $tempSlugs .= $slugs[$i] . '/'; ?>
            @if ($i == 0 && $slugs[$i] != \Config::get('app.defaultAuthRoute'))
                <li><a href="{{ url(\Locales::getLocalizedURL(\Config::get('app.defaultAuthRoute'))) }}">{{ trans('cms/nav.' . \Config::get('app.defaultAuthRoute')) }}</a><span class="glyphicon glyphicon-chevron-right"></span></li>
            @endif
            <li{!! $i == $tempTotal ? ' class="active"' : '' !!}>
                <a href="{{ url(\Locales::getLocalizedURL($tempSlugs)) }}">{{ trans('cms/nav.' . $slugs[$i]) }}</a>@if ($i < $tempTotal)<span class="glyphicon glyphicon-chevron-right"></span>@endif
            </li>
        @endfor
    </ol>
</div>
