<?php

class AdminUserController extends BaseController {

	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }

	/**
	 * To list members
	 * AdminUserController::index()
	 *
	 * @return
	 */
	public function index()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = "Members List";
		$user_list = $user_details = array();
		$group_list_default = array('' => trans('common.select_option'));
		$group_list_arr = $this->adminManageUserService->fetchGroupDetails();
		$group_list = $group_list_default + $group_list_arr;

		$is_shop_owner =  array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$is_allowed_to_add_product = array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$status = array('' => trans('common.select_option'), 'blocked' => Lang::get('common.blocked'), 'active' => Lang::get('common.active'), 'inactive' => Lang::get('common.inactive'));

		$this->adminManageUserService->setMemberFilterArr();
		$this->adminManageUserService->setMemberSrchArr(Input::All());

		$q = $this->adminManageUserService->buildMemberQuery();

		$page 		= (Input::has('page')) ? Input::get('page') : 1;
		$start 		= (Input::has('start')) ? Input::get('start') : Config::get('webshopauthenticate.list_paginate');
		$perPage	= Config::get('webshopauthenticate.list_paginate');
		$user_list 	= $q->paginate(15);

		///Get all group details
		$group_details = array();
		$groups = Sentry::findAllGroups();
		if(count($groups) > 0) {
			foreach($groups as $key => $values) {
				$group_details[$values->id] = $values->name;
			}
		}

		$this->header->setMetaTitle(trans('meta.admin_manage_members_title'));
		return View::make('admin.index', compact('d_arr', 'user_list', 'group_list', 'is_shop_owner', 'is_allowed_to_add_product', 'status'));
	}


	/**
	 * To list shops
	 * AdminUserController::getShops()
	 *
	 * @return
	 */
	public function getShops()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = "Shops List";
		$user_list = $user_details = array();
		$shop_action = array('' => trans('common.select_option'), 'deactivate' => trans('admin/manageMembers.deactivate_shop'), 'activate' => trans('admin/manageMembers.activate_shop'));

		$is_shop_owner =  array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$is_allowed_to_add_product = array('' => trans('common.select_option'), 'Yes' => trans('common.yes'), 'No' => trans('common.no'));
		$status = array('' => trans('common.select_option'), 'blocked' => Lang::get('common.blocked'), 'active' => Lang::get('common.active'), 'inactive' => Lang::get('common.inactive'));
		$shop_status = array('' => trans('common.select_option'), 'active' => Lang::get('common.active'), 'inactive' => Lang::get('common.inactive'), 'inactive' => Lang::get('common.inactive'));

		$this->adminManageUserService->setMemberFilterArr();
		$this->adminManageUserService->setMemberSrchArr(Input::All());

		$q = $this->adminManageUserService->buildShopQuery();

		$page 		= (Input::has('page')) ? Input::get('page') : 1;
		$start 		= (Input::has('start')) ? Input::get('start') : Config::get('webshopauthenticate.list_paginate');
		$perPage	= Config::get('webshopauthenticate.list_paginate');
		$user_list 	= $q->paginate(15);

		///Get all group details
		$group_details = array();
		$groups = Sentry::findAllGroups();
		if(count($groups) > 0) {
			foreach($groups as $key => $values) {
				$group_details[$values->id] = $values->name;
			}
		}

		$this->header->setMetaTitle(trans('meta.admin_manage_shops_title'));
		return View::make('admin.shops', compact('d_arr', 'user_list', 'shop_action', 'is_shop_owner', 'is_allowed_to_add_product', 'status', 'shop_status'));
	}

	public function getEditShop($user_id)
	{
		if($user_id)
		{
			$is_valid_user = $this->adminManageUserService->chkValidUserId($user_id);
			$this->header->setMetaTitle(trans('meta.admin_edit_shops_title'));
			if($is_valid_user)
			{
				$shop_service  = new ShopService();
				$shop_obj = Products::initializeShops();
				$shop_obj->setIncludeBlockedUserShop(true);
		    	$shop_details = $shop_obj->getShopDetails($user_id, false);
		    	//echo "<pre>";print_r($shop_details);echo "</pre>";exit;
		    	$shop_paypal_details = $shop_obj->getUsersShopDetails($user_id);
				$country_arr = $shop_service->getCountryList();
		    	$shop_status = isset($shop_paypal_details['shop_status']) ? $shop_paypal_details['shop_status'] : 1;
		    	$logged_user_id = BasicCUtil::getLoggedUserId();
		    	$cancellation_policy = Products::initializeCancellationPolicy();
		    	//echo "<pre>";print_r($cancellation_policy);echo "</pre>";exit;
		    	$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
				$shop_details['shop_country'] = (isset($shop_details['shop_country']) && $shop_details['shop_country'] != '') ? $shop_details['shop_country'] : 'IND';


				$d_arr = array();
				$d_arr['pageTitle'] = Lang::get('admin/manageMembers.memberlist_edit_shop_details');
				$d_arr['mode'] = 'edit';
				$d_arr['user_id'] = $user_id;
				$user_details = $this->adminManageUserService->fetchUserDetailsById($user_id);

				return View::make('admin.shopDetails', compact('shop_details', 'cancellation_policy_details', 'shop_status', 'breadcrumb_arr', 'country_arr', 'shop_paypal_details', 'shop_obj', 'user_id','d_arr', 'user_details'));
			}
			else
			{
				return Redirect::to('admin/shops')->with('error_message', trans('common.some_problem_try_later'));
			}
		}
		else
		{
			return Redirect::to('admin/shops')->with('error_message', trans('common.some_problem_try_later'));
		}
	}

	public function getDeleteShopImage()
	{
		$this->shopService = new ShopService();

		$resource_id 	= Input::get("resource_id");
		$imagename 		= Input::get("imagename");
		$imageext 		= Input::get("imageext");
		$imagefolder 	= Input::get("imagefolder");
		$user_id 	= Input::get("user_id");

		if($imagename != "")
		{
			$delete_status = $this->shopService->deleteShopImageByAdmin($resource_id, $imagename, $imageext, Config::get($imagefolder), $user_id);
			if($delete_status)
			{
				return Response::json(array('result' => 'success'));
			}
		}
		return Response::json(array('result' => 'error'));
	}
	public function getDeleteCancellationPolicy()
	{

		$this->shopService = new CancellationPolicyService();

		$resource_id 	= Input::get("resource_id");
		$user_id 	= Input::get("user_id");
		if($resource_id != "")
		{
			$delete_status = $this->shopService->deleteShopCancellationPolicyFileByAdmin($resource_id, Config::get("webshoppack.shop_cancellation_policy_folder"), $user_id);
			if($delete_status)
			{
				return Response::json(array('result' => 'success', 'success_message' => trans('shopDetails.cancellation_policy_removed_success')));
			}
		}
		return Response::json(array('result' => 'error', 'error_message' => trans('common.some_problem_try_later')));
	}

	public function postEditShop($user_id)
	{
		$success_message = "";
		$input_arr = Input::All();
		$this->shopService = new ShopService();
		if(Input::has('submit_form')) {
			$shop = Products::initializeShops();

			switch(Input::get('submit_form')) {
				case 'update_policy':
					$shop_status = 1;
					$input_arr = Input::All();
					$shop_details = $shop->getShopDetails($user_id);
					$shop_id = '';
					if(!BasicCUtil::checkIsDemoSite()){
						if($shop_details) {
							$shop_id = $shop_details['id'];
							$shop->setShopId($shop_id);
							//$this->setShopDefaultValues($shop, $shop_details);
						}
						$shop->setShopOwnerId($user_id);
						$shop->setShopName($input_arr['shop_name']);
						$shop->setShopUrlSlug($input_arr['url_slug']);
						$shop->setShopSlogan($input_arr['shop_slogan']);
						$shop->setShopDescription($input_arr['shop_desc']);
						$shop->setShopContactInfo($input_arr['shop_contactinfo']);
						$resp = $shop->save();

						$respd = json_decode($resp, true);

						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							return View::make('shopPolicy', compact('error_message', 'shop_details', 'shop_status', 'user_id'));
						}

						if($shop_id != '') {
							$success_message = trans("shopDetails.shop_details_updated_success");
						}
						else {
							$success_message = trans("shopDetails.shopdetails_added_productadd_success");
							$product_add_url = URL::to('product/add');
							$success_message = str_replace('VAR_ADDPRODUCT_LINK', $product_add_url, $success_message);
						}
						$this->adminManageUserService->sendShopDetailsUpdatedMailToUser('Shop Details', $user_id);
						return View::make('admin.shopPolicy', compact('success_message', 'shop_details', 'shop_status', 'user_id'));
					}else{
						$error_message = Lang::get('common.demo_site_featured_not_allowed');
						return View::make('shopPolicy', compact('error_message', 'shop_details', 'shop_status', 'user_id'));
					}
				break;
				case 'update_shop_paypal':
					if(!BasicCUtil::checkIsDemoSite()){
						$input_arr = Input::All();
						$shop->setShopOwnerId($user_id);
						$shop->setPaypalEmailId($input_arr['paypal_id']);

						$resp = $shop->saveUsersShopDetails();
						$respd = json_decode($resp, true);
						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							$shop_paypal_details = array();
							return View::make('admin.shopPaypal', compact('error_message', 'shop_paypal_details', 'user_id'));
						}
						$this->adminManageUserService->sendShopDetailsUpdatedMailToUser('Shop Paypal details', $user_id);
						$shop_paypal_details = $shop->getUsersShopDetails($user_id);
						$success_message = trans("shopDetails.shop_paypal_updated_success");
						return View::make('shopPaypal', compact('success_message', 'shop_paypal_details', 'user_id'));
					}else{
						$shop_paypal_details = $shop->getUsersShopDetails($user_id);
						$error_message = Lang::get('common.demo_site_featured_not_allowed');
						return View::make('admin.shopPaypal', compact('error_message', 'shop_paypal_details', 'user_id'));
					}
				break;
				case 'update_address':
					$country_arr = $this->shopService->getCountryList();
					if(!BasicCUtil::checkIsDemoSite()){
						$input_arr = Input::All();
						$shop_details = $shop->getShopDetails($user_id);
						if($shop_details) {
							$shop->setShopId($shop_details['id']);
							$shop->setShopName($shop_details['shop_name']);
							$shop->setShopUrlSlug($shop_details['url_slug']);
							//$this->setShopDefaultValues($shop, $shop_details);
						}
						$rules = array('shop_country' => 'required',
							'shop_address1' => 'required',
							'shop_city' => 'required',
							'shop_state' => 'required',
							'shop_zipcode' => 'required');

	        			$validator = Validator::make($input_arr, $rules);
						if($validator->passes())
						{
							$shop->setShopOwnerId($user_id);
							$shop->setShopCountry($input_arr['shop_country']);
							$shop->setShopAddress1($input_arr['shop_address1']);
							$shop->setShopAddress2($input_arr['shop_address2']);
							$shop->setShopCity($input_arr['shop_city']);
							$shop->setShopState($input_arr['shop_state']);
							$shop->setShopZipcode($input_arr['shop_zipcode']);
							$resp = $shop->save();
							$respd = json_decode($resp, true);
							if ($respd['status'] == 'error') {
								$error_message = '';
								if(count($respd['error_messages']) > 0) {
									foreach($respd['error_messages'] AS $err_msg) {
										$error_message .= "<p>".$err_msg."</p>";
									}
								}
								return View::make('admin.shopAddress', compact('error_message', 'shop_details', 'country_arr', 'user_id'));
							}
							$this->adminManageUserService->sendShopDetailsUpdatedMailToUser('Shop Address Details', $user_id);
							$success_message = trans("shopDetails.shop_address_updated_success");
							return View::make('admin.shopAddress', compact('success_message', 'shop_details', 'country_arr', 'user_id'));
						}
					}else{
						$shop_details = $shop->getShopDetails($user_id);
						$error_message = Lang::get('common.demo_site_featured_not_allowed');
						return View::make('admin.shopAddress', compact('error_message', 'shop_details', 'country_arr', 'user_id'));
					}
				break;
				case 'update_banner':
					if(!BasicCUtil::checkIsDemoSite()){
						$input_arr = Input::All();
						if (Input::hasFile('shop_banner_image'))
						{
							if($_FILES['shop_banner_image']['error'])
							{
								$shop_details = $shop->getShopDetails($user_id);
								//$shop_details = $this->shopService->getShopDetails();
								$error_message = trans("common.uploader_max_file_size_err_msg");
								return View::make('admin.shopBanner', compact('error_message', 'shop_details'));
							}
						}
						$rules = array('shop_banner_image' => 'Required|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_uploader_allowed_extensions")),
										//'shop_banner_image' => 'mimes:'.Config::get("shop.shop_uploader_allowed_extensions").'|size:'.Config::get("shop.shop_image_uploader_allowed_file_size")
									);
						$message = array('shop_banner_image.mimes' => trans('common.uploader_allow_format_err_msg'),
									'shop_banner_image.size' => trans('common.uploader_max_file_size_err_msg'),
								);
						$v = Validator::make(Input::all(), $rules, $message);
						if ($v->fails())
						{
							$shop_details = $shop->getShopDetails($user_id);
							$errors = $v->errors();
							return View::make('admin.shopBanner', compact('errors', 'shop_details', 'user_id'));
						}
						else
						{
							$file = Input::file('shop_banner_image');
							$file_size = $file->getClientSize();
							$allowed_size = Config::get("webshoppack.shop_image_uploader_allowed_file_size");
							$allowed_size = $allowed_size * 1024; //To convert KB to Byte
							if(($file_size > $allowed_size)  || $file_size <= 0)
							{
								$shop_details = $shop->getShopDetails($user_id);
								$error_message = trans("common.uploader_max_file_size_err_msg");
								return View::make('admin.shopBanner', compact('error_message', 'shop_details', 'user_id'));
							}

							$shop_details = $shop->getShopDetails($user_id);
							if($shop_details) {
								$shop->setShopId($shop_details['id']);
								$shop->setShopName($shop_details['shop_name']);
								$shop->setShopUrlSlug($shop_details['url_slug']);
								//$this->setShopDefaultValues($shop, $shop_details);
							}

							$file = Input::file('shop_banner_image');
							$image_ext = $file->getClientOriginalExtension();
							$image_name = Str::random(20);
							$destinationpath = URL::asset(Config::get("webshoppack.shop_image_folder"));
							$img_arr = $this->shopService->updateBannerImage($file, $image_ext, $image_name, $destinationpath);

							$shop->setShopOwnerId($user_id);
							$shop->setShopImageName($img_arr['image_name']);
							$shop->setShopImageExtension($img_arr['image_ext']);
							$shop->setShopImageServerUrl($img_arr['image_server_url']);
							$shop->setShopImageHeight($img_arr['t_width']);
							$shop->setShopImageWidth($img_arr['t_height']);
							$resp = $shop->save();
							$respd = json_decode($resp, true);
							if ($respd['status'] == 'error') {
								$error_message = '';
								if(count($respd['error_messages']) > 0) {
									foreach($respd['error_messages'] AS $err_msg) {
										$error_message .= "<p>".$err_msg."</p>";
									}
								}
								return View::make('admin.shopBanner', compact('error_message', 'shop_details', 'country_arr', 'user_id'));
							}
							$shop_details = $shop->getShopDetails($user_id, false);
							$this->adminManageUserService->sendShopDetailsUpdatedMailToUser('Shop Banner Details', $user_id);
							$success_message = trans("shopDetails.shop_banner_updated_success");
							return View::make('admin.shopBanner', compact('success_message', 'shop_details', 'user_id'));
						}
					}else{
						$shop_details = $shop->getShopDetails($user_id);
						$error_message = Lang::get('common.demo_site_featured_not_allowed');
						return View::make('admin.shopBanner', compact('error_message', 'shop_details', 'user_id'));
					}
				break;


				case 'update_cancellation_policy':
					$cancellationPolicyService = new CancellationPolicyService();
					$cancellation_policy = Products::initializeCancellationPolicy();
					if(!BasicCUtil::checkIsDemoSite()){
						$input_arr = Input::All();

						if(Input::has('id') && Input::get('id') >0)
							$cancellation_policy->setCancellationPolicyId(Input::get('id'));

						$cancellation_policy->setUserId($user_id);
						if (Input::hasFile('shop_cancellation_policy_file'))
						{
							if($_FILES['shop_cancellation_policy_file']['error'])
							{
								$error_message = trans("common.uploader_max_file_size_err_msg");
								$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
								return View::make('shopCancellationPolicy', compact('error_message', 'cancellation_policy_details', 'user_id'));
							}
						}
						$rules = array(
										'cancellation_policy_text' => 'required_without:shop_cancellation_policy_file',
										'shop_cancellation_policy_file' => 'required_without:cancellation_policy_text|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_cancellation_policy_allowed_extensions")).'|max:'.Config::get("webshoppack.shop_cancellation_policy_allowed_file_size"),
									);

						$message = array('shop_cancellation_policy_file.mimes' => trans('common.uploader_allow_format_err_msg'),
									'shop_cancellation_policy_file.max' => trans('common.uploader_max_file_size_err_msg'),
									'required_without' => trans('admin/cancellationpolicy.either_cancellation_text_or_file_required')
								);
						$v = Validator::make(Input::all(), $rules, $message);
						if ($v->fails())
						{
							$errors = $v->errors();
							$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
							return View::make('admin.shopCancellationPolicy', compact('errors', 'cancellation_policy_details', 'user_id'));

						}
						else
						{

							if (Input::hasFile('shop_cancellation_policy_file'))
							{
								$file = Input::file('shop_cancellation_policy_file');
								$file_ext = $file->getClientOriginalExtension();
								$file_name = Str::random(20);
								$destinationpath = URL::asset(Config::get("webshoppack.shop_cancellation_policy_folder"));
								$img_arr = $cancellationPolicyService->updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath);

								$cancellation_policy->setCancellationPolicyFilename($img_arr['file_name']);
								$cancellation_policy->setCancellationPolicyFiletype($img_arr['file_ext']);
								$cancellation_policy->setCancellationPolicyServerUrl($img_arr['file_server_url']);


								$cancellation_policy->setCancellationPolicyText('');

							}
							elseif(Input::has('cancellation_policy_text'))
							{
								$cancellation_policy->setCancellationPolicyText(Input::get('cancellation_policy_text'));
								$cancellation_policy->setCancellationPolicyFilename('');
								$cancellation_policy->setCancellationPolicyFiletype('');
								$cancellation_policy->setCancellationPolicyServerUrl('');
								$cancellationPolicyService->deleteShopCancellationPolicyFileByAdmin(null, null, $user_id);
							}

							$resp = $cancellation_policy->save();
							$respd = json_decode($resp, true);
							if ($respd['status'] == 'error') {
								$error_message = '';
								if(count($respd['error_messages']) > 0) {
									foreach($respd['error_messages'] AS $err_msg) {
										$error_message .= "<p>".$err_msg."</p>";
									}
								}
								$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
								return View::make('shopCancellationPolicy', compact('error_message', 'cancellation_policy_details', 'user_id'));
							}
							$this->adminManageUserService->sendShopDetailsUpdatedMailToUser('Shop Cancellation Policy', $user_id);
							$success_message = trans("shopDetails.shop_cancellation_policy_updated_success");
							$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
							return View::make('admin.shopCancellationPolicy', compact('success_message', 'cancellation_policy_details', 'user_id'));
						}
					}else{
						$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($user_id);
						$error_message = Lang::get('common.demo_site_featured_not_allowed');
						return View::make('shopCancellationPolicy', compact('error_message', 'cancellation_policy_details', 'user_id'));
					}
				break;
			}
		}
	}


	public function postChangeShopStatus()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$selected_checkbox_id = Input::has('selected_checkbox_id')?Input::get('selected_checkbox_id'):0;
			$selected_status = Input::has('selected_status_id')?Input::get('selected_status_id'):0;

			$user_ids = explode(",", $selected_checkbox_id);
			//echo "<pre>";print_r($user_ids);echo "</pre>";

			if(!empty($user_ids))
			{
				if ($selected_status !='')
				{
					$shop_status = ($selected_status=='deactivate')?0:1;
					User::whereIn('id', $user_ids)->update(array('shop_status'=>$shop_status));
					$array_multi_key = array('featured_seller_banner_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
					$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key); // Clear cache for product details
					$action = ($selected_status=='deactivate')?'deactivateshop':'activateshop';
					foreach($user_ids as $user_id)
					{
						$this->adminManageUserService->sendShopStatusUpdatedMailToUser($action, $user_id);
					}
					Session::flash('success', trans('admin/manageMembers.shoplist_shop_status_changed_suc_msg'));
					return;
				}
				else
				{
					Session::flash('error', trans('admin/manageMembers.shoplist_please_select_staus_err_msg'));
					return;
				}
			}
			else
			{
				Session::flash('error', trans('admin/manageMembers.shoplist_please_select_staus_err_msg'));
				return;
			}
		}else{
			Session::flash('error', Lang::get('common.demo_site_featured_not_allowed'));
			return;
		}
	}



	/**
	 * AdminUserController::getAddUsers()
	 *
	 * @return
	 */
	public function getAddUsers()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = Lang::get('admin/addMember.addmember_page_title');
		$d_arr['mode'] = 'add';
		$d_arr['user_id'] = 0;
		$user_details = array();
		//Fetch groups details
		$group_array = $this->adminManageUserService->fetchGroups();
		$user_group_id = 2;
		$status = 'active';
		$status_arr = array('activate' => Lang::get('common.activate'), 'deactivate'=> Lang::get('common.deactivate'));
		$this->header->setMetaTitle(trans('meta.admin_add_member_title'));
		return View::make('admin.addMember', compact('d_arr', 'user_details', 'group_array', 'user_group_id', 'status', 'status_arr'));
	}

	/**
	 * AdminAddUserController::postAddUsers()
	 *
	 * @return
	 */
	public function postAddUsers()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$messages = array();
			$this->userAccountService = new UserAccountService();
			$rules = array('first_name' => $this->userAccountService->getValidatorRule('first_name'),
							'last_name' => $this->userAccountService->getValidatorRule('last_name'),
							'user_name' => $this->userAccountService->getValidatorRule('user_name'),
							'email' => $this->userAccountService->getValidatorRule('email'),
							'password' => $this->userAccountService->getValidatorRule('password')
						  );
			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->passes())
			{
				$input = Input::all();
				$user_id = $this->userAccountService->addNewUser($input, false, true);
				if($user_id)
				{
					Session::flash('success', Lang::get('admin/addMember.member_add_success'));
					return Redirect::to('admin/users');
				}
			}
			else
			{
				return Redirect::to('admin/users/add')->withInput()->withErrors($validator);
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	/**
	 * AdminAddUserController::getEditUsers()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public function getEditUsers($user_id='')
	{
		if($user_id)
		{
			if ($user_id == 1 && $user_id != CUtil::getAuthUser()->id) {
				return Redirect::to('admin/users');
			}
			$is_valid_user = $this->adminManageUserService->chkValidUserId($user_id);
			$this->header->setMetaTitle(trans('meta.admin_edit_member_title'));
			if($is_valid_user)
			{
				$d_arr = array();
				$d_arr['pageTitle'] = Lang::get('admin/addMember.editmember_page_title');
				$d_arr['mode'] = 'edit';
				$d_arr['user_id'] = $user_id;
				$user_details = $this->adminManageUserService->fetchUserDetailsById($user_id);
				$user_group_id = UsersGroups::whereRaw('user_id = ?', array($user_id))->pluck('group_id');
				//Fetch groups details
				$group_array = $this->adminManageUserService->fetchGroups();

				$status_arr = array();
				$status = 'deactivate';
				if(count($user_details) > 0) {
					if($user_details->is_banned == 1) {
						$status = 'block';
						$status_arr += array('block'=> Lang::get('common.block'), 'unblock'=> Lang::get('common.unblock'));
					}
					else {
						if($user_details->activated == 1) {
							$status = 'activate';
							$status_arr += array('activate' => Lang::get('common.activate'), 'block'=> Lang::get('common.block'));
						}
						else {
							$status = 'deactivate';
							$status_arr += array('activate' => Lang::get('common.activate'), 'deactivate'=> Lang::get('common.deactivate'), 'block'=> Lang::get('common.block'));
						}
					}
				}
				return View::make('admin.addMember', compact('d_arr', 'user_details', 'group_array', 'user_group_id', 'status', 'status_arr'));
			}
			else
			{
				//$user_details = array();
				//$d_arr['mode'] = 'edit';
				//$d_arr['pageTitle'] = Lang::get('admin/addMember.editmember_page_title');
				//$d_arr['error_msg'] = Lang::get('admin/addMember.invalid_user_id');
				return Redirect::to('admin/users')->with('error_message', Lang::get('admin/addMember.invalid_user_id'));
			}
		}
		else
		{
			return Redirect::to('admin/users/add');
		}
	}

	/**
	 * AdminAddUserController::postEdit()
	 *
	 * @return
	 */
	public function postEditUsers()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$mode = Input::get('mode');
			$user_id = Input::get('user_id');
			$messages = array();
			if($mode == 'edit')
			{
				$this->userAccountService = new UserAccountService();

				$is_valid_user = $this->adminManageUserService->chkValidUserId($user_id);
				if($is_valid_user)
				{
					$user_input = Input::all();
					$rules = array('first_name' => $this->userAccountService->getValidatorRule('first_name'),
								'last_name' => $this->userAccountService->getValidatorRule('last_name'),
								'user_name' => 'Required|unique:users,user_name,'.$user_id.',id'.'|Min:'.Config::get('webshopauthenticate.fieldlength_password_min').
												'|Max:'.Config::get('webshopauthenticate.fieldlength_password_max').
												'|regex:'."/^([-a-z0-9_ ])+$/i",
								'email' => 'Required|Email|unique:users,email,'.$user_id.',id'
							  );
					if(Input::get('password') != '' || Input::get('password_confirmation') != '')
					{
						$rules['password'] = $this->userAccountService->getValidatorRule('password');
						$rules['password_confirmation'] = 'Required';
					}
					$validator = Validator::make($user_input, $rules, $messages);
					if ($validator->passes())
					{
						$is_user_updated = $this->userAccountService->updateUserDetails(Input::all());
						if($is_user_updated)
						{
							$success_msg = Lang::get('admin/addMember.member_update_success');
							return Redirect::to('admin/users')->with('success', $success_msg);
						}
					}
					else
					{
						return Redirect::to('admin/users/edit/'.$user_id)->withInput()->withErrors($validator);
					}
				}
				else
				{
					$error_msg = Lang::get('admin/addMember.invalid_user_id');
					return Redirect::to('admin/users/edit/'.$user_id)->with('error_message', $error_msg);
				}
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	/**
	 * AdminUserController::getChangeUserStatus()
	 *
	 * @return
	 */
	public function getChangeUserStatus()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$action='';
			if(Input::has('user_id') && Input::has('action'))
			{
				$user_id = Input::get('user_id');
				$action = Input::get('action');
				$success_msg = "";
				//echo "Yes this was called", $user_id," action ", $action;
				$success_msg = $this->adminManageUserService->updateUserActivationByAdmin($user_id, $action);
			}
			Session::flash('success', $success_msg);
			if($action == 'deactivateshop' || $action == 'activateshop'){
				$this->adminManageUserService->sendShopStatusUpdatedMailToUser($action, $user_id);
				$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key); // Clear cache for product details
				return Redirect::to('admin/shops');
			}else{
				return Redirect::to('admin/users');
			}
		}else{
			$action = Input::get('action');
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			if($action == 'deactivateshop' || $action == 'activateshop'){
				return Redirect::to('admin/shops')->with('error_message',$errMsg);
			}else{
				return Redirect::to('admin/users')->with('error_message',$errMsg);
			}
		}
	}

	public function getChangeSellerStatus()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			if(Input::has('user_id') && Input::has('action'))
			{
				$user_id = Input::get('user_id');
				$action = Input::get('action');
				$this->userService = new UserAccountService();
				$user = User::where("id", $user_id)->where('activated', 1)->first();
				if($user)
				{
					if($action=='approve')
					{
						$success_msg = Lang::get('admin/manageMembers.seller_approve_success');
						$this->userService->setUserToBecomeSeller($user_id);
					}
					else
					{
						$success_msg = Lang::get('admin/manageMembers.seller_block_success');
						$this->userService->denyUserToBecomeSeller($user_id);
					}
				}
				$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key); // Clear cache for product details
			}
			Session::flash('success', $success_msg);
			return Redirect::to('admin/users');
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->with('error_message',$errMsg);
		}
	}

	/**
	 * AdminUserController::postChangeGroupName
	 *
	 * @return success_msg
	 */
	public function postChangeGroupName()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$selected_checkbox_id = Input::has('selected_checkbox_id')?Input::get('selected_checkbox_id'):0;
			$selected_group_name_id = Input::has('selected_group_name_id')?Input::get('selected_group_name_id'):0;
			$user_ids = explode(",", $selected_checkbox_id);
			if(!empty($user_ids))
			{
				if ($selected_group_name_id !='')
				{
					foreach($user_ids as $user_id)
					{
						$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
						if($group_exists > 0) {
							UsersGroups::whereRaw('user_id = ?', array($user_id))->update(array('group_id'=>$selected_group_name_id));
						} else {
							UsersGroups::insert(array('user_id'=>$user_id, 'group_id'=>$selected_group_name_id));
						}
					}
					Session::flash('success', trans('admin/manageMembers.memberlist_group_name_changed_suc_msg'));
					return;
				}else{
					Session::flash('error', trans('admin/manageMembers.memberlist_please_select_group_name_err_msg'));
					return;
				}
			}else{
				Session::flash('error', trans('admin/manageMembers.memberlist_please_select_checkbox_err_msg'));
				return;
			}
		}else{
			Session::flash('error', Lang::get('common.demo_site_featured_not_allowed'));
			return;
		}
	}

	/**
	 * AdminAddUserController::getManageCredits()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public function getManageCredits($user_id = '', $credit_id = '')
	{
		$user_id = Input::has('user_id') ? Input::get('user_id') : 0;
		$credit_id = Input::has('credit_id') ? Input::get('credit_id') : 0;
		$show_form = true;
		if($user_id)
		{
			if ($user_id == 1 && $user_id != CUtil::getAuthUser()->id) {
				$error_msg = trans('admin/manageMembers.invalid_action');
				return Redirect::to('admin/users')->with('error', $error_msg);
			}
			$is_valid_user = $this->adminManageUserService->chkValidUserId($user_id);
			$this->header->setMetaTitle(trans('admin/manageMembers.manage_credits'));
			if($is_valid_user)
			{
				//Check is valid credit id
				if($credit_id) {
					$is_valid_credit_id = Products::checkIsValidCreditId($user_id, $credit_id);
					if(!$is_valid_credit_id) {
						$error_msg = trans('admin/manageMembers.invalid_credit_id');
						return Redirect::to('admin/users/manage-credits?user_id='.$user_id)->with('error', $error_msg);
					}
				}

				$manage_credits_obj = Products::initializeManageCredits();
				$credit_details = $manage_credits_obj->getCreditsDetailsById($credit_id);

				$d_arr = $credits_details = array();
				$d_arr['pageTitle'] = trans('admin/manageMembers.add_credits');
				$d_arr['mode'] = 'add';
				$d_arr['is_invoice_generated'] = ($credit_details && $credit_details['generate_invoice'] == 'Yes') ? 'Yes' : 'No';
				$post_url = URL::action('AdminUserController@postManageCredits'). '?user_id=' . $user_id;
				if($credit_id) {
					$d_arr['pageTitle'] = trans('admin/manageMembers.edit_credits');
					$d_arr['mode'] = 'edit';
					$post_url = URL::action('AdminUserController@postManageCredits'). '?user_id=' . $user_id.'&credit_id='.$credit_id;
				}
				$d_arr['post_url'] = $post_url;
				$d_arr['user_id'] = $user_id;

				if($credit_details && $credit_details['paid'] == 'Yes') {
					$error_msg = trans('admin/manageMembers.cant_edit_credits_in_paid_status');
					return Redirect::to('admin/users/manage-credits?user_id='.$user_id)->with('error', $error_msg);
				}
				$user_details = CUtil::getUserDetails($user_id);
				//echo '<pre>';print_r($credit_details);echo '</pre>';exit;
				$user_group_id = UsersGroups::whereRaw('user_id = ?', array($user_id))->pluck('group_id');
				$credit_list = $manage_credits_obj->getCreditsList($user_id, 'paginate', 15);
				//echo '<pre>';print_r($credit_list);echo '</pre>';exit;
				return View::make('admin.addCredits', compact('d_arr', 'credit_list', 'credit_details', 'user_details', 'user_group_id'
				, 'credit_id', 'show_form'));
			}
			else {
				$error_msg = Lang::get('admin/addMember.invalid_user_id');
				return Redirect::to('admin/users')->with('error', $error_msg);
			}
		}
		else {
			$error_msg = trans('admin/manageMembers.invalid_action');
			return Redirect::to('admin/users')->with('error', $error_msg);
		}
	}

	/**
	 * AdminAddUserController::postManageCredits()
	 *
	 * @return
	 */
	public function postManageCredits()
	{
		//echo '<pre>';print_r(Input::all());echo '</pre>';exit;
		$user_id = Input::get('user_id');
		if(!BasicCUtil::checkIsDemoSite()){
			$credit_id = Input::get('credit_id');
			$show_form = true;
			$messages = $credit_details = array();
			if($user_id) {
				$is_valid_user = $this->adminManageUserService->chkValidUserId($user_id);
				if($is_valid_user) {
					$input = Input::all();
					$logged_user_id = BasicCUtil::getLoggedUserId();
					//Check is valid credit id
					if($credit_id) {
						$is_valid_credit_id = Products::checkIsValidCreditId($user_id, $credit_id);
						if(!$is_valid_credit_id) {
							$error_msg = trans('admin/manageMembers.invalid_action');
							return Redirect::to('admin/users/manage-credits?user_id='.$user_id)->with('error_msg', $error_msg);
						}
					}

					$default_curreny = Config::get('generalConfig.site_default_currency');

					//Add credits to credits log
					$manage_credits_obj =  Products::initializeManageCredits();
					$common_invoice_obj =  Products::initializeCommonInvoice();
					$invoice_paid_status = 'Unpaid';
					if($credit_id && $credit_id > 0) {
						$manage_credits_obj->setCreditId($credit_id);
						$manage_credits_obj->setCreditsDateUpdated(DB::raw('NOW()'));
					}
					else {
						$manage_credits_obj->setCreditsDateAdded(DB::raw('NOW()'));
					}
					$manage_credits_obj->setCurrency($default_curreny);
					$manage_credits_obj->setAmount($input['amount']);
					$manage_credits_obj->setCreditedBy($logged_user_id);
					$manage_credits_obj->setCreditedTo($user_id);
					$manage_credits_obj->setAdminNotes($input['admin_notes']);
					$manage_credits_obj->setUserNotes($input['user_notes']);

					//Set as paid by default
					$manage_credits_obj->setPaid('Yes');
					$manage_credits_obj->setDatePaid(DB::raw('NOW()'));
					$invoice_paid_status = 'Paid';

					//Generate invoice by default
					$manage_credits_obj->setGenerateInvoice('Yes');

					$resp = $manage_credits_obj->Addcredits();

					$respd = json_decode($resp, true);
					//echo '<pre>';print_r($respd);echo '</pre>';exit;
					if ($respd['status'] == 'error') {
						$error_msg = '';
						if(count($respd['error_messages']) > 0) {
							foreach($respd['error_messages'] AS $err_msg) {
								$error_msg .= "<p>".$err_msg."</p>";
							}
						}
						$redirect_url = URL::action('AdminUserController@getManageCredits'). '?user_id=' . $user_id;
						if($credit_id) {
							$redirect_url = URL::action('AdminUserController@getManageCredits'). '?user_id=' . $user_id.'&credit_id='.$credit_id;
						}
						return Redirect::to($redirect_url)->withInput()->with('error', $error_msg);
					}
					else {
						//Log::info(print_r($cart_details,1));
						//Log::info($input['generate_invoice']);
						//Add common invoice
						//if((isset($input['is_invoice_generated']) && $input['is_invoice_generated'] == 'Yes')) {
							$invoice_info = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId('Credits', $respd['credit_id']);
							if($invoice_info) {
								$common_invoice_obj->setCommonInvoiceId($invoice_info['common_invoice_id']);
							}
							$common_invoice_obj->setUserId($user_id);
							$common_invoice_obj->setReferenceType('Credits');
							$common_invoice_obj->setReferenceId($respd['credit_id']);
							$common_invoice_obj->setCurrency($default_curreny);
							$common_invoice_obj->setAmount(CUtil::formatAmount($input['amount']));
							$common_invoice_obj->setStatus($invoice_paid_status);
							if($invoice_paid_status == 'Paid')
								$common_invoice_obj->setDatePaid(DB::raw('NOW()'));
							$common_invoice_det = $common_invoice_obj->addCommonInvoice();
							$common_invoice_det = json_decode($common_invoice_det);
							//echo "<pre>";print_r($common_invoice_det);echo "</pre>";exit;
						//}

						$action = ($credit_id) ? 'edit' : 'add';
						if($action == 'add') {
							//Add amount to wallet
							$credit_obj = Credits::initialize();
							$credit_obj->setUserId($user_id);
							$credit_obj->setCurrency($default_curreny);
							$credit_obj->setAmount($input['amount']);
							$credit_obj->credit();
						}
						//echo "<pre>";print_r($common_invoice_det);echo "</pre>";
						//Add site transaction details
						if($invoice_paid_status == 'Paid' && isset($common_invoice_det->common_invoice_id) && $common_invoice_det->common_invoice_id >0)
						{
							$trans_obj = new SiteTransactionHandlerService();
							$transaction_arr['date_added'] = new DateTime;
							$transaction_arr['user_id'] = $user_id;
							$transaction_arr['transaction_type'] = 'credit';
							$transaction_arr['amount'] = $input['amount'];
							$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
							$transaction_arr['transaction_key'] = 'walletaccount_fromsite';
							$transaction_arr['reference_content_table'] = 'common_invoice';
							$transaction_arr['reference_content_id'] = $common_invoice_det->common_invoice_id;
							$transaction_arr['status'] = 'completed';
							$transaction_arr['transaction_notes'] = 'Credit amount to your wallet from admin. invoice id: '.$common_invoice_det->common_invoice_id;
							$trans_id = $trans_obj->addNewTransaction($transaction_arr);
						}

						if(isset($input['notify_user']) && $input['notify_user'] == 'Yes') {
							$this->sendInvoiceMailToUser($respd['credit_id'], $action);
						}
					}
					$success_msg = trans('admin/manageMembers.credits_added_successfully');
					if($credit_id) {
						$success_msg = trans('admin/manageMembers.credits_updated_successfully');
					}
					return Redirect::to('admin/users/manage-credits?user_id='.$user_id)->with('success', $success_msg);
				}
				else {
					$error_msg = trans('admin/addMember.invalid_user_id');
					return Redirect::to('admin/users')->with('error', $error_msg);
				}
			}
		}else{
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/users/manage-credits?user_id='.$user_id)->with('error', $error_msg);
		}
	}

	public function sendInvoiceMailToUser($credit_id, $action = 'add')//action add / edit
	{
		$manage_credits_obj =  Products::initializeManageCredits();
		$credit_details = $manage_credits_obj->getCreditsDetailsById($credit_id);
		$invoice_details = DB::table('common_invoice')->where('reference_id', $credit_id)->select('common_invoice_id', 'status')->first();

		if($credit_details) {
			$subject = Config::get('generalConfig.site_name').' - '.Lang::get('email.credit_added');
			$msg = 'Admin added '.$credit_details['currency'].' '.$credit_details['amount'].' Credits to your account.';
			if($action == 'edit') {
				$subject = Config::get('generalConfig.site_name').' - '.Lang::get('email.credit_updated');
				$msg = 'Admin updated credits in your account.';
			}
			$user_details = CUtil::getUserDetails($credit_details['credited_to']);
			$data = array(
				'credit_id' => $credit_details['credit_id'],
				'currency' => $credit_details['currency'],
				'amount' => $credit_details['amount'],
				'credited_by' => $credit_details['credited_by'],
				'credited_to' => $credit_details['credited_to'],
				'admin_notes' => $credit_details['admin_notes'],
				'user_notes' => $credit_details['user_notes'],
				'paid' => $credit_details['paid'],
				'date_paid' => CUtil::FMTDate($credit_details['date_paid'], "Y-m-d H:i:s", ""),
				'generate_invoice' => $credit_details['generate_invoice'],
				'date_added' => CUtil::FMTDate($credit_details['date_added'], "Y-m-d H:i:s", ""),
				'date_updated' => CUtil::FMTDate($credit_details['date_updated'], "Y-m-d H:i:s", ""),
				'display_name'	 => $user_details['display_name'],
				'user_email'	 => $user_details['email'],
				'invoice_details'	 => $invoice_details,
				'subject' => $subject,
				'msg' => $msg,
				'action' => ucfirst($action),
			);
			try {
				//Mail to User
				Mail::send('emails.creditsInvoiceToBuyer', $data, function($m) use ($data) {
						$m->to($data['user_email']);
						$m->subject($data['subject']);
					});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}
	Public function getUserDetails($user_id = '')
	{
		//Get user account balance list
		$user_account_balance = array();
		$credit_obj = Credits::initialize();
		$myaccount_listing_service = new MyAccountListingService();
		$user_acc_bal_details = $myaccount_listing_service->fetchuserAccountBalance($credit_obj, $user_id);

		$main_currency_arr['USD'] = array('currency_symbol' => '$', 'amount' => 0.0);
		$main_currency_arr['INR'] = array('currency_symbol' => 'Rs', 'amount' => 0.0);
		$d_arr['main_currency_arr'] = $main_currency_arr;
		if(count($user_acc_bal_details) > 0) {
			$payments_arr = array();
			foreach($user_acc_bal_details as $acc_bal) {
				if(round($acc_bal['amount']) > 0) {
					if($acc_bal['currency'] == "USD" || $acc_bal['currency'] == "INR") {
						$payments_arr['main'][] = $acc_bal;
						$main_currency_arr[$acc_bal['currency']] = array('currency_symbol' => $acc_bal['currency_symbol'], 'amount' => $acc_bal['amount']);
					}
					else {
						$payments_arr['other'][] = $acc_bal;
					}
				}
			}

			$d_arr['main_currency_arr'] = $main_currency_arr;
			$d_arr['user_acc_bal_details'] = $payments_arr;
		}

		$i = Input::has('invoice')?Input::get('invoice'):'invoice';
		$user_details = DB::table('users')
						->leftjoin('users_groups','users.id','=','users_groups.user_id')
						->where('id', $user_id)
						->first();
		$status = (Input::has('status') && Input::get('status') == 'Unpaid') ? Input::get('status'):'Paid';
    	//echo $invoice_status;exit;
    	//$logged_user_id = BasicCUtil::getLoggedUserId();
    	$invoice_details = DB::table('common_invoice')
    					   ->leftjoin('credits_log','common_invoice.reference_id','=','credits_log.credit_id')
    					   ->leftjoin('shop_order','common_invoice.reference_id','=','shop_order.id')
    					   ->where('user_id',$user_id);
		if ($status == "Paid") {
			$invoice_details->where('status',"paid");
		} else{
			$invoice_details->where('status',"unpaid");
		}
		$invoice_details->orderby('common_invoice_id', 'desc');
		$invoice_details = $invoice_details->paginate(5);
		//echo "<pre>"; print_r($invoice_details); echo "</pre>";exit;
		$this->header->setMetaTitle(trans('meta.user_details'));
		return view::make('admin.userDetail', compact('user_details','invoice_details','status','user_id','i', 'd_arr'));
	}

	public function getManageFeaturedSellers()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0) {
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = 'Add Favorite sellers';
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['featured_details'] = array();
		}
		else {
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = 'Edit Favorite sellers';
			$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
			$d_arr['featured_details'] 	= $this->adminManageUserService->getFeaturedSellersSettings($id);
		}
		$d_arr['id'] = $id;

		$perPage    					= 10;
		$q 								= $this->adminManageUserService->buildFeaturedSellersQuery();
		$details 						= $q->paginate($perPage);
		return View::make('admin.featuredSellersSettings', compact('details', 'd_arr'));
	}

	public function postManageFeaturedSellers()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$input = Input::All();
			$messages = array();
			$messages['user_name.invalid_username'] = 'Invalid Username';
			$messages['user_name.username_exist'] = 'Username already exists';
			$messages['user_name.atleast_one_product'] = 'User should have atleast one product in his shop.';
			$rules['user_name'] = 'Required|FeaturedSellers:'.$input['user_name'].','.$input['settings_id'];
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes()) {
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$this->adminManageUserService->updateFeaturedSellers($input);

			if($input['settings_id'] == 0) {
				return Redirect::to('admin/manage-favorite-sellers')->with('success_message','Favorite seller added Successfully');
			}
			else {
				return Redirect::to('admin/manage-favorite-sellers')->with('success_message','Favorite seller updated Successfully');
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function deleteFeaturedSellers()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$id = Input::has('id') ? Input::get('id') : 0;
			if($id) {
				$this->adminManageUserService->deleteFeaturedSellersRec($id);
			}
			return Redirect::to('admin/manage-favorite-sellers')->with('success_message','Favorite seller deleted Successfully');
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/manage-favorite-sellers')->with('error_message',$errMsg);
		}
	}

	public function getManageToppicksUsers()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0) {
			$d_arr['mode'] 		= Lang::get('common.add');
			$d_arr['pageTitle'] = Lang::get('common.add_top_picks_users');
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['top_picks_details'] = array();
		}
		else {
			$d_arr['mode'] 		= Lang::get('common.edit');
			$d_arr['pageTitle'] = Lang::get('common.edit_top_picks_users');
			$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
			$d_arr['top_picks_details'] 	= $this->adminManageUserService->getTopPicksSettings($id);
		}
		$d_arr['id'] = $id;

		$perPage    					= 10;
		$q 								= $this->adminManageUserService->buildTopPicksUsersQuery();
		$details 						= $q->paginate($perPage);
		return View::make('admin.topPicksUsersSettings', compact('details', 'd_arr'));
	}

	public function postManageToppicksUsers()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$input = Input::All();
			$messages = array();
			$messages['user_name.invalid_username'] = 'Invalid Username';
			$messages['user_name.username_exist'] = 'Username already exists';
			$messages['user_name.atleast_one_favorite'] = 'User should have atleast one favorite item.';
			$rules['user_name'] = 'Required|TopPicksUser:'.$input['user_name'].','.$input['settings_id'];
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes()) {
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$this->adminManageUserService->updateTopPicksUsers($input);

			if($input['settings_id'] == 0) {
				return Redirect::to('admin/manage-toppicks-users')->with('success_message','Top picks user added Successfully');
			}
			else {
				return Redirect::to('admin/manage-toppicks-users')->with('success_message','Top picks user updated Successfully');
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function deleteToppicksUsers()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$id = Input::has('id') ? Input::get('id') : 0;
			if($id) {
				$this->adminManageUserService->deleteTopPicksUsersRec($id);
			}
			return Redirect::to('admin/manage-toppicks-users')->with('success_message','Top picks user deleted Successfully');
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function getManageFavoriteProducts()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0) {
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = 'Add Favorites product';
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['favorites_details'] = array();
		}
		else {
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = 'Edit Favorites product';
			$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
			$d_arr['favorites_details'] 	= $this->adminManageUserService->getFavoriteProductsSettings($id);
		}
		$d_arr['id'] = $id;

		$perPage    					= 10;
		$q 								= $this->adminManageUserService->buildFavoriteProductsQuery();
		$details 						= $q->paginate($perPage);
		return View::make('admin.favoritesProductSettings', compact('details', 'd_arr'));
	}

	public function postManageFavoriteProducts()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$input = Input::All();
			$messages = array();
			$messages['product_code.invalid_product'] = 'Invalid product code';
			$messages['product_code.product_code_exist'] = 'Product code already exists';
			$messages['product_code.atleast_one_favorite'] = 'This product does not have favourites, choose product that having favorites.';
			$rules['product_code'] = 'Required|FavoriteProducts:'.$input['product_code'].','.$input['settings_id'];
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes()) {
				return Redirect::back()->withInput()->withErrors($validator);
			}
			if(isset($input['product_code']) && $input['product_code'] != ''){
				$arr = explode('-',trim($input['product_code']));
				$input['product_code'] = $arr[0];
			}
			$this->adminManageUserService->updateFavoriteProducts($input);

			if($input['settings_id'] == 0) {
				return Redirect::to('admin/manage-favorite-products')->with('success_message','Favorite products added Successfully');
			}
			else {
				return Redirect::to('admin/manage-favorite-products')->with('success_message','Favorite products updated Successfully');
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function deleteFavoriteProducts()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$id = Input::has('id') ? Input::get('id') : 0;
			if($id) {
				$this->adminManageUserService->deleteFavoriteProductsRec($id);
			}
			return Redirect::to('admin/manage-favorite-products')->with('success_message','Favorite products deleted Successfully');
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function getUsersAutoComplete()
	{
		$qry = User::select(DB::raw('user_name, id, email'));
		$qry = $qry->whereRaw('activated = ? AND is_banned = ?', array(1, 0))->get();
		$users_list = array();
		if(count($qry))
		{
			foreach($qry as $key => $val)
			{
				//$users_list[$val['user_name']] = ucfirst($val['user_name']).'('.$val['email'].')';
				$users_list[$val['id']] = $val['user_name'];
			}
		}
		return json_encode($users_list);
	}

	public function getShopOwnersNameAutoComplete()
	{
		$qry = User::select(DB::raw('user_name, id, email'));
		$qry = $qry->whereRaw('activated = ? AND is_banned = ? AND is_allowed_to_add_product = ? AND shop_status = ?', array(1, 0, 'Yes', 1))->get();
		$users_list = array();
		if(count($qry))
		{
			foreach($qry as $key => $val)
			{
				//$users_list[$val['user_name']] = ucfirst($val['user_name']).'('.$val['email'].')';
				$users_list[$val['id']] = $val['user_name'];
			}
		}
		return json_encode($users_list);
	}

	public function getShopOwnersCodeAutoComplete()
	{
		$qry = User::select(DB::raw('user_name, id, email'));
		$qry = $qry->whereRaw('activated = ? AND is_banned = ? AND is_allowed_to_add_product = ? AND shop_status = ?', array(1, 0, 'Yes', 1))->get();
		$users_list = array();
		if(count($qry))
		{
			foreach($qry as $key => $val)
			{
				//$users_list[$val['id']] = $val['user_name'];
				$user_code = BasicCUtil::setUserCode($val['id']);
				$users_list[$user_code] = $user_code.' - '.$val['user_name'].' ('.$val['email'].')';
			}
		}
		return json_encode($users_list);
	}

	public function getProductAutoComplete()
	{
		$qry = Product::select(DB::raw('product.product_code, product.id, product.product_name'))
						->join('users', 'product.product_user_id', '=', 'users.id')
						->where('users.is_banned', '=', 0)
						->where('users.shop_status', '=', 1);
		$qry = $qry->where('product.product_status', 'Ok')->get();
		$users_list = array();
		if(count($qry))
		{
			foreach($qry as $key => $val)
			{
				//$users_list[$val['user_name']] = ucfirst($val['user_name']).'('.$val['email'].')';
				$users_list[$val['id']] = $val['product_code'].' - '. $val['product_name'];
			}
		}
		return json_encode($users_list);
	}

	public function SearchMembers()
	{
		$send_id = Input::get('send_id');
		$d_arr = array();
		$d_arr['pageTitle'] = trans('admin/manageMembers.memberlist_page_title');
		$user_list = $user_details = array();
		$user_groups = $this->adminManageUserService->fetchGroupDetails();

		$this->adminManageUserService->setMemberFilterArr();
		$this->adminManageUserService->setMemberSrchArr(Input::All());

		$q = $this->adminManageUserService->buildMemberQuery();

		$page 		= (Input::has('page')) ? Input::get('page') : 1;
		$start 		= (Input::has('start')) ? Input::get('start') : Config::get('webshopauthenticate.list_paginate');
		$perPage	= Config::get('webshopauthenticate.list_paginate');
		$user_list 	= $q->paginate($perPage);

		return View::make('admin/searchMembers', compact('d_arr', 'user_list', 'user_groups', 'send_id'));
	}
}