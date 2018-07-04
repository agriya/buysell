<?php namespace Cartalyst\SentrySocial\Controllers;
/**
 * Part of the Sentry Social package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use App;
use Config;
use Exception;
use Illuminate\Routing\Controllers\Controller;
use Input;
use URL;
use Redirect;
use Sentry;
use SentrySocial;
use View;

class OAuthController extends Controller {

	/**
	 * Lists all available services to authenticate with.
	 *
	 * @return Illuminate\View\View
	 */
	public function getIndex()
	{
		$connections = array_filter(SentrySocial::getConnections(), function($connection)
		{
			return ($connection->getKey() and $connection->getSecret());
		});

		return View::make('cartalyst/sentry-social::oauth/index', compact('connections'));
	}

	/**
	 * Shows a link to authenticate a service.
	 *
	 * @param  string  $serviceName
	 * @return string
	 */
	public function getAuthorize($serviceName)
	{
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));

		return Redirect::to((string) $service->getAuthorizationUri());
	}

	/**
	 * Handles authentication
	 *
	 * @param  string  $serviceName
	 * @return mixed
	 */
	public function getCallback($serviceName)
	{
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));

		// If there is an error passed back from the OAuth service
		if ($error = Input::get('error'))
		{
			throw new Exception($error);
		}

		// If the user has denied access for the OAuth application
		if (Input::get('denied'))
		{
			throw new Exception("You have denied [$serviceName] access.");
		}

		// If we have an access code from an OAuth 2 service
		elseif ($code = Input::get('code'))
		{
			$access = $code;
		}

		// If we have request token and verifier from an OAuth 1 service
		elseif ($requestToken = Input::get('oauth_token'))
		{
			$access = array($requestToken, Input::get('oauth_verifier'));
		}

		// Otherwise, we'll abort now
		else App::abort(404);

		try
		{
			if (SentrySocial::authenticate($service, $access))
			{
				return Redirect::to('oauth/authenticated');
			}
		}
		catch (Exception $e)
		{
			return Redirect::to('oauth')->withErrors($e->getMessage());
		}
	}

	/**
	 * Returns the "authenticated" view which simply shows the
	 * authenticated user.
	 *
	 * @return mixed
	 */
	public function getAuthenticated()
	{
		if ( ! Sentry::check())
		{
			return Redirect::to('oauth')->withErrors('Not authenticated yet.');
		}

		$user = Sentry::getUser();

		return View::make('cartalyst/sentry-social::oauth/authenticated', compact('user'));
	}

}
