@extends('cms.master')

@section('content')
    <div class="content-header">
        <h1>{{ \Locales::getMetaTitle() }}</h1>
    </div>

    @include('cms/partials.datatables')
@endsection

@section('script')
    unikat.callback = function() {
        @include('cms/partials.datatables-scripts')
    };
@endsection
