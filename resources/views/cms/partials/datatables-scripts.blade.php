@foreach($datatables as $id => $table)
    this.variables.tables['datatable{{ $id }}'] = $('#datatable{{ $id }}').DataTable({
    paging: {{ $table['count'] > \Config::get('datatables.paging') ? 'true' : 'false' }},
    searchDelay: {{ $table['ajax'] ? \Config::get('datatables.searchDelay') : 0 }},
    serverSide: {{ $table['ajax'] ? 'true' : 'false' }},
    pagingType: '{{ \Config::get('datatables.pagingType' . $table['size']) }}',
    pageLength: {{ \Config::get('datatables.pageLength' . $table['size']) }},
    lengthMenu: {!! str_replace('all', trans('cms/messages.all'), \Config::get('datatables.lengthMenu' . $table['size'])) !!},
    order: [[{{ $table['orderByColumn'] }}, '{{ $table['order'] }}']],
    @if ($table['ajax'])
        ajax: this.datatables({
            url: '{{ $table['url'] }}'
        }),
    @else
        data: {!! $table['data'] !!},
    @endif
        columns: [
        @foreach($table['columns'] as $column)
            { data: '{{ $column }}', title: '{{ trans('cms/datatables.' . $column) }}' },
        @endforeach
        ]
    });
@endforeach
