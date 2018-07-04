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

//Route::get('variations', function() {
//    return '<h1>Welcome to variations modules</h1>';
//});
Route::group(array('before' => 'validate.license'), function()
{
	Route::controller('importer-cron', 'App\Plugins\Importer\Controllers\ImporterCronController');



	Route::group(array('before' => 'sentry'), function()
	{
		Route::any('importer/product-actions', 'App\Plugins\Importer\Controllers\ImporterController@anyUploadActions');
		Route::controller('importer', 'App\Plugins\Importer\Controllers\ImporterController');
	});

});
?>