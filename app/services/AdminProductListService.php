<?php

//@added by manikandan133at10
class AdminProductListService extends ProductService
{
	private $search_arr = array();
	public $product_status_arr = array();
	private $qry = '';

	public function getFeatureStatusDropOptions()
	{
		$feature_list = array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		return $feature_list;
	}

	public function getProductStatusDropOptions()
	{
		$this->product_status_arr = array('' => trans('common.select_option'),
								'Draft' => trans('admin/productList.product_status_draft'),
								'Ok' => trans('admin/productList.product_status_ok'),
								'ToActivate' => trans('admin/productList.product_status_to_activate'),
								'NotApproved' => trans('admin/productList.product_status_not_approved'),
								'Verified' => trans('admin/productList.product_status_verified'),
								'Deleted' => trans('admin/productList.product_status_deleted'),
								'Expired' => trans('admin/productList.product_status_expired'),
								);
		return $this->product_status_arr;
	}

	public function getSearchValue($key)
	{
		return (isset($this->search_arr[$key])) ? $this->search_arr[$key] : '';
	}

	public function buildProductsQuery($prod_obj)
	{
		//form the search query
		if(is_numeric($this->getSearchValue('search_product_id_from')) && $this->getSearchValue('search_product_id_from') > 0)
		{
			$prod_obj->setFilterProductIdFrom($this->getSearchValue('search_product_id_from'));
		}
		if(is_numeric($this->getSearchValue('search_product_id_to')) && $this->getSearchValue('search_product_id_to') > 0)
		{
			$prod_obj->setFilterProductIdTo($this->getSearchValue('search_product_id_to'));
		}

		# Status
		if($this->getSearchValue('search_product_status') != '')
		{
			$prod_obj->setFilterProductStatus($this->getSearchValue('search_product_status'));
		}

		# Featured Status
		if($this->getSearchValue('search_featured_product') != '' )
		{
			$prod_obj->setFilterFeaturedProduct($this->getSearchValue('search_featured_product'));
		}

		# Product title
		if($this->getSearchValue('search_product_name') != '')
		{
			$prod_obj->setFilterProductName($this->getSearchValue('search_product_name'));
		}

		# Product code
		if($this->getSearchValue('search_product_code') != '')
		{
			$prod_obj->setFilterProductCode($this->getSearchValue('search_product_code'));
		}

		# Category
		if($this->getSearchValue('search_product_category') > 0)
		{
			$prod_obj->setFilterProductCategory($this->getSearchValue('search_product_category'));
		}

		# user code
		if($this->getSearchValue('search_seller_code') != '')
		{
			$prod_obj->setFilterSellerCode($this->getSearchValue('search_seller_code'));
		}

		# date added
		if($this->getSearchValue('search_product_from_date'))
		{
			$prod_obj->setFilterProductAddedFrom($this->getSearchValue('search_product_from_date'));
		}
		if($this->getSearchValue('search_product_to_date'))
		{
			$prod_obj->setFilterProductAddedTo($this->getSearchValue('search_product_to_date'));
		}

	}

	public function setProductsSearchArr($input)
	{
		$this->search_arr['search_product_id_from'] =(isset($input['search_product_id_from']) && $input['search_product_id_from'] != '') ? $input['search_product_id_from'] : "";
		$this->search_arr['search_product_id_to']= (isset($input['search_product_id_to']) && $input['search_product_id_to'] != '') ? $input['search_product_id_to'] : "";
		$this->search_arr['search_product_name']= (isset($input['search_product_name']) && $input['search_product_name'] != '') ? $input['search_product_name'] : "";
		$this->search_arr['search_product_code']= (isset($input['search_product_code']) && $input['search_product_code'] != '') ? $input['search_product_code'] : "";
		$this->search_arr['search_product_category']= (isset($input['search_product_category']) && $input['search_product_category'] != '') ? $input['search_product_category'] : "";
		$this->search_arr['search_featured_product']= (isset($input['search_featured_product']) && $input['search_featured_product'] != '') ? $input['search_featured_product'] : "";
		$this->search_arr['search_product_author']= (isset($input['search_product_author']) && $input['search_product_author'] != '') ? $input['search_product_author'] : "";
		$this->search_arr['search_product_status']= (isset($input['search_product_status']) && $input['search_product_status'] != '') ? $input['search_product_status'] : "";
		$this->search_arr['search_seller_code']= (isset($input['search_seller_code']) && $input['search_seller_code'] != '') ? $input['search_seller_code'] : "";
		$this->search_arr['search_product_from_date'] =(isset($input['search_product_from_date']) && $input['search_product_from_date'] != '') ? $input['search_product_from_date'] : "";
		$this->search_arr['search_product_to_date']= (isset($input['search_product_to_date']) && $input['search_product_to_date'] != '') ? $input['search_product_to_date'] : "";
	}


	public function getProductCategoryArr($cat_id, $target_blank = false, $cat_link_alone = false, $call_page = 'product', $return_as_array = false)
    {
		$cat_arr = array();
		$prod_obj = Products::initialize();
		$cat_details = $prod_obj->getCategoryArr($cat_id, $target_blank, $cat_link_alone);
		if(count($cat_details) > 0)
		{
			foreach($cat_details AS $cat)
			{
				$cat_arr[$cat->seo_category_name] = $cat->category_name;
			}
			$cat_arr = array_slice($cat_arr, 1); //To remove root category
		}
		return $cat_arr;
	}

	public function activateProduct($p_id, $p_details, $input_arr)
	{
		$product = Products::initialize($p_id);
		if(count($p_details) == 0)
		{
			$p_details = $product->getProductDetails();
		}
		//To update product status to approved
		$product->changeStatus('Ok');

		//To update user total products count
		$product->updateUserTotalProducts($p_details['product_user_id']);
		/*$input_arr['product_id'] = $p_id;
		$this->addProductStatusComment($input_arr);*/

		//To send mail
		//$this->sendProductActionMail($p_id, 'activate', $input_arr);
		return true;
	}

	public function disapproveProduct($p_id, $p_details, $input_arr)
	{
		$product = Products::initialize($p_id);
		if(count($p_details) == 0)
		{
			$p_details = $product->getProductDetails();
		}
		//To update product status to approved
		$product->changeStatus('NotApproved');
		return true;
	}

	public function deleteProduct($p_id, $p_details)
	{
		$product = Products::initialize($p_id);
		if(count($p_details) == 0)
		{
			$p_details = $product->getProductDetails();
		}
		//To update product status to deleted
		$product->changeStatus('Deleted');

		//Delete all dependent records
		$product->deleteGroupPriceDetails($p_id);
		Webshoptaxation::ProductTaxations()->deleteProductAllTaxation($p_id);

		//To update user total products count
		$product->updateUserTotalProducts($p_details['product_user_id']);
		return true;
	}

	public function sendProductActionMail($p_id, $action, $input_arr)
	{
		$product = Products::initialize($p_id);
		$product_details = $product->getProductDetails();
		$user_details = CUtil::getUserDetails($product_details->product_user_id);
		$product_code = $product_details->product_code;
		$url_slug = $product_details->url_slug;
		$view_url = $this->getProductViewURL($product_details->id, $product_details);

		$user_type = (CUtil::isSuperAdmin())? 'Admin':'Staff';
		$logged_user_id = (isLoggedin()) ? getAuthUser()->user_id : 0;
		$staff_details = CUtil::getUserDetails($logged_user_id);

		$data = array(
			'product_code'	=> $product_details['product_code'],
			'product_name'  		=> $product_details['product_name'],
			'display_name'	 => $user_details['display_name'],
			'user_email'	 => $user_details['email'],
			'action'	 => $action,
			'view_url'		=> $view_url,
			'admin_notes'	=> isset($input_arr['comment'])? $input_arr['comment'] : '',
			'user_type'	=> $user_type
		);

		$data['product_details'] = $product_details;
		$data['user_details'] = $user_details;
		$data['staff_details'] = $staff_details;
		try {
			//Mail to User
			Mail::send('emails.mp_product.productStatusUpdate', $data, function($m) use ($data) {
				$m->to($data['user_email']);
				$subject = str_replace('VAR_PRODUCT_CODE', $data['product_code'],trans('email.productStatusUpdate'));
				$m->subject($subject);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
		//Send mail to admin
		$mailer = new AgMailer;
		$data['subject'] = str_replace('VAR_PRODUCT_CODE', $data['product_code'],trans('email.productStatusUpdateAdmin'));
		$mailer->sendAlertMail('mp_product_status_update', 'emails.mp_product.productStatusUpdateAdmin', $data);
	}

	public function getChangeStatusValidationRule()
	{
		return array('rules' => array('product_status' => 'Required',
										'admin_comment' => 'Required'
										),
										'messages' => array('required' => trans('common.required'))
									);
	}

	public function getStatusDropList($from_status)
	{
		$from_status = strtolower($from_status);
		$status_arr = array('' => trans('common.select_option'));
		$product_status_arr = $this->getProductStatusDropOptions();
		if($from_status == "toactivate")
		{
			$status_arr['activate'] = trans('admin/productList.status_activate');
			$status_arr['disapprove'] = trans('admin/productList.status_disapprove');
		}
		return $status_arr;
	}

	public function getPriceGroupsDetailsNew($p_id, $user_id, $quantity = 1, $matrix_id = 0, $allow_cache = true)
	{
		$product = Products::initialize($p_id);
		$price_details = array();
		$product_detail = $product->getProductDetails();
		$group_id = 0;

		$group_price_details = $product->getGroupPriceDetailsById($p_id, 0, $quantity, 1, $allow_cache);
		if($group_price_details[0]['price'] != '') {
			$price_details['product_id'] = $p_id;
			$price_details['group_id'] = $group_id;
	        $price_details['currency'] = $group_price_details[0]['currency'];
	        $price_details['price'] = $group_price_details[0]['price'];
	        $price_details['price_usd'] = $group_price_details[0]['price_usd'];
	        $price_details['discount_percentage'] = $group_price_details[0]['discount_percentage'];
	        $price_details['discount'] = $group_price_details[0]['discount'];
	        $price_details['discount_usd'] = $group_price_details[0]['discount_usd'];
		}
		return $price_details;
	}
}