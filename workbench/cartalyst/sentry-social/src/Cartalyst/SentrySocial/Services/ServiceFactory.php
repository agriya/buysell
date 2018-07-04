<?php namespace Cartalyst\SentrySocial\Services;
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

use Cartalyst\SentrySocial\HttpClients\StreamClient as HttpStreamClient;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface as HttpClientInterface;
use OAuth\Common\Exception\Exception as OAuthException;
use OAuth\Common\Service\ServiceInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\OAuth1ServiceInterface;
use OAuth\OAuth1\Signature\Signature as OAuth1Signature;
use OAuth\OAuth2\Service\OAuth2ServiceInterface;

class ServiceFactory {

	/**
	 * The HTTP client we use to retrieve data.
	 *
	 * @var OAuth\Common\Http\Client\ClientInterface
	 */
	private $httpClient;

	/**
	 * An array of custom OAuth2 services.
	 *
	 * @var array
	 */
	protected $oauth2Services = array();

	/**
	 * An array of custom OAuth1 services.
	 *
	 * @var array
	 */
	protected $oauth1Services = array();

	/**
	 * Create a new service factory instance.
	 *
	 * @param  OAuth\Common\Http\Client\ClientInterface  $httpClient
	 * @return void
	 */
	public function __construct(HttpClientInterface $httpClient = null)
	{
		$this->httpClient = $httpClient ?: new HttpStreamClient;
	}

	/**
	 * @param $serviceName string name of service to create
	 * @param Common\Consumer\Credentials $credentials
	 * @param Common\Storage\TokenStorageInterface $storage
	 * @param array|null $scopes If creating an oauth2 service, array of scopes
	 * @return ServiceInterface
	 * @throws Common\Exception\Exception
	 */
	public function createService($serviceName, Credentials $credentials, TokenStorageInterface $storage, $scopes = array())
	{
		// Try an OAuth2 service first
		if ($className = $this->getOAuth2ClassName($serviceName))
		{
			// Resolve scopes from the service
			$resolvedScopes = array();
			$reflClass = new \ReflectionClass($className);
			$constants = $reflClass->getConstants();

			foreach ($scopes as $scope)
			{
				$key = strtoupper('SCOPE_'.$scope);

				if (array_key_exists($key, $constants))
				{
					$resolvedScopes[] = $constants[$key];
				}
				else
				{
					$resolvedScopes[] = $scope;
				}
			}

			$service = new $className($credentials, $this->httpClient, $storage, $resolvedScopes);
		}

		// Now, try an OAuth 1 service
		elseif ($className = $this->getOAuth1ClassName($serviceName))
		{
			if( ! empty($scopes))
			{
				throw new OAuthException('Scopes passed to ServiceFactory::createService but an OAuth1 service was requested.');
			}

			$signature = new OAuth1Signature($credentials);

			$service = new $className($credentials, $this->httpClient, $storage, $signature);
		}
		else
		{
			return;
		}

		$service->setServiceName($serviceName);
		return $service;
	}

	/**
	 * Register a custom OAuth2 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth2Service($className)
	{
		$this->oauth2Services[$this->getServiceName($className)] = $className;
	}

	/**
	 * Register a custom OAuth1 service with the Service Factory.
	 *
	 * @param  string  $className
	 * @return void
	 */
	public function registerOAuth1Service($className)
	{
		$this->oauth1Services[$this->getServiceName($className)] = $className;
	}

	/**
	 * Extracts the service name from the given class name.
	 *
	 * @param  string  $className
	 * @return string
	 */
	protected function getServiceName($className)
	{
		return basename(str_replace('\\', '/', $className));
	}

	/**
	 * Returns a potential classname for the given OAuth2
	 * service name.
	 *
	 * @param  string  $serviceName
	 * @return string
	 */
	protected function getOAuth2ClassName($serviceName)
	{
		if (isset($this->oauth2Services[$serviceName]))
		{
			return $this->oauth2Services[$serviceName];
		}

		$className = "\\Cartalyst\\SentrySocial\\Services\\OAuth2\\{$serviceName}";
		if (class_exists($className)) return $className;
	}

	/**
	 * Returns a potential classname for the given OAuth1
	 * service name.
	 *
	 * @param  string  $serviceName
	 * @return string
	 */
	protected function getOAuth1ClassName($serviceName)
	{
		if (isset($this->oauth1Services[$serviceName]))
		{
			return $this->oauth1Services[$serviceName];
		}

		$className = "\\Cartalyst\\SentrySocial\\Services\\OAuth1\\{$serviceName}";
		if (class_exists($className)) return $className;
	}

}
