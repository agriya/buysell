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
Route::get('variations', 'App\Plugins\Variations\Controllers\VariationsController@getIndex');
Route::get('variations/add-variation/{variation_id?}', 'App\Plugins\Variations\Controllers\VariationsController@getAddVariations');
Route::post('variations/add-variation/{variation_id?}', 'App\Plugins\Variations\Controllers\VariationsController@postAddVariations');
Route::post('variations/variations-list-action', 'App\Plugins\Variations\Controllers\VariationsController@postVariationsListAction');
Route::get('variations/variations-action', 'App\Plugins\Variations\Controllers\VariationsController@getVariationsAction');

Route::get('variations/groups', 'App\Plugins\Variations\Controllers\VariationsGroupController@getIndex');
Route::get('variations/add-group/{variation_group_id?}', 'App\Plugins\Variations\Controllers\VariationsGroupController@getAddVariationsGroup');
Route::post('variations/add-group/{variation_group_id?}', 'App\Plugins\Variations\Controllers\VariationsGroupController@postAddVariationsGroup');
Route::post('variations/group-list-action', 'App\Plugins\Variations\Controllers\VariationsGroupController@postGroupListAction');
Route::get('variations/group-action', 'App\Plugins\Variations\Controllers\VariationsGroupController@getGroupAction');
Route::get('variations/vartion-stock/{product_id?}', 'App\Plugins\Variations\Controllers\VariationsController@getVartionStock');

?>