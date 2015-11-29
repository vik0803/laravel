@extends('cms.master')

@section('content')
    <div class="content-header">
        <h1>{{ \Locales::getMetaTitle() }}</h1>
    </div>

    @if (isset($datatables) && count($datatables) > 0)
        @include('cms/partials.datatables')
    @endif
@endsection

@if (isset($datatables) && count($datatables) > 0)
@section('script')
    unikat.callback = function() {
        this.datatables({!! json_encode($datatables) !!});
    };
@endsection
@endif
