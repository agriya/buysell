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
class AdminProductListController extends BaseController {

	public function getIndex()
	{
		$this->service = new AdminProductListService();
		$prod_obj = Products::initialize();

		$d_arr = $products_arr = array();
		$error_msg = '';
		$per_page	= Config::get('webshoppack.shop_product_per_page_list');

		$d_arr['allow_to_change_status'] = true;
		$d_arr['product_list_title'] =  trans('admin/productList.product_list_title');
		$d_arr['category_arr'] =  $this->service->getCategoryDropOptions();
		$d_arr['feature_arr'] =  $this->service->getFeatureStatusDropOptions();
		$d_arr['status_arr'] =  $this->service->getProductStatusDropOptions();
		$this->service->setProductsSearchArr(Input::all());
		$this->service->buildProductsQuery($prod_obj);
		$prod_obj->setProductPagination($per_page);
		$prod_obj->setIncludeDeleted(true);
		$prod_obj->setIncludeBlockedUserProducts(true);
		$prod_obj->setOrderByField('id');
		$products_arr = $prod_obj->getProductsList(0, false);

		$service_obj = $this->service;

		$this->header->setMetaTitle(trans('meta.admin_manage_products_title'));
		return View::make('admin.productList', compact('d_arr', 'products_arr', 'service_obj'));
	}

	public function postProductAction()
	{
		$this->service = new AdminProductListService();

		$error_msg = trans('admin/productList.product_invalid_action');
		$sucess_msg = '';
		if(Input::has('product_action') && Input::has('p_id'))
		{
			$p_id = Input::get('p_id');
			$product_action = Input::get('product_action');

			//Validate product id
			//$p_details = Product::whereRaw('id = ? AND product_status != ?', array($p_id, 'Deleted'))->first();
			$product = Products::initialize($p_id);
			$p_details = $product->getProductDetails();
			if(count($p_details) > 0)
			{
				switch($product_action)
				{
					# Activate product
					case 'activate':
						# Product status is changed as Ok
						if($p_details['product_status'] == 'ToActivate')
						{
							$error_msg = '';
							$status = $this->service->activateProduct($p_id, $p_details);
							# Display activate success msg
							if($status)
							{
								$sucess_msg = trans('admin/productList.product_success_activated');
							}
							else
							{
								$error_msg = trans('admin/productList.product_error_on_action');
							}
						}
						break;

					# Activate product
					case 'disapprove':
						# Product status is changed as Ok
						if($p_details['product_status'] == 'ToActivate')
						{
							$error_msg = '';
							$status = $this->service->disapproveProduct($p_id, $p_details);
							# Display activate success msg
							if($status)
							{
								$sucess_msg = trans('admin/productList.product_success_disapproved');
							}
							else
							{
								$error_msg = trans('admin/productList.product_error_on_action');
							}
						}
						break;

					# Delete product
					case 'delete':
						$error_msg = '';
						if(BasicCUtil::checkIsDemoSite()) {
							$error_msg = Lang::get('common.demo_site_featured_not_allowed');
							//return Redirect::back()->withInput()->with('error_message',$errMsg);
						} else {
							# Product status is changed as Deleted
							$status = $this->service->deleteProduct($p_id, $p_details);
							# Display delete success msg
							if($status)
							{
								$sucess_msg = trans('admin/productList.product_success_deleted');
							}
							else
							{
								$error_msg = trans('admin/productList.product_error_on_action');
							}
						}
						break;
				}
			}
		}
		if($sucess_msg != '')
		{
			return Redirect::to('admin/product/list')->with('success_message', $sucess_msg);
		}
		return Redirect::to('admin/product/list')->with('error_message', $error_msg);
	}

	public function getChangeStatus()
	{
		$this->service = new AdminProductListService();
		$p_id = (Input::get('p_id') == '')? Input::old('p_id'): Input::get('p_id');
		$p_details = array();
		if($p_id != '' && $p_id > 0) {
			$product = Products::initialize($p_id);
			$p_details = $product->getProductDetails(0, false);
		}

		//$p_details = Product::whereRaw('id = ? AND product_status != ?', array($p_id, 'Deleted'))->first();
		$error_msg = '';
		$allow_to_view_form = false;
		$d_arr = array();
		if(count($p_details) > 0)
		{
			$error_msg = trans('admin/productList.product_invalid_action');
			if($p_details['product_status'] == 'ToActivate')
			{
				$allow_to_view_form = true;
				$error_msg = '';
				$d_arr['status_drop'] = $this->service->getStatusDropList('ToActivate');
			}
		}
		if($error_msg != '')
		{
			Session::put('error_message', $error_msg);
		}
		$this->header->setMetaTitle(trans('meta.admin_manage_products_title'));
		return View::make('admin/manageProductStatus', compact('d_arr', 'allow_to_view_form', 'p_id'));
	}

	public function postChangeStatus()
	{
		$this->service = new AdminProductListService();
		$p_id = Input::get('p_id');
		$product = Products::initialize($p_id);
		$p_details = $product->getProductDetails();
		//$p_details = Product::whereRaw('id = ? AND product_status != ?', array($p_id, 'Deleted'))->first();
		$error_msg = trans('admin/productList.product_invalid_action');
		$sucess_msg = '';
		if(count($p_details) > 0)
		{
			if(Input::has('change_status'))
			{
				$product_status = Input::get('product_status');
				$input_arr = Input::all();
				switch($product_status)
				{
					# Activate product
					case 'activate':
						# Product status is changed as Ok
						if($p_details['product_status'] == 'ToActivate')
						{
							$error_msg = '';
							$status = $this->service->activateProduct($p_id, $p_details, $input_arr);
							# Display activate success msg
							if($status)
							{
								$sucess_msg = trans('admin/productList.product_success_activated');
							}
							else
							{
								$error_msg = trans('admin/productList.product_error_on_action');
							}
						}
						break;

					# Activate product
					case 'disapprove':
						# Product status is changed as Ok
						if($p_details['product_status'] == 'ToActivate')
						{
							$error_msg = '';
							$status = $this->service->disapproveProduct($p_id, $p_details, $input_arr);
							# Display activate success msg
							if($status)
							{
								$sucess_msg = trans('admin/productList.product_success_disapproved');
							}
							else
							{
								$error_msg = trans('admin/productList.product_error_on_action');
							}
						}
						break;
				}
			}
		}
		if($sucess_msg != '')
		{
			return Redirect::to('admin/product/list/change-status')->with('success_message', $sucess_msg);
		}
		return Redirect::to('admin/product/list/change-status')->with('error_message', $error_msg);
	}

	public function postProductDetails()
	{
		$product_code = Input::get('product_code');
		$product_url = '#';
		$product_details = array();
		try
		{
			$product = Products::initialize();
			$product->setFilterProductCode($product_code);
			$product_det = $product->getProductDetails();
			if(count($product_det) > 0)
			{
				if($product_det['is_free_product'] == 'No')
				{
					$productService = new ProductService();
					$product_id = $product_det['id'];
					$view_url = $productService->getProductViewURL($product_id, $product_det);
					$product_name = e(nl2br($product_det['product_name']));
					$product_details = array('result' => 'success', 'view_url' => $view_url, 'product_code' => $product_code, 'product_name' => $product_name, 'product_id' => $product_id, 'is_free_product' => $product_det['is_free_product']);
				}
				else
				{
					$product_details = array('result' => 'error', 'error_message' => trans('product.special_price_not_allowed_for_free_product'));
				}
			}
			else
			{
				$product_details = array('result' => 'error', 'error_message' => 'Invalid product code');
			}
		}
		catch(Exception $e)
		{
			$error_msg = $e->getMessage();
			$product_details = array('result' => 'error', 'error_message' => 'Invalid product code');
		}
		echo json_encode($product_details);exit;
	}
	public function postUserDetails()
	{
		$username = Input::get('username');

		$user_details = array();
		if($username!='')
		{
			$adminmanageservice  = new AdminManageUserService();
			$user_det = $adminmanageservice->fetchUserDetailsByUsername($username);
			if(count($user_det) > 0)
			{
				//check price already added for this
				$user_details = array('result' => 'success', 'user_name' => $user_det->user_name, 'email' => $user_det->email, 'user_id' => $user_det->id);
			}
			else
			{
				$user_details = array('result' => 'error', 'error_message' => 'Invalid user name');
			}
		}
		echo json_encode($user_details);exit;
	}
	public function getDefaultProductPrice()
	{
		$product_id = (Input::has('product_id') && Input::get('product_id')!='')?Input::get('product_id'):0;
		$product_details = array();
		if($product_id > 0)
		{
			$product = Products::initialize($product_id);
			$product_details = $product->getProductDetails();
			$groups = Sentry::findAllGroups();
			$data['id'] = 0;
			$data['name'] = trans("product.product_price");
			$data['price_details'] = $product->getGroupPriceDetailsById($product_id, 0);
			$data['required'] = 'required-icon';
			$product_price_groups[] = $data;

			foreach($groups as $each_group){
				if ($each_group->id == 1) continue;
				$data['id'] = $each_group->id;
				$data['name'] = 'Price for "'.$each_group->name.'" Group';
				$data['price_details'] = $product->getGroupPriceDetailsById($product_id, $each_group->id);
				$data['required'] = '';
				$product_price_groups[] = $data;
			}
	 		$d_arr['product_price_groups'] = $product_price_groups;
	 	}
	 	return View::make('admin.showProductDefaultPrice', compact('d_arr', 'product_details'));
	}
}