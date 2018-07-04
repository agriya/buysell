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

class AdminFeaturedSellersController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->featured_sellers_service = new \FeaturedSellersService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		if(!\CUtil::chkIsAllowedModule('featuredsellers'))
		{
			return Redirect::to('/admin');
		}
	}

	public function getManageFeaturedSellersPlans()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0) {
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = Lang::get('featuredsellers::featuredsellers.add_featured_sellers_plan');
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['featured_sellers_plans'] = array();
		}
		else {
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = Lang::get('featuredsellers::featuredsellers.edit_featured_sellers_plan');
			$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
			$d_arr['featured_sellers_plans'] 	= $this->featured_sellers_service->getFeaturedSellersPlanSettings($id);
		}
		$d_arr['id'] = $id;
		$d_arr['status_arr'] = array('Active' => trans('common.active'), 'Inactive'=> trans('common.inactive'));

		$perPage    					= Config::get('featuredsellers::featuredsellers.featured_sellers_list_per_row');
		$q 								= $this->featured_sellers_service->buildFeaturedSellersPlanQuery();
		$details 						= $q->paginate($perPage);
		//echo '<pre>';print_r($d_arr['featured_sellers_plans']);echo '</pre>';
		return View::make('featuredsellers::admin.featuredSellersPlanSettings', compact('details', 'd_arr'));
	}

	public function postManageFeaturedSellersPlans()
	{
		$input = Input::All();
		$messages = array();

		$rules['featured_days'] = 'required|numeric|Min:0';
		$rules['featured_price'] = 'required|numeric|Min:0|IsValidPrice';

		$messages += array('featured_days.required' => Lang::get('featuredsellers::featuredsellers.days_required'),
							'featured_days.numeric' => Lang::get('featuredsellers::featuredsellers.days_numeric'),
							'featured_days.min' => Lang::get('featuredsellers::featuredsellers.days_not_less_than_one'),
							'featured_price.required' => Lang::get('featuredsellers::featuredsellers.price_required'),
							'featured_price.numeric' => Lang::get('featuredsellers::featuredsellers.price_numeric'),
							'featured_price.min' => Lang::get('featuredsellers::featuredsellers.price_not_less_than_one'),
							'featured_price.is_valid_price' => Lang::get('featuredsellers::featuredsellers.enter_valid_price')
							);

		$validator = Validator::make($input, $rules, $messages);
		if (!$validator->passes()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}

		$json_res = $this->featured_sellers_service->updateFeaturedSellersPlan($input);
		$json_data = json_decode($json_res , true);
		if(isset($json_data['status']) && $json_data['status'] == 'error')
		{
			return Redirect::back()->withInput()->withErrors($validator)->with('success_message',$json_data['error_message']);
		}
		else{
			if($input['feature_id'] == 0) {
				return Redirect::to('admin/featuredsellers/manage-featured-sellers-plans')->with('success_message',Lang::get('featuredsellers::featuredsellers.featured_sellers_plan_added_success'));
			}
			else {
				return Redirect::to('admin/featuredsellers/manage-featured-sellers-plans')->with('success_message',Lang::get('featuredsellers::featuredsellers.featured_sellers_plan_updated_success'));
			}
		}
	}

	public function getChangePlanStatus()
	{
		$action='';
		if(Input::has('feature_id') && Input::has('feature_id')) {
			$feature_id = Input::get('feature_id');
			$action = Input::get('action');
			$success_msg = "";
			$success_msg = $this->featured_sellers_service->updateFeaturedSellersPlanStatus($feature_id, $action);
		}
		Session::flash('success', $success_msg);
		return Redirect::to('admin/featuredsellers/manage-featured-sellers-plans');
	}

	/**
	 * To list featured sellers
	 * AdminFeaturedSellersController::getFeaturedSellers()
	 *
	 * @return
	 */
	public function getManageFeaturedSellers()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = trans('featuredsellers::featuredsellers.featured_sellers');
		$d_arr['allow_to_change_status'] = true;
		$user_list = $user_details = array();
		$shop_action = array('' => trans('common.select_option'), 'deactivate' => trans('admin/manageMembers.deactivate_shop'), 'activate' => trans('admin/manageMembers.activate_shop'));

		$is_shop_owner =  array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$is_allowed_to_add_product = array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$status = array('' => trans('common.select_option'), 'blocked' => Lang::get('common.blocked'), 'active' => Lang::get('common.active'), 'inactive' => Lang::get('common.inactive'));
		$shop_status = array('' => trans('common.select_option'), 'active' => Lang::get('common.active'), 'inactive' => Lang::get('common.inactive'), 'inactive' => Lang::get('common.inactive'));

		$this->featured_sellers_service->setFeaturedSellersFilterArr();
		$this->featured_sellers_service->setFeaturedSellersSrchArr(Input::All());

		$q = $this->featured_sellers_service->buildFeaturedSellersQuery();

		$page 		= (Input::has('page')) ? Input::get('page') : 1;
		$start 		= (Input::has('start')) ? Input::get('start') : Config::get('featuredsellers::featuredsellers.featured_sellers_list_per_row');
		$perPage	= Config::get('featuredsellers::featuredsellers.featured_sellers_list_per_row');
		$user_list 	= $q->paginate($perPage);

		///Get all group details
		$group_details = array();
		$groups = \Sentry::findAllGroups();
		if(count($groups) > 0) {
			foreach($groups as $key => $values) {
				$group_details[$values->id] = $values->name;
			}
		}

		//$this->header->setMetaTitle(trans('meta.admin_manage_shops_title'));
		return View::make('featuredsellers::admin.featuredSellers', compact('d_arr', 'user_list', 'shop_action', 'is_shop_owner', 'is_allowed_to_add_product', 'status', 'shop_status'));
	}

	public function postSellersAction()
	{
		$error_msg = trans('featuredsellers::featuredsellers.invalid_action');
		$sucess_msg = '';
		//echo "product_action==>".Input::get('product_action')."p_id  =====>".Input::get('p_id');die;
		if(Input::has('seller_action') && Input::has('seller_id'))
		{
			$seller_id = Input::get('seller_id');
			$seller_action = Input::get('seller_action');

			//Validate seller id
			$user_details = \User::Select('is_featured_seller', 'featured_seller_expires')->where('id', '=', $seller_id)->first();
			if(count($user_details) > 0) {
				switch($seller_action)	{
					# Remove featured
					case 'unfeature':
						# Product featured status is changed
						if($user_details['is_featured_seller'] == 'Yes') {
							$error_msg = '';
							$status = $this->featured_sellers_service->changeFeaturedStatus($seller_id, 'No');
							# Display success msg
							if($status) {
								$sucess_msg = trans('featuredsellers::featuredsellers.sellers_unfeatured_success_msg');
							}
							else {
								$error_msg = trans('featuredsellers::featuredsellers.sellers_error_on_action');
							}
						}
						break;

				}
			}
		}
		if($sucess_msg != '') {

			return Redirect::to('admin/featuredsellers/manage-featured-sellers')->with('success_message', $sucess_msg);
		}
		return Redirect::to('admin/featuredsellers/manage-featured-sellers')->with('error_message', $error_msg);
	}
}