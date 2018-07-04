<?php

class ShippingTemplateService
{
	public function setShippingSrchArr($input)
	{
		$this->srch_arr['template_name']= (isset($input['template_name']) && $input['template_name'] != '') ? $input['template_name'] : "";
	}
	public function buildShippingTemplatesQuery($user_id = 0)
	{
		$this->qry = ShippingTemplates::orderBy('id', 'desc');
		if($this->srch_arr['template_name']!='')
		{
			$temp_name = '%'.$this->srch_arr['template_name'].'%';
			$this->qry->where('template_name', 'like', $temp_name);
		}
		if(is_null($user_id) || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();
		$this->qry->where('user_id', $user_id);
		return $this->qry;
	}
	public function getShippingTemplates($user_id = '')
	{
		if($user_id!='')
			$user_id = BasicCUtil::getLoggedUserId();
		$shipping_templates = ShippingTemplates::orderBy('id', 'asc')->get();
		return $shipping_templates;
	}

	public function getUntitledNameForShippingTemplate($user_id = '')
	{

		if(is_null($user_id) || $user_id=='')
			return 'Untitled 1';

		$untitled_name_det = ShippingTemplates::Select('template_name')->where('template_name', 'like', 'Untitled %')->where('user_id', $user_id)->orderBy('id', 'desc')->first();
		if(count($untitled_name_det) > 0)
		{
			$template_name = $untitled_name_det->template_name;
			$template_name = explode(' ',$template_name);
			$untitled_num = isset($template_name[1])?intval($template_name[1]):1;
			if($untitled_num >=1)
				return 'Untitled '.($untitled_num+1);
			else
				return 'Untitled 1';
		}
		else
			return 'Untitled 1';

	}

	public function findShippingTemplateIdFromName($shipping_template_name = '')
	{
		$shipping_templates = ShippingTemplates::where('template_name', $shipping_template_name)->first();
		return $shipping_templates;
	}

	public function getDefaultShippingTemplate($user_id = '')
	{
		$default_template_id = 0;
		if($user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();
		$default_template_id = ShippingTemplates::whereRaw('user_id = ? AND is_default = ?', array($user_id, 1))->pluck('id');
		return $default_template_id;
	}

	public function getShippingTemplatesList($user_id = '')
	{
		if($user_id=='')
			$user_id = BasicCUtil::getLoggedUserId();
		$shipping_templates = ShippingTemplates::orderBy('template_name', 'asc')->where('user_id', '=', $user_id)->lists('template_name','id');
		return $shipping_templates;
	}
	public function getShippingTemplatesCompaniesList($template_id = '')
	{
		$shipping_temp_list = array();
		if($template_id != '' && $template_id > 0) {
			$cache_key = 'STCLCK_'.$template_id;
			if (($shipping_temp_list = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$shipping_temp_list = ShippingTemplateCompanies::Select('shipping_template_companies.id', 'shipping_template_companies.template_id', 'shipping_template_companies.company_id', 'shipping_template_companies.fee_type', 'shipping_template_companies.fee_discount', 'shipping_template_companies.fee_discount as discount', 'shipping_template_companies.delivery_type', 'shipping_template_companies.days', 'shipping_companies.company_name')
											->leftjoin('shipping_companies', 'shipping_companies.id', '=' ,'shipping_template_companies.company_id')
											->whereRaw('template_id = ?', array($template_id))
											->whereRaw('shipping_companies.display = ?', array(1))
											->get();
				HomeCUtil::cachePut($cache_key, $shipping_temp_list, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		return $shipping_temp_list;
	}

	public function getShippingTemplatesCompaniesListByCompanyId($template_id = '', $company_id = 0)
	{
		$shipping_temp_list = array();
		if($template_id != '' && $template_id > 0 && $company_id > 0) {
			$shipping_temp_list = ShippingTemplateCompanies::Select('shipping_template_companies.id', 'shipping_template_companies.template_id', 'shipping_template_companies.company_id', 'shipping_template_companies.fee_type', 'shipping_template_companies.fee_discount', 'shipping_template_companies.fee_discount as discount', 'shipping_template_companies.delivery_type', 'shipping_template_companies.days', 'shipping_companies.company_name')
										->leftjoin('shipping_companies', 'shipping_companies.id', '=' ,'shipping_template_companies.company_id')
										->whereRaw('shipping_template_companies.template_id = ?', array($template_id))
										->whereRaw('shipping_template_companies.company_id = ?', array($company_id))
										->whereRaw('shipping_companies.display = ?', array(1))
										->get();
		}
		return $shipping_temp_list;
	}

	public function getShippingTemplateFeeCustomByCountry($template_id, $company_id, $product_id = 0, $quantity = 1, $to_country_details=array(), $package_details = array(), $from_country_details = array())
	{
		//To country_details
		$country_id = (is_array($to_country_details) && isset($to_country_details['country_id']) && $to_country_details['country_id'] > 0)?$to_country_details['country_id']:0;
		$zip_code = (is_array($to_country_details) && isset($to_country_details['zip_code']) && $to_country_details['zip_code']!='')?$to_country_details['zip_code']:'';

		//From country details
		$shipping_from_country = (is_array($from_country_details) && isset($from_country_details['country_id']) && $from_country_details['country_id'] > 0)?$from_country_details['country_id']:0;
		$cache_key = 'STFCBC_'.$template_id.'_'.$company_id.'_'.$country_id;
		if (($shipping_custom_fee_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$shipping_custom_fee_details = ShippingTemplateFeeCustom::Select('shipping_template_fee_custom.id', 'shipping_template_fee_custom.template_id'
											, 'shipping_template_fee_custom.company_id', 'shipping_template_fee_custom.template_company_id'
											, 'shipping_template_fee_custom.country_selected_type', 'shipping_template_fee_custom.shipping_setting'
											, 'shipping_template_fee_custom.fee_type', 'shipping_template_fee_custom.discount'
											, 'shipping_template_fee_custom.custom_fee_type', 'shipping_template_fee_custom.min_order'
											, 'shipping_template_fee_custom.max_order', 'shipping_template_fee_custom.cost_base_weight'
											, 'shipping_template_fee_custom.extra_units', 'shipping_template_fee_custom.extra_costs'
											, 'shipping_template_fee_custom.initial_weight', 'shipping_template_fee_custom.initial_weight_price')
											->leftjoin('shipping_template_fee_custom_countries', 'shipping_template_fee_custom.id', '=' ,'shipping_template_fee_custom_countries.shipping_template_fee_custom_id')
											->where('shipping_template_fee_custom_countries.template_id', $template_id)
											->where('shipping_template_fee_custom_countries.company_id', $company_id)
											->where('shipping_template_fee_custom_countries.country_id', $country_id)
											->get();
			 HomeCUtil::cachePut($cache_key, $shipping_custom_fee_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		$ret_arr = array();
		if(count($shipping_custom_fee_details) > 0) {
			foreach($shipping_custom_fee_details as $key => $val) {
				$shipping_fee = 0;
				if($val->shipping_setting == 'dont_ship_to')
				{
					$ret_arr = array('return' => false, 'message' => 'Do not ship to this country');
				}
				else if($val->fee_type == 1)//Custom
				{
					$ret_arr = $this->getShippingFeeForCustomFeeType($val, $product_id, $quantity, $package_details);
				}
				else if($val->fee_type == 3)//Free
				{
					$shipping_fee = 0;
					$ret_arr = array('return' => true, 'amount' => $shipping_fee);
				}
				else
				{
					$ret_arr = $this->getShippingFeeStandard($val, $product_id, $quantity, $to_country_details, $package_details, $from_country_details);
				}
			}
		}
		else {
			$cache_key_new = 'SCFDOCK_'.$template_id.'_'.$company_id;
			if (($shipping_custom_fee_details_other = HomeCUtil::cacheGet($cache_key_new)) === NULL) {
				$shipping_custom_fee_details_other = ShippingTemplateFeeCustom::where('template_id', $template_id)
													->where('company_id', $company_id)
													->where('country_selected_type', 'other_countries')
													->get();
				HomeCUtil::cachePut($cache_key_new, $shipping_custom_fee_details_other, Config::get('generalConfig.cache_expiry_minutes'));
			}

			if(count($shipping_custom_fee_details_other) > 0) {
				foreach($shipping_custom_fee_details_other as $key => $val) {
					$shipping_fee = 0;
					if($val->shipping_setting == 'dont_ship_to') {
						$ret_arr = array('return' => false, 'message' => 'Do not ship to this country');
					}
					else if($val->fee_type == 1)//Custom
					{
						$ret_arr = $this->getShippingFeeForCustomFeeType($val, $product_id, $quantity, $package_details);
					}
					else if($val->fee_type == 3)//Free
					{
						$shipping_fee = 0;
						$ret_arr = array('return' => true, 'amount' => $shipping_fee);
					}
					else
					{
						$ret_arr = $this->getShippingFeeStandard($val, $product_id, $quantity, $to_country_details, $package_details, $from_country_details);
					}
				}
			}
			else
			{
				$ret_arr = array('return' => false, 'message' => 'Do not ship to this country');
			}
		}
		return $ret_arr;
	}

	public function getShippingFeeForCustomFeeType($data, $product_id, $quantity, $package_details = array())
	{
		$shipping_fee = 0;
		$fee_custom_id = $data->id;
		if($data->custom_fee_type == 1) { // By Quantity
			if ($quantity < $data->min_order) {
				$ret_arr = array('return' => false, 'message' => 'Quantiry less than minimum order: '.$data->min_order);
				return $ret_arr;
			}

			$min_order = $data->min_order;
			$max_order = $data->max_order;
			$cost_base_weight = $data->cost_base_weight;
			$extra_units = $data->extra_units;
			$extra_costs = $data->extra_costs;
			$additonal_cost = 0;
			//initial shipping charges
			$shipping_fee = $cost_base_weight;

			if($quantity > $max_order) {
				$extra_quantity = $quantity - $max_order;
				if($extra_units > 0) {
				 	$ceil_qty = ceil($extra_quantity / $extra_units);
				 	$additonal_cost = $ceil_qty * $extra_costs;
				}
			}
			$shipping_fee = $shipping_fee + $additonal_cost;
			$ret_arr = array('return' => true, 'amount' => $shipping_fee);
			return $ret_arr;
		}
		else { // By Weight
			$product_package_details = $this->getShippingProductPackageDetails($package_details, $product_id, $quantity);
			$ret_arr = array('return' => false, 'message' => 'Weight range not able to ship');
			if(!empty($product_package_details))
			{
				if(isset($product_package_details) && $product_package_details['calculated_based_qunatity'])
					$prod_weight = $product_package_details['weight'];
				else
					$prod_weight = $quantity * $product_package_details['weight'];
				$initial_weight = $data->initial_weight;
				$initial_weight_price = $data->initial_weight_price;
				$additonal_cost = 0;
				//initial shipping charges
				$shipping_fee = $initial_weight_price;

				if($prod_weight > $initial_weight) {
					$custom_weight_details = ShipppingTemplateFeeCustomWeight::where('shipping_template_fee_custom_id', $fee_custom_id)->get();
					if(count($custom_weight_details) > 0)
					{
						$additional_weight = $prod_weight - $initial_weight;
						$range_found = false;
						foreach($custom_weight_details as $custom_weight){
							if ($prod_weight >= $custom_weight->weight_from && $prod_weight <= $custom_weight->weight_to) {
								$range_found = true;
								//$custom_weight->additional_weight;
								$ceil_qty = ceil($additional_weight / $custom_weight->additional_weight);
								$additonal_cost = $ceil_qty * $custom_weight->additional_weight_price;
							}
						}
						if (!$range_found) {
							return $ret_arr = array('return' => false, 'message' => 'Weight range not able to ship');
						}
						$shipping_fee = $shipping_fee + $additonal_cost;
					}
					else
					{
						return $ret_arr = array('return' => false, 'message' => 'Weight range not able to ship');
					}
				}
				$ret_arr = array('return' => true, 'amount' => $shipping_fee);
			}
			return $ret_arr;
		}
	}

	public function getShippingFeeStandard($company_details, $product_id, $quantity = 1, $to_country_details = array(), $package_details = array(), $from_country_details = array())
	{

		//To country_details
		$country_id = (is_array($to_country_details) && isset($to_country_details['country_id']) && $to_country_details['country_id'] > 0)?$to_country_details['country_id']:0;
		$zip_code = (is_array($to_country_details) && isset($to_country_details['zip_code']) && $to_country_details['zip_code']!='')?$to_country_details['zip_code']:'';

		//From country details
		$shipping_from_country = (is_array($from_country_details) && isset($from_country_details['country_id']) && $from_country_details['country_id'] > 0)?$from_country_details['country_id']:0;
		$shipping_from_zip_code = (is_array($from_country_details) && isset($from_country_details['zip_code']) && $from_country_details['zip_code']!='')?$from_country_details['zip_code']:'';

		$company_id = $company_details->company_id;
		$discount = $company_details->discount;
		//Log::info('companies id==>'.$company_id);
		$available_companies = array(1, 3, 5, 6, 12, 13, 15, 16, 28, 29, 30, 31, 32, 33);
		//echo "<br>company_id: ".$company_id;
		$prod_obj = Products::initialize();

		$product_package_details = $this->getShippingProductPackageDetails($package_details, $product_id, $quantity);
		//echo "<pre>";print_r($product_package_details);echo "</pre>";

		if(in_array($company_id, $available_companies))
		{
			//echo "<pre>";print_r($product_package_details);echo "</pre>";
			if(!empty($product_package_details))
			{
				/*$country_codes = Config::get('generalConfig.country_code_iso2');
				$country_code = isset($country_codes[$country_id])?$country_codes[$country_id]:'';
				if($country_code == '')
					$country_code = 'CN';*/
				//echo "<br>company_id: ".$company_id;//exit;
				if($shipping_from_country > 0)
				{
					$shipping_from = $this->getShippingCountryDetailFromId($shipping_from_country, $shipping_from_zip_code);
				}
				else
				{
					$shipping_from = $this->getShippingFromCountry($prod_obj, $product_id, $shipping_from_zip_code);
				}
				if (!isset($shipping_from['country'])) {
					return array('return' => false, 'message' => 'Select shipping from country.');
				}

				$from_country = $shipping_from['country'];
				$from_zip_code = $shipping_from['zip_code'];
				$from_city = $shipping_from['city'];
				//$from_city = 'BEIJING';
				//$china_post_group = 0;
				//if($from_country == 'PK')
//					$from_city = 'KARACHI';

				$shipping_to = $this->getShippingCountryDetailFromId($country_id, $zip_code);
				if(!empty($shipping_to))
				{
					$to_zip_code = $shipping_to['zip_code'];
					$to_country = $shipping_to['country'];
					$to_city = $shipping_to['city'];
					$china_post_group = $shipping_to['china_post_group'];
				}
				$config = array(
					// Weight
					'weight' => $product_package_details['weight'], // Default = 1
					'weight_units' => 'kg', // lb (default), oz, gram, kg

					// Size
					'size_length' => $product_package_details['length'], // Default = 8
					'size_width' => $product_package_details['width'], // Default = 4
					'size_height' => $product_package_details['height'], // Default = 2
					'size_units' => 'CM', // in (default), feet, cm

					// From
					'from_zip' => $from_zip_code, //97210,
					'from_state' => "", // Only Required for FedEx
					'from_country' => $from_country,//"US",
					'from_city' => $from_city,
					'from_county_id' => $shipping_from_country,

					// To
					'to_city' => $to_city,
					'to_zip' => $to_zip_code,
					'to_state' => "", // Only Required for FedEx
					'to_country' => $to_country, //$country_code,
					'to_country_id' => $country_id,
					'china_post_group' => $china_post_group,

					// Service Logins
					'ups_access' => '0C2D05F55AF310D0', // UPS Access License Key
					'ups_user' => 'dwstudios', // UPS Username
					'ups_pass' => 'dwstudios', // UPS Password
					'ups_account' => '81476R', // UPS Account Number
					'usps_user' => '229DARKW7858', // USPS User Name
					'fedex_account' => '510087020', // FedEX Account Number
					'fedex_meter' => '100005263' // FedEx Meter Number
				);



				//echo "<pre>";print_r($config);echo "</pre>";
				$ship = new ShippingCalculator($config);
				// Get Rate
				if($company_id == 1)
				{
					$rates = $ship->calculate('ems', 'INTERNATIONAL_EMS_RATE');
				}
				else if($company_id == 3)
				{
					$rates = $ship->calculate('ems', 'EXPRESS_EMS_RATE'); //e-EMS
				}
				else if($company_id == 5)
				{
					$rates = $ship->calculate('china_post_air_mail', 'M_BAG_RATE'); //china post air mail - M bag
				}
				else if($company_id == 6)
				{
					$rates = $ship->calculate('china_post_air_mail', 'SMALL_PACKET_RATE'); //china post air mail - Small packet
				}
				else if($company_id == 29)
				{
					$rates = $ship->calculate('ems', 'CHINA_EXPRESS_EMS_RATE');
				}
				else if($company_id == 12)
				{
					$rates = $ship->calculate('ups','07');
				}
				else if($company_id == 13)
				{
					$rates = $ship->calculate('ups','08');
				}
				else if($company_id == 15)
				{
					$rates = $ship->calculate('fedex','INTERNATIONAL_PRIORITY');
				}
				else if($company_id == 16)
				{
					$rates = $ship->calculate('fedex','INTERNATIONAL_ECONOMY');
				}
				else if($company_id == 28)
				{
					$rates = $ship->calculate('fedex','INTERNATIONAL_PRIORITY_FREIGHT');
				}
				else if($company_id == 30)
				{
					$rates = $ship->calculate('DHL','express_worldwide');
				}
				else if($company_id == 31)
				{
					$rates = $ship->calculate('DHL','express_12');
				}
				else if($company_id == 32)
				{
					$rates = $ship->calculate('DHL','express_9');
				}
				else if($company_id == 33)
				{
					$rates = $ship->calculate('DHL','express_easy');
				}

				/*if($company_id == 4 || $company_id == 30 || $company_id == 31 || $company_id == 32)
				{
					$result = $ship->calculate('DHL', 'all');
					if(isset($result['return']) && $result['return'] == true && isset($result['result']) && !empty($result['result']))
					{
						$companies_rate = $result['result'];
						$amount = '';
						foreach($companies_rate as $rate)
						{
							if($company_id == 30 & trim($rate['service_name']) == 'EXPRESS WORLDWIDE')
							{
								$amount = $rate['amount'];
								break;
							}
							if($company_id == 31 & trim($rate['service_name']) == 'EXPRESS 12:00')
							{
								$amount = $rate['amount'];
								break;
							}
							if($company_id == 32 & trim($rate['service_name']) == 'DOMESTIC EXPRESS')
							{
								$amount = $rate['amount'];
								break;
							}
						}
						if($amount!='')
						{
							$rates = array('return' => true, 'amount' => $amount);
						}
						else
						{
							$rates = array('return' => false, 'message' => 'Service not available');
						}
					}
					else
					{
						$rates = $result;
					}
				}*/

				if(isset($rates['return']) && $rates['return'] == true && isset($rates['amount']) && $product_package_details['custom'] == 'No')
				{
					if( $rates['amount'] &&  $rates['amount'] > 0 && $quantity > 0)
						$rates['amount'] =  $rates['amount'] * $quantity;
					if($discount > 0 &&  $rates['amount'] > 0) {
						 $rates['amount'] =  $rates['amount'] - ( $rates['amount'] * ($discount / 100));
					}

				}
				return $rates;
			}
			else
			{
				return array('return' => false, 'message' => 'There are some problem in package information. Kindly contact the seller.');
			}
		}
		else
		{
			$rate = Config::get('generalConfig.default_shipping_cost');
			if(!empty($product_package_details) && $product_package_details['custom'] == 'No' && $quantity > 0) {
				$rate = $rate * $quantity;
			}
			if($discount > 0 && $rate > 0) {
				$rate = $rate - ($rate * ($discount / 100));
			}
			return array('return' => true, 'amount' => $rate);
		}
	}
	public function getShippingTemplatesCompaniesListWithDetails($template_id = '', $company_id = 0, $product_id = 0, $quantity = '1', $to_country_details = array(), $package_details=array(), $from_country_details = array())
	{
		$shipping_template_details = array();

		//To country_details
		$country_id = (is_array($to_country_details) && isset($to_country_details['country_id']) && $to_country_details['country_id'] > 0)?$to_country_details['country_id']:0;
		$zip_code = (is_array($to_country_details) && isset($to_country_details['zip_code']) && $to_country_details['zip_code']!='')?$to_country_details['zip_code']:'';
		//From country details
		$shipping_from_country = (is_array($from_country_details) && isset($from_country_details['country_id']) && $from_country_details['country_id'] > 0)?$from_country_details['country_id']:0;
		$shipping_from_zip_code = (is_array($from_country_details) && isset($from_country_details['zip_code']) && $from_country_details['zip_code']!='')?$from_country_details['zip_code']:'';


		if($country_id=='' || $country_id <=0)
			$country_id = CUtil::getShippingCountry();

		if($product_id > 0)
		{
			$prod_obj = Products::initialize($product_id);
			/*$product_details = $prod_obj->getProductDetails();
			$template_id = $product_details['shipping_template'];*/

			if($template_id != '' && $template_id > 0)
			{
				if($company_id > 0)
					$shipping_companies_list = $this->getShippingTemplatesCompaniesListByCompanyId($template_id, $company_id);
				else
					$shipping_companies_list = $this->getShippingTemplatesCompaniesList($template_id);

				if(count($shipping_companies_list) > 0)
				{
					if($shipping_from_country > 0)
					{
						$shipping_from = $this->getShippingCountryDetailFromId($shipping_from_country, $shipping_from_zip_code);
					}
					else
					{
						$shipping_from = $this->getShippingFromCountry($prod_obj, $product_id, $shipping_from_zip_code);
					}

					$shipping_to = $this->getShippingCountryDetailFromId($country_id, $zip_code);

					foreach($shipping_companies_list as $key => $vlaues) {

						$shipping_type = 'Standard';
						$error_message = '';
						$shipping_fee = '';

						//Entry check start
						$has_entry_check_err = false;
						$available_companies = array(1, 3, 5, 6, 7, 9, 10, 11, 18, 20, 25, 29);
						if(in_array($vlaues->company_id, $available_companies))
						{
							if(isset($shipping_from['country']) && $shipping_from['country'] != 'CN') {
								$has_entry_check_err = true;
								$error_message = Lang::get('shippingTemplates.error_service_not_allowed');
							}
							if(!$has_entry_check_err) {
								if(!empty($shipping_to)) {
									$to_country = $shipping_to['country'];
									if($shipping_from['country'] == 'CN' && $to_country == 'CN') {
										$has_entry_check_err = true;
										$error_message = Lang::get('shippingTemplates.error_service_not_allowed');
									}
								}
							}
							//Custom companies
							if(!$has_entry_check_err) {
								//7-HongKong Post Air Mail, 9-Singapore Post, 10-Swiss Post, 11-Sweden Post
								//20-Aramex, 25-CTR-LAND PICKUP, 27-Seller's Shipping Method
								$custom_companies = array(7, 9, 10, 11, 18, 20, 25, 27);
								if(in_array($vlaues->company_id, $custom_companies)) {
									$country_exits = 0;
									if (isset($shipping_to['country_name']))
										$country_exits = $this->chkShippingCountryExist($vlaues->company_id, $shipping_to['country_name']);
									if($country_exits == 0) {
										$has_entry_check_err = true;
										$error_message = Lang::get('shippingTemplates.error_service_not_allowed');
									}
								}
							}
							//Weight check
							if(!$has_entry_check_err) {
								if(in_array($vlaues->company_id, array(1, 3, 5, 6, 29))) {
									$product_package_details = $this->getShippingProductPackageDetails($package_details, $product_id, $quantity);
									if(!empty($product_package_details)) {
										$prod_weight = $product_package_details['weight'];
										if(in_array($vlaues->company_id, array(1, 3, 29))) {
											if($shipping_to['country'] == 'HK' && $prod_weight > 40) {//91 => Hong Kong
												$has_entry_check_err = true;
												$error_message = 'Hong Kong: Package weight not more than 40 kg.';
											} else if($shipping_to['country'] != 'HK' && $prod_weight > 30) {
												$has_entry_check_err = true;
												$error_message = 'Package weight not more than 30 kg.';
											}
										}
										else if(in_array($vlaues->company_id, array(5, 6))) {
											$china_post_max_weight = Config::get('generalConfig.china_post_airmail_mbag_max_weight');
											if($vlaues->company_id == 6)
												$china_post_max_weight = Config::get('generalConfig.china_post_airmail_small_packet_max_weight');
											if($prod_weight > $china_post_max_weight) {
												$has_entry_check_err = true;
												$error_message = 'Package weight not more than '.$china_post_max_weight.' kg.';
											}
										}
									}
								}
							}
						}
						//Entry check end
						if($vlaues->fee_type == 1)
						{
							$shipping_type = 'Custom';
							if(!$has_entry_check_err) {
								$custom_fee_details = $this->getShippingTemplateFeeCustomByCountry($template_id, $vlaues->company_id, $product_id, $quantity, $shipping_to,  $package_details, $from_country_details);
								if(is_array($custom_fee_details) && isset($custom_fee_details['return']) && $custom_fee_details['return'] == true && isset($custom_fee_details['amount']))
								{
									$shipping_fee = $custom_fee_details['amount'];
								}
								if(isset($custom_fee_details['message']) && $custom_fee_details['message']!='')
									$error_message = $custom_fee_details['message'];
							}
						}
						else if($vlaues->fee_type == 3)
						{
							$shipping_type = 'Free';
							if(!$has_entry_check_err) {
								$shipping_fee = 0;
							}
						}
						else
						{
							if(!$has_entry_check_err) {
								$shipping_fee_detail = $this->getShippingFeeStandard($vlaues, $product_id, $quantity, $shipping_to, $package_details, $from_country_details);
								if(is_array($shipping_fee_detail) && isset($shipping_fee_detail['return']) && $shipping_fee_detail['return'] == true && isset($shipping_fee_detail['amount']))
								{
									$shipping_fee = str_replace(',','',$shipping_fee_detail['amount']);
								}
								if(isset($shipping_fee_detail['message']) && $shipping_fee_detail['message']!='')
									$error_message = $shipping_fee_detail['message'];
							}
						}

						$shipping_times = '--';
						if($vlaues->delivery_type == 1)
							$shipping_times = 'Custom';
						else if($vlaues->delivery_type == 2) {
							$day_txt = ($vlaues->days > 0) ? Lang::get('common.days') : Lang::get('common.day');
							$shipping_times = '<strong>'.$vlaues->days.'</strong> '.$day_txt;
						}
						//if($shipping_type== 'Free' || ($shipping_type!= 'Free' && $shipping_fee > 0))
						//{
							$shipping_template_details[$key]['id'] = $vlaues->id;
							$shipping_template_details[$key]['template_id'] = $vlaues->template_id;
							$shipping_template_details[$key]['company_id'] = $vlaues->company_id;
							$shipping_template_details[$key]['fee_type'] = $vlaues->fee_type;
							$shipping_template_details[$key]['fee_discount'] = $vlaues->fee_discount;
							$shipping_template_details[$key]['delivery_type'] = $vlaues->delivery_type;
							$shipping_template_details[$key]['days'] = $vlaues->days;
							$shipping_template_details[$key]['company_name'] = $vlaues->company_name;

							$shipping_template_details[$key]['shipping_type'] = $shipping_type;
							$shipping_template_details[$key]['shipping_times'] = $shipping_times;
							$shipping_template_details[$key]['error_message'] = $error_message;

							//$shipping_fee = $vlaues->id * 10;//This is added for temporary display
							$shipping_template_details[$key]['shipping_fee'] = ($shipping_type != 'Free') ? $shipping_fee : 0;//This is added for temporary display
						//}

					}
				}
				//echo "<pre>";print_r($shipping_template_details);echo "</pre>";exit;
			}
		}
		else
		{
			if($template_id != '' && $template_id > 0) {
				if($company_id > 0)
					$shipping_companies_list = $this->getShippingTemplatesCompaniesListByCompanyId($template_id, $company_id);
				else
					$shipping_companies_list = $this->getShippingTemplatesCompaniesList($template_id);
				if(count($shipping_companies_list) > 0) {
					foreach($shipping_companies_list as $key => $vlaues) {
						$shipping_type = 'Standard';
						if($vlaues->fee_type == 1)
							$shipping_type = 'Custom';
						else if($vlaues->fee_type == 3)
							$shipping_type = 'Free';

						$shipping_times = '--';
						if($vlaues->delivery_type == 1)
							$shipping_times = 'Custom';
						else if($vlaues->delivery_type == 2) {
							$day_txt = ($vlaues->days > 0) ? 'Days' : 'Day';
							$shipping_times = '<strong>'.$vlaues->days.'</strong> '.$day_txt;
						}

						$shipping_template_details[$key]['id'] = $vlaues->id;
						$shipping_template_details[$key]['template_id'] = $vlaues->template_id;
						$shipping_template_details[$key]['company_id'] = $vlaues->company_id;
						$shipping_template_details[$key]['fee_type'] = $vlaues->fee_type;
						$shipping_template_details[$key]['fee_discount'] = $vlaues->fee_discount;
						$shipping_template_details[$key]['delivery_type'] = $vlaues->delivery_type;
						$shipping_template_details[$key]['days'] = $vlaues->days;
						$shipping_template_details[$key]['company_name'] = $vlaues->company_name;

						$shipping_template_details[$key]['shipping_type'] = $shipping_type;
						$shipping_template_details[$key]['shipping_times'] = $shipping_times;

						$shipping_fee = $vlaues->id * 10;//This is added for temporary display
						$shipping_template_details[$key]['shipping_fee'] = ($shipping_type != 'Free') ? $shipping_fee : 0;//This is added for temporary display
					}
				}
			}
		}
		return $shipping_template_details;
	}
	public function getShippingCompaniesList($category_id = '')
	{

		$shipping_companies = ShippingCompanies::where('display','=',1)->orderBy('category', 'asc')->orderBy('id', 'asc');
		if($category_id !='')
			$shipping_companies = $shipping_companies->where('category','=',$category_id);

		return $shipping_companies->get();
	}
	public function addShippingTemplate($template_det)
	{
		$shipping_temp_det = array();
		$shipping_temp_det['user_id'] = isset($template_det['user_id'])?$template_det['user_id']:BasicCUtil::getLoggedUserId();
		$shipping_temp_det['template_name'] = $template_det['template_name'];
		$shipping_temp_det['is_default'] = isset($template_det['is_default'])?$template_det['is_default']:0;
		$shipping_temp_det['status'] = isset($template_det['status'])?$template_det['status']:'Active';

		$shipping_id = ShippingTemplates::insertGetId($shipping_temp_det);
		return $shipping_id;
	}
	public function updateShippingTemplate($id, $ship_comp)
	{
		$ship_comp_id = ShippingTemplates::where('id', $id)->update($ship_comp);
		return $ship_comp_id;
	}
	public function chkShippingTemplateCompanyExistsByTemplate($template_id, $company_id)
	{
		$details = array();
		$template_comp_details = ShippingTemplateCompanies::whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))->first();
		if(count($template_comp_details))
		{
			$details['id'] = $template_comp_details['id'];
			$details['template_id'] = $template_comp_details['template_id'];
			$details['company_id'] = $template_comp_details['company_id'];
		}
		return $details;
	}
	public function addShippingTemplateCompany($ship_comp)
	{
		$ship_comp_id = ShippingTemplateCompanies::insertGetId($ship_comp);
		return $ship_comp_id;
	}
	public function updateShippingTemplateCompany($id, $temp_comp)
	{
		$tmep_comp_id = ShippingTemplateCompanies::where('id', $id)->update($temp_comp);
		return $tmep_comp_id;
	}
	public function addShippingTemplateFeeCustom($temp_comp_custom)
	{
		$ship_temp_comp_id = ShippingTemplateFeeCustom::insertGetId($temp_comp_custom);
		return $ship_temp_comp_id;
	}
	public function updateShippingTemplateFeeCustom($fee_custom_id, $temp_comp_custom)
	{
		ShippingTemplateFeeCustom::where('id', $fee_custom_id)->update($temp_comp_custom);
		return $fee_custom_id;
	}
	public function checkIsCustomShippingAvailable($template_id,$company_id)
	{
		return ShippingTemplateFeeCustom::where('template_id', $template_id)->where('company_id',$company_id)->count();
	}
	public function checkIsCustomDeliveryAvailable($template_id,$company_id)
	{
		return ShippingTemplateDeliveryCustom::where('template_id', $template_id)->where('company_id',$company_id)->count();
	}
	public function getShippingTemplateFeeCustom($fee_custom_id)
	{
		$fee_custom_arr = array();
		$fee_cust_details = ShippingTemplateFeeCustom::where('id',$fee_custom_id)->first();
		if(count($fee_cust_details) > 0)
		{
			$fee_custom_arr = $fee_cust_details->toArray();
			$countries = ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id',$fee_custom_id)->lists('country_id');
			$fee_custom_arr['countries'] = $countries;
			$custom_weight_det = array();$custom_weight_count = 0;
			if($fee_custom_arr['fee_type'] == 1 && $fee_custom_arr['custom_fee_type'] == 2)
			{
				$custom_weight_details = ShipppingTemplateFeeCustomWeight::where('shipping_template_fee_custom_id',$fee_custom_id)->get();
				if(count($custom_weight_details) > 0)
				{
					$custom_weight_det = $custom_weight_details->toArray();
					$custom_weight_count = count($custom_weight_det);
				}
			}
			$fee_custom_arr['custom_weight_details'] = $custom_weight_det;
			$fee_custom_arr['custom_weight_count'] = $custom_weight_count;
		}
		return $fee_custom_arr;
	}
	public function deleteShippingTemplateFeeCustom($fee_custom_id='')
	{
		if($fee_custom_id == '')
			return false;

		$deleted_countries = ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id',$fee_custom_id)->lists('country_id');
		$temp_comp_id = ShippingTemplateFeeCustom::where('id',$fee_custom_id)->pluck('template_company_id');

		$ship_temp_fee_custom_country = ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id',$fee_custom_id)->delete();
		ShipppingTemplateFeeCustomWeight::where('shipping_template_fee_custom_id', $fee_custom_id)->delete();
		ShippingTemplateFeeCustom::where('id',$fee_custom_id)->delete();


		return array('deleted_countries' => $deleted_countries, 'temp_comp_id' => $temp_comp_id);
	}
	public function checkAndDeleteShippingTemplateFeeCustom($temp_comp_id)
	{
		$count = ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type', '!=', 'other_countries')->count();
		if($count<=0)
			ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type', '=', 'other_countries')->delete();
	}
	public function addShippingTemplateFeeCustomCountry($fee_custom_country)
	{
		$ship_temp_fee_custom_country = ShippingTemplateFeeCustomCountries::insertGetId($fee_custom_country);
		return $ship_temp_fee_custom_country;
	}
	public function addShippingTemplateCustomWeight($fee_custom_weight)
	{
		$custom_weight_id = ShipppingTemplateFeeCustomWeight::insertGetId($fee_custom_weight);
		return $custom_weight_id;
	}
	public function deleteShippingTemplateCustomWeight($fee_custom_id)
	{
		ShipppingTemplateFeeCustomWeight::where('shipping_template_fee_custom_id', $fee_custom_id)->delete();
	}
	public function removeCustomWeightRecord($fee_custom_id)
	{
		$arr = array();
		$arr['initial_weight'] = '';
		$arr['initial_weight_price'] = '';
		ShippingTemplateFeeCustom::where('id',$fee_custom_id)->update($arr);
		ShipppingTemplateFeeCustomWeight::where('shipping_template_fee_custom_id', $fee_custom_id)->delete();
	}
	public function removeCustomQuanityRecord($fee_custom_id)
	{
		$arr = array();
		$arr['min_order'] = '';
		$arr['max_order'] = '';
		$arr['cost_base_weight'] = '';
		$arr['extra_units'] = '';
		$arr['extra_costs'] = '';
		ShippingTemplateFeeCustom::where('id',$fee_custom_id)->update($arr);
	}
	public function removeCustomStandardRecord($fee_custom_id)
	{
		$arr = array();
		$arr['discount'] = '';
		ShippingTemplateFeeCustom::where('id',$fee_custom_id)->update($arr);
	}
	public function deleteShippingTemplateFeeCustomCountry($fee_custom_id)
	{
		ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id', $fee_custom_id)->delete();
	}
	public function getSelectedTemplateCustomCountries($temp_comp_id)
	{
		$ship_temp_fee_custom_country = ShippingTemplateFeeCustomCountries::where('template_company_id',$temp_comp_id)->lists('country_id');
		return $ship_temp_fee_custom_country;
	}
	public function getSelectedTemplateCustomCountriesExcept($temp_comp_id,$fee_custom_id)
	{
		$ship_temp_fee_custom_country = ShippingTemplateFeeCustomCountries::where('template_company_id',$temp_comp_id)->where('shipping_template_fee_custom_id','!=',$fee_custom_id)->lists('country_id');
		return $ship_temp_fee_custom_country;
	}

	public function getShippingTemplateFeeCustomDetails($temp_comp_id)
	{
		$fee_custom_details = ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','!=', 'other_countries')->get();
		if(!$fee_custom_details->isEmpty())
		{
			foreach($fee_custom_details as $key => $temp_fee_custom)
			{
				$fee_custom_id = $temp_fee_custom->id;
				$fee_custom_country_ids = ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id',$fee_custom_id)->lists('country_id');
				$fee_custom_details[$key]['country_ids'] = $fee_custom_country_ids;
				$fee_custom_details[$key]['countries'] = Products::getCountiesNamesFromId(($fee_custom_country_ids));
			}
		}
		return $fee_custom_details;
	}
	public function getShippingTemplateFeeCustomOtherCountries($temp_comp_id)
	{
		$fee_custom_details = ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','=', 'other_countries')->first();
		return $fee_custom_details;
	}
	public function updateShippingTemplateFeeCustomOtherCountries($temp_comp_id, $fee_custom_details)
	{
		$fee_custom_details = ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','=', 'other_countries')->update($fee_custom_details);
		return $fee_custom_details;
	}
	public function addShippingTemplateDeliveryCustom($temp_comp_custom)
	{
		$ship_temp_comp_id = ShippingTemplateDeliveryCustom::insertGetId($temp_comp_custom);
		return $ship_temp_comp_id;
	}
	public function getShippingTemplateDeliveryCustomDetails($temp_comp_id)
	{
		$delivery_custom_details = ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','!=', 'other_countries')->get();
		if(!$delivery_custom_details->isEmpty())
		{
			foreach($delivery_custom_details as $key => $temp_delivery_custom)
			{
				$delivery_custom_id = $temp_delivery_custom->id;
				$fee_custom_country_ids = ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id',$delivery_custom_id)->lists('country_id');
				$delivery_custom_details[$key]['country_ids'] = $fee_custom_country_ids;
				$delivery_custom_details[$key]['countries'] = Products::getCountiesNamesFromId(($fee_custom_country_ids));
			}
		}
		return $delivery_custom_details;
	}
	public function getShippingTemplateDeliveryCustomOtherCountries($temp_comp_id)
	{
		$fee_custom_details = ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','=', 'other_countries')->first();
		return $fee_custom_details;
	}
	public function addShippingTemplateDeliveryCustomCountry($delivery_custom_country)
	{
		$ship_temp_fee_custom_country = ShippingTemplateDeliveryCustomCountries::insertGetId($delivery_custom_country);
		return $ship_temp_fee_custom_country;
	}
	public function deleteShippingTemplateDeliveryCustom($custom_delivery_id='')
	{
		if($custom_delivery_id == '')
			return false;

		$deleted_countries = ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id',$custom_delivery_id)->lists('country_id');
		$temp_comp_id = ShippingTemplateDeliveryCustom::where('id',$custom_delivery_id)->pluck('template_company_id');
		$ship_temp_fee_custom_country = ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id',$custom_delivery_id)->delete();
		ShippingTemplateDeliveryCustom::where('id',$custom_delivery_id)->delete();
		return array('deleted_countries' => $deleted_countries, 'temp_comp_id' => $temp_comp_id);
	}
	public function checkAndDeleteShippingTemplateDeliveryCustom($temp_comp_id)
	{
		$count = ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type', '!=', 'other_countries')->count();
		if($count<=0)
			ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type', '=', 'other_countries')->delete();
	}
	public function getShippingTemplateDeliveryCustom($custom_delivery_id)
	{
		$custom_delivery_arr = array();
		$custom_delivery_countries = ShippingTemplateDeliveryCustom::where('id',$custom_delivery_id)->first();
		if(count($custom_delivery_countries) > 0)
		{
			$custom_delivery_arr = $custom_delivery_countries->toArray();
			$countries = ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id',$custom_delivery_id)->lists('country_id');
			$custom_delivery_arr['countries'] = $countries;
		}
		return $custom_delivery_arr;
	}
	public function getSelectedDeliveryCustomCountries($temp_comp_id)
	{
		$ship_temp_fee_custom_country = ShippingTemplateDeliveryCustomCountries::where('template_company_id',$temp_comp_id)->lists('country_id');
		return $ship_temp_fee_custom_country;
	}
	public function getSelectedTemplateCustomDeliveryCountriesExcept($temp_comp_id,$custom_delivery_id)
	{
		$ship_temp_fee_custom_country = ShippingTemplateDeliveryCustomCountries::where('template_company_id',$temp_comp_id)->where('template_company_delivery_custom_id','!=',$custom_delivery_id)->lists('country_id');
		return $ship_temp_fee_custom_country;
	}
	public function deleteShippingTemplateDeliveryCustomCountry($delivery_custom_id)
	{
		ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id', $delivery_custom_id)->delete();
	}
	public function updateShippingTemplateDeliveryCustom($fee_custom_id, $temp_comp_custom)
	{
		ShippingTemplateDeliveryCustom::where('id', $fee_custom_id)->update($temp_comp_custom);
		return $fee_custom_id;
	}
	public function updateShippingTemplateDeliveryCustomOtherCountries($temp_comp_id, $other_custom_details)
	{
		$other_custom_details = ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->where('country_selected_type','=', 'other_countries')->update($other_custom_details);
		return $other_custom_details;
	}

	public function removeShippingTemplateCompanies($template_id = '')
	{
		if($template_id!='')
		{
			ShippingTemplateCompanies::where('template_id',$template_id)->delete();
		}
	}
	public function deleteShippingTemplateCompany($template_id = '', $company_id = '')
	{
		if($template_id > 0 && $company_id > 0)
		{
			ShippingTemplateCompanies::whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))->delete();
		}
	}
	public function getShippingTemplateDetails($template_id = '')
	{
		$shipping_temp_details = array();
		$shipping_temp = ShippingTemplates::where('id',$template_id)->first();
		$shipping_temp_comp = ShippingTemplateCompanies::where('template_id',$template_id)->get();
		$shipping_temp_details['shipping_template'] = $shipping_temp;
		$shipping_temp_details['shipping_temp_comp'] = $shipping_temp_comp;
		return $shipping_temp_details;
	}
	public function getShippingTemplateName($template_id)
	{
		$temp_name = ShippingTemplates::where('id',$template_id)->pluck('template_name');
		return $temp_name;
	}
	public function getShippingTemplateCompanyId($template_id,$company_id)
	{
		$template_company_id = ShippingTemplateCompanies::where('template_id',$template_id)->where('company_id',$company_id)->pluck('id');
		return $template_company_id;
	}
	public function getShippingTemplateCompanyName($company_id)
	{
		$template_company_name = ShippingCompanies::where('id',$company_id)->pluck('company_name');
		return $template_company_name;
	}
	public function getFeeType($template_id)
	{
		$fee_type['standard']=$fee_type['custom']=$fee_type['free']=array();
		$standard = $custom = $free = '';
		$company_name = DB::table('shipping_template_companies')
						->join('shipping_companies','shipping_template_companies.company_id' , '=' , 'shipping_companies.id')
		        		->where('template_id',$template_id)
		        		->get();
    	//return $company_name;
    	if(count($company_name) > 0) {
			foreach($company_name as $key => $values) {

				if($values->fee_type == 1)
                	$custom .= ($custom == '') ? $values->company_name : ', '.$values->company_name;
            	elseif($values->fee_type == 2)
					$standard .= ($standard == '') ? $values->company_name : ', '.$values->company_name;
            	elseif($values->fee_type == 3)
					$free .= ($free == '') ? $values->company_name : ', '.$values->company_name;
			}
		}
		$fee_type['standard'] = $standard;
		$fee_type['custom'] = $custom;
		$fee_type['free'] = $free;
		return $fee_type;
	}

	public function getCompanyName($template_id)
	{
		$company_name = DB::table('shipping_template_companies')
						->join('shipping_companies','shipping_template_companies.company_id' , '=' , 'shipping_companies.id')
		        		->where('template_id',$template_id)
		        		->get();
    	return $company_name;
	}
	public function getCompanyList($template_id)
	{
		$company_list = DB::table('shipping_template_companies')
						->join('shipping_companies','shipping_template_companies.company_id' , '=' , 'shipping_companies.id')
		        		->where('template_id',$template_id)
		        		->lists('company_name', 'company_id');
    	return $company_list;
	}
	public function getCompanyId($template_id)
	{
		$company_id = DB::table('shipping_template_companies')
						->join('shipping_companies','shipping_template_companies.company_id' , '=' , 'shipping_companies.id')
		        		->where('template_id',$template_id)
		        		->lists('company_id', 'company_name');
    	return $company_id;
	}
	public function getTemplateName($template_id)
	{
		$template_name = DB::table('shipping_templates')
						->where('id',$template_id)
		        		->first();
		return $template_name;
	}

	public function getCompanyDetails($template_id, $company_id)
	{
		$fee_type = DB::table('shipping_template_companies')
						->join('shipping_companies','shipping_template_companies.company_id' , '=' , 'shipping_companies.id')
						->where('template_id',$template_id)
						->where('shipping_template_companies.company_id',$company_id)
						->select('shipping_template_companies.template_id','shipping_template_companies.id','shipping_template_companies.company_id','shipping_template_companies.fee_type','shipping_template_companies.fee_discount','shipping_template_companies.delivery_type','shipping_template_companies.days')
		        		->first();
    	return $fee_type;
	}

	public function getCountriesDetails($template_id, $template_company_id)
	{
		$countries_details = DB::table('shipping_template_fee_custom')
						->join('shipping_template_companies', 'shipping_template_fee_custom.template_company_id', '=', 'shipping_template_companies.id')
						->select('shipping_template_fee_custom.country_selected_type', 'shipping_template_fee_custom.template_id','shipping_template_fee_custom.template_company_id', 'shipping_template_fee_custom.id','shipping_template_fee_custom.shipping_setting','shipping_template_fee_custom.fee_type','shipping_template_fee_custom.discount')
						->where('shipping_template_fee_custom.template_id', $template_id)
						->where('shipping_template_fee_custom.template_company_id', $template_company_id)
						->get();
    	return $countries_details;
	}
	public function getDeliveryCountriesDetails($template_id, $template_company_id)
	{
		$delivery_countries_details = DB::table('shipping_template_delivery_custom')
									->join('shipping_template_companies', 'shipping_template_delivery_custom.template_company_id', '=', 'shipping_template_companies.id')
									->where('shipping_template_delivery_custom.template_id', $template_id)
									->where('shipping_template_delivery_custom.template_company_id', $template_company_id)
									->get();
    	return $delivery_countries_details;
	}

	public function getCountries($template_id, $template_company_id, $shipping_template_fee_custom_id)
	{
		$countries = DB::table('shipping_template_fee_custom_countries')
						->leftjoin('shipping_template_fee_custom','shipping_template_fee_custom_countries.shipping_template_fee_custom_id' , '=' , 'shipping_template_fee_custom.id')
						->where('shipping_template_fee_custom.template_id', $template_id)
						->where('shipping_template_fee_custom_countries.template_company_id', $template_company_id)
						->where('shipping_template_fee_custom.id', $shipping_template_fee_custom_id)
						->get();
		return $countries;
	}

	public function getWeightDetails($shipping_template_fee_custom)
	{
	$weight_details = DB::table('shippping_template_fee_custom_weight')
						->where('shippping_template_fee_custom_weight.shipping_template_fee_custom_id', $shipping_template_fee_custom)
						->get();
		return $weight_details;
	}
	public function getCustomDetails($temp_comp_id)
	{
		$fee_custom_details = ShippingTemplateFeeCustom::where('template_company_id',$temp_comp_id)->get();
		if(!$fee_custom_details->isEmpty())
		{
			foreach($fee_custom_details as $key => $temp_fee_custom)
			{
				$fee_custom_id = $temp_fee_custom->id;
				$fee_custom_country_ids = ShippingTemplateFeeCustomCountries::where('shipping_template_fee_custom_id',$fee_custom_id)->lists('country_id');
				$fee_custom_details[$key]['country_ids'] = $fee_custom_country_ids;
				$fee_custom_details[$key]['countries'] = Products::getCountiesNamesFromId(($fee_custom_country_ids));
			}
		}

		return $fee_custom_details;
	}

	public function getDeliveryCustomDetails($temp_comp_id)
	{
		$delivery_custom_details = ShippingTemplateDeliveryCustom::where('template_company_id',$temp_comp_id)->get();
		if(!$delivery_custom_details->isEmpty())
		{
			foreach($delivery_custom_details as $key => $temp_delivery_custom)
			{
				$delivery_custom_id = $temp_delivery_custom->id;
				$fee_custom_country_ids = ShippingTemplateDeliveryCustomCountries::where('template_company_delivery_custom_id',$delivery_custom_id)->lists('country_id');
				$delivery_custom_details[$key]['country_ids'] = $fee_custom_country_ids;
				$delivery_custom_details[$key]['countries'] = Products::getCountiesNamesFromId(($fee_custom_country_ids));
			}
		}
		return $delivery_custom_details;
	}

	public function getDeleteTemplate($template_id = '')
	{
		$delete_template = array();
		if($template_id!='')
		{
			$delete_t = DB::table('shipping_templates')->where('id',$template_id)->delete();
			$delete_temp = DB::table('shipping_template_companies')->where('template_id',$template_id)->delete();
			$delete_tem = DB::table('shipping_template_fee_custom')->where('template_id',$template_id)->delete();
			$delete_te = DB::table('shipping_template_fee_custom_countries')->where('template_id',$template_id)->delete();
			$delete_template['delete_t'] = $delete_t;
			$delete_template['delete_temp'] = $delete_temp;
			$delete_template['delete_tem'] = $delete_tem;
			$delete_template['delete_te'] = $delete_te;
			return $delete_template;
		}
	}

	public function deleteFeeCustomRelatedTables($template_id, $company_id)
	{
		if($template_id > 0 && $company_id > 0)
		{
			$fee_custom = DB::table('shipping_template_fee_custom')
							->whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))
							->delete();

			$fee_custom_countries = DB::table('shipping_template_fee_custom_countries')
										->whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))
										->delete();

			$fee_custom_weight = DB::table('shippping_template_fee_custom_weight')
									->whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))
									->delete();
		}
	}

	public function deleteDeliveryCustomRelatedTables($template_id, $company_id)
	{
		if($template_id > 0 && $company_id > 0)
		{
			$delivery_custom = DB::table('shipping_template_delivery_custom')
								->whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))
								->delete();

			$delivery_custom_countries = DB::table('shipping_template_delivery_custom_countries')
											->whereRaw('template_id = ? AND company_id = ?', array($template_id, $company_id))
											->delete();
		}
	}

	public static function getShippingCompanyDetails($company_id)
	{
		$details = array();
		$shipping_company_details = ShippingCompanies::whereRaw('id = ? AND display = ?', array($company_id, 1))->first();
		if(count($shipping_company_details)) {
			$details['company_name'] = $shipping_company_details['company_name'];
			$details['category'] = $shipping_company_details['category'];
			$details['is_custom_fee_available'] = $shipping_company_details['is_custom_fee_available'];
			$details['is_standard_fee_available'] = $shipping_company_details['is_standard_fee_available'];
			$details['is_custom_delivery_available'] = $shipping_company_details['is_custom_delivery_available'];
			$details['default_delivery_days'] = $shipping_company_details['default_delivery_days'];
			$details['display'] = $shipping_company_details['display'];
		}
		return $details;
	}

	public function getShippingProductPackageDetails($package_details, $product_id, $quantity) {
		try {
			if(empty($package_details))
			{
				$package_details_db = Webshopshipments::getEditPackageDetails($product_id);
				if(!empty($package_details_db))
				{
					$product_package_details['weight'] = $package_details_db->weight;
					$product_package_details['length'] = $package_details_db->length;
					$product_package_details['width'] = $package_details_db->width;
					$product_package_details['height'] = $package_details_db->height;
					$product_package_details['custom'] = $package_details_db->custom;
					$product_package_details['first_qty'] = $package_details_db->first_qty;
					$product_package_details['additional_qty'] = $package_details_db->additional_qty;
					$product_package_details['additional_weight'] = $package_details_db->additional_weight;
				}
				else
					$product_package_details = array();
			}
			else
			{
				$product_package_details = $package_details;
			}
		} catch(Exception $e) {
			$product_package_details = array();
		}

		//find max weight
		if(!empty($product_package_details)) {

			$calc_lwh_weight = ($product_package_details['length'] * $product_package_details['width'] * $product_package_details['height'])/5000;
			$product_package_details['weight'] = ($calc_lwh_weight > 0 && ($calc_lwh_weight > $product_package_details['weight']))?$calc_lwh_weight:$product_package_details['weight'];
			$product_package_details['calculated_based_qunatity'] = false;
			if($product_package_details['custom'] == 'Yes') {
				$first_qty = $product_package_details['first_qty'];
				$additonal_weight = 0;
				if($first_qty > 0 && $quantity > 0) {
					$additional_quantity = $quantity - $first_qty;
					if($additional_quantity > 0) {
						if($product_package_details['additional_qty'] > 0) {
							$ceil_qty = ceil($additional_quantity / $product_package_details['additional_qty']);
							$additonal_weight = $ceil_qty * $product_package_details['additional_weight'];
						}
					}
				}
				$product_package_details['calculated_based_qunatity'] = true;
				$product_package_details['weight'] = $product_package_details['weight'] + $additonal_weight;
			}
		}
		return $product_package_details;
	}

	public function getShippingFromCountry($prod_obj, $product_id, $zip_code = '')
	{
		$shipping_from_country = 98;//india
		if(is_object($prod_obj))
			$prod_obj->setProductId($product_id);
		else
			$prod_obj = Products::initialize($product_id);

		try{
			$p_details = $prod_obj->getProductDetails();
			$shipping_from_country = (isset($p_details['shipping_from_country']) && $p_details['shipping_from_country'] > 0)?$p_details['shipping_from_country']:98;
			$zip_code = (isset($p_details['shipping_from_zip_code']) && $p_details['shipping_from_zip_code'] > 0)?$p_details['shipping_from_zip_code']:'';
		}
		catch(exception $e)
		{
			$shipping_from_country = 98;//india if nothing has set
		}

		$shipping_from = $this->getShippingCountryDetailFromId($shipping_from_country, $zip_code);
		return $shipping_from;
	}

	public function getShippingCountryDetailFromId($country_id, $zip_code = '') {
		$shipping_to = array();
		$to_country_det = Products::getCountryDetailsByCountryId($country_id);
		//echo "<pre>";print_r($to_country_det);echo "</pre>";exit;
		if(isset($to_country_det['zip_code']))
		{
			if(isset($zip_code) && $zip_code!='')
				$to_zip_code = $zip_code;
			else
				$to_zip_code = $to_country_det['zip_code'];
			$shipping_to['country_id'] = $country_id;
			$shipping_to['zip_code'] = $to_zip_code;
			$shipping_to['country_name'] = $to_country_det['country'];
			$shipping_to['country'] = $to_country_det['iso2_country_code'];
			$shipping_to['city'] = $to_country_det['capital'];
			$shipping_to['country_name_chinese'] = $to_country_det['country_name_chinese'];
			$shipping_to['china_post_group'] = $to_country_det['china_post_group'];
		}
		return $shipping_to;
	}

	function min_with_key($array, $key) {
        if (!is_array($array) || count($array) == 0) return false;
        $found = false;
        $index = 0;
        foreach($array as $i => $a)
        {
        	if($a['error_message']!='')
			{
				continue;
			}
			else
			{
				/*if((int)$a[$key]=='')
				{
					Log::info('key =='.$key);
					Log::info('valuee =='.$a[$key]);
					Log::info('============================================================Continue 2');
					continue;
				}*/
				$found = true;
				if(!isset($min))
				{
					$index = $i;
					$min = $a[$key];
				}

				if($a[$key] == 0)
				{
					$index = $i;
					break;
				}
				if($a[$key] < $min)
				{
					$min = $a[$key];
					$index = $i;
				}
			}
		}
        /*$min = $array[0][$key];
        if($min=='')
        {
			$min ='';
			$index = '';
        }
        foreach($array as $i => $a)
		{
			//echo "<br>min: ".$min."  val =".$a[$key]." i= ".$i."  index = ".$index;
			if($a[$key] === 0)
			{
				$min = $a[$key];
				$index = $i;
				break;
			}
			else
			{
				if($min =='' && $a[$key] > 0)
				{
					$min = $a[$key];
					$index = $i;
				}
	            if($min!='' && $a[$key]>=0 && $a[$key] < $min)
				{
	                $min = $a[$key];
	                $index = $i;
	            }
            }
        }*/

        return $index;
    }

    public function getCountryListId($order_by = 'geo_location_id', $company_id = 0) {
		if(!in_array($order_by,array('geo_location_id','zone_id')))
			$order_by = 'geo_location_id';

		switch($company_id) {
			case 7:
				$country_arr = ShippingCountriesHongkongPostAirMail::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_hongkong_post_air_mail.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 9:
				$country_arr = ShippingCountriesSingaporePost::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_singapore_post.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 10:
				$country_arr = ShippingCountriesSwissPost::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_swiss_post.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 11:
				$country_arr = ShippingCountriesSwedenPost::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_sweden_post.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 18:
				$country_arr = ShippingCountriesSfExpress::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_sf_express.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 20:
				$country_arr = ShippingCountriesAramex::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_aramex.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 25:
				$country_arr = ShippingCountriesCtrLandPickup::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_ctr_land_pickup.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			case 27:
				$country_arr = ShippingCountriesSellerShippingMethod::Leftjoin('currency_exchange_rate', 'currency_exchange_rate.country', '=' ,'shipping_countries_seller_shipping_method.country_name')
										->whereRaw('currency_exchange_rate.status = ?', array('Active'))
										->orderBy('currency_exchange_rate.'.$order_by, 'asc')
										->orderBy('currency_exchange_rate.country', 'asc')
										->get(array('currency_exchange_rate.country', 'currency_exchange_rate.id', 'currency_exchange_rate.'.$order_by));
				break;
			default;
				$country_arr = Products::getCountryListId($order_by, $company_id);
				break;
		}
		return $country_arr;
	}

	public function chkShippingCountryExist($company_id = 0, $country = '')	{
		switch($company_id) {
			case 7:
				$count = ShippingCountriesHongkongPostAirMail::whereRaw('country_name = ?', array($country))->count();
				break;
			case 9:
				$count = ShippingCountriesSingaporePost::whereRaw('country_name = ?', array($country))->count();
				break;
			case 10:
				$count = ShippingCountriesSwissPost::whereRaw('country_name = ?', array($country))->count();
				break;
			case 11:
				$count = ShippingCountriesSwedenPost::whereRaw('country_name = ?', array($country))->count();
				break;
			case 18:
				$count = ShippingCountriesSfExpress::whereRaw('country_name = ?', array($country))->count();
				break;
			case 20:
				$count = ShippingCountriesAramex::whereRaw('country_name = ?', array($country))->count();
				break;
			case 25:
				$count = ShippingCountriesCtrLandPickup::whereRaw('country_name = ?', array($country))->count();
				break;
			case 27:
				$count = ShippingCountriesSellerShippingMethod::whereRaw('country_name = ?', array($country))->count();
				break;
			default;
				$count = 0;
				break;
		}
		return $count;
	}
}