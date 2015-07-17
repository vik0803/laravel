<?php
$metaTitle = 'Login';
$metaDescription = 'Login Description';
?>

@extends('cms.auth.master')

@section('content')
<div class="auth-wrapper">
    {!! HTML::image(\App\Helpers\autover('/img/cms/logo.png'), trans('cms/messages.altLogo')) !!}
    <div class="auth-box">
        <h1>{{ trans('cms/auth.signInTitle') }}</h1>

        @if ($errors->any())
        <div class="alert-messages">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                {!! trans('cms/js.ajaxErrorMessage') !!}
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><span class="glyphicon glyphicon-warning-sign"></span>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {!! Form::open(['id' => 'login-form', 'class' => 'ajax-lock', 'role' => 'form']) !!}

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

        <div class="form-group">
            {!! Form::checkboxInline('remember', 1, null, ['id' => 'input-remember'], trans('cms/forms.rememberOption'), ['class' => 'checkbox-inline']) !!}
        </div>

        {!! Form::submit(trans('cms/forms.signInButton'), ['class' => 'btn btn-primary btn-block']) !!}
        {!! Form::close() !!}
        <p>
            <a href="{{ url(\Locales::getLocalizedURL('pf')) }}">{{ trans('cms/auth.passwordForgottenHelpText') }}</a>
        </p>
    </div>
</div>
@endsection

@section('script')
unikat.ajax_submit('login-form');
@endsection
