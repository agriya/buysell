### Installing in Laravel 4 (with Composer)

**There are three simple steps to install Sentry Social into Laravel 4:**

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

Add `Cartalyst\SentrySocial\SentrySocialServiceProvider` to the list of service providers in `app/config/app.php`.

> **Note:** If you are using our Themes 2 package, you should register Sentry Social **after** the `ThemeServiceProvider`.

##### Step 3  *(optional)*

Append the following to the list of class aliases in `app/config/app.php`:

	'SentrySocial' => 'Cartalyst\SentrySocial\Facades\Laravel\SentrySocial',
