@extends('cartalyst/sentry-social::template')
@section('content')
<div class="container">
	@if (count($connections) > 0)
    	<!-- LOGIN INDEX STARTS -->
		<ul class="row list-unstyled">
			@foreach ($connections as $connection)
				<li class="col-md-6">
					<figure>
						<img src="http://placehold.it/400x180" alt="placehold">
						<figcaption>
							<a href="{{ URL::to('oauth/authorize/'.$connection->getService()) }}" class="btn btn-link">
								Login with {{ $connection->getName() }}
							</a>
						</figcaption>
					</figure>
				</li>
			@endforeach
		</ul>
        <!-- LOGIN INDEX END -->
	@else
    	<!-- INFO STARTS -->
    	<div class="alert alert-danger">
            <h3>Snap! No connections yet</h3>
            <p>Try configuring a service and reloading this page.</p>
        </div>
        <!-- INFO END -->
	@endif
</div>
@endsection
