@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    {!! Form::model($image, ['method' => 'put', 'url' => \Locales::route('banners/update-image'), 'id' => 'edit-image-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}

    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel')) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('description') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-description', trans('cms/forms.descriptionLabel')) !!}
        {!! Form::text('description', null, ['id' => 'input-description', 'class' => 'form-control', 'placeholder' => trans('cms/forms.descriptionPlaceholder')]) !!}
        @if ($errors->has('description'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('url') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-url', trans('cms/forms.urlLabel')) !!}
        {!! Form::text('url', null, ['id' => 'input-url', 'class' => 'form-control', 'placeholder' => trans('cms/forms.urlPlaceholder')]) !!}
        @if ($errors->has('url'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('identifier') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-identifier', trans('cms/forms.identifierLabel')) !!}
        {!! Form::text('identifier', null, ['id' => 'input-identifier', 'class' => 'form-control', 'placeholder' => trans('cms/forms.identifierPlaceholder')]) !!}
        @if ($errors->has('identifier'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    {!! Form::submit(trans('cms/forms.updateButton'), ['class' => 'btn btn-warning btn-block']) !!}

    {!! Form::close() !!}
</div>
@endsection
