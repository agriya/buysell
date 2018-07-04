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
	Route::any('deals-cron/update-tipped-deals', 'App\Plugins\Deals\Controllers\DealsCronController@fetchUpdateTippedDeals');
	Route::any('deals-cron/update-expired-deals', 'App\Plugins\Deals\Controllers\DealsCronController@fetchUpdateExpiredDeals');
	Route::group(array('before' => 'sentry.admin'), function()
	{
		Route::controller('admin/deals', 'App\Plugins\Deals\Controllers\AdminDealsController');
	});

	Route::any('deals/list/{type?}', 'App\Plugins\Deals\Controllers\DealsController@getDealsList');
	Route::any('deals/deal-items/{deal_id?}', 'App\Plugins\Deals\Controllers\DealsController@getDealsItemList');
	Route::controller('deals', 'App\Plugins\Deals\Controllers\DealsController');
	Route::get('deals', 'App\Plugins\Deals\Controllers\DealsController@getIndex');

	Route::group(array('before' => 'sentry'), function()
	{


	});
});
?>