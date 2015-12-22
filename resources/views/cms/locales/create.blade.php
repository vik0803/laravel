@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    @include('cms/shared.errors')

    @if (isset($locale))
    {!! Form::model($locale, ['method' => 'put', 'url' => \Locales::route('settings/locales/update'), 'id' => 'edit-locale-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @else
    {!! Form::open(['url' => \Locales::route('settings/locales/store'), 'id' => 'create-locale-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @endif

    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel')) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('native') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-native', trans('cms/forms.nativeLabel')) !!}
        {!! Form::text('native', null, ['id' => 'input-native', 'class' => 'form-control', 'placeholder' => trans('cms/forms.nativePlaceholder')]) !!}
        @if ($errors->has('native'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('locale') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-locale', trans('cms/forms.localeLabel')) !!}
        {!! Form::text('locale', null, ['id' => 'input-locale', 'class' => 'form-control', 'placeholder' => trans('cms/forms.localePlaceholder')]) !!}
        @if ($errors->has('locale'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('script') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-script', trans('cms/forms.scriptLabel')) !!}
        {!! Form::select('script', $scripts, $script, ['id' => 'input-script', 'class' => 'form-control']) !!}
        @if ($errors->has('script'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('description') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-description', trans('cms/forms.descriptionLabel')) !!}
        {!! Form::textarea('description', null, ['id' => 'input-description', 'class' => 'form-control', 'placeholder' => trans('cms/forms.descriptionPlaceholder')]) !!}
        @if ($errors->has('description'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    @if (isset($locale))
    {!! Form::submit(trans('cms/forms.updateButton'), ['class' => 'btn btn-warning btn-block']) !!}
    @else
    {!! Form::submit(trans('cms/forms.storeButton'), ['class' => 'btn btn-primary btn-block']) !!}
    @endif

    {!! Form::close() !!}
</div>
@endsection
