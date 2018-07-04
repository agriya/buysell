<?php

/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */

Route::group(array('before' => 'validate.license'), function()
{
	Route::group(array('before' => 'sentry.admin'), function() {
		Route::controller('admin/featuredsellers', 'App\Plugins\FeaturedSellers\Controllers\AdminFeaturedSellersController');
	});
	Route::group(array('before' => 'sentry'), function() {
		Route::controller('featuredsellers', 'App\Plugins\FeaturedSellers\Controllers\FeaturedSellersController');
	});
	Route::controller('featured-seller-expiry-cron', 'App\Plugins\FeaturedSellers\Controllers\FeaturedSellersExpiryCronController');
});
?>