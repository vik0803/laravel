<?php
$metaTitle = 'Password Forgotten';
$metaDescription = 'Password Forgotten Description';
?>

@extends('cms.auth.master')

@section('content')
    <h1>{{ trans('cms/auth.resetPasswordTitle') }}</h1>

    @include('cms/shared.success')

    @include('cms/shared.errors')

    {!! Form::open(['id' => 'reset-form', 'class' => 'ajax-lock', 'role' => 'form']) !!}

    <div class="form-group{!! ($errors->has('email') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-email', trans('cms/forms.emailLabel'), ['class' => 'sr-only']) !!}
        {!! Form::email('email', null, ['id' => 'input-email', 'class' => 'form-control', 'placeholder' => trans('cms/forms.emailPlaceholder')]) !!}
        @if ($errors->has('email'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    {!! Form::submit(trans('cms/forms.sendPasswordReseLinktButton'), ['class' => 'btn btn-primary btn-block']) !!}
    {!! Form::close() !!}
@endsection

@section('script')
unikat.ajax_submit('reset-form');
@endsection
