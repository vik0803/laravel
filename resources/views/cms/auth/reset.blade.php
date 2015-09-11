<?php
$metaTitle = 'Reset Password';
$metaDescription = 'Reset Password Description';
?>

@extends('cms.auth.master')

@section('content')
    <h1>{{ trans('cms/auth.resetPasswordTitle') }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['id' => 'reset-form', 'class' => 'ajax-lock', 'role' => 'form', 'url' => url(\Locales::getLocalizedURL('reset'))]) !!}
    {!! Form::hidden('token', $token) !!}

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

    {!! Form::submit(trans('cms/forms.resetPasswordButton'), ['class' => 'btn btn-primary btn-block']) !!}
    {!! Form::close() !!}
@endsection

@section('script')
unikat.callback = function() {
    'use strict';

    unikat.ajax_submit('reset-form');
};
@endsection
