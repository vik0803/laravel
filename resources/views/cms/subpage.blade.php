@extends('cms.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Subpage</div>

				<div class="panel-body">{{ trans('cms/messages.test') }} - Subpage</div>
			</div>
		</div>
	</div>
</div>
@endsection
