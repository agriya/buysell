<?php
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

return array(

	/*
	|--------------------------------------------------------------------------
	| Service Connections
	|--------------------------------------------------------------------------
	|
	| Here, you may specify any number of connection configurations your
	| application requires.
	|
	| Each connection must specify the following:
	| 1. "service" - a valid authentication type (optional). Valid types are:
	|    (OAuth 1): "fitbit", "twitter"
	|    (OAuth 2): "bitly", "facebook", "foursquare", "github", "google",
	|               "microsoft", "soundcloud", "yammer".
	|    If the "service" key is not specified, we will use the array key for
	|    the configuration to guess the type. This allows for convenience
	|    as well as multiple configurations with the same "service".
	|    We plan on adding support for more providers in the future.
	| 2. "name" - a human-friendly name (optional).
	| 3. "key" - your application's key.
	| 4. "secret" - your application's secret.
	|
	| OAuth2 providers can also provide the following:
	| 1. "scopes" - an array of scopes you are requesting access to (optional).
	|
	| All connections are optional, feel free to replace with
	| your own at will.
	|
	*/

	'connections' => array(

		'bitly' => array(
			'name'   => 'bitly',
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'facebook' => array(
			'name'   => 'Facebook',
			'key'    => '',
			'secret' => '',
			'scopes' => array('email'),
		),

		'fitbit' => array(
			'name'   => 'Fitbit',
			'key'    => '',
			'secret' => '',
		),

		'foursquare' => array(
			'name'   => 'Foursquare',
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'github' => array(
			'name'   => 'GitHub',
			'key'    => '',
			'secret' => '',
			'scopes' => array('user'),
		),

		'google' => array(
			'name'   => 'Google',
			'key'    => '',
			'secret' => '',
			'scopes' => array('userinfo_profile', 'userinfo_email'),
		),

		'microsoft' => array(
			'name'   => 'Microsoft',
			'key'    => '',
			'secret' => '',
			'scopes' => array('emails'),
		),

		'soundcloud' => array(
			'name'   => 'SoundCloud',
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'twitter' => array(
			'name'   => 'Twitter',
			'key'    => '',
			'secret' => '',
		),

		'yammer' => array(
			'name'   => 'Yammer',
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'vkontakte' => array(
			'name'   => 'Vkontakte',
			'key'    => '',
			'secret' => '',
			'scopes' => array(),
		),

		'linkedin' => array(
			'name'   => 'LinkedIn',
			'key'    => '',
			'secret' => '',
			'scopes' => array('r_fullprofile', 'r_emailaddress'),
		),

		/*
		// Example of using a different key to the "service"
		// "service" used. This allows for multiple
		// configurations for the one service. You would
		// call SentrySocial::make('secondary'); to make this
		// service.
		'secondary' => array(
			'service' => 'google',
			'name'    => 'Google 2',
			'key'     => '',
			'secret'  => '',
			'scopes'  => array('userinfo_email', 'userinfo_profile'),
		),
		*/

	),

	/*
	|--------------------------------------------------------------------------
	| Social Link Model
	|--------------------------------------------------------------------------
	|
	| When users are registered, a "social link provider" will map the social
	| authentications with user instances. Feel free to use your own model
	| with our provider.
	|
	|
	*/

	'link' => 'Cartalyst\SentrySocial\SocialLinks\Eloquent\SocialLink',

);
