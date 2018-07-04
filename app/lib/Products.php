<?php
class Products {

	public $root_category_id = 0;

	public static function greeting()
	{
		return "What up dawg Products";
	}

	public static function initialize($product_id = '')
	{
		return new WebshopProduct($product_id);
	}

	public static function initializeCategory($category_id = '')
	{
		return new Category($category_id);
	}

	public static function initializeAttribute($attribute_id = '')
	{
		return new Attribute($attribute_id);
	}

	public static function initializeManageCredits($credit_id = '')
	{
		return new ManageCredits($credit_id);
	}

	public static function initializeCommonInvoice($invoice_id = '')
	{
		return new CommonInvoices($invoice_id);
	}

	public static function getProductSections($user_id = 0)
	{
	  	$section_details = UserProductSection::where('status', '=', 'Yes');
		if($user_id > 0)
		{
			$section_details->where('user_id', '=', $user_id);
		}
		$section_arr = $section_details->get();
		return $section_arr;
	}

	public static function getTopCategories()
	{
		$cache_key = 'top_categories_cache_key';
		if (($category_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$category_details = ProductCategory::Select('id', 'seo_category_name', 'category_name', 'category_level')->where('status', '=', 'active')->where('category_level', '=', 1)
								->orderBy('category_left', 'ASC')->get();
			HomeCUtil::cachePut($cache_key, $category_details);
		}
		return $category_details;
	}

	public static function getTopCategoryId()
	{
		$category_details = ProductCategory::Select('id', 'seo_category_name', 'category_name', 'category_level')->where('status', '=', 'active')->where('category_level', '=', 1)
							->orderBy('category_left', 'ASC')->first();
		return $category_details;
	}

	public static function getTopLevelCategoryIds($category_id)
	{
		$cache_key = 'TLCI_cache_key_'.$category_id;
		if (($q = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$q = DB::select('SELECT group_concat(parent.id ORDER BY parent.category_left) as category_ids from product_category AS node, product_category AS parent where node.category_left  BETWEEN parent.category_left AND parent.category_right AND node.id = ?', array($category_id));
			HomeCUtil::cachePut($cache_key, $q, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $q[0]->category_ids;
	}

	public static function getCategoriesList($category_id = 0, $cat_status = '')
	{
		$cache_key = 'CL_cache_key';
		$sub_cat_arr = ProductCategory::Select('id', 'category_name', 'seo_category_name', 'category_level', 'category_left', 'category_right', 'parent_category_id', 'category_level', 'display_order');
		if($category_id > 0)
		{
			$cache_key .= '_CI_'.$category_id;
			$sub_cat_arr = $sub_cat_arr->where('parent_category_id', '=', $category_id);
		}
		if($cat_status == '')
		{
			$cache_key .= '_CS';
			$sub_cat_arr = $sub_cat_arr->where('status', '=', 'active');
		}
		if (($sub_cat_arr_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$sub_cat_arr_result = $sub_cat_arr->orderBy('category_left', 'ASC')->get();
			HomeCUtil::cachePut($cache_key, $sub_cat_arr_result, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $sub_cat_arr_result;
	}

	public static function initializeShops()
	{
		return new Shops();
	}

	public static function initializeCancellationPolicy()
	{
		return new UserCancellationPolicy();
	}

	public static function getRootCategoryId()
	{
		$root_category_id = 1;
		$cache_key = 'root_category_id_key';
		if (!$root_cat = HomeCUtil::cacheGet($cache_key)){
			$root_cat = ProductCategory::Select('id')->whereRaw('category_level = 0 AND parent_category_id = 0')->first();
			HomeCUtil::cachePut($cache_key, $root_cat);
		}
		if(count($root_cat) > 0)
		{
			$root_category_id = $root_cat['id'];
		}
		return $root_category_id;
	}

	public static function insertRootCategory()
	{
		$id = self::getRootCategoryId();
		if($id == 0)
		{
			$arr['seo_category_name'] = "Root";
			$arr['category_left'] = 1;
			$arr['category_right'] = 2;
			$arr['category_level'] = 0;
			$id = ProductCategory::insertGetId($arr);
		}
		$array_multi_key = array('root_category_id_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		return $id;
	}

	public static function getAttributeOptions($attribute_id)
	{
		$cache_key = 'attribute_options_'.$attribute_id;
		if (($d_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$d_arr = ProductAttributeOptions::where('attribute_id', '=', $attribute_id)	->orderBy('id', 'ASC')->get(array('id', 'option_label', 'is_default_option'))->toArray();
			HomeCUtil::cachePut($cache_key, $d_arr, Config::get('generalConfig.cache_expiry_minutes'));
		}
		$data = array();
		foreach($d_arr AS $val)
		{
			$data[$val['id']] = $val['option_label'];
		}
		return $data;
	}

	public static function getProductCountForAllCategories()
	{
		$prod_cat_count_arr = array();
		$cache_key = 'product_details';
		if (($product_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$query = 'select parent.id AS category_id, COUNT(prod.id) product_count from product_category AS node, product_category AS parent,
											product AS prod join users on (prod.product_user_id = users.id) where node.category_left
											BETWEEN parent.category_left AND parent.category_right AND node.id = prod.product_category_id
											AND prod.product_status != \'Deleted\' AND prod.product_status = \'Ok\' AND users.is_banned = 0
											AND users.shop_status = 1';
			$query .= ' AND prod.date_expires !=\'0000-00-00 00:00:00\' AND prod.date_expires >= \''.date('Y-m-d').'\'';
			$query .= ' GROUP BY parent.id ORDER BY node.category_left';
			$product_details = DB::select($query);
			HomeCUtil::cachePut($cache_key, $product_details); // Store Query results into cache
		}
		if (count($product_details))
		{
			foreach($product_details as $product)
			{
				$prod_cat_count_arr[$product->category_id] = $product->product_count;
			}
		}
		return $prod_cat_count_arr;
	}

	public static function getCategoryName($cat_id)
	{
		$category_name = '';
		$cache_key = 'GCN'.$cat_id;
		if (($cat_info = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$cat_info = ProductCategory::Select('category_name')->whereRaw('id = ?', array($cat_id))->first();
			HomeCUtil::cachePut($cache_key, $cat_info, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($cat_info) > 0)
		{
			$category_name = $cat_info['category_name'];
		}
		return $category_name;
	}

	public static function getCategoryDetails($cat_id)
	{
		$category_details = array();
		$cache_key = 'category_details_cache_key'.$cat_id;
		if (($cat_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$cat_details = ProductCategory::Select('category_name', 'seo_category_name', 'category_description', 'parent_category_id', 'status', 'id', 'category_left', 'category_right',
						'category_meta_title', 'category_meta_description', 'category_meta_keyword', 'use_parent_meta_detail', 'available_sort_options', 'is_featured_category',
						'image_name', 'image_ext', 'image_width', 'image_height', 'category_meta_title', 'category_meta_keyword', 'category_meta_description', 'category_level')
						->whereRaw('id = ?', array($cat_id))->first();
			HomeCUtil::cachePut($cache_key, $cat_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($cat_details) > 0)
		{
			$category_details = $cat_details;
		}
		return $category_details;
	}

	public static function getCategoryDetailsBySlug($slug)
	{
		$cache_key = 'GCDBSCK_'.$slug;
		if (($cat_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$cat_details = ProductCategory::where('seo_category_name', '=', $slug)->get();
			HomeCUtil::cachePut($cache_key, $cat_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $cat_details;
	}

	public static function getProductCountForCategory()
	{
		// Get sub category ids
		$this->sub_category_ids = $this->getSubCategoryIds($category_id);
		if(!$this->sub_category_ids)
			$this->sub_category_ids = $category_id;

		$sub_category_ids = explode(',', $this->sub_category_ids);
		$product_count = Product::whereIn('product_category_id', $sub_category_ids)->count();
	    return $product_count;
	}

	public static function getParentCategoryIds($category_id)
	{
		$parent_category_ids = 0;
		$root_category_id = self::getRootCategoryId();
		$cat_details = DB::select('select parent.id AS parent_category_id from product_category node, product_category parent where
									node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? AND parent.id != ? AND parent.id != ?
									ORDER BY parent.category_left', array($category_id, $root_category_id, $category_id));
		if (count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				$parent_category_ids = ($parent_category_ids)?($parent_category_ids . ',' .$cat->parent_category_id ):$cat->parent_category_id;
			}
		}
		return $parent_category_ids;
	}

	public static function getAttributesAssignedForCategory($category_id)
	{
		// get all parent category ids
		$parent_category_ids = self::getParentCategoryIds($category_id);

		$attr_details = DB::select('SELECT A.attribute_id, A.category_id FROM product_category_attributes AS A, product_attributes AS B WHERE	A.attribute_id = B.id AND (A.category_id IN (' . $parent_category_ids .') OR A.category_id = ? ) ORDER BY A.display_order', array($category_id));
		return $attr_details;
	}

	public static function getProductAttributeDetails($attribute_id = '', $paginate = false, $per_page = '')
	{
		$option_fields = array('select', 'check', 'option', 'multiselectlist');
		$return_row = array();
		$attr_details = ProductAttributes::Select('id', 'attribute_question_type', 'validation_rules', 'default_value', 'status', 'is_searchable', 'show_in_list',
												'description', 'attribute_label');
		if($attribute_id != '')
		{
			$attr_details = $attr_details->whereRaw('id = ?', array($attribute_id));
		}
		$attr_details = $attr_details->orderBy('id', 'ASC');

		if($paginate == true)
		{
			$attr_details = $attr_details->paginate($per_page);
			return $attr_details;
		}
		else
		{
			$attr_details = $attr_details->get();
		}

		if(count($attr_details) > 0)
		{
			foreach($attr_details as $attr)
			{
				$return_row[$attr->id]['attribute_id'] = $attr->id;
				$return_row[$attr->id]['is_searchable'] = $attr->is_searchable;
				$return_row[$attr->id]['show_in_list'] = $attr->show_in_list;
				$return_row[$attr->id]['attribute_question_type'] = $attr->attribute_question_type;
				$return_row[$attr->id]['attribute_label'] = $attr->attribute_label;
				if(in_array($attr->attribute_question_type, $option_fields))
				{
					$attr_options = self::getProductAttributeOptions($attr->id);
					$return_row[$attr->id]['default_value'] = is_null($attr->default_value) ? '' :self::getAttributeDefaultOptionValue($attr->default_value);
				}
				else
				{
					$attr_options = array();
					$return_row[$attr->id]['default_value'] = is_null($attr->default_value) ? '' :$attr->default_value;
				}
				$return_row[$attr->id]['attribute_options'] = $attr_options;
				$return_row[$attr->id]['validation_rules'] = is_null($attr->validation_rules) ? '' :$attr->validation_rules ;
				$return_row[$attr->id]['status'] = $attr->status;
				$return_row[$attr->id]['description'] = $attr->description;
			}
		}
		return $return_row;
	}

	public static function getAttributeOptionDetails($attribute)
	{
		$attr_option_arr = array();
		$options = array('select', 'check', 'option', 'multiselectlist');
		if(in_array($attribute['attribute_question_type'], $options))
		{
			$attr_options = self::getProductAttributeOptions($attribute['id']);
			$default_value = is_null($attribute['default_value']) ? '' :self::getAttributeDefaultOptionValue($attribute['default_value']);
		}
		else
		{
			$attr_options = array();
			$default_value = is_null($attribute['default_value']) ? '' :$attribute['default_value'];
		}
		$attr_option_arr['attr_options'] = $attr_options;
		$attr_option_arr['default_value'] = $default_value;
		return $attr_option_arr;
	}

	public static function getProductAttributeOptions($attribute_id)
	{
		$attribute_options = array();
		$attr_option_details = ProductAttributeOptions::whereRaw('attribute_id = ?', array($attribute_id))->get(array('id', 'option_label', 'is_default_option'));
		if(count($attr_option_details) > 0)
		{
			foreach($attr_option_details as $attr_option)
			{
				$attribute_options[$attr_option->id]['option_label'] = $attr_option->option_label;
				$attribute_options[$attr_option->id]['is_default_option'] = $attr_option->is_default_option;
			}
		}
		return $attribute_options;
	}

	public static function getAttributeDefaultOptionValue($attribute_option_id)
	{
		$option_value = ProductAttributeOptions::whereRaw('id = ?', array($attribute_option_id))->pluck('option_label');
		return $option_value;
	}

	public static function updateAssignedAttributeDisplayOrder($attribute_id, $category_id, $display_order)
	{
		$data_arr['display_order'] = $display_order;
		ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->update($data_arr);
	}

	public static function fetchCurrencyDetails()
	{
		$currency_details = array();
		$cache_key = 'fetch_currencies_details_cache_key';
		if (($currency_list = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$currency_list = Currencies::whereRaw("status = ? AND display_currency = ?", array('Active', 'Yes'))->get(array('currency_code'));				      		HomeCUtil::cachePut($cache_key, $currency_list);
		}
		foreach($currency_list as $currency)
		{
			$currency_details[] = $currency['currency_code'];
		}
		return $currency_details;
	}
	public static function fetchAllowedCurrenciesList()
	{
		$cache_key = 'allowed_currencies_list_cache_key';
		if (($currency_list = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$currency_list = Currencies::whereRaw("status = ? AND display_currency = ?", array('Active', 'Yes'))->lists('currency_code','currency_code');
			HomeCUtil::cachePut($cache_key, $currency_list);
		}
		if(!empty($currency_list))
			return $currency_list;
		else
			return array();
	}

	public static function setCurrencyDetails()
	{
		$cache_key = 'currencies_details_cache_key';
		if (($currency_list = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$currency_list = Currencies::whereRaw("status = ? AND display_currency = ?", array('Active', 'Yes'))->get();
			HomeCUtil::cachePut($cache_key, $currency_list);
		}
		$currency_details = array();
		foreach($currency_list as $currency)
		{
			$currency_details[] = array(	"id" => $currency->id,
											"currency_code" => $currency->currency_code,
											"currency_symbol" => $currency->currency_symbol,
											"exchange_rate" => $currency->exchange_rate,
											"currency_name" => $currency->currency_name);
		}
		return $currency_details;
	}

	public static $_valid_currency_details;
	public static function chkIsValidCurrency($currency_code)
	{
		$details = array();
		if (isset(Products::$_valid_currency_details[$currency_code])) {
			$selected_currency_code = Products::$_valid_currency_details[$currency_code];
		} else {
			$cache_key = 'SCCCKN_'.$currency_code;
			if (($selected_currency_code = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$selected_currency_code = Currencies::whereRaw('currency_code= ? AND status = "Active" AND display_currency = "Yes" ', array($currency_code))->first();
				HomeCUtil::cachePut($cache_key, $selected_currency_code);
			}
			Products::$_valid_currency_details[$currency_code] = $selected_currency_code;
		}
		if(count($selected_currency_code))
		{
			$details['country'] = $selected_currency_code['country'];
			$details['currency_code'] = $selected_currency_code['currency_code'];
			$details['exchange_rate'] = $selected_currency_code['exchange_rate'];
			$details['currency_symbol'] = $selected_currency_code['currency_symbol'];
			$details['exchange_rate_static'] = $selected_currency_code['exchange_rate_static'];
		}
		return $details;
	}

	public static $_country_details;

	public static function getCountryIdByCountry($country_name)
	{
		$country_id = "";
		if($country_name != '') {
			if (isset(Products::$_country_details[$country_name])) {
				$currency_exchange = Products::$_country_details[$country_name];
			} else {
				$cache_key = 'country_name_cache_key'.$country_name;
				if (($currency_exchange = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$currency_exchange = CurrencyExchangeRate::where('country','=',$country_name)->get();
					HomeCUtil::cachePut($cache_key, $currency_exchange);
				}
				Products::$_country_details[$country_name] = $currency_exchange;
			}
			if(sizeof($currency_exchange) > 0) {
				$country_id = $currency_exchange[0]['id'];
			}
		}
		return $country_id;
	}

	public static function getCountryNameByCountryId($country_id)
	{
		$country_name = '';
		if($country_id != '') {
			if (isset(Products::$_country_details[$country_id])) {
				$currency_exchange = Products::$_country_details[$country_id];
			} else {
				$currency_exchange = CurrencyExchangeRate::where('id','=',$country_id)->get();
				Products::$_country_details[$country_id] = $currency_exchange;
			}
			if(sizeof($currency_exchange) > 0) {
				$country_name = $currency_exchange[0]['country'];
			}
		}
		return $country_name;
	}
	public static function getCountryNameByIso2CountryCode($country_code)
	{
		$country_name = '';
		if($country_code != '') {
			if (isset(Products::$_country_details[$country_code])) {
				$currency_exchange = Products::$_country_details[$country_code];
			} else {
				$currency_exchange = CurrencyExchangeRate::whereRaw('iso2_country_code = ?', array($country_code))->get();
				Products::$_country_details[$country_code] = $currency_exchange;
			}
			if(sizeof($currency_exchange) > 0) {
				$country_name = $currency_exchange[0]['country'];
			}
		}
		return $country_name;
	}

	public static function getCountryNameByCountryCode($country_code)
	{
		$country_name = '';
		if($country_code != '') {
			if (isset(Products::$_country_details[$country_code])) {
				$currency_exchange = Products::$_country_details[$country_code];
			} else {
				$cache_key = 'country_name_bycountry_code_cache_key_'.$country_code;
				if (($currency_exchange = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$currency_exchange = CurrencyExchangeRate::whereRaw('country_code = ?', array($country_code))->get();
					HomeCUtil::cachePut($cache_key, $currency_exchange, Config::get('generalConfig.cache_expiry_minutes'));
				}
				Products::$_country_details[$country_code] = $currency_exchange;
			}
			if(sizeof($currency_exchange) > 0) {
				$country_name = $currency_exchange[0]['country'];
			}
		}
		return $country_name;
	}

	public static function getCurrencyCodeByCountry($country_name)
	{
		$currency_code = "USD";
		if($country_name != '') {
			if (isset(Products::$_country_details[$country_name])) {
				$currency_exchange = Products::$_country_details[$country_name];
			} else {
				$cache_key = 'country_name_cache_key'.$country_name;
				if (($currency_exchange = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$currency_exchange = CurrencyExchangeRate::where('country','=',$country_name)->get();
					HomeCUtil::cachePut($cache_key, $currency_exchange);
				}
				Products::$_country_details[$country_name] = $currency_exchange;
			}
			if(sizeof($currency_exchange) > 0) {
				$currency_code = $currency_exchange[0]['currency_code'];
			}
		}
		return $currency_code;
	}

	public static function updateCurrencyExchangeRate($input)
	{
		$id = Currencies::whereRaw('status = ? AND display_currency = ? AND currency_code = ?', array('Active', 'Yes', $input['currency_code']))->pluck('id');
		if($id != '') {
			Currencies::whereRaw('id = ?', array($id))->update(array('exchange_rate' => $input['exchange_rate']));
			$array_multi_key = array('allowed_currencies_list_cache_key', 'fetch_currencies_details_cache_key', 'currencies_details_cache_key', 'selected_currency_code_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}
		return 1;
	}

	public static function updateStaticCurrencyExchangeRate($input)
	{
		$id = Currencies::whereRaw('status = ? AND display_currency = ? AND currency_code = ?', array('Active', 'Yes', $input['to']))->pluck('id');
		if($id != '') {
			Currencies::whereRaw('id = ?', array($id))->update(array('exchange_rate_static' => $input['fee']));
			$array_multi_key = array('allowed_currencies_list_cache_key', 'fetch_currencies_details_cache_key', 'currencies_details_cache_key', 'selected_currency_code_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}
		return 1;
	}

	public static function getCountryList()
	{
		$country_list_arr = array();
		$cache_key = 'GCLCK';
		if (($country_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$country_arr = CurrencyExchangeRate::whereRaw("status = ? ORDER BY country", array('Active'))->get(array('country', 'country_code'));
			HomeCUtil::cachePut($cache_key, $country_arr, Config::get('generalConfig.cache_expiry_minutes'));
		}
		foreach($country_arr AS $value)
		{
			$country_list_arr[$value['country_code']] = $value['country'];
		}
		return $country_list_arr;
	}
	public static function getCountryListId($order_by = 'geo_location_id')
	{
		if(!in_array($order_by,array('geo_location_id','zone_id')))
			$order_by = 'geo_location_id';
		$country_arr = CurrencyExchangeRate::whereRaw("status = ?", array('Active'))->orderBy($order_by, 'asc')->orderBy('country', 'asc')->get(array('country', 'id', $order_by));
		return $country_arr;
	}
	public static function getCountiesNamesFromId($country_ids)
	{
		$country_name_arr = array();
		if(!empty($country_ids))
		{
			$country_name_arr = CurrencyExchangeRate::whereIn('id', $country_ids)->lists('country');
		}
		return $country_name_arr;
	}
	public static function getGeoLocationName($geo_location_id)
	{
		$location_name = '';
		switch($geo_location_id)
		{
			case '1':
				$location_name = 'Asia';
				break;
			case '2':
				$location_name = 'Europe';
				break;
			case '3':
				$location_name = 'Africa';
				break;
			case '4':
				$location_name = 'Oceania';
				break;
			case '5':
				$location_name = 'North America';
				break;
			case '6':
				$location_name = 'South America';
				break;
			case '7':
				$location_name = 'Antarctica';
				break;
		}

		return $location_name;
	}

	public static $_country_detail_and_currency_exchange;
	public static function getCountryDetailsByCountryId($country_id)
	{
		$details = array();
		if (isset(Products::$_country_detail_and_currency_exchange[$country_id])) {
			$country_details = Products::$_country_detail_and_currency_exchange[$country_id];
		} else {
			$curr_tbl = (new Currencies)->getTable();
			$curr_exrate_tbl = (new CurrencyExchangeRate)->getTable();

			$cache_key = 'CERCK_'.$curr_exrate_tbl.'_'.$curr_tbl.'_'.$country_id;
			if (($country_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$country_details = CurrencyExchangeRate::Select($curr_exrate_tbl.'.*', $curr_tbl.'.currency_symbol', $curr_tbl.'.currency_name', $curr_tbl.'.exchange_rate', $curr_tbl.'.exchange_rate_static', $curr_tbl.'.paypal_supported', $curr_tbl.'.display_currency')
											->join($curr_tbl, $curr_tbl.'.currency_code', '=', $curr_exrate_tbl.'.currency_code')
											->whereRaw($curr_exrate_tbl.'.id = ? AND '.$curr_exrate_tbl.'.status = "Active"', array($country_id))->first();
				HomeCUtil::cachePut($cache_key, $country_details, Config::get('generalConfig.cache_expiry_minutes'));
				Products::$_country_detail_and_currency_exchange[$country_id] = $country_details;
			}
		}
		if(count($country_details))
		{
			$details['id'] = $country_id;
			$details['country'] = $country_details['country'];
			$details['currency_code'] = $country_details['currency_code'];
			$details['exchange_rate'] = $country_details['exchange_rate'];
			$details['currency_symbol'] = $country_details['currency_symbol'];
			$details['zip_code'] = $country_details['zip_code'];
			$details['iso2_country_code'] = $country_details['iso2_country_code'];
			$details['capital'] = $country_details['capital'];
			$details['country_name_chinese'] = $country_details['country_name_chinese'];
			$details['china_post_group'] = $country_details['china_post_group'];
		}
		return $details;
	}

	public static function checkIsValidCreditId($user_id, $credit_id)
	{
		$credit_det = CreditsLog::whereRaw('credited_to = ? AND credit_id = ?', array($user_id, $credit_id))->first();
		if(count($credit_det) > 0) {
				return true;
		}
		return false;
	}

	public static function checkIsValidCategoryId($category_id)
	{
		$credit_det = ProductCategory::where('id', $category_id)->first();
		if(count($credit_det) > 0) {
				return true;
		}
		return false;
	}
	public static function getProductViewURL($product_id, $product=array())
	{
		$productService =  new ProductService();
		$view_url = $productService->getProductViewURL($product_id, $product);
		return $view_url;

	}

	public static function getFeaturedProducts()
	{
		if(CUtil::chkIsAllowedModule('featuredproducts')) {
			$featured_products_service = new FeaturedProductsService;
			$d_arr = array();
			$product_service = new ProductService;
			$prod_obj = Products::initialize();
			$prod_obj->setFilterProductStatus('Ok');
			$prod_obj->setFilterFeaturedProduct('Yes');
			$prod_obj->setFilterProductExpiry(true);
			$prod_obj->setOrderByField('featured');
			$prod_obj->setProductsLimit(6);
			$featured_products = $prod_obj->getProductsList();
			if(count($featured_products) > 0) {
				$featured_products = $featured_products->toArray();
				foreach($featured_products as $key => $prod) {
					$view_url = $product_service->getProductViewURL($prod['id'], $prod);
					$p_img_arr = $prod_obj->getProductImage($prod['id']);
					$p_thumb_img = $product_service->getProductDefaultThumbImage($prod['id'], 'thumb', $p_img_arr);

					$featured_products[$key]['p_product_details'] = $prod;
					$featured_products[$key]['p_view_url'] = $view_url;
					$featured_products[$key]['p_thumb_img'] = $p_thumb_img;
				}
			}
			$d_arr['featured_products'] = $featured_products;
			$d_arr['featured_products_total'] = $featured_products_service->getTotalFeaturedProducts();
			$d_arr['featured_products_url'] = Url::to('product?orderby_field=featured');
			return View::make('featuredproducts::featuredProducts', compact('d_arr', 'product_service'));
		}
	}

	public static function getFeaturedProductsIndex()
	{
		if(CUtil::chkIsAllowedModule('featuredproducts')) {
			$product_service = new ProductService;
			$shop_obj = Products::initializeShops();
			$prod_obj = Products::initialize();
			$prod_obj->setFilterProductStatus('Ok');
			$prod_obj->setFilterFeaturedProduct('Yes');
			$prod_obj->setFilterProductExpiry(true);
			$prod_obj->setProductsLimit(8);
			$featured_products = $prod_obj->getProductsList();
			return View::make('featuredproducts::featuredProductsIndex', compact('featured_products', 'product_service', 'shop_obj'));
		}
	}
}