<?php
$metaTitle = 'Login';
$metaDescription = 'Description';
?>

@extends('cms.auth.master')

@section('content')
<div class="login-wrapper">

    {!! HTML::image(\App\Helpers\autover('/img/cms/logo.png'), 'Logo') !!}

    <div class="login-box">
        <h1>Login</h1>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(['id' => 'login-form', 'class' => 'form-horizontal', 'role' => 'form']) !!}
        <div class="form-group">
            {!! Form::label('input-email', 'E-mail Address', ['class' => 'sr-only']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-email"></span></span>
                {!! Form::email('email', null, ['id' => 'input-email', 'class' => 'form-control', 'placeholder' => 'E-mail']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('input-password', 'Password', ['class' => 'sr-only']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-security"></span></span>
                {!! Form::password('password', ['id' => 'input-password', 'class' => 'form-control', 'placeholder' => 'Password']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox">
                {!! Form::checkbox('remember', 1, null, ['id' => 'input-remember']) !!}
                {!! Form::label('input-remember', 'Remember me') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::submit('Login', ['class' => 'btn btn-primary']) !!}
            <a class="btn btn-link" href="{{ url(\Locales::getLocalizedURL('pf')) }}">Forgot Your Password?</a>
        </div>
    {!! Form::close() !!}

    </div>

</div>
@endsection

@section('script')
ajax_submit('login-form', true);
@endsection
