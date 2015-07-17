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
