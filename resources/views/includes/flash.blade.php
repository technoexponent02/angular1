@if(session('flash_notification.message'))
	<div style="margin-left: 0px;margin-top: 30px">
		<div class="alert {{ session('flash_notification.level') }}" role="alert">
			<button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
			<p>{{ session('flash_notification.message') }}</p>
		</div>
	</div>
@endif

@if(Session::has('status'))
	<div style="margin-left: 0px;margin-top: 30px">
		<div class="alert alert-success" role="alert">
			<button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
			<p>{{ session('status') }}</p>
		</div>
	</div>
@endif

