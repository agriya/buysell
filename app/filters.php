<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	// Our own method to defend XSS attacks globally.
	HomeCUtil::globalXssClean();
	//
	$language = Config::get('generalConfig.lang');
	if(Config::get("generalConfig.is_multi_lang_support")) {
		$lang_code = Config::get('generalConfig.site_cookie_prefix')."_selected_language";
		if(BasicCUtil::getCookie($lang_code) != "")
			$language = BasicCUtil::getCookie($lang_code);
	}
	if (is_dir(base_path() . '/app/lang/'.$language)
		&& file_exists(base_path() . '/app/lang/'.$language.'/common.php')) {
		App::setLocale($language);
	}
	else {
		App::setLocale('en');
	}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Event listener
|--------------------------------------------------------------------------
|
*/
$subscriber = new EventHandler;
Event::subscribe($subscriber);

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('sentry', function()
{
	if (!BasicCUtil::sentryCheck()) return Redirect::to('users/login');
});

Route::filter('sentry.admin', function()
{
	/*
	|--------------------------------------------------------------------------
	| To validate license key in admin pages
	|--------------------------------------------------------------------------
	|
	| This will validate given license key from customers agriya.
	|
	*/
	$ionoObj = new IonoLicenseHandler();
	$ionoObj->setConfigLicenseValues();
	$ionoObj->setLangLicenseValues();
	$err_msg = $ionoObj->ionLicenseHandler(Config::get('license/credentials.key'), 1);
	if($err_msg != '') { //Display error if Invalid license
		$ipaddresslist = Request::getClientIp();
		$ipaddress_arr = explode(",", $ipaddresslist);
		$remoteipaddress = $ipaddress_arr[0];
		$site_url = Url::to('');

		try {
			// Create the SMTP configuration
			$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 587, 'tls');
			$transport->setUsername("travelhub.ahsan@gmail.com");
			$transport->setPassword("ahsan.in");

			// Create the message
			$message = Swift_Message::newInstance();

			$message->setTo(array("buysell@agriya.in" => "Buysell Agriya"));
			//$message->setCc(array("s.sridharan@agriya.in" => "Sridharan"));
			$message->setSubject("[Buysell] - Buysell running without license in ". $site_url);
			$content = '
Hi Buysell Team,

Buysell running in '.$site_url.' on '.date("F j, Y, g:i a").' without valid permission.

Version: '.Config::get('version.version').'
SVN: '.Config::get('version.svn').'
License Key: '.Config::get('license/credentials.key').'
License HASH: '.Config::get('license/credentials.hash').'
Error: '.$err_msg.'
Server IP Address: '.$_SERVER['SERVER_ADDR'].'
IP ADDRESS: '.$remoteipaddress.'

Check the site '.$site_url.'
Request URL '.Request::url().'

Regards,
Buysell Development Team.
	';
			$message->setBody($content);
			$message->setFrom("noreply@buysell.com", "Buysell");

			// Send the email
			$mailer = Swift_Mailer::newInstance($transport);
			//$mailer->send($message, $failedRecipients);

		} catch (Exception $e) {
			//do nothing..
		}

		//return View::make('license', compact('err_msg'));
	}
	if (!BasicCUtil::sentryCheck()) return Redirect::to('users/login');
	if (!Sentry::getUser()->hasAnyAccess(['system', 'system.Admin'])) return Redirect::to('product');
});

/*
|--------------------------------------------------------------------------
| To validate license key in user pages
|--------------------------------------------------------------------------
|
| This will validate license with existing license key and verified hash srting.
|
*/

Route::filter('validate.license', function()
{
	$host = Request::server('HTTP_HOST');

	//To check whether the domain is agriya.com
	if(strstr($host, 'agriya.com')) {
	} else {
		if(strcasecmp('www.', substr($host ,0, 4)) == 0) {
			$host = substr($host, 4);
		}
		$host = strtolower($host);
		$str = Config::get('license/credentials.key').$host.'buysell';
		$valid_license = (Config::get('license/credentials.hash') == md5($str));
		if (!$valid_license) {  //Display error if Invalid license
			$err_msg = Lang::get('license/error.invalid_user');
			$ipaddresslist = Request::getClientIp();
			$ipaddress_arr = explode(",", $ipaddresslist);
			$remoteipaddress = $ipaddress_arr[0];
			$site_url = Url::to('');

			try {
				// Create the SMTP configuration
				$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 587, 'tls');
				$transport->setUsername("travelhub.ahsan@gmail.com");
				$transport->setPassword("ahsan.in");

				// Create the message
				$message = Swift_Message::newInstance();

				$message->setTo(array("buysell@agriya.in" => "Buysell Agriya"));
				//$message->setCc(array("s.sridharan@agriya.in" => "Sridharan"));
				$message->setSubject("[Buysell] - Buysell running without license in ". $site_url);
				$content = '
Hi Buysell Team,

Buysell running in '.$site_url.' on '.date("F j, Y, g:i a").' without valid permission.

Version: '.Config::get('version.version').'
SVN: '.Config::get('version.svn').'
License Key: '.Config::get('license/credentials.key').'
License HASH: '.Config::get('license/credentials.hash').'
Local HASH: '.md5($str).'
Host: '.$host.'
Server IP Address: '.$_SERVER['SERVER_ADDR'].'
IP ADDRESS: '.$remoteipaddress.'

Check the site '.$site_url.'
Request URL '.Request::url().'

Regards,
Buysell Development Team.
	';
				$message->setBody($content);
				$message->setFrom("noreply@buysell.com", "Buysell");

				// Send the email
				$mailer = Swift_Mailer::newInstance($transport);
				//$mailer->send($message, $failedRecipients);

			} catch (Exception $e) {
				// do nothing..
			}
			//return View::make('license', compact('err_msg'));
		}
	}
});

/*
|--------------------------------------------------------------------------
| Testing - To validate license key
|--------------------------------------------------------------------------
|
| This will validate given license key from customers agriya.
|
*/

Route::filter('test.license', function()
{
	$ionoObj = new IonoLicenseHandler();
	$ionoObj->setConfigLicenseValues();
	$ionoObj->setLangLicenseValues();
	$err_msg = $ionoObj->ionLicenseHandler(Config::get('license/credentials.key'), 1);
	if($err_msg != '') {  //Display error if Invalid license
		$ipaddresslist = Request::getClientIp();
		$ipaddress_arr = explode(",", $ipaddresslist);
		$remoteipaddress = $ipaddress_arr[0];
		$site_url = Url::to('');

		// Create the SMTP configuration
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 587, 'tls');
		$transport->setUsername("travelhub.ahsan@gmail.com");
		$transport->setPassword("ahsan.in");

		// Create the message
		$message = Swift_Message::newInstance();

		$message->setTo(array("buysell@agriya.in" => "Buysell Agriya"));
		//$message->setCc(array("s.sridharan@agriya.in" => "Sridharan"));
		$message->setSubject("[Buysell] - Buysell running without license in ". $site_url);
		$content = '
Hi Buysell Team,

Buysell running in '.$site_url.' on '.date("F j, Y, g:i a").' without valid permission.

Version: '.Config::get('version.version').'
SVN: '.Config::get('version.svn').'
License Key: '.Config::get('license/credentials.key').'
IP ADDRESS: '.$remoteipaddress.'

Check the site '.$site_url.'

Regards,
Buysell Development Team.
	';
		$message->setBody($content);
		$message->setFrom("noreply@buysell.com", "Buysell");

		// Send the email
		$mailer = Swift_Mailer::newInstance($transport);
		//$mailer->send($message, $failedRecipients);

		return View::make('license', compact('err_msg'));
	}
});

Route::filter('validate.seller', function()
{
	/*
	|--------------------------------------------------------------------------
	| To validate became a seller
	|--------------------------------------------------------------------------
	|
	| This will user became a seller
	|
	*/
	if(CUtil::isMember() && !BasicCUtil::isValidToAddProduct()){
		return Redirect::to('users/request-seller');
	}
});
if (is_dir(base_path() . '/app/plugins/variations')) {
	App::register('App\Plugins\Variations\ServiceProvider');
}
if (is_dir(base_path() . '/app/plugins/importer')) {
	App::register('App\Plugins\Importer\ServiceProvider');
}
if (is_dir(base_path() . '/app/plugins/deals')) {
	App::register('App\Plugins\Deals\ServiceProvider');
}
if (is_dir(base_path() . '/app/plugins/featuredproducts')) {
	App::register('App\Plugins\FeaturedProducts\ServiceProvider');
}
if (is_dir(base_path() . '/app/plugins/featuredsellers')) {
	App::register('App\Plugins\FeaturedSellers\ServiceProvider');
}
if (is_dir(base_path() . '/app/plugins/sudopay')) {
	App::register('App\Plugins\Sudopay\ServiceProvider');
}
?>