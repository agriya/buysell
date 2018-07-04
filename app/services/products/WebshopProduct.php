<?php
class ProductNotFoundException extends Exception {}
class InvalidProductIdException extends Exception {}

class WebshopProduct {

	protected $product_id;

	protected $fields_arr = array();

	protected $section_arr = array();

	protected $filter_product_ids = array();

	protected $url_slug = '';

	protected $filter_section_id = '';

	protected $filter_product_status = '';

	protected $filter_product_expiry = '';

	protected $filter_product_code = '';

	protected $filter_product_name = '';

	protected $filter_product_category = '';

	protected $filter_attribute_options  = array();

	protected $filter_attribute_values = array();

	protected $products_per_page = '';

	protected $products_limt = '';

	protected $filter_product_qty = '';

	protected $filter_stock_country = '';

	protected $filter_user_group_id = '';

	protected $filter_logged_user_id = '';

	protected $filter_user_allowed_to_add_product = '';

	protected $filter_product_from_price = '';

	protected $filter_product_to_price = '';

	protected $order_by = '';

	protected $filter_keyword = '';

	protected $filter_product_id_from = '';

	protected $filter_product_id_to = '';

	protected $filter_seller_code = '';

	protected $filter_product_added_from = '';

	protected $filter_product_added_to = '';

	protected $filter_product_ids_in = array();

	protected $filter_featured_product = '';

	protected $filter_shop_name = '';

	protected $sub_category_ids = array();

	protected $include_deleted = false;

	protected $include_blocked_user_products = false;

	protected $filter_product_free = '';

	protected $filter_download_product = '';

	public function __construct($product_id = '')
	{
		$this->product_id = $product_id;
	}

	public function getProductId()
	{
		return $this->product_id;
	}

	public function setProductId($val)
	{
		$this->product_id = $val;
	}

	public function setTitle($val)
	{
		$this->fields_arr['product_name'] = $val;
	}

	public function setDescription($val)
	{
		$this->fields_arr['product_description'] = $val;
	}

	public function setDateExpires($val)
	{
		$this->fields_arr['date_expires'] = $val;
	}

	public function setSupportContent($val)
	{
		$this->fields_arr['product_support_content'] = $val;
	}

	public function setSummary($val)
	{
		$this->fields_arr['product_highlight_text'] = $val;
	}

	public function setCategory($val)
	{
		$this->fields_arr['product_category_id'] = $val;
	}

	public function setSection($val)
	{
		$this->section_arr['section'] = $val;
	}

	public function setDemoUrl($val)
	{
		$this->fields_arr['demo_url'] = $val;
	}

	public function setDemoDetails($val)
	{
		$this->fields_arr['demo_details'] = $val;
	}

	public function setProductTags($val)
	{
		$this->fields_arr['product_tags'] = $val;
	}

	public function setStockCountry($val)
	{
		$this->fields_arr['stock_country_id'] = $val;
	}

	public function setStockQuantity($val)
	{
		$this->fields_arr['quantity'] = $val;
	}

	public function setSerialNumbers($val)
	{
		$this->fields_arr['serial_numbers'] = $val;
	}

	public function setMetaTitle($val)
	{
		$this->fields_arr['meta_title'] = $val;
	}

	public function setMetaDescription($val)
	{
		$this->fields_arr['meta_description'] = $val;
	}

	public function setMetaKeyword($val)
	{
		$this->fields_arr['meta_keyword'] = $val;
	}

	public function setIsFreeProduct($val)
	{
		$this->fields_arr['is_free_product'] = $val;
	}

	public function setPurchasePrice($val)
	{
		$this->fields_arr['purchase_price'] = $val;
    }

	public function setProductPrice($val)
	{
		$this->fields_arr['product_price'] = $val;
	}

	public function setProductPriceCurrency($val)
	{
		$this->fields_arr['product_price_currency'] = $val;
	}

	public function setPriceAfterDiscount($val)
	{
		$this->fields_arr['product_discount_price'] = $val;
	}

	public function setDiscountPriceFromDate($val)
	{
		$this->fields_arr['product_discount_fromdate'] = $val;
	}

	public function setDiscountPriceToDate($val)
	{
		$this->fields_arr['product_discount_todate'] = $val;
	}

	public function setGlobalTransactionFeeUsed($val)
	{
		$this->fields_arr['global_transaction_fee_used'] = $val;
	}

	public function setSiteTransactionFeePercent($val)
	{
		$this->fields_arr['site_transaction_fee_percent'] = $val;
	}

	public function setSiteTransactionFee($val)
	{
		$this->fields_arr['site_transaction_fee'] = $val;
	}

	public function setUseVariation($val)
	{
		$this->fields_arr['use_variation'] = $val;
	}

	public function setAcceptGiftwrape($val)
	{
		$this->fields_arr['accept_giftwrap'] = $val;
	}

	public function setAcceptGiftwrapMessage($val)
	{
		$this->fields_arr['accept_giftwrap_message'] = $val;
	}

	public function setGiftwrapType($val)
	{
		$this->fields_arr['giftwrap_type'] = $val;
	}

	public function setGiftwrapPricing($val)
	{
		$this->fields_arr['giftwrap_pricing'] = $val;
	}

	public function setFilterSectionId($val)
	{
		$this->filter_section_id = $val;
	}

	public function setFilterProductStatus($val)
	{
		$this->filter_product_status = $val;
	}

	public function setFilterProductExpiry($val = true)
	{
		$this->filter_product_expiry = $val;
	}

	public function setFilterProductFree($val = 'No')
	{
		$this->filter_product_free = $val;
	}

	public function setFilterDownloadProduct($val = 'No')
	{
		$this->filter_download_product = $val;
	}

	public function setFilterProductCode($val)
	{
		$this->filter_product_code = $val;
	}

	public function setFilterProductName($val)
	{
		$this->filter_product_name = $val;
	}

	public function setFilterProductCategory($val)
	{
		$this->filter_product_category = $val;
	}

	public function setFilterAttributeOptions($val)
	{
		$this->filter_attribute_options = $val;
	}

	public function setFilterAttributeValues($val)
	{
		$this->filter_attribute_values = $val;
	}

	public function setProductUserId($val)
	{
		$this->fields_arr['product_user_id'] = $val;
	}

	public function setProductPagination($val)
	{
		$this->products_per_page = $val;
	}

	public function setProductsLimit($val)
	{
		$this->products_limt = $val;
	}

	public function setDeliveryDays($val)
	{
		$this->fields_arr['delivery_days'] = $val;
	}

	public function setIsDownloadableProduct($val)
	{
		$this->fields_arr['is_downloadable_product'] = $val;
	}

	public function setUseCancellationPolicy($val)
	{
		$this->fields_arr['use_cancellation_policy'] = $val;
	}

	public function setUseDefaultCancellation($val)
	{
		$this->fields_arr['use_default_cancellation'] = $val;
	}

	public function setCancellationPolicyFileName($val)
	{
		$this->fields_arr['cancellation_policy_filename'] = $val;
	}

	public function setCancellationPolicyFileType($val)
	{
		$this->fields_arr['cancellation_policy_filetype'] = $val;
	}

	public function setCancellationPolicyServerUrl($val)
	{
		$this->fields_arr['cancellation_policy_server_url'] = $val;
	}

	public function setCancellationPolicyText($val)
	{
		$this->fields_arr['cancellation_policy_text'] = $val;
	}

	public function setShippingTemplate($val)
	{
		$this->fields_arr['shipping_template'] = $val;
	}
	public function setShippingFromCountry($val)
	{
		$this->fields_arr['shipping_from_country'] = $val;
	}
	public function setShippingFromZipCode($val)
	{
		$this->fields_arr['shipping_from_zip_code'] = $val;
	}
	public function setFilterProductQty($val)
	{
		$this->filter_product_qty = $val;
	}

	public function setFilterStockCountry($val)
	{
		$this->filter_stock_country = $val;
	}

	public function setFilterUserGroupId($val)
	{
		$this->filter_user_group_id = $val;
	}

	public function setFilterLoggedUserId($val)
	{
		$this->filter_logged_user_id = $val;
	}

	public function setFilterUserAllowedToAddProduct($val)
	{
		$this->filter_user_allowed_to_add_product = $val;
	}

	public function setFilterProductFromPrice($val)
	{
		$this->filter_product_from_price = $val;
	}

	public function setFilterProductToPrice($val)
	{
		$this->filter_product_to_price = $val;
	}

	public function setOrderByField($val)
	{
		$this->order_by = $val;
	}

	public function setFilterKeyword($val)
	{
		$this->filter_keyword = $val;
	}

	public function setFilterProductIdFrom($val)
	{
		$this->filter_product_id_from = $val;
	}

	public function setFilterProductIdTo($val)
	{
		$this->filter_product_id_to = $val;
	}

	public function setFilterSellerCode($val)
	{
		$this->filter_seller_code = $val;
	}

	public function setFilterProductAddedFrom($val)
	{
		$this->filter_product_added_from = $val;
	}

	public function setFilterProductAddedTo($val)
	{
		$this->filter_product_added_to = $val;
	}

	public function setFilterProductIdsIn($val)
	{
		$this->filter_product_ids_in = $val;
	}
	public function setCombinedFilterProductIdsIn($val)
	{
		if(isset($this->filter_product_ids_in))
			$this->filter_product_ids_in = array_unique(array_merge($this->filter_product_ids_in,$val));
		else
			$this->setFilterProductIdsIn($val);
	}

	public function setFilterFeaturedProduct($val)
	{
		$this->filter_featured_product = $val;
	}

	public function setFilterProductIds($val)
	{
		$this->filter_product_ids = $val;
	}

	public function setFilterShopName($val)
	{
		$this->filter_shop_name = $val;
	}

	public function setUrlSlug($val)
	{
		$this->url_slug = $val;
	}

	public function setIncludeDeleted($val = true)
	{
		$this->include_deleted = $val;
	}

	public function setIncludeBlockedUserProducts($val = true)
	{
		$this->include_blocked_user_products = $val;
	}

	public function addSectionName($section_name)
	{
		$logged_user_id = 0;
		$user_section_id = 0;
		if(count($this->fields_arr) > 0 && isset($this->fields_arr['product_user_id']))
		{
			$logged_user_id = $this->fields_arr['product_user_id'];
		}
		if($logged_user_id > 0)
		{
			$section_details = UserProductSection::Select('id', 'user_id')->whereRaw('section_name = ?', array($section_name))->first();
			if(count($section_details) > 0)
			{
				if($section_details->user_id != $logged_user_id)
				{
					//throw exception
				}
				$user_section_id = $section_details->id;
			}
			else
			{
				$data_arr = array('user_id' => $logged_user_id,
	                          	  'section_name' => $section_name,
	                          	  'status' => 'Yes',
	                          	  'date_added' => DB::raw('NOW()')         );
	            $user_section_id = UserProductSection::insertGetId($data_arr);
			}
		}
		return $user_section_id;
	}

	public function addSection($section_name)
	{
		$input_arr['section_name'] = $section_name;
		$logged_user_id = 0;
		if(count($this->fields_arr) > 0 && isset($this->fields_arr['product_user_id']))
		{
			$logged_user_id = $this->fields_arr['product_user_id'];
		}
		$rules_arr = array('section_name' => 'Required|unique:user_product_section,section_name,null,id,user_id,'.$logged_user_id);
		$message_arr = array('section_name.unique' => trans('products.webshop_section_already_exists'));
		$validator = Validator::make($input_arr, $rules_arr, $message_arr);
		if($validator->passes())
		{
			$data_arr = array('user_id' => $logged_user_id,
                          	  'section_name' => $section_name,
                          	  'status' => 'Yes',
                          	  'date_added' => DB::raw('NOW()')      );
            $user_section_id = UserProductSection::insertGetId($data_arr);
            return json_encode(array('status' => 'success', 'user_section_id' => $user_section_id));
		}
		else
		{
			$errors = $validator->getMessageBag()->toArray();
			return json_encode(array('status' => 'error', 'error_messages' => $errors['section_name']));
		}
	}

	public function save($importer_allow = 'no')
	{
		$p_id = 0;
		if(count($this->section_arr) > 0 && isset($this->section_arr['section']))
		{
			if(is_numeric($this->section_arr['section']))
			{
				$user_section_id = $this->section_arr['section'];
			}
			else
			{
				$user_section_id = $this->addSectionName($this->section_arr['section']);
			}
			$this->fields_arr['user_section_id'] = $user_section_id;
		}

		$validator_arr = $this->validateProductDetails($this->fields_arr, $importer_allow);
		if($this->url_slug != '')
			$this->fields_arr['url_slug'] = $this->url_slug;
		$filter_rules_arr = array_intersect_key($validator_arr['rules'], $this->fields_arr);
		$filter_messages_arr = $validator_arr['messages'];//array_intersect_key($validator_arr['messages'], $this->fields_arr);

		$price_error = '';
		if (isset($this->fields_arr['delivery_days']))
		{
			$is_free_product = Product::whereRaw('id = ?', array($this->product_id))->pluck('is_free_product');
			if ($is_free_product != 'Yes') {
				$price_details = $this->getGroupPriceDetailsById($this->product_id, 0, 1, 0, false);
				if ($price_details[0]['price'] == '') {
					$price_error = trans('products.enter_price_details');
				}
			}
		}

		$validator = Validator::make($this->fields_arr, $filter_rules_arr, $filter_messages_arr);
		if($validator->passes() && $price_error == '')
		{
			if($this->product_id == '')
			{
				if(count($this->fields_arr) > 0)
				{
					$product_code = $product_code = CUtil::generateRandomUniqueCode('P', 'product', 'product_code');
					$this->fields_arr['product_code'] = $product_code;
					if(isset($this->fields_arr['product_name']))
					{
						$url_slug = Str::slug($this->fields_arr['product_name']);
						if($this->url_slug != '')
						{
							$url_slug = $this->url_slug;
						}
						$this->fields_arr['url_slug'] = $url_slug;
					}

//					if(isset($this->fields_arr['is_free_product']) && $this->fields_arr['is_free_product'] == 'No')
//					{
////						if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
////						{
////							if(isset($this->fields_arr['product_discount_fromdate']) && $this->fields_arr['product_discount_fromdate'] != '')
////							{
////								$from_date = str_replace('/', '-', $this->fields_arr['product_discount_fromdate']);
////								$from_date = date('Y-m-d', strtotime($from_date));
////
////								$this->fields_arr['product_discount_fromdate'] =  $from_date;
////							}
////							else {
////								$this->fields_arr['product_discount_fromdate'] =  '0000-00-00';
////							}
////							if(isset($this->fields_arr['product_discount_todate']) && $this->fields_arr['product_discount_todate'] != '')
////							{
////								$to_date = str_replace('/', '-', $this->fields_arr['product_discount_todate']);
////								$to_date = date("Y-m-d", strtotime($to_date));
////
////								$this->fields_arr['product_discount_todate'] =  $to_date;
////							}
////							else {
////								$this->fields_arr['product_discount_todate'] =  '0000-00-00';
////							}
////						}
////
////						if(isset($this->fields_arr['product_price']) && $this->fields_arr['product_price'] > 0)
////						{
////							if(isset($this->fields_arr['product_price_currency']))
////							{
////								$product_price_currency = $this->fields_arr['product_price_currency'];
////							}
////							if($product_price_currency == '') {
////								$product_price_currency = Config::get('products.site_default_currency');
////							}
////							$this->fields_arr['product_price_currency'] = $product_price_currency;
////
////						 	$this->fields_arr['product_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_price'], $product_price_currency);
////
////							if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
////							{
////								$this->fields_arr['product_discount_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_discount_price'], $product_price_currency);
////							}
////						}
//					}

					$this->fields_arr['product_status'] = 'Draft';
					$this->fields_arr['product_added_date'] = DB::raw('NOW()');
					$this->fields_arr['last_updated_date'] = DB::raw('NOW()');

					$p_id = Product::insertGetId($this->fields_arr);

					//To add dumb data for product image
					$p_img_arr = array('product_id' => $p_id);
					$p_img_id = ProductImage::insertGetId($p_img_arr);
				}
			}
			else
			{
				if(count($this->fields_arr) > 0)
				{
					$p_id = $this->product_id;

					//To remove old category attribute values..
					if(isset($this->fields_arr['product_category_id']) && $this->fields_arr['product_category_id'] > 0)
					{
						$product_category_id = Product::whereRaw('id = ?', array($p_id))->pluck('product_category_id');
						if($product_category_id != $this->fields_arr['product_category_id'])
						{
							$this->removeProductCategoryAttribute();
						}
					}

					if(isset($this->fields_arr['is_free_product']) && $this->fields_arr['is_free_product'] == 'No')
					{
//						if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
//						{
//							if(isset($this->fields_arr['product_discount_fromdate']) && $this->fields_arr['product_discount_fromdate'] != '')
//							{
//								$from_date = str_replace('/', '-', $this->fields_arr['product_discount_fromdate']);
//								$from_date = date('Y-m-d', strtotime($from_date));
//
//								$this->fields_arr['product_discount_fromdate'] =  $from_date;
//							}
//							else {
//								$this->fields_arr['product_discount_fromdate'] =  '0000-00-00';
//							}
//
//							if(isset($this->fields_arr['product_discount_todate']) && $this->fields_arr['product_discount_todate'] != '')
//							{
//								$to_date = str_replace('/', '-', $this->fields_arr['product_discount_todate']);
//								$to_date = date("Y-m-d", strtotime($to_date));
//
//								$this->fields_arr['product_discount_todate'] =  $to_date;
//							}
//							else {
//								$this->fields_arr['product_discount_todate'] =  '0000-00-00';
//							}
//						}
//
//						if(isset($this->fields_arr['product_price']) && $this->fields_arr['product_price'] > 0)
//						{
//							if(isset($this->fields_arr['product_price_currency']))
//							{
//								$product_price_currency = $this->fields_arr['product_price_currency'];
//							}
//							if($product_price_currency == '') {
//								$product_price_currency = Config::get('products.site_default_currency');
//							}
//							$this->fields_arr['product_price_currency'] = $product_price_currency;
//
//						 	$this->fields_arr['product_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_price'], $product_price_currency);
//
//							if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
//							{
//								$this->fields_arr['product_discount_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_discount_price'], $product_price_currency);
//							}
//						}
					}
					$this->fields_arr['last_updated_date'] = DB::raw('NOW()');
					//print_r($this->fields_arr);die;
					Product::whereRaw('id = ?', array($this->product_id))->update($this->fields_arr);
				}
			}
			return json_encode(array('status' => 'success', 'product_id' => $p_id));
		}
		else
		{
			$error_msg = $validator->errors()->all();
			if (!$error_msg)
				$error_msg = array('price' => $price_error);
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
	}

	public function saveStocks($input_arr)
	{
		$rules_arr = $message_arr = array();

		$rules_arr += array(//'stock_country_id' => 'Required',
							'quantity' => 'Required|numeric|Min:0',
							//'serial_numbers' => 'Sometimes|Required|CheckEmptyLines|IsValidSerialNumbers:'.$input_arr['quantity'].'|IsDuplicateSerialNumbers'//|IsSerialNumberExists:'.$input_arr['product_id'].','.$input_arr['stock_country_id'] to check in db
							);

		$message_arr += array(//'stock_country_id.required' => 'Country required',
							'quantity.required' => 'Quantity required',
							'serial_numbers.required' => 'Serial numbers required',
							'serial_numbers.check_empty_lines' => 'There are some empty lines. Enter each serial number in sperate lines without empty line',
							'serial_numbers.is_valid_serial_numbers' => 'Serial numbers count must be equal to the entered Quantity.',
							'serial_numbers.is_duplicate_serial_numbers' => 'Serial numbers having duplicate entries'
							);//'serial_numbers.is_serial_number_exists' => 'Serial numbers exists already for other products/country.'

		$validator = Validator::make($input_arr, $rules_arr, $message_arr);

		if ($validator->fails()) {
			$error_msg = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
		else {
			/*$stock_country_id = 38;//country id for china in currency_exchange_rate tbl
			if($input_arr['stock_country_id'] == 'No') {
				$stock_country_id = 153;//country id for pakistan in currency_exchange_rate tbl
			}*/
			//$stock_country_id = $input_arr['stock_country_id'];
			//To add stocks
			//if(isset($stock_country_id) && $stock_country_id > 0)
			//{
				$stocks['quantity'] = $input_arr['quantity'];
				//$stocks['stock_country_id'] = $stock_country_id;
				$stocks['serial_numbers'] = $input_arr['serial_numbers'];
				$stocks['date_updated'] = DB::raw('NOW()');

				$count = ProductStocks::whereRaw('product_id = ? ', array($input_arr['product_id']))->count();//AND stock_country_id = ?
				if($count == 0) {
					$stocks['product_id'] = $input_arr['product_id'];
					//echo "<pre>";print_r($stocks);echo "</pre>";exit;
					$product_id = ProductStocks::insertGetId($stocks);
				}
				else {
					ProductStocks::whereRaw('product_id = ?', array($input_arr['product_id']))->update($stocks);//AND stock_country_id = ?
				}
			//}
			return json_encode(array('status' => 'success', 'product_id' => $input_arr['product_id']));
		}
	}

	public function addStocks($input_arr)
	{
		$rules_arr = $message_arr = array();

		if(isset($input_arr['stock_id']) && $input_arr['stock_id'] > 0) {
			$rules_arr += array('stock_country_id' => 'Required|Unique:product_stocks,stock_country_id,'.$input_arr['stock_id'].',id,product_id,'.$input_arr['p_id']);
		}
		else {
			$rules_arr += array('stock_country_id' => 'Required');
		}

		$rules_arr += array('quantity' => 'Required|numeric|Min:0',
							'serial_numbers' => 'Required|CheckEmptyLines|IsValidSerialNumbers:'.$input_arr['quantity']);

		$message_arr += array('stock_country_id.required' => 'Country required',
							'stock_country_id.unique' => 'The stock country already added.',
							'quantity.required' => 'Quantity required',
							'serial_numbers.required' => 'Serial numbers required',
							'serial_numbers.check_empty_lines' => 'There are some empty lines. Enter each serial number in sperate lines without empty line',
							'serial_numbers.is_valid_serial_numbers' => 'Serial numbers count must be equal to the entered Quantity.');

		$validator = Validator::make($input_arr, $rules_arr, $message_arr);

		if ($validator->fails()) {
			$error_msg = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
		else {
			//To add stocks
			$p_id = $input_arr['p_id'];

			$stock_country_id = 38;//country id for china in currency_exchange_rate tbl
			if($input_arr['stock_country_id'] == 'No') {
				$stock_country_id = 153;//country id for pakistan in currency_exchange_rate tbl
			}

			if(isset($stock_country_id) && $stock_country_id != '')
			{
				$stocks['stock_country_id'] = $stock_country_id;
				$stocks['quantity'] = $input_arr['quantity'];
				$stocks['serial_numbers'] = $input_arr['serial_numbers'];
				$stocks['date_updated'] = DB::raw('NOW()');

				if(isset($input_arr['stock_id']) && $input_arr['stock_id'] > 0) {
					ProductStocks::whereRaw('id = ?', array($input_arr['stock_id']))->update($stocks);
				}
				else {
					$count = ProductStocks::whereRaw('product_id = ? and stock_country_id = ?', array($p_id, $stock_country_id))->count();
					if($count == 0) {
						$stocks['product_id'] = $p_id;
						ProductStocks::insertGetId($stocks);
					}
					else {
						$stocks['quantity'] = DB::raw('quantity + '.$input_arr['quantity']);
						$stocks['serial_numbers'] = DB::raw('CONCAT(serial_numbers, \'\n\',\''.addslashes($input_arr['serial_numbers']).'\')');
						ProductStocks::whereRaw('product_id = ? and stock_country_id = ?', array($p_id, $stock_country_id))->update($stocks);
					}
				}
			}
			return json_encode(array('status' => 'success', 'product_id' => $p_id));
		}
	}

	public function deleteProductStocks($stock_id)
	{
		ProductStocks::whereRaw('id = ?', array($stock_id))->delete();
		return true;
	}
	public function countProductStocks($product_id ,$cookie_stock_country_id)
	{
		$cache_key = 'CPS_'.$product_id.'_'.$cookie_stock_country_id;
		if (($count = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$count = ProductStocks::whereRaw('product_id = ? AND stock_country_id = ?', array($product_id, $cookie_stock_country_id))->count();
			HomeCUtil::cachePut($cache_key, $count, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $count;
	}

	public function deleteProductStocksByProductCountry($product_id)
	{
		ProductStocks::whereRaw('product_id = ? ', array($product_id))->delete();//AND stock_country_id = ?, $stock_country_id
		return true;
	}

	public function getProductStocks($product_id)
	{
		$stock_details_arr = array();
		$stock_details = ProductStocks::Select('id', 'product_id', 'stock_country_id', 'quantity', 'serial_numbers')
									->whereRaw('product_id = ? AND (stock_country_id = 38 || stock_country_id = 153)', array($product_id))
									->take(1)
									->orderBy('date_updated', 'DESC')
									->get();
		if(count($stock_details) > 0) {
			foreach($stock_details as $key => $vlaues) {
				$stock_details_arr['id'] = $vlaues->id;
				$stock_details_arr['product_id'] = $vlaues->product_id;
				$stock_details_arr['stock_country_id'] = $vlaues->stock_country_id;
				$stock_details_arr['quantity'] = $vlaues->quantity;
				$stock_details_arr['serial_numbers'] = $vlaues->serial_numbers;
			}
		}
		return $stock_details_arr;
	}

	public function getProductStockCountries($product_id)
	{
		$stock_details = ProductStocks::where('product_id', $product_id)->lists('stock_country_id');
		return $stock_details;
	}

	public function getProductStocksById($p_id, $stock_id)
	{
		$stock_details_arr = array();
		$stock_details = ProductStocks::Select('id', 'product_id', 'stock_country_id', 'quantity', 'serial_numbers')
									->whereRaw('id = ? AND product_id = ?', array($stock_id, $p_id))
									->take(1)
									->get();
		if(count($stock_details) > 0) {
			foreach($stock_details as $key => $vlaues) {
				$stock_details_arr['id'] = $vlaues->id;
				$stock_details_arr['product_id'] = $vlaues->product_id;
				$stock_details_arr['stock_country_id'] = $vlaues->stock_country_id;
				$stock_details_arr['quantity'] = $vlaues->quantity;
				$stock_details_arr['serial_numbers'] = $vlaues->serial_numbers;
			}
		}
		return $stock_details_arr;
	}

	public function getProductStocksList($product_id)
	{
		$stock_details_arr = array();
		$stock_details = ProductStocks::Select('id', 'product_id', 'stock_country_id', 'quantity', 'serial_numbers')
									->where('product_id', $product_id)
									->orderBy('date_updated', 'DESC')
									->get();
		if(count($stock_details) > 0) {
			foreach($stock_details as $key => $vlaues) {
				$stock_details_arr[$key]['id'] = $vlaues->id;
				$stock_details_arr[$key]['product_id'] = $vlaues->product_id;
				$stock_details_arr[$key]['stock_country_id'] = $vlaues->stock_country_id;
				$stock_details_arr[$key]['quantity'] = $vlaues->quantity;
				$stock_details_arr[$key]['serial_numbers'] = $vlaues->serial_numbers;
			}
		}
		return $stock_details_arr;
	}

	public function insertProductAttribute($attribute_id, $attribute_value)
	{
		$attr = new Attribute();
		if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		$attribute_details = $attr->getAttributeDetails($attribute_id);
	 		if(count($attribute_details) > 0)
	 		{
	 			$rules_arr = $message_arr = array();
	 			$add_data = true;
	 			$error_message = '';
	 			$id = $attribute_id;
				$cache_key = 'attribute_'.$id;
				$input_arr[$key] = $attribute_value;
				if($attribute_details['validation_rules'] != '')
				{
					$rule_str = str_replace('minlength-', 'min:', $attribute_details['validation_rules']);
					$rule_str = str_replace('maxlength-', 'max:', $rule_str);
					$rules_arr[$key] = $rule_str;

					$message_arr[$key.'.required'] = trans('products.product_attribute_required', array('attribute_label' => $attribute_details['attribute_label']));
					$message_arr[$key.'.alpha'] = trans('products.product_attribute_alpha', array('attribute_label' => $attribute_details['attribute_label']));
					$message_arr[$key.'.numeric'] = trans('products.product_attribute_numeric', array('attribute_label' => $attribute_details['attribute_label']));
				}
				if(count($rules_arr) > 0)
				{
					$validator = Validator::make($input_arr, $rules_arr, $message_arr);
					if($validator->fails())
					{
						$add_data = false;
						$error_message = $validator->errors()->all();
					}
				}

	 			if($add_data)
	 			{
				 	$data_arr = array('product_id' => $this->product_id,
							  'attribute_id' => $attribute_id,
							  'attribute_value' => $attribute_value
							);
					ProductAttributesValues::insertGetId($data_arr);
					return json_encode(array('status' => 'success'));
				}
				else
				{
					return json_encode(array('status' => 'error', 'error_messages' => $error_message));
				}
			}
		}
	}

	public function insertAttributeOption($attribute_id, $attribute_options = array())
	{
		if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		if(is_array($attribute_options))
	 		{
	 			foreach($attribute_options as $option)
	 			{
	 				//Get Attribute option id
	 				$attr_options = ProductAttributeOptions::Select('id')->whereRaw('option_label = ?', array($option))->first();
	 				if(count($attr_options) > 0)
	 				{
					 	$attr_options_id = $attr_options->id;
					 	$data_arr = array('product_id' => $this->product_id,
										  'attribute_id' => $attribute_id,
								          'attribute_options_id' => $attr_options_id
										  );
						ProductAttributesOptionValues::insertGetId($data_arr);
					}
				}
			}
	 	}
	}

	public function insertAttributeOptionByOptionId($attribute_id, $attribute_option_id)
	{
		$data_arr = array('product_id' => $this->product_id,
						'attribute_id' => $attribute_id,
						'attribute_options_id' => $attribute_option_id);
		ProductAttributesOptionValues::insertGetId($data_arr);
	}

	public function checkProductHasAttribute($category_id)
	{
		$category_ids = Products::getTopLevelCategoryIds($category_id);
		$cat_arr = explode(',', $category_ids);
		if(count($cat_arr) > 0)
		{
			$a_count = ProductCategoryAttributes::whereIn('category_id', $cat_arr)->count();
			if($a_count > 0)
			{
				return true;
			}
		}
		return false;
	}

	public function validateDownloadProduct($p_id)
	{
		if(Config::get('products.download_files_is_mandatory'))
		{
			$count = ProductResource::whereRaw('product_id = ? AND resource_type = ?', array($p_id, 'Archive'))->count();
			return ($count == 0) ? false : true;
		}
		return true;
	}

	public function validateProductDetails($input_arr, $importer_accept = 'no')
	{
		$rules_arr = $message_arr = array();

		$rules_arr += array('product_name' => 'Required|min:'.Config::get("products.title_min_length").'|max:'.Config::get("products.title_max_length"),
							'product_category_id' => 'Required',
							'product_tags' => 'Required',
							'product_highlight_text' => 'max:'.Config::get("products.summary_max_length"),
							'demo_url' => 'url',
							'delivery_days' => 'min:0|integer',
							'purchase_price' => 'IsValidPrice|numeric|Min:0'
						);
		if($this->url_slug != '') {
			$product_id = (isset($this->product_id) && $this->product_id > 0) ? $this->product_id : null;
			$rules_arr += array('url_slug' => 'IsValidSlugUrl|unique:product,url_slug,'.$product_id.',id');
		}
		//To validate section, only if input from user form
		//if(isset($input_arr['user_section_id'])  && $input_arr['user_section_id'] > 0)
		//{
			//$rules_arr['user_section_id'] = 'exists:user_product_section,id,user_id,'.$input_arr['product_user_id'];
		//}

		if(isset($input_arr['is_free_product']))
		{
//			$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
//			if($is_free_product != 'Yes')
//			{
//			############## NOT NEED AS WE HAVE GROUP PRICE RANGES ########################
////				$rules_arr += array('product_price' => 'Required|IsValidPrice|numeric|Min:1',
////								  'product_discount_price' => 'IsValidPrice|numeric|Max:'.$input_arr['product_price']
////							 );
////				if(isset($input_arr['product_discount_price']) && $input_arr['product_discount_price'] > 0)
////				{
////					$date_format = 'd/m/Y';
////					if(isset($input_arr['product_discount_fromdate']))
////					{
////						$rules_arr['product_discount_fromdate'] = 'date_format:VAR_DATE_FORMAT';
////					}
////					if(isset($input_arr['product_discount_todate']) && isset($input_arr['product_discount_fromdate']))
////					{
////						//check validation from database?..
////						$from_date = str_replace('/', '-', $input_arr['product_discount_fromdate']);
////						$from_date = date('Y-m-d', strtotime($from_date));
////
////						$to_date = str_replace('/', '-', $input_arr['product_discount_todate']);
////						$to_date = date('Y-m-d', strtotime($to_date));
////						$rules_arr['product_discount_todate'] = 'date_format:VAR_DATE_FORMAT|CustAfter:'.$from_date.','.$to_date;
////						//To replace the datre format
////						$rules_arr['product_discount_fromdate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_fromdate']);
////						$rules_arr['product_discount_todate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_todate']);
////					}
////				}
////				$message_arr += array('product_price.is_valid_price' => trans('products.product_section_already_exists'),
////									'purchase_price.required' => trans('products.purchase_price_required'),
////									'product_price.required' => trans('products.prodcut_price_required'),
////									'product_discount_price.is_valid_price' => trans('products.product_invalid_discount'),
////									'product_price.min' => trans('products.product_price_greater_than_zero'),
////									'product_discount_price.max' => trans('products.product_discount_less_than_price'),
////									'product_discount_todate.cust_after' => trans('products.prodcut_discount_to_date_greater'),
////									'product_discount_fromdate.date_format' => trans('products.product_ivalid_from_date_format'),
////									'product_discount_fromdate.required' => trans('products.product_discount_from_date_required'),
////									'product_discount_todate.date_format' => trans('products.product_ivalid_to_date_format'),
////									'product_discount_todate.required' => trans('products.product_discount_to_date_required')
////								);
//				############## NOT NEED AS WE HAVE GROUP PRICE RANGES ########################
//
//			}
		}

		if(isset($input_arr['product_category_id']) && is_numeric($input_arr['product_category_id']))
		{
			$attr_arr = $this->getAttributesList($input_arr['product_category_id']);
			foreach($attr_arr AS $key => $val)
			{
				$id = $val['attribute_id'];
				$cache_key = 'attribute_'.$id;
				if($val['validation_rules'] != '')
				{
					$rule_str = str_replace('minlength-', 'min:', $val['validation_rules']);
					$rule_str = str_replace('maxlength-', 'max:', $rule_str);
					$rules_arr[$key] = $rule_str;
					$message_arr[$key.'.required'] = trans('products.product_attribute_required', array('attribute_label' => $val['attribute_label']));//$val['attribute_label'].' required';
					$message_arr[$key.'.alpha'] = trans('products.product_attribute_alpha', array('attribute_label' => $val['attribute_label']));//$val['attribute_label'].' should contain alphabets only';
					$message_arr[$key.'.numeric'] = trans('products.product_attribute_numeric', array('attribute_label' => $val['attribute_label']));//$val['attribute_label'].' should contain numeric only';
				}
			}
		}
		if($importer_accept != 'yes')
		{
			$message_arr += array('product_name.min' => trans('products.product_name_min', array('min_length' => Config::get("products.title_min_length"))),
								'product_name.max' => trans('products.product_name_max', array('max_length' => Config::get("products.title_max_length"))),
								'product_name.required' => trans('products.product_title_required'),
								'product_category_id.required' => trans('products.product_category_required'),
								'product_tags.required' => trans('products.product_tags_required'),
								'product_highlight_text.max' => trans('products.product_summary_max', array('max_length' => Config::get("products.summary_max_length"))),
								'demo_url.url' => trans('products.product_invlid_demo_url'),
								'delivery_days.numeric' => trans('products.product_delivery_days_numeric'));
			if($this->url_slug != '') {
				$message_arr += array('url_slug.is_valid_slug_url' => trans('products.enter_valid_url_slug'),
										'url_slug.unique' => trans('products.url_slug_already_been_taken'),);
			}
		}else{
			$message_arr += array('product_name.min' => 'product_name_min_import',
								'product_name.max' => 'product_name_max_import',
								'product_name.required' => 'product_title_required',
								'product_category_id.required' => 'product_category_required',
								'product_tags.required' => 'product_tags_required',
								'product_highlight_text.max' => 'product_summary_max_import',
								'demo_url.url' => 'product_invlid_demo_url',
								'delivery_days.numeric' => 'product_delivery_days_numeric');
			if($this->url_slug != '') {
				$message_arr += array('url_slug.is_valid_slug_url' => 'enter_valid_url_slug',
									  'url_slug.unique' => 'url_slug_already_been_taken',);
			}
		}
		return array('rules' => $rules_arr, 'messages' => $message_arr);
	}

	public function updateUserTotalProducts($user_id)
	{
		$p_count = $this->getTotalProduct($user_id);
		User::where('id', '=', $user_id)->update( array('total_products' => $p_count));
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function getTotalProduct($user_id)
	{
		return Product::whereRaw('product_user_id = ? AND product_status = ?', array($user_id, 'Ok'))->count();
	}

	public function getProductCategoryAttributeValue($p_id, $product_category_id)
	{
		$input_arr = array();
		$attr_arr = $this->getAttributesList($product_category_id);
		foreach($attr_arr AS $key => $val)
		{
			$id = $val['attribute_id'];
			$cache_key = 'attribute_'.$id;
			if($val['validation_rules'] != '')
			{
				$attr_type = $val['attribute_question_type'];
				switch($attr_type)
				{
					case 'text':
					case 'textarea':
						$input_arr[$key] = ProductAttributesValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->pluck('attribute_value');
						break;

					case 'select':
					case 'option': // radio button
					case 'multiselectlist':
					case 'check': // checkbox
						$option_val = ProductAttributesOptionValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->get( array('attribute_options_id'));
						foreach($option_val AS $option)
						{
							$input_arr[$key][] = $option->attribute_options_id;
						}
						break;
				}
			}
		}
		return $input_arr;
	}

	public function getProductCategoryAttributeValueLabels($p_id, $product_category_id)
	{
		$input_arr = array();
		$attr_arr = $this->getAttributesList($product_category_id);
		$attribute_list_details = array();
		$inc=0;
		foreach($attr_arr AS $key => $val)
		{
			$id = $val['attribute_id'];
			$attr_type = $val['attribute_question_type'];
			$value = '';
			switch($attr_type)
			{
				case 'text':
				case 'textarea':
					$value = ProductAttributesValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->pluck('attribute_value');
					break;

				case 'select':
				case 'option': // radio button
				case 'multiselectlist':
				case 'check': // checkbox
					$option_val = ProductAttributesOptionValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->get( array('attribute_options_id'));
					$option_value = array();
					foreach($option_val AS $option)
					{
						$option_value[] = $this->getAttributeLabel($option['attribute_options_id']);
					}
					$value = (isset($option_value) && !empty($option_value))?implode(',',$option_value):'';
					break;
			}
			if($value != '')
			{
				$attribute_list_details[$inc]['id'] = $id;
				$attribute_list_details[$inc]['attribute_label'] = $val['attribute_label'];
				$attribute_list_details[$inc]['attribute_value'] = $value;
				$inc++;
			}
		}
		return $attribute_list_details;
	}

	public function getAttributeLabel($attr_id)
	{
		return ProductAttributeOptions::whereRaw('id = ?', array($attr_id))->pluck('option_label');
	}


	public function validateUpdate()
	{
		$allow_publish = false;
		$check_attributes = true;
		$download_product = true;
		$check_variation = true;
		$p_details = $this->getProductDetails(0, false);
		if(count($p_details) > 0)
		{
			if(isset($p_details['product_category_id'])) //No need to check for add product page
			{
				 $has_attr = $this->checkProductHasAttribute($p_details['product_category_id']);
				 if(!$has_attr)
				 {
				 	$check_attributes = false;
				 }
			}

			if(strtolower($p_details['is_downloadable_product']) == "yes" && !$this->validateDownloadProduct($this->product_id))
			{
				$download_product = false;
			}

			// Check variation error
			if(CUtil::chkIsAllowedModule('variations'))
			{
				if($p_details['use_variation'] > 0 && $p_details['variation_group_id'] > 0)
				{
					$variations_service = new VariationsService();
					if(!$variations_service->chkIsDefaultMatrixExist($this->product_id))
					{
						$check_variation = false;
					}
				}
			}

			$input_arr = $p_details;
			if($check_attributes)
			{
				$input_arr += $this->getProductCategoryAttributeValue($this->product_id, $p_details['product_category_id']);
			}

			$validator_arr = $this->validateProductDetails($input_arr);
			$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
			if($validator->passes())
			{
				if(!$download_product)
				{
					return json_encode(array('status' => 'error', 'error_messages' => trans('products.product_add_downlodable')));
				}
				if(strtolower($p_details['is_downloadable_product']) != "yes")
				{
					if(!isset($p_details['shipping_from_country']) || (isset($p_details['shipping_from_country']) && ($p_details['shipping_from_country'] == null || !is_numeric($p_details['shipping_from_country'])))) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_from_country_required')));
					}

					if(!isset($p_details['shipping_from_zip_code']) || (isset($p_details['shipping_from_zip_code']) && (!$p_details['shipping_from_zip_code'])) ) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_from_zip_code_required')));
					}

					if(isset($p_details['shipping_template']) && $p_details['shipping_template'] == 0) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_template_required')));
					}
				}

				// Check variation error
				if(!$check_variation)
				{
					return json_encode(array('status' => 'error', 'error_messages' => Lang::get('variations::variations.deafult_variation_none_err')));
				}

				//Can publish
				if($p_details['product_status'] != 'Ok' && Config::get('products.product_auto_approve'))
				{
					$alert_message = trans('products.product_successfully_published');
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
				else if($p_details['product_status'] != 'Ok' && $p_details['product_status'] != 'ToActivate')
				{
					$alert_message = trans('products.product_submitted_for_approval');
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
				else
				{
					$alert_message = trans('products.product_successfully_updated');
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
			}
			else
			{
				$error_msg = $validator->errors()->all();
				if(!$download_product)
				{
					$error_msg[] = trans('products.product_add_downlodable');
				}
				return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
			}
		}
		return json_encode(array('status' => 'error', 'error_messages' => trans('products.product_invalid_id')));
	}


	public function publish()
	{
		$allow_publish = false;
		$check_attributes = true;
		$download_product = true;
		$check_variation = $check_variation_stock = true;

		$p_details = $this->getProductDetails(0, false);
		if(count($p_details) > 0)
		{
//			if($p_details['product_discount_price'] > 0)
//			{
//				$p_details['product_discount_fromdate'] = date('d/m/Y', strtotime($p_details['product_discount_fromdate']));
//				$p_details['product_discount_todate'] = date('d/m/Y', strtotime($p_details['product_discount_todate']));
//			}
			if(isset($p_details['product_category_id'])) //No need to check for add product page
			{
				 $has_attr = $this->checkProductHasAttribute($p_details['product_category_id']);
				 if(!$has_attr)
				 {
				 	$check_attributes = false;
				 }
			}

			if(strtolower($p_details['is_downloadable_product']) == "yes" && !$this->validateDownloadProduct($this->product_id))
			{
				$download_product = false;
			}

			if(CUtil::chkIsAllowedModule('variations'))
			{
				if($p_details['use_variation'] > 0 && $p_details['variation_group_id'] > 0)
				{
					$variations_service = new VariationsService();
					if(!$variations_service->chkIsDefaultMatrixExist($this->product_id))
					{
						$check_variation = false;
					}
					if(!$variations_service->chkIsVariationStockExist($this->product_id))
					{
						$check_variation_stock = false;
					}
				}
			}

			$input_arr = $p_details;
			if($check_attributes)
			{
				$input_arr += $this->getProductCategoryAttributeValue($this->product_id, $p_details['product_category_id']);
			}

			$validator_arr = $this->validateProductDetails($input_arr);
			$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
			if($validator->passes())
			{
				if(!$download_product)
				{
					return json_encode(array('status' => 'error', 'error_messages' => trans('products.product_add_downlodable')));
				}
				if(strtolower($p_details['is_downloadable_product']) != "yes")
				{
					if(!isset($p_details['shipping_from_country']) || (isset($p_details['shipping_from_country']) && ($p_details['shipping_from_country'] == null || !is_numeric($p_details['shipping_from_country'])))) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_from_country_required')));
					}

					if(!isset($p_details['shipping_from_zip_code']) || (isset($p_details['shipping_from_zip_code']) && (!$p_details['shipping_from_zip_code'])) ) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_from_zip_code_required')));
					}

					if(isset($p_details['shipping_template']) && $p_details['shipping_template'] == 0) {
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.shipping_template_required')));
					}
				}

				// Check variation error
				if(!$check_variation)
				{
					return json_encode(array('status' => 'error', 'error_messages' => Lang::get('variations::variations.deafult_variation_none_err')));
				}
				elseif(!$check_variation_stock)
				{
					return json_encode(array('status' => 'error', 'error_messages' => Lang::get('variations::variations.variation_stock_none_err')));
				}

				//Can publish
				if($p_details['product_status'] != 'Ok' && Config::get('products.product_auto_approve'))
				{
					$data_arr['product_status'] = 'Ok';
					$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
					HomeCUtil::forgotMultiCacheKey($array_multi_key);
					$date_activated = Product::whereRaw('id = ?', array($this->product_id))->pluck('date_activated');
					if($date_activated == '0')
					{
						$data_arr['date_activated'] = DB::raw('UNIX_TIMESTAMP(NOW())');
					}
					Product::whereRaw('id = ?', array($this->product_id))->update($data_arr);

					$this->updateUserTotalProducts($p_details['product_user_id']);

					$alert_message = trans('products.product_successfully_published');
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
				else if($p_details['product_status'] != 'Ok' && $p_details['product_status'] != 'ToActivate')
				{
					$data_arr['product_status'] = 'ToActivate';
					Product::whereRaw('id = ?', array($this->product_id))->update($data_arr);
					$alert_message = trans('products.product_submitted_for_approval');
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
			}
			else
			{
				$error_msg = $validator->errors()->all();
				if(!$download_product)
				{
					$error_msg[] = trans('products.product_add_downlodable');
				}
				return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
			}
		}
		return json_encode(array('status' => 'error', 'error_messages' => trans('products.product_invalid_id')));
	}

	public function insertDownloadFile($file_name, $ext, $title)
	{
	    $data_arr = array('product_id' => $this->product_id,
	 		  		'resource_type' => 'Archive',
					'filename' => $file_name,
					'ext' => $ext,
					'title' => $title,
					'is_downloadable'=> 'Yes');
		ProductResource::insertGetId($data_arr);
	}

	public function insertPreviewFiles($file_name, $ext, $title, $server_url, $org_width, $org_height, $large_width, $large_height, $thumb_width, $thumb_height, $small_width, $small_height)
	{
		$data_arr = array('product_id' => $this->product_id,
	 		  		'resource_type' => 'Image',
					'filename' => $file_name,
					'ext' => $ext,
					'title' => $title,
					'width' => $org_width,
					'height' => $org_height,
					'l_width' => $large_width,
					'l_height' => $large_height,
					't_width' => $thumb_width,
					't_height' => $thumb_width,
					's_width' => $small_width,
					's_height' => $small_height,
					'server_url' => $server_url,
					'is_downloadable' => 'No'
					);
	   $resource_id = ProductResource::insertGetId($data_arr);
		return $resource_id;
	}

	public function updateProductThumbImage($thumbnail_title, $thumbnail_img_name, $thumbnail_ext, $width, $height, $s_width, $s_height, $t_width, $t_height, $l_width, $l_height)
	{
		$data_arr = array('thumbnail_img' => $thumbnail_img_name,
						 'thumbnail_ext' =>	$thumbnail_ext,
						 'thumbnail_width' => $width,
						 'thumbnail_height' => $height,
						 'thumbnail_s_width' => $s_width,
						 'thumbnail_s_height' => $s_height,
						 'thumbnail_t_width' => $t_width,
						 'thumbnail_t_height' => $t_height,
						 'thumbnail_l_width' => $l_width,
						 'thumbnail_l_height' => $l_height,
						 'thumbnail_title' => $thumbnail_title);
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
	}

	public function updateProductDefaultImage($default_title, $default_img_name, $default_ext, $width, $height, $s_width, $s_height, $t_width, $t_height, $l_width, $l_height)
	{
		$data_arr = array('default_img' => $default_img_name,
						'default_ext' => $default_ext,
						'default_width' => $width,
						'default_height' => $height,
						'default_s_width' => $s_width,
						'default_s_height' => $s_height,
						'default_t_width' => $t_width,
						'default_t_height' => $t_height,
						'default_l_width' => $l_width,
						'default_l_height' => $l_height,
						'default_title' => $default_title);
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
	}

	public function changeStatus($product_status = 'Draft')
	{
	 	if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		$update_arr['product_status'] = $product_status;
	 		if($product_status == 'Ok')
	 		{
	 			$update_arr['date_activated'] = DB::raw('UNIX_TIMESTAMP(NOW())');
			}
			else if($product_status == 'Draft')
			{
				$update_arr['last_updated_date'] = DB::raw('NOW()');
			}
			Product::whereRaw('id = ?', array($this->product_id))->update($update_arr);

			// clear cache, for reflect update
			$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}
	}

	public function getTotalProducts($user_id)
	{
		$product_cnt_qry = Product::Select('product.id');
		$cache_key = 'total_products)_'.$user_id ;

		if(!$this->include_blocked_user_products) {
			$cache_key .= '_IBUD';
			$product_cnt_qry = $product_cnt_qry->join('users', function($join)
									 {
										 $join->on('product.product_user_id', '=', 'users.id');
										 $join->where('users.is_banned', '=', 0);
										 $join->where('users.shop_status', '=', 1);
									 });
		}

		$product_cnt_qry = $product_cnt_qry->whereRaw('product_user_id = ?', array($user_id));

		if(!$this->include_deleted) {
			$cache_key .= '_ID';
			$product_cnt_qry = $product_cnt_qry->whereRaw('product.product_status != ?', array('Deleted'));
		}

		if($this->filter_product_status != '') {
			$cache_key .= '_FPS';
			$product_cnt_qry = $product_cnt_qry->whereRaw('product_status = ?', array($this->filter_product_status));
		}

		if(isset($this->filter_product_expiry) && $this->filter_product_expiry) {
			$cache_key .= '_FPE';
			$product_cnt_qry = $product_cnt_qry->where('product.date_expires', '>=', '0000-00-00 00:00:00')->where('product.date_expires', '>=', date('Y-m-d'));
		}
		if (($shop_count = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$shop_count = $product_cnt_qry->count();
			HomeCUtil::cachePut($cache_key, $shop_count, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $shop_count;
	}

	public function getShopProductSalesCount($user_id)
	{
		$product_cnt_qry = Product::whereRaw('product_user_id = ?', array($user_id));
		if($this->filter_product_status != '') {
			$product_cnt_qry = $product_cnt_qry->whereRaw('product_status = ?', array($this->filter_product_status));
		}
		$shop_count = $product_cnt_qry->sum('product_sold');
		return $shop_count;
	}

	/**
	 * Getting product list
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getProductsList($user_id = 0, $allow_cache = true)
	{
		$product_details_arr = array();
		$cache_key = 'product_list_'.$user_id;
		$product_details = Product::Select('product.id', 'product.product_code', 'product.product_name', 'product.product_description'
											, 'product.product_support_content', 'product.meta_title', 'product.meta_keyword'
											, 'product.meta_description', 'product.product_highlight_text', 'product.product_slogan'
											, 'product.purchase_price', 'product.product_price', 'product.product_price_usd'
											, 'product.product_price_currency', 'product.product_user_id', 'product.product_sold'
											, 'product.product_added_date', 'product.url_slug', 'product.demo_url', 'product.demo_details'
											, 'product.product_category_id', 'product.product_tags', 'product.total_views', 'product.is_featured_product'
											, 'product.featured_product_expires', 'product.is_user_featured_product', 'product.date_activated'
											, 'product.product_discount_price', 'product.product_discount_price_usd', 'product.product_discount_fromdate'
											, 'product.product_discount_todate', 'product.product_preview_type', 'product.is_free_product'
											, 'product.last_updated_date', 'product.total_downloads', 'product.product_moreinfo_url'
											, 'product.global_transaction_fee_used', 'product.site_transaction_fee_type', 'product.site_transaction_fee'
											, 'product.site_transaction_fee_percent', 'product.is_downloadable_product', 'product.user_section_id'
											, 'product.delivery_days', 'product.date_expires', 'product.default_orig_img_width'
											, 'product.default_orig_img_height', 'product.product_status', 'product.shipping_from_country'
											, 'product.shipping_template', 'accept_giftwrap', 'accept_giftwrap_message', 'giftwrap_type'
											, 'giftwrap_pricing', 'use_variation', 'variation_group_id'					);
												/* DB::raw('IF( ( DATE( NOW() ) BETWEEN product.product_discount_fromdate AND product.product_discount_todate), 1,IF((product.product_discount_price > 0 AND product.product_discount_fromdate = \'0000-00-00\' AND product.product_discount_todate = \'0000-00-00\'),1,0) ) AS have_discount')*/
		$product_details = $product_details->join('product_category', 'product.product_category_id', '=', 'product_category.id');
		if($this->filter_user_allowed_to_add_product == 'Yes') {
			$cache_key .= '_UATP';
			$product_details = $product_details->leftjoin('shop_details', 'product.product_user_id', '=', 'shop_details.user_id');
		}
		if($this->filter_product_from_price != '' OR $this->filter_product_to_price != '') {
			$cache_key .= '_PP';
			$quantity = $this->filter_product_qty;
			$quantity = ($quantity > 1) ? $quantity : 1;
			$data_arr['quantity'] = $quantity;
			$data_arr['group_id'] = $this->filter_user_group_id;
			//if($this->filter_user_group_id == 0) {
				$product_details = $product_details->leftjoin('product_price_groups', function($join) use ($data_arr)
			                         {
			                             $join->on('product.id', '=', 'product_price_groups.product_id');
			                             $join->where('product_price_groups.group_id', '=', 0);
			                             if($data_arr['quantity'] == 1)
			                             	$join->where('product_price_groups.range_start', '=', 1);
			                         });
			/*}
			else {
				$temp_product_price_group_table = 'temp_product_price_group_'.$this->filter_logged_user_id;
				$product_details = $product_details->join($temp_product_price_group_table, 'product.id', '=', $temp_product_price_group_table.'.product_id');
			}*/
		}
		if($this->filter_stock_country != '') {
			$cache_key .= '_FSC'.$this->filter_stock_country;
			$product_details = $product_details->join('product_stocks', 'product.id', '=', 'product_stocks.product_id')->whereRaw('product_stocks.stock_country_id = '.$this->filter_stock_country);
		}

		if(!$this->include_blocked_user_products) {
			//echo "here";die;
			$cache_key .= '_IBP';
			$data_arr = array();
			$product_details = $product_details->join('users', function($join) use ($data_arr)
			                         {
			                             $join->on('product.product_user_id', '=', 'users.id');
			                             $join->where('users.is_banned', '=', 0);
			                             $join->where('users.shop_status', '=', 1);
			                         });
		}

		if($user_id > 0) {
			$cache_key .= '_UA'.$user_id;
			$product_details = $product_details->whereRaw('product.product_user_id = ?', array($user_id));
		}

		if(!$this->include_deleted) {
			$cache_key .= '_ID';
			$product_details = $product_details->whereRaw('product.product_status != ?', array('Deleted'));
		}

		if($this->filter_product_status != '') {
			$cache_key .= '_PS'.$this->filter_product_status;
			$product_details = $product_details->whereRaw('product.product_status = ?', array($this->filter_product_status));
		}

		if(isset($this->filter_product_expiry) && $this->filter_product_expiry) {
			$cache_key .= '_PE';
			$product_details = $product_details->where('product.date_expires', '>=', '0000-00-00 00:00:00')->where('product.date_expires', '>=', date('Y-m-d'));
		}

		if(count($this->filter_product_ids) > 0) {
			$cache_key .= '_PI'.serialize($this->filter_product_ids);
			$product_details = $product_details->whereIn('product.id', $this->filter_product_ids);
		}
		if($this->filter_section_id != '') {
			$cache_key .= '_SI'.$this->filter_section_id;
			$product_details = $product_details->join('user_product_section', 'user_product_section.id', '=', 'product.user_section_id')->whereRaw("( user_product_section.id = ".$this->filter_section_id." )");
		}
		if($this->filter_product_code != '') {
			$cache_key .= '_PC'.$this->filter_product_code;
			$product_details = $product_details->whereRaw('product.product_code = ?', array($this->filter_product_code));
		}
		if($this->filter_product_name != '') {
			$cache_key .= '_PN_'.$this->filter_product_name;
			$product_details = $product_details->where('product.product_name', 'LIKE', '%'.addslashes($this->filter_product_name).'%');
		}
		if($this->filter_seller_code != '') {
			$cache_key .= '_SC'.$this->filter_seller_code;
			$product_details = $product_details->where('product.product_user_id', '=', BasicCUtil::getUserIDFromCode($this->filter_seller_code));
		}

		if($this->filter_product_free != ''){
			$cache_key .= '_PF'.$this->filter_product_free;
			$product_details = $product_details->whereRaw('product.is_free_product = ?', array($this->filter_product_free));
		}
		if($this->filter_download_product != ''){
			$cache_key .= '_DP'.$this->filter_download_product;
			$product_details = $product_details->whereRaw('product.is_downloadable_product = ?', array($this->filter_download_product));
		}
		if($this->filter_product_category != '') {
			$cat_id_arr = $this->filter_product_category;
			if(!is_array($this->filter_product_category))
			{
				$cat_id_arr = $this->getSubCategoryIds($this->filter_product_category);
			}
			$cache_key .= '_PCT_'.serialize($cat_id_arr);
			$product_details = $product_details->whereIn('product.product_category_id', $cat_id_arr);
		}

		if($this->filter_product_id_from != '') {
			$cache_key .= '_PIF';
			$product_details = $product_details->where('product.id', '>=', $this->filter_product_id_from);
		}

		if($this->filter_product_id_to != '') {
			$cache_key .= '_PIT'.$this->filter_product_id_to;
			$product_details = $product_details->where('product.id', '<=', $this->filter_product_id_to);
		}

		if($this->filter_product_added_from != '') {
			$cache_key .= '_PAF'.$this->filter_product_added_from;
			if($this->filter_product_added_to != '') {
				$product_details = $product_details->where('product.product_added_date', '>=', $this->filter_product_added_from);
			} else {
				$product_details = $product_details->where('product.product_added_date', '=', $this->filter_product_added_from);
			}
		}

		if($this->filter_product_added_to != '') {
			$cache_key .= '_PAT'.$this->filter_product_added_to;
			if($this->filter_product_added_from != '') {
				$product_details = $product_details->where('product.product_added_date', '<=', $this->filter_product_added_to);
			} else {
				$product_details = $product_details->where('product.product_added_date', '=', $this->filter_product_added_to);
			}
		}

		if($this->filter_featured_product != '') {
			$cache_key .= '_FP'.$this->filter_featured_product;
			$product_details = $product_details->where('product.is_featured_product', '=', $this->filter_featured_product);
			if($this->filter_featured_product == 'Yes') {
				$product_details = $product_details->where('product.featured_product_expires', '>=', '0000-00-00 00:00:00')->where('product.featured_product_expires', '>=', new DateTime('today'));
			}
		}

		if($this->filter_shop_name != '') {
			$cache_key .= '_SN'.$this->filter_shop_name;
			$product_details = $product_details->join('shop_details as sd', 'sd.user_id', '=', 'product.product_user_id')
												->Where('sd.shop_name', 'LIKE', '%'.addslashes($this->filter_shop_name).'%');
		}
		$is_attr_option_srch_done = $is_attr_values_srch_done = false;
		$attr_action_ids_arr = $attr_value_ids_arr = array();
		if(!empty($this->filter_attribute_options))
		{
			$is_attr_option_srch_done = true;
			$attr_action_ids_arr = $this->getProductIdsFromAttributeOptionValues($this->filter_attribute_options);
			$cache_key .= '_AO';
			//$this->setCombinedFilterProductIdsIn($product_ids_arr);
		}
		if(!empty($this->filter_attribute_values))
		{
			$is_attr_values_srch_done = true;
			$attr_value_ids_arr = $this->getProductIdsFromAttributeValue($this->filter_attribute_values);
			$cache_key .= '_AV';
			//$this->setCombinedFilterProductIdsIn($product_ids_arr);
		}

		if($is_attr_option_srch_done || $is_attr_values_srch_done)
		{
			$attr_search_product_ids = array();
			if(!empty($attr_action_ids_arr) && !empty($attr_value_ids_arr))
			{
				$attr_search_product_ids = array_intersect($attr_action_ids_arr,$attr_value_ids_arr);
			}
			elseif(!empty($attr_action_ids_arr) || !empty($attr_value_ids_arr))
			{
				$attr_search_product_ids = !empty($attr_action_ids_arr)?$attr_action_ids_arr:(!empty($attr_value_ids_arr)?$attr_value_ids_arr:array());
			}
			if(!empty($attr_search_product_ids))
			{
				$cache_key .= '_AO'.serialize($attr_search_product_ids);
				$this->setFilterProductIdsIn($attr_search_product_ids);
			}
			else
			{
				$cache_key .= '_ID_1';
				$product_details = $product_details->whereIn('product.id', array('-1'));
				if($this->products_per_page != '' && $this->products_per_page > 0){
					$cache_key .= '_PPP';
					$product_details = $product_details->paginate($this->products_per_page);
				}else{
					$cache_key .= '_PG';
					$product_details = $product_details->get();// This had done to return the empty result
				}
				return $product_details;
			}
		}

		if($this->filter_product_from_price != '' OR $this->filter_product_to_price != '') {
			$cache_key .= '_PF_PT';
			$start_price = $this->filter_product_from_price;
			$end_price = $this->filter_product_to_price;


			//$condn_to_check_discount = '((DATE(NOW()) BETWEEN product.product_discount_fromdate AND product.product_discount_todate) AND product.product_discount_price)';
			/*$condn_to_check_discount = '((DATE(NOW()) BETWEEN product.product_discount_fromdate AND product.product_discount_todate) OR (product.product_discount_price > 0 AND product.product_discount_fromdate = \'0000-00-00\' AND product.product_discount_todate = \'0000-00-00\'))';
			if($start_price != '' AND $end_price != '')
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd  BETWEEN '.$start_price.' AND '.$end_price.'),'.
										'(product.product_price_usd BETWEEN '.$start_price.' AND '.$end_price.')))'.
										' AND product.is_free_product = \'No\''));
			}
			elseif($start_price AND !$end_price)
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd >= '.$start_price.'),'.
										'(product.product_price_usd >= '.$start_price.')))'.
										' AND product.is_free_product = \'No\''));
			}
			elseif(!$start_price AND $end_price)
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd <= '.$end_price.'),'.
										'(product.product_price_usd <= '.$end_price.')))'.
										' AND product.is_free_product = \'No\''));
			}*/

			/*if($this->filter_user_group_id == 0) {*/
				$product_details = $product_details->whereRaw('product_price_groups.range_start <= ? AND (product_price_groups.range_end >= ? || product_price_groups.range_end = ?)', array($quantity, $quantity, -1));

				if($this->filter_product_from_price != ''){
					$cache_key .= '_NEPF'.$this->filter_product_from_price;
					$product_details = $product_details->whereRaw('product_price_groups.discount >= ?', array($this->filter_product_from_price));
				}
				if($this->filter_product_to_price != ''){
					$cache_key .= '_NEPT'.$this->filter_product_to_price;
					$product_details = $product_details->whereRaw('product_price_groups.discount <= ?', array($this->filter_product_to_price));
				}
			/*}
			else {
				$temp_product_price_group_table = 'temp_product_price_group_'.$this->filter_logged_user_id;
				if($this->filter_product_from_price != '')
					$product_details = $product_details->whereRaw($temp_product_price_group_table.'.discount >= ?', array($this->filter_product_from_price));

				if($this->filter_product_to_price != '')
					$product_details = $product_details->whereRaw($temp_product_price_group_table.'.discount <= ?', array($this->filter_product_to_price));
			}*/

			if($start_price <= 0 || ($start_price == '' && $end_price <= 0))
			{
				$cache_key .= '_FPC';
				$product_details = $product_details->orWhere('product.is_free_product', '=', 'Yes');
			}
		}

		if($this->filter_product_ids_in != '' && is_array($this->filter_product_ids_in) && !empty($this->filter_product_ids_in)) {
			$cache_key .= '_FPIN_'.serialize($this->filter_product_ids_in);
			$product_details = $product_details->whereIn('product.id', $this->filter_product_ids_in);
		}

		if($this->filter_keyword != '') {
			if(is_array($this->filter_keyword))
			{
				$tags_condition = '';
				$tagsearch_list = $this->filter_keyword;

				if(!empty($tagsearch_list) and COUNT($tagsearch_list) > 0)
				{
					foreach($tagsearch_list as $tag_key => $tag_val)
					{
						if($tags_condition != "") {
							$tags_condition .= " OR ";
						}

						$tags_condition .= "((product.product_tags LIKE '%".addslashes($tag_val)."%') OR (product.product_name LIKE '%".addslashes($tag_val)."%')
											OR (product.product_description LIKE '%".addslashes($tag_val)."%') )";
					}
					if($tags_condition != '') {
						$cache_key .= '_TCC_'.serialize($tags_condition);
						$product_details = $product_details->whereRaw(DB::raw("(".$tags_condition.")"));
					}
				}
			}
		}
		$this->order_by_field = '';
		if($this->order_by != '') {
			$cache_key .= '_OB';
			if($this->order_by == 'id')	{
				$cache_key .= '_OPID';
				$this->order_by_field = 'id';
			}
			else if($this->order_by == 'product_sold') {
				$cache_key .= '_OPS';
				//$product_details = $product_details->whereRaw("(product.is_free_product = 'No' AND product.product_price_usd != 0 AND  product.product_price_usd != '' ) AND (product.product_sold > 0)");
				$product_details = $product_details->whereRaw("(product.product_sold > 0)");
				$this->order_by_field = 'product_sold';
			}
			else if($this->order_by == 'featured') {
				$cache_key .= '_OBF';
				$this->order_by_field = 'date_activated';
				$product_details = $product_details->Where('product.is_featured_product', '=', 'Yes');
			}
			else if($this->order_by == 'is_free_product') {
				$cache_key .= '_OFP';
				$product_details = $product_details->whereRaw(" ( product.is_free_product = 'Yes')");//OR  (product.product_price_usd = 0 OR product.product_price_usd = '' )
			}
		}

		//$product_details = $product_details->groupBy('product.id');

		if($this->order_by_field != '')	{
			$cache_key .= '_OBFD';
			if($this->order_by_field == 'product_sold')
			{
				$cache_key .= '_OBFPS';
				$product_details->orderby(DB::raw('FIELD(is_free_product, \'No\')'), 'desc');
			}
			$cache_key .= '_OBFP'.$this->order_by_field;
			$product_details = $product_details->orderBy($this->order_by_field, 'DESC');
		}
		if($this->products_per_page != '' && $this->products_per_page > 0){
			if(!$allow_cache || !HomeCUtil::cacheAllowed())
				$product_details = $product_details->paginate($this->products_per_page);
			else{
				$page_name = (!Input::has('page') ? '1' :  Input::get('page'));
				$cache_key .= '_PPPG_'.$page_name;
				if(! Cache::has('products_' . $cache_key)) {
					$products = array(
								'total' => $product_details->get()->count(),
								'items' => $product_details->paginate($this->products_per_page)->getItems(),
								'perpage' => $this->products_per_page,
								);
					Cache::put('products_' . $cache_key, $products, Config::get('generalConfig.cache_expiry_minutes'));
				}
				$products = Cache::get('products_' . $cache_key);
				$product_details = Paginator::make($products['items'], $products['total'], $products['perpage']);
			}
		} else {
			if($this->products_limt != '' && $this->products_limt > 0){
				$cache_key .= '_PGL';
				$product_details = $product_details->take($this->products_limt);
			}
			if (!$allow_cache || ((HomeCUtil::cacheGet($cache_key)) === NULL)) {
				$product_details = $product_details->get();
				HomeCUtil::cachePut($cache_key, $product_details, Config::get('generalConfig.cache_expiry_minutes'));
			}else{
				$product_details = HomeCUtil::cacheGet($cache_key);
			}
		}
		return $product_details;
	}

	public function getSubCategoryIds($category_id)
	{
		$sub_category_ids_arr = array(0);
		$cache_key = 'sub_category_ids_'.$category_id;
		if (($sub_cat_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$sub_cat_details = DB::select('select node.id AS sub_category_id from product_category node, product_category parent where
			node.category_left BETWEEN parent.category_left AND parent.category_right AND parent.id = ? ORDER BY node.category_left', array($category_id));
			HomeCUtil::cachePut($cache_key, $sub_cat_details, Config::get('generalConfig.cache_expiry_minutes'));
		}

		if(count($sub_cat_details) > 0)
		{
			$sub_category_ids_arr = array();
			foreach($sub_cat_details as $sub_cat)
			{
				$sub_category_ids_arr[] = $sub_cat->sub_category_id;
			}
		}
		return $sub_category_ids_arr;
	}

	public function getPriceGroupProductIds($temp_product_price_group_table)
	{
		$product_ids_arr = array(0);
		$price_details = DB::select('select product_id from '.$temp_product_price_group_table.' where 1');

		if(count($price_details) > 0)
		{
			$product_ids_arr = array();
			foreach($price_details as $price)
			{
				$product_ids_arr[] = $price->product_id;
			}
		}
		return $product_ids_arr;
	}

	public function getProductIdsFromAttributeOptionValues($attribute_details = array())
	{
		$attr_product_ids_arr = array();
		if(!empty($attribute_details))
		{
			foreach($attribute_details as $attr_id => $attr_options)
			{
				$attr_options_ids = !empty($attr_options)?implode(',',$attr_options):'';
				if($attr_options_ids!='')
				{
					$product_ids_arr = array();
					$cache_key = 'product_ids_from_attribute_option_values_key_'.$attr_id;
					if (($product_ids_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
						$product_ids_details = DB::select('select product_id from product_attributes_option_values where attribute_id = ? and attribute_options_id in ('.$attr_options_ids.')', array($attr_id));
						HomeCUtil::cachePut($cache_key, $product_ids_details, Config::get('generalConfig.cache_expiry_minutes'));
					}
					if(count($product_ids_details) > 0)
					{
						foreach($product_ids_details as $product_id)
						{
							$product_id = $product_id->product_id;
							if(!isset($product_ids_arr[$product_id]))
								$product_ids_arr[$product_id] = $product_id;
						}
					}
					else
					{
						return array();
					}
				}
				if(!empty($attr_product_ids_arr))
					$attr_product_ids_arr = array_intersect($attr_product_ids_arr,$product_ids_arr);
				else
					$attr_product_ids_arr  = $product_ids_arr;
			}
		}
		return $attr_product_ids_arr;
	}

	public function getProductIdsFromAttributeValue($attribute_values)
	{
		$attr_product_ids_arr = array();
		foreach($attribute_values as $attr_id => $value)
		{
			$value = '%'.$value.'%';
			$cache_key = 'product_ids_from_attribute_value'.$attr_id;
			if (($product_ids_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$product_ids_details = DB::select('select product_id from product_attributes_values where attribute_id = ? and attribute_value like ?',array($attr_id,$value));
				HomeCUtil::cachePut($cache_key, $product_ids_details, Config::get('generalConfig.cache_expiry_minutes'));
			}
			if(count($product_ids_details) > 0)
			{
				$product_ids_arr = array();
				foreach($product_ids_details as $product_id)
				{
					$product_id = $product_id->product_id;
					if(!isset($product_ids_arr[$product_id]))
						$product_ids_arr[$product_id] = $product_id;
				}
			}
			else
			{
				return array();
			}
			if(!empty($attr_product_ids_arr))
				$attr_product_ids_arr = array_intersect($attr_product_ids_arr,$product_ids_arr);
			else
				$attr_product_ids_arr  = $product_ids_arr;
		}
		return $product_ids_arr;
	}

	public static $_product_images_details;
	public function getProductImage($product_id, $allow_cache = true)
	{
		$p_id = 0;
		if($product_id != '' && $product_id > 0)
		{
			$p_id = $product_id;
		}
		$product_img_arr = array();
		if (isset(WebshopProduct::$_product_images_details[$p_id])) {
			$product_img = WebshopProduct::$_product_images_details[$p_id];
		} else {
				$cache_key = 'product_image_'.$product_id;
				if (!$allow_cache || (($product_img = HomeCUtil::cacheGet($cache_key)) === NULL)) {
					$product_img = ProductImage::where('product_id', '=', $p_id)->get();
					HomeCUtil::cachePut($cache_key, $product_img, Config::get('generalConfig.cache_expiry_minutes'));
				}
			WebshopProduct::$_product_images_details[$p_id] = $product_img;
		}
		if(count($product_img) > 0) {
			foreach($product_img as $key => $vlaues) {
				$product_img_arr['id'] = $vlaues->id;
				$product_img_arr['product_id'] = $vlaues->product_id;
				$product_img_arr['thumbnail_title'] = $vlaues->thumbnail_title;
				$product_img_arr['thumbnail_img'] = $vlaues->thumbnail_img;
				$product_img_arr['thumbnail_ext'] = $vlaues->thumbnail_ext;
				$product_img_arr['thumbnail_width'] = $vlaues->thumbnail_width;
				$product_img_arr['thumbnail_height'] = $vlaues->thumbnail_height;
				$product_img_arr['thumbnail_s_width'] = $vlaues->thumbnail_s_width;
				$product_img_arr['thumbnail_s_height'] = $vlaues->thumbnail_s_height;
				$product_img_arr['thumbnail_t_width'] = $vlaues->thumbnail_t_width;
				$product_img_arr['thumbnail_t_height'] = $vlaues->thumbnail_t_height;
				$product_img_arr['thumbnail_l_width'] = $vlaues->thumbnail_t_width;
				$product_img_arr['thumbnail_l_height'] = $vlaues->thumbnail_t_height;
				$product_img_arr['default_title'] = $vlaues->default_title;
				$product_img_arr['default_img'] = $vlaues->default_img;
				$product_img_arr['default_ext'] = $vlaues->default_ext;
				$product_img_arr['default_width'] = $vlaues->default_width;
				$product_img_arr['default_height'] = $vlaues->default_height;
				$product_img_arr['default_s_width'] = $vlaues->default_s_width;
				$product_img_arr['default_s_height'] = $vlaues->default_s_height;
				$product_img_arr['default_t_width'] = $vlaues->default_t_width;
				$product_img_arr['default_t_height'] = $vlaues->default_t_height;
				$product_img_arr['default_l_width'] = $vlaues->default_l_width;
				$product_img_arr['default_l_height'] = $vlaues->default_l_height;
			}
		}
		return $product_img_arr;
	}

	public function getShopProductSectionDetails($owner_id)
	{
		$section_details_arr = array();
		$section_details = UserProductSection::Select('user_product_section.id', 'user_product_section.section_name',
													 'user_product_section.status', 'user_product_section.date_added'
													 , DB::raw('COUNT(prd.user_section_id) AS section_count'))
												->whereRaw('user_product_section.user_id =  ? AND prd.product_user_id = ?
															AND prd.product_status = \'Ok\'
															AND prd.date_expires!=\'0000-00-00 00:00:00\' AND prd.date_expires >= \''.date('Y-m-d').'\'
															', array($owner_id, $owner_id))
												->join('product AS prd', 'prd.user_section_id', '=', 'user_product_section.id')
												->Groupby('user_product_section.id')->get();
		if(count($section_details) > 0) {
			foreach($section_details as $key => $vlaues) {
				$section_details_arr['id'] = $vlaues->id;
				$section_details_arr['section_name'] = $vlaues->section_name;
				$section_details_arr['status'] = $vlaues->status;
				$section_details_arr['date_added'] = $vlaues->date_added;
				$section_details_arr['section_count'] = $vlaues->section_count;
			}
		}
		return $section_details;
	}

	public static $_product_details;
	public function getProductDetails($logged_user_id = 0, $allow_cache = true)
	{
		$product_arr = array();
		if((is_numeric($this->product_id) && $this->product_id > 0 ) || $this->filter_product_code != '')
		{
			$cache_key = 'product_details_'.$logged_user_id.'_'.$this->product_id;
			$fetch_from_cache = true;
			$product_details = Product::Select('product.id', 'product.product_code', 'product.product_name', 'product.product_description'
												, 'product.product_support_content', 'product.meta_title', 'product.meta_keyword'
												, 'product.meta_description', 'product.product_highlight_text', 'product.product_slogan'
												, 'product.purchase_price', 'product.product_price', 'product.product_price_usd', 'product.product_price_currency'
												, 'product.product_user_id', 'product.product_sold', 'product.product_added_date'
												, 'product.url_slug', 'product.demo_url', 'product.demo_details', 'product.product_category_id'
												, 'product.product_tags', 'product.total_views', 'product.is_featured_product', 'product.featured_product_expires'
												, 'product.is_user_featured_product', 'product.date_activated', 'product.product_discount_price'
												, 'product.product_discount_price_usd', 'product.product_discount_fromdate'
												, 'product.product_discount_todate', 'product.product_preview_type', 'product.is_free_product'
												, 'product.last_updated_date', 'product.total_downloads', 'product.product_moreinfo_url'
												, 'product.global_transaction_fee_used', 'product.site_transaction_fee_type', 'product.site_transaction_fee'
												, 'product.site_transaction_fee_percent', 'product.is_downloadable_product', 'product.user_section_id'
												, 'product.delivery_days', 'product.date_expires', 'product.default_orig_img_width'
												, 'product.default_orig_img_height', 'product.product_status'
												, 'product.use_cancellation_policy', 'product.use_default_cancellation'
												, 'product.cancellation_policy_text', 'product.cancellation_policy_filename'
												, 'product.cancellation_policy_filetype', 'product.cancellation_policy_server_url'
												, 'product.shipping_from_country', 'product.shipping_from_zip_code', 'product.shipping_template'
												, 'accept_giftwrap', 'accept_giftwrap_message', 'giftwrap_type', 'giftwrap_pricing', 'use_variation'
												, 'variation_group_id'
												);
												//, DB::raw('IF( ( DATE( NOW() ) BETWEEN product.product_discount_fromdate AND product.product_discount_todate), 1,	IF((product.product_discount_price > 0 AND product.product_discount_fromdate = \'0000-00-00\' AND product.product_discount_todate = \'0000-00-00\'),1,0) ) AS have_discount')

			if(!$this->include_blocked_user_products) {
				//echo "here";die;
				$cache_key .= '_BU';
				$data_arr2 = array();
				$product_details = $product_details->join('users', function($join) use ($data_arr2)
				                         {
				                             $join->on('product.product_user_id', '=', 'users.id');
				                             $join->where('users.is_banned', '=', 0);
				                             $join->where('users.shop_status', '=', 1);
				                         });
				$fetch_from_cache = true;
			}

			if($this->filter_product_code != '') {
				$cache_key .= '_FPC_'.$this->filter_product_code;
				$product_details = $product_details->whereRaw('product.product_code = ?', array($this->filter_product_code));
				$fetch_from_cache = true;
			}
			else {
				$cache_key .= '_PI_'.$this->product_id;
				$product_details = $product_details->whereRaw('product.id = ?', array($this->product_id));
				$fetch_from_cache = true;
			}

			//echo "dekekte: ".$this->include_deleted;
			//echo "hii";exit;
			if(!$this->include_deleted) {
				$cache_key .= '_ID';
				$product_details = $product_details->whereRaw('product.product_status != ?', array('Deleted'));
				$fetch_from_cache = true;
			}
			if($this->filter_product_status != '') {
				$cache_key .= '_FPS_'.$this->filter_product_status;
				$product_details = $product_details->whereRaw('product.product_status = ?', array($this->filter_product_status));
				$fetch_from_cache = false;
			}
			if(isset($this->filter_product_expiry) && $this->filter_product_expiry) {
				$cache_key .= '_FPE';
				$product_details = $product_details->where('product.date_expires', '>=', '0000-00-00 00:00:00')->where('product.date_expires', '>=', date('Y-m-d'));
				$fetch_from_cache = false;
			}
			if($logged_user_id > 0)
			{
				$cache_key .= '_LUID_'.$logged_user_id;
				$product_details = $product_details->whereRaw('product.product_user_id = ?', array($logged_user_id));
				$fetch_from_cache = false;
			}
			$fetch_from_query = false;
			if ($fetch_from_cache) {
				if ($this->filter_product_code != '' && isset(WebshopProduct::$_product_details[$this->filter_product_code])) {
					$product_details_result = WebshopProduct::$_product_details[$this->filter_product_code];
				} elseif (isset(WebshopProduct::$_product_details[$this->product_id])) {
					$product_details_result = WebshopProduct::$_product_details[$this->product_id];
				} else {
					$fetch_from_query = true;
				}
			} else {
				$fetch_from_query = true;
			}
			if ($fetch_from_query) {
				if (!$allow_cache || (($product_details_result = HomeCUtil::cacheGet($cache_key)) === NULL)) {
					$product_details_result = $product_details->first();
					HomeCUtil::cachePut($cache_key, $product_details_result, Config::get('generalConfig.cache_expiry_minutes'));
				}
				//print_r(var_dump($product_details_result));
				if($this->filter_product_code != '') {
					WebshopProduct::$_product_details[$this->filter_product_code] = $product_details_result;
				} else {
					WebshopProduct::$_product_details[$this->product_id] = $product_details_result;
				}
			}

			if(count($product_details_result) > 0)
			{
				$product_arr = $product_details_result->toArray();
				return $product_arr;
			}
		}
		return $product_arr;
	}

	public function removeProductCategoryAttribute()
	{
		//To delete product attributes values
		ProductAttributesValues::whereRaw("product_id = ?", array($this->product_id))->delete();
		//To delete product attributes options values
		ProductAttributesOptionValues::whereRaw("product_id = ?", array($this->product_id))->delete();
	}

	public function addProductComment($user_id, $notes, $added_by)
	{
		$data_arr = array('user_id' => $user_id,
                          'product_id' => $this->product_id,
                          'added_by' => $added_by,
                          'notes' => $notes,
                          'date_added' => DB::raw('NOW()'));
		ProductLog::insertGetId($data_arr);
	}

	public function saveProductImageTitle($type, $title)
	{
		if (strcmp($type, 'thumb') == 0)
		{
			ProductImage::whereRaw('product_id = ?', array($this->product_id))->update(array('thumbnail_title' => $title));
		}
		else
		{
			ProductImage::whereRaw('product_id = ?', array($this->product_id))->update(array('default_title' => $title));
		}
        return true;
	}

	public function removeProductThumbImage()
	{
        $data_arr = array('thumbnail_img' => '' ,
		 		  		'thumbnail_ext' => '' ,
		 		  		'thumbnail_width' => 0,
		 		  		'thumbnail_height' => 0,
		 		  		'thumbnail_s_width' => 0,
		 		  		'thumbnail_s_height' => 0,
		 		  		'thumbnail_t_width' => 0,
		 		  		'thumbnail_t_height' => 0,
		 		  		'thumbnail_l_width' => 0,
		 		  		'thumbnail_l_height' => 0,
						'thumbnail_title' => '' );
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
        return true;
	}

	public function removeProductDefaultImage()
	{
        $data_arr = array('default_img' => '' ,
		 		  		 'default_ext' => '' ,
		 		  		 'default_width' => 0,
		 		  		 'default_height' => 0,
		 		  		 'default_s_width' => 0,
		 		  		 'default_s_height' => 0,
		 		  		 'default_t_width' => 0,
		 		  		 'default_t_height' => 0,
		 		  		 'default_l_width' => 0,
		 		  		 'default_l_height' => 0,
						 'default_title' => '' );
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
        return true;
    }

    public function getAttributesList($category_id)
	{
		$data_arr = array();
		if(is_numeric($category_id) && $category_id > 0)
		{
			//get all the category_id up in tree and the corresponding attribute ids..
			$category_ids = Products::getTopLevelCategoryIds($category_id);
			$cache_key = 'CSAL_cache_key_'.$category_id;
			if (($recs_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$q = ' SELECT MCA.attribute_id, attribute_question_type, validation_rules, default_value, MA.status , attribute_label ' .
					 ' FROM product_attributes AS MA LEFT JOIN ' .
					 ' product_category_attributes AS MCA ON MA.id = MCA.attribute_id '.
					 ' WHERE MCA.category_id IN ('.$category_ids.') '.
					 ' ORDER BY display_order, MA.id';
				$recs_arr = DB::select($q);
				HomeCUtil::cachePut($cache_key, $recs_arr, Config::get('generalConfig.cache_expiry_minutes'));
			}
			foreach($recs_arr AS $key => $val)
			{
				$dafault_value =  $val->default_value;
				//If product is avalilable, set the form field values by user entered data
				if($this->product_id != '' && $this->product_id > 0)
				{
					$dafault_value = $this->getAttributeValue($this->product_id, $val->attribute_id, $val->attribute_question_type, $dafault_value);
				}

				$data_arr[$val->attribute_id] = array('attribute_id' => $val->attribute_id,
													  'attribute_question_type' => $val->attribute_question_type,
													  'validation_rules' => $val->validation_rules,
													  'default_value' => $dafault_value,
													  'status' => $val->status,
													  'attribute_label' => $val->attribute_label	);
			}
		}
		return $data_arr;
	}

	public function getSearchableAttributesList($category_id)
	{
		$data_arr = array();
		if(is_numeric($category_id) && $category_id > 0)
		{
			//get all the category_id up in tree and the corresponding attribute ids..
			$category_ids = Products::getTopLevelCategoryIds($category_id);
			$cache_key = 'CSAL_cache_key_'.$category_id.serialize($category_ids);
			if (($recs_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$q = ' SELECT MCA.attribute_id, attribute_question_type, validation_rules, default_value, MA.status , attribute_label, is_searchable ' .
					 ' FROM product_attributes AS MA LEFT JOIN ' .
					 ' product_category_attributes AS MCA ON MA.id = MCA.attribute_id '.
					 ' WHERE MCA.category_id IN ('.$category_ids.') '.
					 ' AND is_searchable = \'yes\' '.
					 ' ORDER BY display_order, MA.id';
				$recs_arr = DB::select($q);
				HomeCUtil::cachePut($cache_key, $recs_arr, Config::get('generalConfig.cache_expiry_minutes'));
			}
			foreach($recs_arr AS $key => $val)
			{
				$dafault_value =  $val->default_value;
				//If product is avalilable, set the form field values by user entered data
				if($this->product_id != '' && $this->product_id > 0)
				{
					$dafault_value = $this->getAttributeValue($this->product_id, $val->attribute_id, $val->attribute_question_type, $dafault_value);
				}

				$data_arr[$val->attribute_id] = array('attribute_id' => $val->attribute_id,
													  'attribute_question_type' => $val->attribute_question_type,
													  'validation_rules' => $val->validation_rules,
													  'default_value' => $dafault_value,
													  'status' => $val->status,
													  'attribute_label' => $val->attribute_label,
													  'is_searchable' => $val->is_searchable	);
			}
		}
		return $data_arr;
	}

	public function getAttributeValue($p_id, $attr_id, $attr_type, $dafault_value)
	{
		switch($attr_type)
		{
			case 'text':
			case 'textarea':
				$count = ProductAttributesValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->count();
				if($count > 0)
				{
					return ProductAttributesValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->pluck('attribute_value');
				}
				break;

			case 'select':
			case 'option': // radio button
			case 'multiselectlist':
			case 'check': // checkbox
				$count = ProductAttributesOptionValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->count();
				if($count > 0)
				{
					$rtn_arr = array();
					$t_arr = ProductAttributesOptionValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->get(array('attribute_options_id'))
								->toArray();
					foreach($t_arr AS $arr)
					{
						$rtn_arr[] = $arr['attribute_options_id'];
					}
					return $rtn_arr;
				}
				break;
		}
		return $dafault_value;
	}

	public function getProductNotes()
	{
		$p_id = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$p_id = $this->product_id;
		}
		return ProductLog::whereRaw('product_id = ?', array($p_id))->orderBy('id', 'DESC')->get();
	}

	public function populateProductResources($resource_type = '', $is_downloadable = 'No', $product_id = 0)
	{
		$resources_arr = array();

		$p_id = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$p_id = $this->product_id;
		}
		else if($product_id > 0){
			$p_id = $product_id;
		}
		if($p_id == 0)
		{
			return $resources_arr;
		}

		$cache_key = 'PRCK_'.$p_id.'_'.$resource_type.'_'.$is_downloadable;
		if (($d_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$d_arr = ProductResource::where('product_id', '=', $p_id)->where('resource_type', '=', $resource_type)->where('is_downloadable', '=', $is_downloadable)
					 ->orderBy('display_order', 'ASC')
					 ->get(array('id', 'product_id', 'resource_type', 'is_downloadable', 'filename', 'ext', 'title', 'default_flag', 'server_url', 'width', 'height', 't_width', 't_height', 'l_width', 'l_height', 's_width', 's_height'))
					 ->toArray();
			HomeCUtil::cachePut($cache_key, $d_arr, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($d_arr) > 0)
		{
			foreach($d_arr AS $data)
			{
				$resources_arr[] = array(	'resource_id' => $data['id'],
											'resource_type' => $data['resource_type'],
											'filename' => $data['filename'],
											'filename_thumb' => $data['filename'] . 'T.' . $data['ext'],
											'filename_large' => $data['filename'] . 'L.' . $data['ext'],
											'filename_original' => $data['filename'] . '.' . $data['ext'],
											'width' => $data['width'],
											'height' => $data['height'],
											's_width' => $data['s_width'],
											's_height' => $data['s_height'],
											't_width' => $data['t_width'],
											't_height' => $data['t_height'],
											'l_width' => $data['l_width'],
											'l_height' => $data['l_height'],
											'ext' => $data['ext'],
											'title' => $data['title'],
											'is_downloadable' => $data['is_downloadable'],
											'default_flag' => $data['default_flag'],
											'server_url' => $data['server_url']	);
			}
		}
		return $resources_arr;
	}

	public function updateProductResourceTitle($resource_id, $title)
	{
		ProductResource::whereRaw('id = ?', array($resource_id))->update(array('title' => $title));
	    return true;
	}

	public function getProductResource($row_id)
	{
		$data_arr = ProductResource::where('id', '=', $row_id)->get(array('filename', 'resource_type', 'ext'))->toArray();
		return $data_arr;
	}

	public function deleteProductResource($row_id)
	{
		ProductResource::where('id', '=', $row_id)->delete();
	}

	public function updateProductResourceDisplayOrder($resource_id, $display_order)
	{
		ProductResource::whereRaw('id = ?', array($resource_id))->update(array('display_order' => $display_order));
	}

	public function getDownloadProductDetails($product_id = 0)
	{
		$download_arr = DB::select('SELECT filename, ext, resource_type, title, product_user_id, is_free_product FROM product_resource AS PR, product AS P WHERE PR.product_id = '.$product_id.' AND PR.product_id = P.id AND is_downloadable = "Yes"');
		return $download_arr;
	}

	public function setProductPreviewType()
	{
		$product_preview_type = '';
		if($this->product_id != '' && $this->product_id > 0)
		{
			$product_preview_type = Product::where('id', '=', $this->product_id)->pluck('product_preview_type');
		}
		return $product_preview_type;
	}

	public function getProductResourceCount($resource_type)
	{
		$count = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$count = ProductResource::whereRaw('product_id = ? AND resource_type = ? ', array($this->product_id, $resource_type))->count();
		}
		return $count;
	}

	public function getUserLastProductNote($user_id)
	{
		return ProductLog::whereRaw('product_id = ? AND user_id = ?', array($this->product_id, $user_id))->orderBy('id', 'DESC')->pluck('notes');
	}

	public function getCategoryArr($category_id)
	{
		$cache_key = 'GCACK_'.$category_id;
		if (($cat_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$cat_details = DB::select('SELECT parent.category_name, parent.id, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? ORDER BY node.category_left;', array($category_id));
			HomeCUtil::cachePut($cache_key, $cat_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		//$cat_details = DB::select('select parent.category_name, parent.id, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? ORDER BY parent.category_left', array($category_id));
		return $cat_details;
	}

	public function addProductViewCount($product_id)
	{
		//To increment the view count.
		Product::where('id', '=', $product_id)->increment('total_views');
	}

	public function updateLastUpdatedDate()
	{
	 	if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
			Product::whereRaw('id = ?', array($this->product_id))->update(array('last_updated_date' => DB::raw('NOW()')));
		}
	}

	public function getProductCountForCategory($category_id)
	{
		$this->sub_category_ids = $this->getSubCategoryIds($category_id);
		if(isset($sub_category_ids) && count($sub_category_ids) == 0)
			$this->sub_category_ids = $category_id;

		$product_count = Product::whereIn('product_category_id', $this->sub_category_ids)->count();
	    return $product_count;
	}

	public function isCategoryExists($category_id)
	{
		$cat_details = Products::getCategoryDetails($category_id);
		$category_count = count($cat_details);
	    return $category_count;
	}

	public function isCategoryProductExists($category_id)
	{
		$product = Products::initialize();
		$product_count = $product->getProductCountForCategory($category_id);
		return $product_count;
	}

	public function deleteCategory($category_id)
	{
		// check category exist or not
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_category_not_found')));
		}

		// check products added for the selected category or its subcategories
		if($this->isCategoryProductExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_category_in_use')));
		}

		// delete category details in all assigned attributes & category image.
		$this->sub_category_ids = $this->getSubCategoryIds($category_id);
		$cat_details = ProductCategory::whereIn('id', $this->sub_category_ids)->get(array('id'));
		if(count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				//echo "<br>cat: ".$cat->id;exit;
				// Delete all attributes assigned to the selected category & its subcategories
				ProductCategoryAttributes::whereRaw('category_id = ?', array($cat->id))->delete();
			}
		}

		//store the values of the left and right of the category to be deleted
		//delete all those cateogries b/w the above 2
		// update the cateogies to the right of the deleted category  - reduce left and right bu width of the deleted category
		$cat_info = Products::getCategoryDetails($category_id);
		if(count($cat_info) > 0)
		{
			$category_left = $cat_info->category_left;
			$category_right = $cat_info->category_right;
			$width = $category_right - $category_left + 1;

			ProductCategory::whereRaw(DB::raw('category_left  between  '. $category_left.' AND '.$category_right))->delete();

			//To update category left
			ProductCategory::whereRaw(DB::raw('category_left >  '.$category_right))->update(array("category_left" => DB::raw('category_left - '. $width)));

			//To update category right
			ProductCategory::whereRaw(DB::raw('category_right >  '.$category_right))->update(array("category_right" => DB::raw('category_right - '. $width)));
		}
		$array_multi_key = array('root_category_id_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		return json_encode(array('status' => 'success', 'category_id' => $category_id));
	}

	public function isAttributeAlreadyAssigned($attribute_id, $category_id)
	{
		$category_attr_count = ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->count();
		return $category_attr_count;
	}

	public function assignAttributeForCategory($attribute_id, $category_id)
	{
		$attr = new Attribute();
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_category_not_found')));
		}
		else if(!$attr->isAttributeExists($attribute_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_attribute_not_found')));
		}
		else if($this->isAttributeAlreadyAssigned($attribute_id, $category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.prodcut_attribute_already_assigned')));
		}

		$input_arr['attribute_id'] = $attribute_id;
		$input_arr['category_id'] = $category_id;
		$input_arr['date_added'] = DB::raw('NOW()');
		$cat_attribute_id = ProductCategoryAttributes::insertGetId($input_arr);
		return json_encode(array('status' => 'success'));
	}

	public function removeAssignedAttributeForCategory($category_id, $attribute_id)
	{
		$attr = new Attribute();
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_category_not_found')));
		}
		else if(!$attr->isAttributeExists($attribute_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_attribute_not_found')));
		}
		$affectedRows = ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->delete();
		if($affectedRows)
		{
			return json_encode(array('status' => 'success'));
		}
		else
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.product_error_in_removing_attribute')));
		}
	}

	public function updateProductSold($product_id, $item_qty=1)
	{
		Product::whereRaw('id = ?', array($product_id))->update(array('product_sold' => DB::Raw('product_sold + '.$item_qty) ));
	}

	public function getGroupPriceDetailsById($product_id, $group_id, $quantity = 1, $limit = 0, $allow_cache = true)
	{
		$cache_key = 'GGPDBID_'.$product_id.'_'.$group_id.'_'.$quantity;
		$product_price_query = ProductPriceGroups::Select("range_start", "range_end", "currency", "price", "price_usd", "discount_percentage", "discount",
													  "discount_usd", "added_on", "price_group_id")
											->whereRaw('product_id = ?', array($product_id));
		//if ($quantity > 1) {
		//	$product_price_group = $product_price_group->whereRaw('(range_start <= ? AND (range_end >= ? OR range_end = -1))', array($quantity, $quantity));
		//}
		//$product_price_group = $product_price_group->orderBy('price_group_id', 'asc');

		if($limit > 0)
		{
			$cache_key .= 'L'.$limit;
			$product_price_query = $product_price_query->take($limit);
		}
		if (!$allow_cache || (($product_price_group = HomeCUtil::cacheGet($cache_key)) === NULL)) {
			$product_price_group = $product_price_query->get();
			HomeCUtil::cachePut($cache_key, $product_price_group, Config::get('generalConfig.cache_expiry_minutes'));
		}

		$group_price_details = array();
		$i = 0;
		if (!count($product_price_group)) {
			$group_price_details[] = array('range_start' => '1', 'range_end' => '-1', 'price' => '', 'discount_percentage' => '', 'discount' => '',
											'group_field' => 'selGroup_'.$group_id.'_'.$i,
											'group_elements_field' => 'selGroupFields_'.$group_id.'_'.$i,
											'range_start_field' => 'range_start_'.$group_id.'_'.$i,
											'range_end_field' => 'range_end_'.$group_id.'_'.$i,
											'price_field' => 'price_'.$group_id.'_'.$i,
											'discount_percentage_field' => 'discount_percentage_'.$group_id.'_'.$i,
											'discount_field' => 'discount_'.$group_id.'_'.$i,
											'error_field' => 'error_'.$group_id.'_'.$i,
											'delete_field' => 'delete_'.$group_id.'_'.$i);
		} else {
			foreach($product_price_group as $each_group_price){
				$each_data['range_start'] = $each_group_price->range_start;
				$each_data['range_end'] = $each_group_price->range_end;
				$each_data['currency'] = $each_group_price->currency;
				$each_data['price'] = $each_group_price->price;
				$each_data['price_usd'] = $each_group_price->price_usd;
				$each_data['discount_percentage'] = $each_group_price->discount_percentage;
				$each_data['discount'] = $each_group_price->discount;
				$each_data['discount_usd'] = $each_group_price->discount_usd;
				$each_data['added_on'] = $each_group_price->added_on;

				$each_data['group_field'] = 'selGroup_'.$group_id.'_'.$i;
				$each_data['group_elements_field'] = 'selGroupFields_'.$group_id.'_'.$i;
				$each_data['range_start_field'] = 'range_start_'.$group_id.'_'.$i;
				$each_data['range_end_field'] = 'range_end_'.$group_id.'_'.$i;
				$each_data['price_field'] = 'price_'.$group_id.'_'.$i;
				$each_data['discount_percentage_field'] = 'discount_percentage_'.$group_id.'_'.$i;
				$each_data['discount_field'] = 'discount_'.$group_id.'_'.$i;
				$each_data['error_field'] = 'error_'.$group_id.'_'.$i;
				$each_data['delete_field'] = 'delete_'.$group_id.'_'.$i;

				$group_price_details[] = $each_data;
				$i++;
			}
		}
		return $group_price_details;
	}

	public function updateGroupPriceDetailsById($product_id, $product_price_groups)
	{
		$this->deleteGroupPriceDetails($product_id);
	 	//Loop each group range and validate
	 	foreach($product_price_groups as $key => $each_group){
	 		$group_id = $each_group['id'];
			foreach($each_group['price_details'] as $index => $each_group_range){
				if ($each_group_range['range_end'] != '' && $each_group_range['price'] != '') {
					$data['product_id'] = $product_id;
					$data['group_id'] = $group_id;
			 		$data['range_start'] = $each_group_range['range_start'];
			 		$data['range_end'] = $each_group_range['range_end'];
			 		$data['currency'] = Config::get('generalConfig.site_default_currency');
			 		$data['price'] = $each_group_range['price'];
			 		$data['price_usd'] = $each_group_range['price'];
			 		$data['discount_percentage'] = $each_group_range['discount_percentage'];
			 		$data['discount'] = $each_group_range['discount'];
			 		$data['discount_usd'] = $each_group_range['discount'];
			 		$data['added_on'] = DB::raw('NOW()');
			 		ProductPriceGroups::insertGetId($data);
				}
			}
	 	}
	}

	public function deleteGroupPriceDetails($product_id)
	{
		ProductPriceGroups::whereRaw('product_id = ?', array($product_id))->delete();
	}

	public function addPackageDetails($inputs = array())
	{
		//echo '<pre>';print_r($inputs);echo '</pre>';exit;
		$id = $inputs['id'];
		$product_id = $inputs['id'];
		$weight = $inputs['weight'];
		$length = $inputs['length'];
		$width = $inputs['width'];
		$height = $inputs['height'];
		$custom = isset($inputs['custom'])?$inputs['custom']:'NO';
		$first_qty = isset($inputs['first_qty'])?$inputs['first_qty']:'';
		$additional_qty = isset($inputs['additional_qty'])?$inputs['additional_qty']:'';
		$additional_weight = isset($inputs['additional_weight'])?$inputs['additional_weight']:'';
		$validator = Validator::make
		(
  			array('weight' => $weight,'length' =>$length,'width' => $width,'height' => $height,'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight),
    		array('weight' => 'required|numeric|between:0.001,500','length' => 'required|integer|between:1,700','width' => 'required|integer|between:1,700','height' => 'required|integer|between:1,700','first_qty'=> 'integer','additional_qty' => 'integer','additional_weight' => 'numeric')
		);
		if($validator->passes())
		{
			$package = DB::table('product_package_details')->where('product_id',$product_id)->get();
			if(count($package) == 0)
			{
				//echo $size_add.$size_add1.$size_add2;exit;
				$package_details_id = DB::table('product_package_details')->insertGetId(
				    array('weight' => $weight, 'length' => $length, 'width' => $width, 'height' => $height, 'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight, 'custom' => $custom, 'product_id' => $id)
				);
			}
			else
			{
				$package_details_id = isset($package[0]->id) ? $package[0]->id : 0;
				DB::table('product_package_details')
									->where('product_id', '=', $product_id)
					            	->update(array('weight' => $weight, 'length' => $length, 'width' => $width, 'height' => $height, 'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight, 'custom' => $custom, 'product_id' => $id)
				);
			}
			return json_encode(array('status' => 'success', 'package_details_id' => $package_details_id));
		}
		else
		{
			return json_encode(array('status' => 'error', 'error_messages' => $validator->messages()->first()));
		}
	}

	public function removePackageDetails($product_id)
	{
		$affeted_rows = DB::table('product_package_details')->where('product_id',$product_id)->delete();
		return $affeted_rows;
	}

	public function findShippingTemplateIdFromName($shipping_template_name = '')
	{
		$shippingTemplateService = new ShippingTemplateService();
		$template_det = $shippingTemplateService->findShippingTemplateIdFromName($shipping_template_name);
		return $template_det;
	}

	public function removeItemImageFile($p_id, $type='')
	{
		$productService = new ProductService();
		$productService->removeItemImageFile($p_id, $type);
	}
}