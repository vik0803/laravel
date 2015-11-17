@foreach($datatables as $id => $table)
    @foreach($table['buttons'] as $button)
    <div class="btn-group">
        <a data-table="{{ $id }}" href="{{ $button['url'] }}" class="btn {{ $button['class'] }}">
            @if ($button['icon'])<span class="glyphicon glyphicon-{{ $button['icon'] }}"></span>@endif
            {{ $button['name'] }}
        </a>
    </div>
    @endforeach
    <div class="dataTableWrapper table-responsive ajax-lock" data-ajax-queue="async-{{ $id }}">
        <table id="datatable{{ $id }}" class="dataTable table {{ $table['class'] }}">
        @if ($table['ajax'])
            <thead>
            @foreach($table['columns'] as $column)
                <th>{{ trans('cms/datatables.' . $column) }}</th>
            @endforeach
            </thead>
            <tbody>
            </tbody>
        @endif
        </table>
    </div>
@endforeach
