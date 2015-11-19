@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1 class="text-center">{{ \Locales::getMetaTitle() }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['method' => 'delete', 'url' => \Locales::route('users/destroy'), 'id' => 'destroy-user-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}
    {!! Form::submit(trans('cms/forms.destroyButton'), ['class' => 'btn btn-danger btn-block']) !!}
    {!! Form::close() !!}
</div>
@endsection
