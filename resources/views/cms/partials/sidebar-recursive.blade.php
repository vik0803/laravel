<?php $nav = (isset($item) ? $item['children'] : \Locales::getNavigation('sidebar')); ?>
@foreach ($nav as $item)
    @if ($item['divider-before'])<li class="divider"></li>@endif
    @if (isset($item['children']))
    <li class="submenu {!! $item['active'] ? $item['active'] : '' !!}">
        <a class="slidedown-toggle" href="{{ $item['link'] }}">
            @if ($item['icon'])<span class="glyphicon glyphicon-{{ $item['icon'] }}"></span>@endif{{ $item['name'] }}<span class="caret"></span>
        </a>
        <ul data-level="{{ $item['level'] }}"  class="slidedown-menu menu-static">
            @include('cms/partials.sidebar-recursive')
        </ul>
    </li>
    @else
    <li{!! $item['active'] ? ' class="' . $item['active'] . '"' : '' !!}>
        <a href="{{ $item['link'] }}">
            @if ($item['icon'])<span class="glyphicon glyphicon-{{ $item['icon'] }}"></span>@endif{{ $item['name'] }}
        </a>
    </li>
    @endif
    @if ($item['divider-after'])<li class="divider"></li>@endif
@endforeach
