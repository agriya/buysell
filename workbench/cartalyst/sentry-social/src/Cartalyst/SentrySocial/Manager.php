<?php namespace Cartalyst\SentrySocial;
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

use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\SentrySocial\SocialLinks\Eloquent\Provider as SocialLinkProvider;
use Cartalyst\SentrySocial\SocialLinks\ProviderInterface as SocialLinkProviderInterface;
use Cartalyst\SentrySocial\Services\ServiceInterface;
use Cartalyst\SentrySocial\Services\ServiceFactory;
use Illuminate\Database\Eloquent\Model;
use OAuth\Common\Consumer\Credentials;

class Manager {

	/**
	 * The Sentry instance.
	 *
	 * @var Cartalyst\Sentry\Sentry
	 */
	protected $sentry;

	/**
	 * The social link provider, used for tying logins
	 * to Sentry logins.
	 *
	 * @var Cartalyst\SentrySocial\SocialLinks\ProviderInterface
	 */
	protected $socialLinkProvider;

	/**
	 * The Service Factory, used for creating
	 * service instances.
	 *
	 * @var Cartalyst\SentrySocial\ServiceFactory
	 */
	protected $serviceFactory;

	/**
	 * Array of registered connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Create a new Sentry Social manager.
	 *
	 * @param  Cartalyst\SentrySocial\SocialLinks\ProviderInterface  $socialLinkProvider
	 * @param  Cartalyst\Sentry\ServiceFactory  $serviceFactory
	 * @param  array  $connections
	 * @return void
	 */
	public function __construct(Sentry $sentry, SocialLinkProviderInterface $socialLinkProvider = null, ServiceFactory $serviceFactory = null, array $connections = array())
	{
		$this->sentry             = $sentry;
		$this->socialLinkProvider = $socialLinkProvider ?: new SocialLinkProvider;
		$this->serviceFactory     = $serviceFactory ?: new ServiceFactory;

		foreach ($connections as $name => $connection)
		{
			$this->register($name, $connection);
		}
	}

	/**
	 * Registers a connection with the manager.
	 *
	 * @param  string  $slug
	 * @param  array   $attributes
	 * @return Cartalyst\SentrySocial\Connection  $connection
	 */
	public function register($slug, array $attributes)
	{
		$connection = $this->createConnection($slug, $attributes);

		return $this->connections[$slug] = $connection;
	}

	/**
	 * Register a custom OAuth2 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth2Service($className)
	{
		$this->serviceFactory->registerOAuth2Service($className);
	}

	/**
	 * Register a custom OAuth1 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth1Service($className)
	{
		$this->serviceFactory->registerOAuth1Service($className);
	}

	/**
	 * Makes a new service from a connection with the given slug.
	 *
	 * @param  string  $slug
	 * @param  string  $callback
	 * @return Cartalyst\SentrySocial\Services\ServiceInterface
	 */
	public function make($slug, $callback = null)
	{
		$connection  = $this->getConnection($slug, $callback);
		$credentials = $this->createCredentials($connection->getKey(), $connection->getSecret(), $callback);
		$storage     = $this->createStorage($service = $connection->getService());
		return $this->serviceFactory->createService($service, $credentials, $storage, $connection->getScopes());
	}


	public function authenticateOnly(ServiceInterface $service, $access, $remember = false)
	{
		$attributes = array();
		$this->sentry->logout();

		$serviceName = $service->getServiceName();

		$uid         = $service->getUserUniqueIdentifier();
		$temp_uid = 0;
		if($uid > 0)
			$temp_uid = $uid;

		$link = $this->socialLinkProvider->findLink($serviceName, $temp_uid);
		$user = $link->getUser();
		sleep(1);
		//\Log::info('========authenticateOnly=========');
		//\Log::info(print_r($user));
		// If we have no user associated with the link, we'll register one now
		if ( ! $user = $link->getUser())
		{
			$provider = $this->sentry->getUserProvider();
			$login    = $service->getUserEmail() ?: "{$uid}@{$serviceName}";

			// Lazily create a user
			try
			{
				$user = $provider->findByLogin($login);

				// Some providers give a first / last name, some don't.
				// If we only have one name, we'll just put it in the
				// "first_name" attribute.
				if (is_array($name = $service->getUserName()))
				{
					$attributes['first_name'] = $name[0];
					$attributes['last_name']  = $name[1];
				}
				elseif (is_string($name))
				{
					$attributes['first_name'] = $name;
				}
				$email = $service->getUserEmail();
				$attributes['email'] = isset($email) ? $email : '';
				return $attributes;
			}
			catch (UserNotFoundException $e)
			{
				$emptyUser = $provider->getEmptyUser();

				// Create a dummy password for the user
				$passwordParams = array($serviceName, $uid, $login, time(), mt_rand());
				shuffle($passwordParams);

				// Setup an array of attributes we'll add onto
				// so we can create our user.
				$attributes = array(
					$emptyUser->getLoginName()    => $login,
					$emptyUser->getPasswordName() => implode('', $passwordParams),
				);

				// Some providers give a first / last name, some don't.
				// If we only have one name, we'll just put it in the
				// "first_name" attribute.
				if (is_array($name = $service->getUserName()))
				{
					$attributes['first_name'] = $name[0];
					$attributes['last_name']  = $name[1];
				}
				elseif (is_string($name))
				{
					$attributes['first_name'] = $name;
				}
				$email = $service->getUserEmail();
				$attributes['email'] = isset($email) ? $email : '';
				return $attributes;
			}
		}
		return $attributes;
	}

	public function addNewSentryUser(ServiceInterface $service, $access, $custom_attributes, $remember = false)
	{
		$this->sentry->logout();
		$serviceName = $service->getServiceName();
		$uid         = $service->getUserUniqueIdentifier();
		$link = $this->socialLinkProvider->findLink($serviceName, $uid);

		if ( ! $user = $link->getUser())
		{

			$provider = $this->sentry->getUserProvider();
			$login    = $service->getUserEmail() ?: "{$uid}@{$serviceName}";


			// Lazily create a user
			try
			{
				$user = $provider->findByLogin($login);
			}
			catch (UserNotFoundException $e)
			{
				$emptyUser = $provider->getEmptyUser();

				// Create a dummy password for the user
				$passwordParams = array($serviceName, $uid, $login, time(), mt_rand());
				shuffle($passwordParams);

				// Setup an array of attributes we'll add onto
				// so we can create our user.
				$attributes = array(
					$emptyUser->getLoginName()    => $login,
					$emptyUser->getPasswordName() => implode('', $passwordParams),
				);

				$attributes['first_name'] = $custom_attributes['first_name'];
				$attributes['last_name'] = $custom_attributes['last_name'];
				$attributes['user_name'] = $custom_attributes['user_name'];
				$attributes['email'] = $custom_attributes['email'];
				//$attributes['phone'] = isset($custom_attributes['contact_no'])? $custom_attributes['contact_no']:'';
				$user = $provider->create($attributes);
				$user->attemptActivation($user->getActivationCode());
			}

			$link->setUser($user);
		}
		$throttleProvider = $this->sentry->getThrottleProvider();
		// Now, we'll check throttling to ensure we're
		// not logging in a user which shouldn't be allowed.
		if ($throttleProvider->isEnabled())
		{
			$throttle = $throttleProvider->findByUserId(
				$user->getId(),
				$this->sentry->getIpAddress()
			);
			$throttle->check();
		}
		$this->sentry->login($user, $remember);
		return $user->id;
	}

	public function chkSentryUserExist(ServiceInterface $service, $access)
	{
		$this->sentry->logout();
		// OAuth 1
		if (is_array($access))
		{
			if (count($access) != 2)
			{
				throw new \RuntimeException("If access is an array, we are dealing with OAuth 1 where the first parameter is the request token and the second parameter is the request verifier.");
			}

			list($requestToken, $requestVerifier) = $access;

			$token = $service->getStorage()->retrieveAccessToken('Twitter');

			$service->requestAccessToken(
				$requestToken,
				$requestVerifier,
				$token->getRequestTokenSecret()
			);
		}
		// OAuth 2
		else
		{
			$service->requestAccessToken($access);
		}
		$serviceName = $service->getServiceName();
		$uid         = $service->getUserUniqueIdentifier();
		$temp_uid = 0;
		if($uid > 0)
			$temp_uid = $uid;
		//$user_id = DB::table('social')->whereRaw('uid', $temp_uid)->pluck('user_id');
		//$link = $this->socialLinkProvider->findLink($serviceName, $temp_uid);
		//$user = $link->getUser();
		return $temp_uid;
	}
	/**
	 * Authenticates the given Sentry Social OAuth service.
	 *
	 * @param  Cartalyst\SentrySocial\Services\ServiceInterface  $service
	 * @param  string  $access
	 * @param  bool    $remember
	 * @return Cartalyst\Sentry\Users\UserInterface  $user
	 * @todo   Add a "email_changed_from_social" field to `users` and update
	 *         email address if different when authenticating??
	 */
	public function authenticate(ServiceInterface $service, $access, $remember = false)
	{

		\Log::info('========================this sentry social manager authenticate()======================');

		$this->sentry->logout();
		/*
		// OAuth 1
		 if (is_array($access))
		{
			if (count($access) != 2)
			{
				throw new \RuntimeException("If access is an array, we are dealing with OAuth 1 where the first parameter is the request token and the second parameter is the request verifier.");
			}

			list($requestToken, $requestVerifier) = $access;

			$token = $service->getStorage()->retrieveAccessToken();

			$service->requestAccessToken(
				$requestToken,
				$requestVerifier,
				$token->getRequestTokenSecret()
			);
		}

		// OAuth 2
		else
		{
			$service->requestAccessToken($access);
		}
		*/
		$serviceName = $service->getServiceName();

		$uid         = $service->getUserUniqueIdentifier();

		//\Log::infoinfo("going to take the user from identifier");
		//\Log::info(print_r($uid,1));

		$temp_uid = 0;
		if($uid > 0)
		{
			$temp_uid = $uid;
		}

		$link = $this->socialLinkProvider->findLink($serviceName, $temp_uid);


		// If we have no user associated with the link, we'll register one now
		if ( ! $user = $link->getUser())
		{
			//\Log::info("================user not found  maanager in get user=======================");
			//\Log::info(print_r($user,1));
			$provider = $this->sentry->getUserProvider();
			$login    = $service->getUserEmail() ?: "{$uid}@{$serviceName}";

			//\Log::info("================going to check with login email=======================");
			//\Log::info($login);
			// Lazily create a user
			try
			{
				$user = $provider->findByLogin($login);

				//\Log::info("================user found by login  maanager in get user=======================");
				//\Log::info(print_r($user,1));
			}
			catch (UserNotFoundException $e)
			{
				//\Log::info("================user not found by login also  maanager in get user=======================");

				$emptyUser = $provider->getEmptyUser();

				//\Log::info("================empty user now  maanager in get user=======================");
				//\Log::info(print_r($emptyUser,1));

				// Create a dummy password for the user
				$passwordParams = array($serviceName, $uid, $login, time(), mt_rand());
				shuffle($passwordParams);

				// Setup an array of attributes we'll add onto
				// so we can create our user.
				$attributes = array(
					$emptyUser->getLoginName()    => $login,
					$emptyUser->getPasswordName() => implode('', $passwordParams),
				);

				// Some providers give a first / last name, some don't.
				// If we only have one name, we'll just put it in the
				// "first_name" attribute.
				if (is_array($name = $service->getUserName()))
				{
					$attributes['first_name'] = $name[0];
					$attributes['last_name']  = $name[1];
				}
				elseif (is_string($name))
				{
					$attributes['first_name'] = $name;
				}

				$user = $provider->create($attributes);


				//\Log::info("================final user when user not found  maanager in get user=======================");
				//\Log::info(print_r($user,1));

				$user->attemptActivation($user->getActivationCode());
			}
			//\Log::info("================final user after try catch when user not found  maanager in get user=======================");
			//\Log::info(print_r($user,1));

			$link->setUser($user);
		}

		//\Log::info("================Final user foudn details   maanager in get user=======================");
			//\Log::info(print_r($user,1));
		$throttleProvider = $this->sentry->getThrottleProvider();

		// Now, we'll check throttling to ensure we're
		// not logging in a user which shouldn't be allowed.
		if ($throttleProvider->isEnabled())
		{
			$throttle = $throttleProvider->findByUserId(
				$user->getId(),
				$this->sentry->getIpAddress()
			);

			$throttle->check();
		}

		$this->sentry->login($user, $remember);
		//\Log::info('========================authenticate from sentry social manager authenticate()======================');
		//\Log::info(print_r($user,1));
		return $user;
	}

	/**
	 * Get the registered connections.
	 *
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connections;
	}

	/**
	 * Gets a connection registered with the manager with the given slug.
	 * The callback URI can also can be overridden at runtime.
	 *
	 * @param  string|array  $name
	 * @param  string  $callback
	 * @return array
	 */
	protected function getConnection($slug, $callback = null)
	{
		// If our connection is already an array,
		// the developer is creating a connection
		// on the fly, without registering it.
		if (is_array($slug))
		{
			$connection = $this->createConnection($slug);
		}

		// Otherwise, we will retrieve it from the array
		// of registered connections.
		else
		{
			if ( ! isset($this->connections[$slug]))
			{
				throw new \RuntimeException("Cannot make connection [$slug] as it has not been registered.");
			}

			$connection = $this->connections[$slug];
		}

		// If a runtime callback has been passed, override the connection with it.
		if (isset($callback))
		{
			$connection->setCallback($callback);
		}

		return $connection;
	}

	/**
	 * Creates a connection from the given slug and attributes.
	 *
	 * @param  string  $slug
	 * @param  array   $attributes
	 * @return Cartalyst\SentrySocial\Connection  $connection
	 */
	protected function createConnection($slug, array $attributes)
	{
		$connection = new Connection;
		$connection->setService(isset($attributes['service']) ? $attributes['service'] : $slug);
		$connection->setName(isset($attributes['name']) ? $attributes['name'] : $connection->getService());
		$connection->setKey($attributes['key']);
		$connection->setSecret($attributes['secret']);

		if (isset($attributes['scopes']))
		{
			$connection->setScopes($attributes['scopes']);
		}

		if (isset($attributes['callback']))
		{
			$connection->setCallback($callback);
		}

		return $connection;
	}

	/**
	 * Creates a Credentials object from the given
	 * application key, secret and callback URL.
	 *
	 * @param  string  $key
	 * @param  string  $secret
	 * @param  string  $callback
	 * @return void
	 */
	protected function createCredentials($key, $secret, $callback)
	{
		return new Credentials($key, $secret, $callback);
	}

	/**
	 * Creates a storage driver for the given service name.
	 *
	 *
	 */
	protected function createStorage($serviceName)
	{
		return new \OAuth\Common\Storage\Session(true, 'oauth_token_'.$serviceName);
	}

}
