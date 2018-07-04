<?php namespace Cartalyst\SentrySocial\Services\OAuth2;
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

use Cartalyst\SentrySocial\Services\ServiceInterface;
use OAuth\OAuth2\Service\Facebook as BaseService;

class Facebook extends BaseService implements ServiceInterface {

	/**
	 * THe service name.
	 *
	 * @var string
	 */
	protected $serviceName;

	/**
	 * Array of cached user info.
	 *
	 * @var array
	 */
	protected $cachedInfo = array();

	/**
	 * Gets the service name, or "alias".
	 *
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->serviceName;
	}

	/**
	 * Sets the service name, or "alias".
	 *
	 * @param  string  $serviceName
	 * @return void
	 */
	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
	}

	/**
	 * Returns the user's unique identifier on the service.
	 *
	 * @return mixed
	 */
	public function getUserUniqueIdentifier()
	{
		$info = $this->getUserInfo();
		//return (int) $info['id'];
		return $info['id'];
	}

	/**
	 * Returns the user's email address. Note, some services
	 * do not provide this in which case "null" is returned.
	 *
	 * @return string|null
	 */
	public function getUserEmail()
	{
		$info = $this->getUserInfo();

		// Facebook requires the "email" scope to access
		// the user's email address. This field therefore
		// may be present or may not.
		if (isset($info['email']))
		{
			return $info['email'];
		}

		return null;
	}

	/**
	 * Returns the user's name. If first / last name can be
	 * determined, an array is returned. If not, a string is
	 * returned. If it cannot be determined, "null" is returned.
	 *
	 * @return array|string|null
	 */
	public function getUserName()
	{
		$info = $this->getUserInfo();
		//\Log::info('User Info '.print_r($info, 1));
		$first_name = isset($info['first_name']) ? $info['first_name'] : null;
		$last_name = isset($info['last_name']) ? $info['last_name'] : null;
		if(!isset($info['first_name']) && isset($info['name']))
		{
			$namesArr = explode(" ", $info['name']);
			$first_name = isset($namesArr[0]) ? $namesArr[0] : '';
			$last_name = isset($namesArr[1]) ? $namesArr[1] : '';
		}
		return array($first_name, $last_name);
	}

	/**
	 * Retuns an array of basic user information.
	 *
	 * @return array
	 * @link   https://developers.facebook.com/docs/reference/api/user/
	 */
	public function getUserInfo()
	{
		if (empty($this->cachedInfo))
		{
			$this->cachedInfo = json_decode($this->request('me'), true);
		}

		return $this->cachedInfo;
	}

}
