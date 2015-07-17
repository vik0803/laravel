<?php
$metaTitle = 'Password Forgotten';
$metaDescription = 'Password Forgotten Description';
?>

@extends('cms.auth.master')

@section('content')
<div class="auth-wrapper">
    {!! HTML::image(\App\Helpers\autover('/img/cms/logo.png'), trans('cms/messages.altLogo')) !!}
    <div class="auth-box">
        <h1>{{ trans('cms/auth.resetPasswordTitle') }}</h1>

        @if (session('success'))
        <div class="alert-messages">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                <span class="glyphicon glyphicon-ok"></span>
                {{ session('success') }}
            </div>
        </div>
        @endif

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

        {!! Form::open(['id' => 'reset-form', 'class' => 'ajax-lock', 'role' => 'form']) !!}

        <div class="form-group{!! ($errors->has('email') ? ' has-error has-feedback' : '') !!}">
            {!! Form::label('input-email', trans('cms/forms.emailLabel'), ['class' => 'sr-only']) !!}
            {!! Form::email('email', null, ['id' => 'input-email', 'class' => 'form-control', 'placeholder' => trans('cms/forms.emailPlaceholder')]) !!}
            @if ($errors->has('email'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
        </div>

        {!! Form::submit(trans('cms/forms.sendPasswordReseLinktButton'), ['class' => 'btn btn-primary btn-block']) !!}
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('script')
unikat.ajax_submit('reset-form');
@endsection
