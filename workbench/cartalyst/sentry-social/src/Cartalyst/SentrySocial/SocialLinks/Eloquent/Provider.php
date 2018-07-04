<?php namespace Cartalyst\SentrySocial\SocialLinks\Eloquent;
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

use Cartalyst\SentrySocial\SocialLinks\ProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent social model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\SentrySocial\SocialLinks\Eloquent\SocialLink';

	/**
	 * Create a new Eloquent Social Link provider.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Finds the a social link object by the service name
	 * and user's unique identifier.
	 *
	 * @param  string  $serviceName
	 * @param  string  $userUniqueIdentifier
	 * @return Cartalyst\SentrSocial\SocialLinks\LinkInterface
	 */
	public function findLink($serviceName, $userUniqueIdentifier)
	{
		$query = $this
			->createModel()
			->newQuery()
			->where('service', '=', $serviceName)
			->where('uid', '=', $userUniqueIdentifier);

		if ( ! $link = $query->first())
		{
			$link = $this->createModel();
			$link->service = $serviceName;
			$link->uid     = $userUniqueIdentifier;
			$link->save();
		}

		return $link;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
