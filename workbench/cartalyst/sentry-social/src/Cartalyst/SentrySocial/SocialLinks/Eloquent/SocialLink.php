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

use Cartalyst\Sentry\Users\UserInterface;
use Cartalyst\SentrySocial\SocialLinks\LinkInterface;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model implements LinkInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'social';

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array();

	/**
	 * Returns the relationship to the user that this
	 * service belongs to.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'user_id');
	}

	/**
	 * Get the user associated with the social link.
	 *
	 * @return Cartalyst\Sentry\Users\UserInterface  $user
	 */
	public function getUser()
	{
		return $this->user()->getResults();
	}

	/**
	 * Set the user associated with the social link.
	 *
	 * @param  Cartalyst\Sentry\Users\UserInterface  $user
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user_id = $user->getId();
		$this->save();
	}

}
