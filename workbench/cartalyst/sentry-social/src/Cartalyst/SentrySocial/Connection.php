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

class Connection {

	protected $service;

	protected $name;

	protected $key;

	protected $secret;

	protected $scopes = array();

	protected $callback;

	/**
	 * Get the connection's service name.
	 *
	 * @return string
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * Get the connection's service name.
	 *
	 * @param  string  $service
	 * @return void
	 */
	public function setService($service)
	{
		$this->service = ucfirst($service);
	}

	/**
	 * Get the connection's display name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the connection's display name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get the connection's application key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get the connection's application key.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * Get the connection's application secret.
	 *
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * Get the connection's application secret.
	 *
	 * @param  string  $secret
	 * @return void
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}

	/**
	 * Get the connection's application scopes (only required
	 * for OAuth 2).
	 *
	 * @return array
	 */
	public function getScopes()
	{
		return $this->scopes;
	}

	/**
	 * Get the connection's application scopes (only required
	 * for OAuth 2).
	 *
	 * @param  array  $scopes
	 * @return void
	 */
	public function setScopes(array $scopes)
	{
		$this->scopes = $scopes;
	}

	/**
	 * Get the connection's callback URI.
	 *
	 * @return string
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Get the connection's callback URI.
	 *
	 * @param  string  $callback
	 * @return void
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

}
