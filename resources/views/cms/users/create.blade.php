@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['url' => \Locales::route('users/create'), 'id' => 'create-user-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    {!! $table ? Form::hidden('table', $table) : '' !!}

    <div class="form-group{!! ($errors->has('group') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-group', trans('cms/forms.groupLabel')) !!}
        {!! Form::select('group', $roles, $group, ['id' => 'input-group', 'class' => 'form-control']) !!}
        @if ($errors->has('group'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel')) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('email') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-email', trans('cms/forms.emailLabel')) !!}
        {!! Form::email('email', null, ['id' => 'input-email', 'class' => 'form-control', 'placeholder' => trans('cms/forms.emailPlaceholder')]) !!}
        @if ($errors->has('email'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('password') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-password', trans('cms/forms.passwordLabel')) !!}
        {!! Form::password('password', ['id' => 'input-password', 'class' => 'form-control', 'placeholder' => trans('cms/forms.passwordPlaceholder')]) !!}
        @if ($errors->has('password'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('password_confirmation') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-password_confirmation', trans('cms/forms.confirmPasswordLabel')) !!}
        {!! Form::password('password_confirmation', ['id' => 'input-password_confirmation', 'class' => 'form-control', 'placeholder' => trans('cms/forms.confirmPasswordPlaceholder')]) !!}
        @if ($errors->has('password_confirmation'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    {!! Form::submit(trans('cms/forms.saveButton'), ['class' => 'btn btn-primary btn-block']) !!}
    {!! Form::close() !!}
</div>
@endsection
