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

use OAuth\Common\Service\ServiceInterface as BaseServiceInterface;

interface ServiceInterface extends BaseServiceInterface {

	/**
	 * Gets the service name, or "alias".
	 *
	 * @return string
	 */
	public function getServiceName();

	/**
	 * Sets the service name, or "alias".
	 *
	 * @param  string  $serviceName
	 * @return void
	 */
	public function setServiceName($serviceName);

	/**
	 * Returns the user's unique identifier on the service.
	 *
	 * @return mixed
	 */
	public function getUserUniqueIdentifier();

	/**
	 * Returns the user's email address. Note, some services
	 * do not provide this in which case "null" is returned.
	 *
	 * @return string|null
	 */
	public function getUserEmail();

	/**
	 * Returns the user's name. If first / last name can be
	 * determined, an array is returned. If not, a string is
	 * returned. If it cannot be determined, "null" is returned.
	 *
	 * @return array|string|null
	 */
	public function getUserName();

	/**
	 * Retuns an array of basic user information.
	 *
	 * @return array
	 */
	public function getUserInfo();

}
