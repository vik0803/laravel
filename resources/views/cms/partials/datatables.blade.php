@foreach($datatables as $id => $table)
    @foreach($table['buttons'] as $button)
    <div class="btn-group">
        <a @if ($multipleDatatables)data-table="{{ $id }}"@endif href="{{ $button['url'] }}" class="btn {{ $button['class'] }}">
            @if ($button['icon'])<span class="glyphicon glyphicon-{{ $button['icon'] }}"></span>@endif
            {{ $button['name'] }}
        </a>
    </div>
    @endforeach
    <div class="dataTableWrapper table-responsive ajax-lock" data-ajax-queue="async-{{ $id }}">
        <table id="datatable{{ $id }}" class="dataTable table {{ $table['class'] }}"></table>
    </div>
@endforeach
