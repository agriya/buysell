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
class ShipmentsController extends BaseController
{
	public function getIndex()
	{
		try
		{
			$countries = array('' => trans('common.select_a_country'));
			$countries_arr = Webshopshipments::getCountriesList('list');
			$countries = $countries+$countries_arr;

			$inputs = Input::all();
			if(isset($inputs['foreign_id']) && $inputs['foreign_id'] > 0 && isset($inputs['country']) && $inputs['country'] > 0)
			{
				$shipping_fee_list = Webshopshipments::getShippingDetails(array('foreign_id'=>$inputs['foreign_id'], 'country_id' =>$inputs['country']));
			}
			elseif(isset($inputs['foreign_id']) && $inputs['foreign_id'] > 0)
				$shipping_fee_list = Webshopshipments::getItemShippingList($inputs['foreign_id']);
			else
				$shipping_fee_list = array();

			$foreign_ids = array_combine(range(1,10),range(1,10));

			return View::make('shipments', compact('countries', 'shipping_fee_list', 'foreign_ids'));
		}
		catch(Exception $e)
		{
			echo "some problem: ".$e->getMessage();
		}

	}
	public function postIndex()
	{
		$params	 = Input::all();
		$request = Request::create('shipments', 'GET', $params);
		return Route::dispatch($request)->getContent();
	}
	public function postAddShipment()
	{
		$inputs = Input::all();
		try
		{
			$shipment_id = Webshopshipments::addShipments(array('country_id' => $inputs['country'], 'shipping_fee' => $inputs['fee'], 'foreign_id' => $inputs['foreign_id']));
			return Redirect::action('ShipmentsController@getIndex')->with('success_message',trans('shippingTemplates.shipping_details_added_success'));
		}
		catch(Exception $e)
		{
			return Redirect::action('ShipmentsController@getIndex')->withInput()->with('error_message',$e->getMessage());
		}
	}
	public function postUpdate()
	{
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		try
		{	//'country_id' => $inputs['country_id'],'foreign_id' => $inputs['foreign_id'],
			$shipment_id = Webshopshipments::updateShippingFee(array('shipping_fee' => $inputs['shipping_fee']), $inputs['primary']);
			return Redirect::action('ShipmentsController@getIndex')->with('success_message',trans('shippingTemplates.shipping_details_update_success'));
		}
		catch(Exception $e)
		{
			return Redirect::action('ShipmentsController@getIndex')->withInput()->with('error_message',$e->getMessage());
		}
	}
	public function postDelete()
	{
		$inputs = Input::all();
		try
		{
			$shipment_id = Webshopshipments::deleteShippingFee(array('country_id' => $inputs['country_id'], 'foreign_id' => $inputs['foreign_id']), $inputs['primary']);
			return Redirect::action('ShipmentsController@getIndex')->with('success_message', trans('shippingTemplates.shipping_details_deleted_successfully'));
		}
		catch(Exception $e)
		{
			return Redirect::action('ShipmentsController@getIndex')->withInput()->with('error_message',$e->getMessage());
		}
	}
	public function showWelcome()
	{
		return View::make('hello');
	}
}