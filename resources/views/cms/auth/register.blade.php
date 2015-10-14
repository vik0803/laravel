<?php
$metaTitle = 'Register';
$metaDescription = 'Register Description';
?>

@extends('cms.auth.master')

@section('content')
    <h1>{{ trans('cms/auth.registerTitle') }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['id' => 'register-form', 'class' => 'ajax-lock', 'role' => 'form']) !!}

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel'), ['class' => 'sr-only']) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('email') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-email', trans('cms/forms.emailLabel'), ['class' => 'sr-only']) !!}
        {!! Form::email('email', null, ['id' => 'input-email', 'class' => 'form-control', 'placeholder' => trans('cms/forms.emailPlaceholder')]) !!}
        @if ($errors->has('email'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('password') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-password', trans('cms/forms.passwordLabel'), ['class' => 'sr-only']) !!}
        {!! Form::password('password', ['id' => 'input-password', 'class' => 'form-control', 'placeholder' => trans('cms/forms.passwordPlaceholder')]) !!}
        @if ($errors->has('password'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('password_confirmation') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-password_confirmation', trans('cms/forms.confirmPasswordLabel'), ['class' => 'sr-only']) !!}
        {!! Form::password('password_confirmation', ['id' => 'input-password_confirmation', 'class' => 'form-control', 'placeholder' => trans('cms/forms.confirmPasswordPlaceholder')]) !!}
        @if ($errors->has('password_confirmation'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    {!! Form::submit(trans('cms/forms.registerButton'), ['class' => 'btn btn-primary btn-block']) !!}
    {!! Form::close() !!}
@endsection
