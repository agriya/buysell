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

class AdminFeaturedProductsController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->featured_product_service = new \FeaturedProductsService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		if(!\CUtil::chkIsAllowedModule('featuredproducts'))
		{
			return Redirect::to('/admin');
		}
	}

	public function getManageFeaturedProductPlans()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0) {
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = Lang::get('featuredproducts::featuredproducts.add_featured_product_plan');
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['featured_prod_plans'] = array();
		}
		else {
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = Lang::get('featuredproducts::featuredproducts.edit_featured_product_plan');
			$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
			$d_arr['featured_prod_plans'] 	= $this->featured_product_service->getFeaturedProductsPlanSettings($id);
		}
		$d_arr['id'] = $id;
		$d_arr['status_arr'] = array('Active' => trans('common.active'), 'Inactive'=> trans('common.inactive'));

		$perPage    					= 10;
		$q 								= $this->featured_product_service->buildFeaturedProductsPlanQuery();
		$details 						= $q->paginate($perPage);
		//echo '<pre>';print_r($d_arr['featured_prod_plans']);echo '</pre>';
		return View::make('featuredproducts::admin.featuredProductsPlanSettings', compact('details', 'd_arr'));
	}

	public function postManageFeaturedProductPlans()
	{
		$input = Input::All();
		$messages = array();

		$rules['featured_days'] = 'required|numeric|Min:0';
		$rules['featured_price'] = 'required|numeric|Min:0|IsValidPrice';

		$messages += array('featured_days.required' => Lang::get('featuredproducts::featuredproducts.days_required'),
							'featured_days.numeric' => Lang::get('featuredproducts::featuredproducts.days_numeric'),
							'featured_days.min' => Lang::get('featuredproducts::featuredproducts.days_not_less_than_one'),
							'featured_price.required' => Lang::get('featuredproducts::featuredproducts.price_required'),
							'featured_price.numeric' => Lang::get('featuredproducts::featuredproducts.price_numeric'),
							'featured_price.min' => Lang::get('featuredproducts::featuredproducts.price_not_less_than_one'),
							'featured_price.is_valid_price' => Lang::get('featuredproducts::featuredproducts.enter_valid_price')
							);

		$validator = Validator::make($input, $rules, $messages);
		if (!$validator->passes()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}

		$json_res = $this->featured_product_service->updateFeaturedProductsPlan($input);
		$json_data = json_decode($json_res , true);
		if(isset($json_data['status']) && $json_data['status'] == 'error')
		{
			return Redirect::back()->withInput()->withErrors($validator)->with('success_message',$json_data['error_message']);
		}
		else{
			if($input['feature_id'] == 0) {
				return Redirect::to('admin/featuredproducts/manage-featured-product-plans')->with('success_message',Lang::get('featuredproducts::featuredproducts.featured_prod_plan_added_success'));
			}
			else {
				return Redirect::to('admin/featuredproducts/manage-featured-product-plans')->with('success_message',Lang::get('featuredproducts::featuredproducts.featured_prod_plan_updated_success'));
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
			$success_msg = $this->featured_product_service->updateFeaturedProductsPlanStatus($feature_id, $action);
		}
		Session::flash('success', $success_msg);
		return Redirect::to('admin/featuredproducts/manage-featured-product-plans');
	}

	public function getManageFeaturedProducts()
	{
		$prod_list_service = new \AdminProductListService();
		$prod_service = new \ProductService();
		$prod_obj = Products::initialize();

		$d_arr = $products_arr = array();
		$error_msg = '';
		$per_page	= Config::get('webshoppack.shop_product_per_page_list');

		$d_arr['allow_to_change_status'] = true;
		$d_arr['product_list_title'] = trans('featuredproducts::featuredproducts.featured_product');
		$d_arr['pageTitle'] = Lang::get('featuredproducts::featuredproducts.featured_products');
		//$d_arr['category_arr'] =  $prod_list_service->getCategoryDropOptions();
		//$d_arr['feature_arr'] =  $prod_list_service->getFeatureStatusDropOptions();
		$d_arr['status_arr'] =  $prod_list_service->getProductStatusDropOptions();
		$prod_list_service->setProductsSearchArr(Input::all());
		$prod_list_service->buildProductsQuery($prod_obj);
		$prod_obj->setIncludeBlockedUserProducts(true);
		$prod_obj->setFilterFeaturedProduct('Yes');
		$prod_obj->setOrderByField('id');
		$prod_obj->setProductPagination($per_page);
		$products_arr = $prod_obj->getProductsList(0, false);

		return View::make('featuredproducts::admin.featuredProducts', compact('d_arr', 'products_arr', 'prod_service', 'prod_list_service'));
	}

	public function postProductAction()
	{
		$error_msg = trans('featuredproducts::featuredproducts.product_invalid_action');
		$sucess_msg = '';
		//echo "product_action==>".Input::get('product_action')."p_id  =====>".Input::get('p_id');die;
		if(Input::has('product_action') && Input::has('p_id'))
		{
			$p_id = Input::get('p_id');
			$product_action = Input::get('product_action');

			//Validate product id
			$product = Products::initialize($p_id);
			$p_details = $product->getProductDetails(0, false);
			if(count($p_details) > 0) {
				switch($product_action)	{
					# Set featured
					case 'feature':
						# Product featured status is changed
						if($p_details['product_status'] == 'Ok' && $p_details['is_featured_product'] == 'No') {
							$error_msg = '';
							$status = $this->featured_product_service->changeFeaturedStatus($p_id, 'Yes');
							# Display success msg
							\Cache::flush(); 	// Remove the existing catche for reflect the change.
							if($status) {
								$sucess_msg = trans('featuredproducts::featuredproducts.product_featured_success_msg');
							}
							else {
								$error_msg = trans('featuredproducts::featuredproducts.product_error_on_action');
							}
						}
						break;

					# Remove featured
					case 'unfeature':
						# Product featured status is changed
						if($p_details['product_status'] == 'Ok' && $p_details['is_featured_product'] == 'Yes') {
							$error_msg = '';
							$status = $this->featured_product_service->changeFeaturedStatus($p_id, 'No');
							\Cache::flush(); 	// Remove the existing catche for reflect the change.
							# Display success msg
							if($status) {
								$sucess_msg = trans('featuredproducts::featuredproducts.product_unfeatured_success_msg');
							}
							else {
								$error_msg = trans('featuredproducts::featuredproducts.product_error_on_action');
							}
						}
						break;
				}
			}
		}
		if($sucess_msg != '') {
			return Redirect::to('admin/featuredproducts/manage-featured-products')->with('success_message', $sucess_msg);
		}
		return Redirect::to('admin/featuredproducts/manage-featured-products')->with('error_message', $error_msg);
	}
}