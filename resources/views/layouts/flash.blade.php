@if ($flash = session('message'))
	<div id='flash-message' class='alert alert-success text-center' role="alert">

	    {{ $flash }}
	
	</div>
@endif	