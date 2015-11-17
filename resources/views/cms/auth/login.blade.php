@extends('cms.auth.master')

@section('content')
    <h1>{{ trans('cms/auth.signInTitle') }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['id' => 'login-form', 'class' => 'ajax-lock', 'data-ajax-queue' => 'sync', 'role' => 'form']) !!}

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
        <a href="{{ \Locales::route('pf') }}">{{ trans('cms/auth.passwordForgottenHelpText') }}</a>
    </p>
@endsection
