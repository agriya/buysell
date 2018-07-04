<?php namespace App\Plugins\FeaturedProducts\Controllers;
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
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Products, Config;
use Session, Redirect, BaseController;

class FeaturedProductsController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->featured_product_service = new \FeaturedProductsService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		if(!\CUtil::chkIsAllowedModule('featuredproducts'))
		{
			return Redirect::to('/');
		}
	}

	/**
	 * FeaturedProductsController::getSetAsFeatured()
	 *
	 * @return
	 */
	public function getSetAsFeatured()
	{
		$product_id = Input::has('id') ? Input::get('id') : 0;
		$d_arr = $plans_arr = array();

		$product = Products::initialize($product_id);
		$p_details = $product->getProductDetails();
		if(count($p_details) > 0) {
			if(\CUtil::isAdmin() OR ($this->logged_user_id == $p_details['product_user_id'])) {
				if($p_details['product_status'] == 'Ok' && $p_details['is_featured_product'] == 'No') {
					$d_arr['product_id'] = $product_id;
					$d_arr['seller_id'] = $p_details['product_user_id'];
					$d_arr['user_account_balance'] = \CUtil::getUserAccountBalance($p_details['product_user_id']);
					$plans_arr = $this->featured_product_service->getFeaturedProductsPlans();
					$d_arr['plans_arr'] = array('' => trans('common.select')) + $plans_arr;
				}
				else {
					$d_arr['error_msg'] = trans('common.invalid_action');
				}
			}
			else {
				$d_arr['error_msg'] = trans('common.invalid_action');
			}
		}
		else {
			$d_arr['error_msg'] = trans('featuredproducts::featuredproducts.invalid_product');
		}
		return View::make('featuredproducts::setAsFeaturedProduct', compact('d_arr'));
	}

	/**
	 * MessageAddController::postSetAsFeatured()
	 *
	 * @return
	 */
	public function postSetAsFeatured()
	{
		$messages = $d_arr = array();
		$messages['plan.required'] = trans('common.required');
		$rules = array('plan' => 'Required');
		$product_id = Input::get('product_id');
		$product = Products::initialize($product_id);
		$p_details = $product->getProductDetails();
		if(count($p_details) > 0) {
			if(\CUtil::isAdmin() OR ($this->logged_user_id == $p_details['product_user_id'])) {
				if($p_details['product_status'] == 'Ok' && $p_details['is_featured_product'] == 'No') {
					$v = Validator::make(Input::all(), $rules, $messages);
					if ( $v->passes()) {
						$input = Input::all();
						$plan_details = $this->featured_product_service->getFeaturedProductsPlanSettings($input['plan']);
						if(count($plan_details) > 0) {
							$plan_details = (array) $plan_details;
							$user_account_balance = \CUtil::getUserAccountBalance($p_details['product_user_id']);
							if($user_account_balance['amount'] > $plan_details['featured_price']) {
								$this->featured_product_service->updateFeaturedProdcutExpiryDate($p_details, $plan_details);
								$this->featured_product_service->setFeaturedProdcutTransaction($p_details, $plan_details);
								$d_arr['success_message'] = trans('featuredproducts::featuredproducts.product_featured_success_msg'); 
								return View::make('featuredproducts::setAsFeaturedProduct', compact('d_arr'));
							}
							else {
								$error_msg = trans('featuredproducts::featuredproducts.insufficient_wallet_balance'); 
							}
						}
						else {
							$error_msg = trans('featuredproducts::featuredproducts.invalid_plan');
						}
					}
					else {
						return Redirect::to('featuredproducts/set-as-featured?id='.$product_id)->withInput()->withErrors($v);
					}
				}
				else {
					$error_msg = trans('common.invalid_action');
				}
			}
			else {
				$error_msg = trans('common.invalid_action');
			}
		}
		else {
			$error_msg = trans('featuredproducts::featuredproducts.invalid_product');
		}
		if($error_msg != ''){
			$d_arr['error_msg'] = $error_msg; 
			return View::make('featuredproducts::setAsFeaturedProduct', compact('d_arr'));
		}
	}
}