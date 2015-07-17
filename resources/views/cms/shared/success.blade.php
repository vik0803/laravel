@if (session('success'))
<div class="alert-messages">
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
        <span class="glyphicon glyphicon-ok"></span>
        {{ session('success') }}
    </div>
</div>
@endif
