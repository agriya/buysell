<?php

class UserCancellationPolicy {

	protected $user_id;

	protected $fields_arr = array();

	protected $detail_fields_arr = array();

	protected $filter_shop_id = '';

	protected $filter_shop_owner_id = '';

	protected $filter_shop_name = '';

	protected $filter_url_slug = '';

	protected $filter_shop_status = '';

	protected $filter_is_featured_shop = '';

	protected $shops_per_page = '';

	public function __construct()
	{
		//$this->shop_id = $shop_id;
		//$this->shopservice = new ShopService;
	}

	public function getUserId()
	{
		return $this->user_id;
	}
	public function setCancellationPolicyId($val)
	{
		$this->fields_arr['id'] = $val;
	}
	public function setUserId($val)
	{
		$this->fields_arr['user_id'] = $val;
	}

	public function setShopOwnerId($val)
	{
		$this->fields_arr['user_id'] = $val;
		$this->detail_fields_arr['user_id'] = $val;
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

	//Filters
	public function setFilterShopId($val)
	{
		$this->filter_shop_id = $val;
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

	public function save()
	{
		//Validation start
		$rules = $message = array();
		$validator = Validator::make($this->fields_arr, $rules, $message);

		if(!isset($this->fields_arr['id']) || $this->fields_arr['id'] == '' || $this->fields_arr['id'] <= 0)
		{
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return Response::json(array('error' => $errors));
			}
			//Validation End
			$cancellation_policy_id = CancellationPolicy::insertGetId($this->fields_arr);

			//$cancellation_policy_arr = array('user_id' => $this->fields_arr['user_id']);
			//$cancellation_policy_id = CancellationPolicy::insertGetId($cancellation_policy_arr);
		}
		else {
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return json_encode(array('status' => 'error', 'error_messages' => $errors));
			}
			//Validation End
			CancellationPolicy::whereRaw('id = ?', array($this->fields_arr['id']))->update($this->fields_arr);
			return json_encode(array('status' => 'success'));
		}
	}

	public function getCancellationPolicyDetails($user_id)
	{
		$cancellation_policy_det = CancellationPolicy::where('user_id','=',$user_id)->first();
		return $cancellation_policy_det;
	}


	public function getShopDetails($user_id)
	{
		$shop_details_arr = array();
		$shop_details = ShopDetails::Select('id', 'user_id', 'shop_name', 'url_slug', 'shop_slogan', 'shop_desc'
												, 'shop_address1', 'shop_address2', 'shop_city', 'shop_state'
												, 'shop_zipcode', 'shop_country', 'shop_message', 'shop_contactinfo'
												, 'image_name', 'image_ext', 'image_server_url', 't_height', 't_width'
												, 'cancellation_policy_text', 'cancellation_policy_filename'
												, 'cancellation_policy_filetype', 'cancellation_policy_server_url')
									->where('user_id', $user_id)
									->get();
		if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['shop_name'] = $vlaues->shop_name;
				$shop_details_arr['url_slug'] = $vlaues->url_slug;
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

			}
		}
		return $shop_details_arr;
	}

}