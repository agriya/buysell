### Installing Everywhere Else

##### Step 1

Ensure your `composer.json` file has the following structure (that you have the `repositories` and the `require` entry):

	{
		"repositories": [
			{
				"type": "composer",
				"url": "http://packages.cartalyst.com"
			}
		],
		"require": {
			"cartalyst/sentry-social": "2.0.*"
		}
	}

You may need to add a `"minimum-stability": "dev"` flag if it doesn't already exist until `cartalyst/sentry-social` has been marked as stable. A default Laravel 4 installation has this already as Laravel 4 isn't marked as stable yet.

##### Step 2

Create an instance of Sentry Social:

	$sentrySocial = new Cartalyst\SentrySocial\SentrySocial($instanceOfSentry);
	
	// In FuelPHP / CodeIgniter
	$sentrySocial = new Cartalyst\SentrySocial\SentrySocial(Sentry::instance());

This instance will need to be shared / passed around, until we provide Facades for these frameworks (which will be coming in the **very** short-term).
