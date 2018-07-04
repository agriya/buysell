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

use Cartalyst\SentrySocial\SocialLinks\Eloquent\Provider as SocialLinkProvider;
use Cartalyst\SentrySocial\Services\ServiceFactory;

class SentrySocialServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cartalyst/sentry-social', 'cartalyst/sentry-social');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerServiceFactory();

		$this->registerSocialLinkProvider();

		$this->registerSentrySocial();
	}

	/**
	 * Registers the OAuth service factory.
	 *
	 * @return void
	 */
	protected function registerServiceFactory()
	{
		$this->app['sentry.social.factory'] = $this->app->share(function($app)
		{
			return new ServiceFactory;
		});
	}

	protected function registerSocialLinkProvider()
	{
		$this->app['sentry.social.link'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry-social::link'];

			return new SocialLinkProvider($model);
		});
	}

	/**
	 * Registers Sentry Social.
	 *
	 * @return void
	 */
	protected function registerSentrySocial()
	{
		$this->app['sentry.social'] = $this->app->share(function($app)
		{
			$connections = $app['config']['cartalyst/sentry-social::connections'];

			return new Manager(
				$app['sentry'],
				$app['sentry.social.link'],
				$app['sentry.social.factory'],
				$connections
			);
		});
	}

}
