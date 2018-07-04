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
use Cartalyst\SentrySocial\SocialLinks\Eloquent\Provider as SocialLinkProvider;
class OAuthController extends Controller {

	/**
	 * Lists all available services to authenticate with.
	 *
	 * @return Illuminate\View\View
	 */
	public function getIndex()
	{
		//Log::info('came to getindex page');
		$connections = array_filter(SentrySocial::getConnections(), function($connection)
		{
			return ($connection->getKey() and $connection->getSecret());
		});
		return View::make('oauth/index', compact('connections'));
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
		if($serviceName == 'facebook')
		{
			$service_url = $service->getAuthorizationUri().'&display=popup';
			return Redirect::to((string) $service_url);
		}
		else
		{
		  	return Redirect::to((string) $service->getAuthorizationUri());
		}
	}

	public function getPopupAuthorize($serviceName)
	{
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));
		if($serviceName == 'facebook')
		{
			$service_url = $service->getAuthorizationUri();
			return Redirect::to((string) $service_url);
		}
		else
		{
		  	return Redirect::to((string) $service->getAuthorizationUri());
		}
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
			echo '<script>window.close();</script>';
			//throw new Exception($error);
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

			//Log::info('-------------------------------facebook response and process-------------------------------');
			// If we have no user associated with the link, we'll register one now
			if ( $temp_user_id = SentrySocial::chkSentryUserExist($service, $access))
			{
				//Log::info('temp_user_id: '.$temp_user_id);
				$user_id = DB::table('social')->whereRaw('uid = ?', array($temp_user_id))->pluck('user_id');
				//Log::info('user_id: '.$user_id);
				if(!$user_id || is_null($user_id))
				{
					//Log::info('authenticate  123 is over: ');
					if ($attributes = SentrySocial::authenticateOnly($service, $access))
					{
						// Handle first name empty checking.
						if(!isset($attributes["first_name"]))
						{
							$first_name = '';
							if(isset($attributes["email"]) && $attributes["email"] != '')
							{
								$parts = explode('@', $attributes["email"]);
								$first_name = isset($parts[0]) ? $parts[0] : '';
							}
							$attributes['first_name'] = $first_name;
						}
						$attributes['last_name'] = isset($attributes['last_name']) ? $attributes['last_name'] : '';
						$attributes['service_name'] = $serviceName;
						$attributes['access'] = $access;
						$attributes['entry_type'] = 'new';

						Session::put('attributes',$attributes);

						$this->userService = new UserAccountService();
						if($this->userService->checkEmailAlreadyExists($attributes['email'])) {
							$attributes['entry_type'] = 'map';
						}

						Session::put('attributes',$attributes);
						//echo "oauth attributes: <pre>";print_r($attributes);echo "</pre>";exit;
						return Redirect::to('oauth/externalsignup');//->with('attributes', $attributes);
						//return Redirect::to('oauth/authenticated');
					}
				}
				else
				{
					//Log::info('going to authenticate is over: ');
					if (SentrySocial::authenticate($service, $access))
					{
						//Log::info('authenticate is over: ');
						return Redirect::to('oauth/authenticated');
					}
					//Log::info('not redirected. not authenticated ');
				}
			}
			else
			{

				if (SentrySocial::authenticate($service, $access))
				{
					return Redirect::to('oauth/authenticated');
				}
			}
		}
		catch (Exception $e)
		{
			//Log::info('authenticate error : Msg =>'.$e->getMessage());
			return Redirect::to('oauth')->withErrors($e->getMessage());
		}
	}

	public function getExternalsignup()
	{
		$external_attributes = Session::get('attributes');
		return View::make('oauth/externalsignup', compact('external_attributes'));
	}

	/**
	 * Returns the "authenticated" view which simply shows the
	 * authenticated user.
	 *
	 * @return mixed
	 */
	public function getAuthenticated()
	{
		//Log::info('came to authenticated');
		if ( ! BasicCUtil::sentryCheck())
		{
			//Log::info('authenticated sentry check failed');
			return Redirect::to('oauth')->withErrors('Not authenticated yet.');
		}
		$user = Sentry::getUser();
		//Log::info('authenticated sentry success');
		//Log::info(print_r($user,1));
		if($user->id != '')
		{
				$user_code = User::where('id', $user->id)->pluck('id');
				if($user_code == '')
				{
					User::where('id', $user->id)->update(array(//'user_code' => CUtil::generateRandomUniqueCode('', 'users', 'user_code'),
																'last_login' => date('Y-m-d H:i:s'),
																'signup_ip'	  => $_SERVER['REMOTE_ADDR']
																));
					$array_multi_key = array('featured_seller_banner_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
				}
				else
				{
					User::where('id', $user->id)->update(array('last_login' => date('Y-m-d H:i:s')));
				}
		}
		return View::make('oauth/authenticated', compact('user'));
	}

}