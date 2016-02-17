@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    {!! Form::model($image, ['method' => 'put', 'url' => \Locales::route('nav/update-image'), 'id' => 'edit-image-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}

    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel')) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('title') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-title', trans('cms/forms.titleLabel')) !!}
        {!! Form::text('title', null, ['id' => 'input-title', 'class' => 'form-control', 'placeholder' => trans('cms/forms.titlePlaceholder')]) !!}
        @if ($errors->has('title'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    {!! Form::submit(trans('cms/forms.updateButton'), ['class' => 'btn btn-warning btn-block']) !!}

    {!! Form::close() !!}
</div>
@endsection
