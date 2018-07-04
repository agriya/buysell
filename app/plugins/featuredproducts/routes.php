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
		Route::controller('admin/featuredproducts', 'App\Plugins\FeaturedProducts\Controllers\AdminFeaturedProductsController');
	});
	Route::group(array('before' => 'sentry'), function() {
		Route::controller('featuredproducts', 'App\Plugins\FeaturedProducts\Controllers\FeaturedProductsController');
	});
	Route::controller('featured-product-expiry-cron', 'App\Plugins\FeaturedProducts\Controllers\FeaturedProductsExpiryCronController');
});
?>