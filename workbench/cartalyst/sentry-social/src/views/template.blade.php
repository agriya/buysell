<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title')</title>

		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet" media="screen">

		@yield('styles')

	</head>
	<body>

		<div class="container">
			<div class="page-header">
				<h1>Social Authentication</h1>
			</div>
		</div>

		@if ($errors->count())
			<div class="container">
				@foreach ($errors->all(':message') as $error)
					<div class="alert alert-error">
						{{ $error }}
					</div>
				@endforeach
			</div>
		@endif

		@yield('content')

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script>

		@yield('scripts')

	</body>
</html>
