<?php namespace Cartalyst\SentrySocial\SocialLinks;
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

interface ProviderInterface {

	/**
	 * Finds the a social link object by the service name
	 * and user's unique identifier.
	 *
	 * @param  string  $serviceName
	 * @param  string  $userUniqueIdentifier
	 * @return Cartalyst\SentrSocial\SocialLinks\LinkInterface
	 */
	public function findLink($serviceName, $userUniqueIdentifier);

}
