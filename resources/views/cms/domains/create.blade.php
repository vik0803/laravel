@extends('cms.master')

@section('content')
<div class="magnific-popup">
    <h1>{{ \Locales::getMetaTitle() }}</h1>

    @include('cms/shared.errors')

    @if (isset($domain))
    {!! Form::model($domain, ['method' => 'put', 'url' => \Locales::route('settings/domains/update'), 'id' => 'edit-domain-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @else
    {!! Form::open(['url' => \Locales::route('settings/domains/store'), 'id' => 'create-domain-form', 'data-ajax-queue' => 'sync', 'class' => 'ajax-lock', 'role' => 'form']) !!}
    @endif

    {!! Form::hidden('table', $table, ['id' => 'input-table']) !!}

    <div class="form-group{!! ($errors->has('name') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-name', trans('cms/forms.nameLabel')) !!}
        {!! Form::text('name', null, ['id' => 'input-name', 'class' => 'form-control', 'placeholder' => trans('cms/forms.namePlaceholder')]) !!}
        @if ($errors->has('name'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('slug') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-slug', trans('cms/forms.slugLabel')) !!}
        {!! Form::text('slug', null, ['id' => 'input-slug', 'class' => 'form-control', 'placeholder' => trans('cms/forms.slugPlaceholder')]) !!}
        @if ($errors->has('slug'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('locales') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-locales', trans('cms/forms.localesLabel')) !!}
        {!! Form::multiselect('locales[]', $multiselect['locales'], ['id' => 'input-locales', 'class' => 'form-control']) !!}
        @if ($errors->has('locales'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    <div class="form-group{!! ($errors->has('default_locale_id') ? ' has-error has-feedback' : '') !!}">
        {!! Form::label('input-default_locale_id', trans('cms/forms.defaultLocaleLabel')) !!}
        @if (count($multiselect['default_locale_id']['options']))
            {!! Form::multiselect('default_locale_id', $multiselect['default_locale_id'], ['id' => 'input-default_locale_id', 'class' => 'form-control']) !!}
        @else
            {!! Form::multiselect('default_locale_id', $multiselect['default_locale_id'], ['id' => 'input-default_locale_id', 'class' => 'form-control', 'disabled' => 'disabled']) !!}
        @endif
        @if ($errors->has('default_locale_id'))<span class="glyphicon glyphicon-remove form-control-feedback"></span>@endif
    </div>

    @if (isset($domain))
    {!! Form::submit(trans('cms/forms.updateButton'), ['class' => 'btn btn-warning btn-block']) !!}
    @else
    {!! Form::submit(trans('cms/forms.storeButton'), ['class' => 'btn btn-primary btn-block']) !!}
    @endif

    {!! Form::close() !!}

    @if (\Request::ajax())<script>@endif
    @section('script')
        unikat.magnificPopupCreateCallback = function() {
            $('#input-locales').multiselect({
                close: function(event, ui) {
                    var select = $('#input-default_locale_id');
                    var value = select.val();
                    var values = $(this).val();

                    var options = $('option', $(this)).map(function() {
                        if ($.inArray(this.value, values) !== -1) {
                            $(this).removeAttr('aria-selected').removeAttr('checked');
                            return this;
                        }

                        return null;
                    });

                    select.html(options.clone()).val(value).multiselect('refresh');

                    if (values) {
                        select.multiselect('enable');
                    } else {
                        select.multiselect('disable');
                    }
                },
            });
            $('#input-default_locale_id').multiselect({
                multiple: false,
            });
        };

    @if (\Request::ajax())
        @show
        </script>
    @else
        @if (isset($domain))
            unikat.magnificPopupEditCallback();
        @else
            unikat.magnificPopupCreateCallback();
        @endif
    @endsection
    @endif
</div>
@endsection
