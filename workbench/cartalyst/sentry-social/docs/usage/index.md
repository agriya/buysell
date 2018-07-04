### Using Sentry Social

Using Sentry Social is easy. Once you have configured it, you simply need to call the `make()` method to retrieve a "Service" instance. From there, you may request tokens, authenticate etc. Sounds complicated? It's not.

For the examples, we'll use the Laravel Facade, however the same methods may be called on a `$sentrySocial` variable, non-statically.

#### Making a Service

Making a service is very easy:

	// Make the "facebook" service
	$service = SentrySocial::make('facebook', 'http://url-which-you-would-like-to/redirect-do');

#### Using a Service

Once you have made a service, you need to redirect the user to the "authorization URI". This is the URI where they will login to their service (e.g. Facebook) and it will ask for permission to access their basic data.

	$service = SentrySocial::make('facebook', 'http://url-which-you-would-like-to/redirect-do');

	// Let's redirect the user to authorize with Facebook
	return Redirect::to((string) $service->getAuthorizationUri());

#### Authenticating the User

Once the user has authorized your application, they'll get redirected to the URL which you specified when you made the service, in this case `http://url-which-you-would-like-to/redirect-do`. We then want to use the `code` which has been passed back as `$_GET` parameter and authenticate the user. This is the magic which ties the service login with a Sentry login. It will create non-existent users and link existing ones. A user may be linked to multiple services as well:

	$service = SentrySocial::make('facebook', 'http://url-which-you-would-like-to/redirect-do');

	if ($code = Input::get('code'))
	{
		if ($user = SentrySocial::authenticate($service, $code))
		{
			var_dump($user);

			// Additionally, the user will be logged in, so this
			// is the same:
			// var_dump(Sentry::getUser());

			// Continue with your application's workflow, the user is logged in!
		}
	}

#### Laravel 4

In Laravel 4, we've added a controller which handles the registration flow for you. Feel free to use it, extend it or replace it, but it should get you started on your way to authenticating using Sentry Social.

	// To use it, in app/routes.php
	Route::controller('oauth', 'Cartalyst\SentrySocial\Controllers\OAuthController');

	// To extend it, make a class which extends Cartalyst\SentrySocial\Controllers\OAuthController
	Route::controller('oauth', 'MyOAuthController');
