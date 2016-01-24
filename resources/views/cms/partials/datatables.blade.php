@foreach($datatables as $id => $table)
    <div class="dataTableWrapper table-responsive ajax-lock" data-ajax-queue="async-{{ $id }}">
        <div class="btn-group-wrapper">
            @foreach($table['buttons'] as $button)
            <div class="btn-group">
                @if (isset($button['upload']))
                <div id="{{ $button['id'] }}" data-page-id="{{ $pageId or '' }}" data-url="{{ $button['url'] }}" data-table="{{ $id }}" class="btn {{ $button['class'] }}">
                    @if ($button['icon'])<span class="glyphicon glyphicon-{{ $button['icon'] }}"></span>@endif
                    {{ $button['name'] }}
                </div>
                @else
                <a data-table="{{ $id }}" href="{{ $button['url'] }}" class="btn {{ $button['class'] }}">
                    @if ($button['icon'])<span class="glyphicon glyphicon-{{ $button['icon'] }}"></span>@endif
                    {{ $button['name'] }}
                </a>
                @endif
            </div>
            @endforeach
        </div>
        <table id="datatable{{ $id }}" class="dataTable table {{ $table['class'] }}"></table>
    </div>
@endforeach
