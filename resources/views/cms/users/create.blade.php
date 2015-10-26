@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ trans('cms/users.createTitle') }}</h1>

    @include('cms/shared.errors')

    {!! Form::open(['id' => 'create-user-form', 'class' => 'ajax-lock', 'role' => 'form']) !!}

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

    {!! Form::submit(trans('cms/forms.saveButton'), ['class' => 'btn btn-primary btn-block']) !!}
    {!! Form::close() !!}
</div>
@endsection
