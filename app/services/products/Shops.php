<?php

class Shops {

	protected $shop_id;

	protected $fields_arr = array();

	protected $detail_fields_arr = array();

	protected $filter_shop_id = '';

	protected $filter_shop_ids_in = array();


	protected $filter_shop_owner_id = '';

	protected $filter_shop_name = '';

	protected $filter_url_slug = '';

	protected $filter_shop_status = '';

	protected $filter_is_featured_shop = '';

	protected $shops_per_page = '';

	protected $order_by = '';

	protected $include_blocked_user_shop = false;

	public function __construct()
	{
		//$this->shop_id = $shop_id;
		//$this->shopservice = new ShopService;
	}

	public function getShopId()
	{
		return $this->shop_id;
	}

	public function setShopId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setShopOwnerId($val)
	{
		$this->fields_arr['user_id'] = $val;
		$this->detail_fields_arr['id'] = $val;
	}

	public function setShopName($val)
	{
		$this->fields_arr['shop_name'] = $val;
	}

	public function setShopUrlSlug($val)
	{
		$this->fields_arr['url_slug'] = $val;
	}

	public function setShopSlogan($val)
	{
		$this->fields_arr['shop_slogan'] = $val;
	}

	public function setShopDescription($val)
	{
		$this->fields_arr['shop_desc'] = $val;
	}

	public function setShopAddress1($val)
	{
		$this->fields_arr['shop_address1'] = $val;
	}

	public function setShopAddress2($val)
	{
		$this->fields_arr['shop_address2'] = $val;
	}

	public function setShopCity($val)
	{
		$this->fields_arr['shop_city'] = $val;
	}

	public function setShopState($val)
	{
		$this->fields_arr['shop_state'] = $val;
	}

	public function setShopZipcode($val)
	{
		$this->fields_arr['shop_zipcode'] = $val;
	}

	public function setShopCountry($val)
	{
		$this->fields_arr['shop_country'] = $val;
	}

	public function setShopMessage($val)
	{
		$this->fields_arr['shop_message'] = $val;
	}

	public function setShopContactInfo($val)
	{
		$this->fields_arr['shop_contactinfo'] = $val;
	}

	public function setShopImageName($val)
	{
		$this->fields_arr['image_name'] = $val;
	}

	public function setShopImageExtension($val)
	{
		$this->fields_arr['image_ext'] = $val;
	}

	public function setShopImageServerUrl($val)
	{
		$this->fields_arr['image_server_url'] = $val;
	}

	public function setShopImageHeight($val)
	{
		$this->fields_arr['t_height'] = $val;
	}

	public function setShopImageWidth($val)
	{
		$this->fields_arr['t_width'] = $val;
	}

	public function setIsFeaturedShop($val)
	{
		$this->fields_arr['is_featured_shop'] = $val;
	}

	public function setCancellationPolicyText($val)
	{
		$this->fields_arr['cancellation_policy_text'] = $val;
	}

	public function setCancellationPolicyFilename($val)
	{
		$this->fields_arr['cancellation_policy_filename'] = $val;
	}

	public function setCancellationPolicyFiletype($val)
	{
		$this->fields_arr['cancellation_policy_filetype'] = $val;
	}

	public function setCancellationPolicyServerUrl($val)
	{
		$this->fields_arr['cancellation_policy_server_url'] = $val;
	}

	public function setIsShopOwner($val)
	{
		$this->detail_fields_arr['is_shop_owner'] = $val;
	}

	public function setShopStatus($val)
	{
		$this->detail_fields_arr['shop_status'] = $val;
	}

	public function setTotalProducts($val)
	{
		$this->detail_fields_arr['total_products'] = $val;
	}

	public function setPaypalEmailId($val)
	{
		$this->detail_fields_arr['paypal_id'] = $val;
	}


	public function setPolicyWelcome($val)
	{
		$this->fields_arr['policy_welcome'] = $val;
	}

	public function setPolicyPayment($val)
	{
		$this->fields_arr['policy_payment'] = $val;
	}

	public function setPolicyShipping($val)
	{
		$this->fields_arr['policy_shipping'] = $val;
	}

	public function setPolicyRefundExchange($val)
	{
		$this->fields_arr['policy_refund_exchange'] = $val;
	}

	public function setPolicyFaq($val)
	{
		$this->fields_arr['policy_faq'] = $val;
	}

	public function setCreatedAt()
	{
		$this->fields_arr['created_at'] = DB::raw('NOW()');
	}

	//Filters
	public function setFilterShopId($val)
	{
		$this->filter_shop_id = $val;
	}
	public function setFilterShopIdsIn($val = array())
	{
		$this->filter_shop_ids_in = $val;
	}


	public function setFilterShopOwnerId($val)
	{
		$this->filter_shop_owner_id = $val;
	}

	public function setFilterShopName($val)
	{
		$this->filter_shop_name = $val;
	}

	public function setFilterShopUrlSlug($val)
	{
		$this->filter_url_slug = $val;
	}

	public function setFilterShopStatus($val)
	{
		$this->filter_shop_status = $val;
	}

	public function setFilterIsFeaturedShop($val)
	{
		$this->filter_is_featured_shop = $val;
	}

	public function setShopPagination($val)
	{
		$this->shops_per_page = $val;
	}

	public function setIncludeBlockedUserShop($val = true)
	{
		$this->include_blocked_user_shop = $val;
	}

	public function setOrderByField($val)
	{
		$this->order_by = $val;
	}

	public function save()
	{
		//Validation start
		$rules = $message = array();
		$rules += array(
				'shop_name' => 'Required|Min:'.Config::get('products.shopname_min_length').'|Max:'.Config::get('products.shopname_max_length').'|unique:shop_details,shop_name,'.$this->fields_arr['user_id'].',user_id',
				'url_slug' => 'Required|unique:shop_details,url_slug,'.$this->fields_arr['user_id'].',user_id',
				'shop_slogan' => 'Min:'.Config::get('products.shopslogan_min_length').'|Max:'.Config::get('products.shopslogan_max_length'),
				'shop_desc' => 'Min:'.Config::get('products.fieldlength_shop_description_min').'|Max:'.Config::get('products.fieldlength_shop_description_max'),
				'shop_contactinfo' => 'Min:'.Config::get('products.fieldlength_shop_contactinfo_min').'|Max:'.Config::get('products.fieldlength_shop_contactinfo_max'),
		);
		$message = array('shop_name.unique' => trans('shopDetails.shopname_already_exists'),
						'url_slug.unique' => trans('shopDetails.shopurlslug_already_exists'),
						);

		$validator = Validator::make($this->fields_arr, $rules, $message);

		if(!isset($this->fields_arr['id']) || $this->fields_arr['id'] == '')
		{
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return Response::json(array('error' => $errors));
			}
			//echo "<pre>";print_r($this->fields_arr);echo "</pre>";exit;
			//Validation End
			$this->setCreatedAt();
			$shop_id = ShopDetails::insertGetId($this->fields_arr);
			$shop_details_arr = array('is_shop_owner' => 'Yes'
										, 'shop_status' => 1
										, 'total_products' => 0);
			$affected_rows = User::whereRaw('id = ?', array($this->detail_fields_arr['id']))->update($shop_details_arr);
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			$this->chkAndUpdateUserToBecomeSeller($this->fields_arr['user_id']);
			return json_encode(array('status' => 'success', 'affected_rows' => $affected_rows));
		}
		else {
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return json_encode(array('status' => 'error', 'error_messages' => $errors));
			}
			//echo "<pre>";print_r($this->fields_arr);echo "</pre>";exit;
			//Validation End
			ShopDetails::whereRaw('id = ?', array($this->fields_arr['id']))->update($this->fields_arr);

			$shop_details_arr = array('is_shop_owner' => 'Yes');
			$affected_rows = User::whereRaw('id = ?', array($this->detail_fields_arr['id']))->update($shop_details_arr);
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			$this->chkAndUpdateUserToBecomeSeller($this->fields_arr['user_id']);
			return json_encode(array('status' => 'success', 'affected_rows' => $affected_rows));
		}
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function saveUsersShopDetails()
	{
		if($this->detail_fields_arr['id'] > 0) {
			$rules = $message = array();
			$rules += array(
				'paypal_id' => 'Required|email'
			);
			$validator = Validator::make($this->detail_fields_arr, $rules, $message);
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return json_encode(array('status' => 'error', 'error_messages' => $errors));
			}
			else {

				if(!CUtil::chkIsValidPaypalBusinessEmail($this->detail_fields_arr['paypal_id'])) {
					$errors = trans('shopDetails.valid_paypal_bussiness_email');
					return json_encode(array('status' => 'error', 'error_messages' => array($errors)));
				}
				//Check shop details record exists for the given shop owner
				$shop_rec_count = ShopDetails::whereRaw('user_id = ?', array($this->fields_arr['user_id']))->count();
				if($shop_rec_count == 0) {
					$shop_arr = array('user_id' => $this->fields_arr['user_id'], 'created_at' => DB::raw('NOW()'));
					$shop_id = ShopDetails::insertGetId($shop_arr);
					$array_multi_key = array('featured_seller_banner_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
				}
				User::whereRaw('id = ?', array($this->detail_fields_arr['id']))->update($this->detail_fields_arr);
				$this->chkAndUpdateUserToBecomeSeller($this->fields_arr['user_id']);
				return json_encode(array('status' => 'success'));
			}
		}
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopDetails($user_id, $allow_cache = true)
	{
		$shop_details_arr = array();
		$cache_key = 'shop_details_'.$user_id ;
		$shop_details_query = ShopDetails::Select('shop_details.id', 'shop_details.user_id', 'shop_details.shop_name'
												, 'shop_details.url_slug', 'shop_details.shop_slogan', 'shop_details.shop_desc'
												, 'shop_details.shop_address1', 'shop_details.shop_address2'
												, 'shop_details.shop_city', 'shop_details.shop_state'
												, 'shop_details.shop_zipcode', 'shop_details.shop_country', 'shop_details.shop_message'
												, 'shop_details.shop_contactinfo', 'shop_details.image_name', 'shop_details.image_ext'
												, 'shop_details.image_server_url', 'shop_details.t_height', 'shop_details.t_width'
												, 'shop_details.cancellation_policy_text', 'shop_details.cancellation_policy_filename'
												, 'shop_details.cancellation_policy_filetype', 'shop_details.cancellation_policy_server_url'
												, 'shop_details.policy_welcome', 'shop_details.policy_payment', 'shop_details.policy_shipping'
												, 'shop_details.policy_refund_exchange', 'shop_details.policy_faq', 'shop_details.created_at'
												, 'shop_details.updated_at');

		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$cache_key .= '_FS';
			$shop_details_query = $shop_details_query->addSelect("users.is_featured_seller", "users.featured_seller_expires");
		}

		if(!$this->include_blocked_user_shop) {
			$cache_key .= '_IBUS';
			$shop_details_query = $shop_details_query->join('users', function($join)
									 {
										 $join->on('shop_details.user_id', '=', 'users.id');
										 $join->where('users.is_banned', '=', 0);
										 $join->where('users.shop_status', '=', 1);
									 });
		}
		if(CUtil::chkIsAllowedModule('featuredsellers') && $this->include_blocked_user_shop) {
			$cache_key .= '_FSIBUS';
			$shop_details_query = $shop_details_query->join('users', function($join)
									 {
										 $join->on('shop_details.user_id', '=', 'users.id');
									 });
		}
		if (!$allow_cache || (($shop_details = HomeCUtil::cacheGet($cache_key)) === NULL)) {
			$shop_details = $shop_details_query->where('shop_details.user_id', $user_id)->get();
			HomeCUtil::cachePut($cache_key, $shop_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		//echo "<pre>";print_r($shop_details);echo "</pre>";exit;
		if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['shop_name'] = $vlaues->shop_name;
				$shop_details_arr['url_slug'] = $vlaues->url_slug;
				$shop_details_arr['shop_url'] = URL::to('shop/'.$vlaues->url_slug);
				$shop_details_arr['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr['shop_city'] = $vlaues->shop_city;
				$shop_details_arr['shop_state'] = $vlaues->shop_state;
				$shop_details_arr['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr['shop_country'] = $vlaues->shop_country;
				$shop_details_arr['shop_message'] = $vlaues->shop_message;
				$shop_details_arr['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr['image_name'] = $vlaues->image_name;
				$shop_details_arr['image_ext'] = $vlaues->image_ext;
				$shop_details_arr['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr['t_height'] = $vlaues->t_height;
				$shop_details_arr['t_width'] = $vlaues->t_width;

				$shop_details_arr['cancellation_policy_text'] = $vlaues->cancellation_policy_text;
				$shop_details_arr['cancellation_policy_filename'] = $vlaues->cancellation_policy_filename;
				$shop_details_arr['cancellation_policy_filetype'] = $vlaues->cancellation_policy_filetype;
				$shop_details_arr['cancellation_policy_server_url'] = $vlaues->cancellation_policy_server_url;

				$shop_details_arr['policy_welcome'] = $vlaues->policy_welcome;
				$shop_details_arr['policy_payment'] = $vlaues->policy_payment;
				$shop_details_arr['policy_shipping'] = $vlaues->policy_shipping;
				$shop_details_arr['policy_refund_exchange'] = $vlaues->policy_refund_exchange;
				$shop_details_arr['policy_faq'] = $vlaues->policy_faq;

				$shop_details_arr['created_at'] = $vlaues->created_at;
				$shop_details_arr['updated_at'] = $vlaues->updated_at;

				if(CUtil::chkIsAllowedModule('featuredsellers')) {
					$shop_details_arr['is_featured_seller'] = $vlaues->is_featured_seller;
					$shop_details_arr['featured_seller_expires'] = $vlaues->featured_seller_expires;
				}
			}
		}
		return $shop_details_arr;
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopDetailsWithFilter()
	{
		$cache_key = 'SDWFCK';
		$shop_details_arr = array();
		$shop_details = ShopDetails::Select('shop_details.id', 'shop_details.user_id', 'shop_details.shop_name'
												, 'shop_details.url_slug', 'shop_details.shop_slogan', 'shop_details.shop_desc'
												, 'shop_details.shop_address1', 'shop_details.shop_address2'
												, 'shop_details.shop_city', 'shop_details.shop_state'
												, 'shop_details.shop_zipcode', 'shop_details.shop_country', 'shop_details.shop_message'
												, 'shop_details.shop_contactinfo', 'shop_details.image_name', 'shop_details.image_ext'
												, 'shop_details.image_server_url', 'shop_details.t_height', 'shop_details.t_width'
												, 'shop_details.cancellation_policy_text', 'shop_details.cancellation_policy_filename'
												, 'shop_details.cancellation_policy_filetype', 'shop_details.cancellation_policy_server_url'
												, 'shop_details.policy_welcome', 'shop_details.policy_payment', 'shop_details.policy_shipping'
												, 'shop_details.policy_refund_exchange', 'shop_details.policy_faq', 'shop_details.created_at'
												, 'shop_details.updated_at');

		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$cache_key .= 'CAMFS';
			$shop_details = $shop_details->addSelect("users.is_featured_seller", "users.featured_seller_expires");
		}
		if(!$this->include_blocked_user_shop) {
			$cache_key .= 'NIBUS';
			$shop_details = $shop_details->join('users', function($join)
			                         {
			                             $join->on('shop_details.user_id', '=', 'users.id');
			                             $join->where('users.is_banned', '=', 0);
			                             $join->where('users.shop_status', '=', 1);
			                         });
		}
		if(CUtil::chkIsAllowedModule('featuredsellers') && $this->include_blocked_user_shop) {
			$cache_key .= 'FSNBUS';
			$shop_details = $shop_details->join('users', function($join)
			                         {
			                             $join->on('shop_details.user_id', '=', 'users.id');
			                         });
		}

		if($this->filter_shop_id != '')
		{
			$cache_key .= 'FSID_'.$this->filter_shop_id;
			$shop_details = $shop_details->whereRaw('shop_details.id = ?', array($this->filter_shop_id));
		}
		if($this->filter_shop_owner_id != '')
		{
			$cache_key .= 'FSOID_'.$this->filter_shop_owner_id;
			$shop_details = $shop_details->whereRaw('shop_details.user_id = ?', array($this->filter_shop_owner_id));
		}
		if($this->filter_shop_name != '')
		{
			$cache_key .= 'FSNA_'.$this->filter_shop_name;
			$shop_details = $shop_details->whereRaw('shop_details.shop_name = ?', array($this->filter_shop_name));
		}
		if($this->filter_url_slug != '')
		{
			$cache_key .= 'FSNA_'.$this->filter_url_slug;
			$shop_details = $shop_details->whereRaw('shop_details.url_slug = ?', array($this->filter_url_slug));
		}
		if (($shop_details_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$shop_details_result = $shop_details->get();
			HomeCUtil::cachePut($cache_key, $shop_details_result, Config::get('generalConfig.cache_expiry_minutes'));
		}

		if(count($shop_details_result) > 0) {
			foreach($shop_details_result as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['shop_name'] = $vlaues->shop_name;
				$shop_details_arr['url_slug'] = $vlaues->url_slug;
				$shop_details_arr['shop_url'] = URL::to('shop/'.$vlaues->url_slug);
				$shop_details_arr['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr['shop_city'] = $vlaues->shop_city;
				$shop_details_arr['shop_state'] = $vlaues->shop_state;
				$shop_details_arr['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr['shop_country'] = $vlaues->shop_country;
				$shop_details_arr['shop_message'] = $vlaues->shop_message;
				$shop_details_arr['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr['image_name'] = $vlaues->image_name;
				$shop_details_arr['image_ext'] = $vlaues->image_ext;
				$shop_details_arr['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr['t_height'] = $vlaues->t_height;
				$shop_details_arr['t_width'] = $vlaues->t_width;
				$shop_details_arr['is_featured_shop'] = $vlaues->is_featured_shop;

				$shop_details_arr['cancellation_policy_text'] = $vlaues->cancellation_policy_text;
				$shop_details_arr['cancellation_policy_filename'] = $vlaues->cancellation_policy_filename;
				$shop_details_arr['cancellation_policy_filetype'] = $vlaues->cancellation_policy_filetype;
				$shop_details_arr['cancellation_policy_server_url'] = $vlaues->cancellation_policy_server_url;

				$shop_details_arr['policy_welcome'] = $vlaues->policy_welcome;
				$shop_details_arr['policy_payment'] = $vlaues->policy_payment;
				$shop_details_arr['policy_shipping'] = $vlaues->policy_shipping;
				$shop_details_arr['policy_refund_exchange'] = $vlaues->policy_refund_exchange;
				$shop_details_arr['policy_faq'] = $vlaues->policy_faq;

				$shop_details_arr['created_at'] = $vlaues->created_at;
				$shop_details_arr['updated_at'] = $vlaues->updated_at;
				if(CUtil::chkIsAllowedModule('featuredsellers')) {
					$shop_details_arr['is_featured_seller'] = $vlaues->is_featured_seller;
					$shop_details_arr['featured_seller_expires'] = $vlaues->featured_seller_expires;
				}
			}
		}
		return $shop_details_arr;
	}

	/**
	 * Getting shop list
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopList()
	{
		$shop_details_arr = array();
		$cache_key = 'SLCK';
		$shop_details = ShopDetails::Select('shop_details.id', 'shop_details.user_id', 'shop_details.shop_name', 'shop_details.url_slug'
												, 'shop_details.shop_slogan', 'shop_details.shop_desc'
												, 'shop_details.shop_address1', 'shop_details.shop_address2'
												, 'shop_details.shop_city', 'shop_details.shop_state'
												, 'shop_details.shop_zipcode', 'shop_details.shop_country'
												, 'shop_details.shop_message', 'shop_details.shop_contactinfo'
												, 'shop_details.image_name', 'shop_details.image_ext'
												, 'shop_details.image_server_url', 'shop_details.t_height', 'shop_details.t_width'
												, 'shop_details.is_featured_shop', 'shop_details.created_at', 'shop_details.updated_at');

		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$cache_key .= 'AMFS';
			$shop_details = $shop_details->addSelect("users.is_featured_seller", "users.featured_seller_expires");
		}
		if(!$this->include_blocked_user_shop) {
			$cache_key .= 'NIBUS';
			$shop_details = $shop_details->join('users', function($join)
			                         {
			                             $join->on('shop_details.user_id', '=', 'users.id');
			                             $join->where('users.is_banned', '=', 0);
			                             $join->where('users.shop_status', '=', 1);
			                         });
		}
		if(CUtil::chkIsAllowedModule('featuredsellers') && $this->include_blocked_user_shop) {
			$cache_key .= 'AFSIBUS';
			$shop_details = $shop_details->join('users', function($join)
			                         {
			                             $join->on('shop_details.user_id', '=', 'users.id');
			                         });
		}

		if($this->filter_shop_id != '')
		{
			$cache_key .= 'SI'.$this->filter_shop_id;
			$shop_details = $shop_details->whereRaw('shop_details.id = ?', array($this->filter_shop_id));
		}
		if($this->filter_shop_ids_in != '' && is_array($this->filter_shop_ids_in) && !empty($this->filter_shop_ids_in)) {
			$cache_key .= 'SIDS'.serialize($this->filter_shop_ids_in);
			$shop_details = $shop_details->whereIn('shop_details.id', $this->filter_shop_ids_in);
		}

		if($this->filter_shop_owner_id != '')
		{
			$cache_key .= 'SOID'.$this->filter_shop_owner_id;
			$shop_details = $shop_details->whereRaw('shop_details.user_id = ?', array($this->filter_shop_owner_id));
		}
		if($this->filter_shop_name != '') {
			$name_arr = explode(" ",$this->filter_shop_name);
			if(count($name_arr) > 0) {
				foreach($name_arr AS $names) {
					$cache_key .= '_NS'.$names;
					$shop_details = $shop_details->whereRaw("( shop_details.shop_name LIKE '%".addslashes($names)."%')");
				}
			}
		}
		if($this->filter_url_slug != '')
		{
			$cache_key .= 'FUS'.$this->filter_url_slug;
			$shop_details = $shop_details->whereRaw('shop_details.url_slug = ?', array($this->filter_url_slug));
		}
		if($this->filter_is_featured_shop != '')
		{
			$cache_key .= 'FIFS'.$this->filter_is_featured_shop;
			$shop_details = $shop_details->whereRaw('shop_details.is_featured_shop = ?', array($this->filter_is_featured_shop));
		}

		if($this->order_by != '') {
			if($this->order_by == 'recently_added')
			{
				$cache_key .= '_recently_added';
				$order_by_field = 'shop_details.id';
			}
			if(!$this->include_blocked_user_shop) {
				if($this->order_by == 'featured') {
					$cache_key .= '_featured';
					$order_by_field = 'shop_details.id';
					$shop_details = $shop_details->Where('users.is_featured_seller', '=', 'Yes');
				}
			}
			$shop_details = $shop_details->orderBy($order_by_field, 'DESC');
		}

		if($this->shops_per_page != '' && $this->shops_per_page > 0)
		{
			if(!HomeCUtil::cacheAllowed())
				$shop_details_result = $shop_details->paginate($this->shops_per_page);
			else{
				$page_name = (!Input::has('page') ? '1' :  Input::get('page'));
				$cache_key .= '_GPR'.$this->shops_per_page.'_'.$page_name;
				if( ! Cache::has($cache_key)) {
					$shops = array(
							'total' => $shop_details->get()->count(),
							'items' => $shop_details->paginate($this->shops_per_page)->getItems(),
							'perpage_count' => $this->shops_per_page,
							);
					Cache::put($cache_key, $shops, Config::get('generalConfig.cache_expiry_minutes'));
				}
				$shops = Cache::get($cache_key);
				$shop_details_result = Paginator::make($shops['items'], $shops['total'], $shops['perpage_count']);
			}
		}
		else
		{
			$cache_key .= 'GR';
			if (($shop_details_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$shop_details_result = $shop_details->get();
				HomeCUtil::cachePut($cache_key, $shop_details_result, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}

		/*if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr[$key]['id'] = $vlaues->id;
				$shop_details_arr[$key]['user_id'] = $vlaues->user_id;
				$shop_details_arr[$key]['shop_name'] = $vlaues->shop_name;
				$shop_details_arr[$key]['url_slug'] = $vlaues->url_slug;
				$shop_details_arr[$key]['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr[$key]['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr[$key]['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr[$key]['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr[$key]['shop_city'] = $vlaues->shop_city;
				$shop_details_arr[$key]['shop_state'] = $vlaues->shop_state;
				$shop_details_arr[$key]['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr[$key]['shop_country'] = $vlaues->shop_country;
				$shop_details_arr[$key]['shop_message'] = $vlaues->shop_message;
				$shop_details_arr[$key]['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr[$key]['image_name'] = $vlaues->image_name;
				$shop_details_arr[$key]['image_ext'] = $vlaues->image_ext;
				$shop_details_arr[$key]['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr[$key]['t_height'] = $vlaues->t_height;
				$shop_details_arr[$key]['t_width'] = $vlaues->t_width;
				$shop_details_arr[$key]['is_featured_shop'] = $vlaues->is_featured_shop;
			}
		}*/
		return $shop_details_result;
	}

	/**
	 * Getting users shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getUsersShopDetails($user_id)
	{
		$shop_details_arr = array();
		//$shop_details = UsersShopDetails::Select('id', 'user_id', 'is_shop_owner', 'shop_status', 'total_products', 'paypal_id')
									//->where('user_id', $user_id)
									//->get();
		$shop_details = CUtil::getUserDetails($user_id);
//		Log::info('=========user shop details============');
//		Log::info(print_r($shop_details,1));
//		Log::info('=========user shop details============');
		if(count($shop_details) > 0) {
			$shop_details_arr['user_id'] = $shop_details['id'];
			$shop_details_arr['is_shop_owner'] = $shop_details['is_shop_owner'];
			$shop_details_arr['shop_status'] = $shop_details['shop_status'];
			$shop_details_arr['total_products'] = $shop_details['total_products'];
			$shop_details_arr['paypal_id'] = $shop_details['paypal_id'];
			$shop_details_arr['is_banned'] = $shop_details['is_banned'];
		}
		return $shop_details_arr;
	}

	public function setShopFeaturedStatus($shop_id)
	{
	 	if(is_numeric($shop_id) && $shop_id > 0) {
			ShopDetails::whereRaw('id = ?', array($shop_id))->update(array('is_featured_shop' => $this->is_featured_shop));
		}
	}

	public function checkIsShopNameExist($user_id)
	{
		$shop_name = ShopDetails::where('user_id', '=', $user_id)->pluck('shop_name');
		return ($shop_name == '')? false : true;
	}

	public function checkIsShopPaypalUpdated($user_id)
	{
		return true;
		//$shop_paypal_id = UsersShopDetails::where('user_id', '=', $user_id)->pluck('paypal_id');
		$reponse = false;
		$details = $this->getUsersShopDetails($user_id);
		if(count($details)) {
			$reponse = ($details['paypal_id'] != '')? true : false;
		}
		return $reponse;
	}

	public function chkAndUpdateUserToBecomeSeller($user_id)
	{
		if($user_id != '' && $user_id <= 0)
			return false;

		$user_account_service = new UserAccountService();
		$user_info = $user_account_service->getUserinfo($user_id);
		if(count($user_info) > 0) {
			if($user_info->is_allowed_to_add_product == 'No') {
				$become_seller = true;
				$user_shop_details = $this->getUsersShopDetails($user_id);
				/*if(count($user_shop_details) > 0) {
					if($user_shop_details['paypal_id'] == '')
						$become_seller = false;
				}*/
				$shop_details = $this->getShopDetails($user_id);
				if(count($shop_details) > 0) {
					if($shop_details['shop_name'] == '' || $shop_details['url_slug'] == '')
						$become_seller = false;
				}
				if($become_seller) {
					$user_account_service->setUserToBecomeSeller($user_id);
				}
			}
		}
	}
}