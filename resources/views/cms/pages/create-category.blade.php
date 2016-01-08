@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    @if (isset($page))
    {!! Form::model($page, ['method' => 'put', 'url' => \Locales::route('pages/update'), 'id' => 'edit-category-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @else
    {!! Form::open(['url' => \Locales::route('pages/store'), 'id' => 'create-category-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @endif

    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}
    {!! Form::hidden('is_category', 1, ['id' => 'input-is_category']) !!}

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

    <div class="form-group{!! ($errors->has('slug') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-slug', trans('cms/forms.slugLabel')) !!}
        {!! Form::text('slug', null, ['id' => 'input-slug', 'class' => 'form-control', 'placeholder' => trans('cms/forms.slugPlaceholder')]) !!}
        @if ($errors->has('slug'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('description') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-description', trans('cms/forms.descriptionLabel')) !!}
        {!! Form::text('description', null, ['id' => 'input-description', 'class' => 'form-control', 'placeholder' => trans('cms/forms.descriptionPlaceholder')]) !!}
        @if ($errors->has('description'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group">
        {!! Form::checkboxInline('is_dropdown', 1, null, ['id' => 'input-is_dropdown'], trans('cms/forms.isDropdownOption'), ['class' => 'checkbox-inline']) !!}
    </div>

    <div class="form-group{!! ($errors->has('content') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-content', trans('cms/forms.contentLabel')) !!}
        {!! Form::textarea('content', null, ['id' => 'input-content', 'class' => 'form-control', 'placeholder' => trans('cms/forms.contentPlaceholder')]) !!}
        @if ($errors->has('content'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    @if (isset($page))
    {!! Form::submit(trans('cms/forms.updateButton'), ['class' => 'btn btn-warning btn-block']) !!}
    @else
    {!! Form::submit(trans('cms/forms.storeButton'), ['class' => 'btn btn-primary btn-block']) !!}
    @endif

    {!! Form::close() !!}
</div>
@endsection
