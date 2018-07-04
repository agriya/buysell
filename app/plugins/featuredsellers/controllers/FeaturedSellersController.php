<?php namespace App\Plugins\FeaturedSellers\Controllers;
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

class FeaturedSellersController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->featured_sellers_service = new \FeaturedSellersService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		if(!\CUtil::chkIsAllowedModule('featuredsellers'))
		{
			return Redirect::to('/');
		}
	}

	public function getUserinfo($user_id = 0)
	{
		$udetails = $user_destination = $company_info = array();
		$udetails = \User::where('id', $user_id)->where('activated', 1)->where('is_banned', 0)->where('shop_status', 1)->first();
		//$user_image = UserImage::where('user_id', $user_id)->first();
		//$udetails['user_image'] = $user_image;
		return $udetails;
	}

	/**
	 * FeaturedSellersController::getSetAsFeatured()
	 *
	 * @return
	 */
	public function getSetAsFeatured()
	{
		$d_arr = $plans_arr = array();
		$seller_id = Input::has('id') ? Input::get('id') : 0;
		$logged_user_id = $this->logged_user_id;
		$usr_details = $this->getUserinfo($seller_id);
		if(count($usr_details) > 0) {
			if(\CUtil::isAdmin() OR ($logged_user_id == $seller_id)) {
				if($usr_details['is_shop_owner'] == 'Yes') {
					if($usr_details['is_featured_seller'] == 'No') {
						$d_arr['seller_id'] = $seller_id;
						$d_arr['user_account_balance'] = \CUtil::getUserAccountBalance($seller_id);
						$plans_arr = $this->featured_sellers_service->getFeaturedSellersPlans();
						$d_arr['plans_arr'] = array('' => trans('common.select')) + $plans_arr;
					}
					else {
						$d_arr['error_msg'] = trans('featuredsellers::featuredsellers.seller_already_featured');
					}
				}
				else {
					$d_arr['error_msg'] = trans('featuredsellers::featuredsellers.shop_settings_required');
				}
			}
			else {
				$d_arr['error_msg'] = trans('featuredsellers::featuredsellers.invalid_action');
			}
		}
		else {
			$d_arr['error_msg'] = trans('featuredsellers::featuredsellers.user_or_shop_should_be_active');
		}
		return View::make('featuredsellers::setAsFeaturedSellers', compact('d_arr'));
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
		$seller_id = Input::get('seller_id');
		$logged_user_id = $this->logged_user_id;
		$usr_details = $this->getUserinfo($seller_id);
		if(count($usr_details) > 0) {
			if(\CUtil::isAdmin() OR ($logged_user_id == $seller_id)) {
				if($usr_details['is_shop_owner'] == 'Yes') {
					if($usr_details['is_featured_seller'] == 'No') {
						$v = Validator::make(Input::all(), $rules, $messages);
						if ( $v->passes()) {
							$input = Input::all();
							$plan_details = $this->featured_sellers_service->getFeaturedSellersPlanSettings($input['plan']);
							if(count($plan_details) > 0) {
								$plan_details = (array) $plan_details;
								$this->featured_sellers_service->updateFeaturedSellerExpiryDate($usr_details, $plan_details);
								$this->featured_sellers_service->setFeaturedSellerTransaction($usr_details, $plan_details);
								$d_arr['success_message'] = trans('featuredsellers::featuredsellers.seller_featured_success_msg');
								return View::make('featuredsellers::setAsFeaturedSellers', compact('d_arr'));
							}
							else {
								$error_msg = trans('featuredsellers::featuredsellers.plan_not_exist');
							}
						}
						else {
							return Redirect::to('featuredsellers/set-as-featured?id='.$seller_id)->withInput()->withErrors($v);
						}
					}
					else {
						$error_msg = trans('featuredsellers::featuredsellers.seller_already_featured');
					}
				}
				else {
					$error_msg = trans('featuredsellers::featuredsellers.shop_settings_required');
				}
			}
			else {
				$error_msg = trans('featuredsellers::featuredsellers.invalid_action');
			}
		}
		else {
			$error_msg = trans('featuredsellers::featuredsellers.user_or_shop_should_be_active');
		}
		if($error_msg != ''){
			$d_arr['error_msg'] = $error_msg;
			return View::make('featuredsellers::setAsFeaturedSellers', compact('d_arr'));
		}
	}

	/**
	 * MessageAddController::postSetAsFeatured()
	 *
	 * @return
	 */
	public function postSetAsFeaturedBlock()
	{
		$messages = $d_arr = array();
		$messages['plan.required'] = trans('common.required');
		$rules = array('plan' => 'Required');
		$seller_id = Input::get('seller_id');
		$logged_user_id = $this->logged_user_id;
		$usr_details = $this->getUserinfo($seller_id);
		if(count($usr_details) > 0) {
			if(\CUtil::isAdmin() OR ($logged_user_id == $seller_id)) {
				if($usr_details['is_shop_owner'] == 'Yes') {
					if($usr_details['is_featured_seller'] == 'No') {
						$v = Validator::make(Input::all(), $rules, $messages);
						if ( $v->passes()) {
							$input = Input::all();
							$plan_details = $this->featured_sellers_service->getFeaturedSellersPlanSettings($input['plan']);
							if(count($plan_details) > 0) {
								$plan_details = (array) $plan_details;
								$this->featured_sellers_service->updateFeaturedSellerExpiryDate($usr_details, $plan_details);
								$this->featured_sellers_service->setFeaturedSellerTransaction($usr_details, $plan_details);
								$success_message = trans('featuredsellers::featuredsellers.seller_featured_success_msg');
								return Redirect::to('shop/users/shop-details')->with('success_message', $success_message);
							}
							else {
								$error_msg = trans('featuredsellers::featuredsellers.plan_not_exist');
							}
						}
						else {
							$error_msg = trans('featuredsellers::featuredsellers.pls_chk_error');
							return Redirect::to('shop/users/shop-details')->withInput()->withErrors($v)->with('error_msg', $error_msg);
						}
					}
					else {
						$error_msg = trans('featuredsellers::featuredsellers.seller_already_featured');
					}
				}
				else {
					$error_msg = trans('featuredsellers::featuredsellers.shop_settings_required');
				}
			}
			else {
				$error_msg = trans('featuredsellers::featuredsellers.invalid_action');
			}
		}
		else {
			$error_msg = trans('featuredsellers::featuredsellers.user_or_shop_should_be_active');
		}
		if($error_msg != ''){
			return Redirect::to('shop/users/shop-details')->with('error_msg', $error_msg);
		}
	}
}