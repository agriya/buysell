<?php

/*
|--------------------------------------------------------------------------
| Check project files installed
|--------------------------------------------------------------------------
|
| Check config/database.php, config/licence/credentials.php files exists.
| Also check basic folders and required settings exists.
| If updated, the project already installed
| If not updated, then redirect to install.php if exists
|
*/

	$project_installed = true;
    if (!is_dir(base_path() . '/app')
        || !file_exists(base_path() . '/app/config/database.php')
        || !is_dir(base_path() . '/bootstrap')
        || !is_dir(base_path() . '/public')
        || !is_dir(base_path() . '/vendor')
        || Config::get('database.connections.mysql.host') == 'VAR_LOCALHOST'
		|| Config::get('database.connections.mysql.database') == 'VAR_DATABASE'
		|| Config::get('database.connections.mysql.username') == 'VAR_LOGIN_USER'
		|| Config::get('database.connections.mysql.password') == 'VAR_PASSWORD') {

		$project_installed = false;
    }

	if (!$project_installed && file_exists(base_path() . '/install.php')) {
		header('Location: '.str_replace('/public', '', url('')).'/install.php');
		exit;
	} else {
		//die('Project installed already!');
	}

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);
/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

Validator::resolver(function($translator, $data, $rules, $messages)
{
	//todo rename to CustomValidator
  	return new UserAccountValidator($translator, $data, $rules, $messages);
});

//Added by mohamed_158at11
App::before(function($request)
{
	App::singleton('Header', function($app)
	{
    	return new Header();
	});
	$headerObj = App::make('Header');
	View::share('header', $headerObj);
});
//Added by mohamed_158at11

App::bind('PaymentInterface', function($app, $params)
{
	//Log::info('Log message:'.$params['payment_method']);
	if($params['payment_method'] == 'paypal') {
		//Log::info('Log message: I am in paypal interface');
		return new PaypalInterface();
	}
	//Log::info('Log message:'.$params['payment_method']);
	if($params['payment_method'] == 'sudopay') {
		//Log::info('Log message: I am in paypal interface');
		return new SudopayInterface();
	}
	if($params['payment_method'] == 'wallet') {
		//Log::info('Log message: I am in paypal interface');
		return new WalletInterface();
	}
	else {
		//Log::info('Log message: I am in dummy interface');
		return new DummyInterface();
	}
});


App::bind('FavoriteInterface', function($app, $params)
{
	if(isset($params['favorites']) && $params['favorites'] == 'shop') {
		return new ShopFavoritesService();
	}
	elseif(isset($params['favorites']) && $params['favorites'] == 'collection') {
		return new CollectionFavoritesService();
	}
	else {
		return new ProductFavoritesService();
	}
});


CUtil::getShippingCountry();

App::error(function(Exception $exception, $code)
{
    Log::error($exception);

    /*if (Config::get('app.debug')) {
    	return;
    }*/

    switch ($code)
    {
        case 403:
            return Response::view('error/404', array(), 403);

        case 500:
           return;// Response::view('error/500', array(), 500);

        default:
           return Response::view('error/404', array(), $code);
    }
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
require app_path().'/lib/CommonFunctions.php';
require app_path().'/lib/SolveMedia.php';

?>