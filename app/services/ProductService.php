<?php

class ProductService
{
	public $p_tab_lang_arr = array();
	public $root_category_id = 0;
	public $prod_cat_count_arr = array();
	public $p_tab_arr = array();
	public $alert_message = '';
	public $validate_tab_arr = array();
	public $product_media_type = '';
	public $product_max_upload_size = 0;
	public $allowed_upload_formats = '';
	# space displayed before category name in category list drop down for ease identification of sub-categories
	const MAX_CATEGORY_SPACING = 4;
	private $srch_arr = array();

	function __construct()
	{
		$this->p_tab_arr = array('basic' => false, 'price' => false, 'shipping' => false, 'tax' => false, 'attribute' => false, 'preview_files' => false, 'variations' => false, 'download_files' => false, 'publish' => false);
		$this->initProductTabList();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
    }

    public function getCookie($cookie_name)
	{
		$value = "";
		if(Cookie::has($cookie_name) && Cookie::get($cookie_name)!=null)
		{
			$value = Cookie::get($cookie_name);
		}
		return $value;
	}
	public function getShopDetails($user_id)
	{
		$shop_arr = ShopDetails::whereRaw('user_id = ?', array($user_id))->first(array('id', 'shop_name', 'url_slug', 'shop_slogan'));
		if(count($shop_arr) > 0)
		{
			$shop_arr['shop_url'] = $this->getProductShopURL($shop_arr['id'],$shop_arr);
		}
		return $shop_arr;
	}

	public function fetchSliderDefaultImage($p_id, $prod_obj)
	{
		$result_arr = array();
		/*$p_default_arr = ProductImage::where('product_id', '=', $p_id)
										->first( array('default_title', 'default_img', 'default_ext', 'default_width', 'default_height', 'default_orig_img_width', 'default_orig_img_height'));*/
		$p_default_arr = $prod_obj->getProductImage($p_id);
		$cfg_large_width = Config::get("webshoppack.photos_large_width");
		$cfg_large_height = Config::get("webshoppack.photos_large_height");
		$cfg_thumb_width = Config::get("webshoppack.photos_thumb_width");
		$cfg_thumb_height = Config::get("webshoppack.photos_thumb_height");
		$cfg_small_width = Config::get("webshoppack.photos_small_width");
		$cfg_small_height = Config::get("webshoppack.photos_small_height");


		if(count($p_default_arr) > 0 && isset($p_default_arr['default_title']) && $p_default_arr['default_title'] != "")
		{
			$img_path = URL::asset(Config::get("webshoppack.photos_folder"))."/";
			$large_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_large_width, $cfg_large_height, $p_default_arr["default_l_width"], $p_default_arr["default_l_height"]);
			$thumb_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_thumb_width, $cfg_thumb_height, $p_default_arr["default_t_width"], $p_default_arr["default_t_height"]);
			$small_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_small_width, $cfg_small_height, $p_default_arr["default_s_width"], $p_default_arr["default_s_height"]);

			$result_arr = array('title' => $p_default_arr['default_title'],
									'img_name' => $p_default_arr['default_img'],
									'img_ext' => $p_default_arr['default_ext'],
									'large_img_path' => $img_path . $p_default_arr["default_img"]."L.".$p_default_arr["default_ext"],
									'large_img_attr' => $large_img_attr,
									'thumb_img_path' => $img_path . $p_default_arr["default_img"]."T.".$p_default_arr["default_ext"],
									'thumb_img_attr' => $thumb_img_attr,
									'small_img_path' => $img_path . $p_default_arr["default_img"]."S.".$p_default_arr["default_ext"],
									'small_img_attr' => $small_img_attr,
									'orig_img_path' => $img_path . $p_default_arr["default_img"].".".$p_default_arr["default_ext"],
									'default_orig_img_width' => $p_default_arr['default_width'],
									'default_orig_img_height' => $p_default_arr['default_height'],
									'image_exits' => true,
									);
		}
		else
		{
			$result_arr = array('title' => trans('viewProduct.no_image'),
									'img_name' => '',
									'img_ext' => '',
									'orig_img_path' => '',
									'large_img_path' => URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image"),
									'large_img_attr' => BasicCUtil::TPL_DISP_IMAGE($cfg_large_width, $cfg_large_height, $cfg_large_width, $cfg_large_height),
									'thumb_img_path' => URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_thumb_no_image"),
									'thumb_img_attr' => BasicCUtil::TPL_DISP_IMAGE($cfg_thumb_width, $cfg_thumb_height, $cfg_thumb_width, $cfg_thumb_height),
									'small_img_path' => URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_small_no_image"),
									'small_img_attr' => BasicCUtil::TPL_DISP_IMAGE($cfg_small_width, $cfg_small_height, $cfg_small_width, $cfg_small_height),
									'default_orig_img_width' => $cfg_thumb_width,//$p_default_arr['default_orig_img_width'],
									'default_orig_img_height' => $cfg_thumb_height,//$p_default_arr['default_orig_img_height'],
									'image_exits' => false,

									);

		}
		return $result_arr;
	}
	public function fetchSliderPreviewImage($p_id, $prod_obj)
	{
		$result_arr = array();
		/*$preview_arr = ProductResource::where('product_id', '=', $p_id)
										->where('resource_type', '=', 'Image')
										->orderBy('display_order')
										->get( array('filename', 'ext', 'title', 'width', 'height', 'l_width', 'l_height', 't_width', 't_height'));*/
		$preview_arr = $prod_obj->populateProductResources('Image', 'No', $p_id);
		$cfg_large_width = Config::get("webshoppack.photos_large_width");
		$cfg_large_height = Config::get("webshoppack.photos_large_height");
		$cfg_thumb_width = Config::get("webshoppack.photos_thumb_width");
		$cfg_thumb_height = Config::get("webshoppack.photos_thumb_height");
		$cfg_small_width = Config::get("webshoppack.photos_small_width");
		$cfg_small_height = Config::get("webshoppack.photos_small_height");

		if(count($preview_arr) > 0)
		{
			$img_path = URL::asset(Config::get("webshoppack.photos_folder"))."/";
			foreach($preview_arr AS $img)
			{
				$large_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_large_width, $cfg_large_height, $img["l_width"], $img["l_height"]);
				$thumb_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_thumb_width, $cfg_thumb_height, $img["t_width"], $img["t_height"]);
				$small_img_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_small_width, $cfg_small_height, $img["s_width"], $img["s_height"]);

				$result_arr[] = array('title' => $img['title'],
										'img_name' => $img['filename'],
										'img_ext' => $img['ext'],
										'large_img_path' => $img_path . $img["filename"]."L.".$img["ext"],
										'large_img_attr' => $large_img_attr,
										'thumb_img_path' => $img_path . $img["filename"]."T.".$img["ext"],
										'thumb_img_attr' => $thumb_img_attr,
										'small_img_path' => $img_path . $img["filename"]."S.".$img["ext"],
										'small_img_attr' => $small_img_attr,
										'orig_img_path' => $img_path . $img["filename"].".".$img["ext"],
										'width' => $img['width'],
										'height' => $img['height'],
										);
			}
		}
		return $result_arr;
	}

    public function initProductTabList()
	{
		$this->p_tab_lang_arr = array('basic' => trans('product.basic_tab'),
									  'price' => trans('product.price_tab'),
									  'shipping' => trans('product.shipping_tab'),
									  'stocks' => trans('product.stocks_tab'),
									  'tax' => trans('product.tax_tab'),
									  'meta' => trans('product.meta_tab'),
									  'attribute' => trans('product.attribute_tab'),
									  'preview_files' => trans('product.preview_files_tab'),
									  'download_files' => trans('product.download_files_tab'),
									  'cancellation_policy' => trans('product.cancellation_policy'),
									  'publish' => trans('product.publish_tab'),
									  'status' => trans('product.approval_status_tab')
									);
		if(CUtil::chkIsAllowedModule('variations'))
		{
			$this->p_tab_lang_arr['variations'] = trans('product.variations_tab');
		}
	}

	public function populateProductCategoryList($cat_id)
	{
		//$stock_country_id = isset($_COOKIE['stock_country']) ? $_COOKIE['stock_country'] : key(Config::get('generalConfig.site_default_country'));
		$catList = array();
		$cat_details = Products::getCategoriesList($cat_id);
		if(count($cat_details) > 0)
		{
			$prod_count_arr = Products::getProductCountForAllCategories();
			foreach($cat_details as $catkey => $cat)
			{
				$catList[$catkey] = $cat;
				$count = isset($prod_count_arr[$cat['id']]) ? $prod_count_arr[$cat['id']] : 0;
				$catList[$catkey]['product_count'] = $count;
				$catList[$catkey]['cat_link'] = $this->urlLink($cat['seo_category_name']);
			}
		}
		return $catList;
	}

	public function urlLink($values)
	{
		$qryString = '';
		$current_script = URL::full();
		$parts = parse_url($current_script);
		$qryPart = parse_url($_SERVER['REQUEST_URI']);

		$concat_slash = ($values != '') ? '/' : '';
		if (isset($parts['path']))
			$parts['path'] = $parts['path'].$concat_slash.$values;
		else
			$parts['path'] = $concat_slash.$values;

		$port = '';
		if(isset($parts['port']) && $parts['port'] != "")
		{
			$port = ':'.$parts['port'];
		}

		if($qryString!="" && $qryString!="?")
			$newUrl = $parts['scheme'].'://'.$parts['host'].$port.$parts['path'].'/'.$qryString;
		else
			$newUrl = $parts['scheme'].'://'.$parts['host'].$port.$parts['path'];
		return $newUrl;
	}
	public function getProductCode($seo_title)
	{
		$product_code = '';
		$matches = null;
		preg_match('/^(P[0-9]{6})\-/', $seo_title, $matches);
		if (!isset($matches[1])) {
			preg_match('/^(P[0-9]{6})$/', $seo_title, $matches);
		}
		if (isset($matches[1])){
			$product_code = $matches[1];
		}
		return $product_code;
	}

	public function buildProductQuery($product, $cat_id)
	{
		$this->applicable_cats_ids = array();

		$product->setFilterProductStatus('Ok');
		$product->setFilterProductExpiry(true);
		$product->setFilterLoggedUserId($this->logged_user_id);

		$user_allowed_to_add_product = (Config::get('generalConfig.user_allow_to_add_product')) ? 'Yes' : 'No';
		$product->setFilterUserAllowedToAddProduct($user_allowed_to_add_product);

		$group_id = BasicCUtil::getUserGroupId($this->logged_user_id);
		$group_id = ($group_id != '') ? $group_id : 0;
		if($group_id == 1) $group_id = 0;
		$product->setFilterUserGroupId($group_id);

		//Filter by stock id
		//$stock_country_id = isset($_COOKIE['stock_country']) ? $_COOKIE['stock_country'] : key(Config::get('generalConfig.site_default_country'));
		//$product->setFilterStockCountry($stock_country_id);

		$product_qty = 1;

		if($cat_id > 0)
		{
			$search_category_array = array($cat_id);
		}

		if(Input::has('cat_search') && Input::get('cat_search') != '')
		{
			$search_category_array = Input::get('cat_search');
		}

		if(isset($search_category_array) && count($search_category_array) > 0)
		{
			foreach($search_category_array as $c_id)
			{
				//select the applicable categories to which the items may belong ..
			 	$sub_cat_ids = $product->getSubCategoryIds($c_id);
				$this->applicable_cats_ids =  array_unique(array_merge($this->applicable_cats_ids, $sub_cat_ids));
			}
			if(count($this->applicable_cats_ids))
			{
				$product->setFilterProductCategory($this->applicable_cats_ids);
			}
		}

		if( (Input::has('price_range_start') && is_numeric(Input::get('price_range_start'))) OR (Input::has('price_range_end') && is_numeric(Input::get('price_range_end'))) )
		{
			$start_price = Input::get('price_range_start');
			$end_price = Input::get('price_range_end');
			$start_price = is_numeric($start_price) ? $start_price : '';
			$end_price = is_numeric($end_price) ? $end_price : '';
			if(Config::get('generalConfig.currency_is_multi_currency_support'))
			{
				$current_currency = CUtil::getCurrencyToDisplay();
				$from_currency = $current_currency['currency_code'];
				if($start_price != '')
					$start_price = CUtil::convertAmountToCurrency($start_price, $from_currency, Config::get('generalConfig.site_default_currency'), false);
				if($end_price != '')
					$end_price = CUtil::convertAmountToCurrency($end_price, $from_currency, Config::get('generalConfig.site_default_currency'),false);

			}
			$product->setFilterProductFromPrice($start_price);
			$product->setFilterProductToPrice($end_price);
			$product->setFilterProductQty($product_qty);

			/*$logged_user_id = $this->logged_user_id;
			if($product_qty > 1 && $logged_user_id > 0) {
				$temp_product_price_group_table = 'temp_product_price_group_'.$logged_user_id;
				//Temp table log
				$temp_table_exists = TempTableLog::whereRaw('temp_table_name = ?', array($temp_product_price_group_table))->count('temp_table_name');
				if($temp_table_exists > 0) {
					DB::statement(DB::raw("TRUNCATE TABLE $temp_product_price_group_table"));
				}
				else {
					DB::statement(DB::raw("DROP TABLE IF EXISTS $temp_product_price_group_table;"));
					$create_temp_table = "CREATE TABLE $temp_product_price_group_table (
					id int(11) NOT NULL AUTO_INCREMENT,
					product_id int(11) NOT NULL,
					discount float(8,2) NOT NULL,
  					discount_usd float(8,2) NOT NULL,
		  			PRIMARY KEY (id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
					";
					DB::statement(DB::raw($create_temp_table));
				}

				$insert_group_zero_prod = "INSERT INTO $temp_product_price_group_table (product_id, discount, discount_usd)
										SELECT product_id, discount, discount_usd FROM product_price_groups WHERE group_id = 0
										AND range_start <= $product_qty AND (range_end >= $product_qty OR range_end = '-1')";

				$remove_user_group_prod = "DELETE FROM $temp_product_price_group_table where product_id IN
											(SELECT product_id FROM product_price_groups WHERE group_id = $group_id
											 AND range_start <= $product_qty AND (range_end >= $product_qty OR range_end = '-1'))";

				$insert_user_group_prod = "INSERT INTO $temp_product_price_group_table (product_id, discount, discount_usd)
										SELECT product_id, discount, discount_usd FROM product_price_groups WHERE group_id = $group_id
										AND range_start <= $product_qty AND (range_end >= $product_qty OR range_end = '-1')";

				DB::statement(DB::raw($insert_group_zero_prod));
				DB::statement(DB::raw($remove_user_group_prod));
				DB::statement(DB::raw($insert_user_group_prod));

				if($temp_table_exists > 0) {
					TempTableLog::whereRaw('temp_table_name = ?', array($temp_product_price_group_table))->update(array('date_used' => DB::raw('NOW()')));
				} else {
					TempTableLog::insert(array('temp_table_name' => $temp_product_price_group_table, 'date_used' => DB::raw('NOW()')));
				}
			}*/
		}

		if(Input::has('tag_search') && Input::get('tag_search') != '')
		{
			$text_to_search = Input::get('tag_search');
			$symbol_arr = array(',', '.', '?', '*');
			$text_to_search = str_replace($symbol_arr, ' ', $text_to_search);
			$search_arr = explode(" ", $text_to_search);

			$product->setFilterKeyword($search_arr);
		}

		if(Input::has('shop_search') && Input::get('shop_search') != '')
		{
			$product->setFilterShopName(Input::get('shop_search'));
		}
		if(Input::has('attributes'))
		{
			$attributes = Input::get('attributes');
			if(!empty($attributes))
			{
				$attributes_options = array();
				$attributes = array_filter($attributes);
				foreach($attributes as $attribute)
				{
					$attr_det = explode('_', $attribute);
					if(count($attr_det)== 2)
					{
						$attr_id = $attr_det[0];
						$attributes_options[$attr_id][] = $attr_det[1];
					}

				}
				$product->setFilterAttributeOptions($attributes_options);
			}
		}
		$input_arr = Input::all();
		$text_attribute_arr = array();
		foreach($input_arr as $key => $val)
		{
			if(starts_with($key, 'textattribute_'))
			{
				$name_arr = explode('_', $key);
				if(count($name_arr) == 2 && $val!='')
				{
					$id = $name_arr[1];
					$text_attribute_arr[$id] = $val;
				}
			}
		}
		if(!empty($text_attribute_arr))
		{
			$product->setFilterAttributeValues($text_attribute_arr);
		}
	}

	public function getProductSectionDropList($user_id = 0)
	{
		$section_list_arr = array('' => trans('common.select_option'));

		$section_arr = Products::getProductSections($user_id);
		foreach($section_arr AS $value)
		{
			$section_list_arr[$value->id] = $value->section_name;
		}
		return $section_list_arr;
	}

	public function getCategoryListArr()
	{
		$r_arr = array('' => trans('common.select_option'));
		$categories = Products::getTopCategories();
		if(count($categories) > 0)
		{
			$d_arr = $categories->toArray();
			foreach($d_arr AS $val)
			{
				if($val['category_level'] == 0)
				{
					$this->root_category_id = $val['id'];
				}
				$r_arr[$val['id']] = $val['category_name'];
			}
		}

		return $r_arr;
	}

	public function getAllTopLevelCategoryIds($category_id = 0)
	{
		$category_ids = Products::getTopLevelCategoryIds($category_id);
		return $category_ids;
	}

	public function getTabList($p_id, $input_arr = array(), $action = 'add')
	{
		$product = Products::initialize($p_id);
		 if(count($input_arr) > 0)
		 {
			$p_id = ($p_id == '')? 0 : $p_id;
			//check prodcut category has attributes..
			if(isset($input_arr['product_category_id'])) //No need to check for add product page
			{
				 $has_attr_tab = $product->checkProductHasAttribute($input_arr['product_category_id']);
				 if(!$has_attr_tab)
				 {
				 	unset($this->p_tab_arr['attribute']);
				 }
			}
			//Check download option are avalilable for this product
			if(isset($input_arr['is_downloadable_product']) && $input_arr['is_downloadable_product'] == 'No')
			{
				unset($this->p_tab_arr['download_files']);
			}

			////Check shipping option is avalilable for this product
			if(isset($input_arr['is_downloadable_product']) && strtolower($input_arr['is_downloadable_product']) == 'yes')
			{
				unset($this->p_tab_arr['shipping']);
			}

			////Check shipping option is avalilable for this product
			if(isset($input_arr['is_free_product']) && strtolower($input_arr['is_free_product']) == 'yes')
			{
				unset($this->p_tab_arr['tax']);
			}

			if( (CUtil::chkIsAllowedModule('variations')) && (isset($input_arr['use_variation']) && strtolower($input_arr['use_variation']) == 1))
			{
				unset($this->p_tab_arr['stocks']);
			}


		 	if($action == 'add')
		 	{
			 	$this->p_tab_arr['basic'] = true;
			}
			else
			{
				$prev_value = false; //To check, if previous value are true, then make it next tab are visible
				foreach($this->p_tab_arr AS $key => $name)
			 	{
					$this->p_tab_arr[$key] = true;
				}
			}
		 }
		 else
		 {
		 	if($action == 'add')
		 	{
			 	$this->p_tab_arr['basic'] = true;
			}
		 }
		 return $this->p_tab_arr;
	}

	public function getSubCategoryList($category_id)
	{
		$r_arr = array('' => trans('common.select_option'));

		$sub_cat_arr = Products::getCategoriesList($category_id);
		if(count($sub_cat_arr) > 0)
		{
			$d_arr = $sub_cat_arr->toArray();
			foreach($d_arr AS $val)
			{
				$r_arr[$val['id']] = $val['category_name'];
			}
		}
		return $r_arr;
	}

	public function getproductValidation($input_arr, $id = 0, $tab = 'basic')
    {
		$rules_arr = $message_arr = array();
		if($tab == 'basic')
		{
			$rules_arr = array('product_name' => 'Required|min:'.Config::get("webshoppack.title_min_length").'|max:'.Config::get("webshoppack.title_max_length"),
								'product_category_id' => 'Required',
								'product_tags' => 'Required',
								'product_highlight_text' => 'max:'.Config::get("webshoppack.summary_max_length"),
								'demo_url' => 'url',
			);
			//To validate section, only if input from user form
			if(Input::has('user_section_id'))
			{
				$rules_arr['user_section_id'] = 'exists:user_product_section,id,user_id,'.$this->logged_user_id;
			}
			$message_arr = array('section_name.unique' => trans("product.section_already_exists"));
		}
		elseif($tab == 'price')
		{
			$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
			if($is_free_product != 'Yes')
			{
				$rules_arr = array('product_price' => 'Required|IsValidPrice|numeric|Min:1',
								'product_discount_price' => 'IsValidPrice|numeric|Max:'.$input_arr['product_price']
							);
				if($input_arr['product_discount_price'] > 0)
				{
					$date_format = 'd/m/Y';
					if($input_arr['product_discount_fromdate'] != '0000-00-00')
					{
						$rules_arr['product_discount_fromdate'] = 'Required|date_format:VAR_DATE_FORMAT';
					}
					if($input_arr['product_discount_todate'] != '0000-00-00' && $input_arr['product_discount_fromdate'] != '0000-00-00')
					{
						//check validation from database?..
						$from_date = str_replace('/', '-', $input_arr['product_discount_fromdate']);
						$from_date = date('Y-m-d', strtotime($from_date));

						$to_date = str_replace('/', '-', $input_arr['product_discount_todate']);
						$to_date = date('Y-m-d', strtotime($to_date));
						$rules_arr['product_discount_todate'] = 'Required|date_format:VAR_DATE_FORMAT|CustAfter:'.$from_date.','.$to_date;
						//To replace the datre format
						$rules_arr['product_discount_fromdate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_fromdate']);
						$rules_arr['product_discount_todate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_todate']);
					}
				}
				$message_arr = array('product_price.is_valid_price' => trans("product.invalid_product_price"),
									'product_discount_price.is_valid_price' => trans("product.invalid_product_price"),
									'product_price.min' => trans("product.err_tip_greater_than_zero"),
									'product_discount_price.max' => trans("product.invalid_product_discount_price"),
									'product_discount_todate.cust_after' => trans("product.invalid_product_discount_todate"),
									'date_format' => trans("product.invalid_date_format"),
									'required' => trans('common.required')
								);
			}
		}
		elseif($tab == 'attribute')
		{
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
						$message_arr[$key.'.required'] = trans('common.required');
						$message_arr[$key.'.alpha'] = trans("product.alpha_only");
						$message_arr[$key.'.numeric'] = trans("product.numeric_only");
					}
				}
			}
		}
		elseif($tab == 'publish')
		{
			$rules_arr = array('delivery_days' => 'numeric');
		}
		return array('rules' => $rules_arr, 'messages' => $message_arr);
	}

	public function checkProductHasAttributeTab($category_id)
	{
		$category_ids = $this->getAllTopLevelCategoryIds($category_id);
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

	public function validateDownloadTab($p_id)
	{
		if(Config::get('webshoppack.download_files_is_mandatory'))
		{
			$count = ProductResource::whereRaw('product_id = ? AND resource_type = ?', array($p_id, 'Archive'))->count();
			return ($count == 0) ? false : true;
		}
		return true;
	}

	public function getNewTabKey($current_tab, $p_id)
	{
		$new_tab_key = '';
		if($current_tab != '')
		{
			$tab_keys = array_keys($this->p_tab_arr);
			$new_tab_index = array_search($current_tab, $tab_keys);
			$new_tab_key =  isset($tab_keys[$new_tab_index +1 ])? $tab_keys[$new_tab_index +1 ] : '';
			//echo "<br>fn new_tab_key: ".$new_tab_key." ";echo "<br>here: ";//.exit;
			//Check Attribute & download are available for this product
			if($new_tab_key == 'stocks' || $new_tab_key == 'attribute' || $new_tab_key == 'download_files' || $new_tab_key == 'shipping' || $new_tab_key == 'tax')
			{
				$product = Products::initialize($p_id);
				try
				{

					$p_details = $product->getProductDetails(0, false);
					//echo "<pre>";print_r($p_details);echo "</pre>";
					if(count($p_details) > 0)
					{
						//echo "<br>new_tab_key inside count if: '".$new_tab_key."'";
						if($new_tab_key == 'attribute')
						{
							if(!$product->checkProductHasAttribute($p_details['product_category_id']))
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
						}
						else if($new_tab_key == 'shipping')
						{
							if($p_details['is_downloadable_product'] == 'Yes')
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
						}
						else if($new_tab_key == 'tax')
						{
							if($p_details['is_free_product'] == 'Yes')
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
						}
						else if($new_tab_key == 'stocks')
						{
							if(CUtil::chkIsAllowedModule('variations') &&  $p_details['use_variation'])
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
							else
							{
								return $new_tab_key;
							}
						}
						else
						{

							if($p_details['is_downloadable_product'] == 'No')
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
						}
					}
				}
				catch (Exception $e)
				{
					if($e instanceOf ProductNotFoundException)
					{
						return $new_tab_key;
					}
					else  if($e instanceOf InvalidProductIdException)
					{
						return $new_tab_key;
					}
				}
			}
		}
		return $new_tab_key;
	}

	public static function getProductDefaultThumbImage($p_id, $image_size = "thumb", $p_image_info = array())
	{
		$image_exists = false;
		$image_details = array();
		$image_title = trans('product.no_image');
		$no_image = true;

		if(count($p_image_info) > 0 && $image_size == "default" && isset($p_image_info['default_img']) && $p_image_info['default_img'] != '' )
		{
			$image_exists = true;
			$image_details["default_img"] = $p_image_info['default_img'];
			$image_details["default_ext"] = $p_image_info['default_ext'];
			$image_details["default_width"] = $p_image_info['default_l_width'];
			$image_details["default_height"] = $p_image_info['default_l_height'];
			$image_details["default_title"] = $p_image_info['default_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		if(count($p_image_info) > 0 && $image_size == "large" && isset($p_image_info['thumbnail_img']) && $p_image_info['thumbnail_img'] != '' )
		{
			$image_exists = true;
			$image_details["thumbnail_img"] = $p_image_info['thumbnail_img'];
			$image_details["thumbnail_ext"] = $p_image_info['thumbnail_ext'];
			$image_details["thumbnail_width"] = $p_image_info['thumbnail_l_width'];
			$image_details["thumbnail_height"] = $p_image_info['thumbnail_l_height'];
			$image_details["thumbnail_title"] = $p_image_info['thumbnail_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		if(count($p_image_info) > 0 && $image_size == "thumb" && isset($p_image_info['thumbnail_img']) && $p_image_info['thumbnail_img'] != '' )
		{
			$image_exists = true;
			$image_details["thumbnail_img"] = $p_image_info['thumbnail_img'];
			$image_details["thumbnail_ext"] = $p_image_info['thumbnail_ext'];
			$image_details["thumbnail_width"] = $p_image_info['thumbnail_t_width'];
			$image_details["thumbnail_height"] = $p_image_info['thumbnail_t_height'];
			$image_details["thumbnail_title"] = $p_image_info['thumbnail_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		if(count($p_image_info) > 0 && $image_size == "small" && isset($p_image_info['thumbnail_img']) && $p_image_info['thumbnail_img'] != '' )
		{
			$image_exists = true;
			$image_details["thumbnail_img"] = $p_image_info['thumbnail_img'];
			$image_details["thumbnail_ext"] = $p_image_info['thumbnail_ext'];
			$image_details["thumbnail_width"] = $p_image_info['thumbnail_s_width'];
			$image_details["thumbnail_height"] = $p_image_info['thumbnail_s_height'];
			$image_details["thumbnail_title"] = $p_image_info['thumbnail_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		$image_path = "";
		$image_url = "";
		$image_attr = "";
		if($image_exists)
		{
			$image_path = URL::asset(Config::get("webshoppack.photos_folder"))."/";
		}

		$cfg_user_img_large_width = Config::get("webshoppack.photos_large_width");
		$cfg_user_img_large_height = Config::get("webshoppack.photos_large_height");

		$cfg_user_img_thumb_width = Config::get("webshoppack.photos_thumb_width");
		$cfg_user_img_thumb_height = Config::get("webshoppack.photos_thumb_height");

		$cfg_user_img_small_width = Config::get("webshoppack.photos_small_width");
		$cfg_user_img_small_height = Config::get("webshoppack.photos_small_height");

		switch($image_size)
		{
			case 'default':
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $cfg_user_img_large_width, $cfg_user_img_large_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["default_img"]."L.".$image_details["default_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $image_details["default_width"], $image_details["default_height"]);
					$image_title = $image_details["default_title"];
					$no_image = false;
				}
				break;

			case 'large':
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $cfg_user_img_large_width, $cfg_user_img_large_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."L.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $image_details["thumbnail_width"], $image_details["thumbnail_height"]);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
				break;

			case "thumb":

				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_thumb_no_image");

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $cfg_user_img_thumb_width, $cfg_user_img_thumb_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $image_details["thumbnail_width"], $image_details["thumbnail_height"]);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
				break;

			case "small":

				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_small_no_image");

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $cfg_user_img_small_width, $cfg_user_img_small_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."S.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $cfg_user_img_small_width, $cfg_user_img_small_height);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
				break;

			default:

				$image_url = URL::asset("images/no_image").'/prodnoimage-215x170.jpg';
				$image_attr = BasicCUtil::TPL_DISP_IMAGE(215, 170, 215, 170);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE(215, 170, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
		}
		$image_details['image_url'] = $image_url;
		$image_details['image_attr'] = $image_attr;
		$image_details['title'] = $image_title;
		$image_details['no_image'] = $no_image;
		return $image_details;
	}

	/*public static function getProductDefaultThumbImageNew($p_id, $image_size = "thumb", $p_image_info = array())
	{
		$image_exists = false;
		$image_details = array();
		$image_title = trans('product.no_image');
		$no_image = true;
		if(count($p_image_info) > 0 && $image_size == "thumb" && isset($p_image_info['thumbnail_img']) && $p_image_info['thumbnail_img'] != '' )
		{
			$image_exists = true;
			$image_details["thumbnail_img"] = $p_image_info['thumbnail_img'];
			$image_details["thumbnail_ext"] = $p_image_info['thumbnail_ext'];
			$image_details["thumbnail_width"] = $p_image_info['thumbnail_width'];
			$image_details["thumbnail_height"] = $p_image_info['thumbnail_height'];
			$image_details["thumbnail_title"] = $p_image_info['thumbnail_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		if(count($p_image_info) > 0 && $image_size == "default" && isset($p_image_info['default_img']) && $p_image_info['default_img'] != '' )
		{
			$image_exists = true;
			$image_details["default_img"] = $p_image_info['default_img'];
			$image_details["default_ext"] = $p_image_info['default_ext'];
			$image_details["default_width"] = $p_image_info['default_width'];
			$image_details["default_height"] = $p_image_info['default_height'];
			$image_details["default_title"] = $p_image_info['default_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}
		if(count($p_image_info) > 0 && $image_size == "small" && isset($p_image_info['default_img']) && $p_image_info['default_img'] != '' )
		{
			$image_exists = true;
			$image_details["thumbnail_img"] = $p_image_info['thumbnail_img'];
			$image_details["thumbnail_ext"] = $p_image_info['thumbnail_ext'];
			$image_details["thumbnail_width"] = $p_image_info['thumbnail_width'];
			$image_details["thumbnail_height"] = $p_image_info['thumbnail_height'];
			$image_details["thumbnail_title"] = $p_image_info['thumbnail_title'];
			$image_details["image_folder"] = Config::get("webshoppack.photos_folder");
		}

		$image_path = "";
		$image_url = "";
		$image_attr = "";
		if($image_exists)
		{
			$image_path = URL::asset(Config::get("webshoppack.photos_folder"))."/";
		}

		$cfg_user_img_large_width = Config::get("webshoppack.photos_large_width");
		$cfg_user_img_large_height = Config::get("webshoppack.photos_large_height");

		$cfg_user_img_thumb_width = Config::get("webshoppack.photos_thumb_width");
		$cfg_user_img_thumb_height = Config::get("webshoppack.photos_thumb_height");

		$cfg_user_img_small_width = Config::get("webshoppack.photos_small_width");
		$cfg_user_img_small_height = Config::get("webshoppack.photos_small_height");

		switch($image_size)
		{
			case 'default':
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $cfg_user_img_large_width, $cfg_user_img_large_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["default_img"]."L.".$image_details["default_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $image_details["default_width"], $image_details["default_height"]);
					$image_title = $image_details["default_title"];
					$no_image = false;
				}
				break;

			case "thumb":

				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_thumb_no_image");

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $cfg_user_img_thumb_width, $cfg_user_img_thumb_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					//$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $image_details["thumbnail_width"], $image_details["thumbnail_height"]);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
				break;

			case "small":

				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_small_no_image");

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $cfg_user_img_small_width, $cfg_user_img_small_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."S.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $cfg_user_img_small_width, $cfg_user_img_small_height);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
				break;

			default:

				$image_url = URL::asset("images/no_image").'/product-thumb-170.jpg';
				$image_attr = BasicCUtil::TPL_DISP_IMAGE(215, 170, 215, 170);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE(215, 170, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
					$image_title = $image_details["thumbnail_title"];
					$no_image = false;
				}
		}
		$image_details['image_url'] = $image_url;
		$image_details['image_attr'] = $image_attr;
		$image_details['title'] = $image_title;
		$image_details['no_image'] = $no_image;
		return $image_details;
	}*/

	public function updateProductStatus($p_id, $product_status = 'Draft')
	{
	 	if(is_numeric($p_id) && $p_id > 0)
	 	{
			Product::whereRaw('id = ?', array($p_id))->update(array('product_status' => $product_status, 'last_updated_date' => DB::raw('NOW()')));
		}
	}

	 public function setProductPreviewType($p_id)
	 {
	 	$product = Products::initialize($p_id);
	 	$this->product_media_type = $product->setProductPreviewType();
	 }

	 public function  setAllowedUploadFormats($file_context = '')
	 {
		$allowed_formats = false;
		if ($this->product_media_type != '') {
			switch($this->product_media_type)
			{
				case 'image':

					if ($file_context == 'thumb')
					{
						$allowed_formats = implode(',', Config::get("webshoppack.thumb_format_arr"));
					}
					elseif ($file_context == 'default')
					{
						$allowed_formats = implode(',', Config::get("webshoppack.default_format_arr"));
					}
					elseif ($file_context == 'preview')
					{
						$allowed_formats = implode(',', Config::get("webshoppack.preview_format_arr"));
					}

					break;
				case 'archive':
					$allowed_formats = implode(',', Config::get("webshoppack.download_format_arr"));
					break;
			}
		}
		$this->allowed_upload_formats = $allowed_formats;
	 }

	public function  setMaxUploadSize($file_context = '')
	{
		$item_max_upload_size = 0;

		if ($this->product_media_type != '')
		{
			switch($this->product_media_type)
			{
				case 'image':
					if ($file_context == 'thumb')
					{
						$item_max_upload_size = Config::get("webshoppack.thumb_max_size");
					}
					elseif ($file_context == 'default')
					{
						$item_max_upload_size = Config::get("webshoppack.default_max_size");
					}
					elseif ($file_context == 'preview')
					{
						$item_max_upload_size = Config::get("webshoppack.preview_max_size");
					}

					break;
				case 'archive':
					$item_max_upload_size = Config::get("webshoppack.download_max_size");
					break;
			}
		}
		$this->product_max_upload_size = $item_max_upload_size;
	}

	public function insertResource($data)
	{
		$id = 0;
		if(count($data) > 0)
		{
		    $d_arr = array('product_id' => $data['product_id'],
		 		  		'resource_type' => $data['resource_type'],
						'filename' => $data['filename'],
						'ext' => $data['ext'],
						'title' => $data['title'],
						'width' => $data['width'],
						'height' => $data['height'],
						'l_width' => $data['l_width'],
						'l_height' => $data['l_height'],
						't_width' => $data['t_width'],
						't_height' => $data['t_height'],
						'server_url'=>$data['server_url'],
						'is_downloadable'=>$data['is_downloadable']
						);
		   $id = ProductResource::insertGetId($d_arr);
		}
		return $id;
    }

    public function uploadMediaFile($file_ctrl_name, $file_type,  &$file_info, $download_file = false)
	{
		if (!isset($_FILES[$file_ctrl_name])) return array('status'=>'error', 'error_message' => trans("product.products_select_file"));

		// default settings
		$file_original = '';
		$file_thumb = '';
		$file_large ='';
		$width = 0;
		$height = 0;
		$t_width = 0;
		$t_height = 0;
		$l_width = 0;
		$l_height = 0;
		$server_url = '';
		$is_downloadable = 'No';

		$file = Input::file('uploadfile');
		$file_size = $file->getClientSize();
		if($file_size == 0)
		{
			return array('status'=>'error', 'error_message' => trans("product.common_err_tip_invalid_file_size"));
		}
		$upload_file_name = $file->getClientOriginalName();
		$ext_index = strrpos($upload_file_name, '.') + 1;
		$ext = substr($upload_file_name, $ext_index, strlen($upload_file_name));
		$title = substr($upload_file_name, 0, $ext_index - 1);
		$filename_no_ext = uniqid(); // generate filename
		//$file = $filename_no_ext . '.' . $ext;

		if (!($file_size  <= $this->product_max_upload_size * 1024 * 1024))// size in MB
		{
			return array('status'=>'error', 'error_message' => trans("product.common_err_tip_invalid_file_size"));
		}

		switch($file_type) {
			case 'image':
				$file_path = Config::get("webshoppack.photos_folder");
				$server_url = URL::asset($file_path);
				$file_original  = $filename_no_ext . '.' . $ext;
				$file_thumb = $filename_no_ext . 'T.' . $ext;
				$file_large = $filename_no_ext . 'L.' . $ext;
				$file_small = $filename_no_ext . 'S.' . $ext;

				CUtil::chkAndCreateFolder($file_path);

				@chmod($file_original, 0777);
				@chmod($file_thumb, 0777);
				@chmod($file_large, 0777);
				@chmod($file_small, 0777);

				try{

					Image::make($file->getRealPath())->save($file_path.$file_original);

					//Resize original image for large image
					Image::make($file->getRealPath())
						->resize(Config::get("webshoppack.photos_large_width"), Config::get("webshoppack.photos_large_height"), true, false)
						->save($file_path.$file_large);

					 //Resize original image for thump image
					Image::make($file->getRealPath())
						->resize(Config::get("webshoppack.photos_thumb_width"), Config::get("webshoppack.photos_thumb_height"), true, false)
						->save($file_path.$file_thumb);

					//Resize original image for small image for index page
					Image::make($file->getRealPath())
						->resize(Config::get("webshoppack.photos_small_width"), Config::get("webshoppack.photos_small_height"), false, false)
						->save($file_path.$file_small);
				}
				catch(\Exception $e){
					return array('status'=>'error','error_message' => $e->getMessage());
				}

				list($width, $height) 		= getimagesize($file_path . $file_original);
				list($l_width, $l_height) 	= getimagesize($file_path . $file_large);
				list($t_width, $t_height) 	= getimagesize($file_path . $file_thumb);
				break;
			default:
				$file_type = ($file_type == 'archive') ? 'zip' : $file_type;
				$file_path = Config::get("webshoppack.archive_folder");
				try
				{
					$file->move($file_path, $file_path . $filename_no_ext . '.' . $ext);
				}
				catch(\Exception $e)
				{
					return array('status'=>'error','error_message' => trans("product.products_file_upload_error"));
				}
				$is_downloadable = ($download_file) ? 'Yes' : 'No';
				break;
		}

		$file_info = array(
			'title'				=> $title,
			'filename_no_ext'	=> $filename_no_ext,
			'ext'				=> $ext,
			'file_original'		=> $file_original,
			'file_thumb'		=> $file_thumb,
			'file_large'		=> $file_large,
			'width'				=> $width,
			'height'			=> $height,
			't_width'			=> $t_width,
			't_height'			=> $t_height,
			'l_width'			=> $l_width,
			'l_height'			=> $l_height,
			'server_url'		=> $server_url,
			'is_downloadable'	=> $is_downloadable);

		 return array('status'=>'success');
	}

	public function updateItemProductImage($p_id, $title, $file_info)
	{
		$this->removeItemImageFile($p_id, 'thumb'); // removes actual file if already exists
		$data_arr = array('thumbnail_img' => $file_info['filename_no_ext'],
								'thumbnail_ext' =>$file_info['ext'],
								'thumbnail_width' => $file_info['t_width'],
								'thumbnail_height' => $file_info['t_height']);

		if(empty($title))
		{
			$data_arr = array('thumbnail_img' => $file_info['filename_no_ext'],
								'thumbnail_ext' =>$file_info['ext'],
								'thumbnail_width' => $file_info['t_width'],
								'thumbnail_height' => $file_info['t_height'],
								'thumbnail_title' => $file_info['title']);
		}
		ProductImage::whereRaw('product_id = ?', array($p_id))->update($data_arr);
	}

	public function removeItemImageFile($p_id, $type='')
	{
		$fields = 'default_img as file_name, default_ext as file_ext';
		if (strcmp($type, 'thumb') == 0)
		{
			$fields = 'thumbnail_img as file_name, thumbnail_ext as file_ext';
		}
		$condition = ' FROM product_image WHERE product_id = '.$p_id;
		$d_arr = DB::select('SELECT '.$fields.$condition);
		if (count($d_arr) > 0)
		{
			foreach($d_arr AS $data)
			if ($data->file_name != '')
			{
				$file_path = Config::get("webshoppack.photos_folder");

				if (file_exists($file_path.$data->file_name.'.'.$data->file_ext))
				{
					unlink($file_path.$data->file_name.'.'.$data->file_ext);
				}
				if (file_exists($file_path.$data->file_name.'T.'.$data->file_ext))
				{
					unlink($file_path.$data->file_name.'T.'.$data->file_ext);
				}
				if (file_exists($file_path.$data->file_name.'L.'.$data->file_ext))
				{
					unlink($file_path.$data->file_name.'L.'.$data->file_ext);
				}
				return true;
			}
		}
		return false;
	}

	/*public function updateProductDefaultImage($p_id, $title, $file_info)
	{
		$this->removeItemImageFile($p_id, 'default'); // removes actual file if already exists

		$data_arr = array('default_img' => $file_info['filename_no_ext'],
								'default_ext' => $file_info['ext'],
								'default_width' => $file_info['l_width'],
								'default_height' => $file_info['l_height'],
								'default_orig_img_width' => $file_info['width'],
								'default_orig_img_height' => $file_info['height']);

		if (empty($title))
		{
			$data_arr = array('default_img' => $file_info['filename_no_ext'],
								'default_ext' => $file_info['ext'],
								'default_width' => $file_info['l_width'],
								'default_height' => $file_info['l_height'],
								'default_title' => $file_info['title'],
								'default_orig_img_width' => $file_info['width'],
								'default_orig_img_height' => $file_info['height']);
		}
		ProductImage::whereRaw('product_id = ?', array($p_id))->update($data_arr);
	}*/

	public function removeProductThumbImage($p_id)
	{
		$this->removeItemImageFile($p_id, 'thumb'); // removes actual file
        $d_arr = array('thumbnail_img' => '' ,
		 		  		'thumbnail_ext' => '' ,
		 		  		'thumbnail_width' => 0,
		 		  		'thumbnail_height' => 0,
						'thumbnail_title' => '' );
		ProductImage::whereRaw('product_id = ?', array($p_id))->update($d_arr);
        return true;
	}

	public function getCategoryDropOptions()
	{
		$category_list = array('' => trans('common.select_option'));
		$c_data = Products::getCategoriesList();

		foreach($c_data AS $row)
		{
			if($row['category_level'] != 0)
			{
				$category_list[$row['id']] = ($row['category_level']) ? str_repeat('&nbsp;', (self::MAX_CATEGORY_SPACING * ($row['category_level']))) . $row['category_name'] : $row['category_name'];
			}
		}
		return $category_list;
	}

	public function getProductCategoryArr($cat_id, $target_blank = false, $cat_link_alone = false, $call_page = 'product', $return_as_array = false)
    {
		$cat_arr = array();
		$prod_obj = Products::initialize();
		$cat_details = $prod_obj->getCategoryArr($cat_id);

		if(count($cat_details) > 0)
		{
			$i=0;
			$category = '';
			foreach($cat_details AS $cat)
			{
				if($i==0)
				{
					$i++;
					continue;
				}
				$category.= '/'.$cat->seo_category_name;
				if($call_page == 'browse')
					$cat_link = URL::to('browse'.$category);
				else
					$cat_link = URL::to('product'.$category);
				if(!$return_as_array)
				{
					if($target_blank)
						$cat_arr[$cat->seo_category_name] = '<a target="_blank" href="'.$cat_link.'">'.$cat->category_name.'</a>';
					else
						$cat_arr[$cat->seo_category_name] = '<a href="'.$cat_link.'">'.$cat->category_name.'</a>';
				}
				else
					$cat_arr[$cat->seo_category_name] = array('category_name' => $cat->category_name, 'cat_link' => $cat_link);

			}
			if($cat_link_alone)
				$cat_arr['cat_link'] = $cat_link;
			//$cat_arr = array_slice($cat_arr, 1); //To remove root category
		}
		return $cat_arr;
	}
	public function getProductBreadcrumbArr($cat_id)
    {
		$cat_arr = array();
		$cache_key = 'GPBA_cache_key_'.$cat_id;
		if (($q = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$q = DB::select('SELECT parent.category_name, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right  AND node.id = ? ORDER BY node.category_left;', array($cat_id));
			HomeCUtil::cachePut($cache_key, $q, Config::get('generalConfig.cache_expiry_minutes'));
		}
		$count = count($q);
		if(count($q) > 0)
		{
			$i = 0;
			foreach($q AS $cat)
			{
				$category = '';
				$category.= '/'.$cat->seo_category_name;
				$i++;
				if($i < $count)
					$cat_link = URL::to('product'.$category);
				else
					$cat_link ='';
				$cat_arr[$cat->seo_category_name] = $cat_link;
			}
			$cat_arr = array_slice($cat_arr, 1); //To remove root category
		}
		return $cat_arr;
	}

	public function deleteProductResource($product, $row_id)
	{
		# Get all attribute option ids related to the deleted attribute
		$d_arr = $product->getProductResource($row_id);

	    foreach($d_arr AS $data)
	    {
			if($data['resource_type'] == 'Image')
			{
				$file_path = Config::get("webshoppack.photos_folder");
				if (file_exists($file_path.$data['filename'].'.'.$data['ext']))
				{
					unlink($file_path.$data['filename'].'.'.$data['ext']);
				}
				if (file_exists($file_path.$data['filename'].'T.'.$data['ext']))
				{
					unlink($file_path.$data['filename'].'T.'.$data['ext']);
				}
				if (file_exists($file_path.$data['filename'].'L.'.$data['ext']))
				{
					unlink($file_path.$data['filename'].'L.'.$data['ext']);
				}
			}
			elseif($data['resource_type'] == 'Archive')
			{
				$file_path = Config::get("webshoppack.archive_folder");
				if (file_exists($file_path.$data['filename'].'.'.$data['ext']))
				{
					unlink($file_path.$data['filename'].'.'.$data['ext']);
				}
			}
		}
		$product->deleteProductResource($row_id);
		return $row_id;
	}

	public function downloadProductResouceFile($product_id = 0, $use_title = false)
	{
		$allowed_download = false;
		$product = Products::initialize($product_id);
		$q = $product->getDownloadProductDetails();

		if(count($q) > 0)
		{
			//check if the logged in user has access
			if($q[0]->product_user_id == $this->logged_user_id || $q[0]->is_free_product == 'Yes')
			{
				$allowed_download = true;
				$filename = $q[0]->filename . '.'. $q[0]->ext;
				$media_type = (strtolower($q[0]->resource_type) == 'archive') ? 'zip' : strtolower($q[0]->resource_type);
				$path = Config::get("webshoppack.archive_folder") ;

				if ($use_title && $q[0]->title != '')
				{
					$save_filename = preg_replace('/[^0-9a-z\.\_\-)]/i', '', $q[0]->title) . '.' . $q[0]->ext;
				}
				else
				{
					$save_filename = md5($product_id) . '.' . $q[0]->ext;
				}

				$pathToFile = public_path().'/'.$path.$filename;

				if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header("Content-Transfer-Encoding: binary");
					header('Pragma: public');
					header("Content-Length: ".filesize($pathToFile));
				}
				else
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header("Content-Transfer-Encoding: binary");
					header('Expires: 0');
					header('Pragma: no-cache');
					header("Content-Length: ".filesize($pathToFile));
				}

				ob_clean();
				flush();
				@readfile($pathToFile);
			}
		}
		if(!$allowed_download)
			die('Error: Unable to get the file!');
	}

	public function downloadProductFile($product_id = 0, $use_title = false)
	{
		$allowed_download = false;
		$product = Products::initialize($product_id);
		$q = $product->getDownloadProductDetails($product_id);

		if(count($q) > 0)
		{
			//check if the logged in user has access
			//if($q[0]->product_user_id == $this->logged_user_id || $q[0]->is_free_product == 'Yes')
			//{
				$allowed_download = true;
				$filename = $q[0]->filename . '.'. $q[0]->ext;
				$media_type = (strtolower($q[0]->resource_type) == 'archive') ? 'zip' : strtolower($q[0]->resource_type);
				$path = Config::get("webshoppack.archive_folder") ;

				if ($use_title && $q[0]->title != '')
				{
					$save_filename = preg_replace('/[^0-9a-z\.\_\-)]/i', '', $q[0]->title) . '.' . $q[0]->ext;
				}
				else
				{
					$save_filename = md5($product_id) . '.' . $q[0]->ext;
				}

				$pathToFile = public_path().'/'.$path.$filename;

				if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header("Content-Transfer-Encoding: binary");
					header('Pragma: public');
					header("Content-Length: ".filesize($pathToFile));
				}
				else
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header("Content-Transfer-Encoding: binary");
					header('Expires: 0');
					header('Pragma: no-cache');
					header("Content-Length: ".filesize($pathToFile));
				}

				ob_clean();
				flush();
				@readfile($pathToFile);
			//}
		}
		if(!$allowed_download)
			die('Error: Unable to get the file!');
	}


	public function addProductStatusComment($input_arr)
	{
		$c_id = 0;
		if(count($input_arr) > 0 )
		{
			$user_type = 'User';
			if($this->logged_user_id > 0) {
				if(Config::get("webshoppack.is_admin"))
				{
					$user_type = 'Admin';
				}
			}

			$product = Products::initialize($input_arr['product_id']);
			$product->addProductComment($this->logged_user_id, $input_arr['comment'], $user_type);
		}
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

	public function getAttributesList($category_id, $p_id = 0)
	{
		$data_arr = array();
		if(is_numeric($category_id) && $category_id > 0)
		{
			//get all the category_id up in tree and the corresponding attribute ids..
			$category_ids = $this->getAllTopLevelCategoryIds($category_id);
			$cache_key = 'ALCK_'.$category_id.'_'.$p_id;
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
				if($p_id > 0)
				{
					$dafault_value = $this->getAttributeValue($p_id, $val->attribute_id, $val->attribute_question_type, $dafault_value);
				}

				$data_arr[$val->attribute_id] = array('attribute_id' => $val->attribute_id,
													  'attribute_question_type' => $val->attribute_question_type,
													  'validation_rules' => $val->validation_rules,
													  'default_value' => $dafault_value,
													  'status' => $val->status,
													  'attribute_label' => $val->attribute_label
												);
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

	public function getProductViewURL($p_id, $p_details = array())
	{
		$url_slug = '';
		if(isset($p_details['product_code']) && isset($p_details['url_slug']))
		{
			$url_slug = $p_details['product_code']. '-'.$p_details['url_slug'];
		}
		else
		{
			$product = Products::initialize($p_id);
			$p_details = $product->getProductDetails();
			if(count($p_details) > 0)
			{
				$url_slug = $p_details['product_code']. '-'.$p_details['url_slug'];
			}
		}
		if($url_slug != '')
		{
			$view_url = URL::to('product/view/'.$url_slug);
			return $view_url;
		}
		return '';
	}

	public function getProductViewURLNew($p_id, $p_details = array())
	{
		$url_slug = '';
		if(isset($p_details['product_code']) && isset($p_details['url_slug']))
		{
			$url_slug = $p_details['product_code']. '-'.$p_details['url_slug'];
		}
		if($url_slug != '')
		{
			$view_url = URL::to('product/view/'.$url_slug);
			return $view_url;
		}
		return '';
	}

	public function getAdminProductViewURL($p_id, $p_details = array())
	{
		$url_slug = '';
		if(isset($p_details['product_code']) && isset($p_details['url_slug']))
		{
			$url_slug = $p_details['product_code']. '-'.$p_details['url_slug'];
		}
		else
		{
			$product = Products::initialize($p_id);
			$p_details = $product->getProductDetails();
			if(count($p_details) > 0)
			{
				$url_slug = $p_details['product_code']. '-'.$p_details['url_slug'];
			}
		}
		if($url_slug != '')
		{
			$view_url = URL::to('admin/product/view/'.$url_slug);
			return $view_url;
		}
		return '';
	}

	public function checkIsShopNameExist()
	{
		$shop_name = ShopDetails::where('user_id', '=', $this->logged_user_id)->pluck('shop_name');
		return ($shop_name == '')? false : true;
	}

	public function getAttributeOptions($attribute_id)
	{
		$attribute = Products::initializeAttribute();
		return $attribute->getAttributeOptions($attribute_id);

	}

	public function addProductCategoryAttribute($input_arr)
	{
		$error_msg = '';
		if(isset($input_arr['product_category_id']) && is_numeric($input_arr['product_category_id']))
		{
			$attr_arr = $this->getAttributesList($input_arr['product_category_id']);
			foreach($input_arr AS $key => $val)
			{
				if(starts_with($key, 'attribute_'))
				{
					$name_arr = explode('_', $key);
					if(count($name_arr) == 2)
					{
						$id = $name_arr[1];
						if(isset($attr_arr[$id]) && $attr_arr[$id]['attribute_question_type'] != '')
						{
							$attr_type = $attr_arr[$id]['attribute_question_type'];
							switch($attr_type)
							{
								case 'text':
								case 'textarea':
									$product = Products::initialize($input_arr['id']);
									$details = $product->insertProductAttribute($id, $input_arr['attribute_'.$id]);
									$json_data = json_decode($details, true);
									if(isset($json_data['status']) && $json_data['status'] == 'error')
									{
										foreach($json_data['error_messages'] AS $err_msg)
										{
											$error_msg .= "<p>".$err_msg."</p>";
										}
									}
									break;

								case 'select':
								case 'option': // radio button
								case 'multiselectlist':
								case 'check': // checkbox
									$this->insertAttributeOption($input_arr, $id);
									break;
							}
						}
					}
				}
			}
			if($error_msg == '')
			{
				$product = Products::initialize($input_arr['id']);
				$product->updateLastUpdatedDate();
			}
			return $error_msg;
		}
		return false;;
	}

	public function insertAttributeOption($input_arr, $attribute_id)
	{
		if(isset($input_arr['attribute_'.$attribute_id]))
		{
			if(is_array($input_arr['attribute_'.$attribute_id]))
			{
				foreach($input_arr['attribute_'.$attribute_id] AS $attr_key => $attr_val)
				{
					$product = Products::initialize($input_arr['id']);
					$product->insertAttributeOptionByOptionId($attribute_id, $attr_val);
				}
			}
			else
			{
				$product = Products::initialize($input_arr['id']);
				$product->insertAttributeOptionByOptionId($attribute_id, $input_arr['attribute_'.$attribute_id]);
			}
		}
	}

	public function updateLastUpdatedDate($p_id)
	 {
	 	if(is_numeric($p_id) && $p_id > 0)
	 	{
			Product::whereRaw('id = ?', array($p_id))->update(array('last_updated_date' => DB::raw('NOW()')));
		}
	 }

	public function fetchShopItems($shop_user_id, $current_p_id = 0, $limit = 5)
	{
		$items_arr = array();
		$p_details = Product::whereRaw('product_user_id = ? AND product_status = ? AND id != ?', array($shop_user_id, 'Ok', $current_p_id))
								->orderByRaw('RAND()')
								->take($limit)
								->Select(DB::raw("product.id, product.product_status, product.total_downloads, product.url_slug, product.product_user_id, product.product_sold, product.product_added_date,
									   product.product_category_id, product.product_tags, product.is_free_product, product.total_views, product.product_discount_price, product.product_discount_fromdate,
									   product.product_discount_todate, product.product_price, product.product_name, product.product_description, product.product_highlight_text,
									   product.date_activated, NOW() as date_current, IF( ( DATE( NOW() ) BETWEEN product.product_discount_fromdate AND product.product_discount_todate), 1, IF((product.product_discount_price > 0 AND product.product_discount_fromdate = \'0000-00-00\' AND product.product_discount_todate = \'0000-00-00\'),1,0) ) AS have_discount,
									   product.product_price_currency, product.product_price_usd, product.product_discount_price_usd"))
								->get( array('id', 'product_code', 'product_name', 'url_slug'));
		return $p_details;
	}

	public function getProductShopURL($id, $shop_details = array())
	{
		$url_slug = '';
		if(isset($shop_details['url_slug']))
		{
			$url_slug = $shop_details['url_slug'];
		}
		else
		{
			$s_details = ShopDetails::where('id', $id)->first(array('url_slug'));
			if(count($s_details) > 0)
			{
				$url_slug = $s_details['url_slug'];
			}
		}
		if($url_slug != '')
		{
			$view_url = URL::to('shop/'.$url_slug);
			return $view_url;
		}
		return '';
	}


	public function getProductList($id)
	{
		$product_list =	Product::where('product_user_id', '=', $id)
						->paginate(Config::get('webshoppack.paginate'));
        return $product_list;
	}

	public function getProductStatusArr()
	{
		$status_arr = array( 'All' => Lang::get('product.refine.all'),
							 'Draft' => Lang::get('product.refine.draft_label'),
							 'Ok' => Lang::get('product.refine.active'),
							 'ToActivate' => Lang::get('product.refine.rejected_label'),
							 'NotApproved' => Lang::get('product.refine.pending_approval_label'),
							 'Deleted' => Lang::get('product.refine.deleted')
									);
		return $status_arr;
	}

	public function setSearchFields($input)
	{
		$this->srch_arr['search_product_code'] =(isset($input['search_product_code']) && $input['search_product_code'] != '') ? $input['search_product_code'] : "";
		$this->srch_arr['search_product_name'] =(isset($input['search_product_name']) && $input['search_product_name'] != '') ? $input['search_product_name'] : "";
		$this->srch_arr['search_product_category']= (isset($input['search_product_category']) && $input['search_product_category'] != '') ? $input['search_product_category'] : "";
		$this->srch_arr['search_product_status']= (isset($input['search_product_status']) && $input['search_product_status'] != '') ? $input['search_product_status'] : "All";
		if(CUtil::chkIsAllowedModule('featuredproducts')) {
			$this->srch_arr['search_featured_product'] =(isset($input['search_featured_product']) && $input['search_featured_product'] != '') ? $input['search_featured_product'] : "";
		}
	}

	public function getSearchValue($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : '';
	}

	public function buildMyProductQuery($prod_obj)
	{
		if($this->getSearchValue('search_product_code'))
		{
			$prod_obj->setFilterProductCode($this->getSearchValue('search_product_code'));
		}
		if($this->getSearchValue('search_product_name') != '')
		{
			$prod_obj->setFilterProductName($this->getSearchValue('search_product_name'));
		}
		if($this->getSearchValue('search_product_category') > 0)
		{
			$prod_obj->setFilterProductCategory($this->getSearchValue('search_product_category'));
		}
		if($this->getSearchValue('search_product_status') != '' && $this->getSearchValue('search_product_status') != 'All')
		{
			$prod_obj->setFilterProductStatus($this->getSearchValue('search_product_status'));
		}
		if(CUtil::chkIsAllowedModule('featuredproducts')) {
			if($this->getSearchValue('search_featured_product') != '') {
				$prod_obj->setFilterFeaturedProduct($this->getSearchValue('search_featured_product'));
			}
		}
	}

	public function deleteProduct($p_id, $p_details)
	{
		if(count($p_details) == 0)
		{
			$p_details = Product::whereRaw('id = ?', array($p_id))->first();
		}
		//To update product status to deleted
		$affected_rows = Product::where('id', '=', $p_id)->update( array('product_status' => 'Deleted'));
		return true;
	}

	public function getBaseAmountToDisplay($price, $currency, $return_as_arr = false)
	{
		$currency_symbol = "USD";
		$currency_symbol_font = "$";

		$currency_details = Products::chkIsValidCurrency($currency);
		if(count($currency_details) > 0)
		{
			$currency_symbol = $currency_details["currency_code"];
			$currency_symbol_font = $currency_details["currency_symbol"];
			if($currency_symbol == "INR")
				$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
		}
		$formatted_amt = "";
		$formatted_amt = number_format ($price, 2, '.','');
		$formatted_amt = str_replace(".00", "", $formatted_amt);
		$formatted_amt = str_replace("Rs.", "", $formatted_amt);

		if($return_as_arr)
			return compact('currency_symbol','formatted_amt');
		else
			return "<small>".$currency_symbol. '</small> <strong>' . $formatted_amt.'</strong>';
	}

	/*public function chkIsValidCurrency($currency_code)
	{
		$details = array();
		$selected_currency_code = CurrencyExchangeRate::whereRaw('currency_code= ? AND status = "Active" AND display_currency = "Yes" ', array($currency_code))->first();
		if(count($selected_currency_code))
		{
			$details['country'] = $selected_currency_code['country'];
			$details['currency_code'] = $selected_currency_code['currency_code'];
			$details['exchange_rate'] = $selected_currency_code['exchange_rate'];
			$details['currency_symbol'] = $selected_currency_code['currency_symbol'];
		}
		return $details;
	}*/

	public function getTotalProduct($shop_user_id)
	{
		return Product::whereRaw('product_user_id = ? AND product_status = ?', array($shop_user_id, 'Ok'))->count();
	}

	public function formatProductPrice($product_details)
	{
		# Return values
		$price_details['disp_price'] = $price_details['disp_label'] = $price_details['disp_link'] = $price_details['disp_discount'] = false;

		# Assigned default values from the input
		$is_free_product = isset($product_details['is_free_product']) ? $product_details['is_free_product'] : 'No';
		//$have_discount = isset($product_details['have_discount']) ? $product_details['have_discount'] : 0;
		$have_discount = 0;
		$product_discount_price = isset($product_details['product_discount_price']) ? $product_details['product_discount_price'] : 0.00;
		$product_price = isset($product_details['product_price']) ? $product_details['product_price'] : 0.00;
		$product_discount_fromdate = isset($product_details['product_discount_fromdate']) ? $product_details['product_discount_fromdate'] : '';
		$product_discount_todate = isset($product_details['product_discount_todate']) ? $product_details['product_discount_todate'] : '';


		//echo "product_discount_fromdate: ".$product_discount_fromdate;
		//echo "<br>product_discount_todate: ".$product_discount_todate;
		# If not checked the discount option in query & discount from & to dates passed then checked the discount
		if($product_discount_fromdate!='0000-00-00' && $product_discount_todate!='0000-00-00')
		{
			$discount_from_time = strtotime($product_discount_fromdate);
			$discount_end_time = strtotime($product_discount_todate);
			$curr_time = strtotime(date('Y-m-d'));
			 if($discount_end_time >= $curr_time && $discount_from_time <= $curr_time)
			{
				$have_discount = 1;
			}
		}
		else if($product_discount_fromdate != '0000-00-00')
		{
			$discount_from_time = strtotime($product_discount_fromdate);
			$curr_time = strtotime(date('Y-m-d'));
			if($discount_from_time <= $curr_time)
			{
				$have_discount = 1;
			}
		}
		else if($product_discount_todate != '0000-00-00')
		{
			$discount_end_time = strtotime($product_discount_todate);
			$curr_time = strtotime(date('Y-m-d'));
			if($discount_end_time >= $curr_time)
			{
				$have_discount = 1;
			}
		}
		else
		{
			if($product_discount_price > 0)
            {
                $have_discount = 1;
            }
		}

		# Checked discount or not
		if($is_free_product == 'No' && $have_discount && $product_discount_price > 0)
		{
			$price_details['disp_discount'] = true;
		}

		# Set price details
		if($product_price)
		{
			$price_details['disp_price'] = true;
		}
		return $price_details;
	}

	public function formatProductPriceNew($product_details, $allow_cache = true)
	{
		$quantity = (Input::has('product_qty') && Input::get('product_qty') != '') ? Input::get('product_qty') : 1;
		# Return values
		$price_details['disp_price'] = $price_details['disp_label'] = $price_details['disp_link'] = $price_details['disp_discount'] = false;
		$price_details['group_disp_discount'] = $price_details['group_disp_price'] = false;
		# Assigned default values from the input
		$is_free_product = isset($product_details['is_free_product']) ? $product_details['is_free_product'] : 'No';
		$have_discount = 0;
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$price_group_details = $this->getPriceGroupsDetailsNew($product_details, $logged_user_id, $quantity, 0, $allow_cache);

		/*echo '<br>Responds For product id: '.$product_details['id'].' Logged user id '.$logged_user_id;
		echo '<pre>';print_r($price_group_details);echo '</pre>';*/
		$product_discount_price = isset($price_group_details['discount']) ? $price_group_details['discount'] : 0.00;
		$product_price = isset($price_group_details['price']) ? $price_group_details['price'] : 0.00;

		if($product_discount_price > 0 && $product_price > $product_discount_price)
        	$have_discount = 1;

        # Checked discount or not
		if($is_free_product == 'No' && $have_discount && $product_discount_price > 0) {
			$price_details['disp_discount'] = true;
		}

		# Set price details
		if($product_price) {
			$price_details['disp_price'] = true;
		}
		$price_details['product'] = $price_group_details;
		//echo '<pre>';print_r($price_details);echo '</pre>';exit;
		return $price_details;
	}

	public function getPriceGroupsDetailsNew($product_detail, $user_id, $quantity = 1, $matrix_id = 0, $allow_cache = true)
	{
		if (isset($product_detail['id'])) {
			$p_id = $product_detail['id'];
			$product = Products::initialize($p_id);
		} else {
			$p_id = $product_detail;
			$product = Products::initialize($p_id);
			$product->setIncludeDeleted(true);
			$product_detail = $product->getProductDetails();
		}
		$price_details = array();
		$group_id = 0;
		if(isset($product_detail['is_free_product']) && $product_detail['is_free_product']=='Yes')
		{
			$price_details['product_id'] = $p_id;
			$price_details['group_id'] = $group_id;
	        $price_details['currency'] = $product_detail['product_price_currency'];
	        $price_details['price'] = $product_detail['product_price'];
	        $price_details['price_usd'] = $product_detail['product_price_usd'];
	        if($product_detail['product_discount_price'] > $product_detail['product_price'])
	        	$discount_percentage = ($product_detail['product_price']/$product_detail['product_discount_price'])*100;
	        else
	        	$discount_percentage = 0;
	        $price_details['discount_percentage'] = $discount_percentage;
	        $price_details['discount'] = $product_detail['product_discount_price'];
	        $price_details['discount_usd'] = $product_detail['product_discount_price_usd'];
		}
		else
		{
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

				if(CUtil::chkIsAllowedModule('variations') && $matrix_id > 0)
				{
					$variations_service = new VariationsService();
					$variations_det_arr = $variations_service->populateMatrixDetails($matrix_id, $p_id);
					if(COUNT($variations_det_arr) > 0)
					{
						$variation_price	= $price_details['discount'];
						if(isset($variations_det_arr['price_impact']) && $variations_det_arr['price_impact']!= '')
						{
							switch($variations_det_arr['price_impact'])
							{
								case 'increase':
									$variation_price += $variations_det_arr['price'];
									break;
								case 'decrease':
									$variation_price += $variations_det_arr['price'];
									break;
							}
						}
						$price_details['discount'] = $variation_price;
						$price_details['variation_details'] = $variations_det_arr;
					}
				}

				if(CUtil::chkIsAllowedModule('deals'))
				{
					$deal_service = new DealsService();

					$input_det_arr = array();
			        $input_det_arr['item_id'] = $p_id;
			        $input_det_arr['item_owner_id'] = isset($product_detail['product_user_id'])?$product_detail['product_user_id']:'';
			        $deal_details =  $deal_service->fetchItemDealDetails($input_det_arr);
					if(COUNT($deal_details) > 0 && isset($deal_details['deal_available']) && $deal_details['deal_available'])
					{
						$item_deal_discount = $price_details['discount'] * ($deal_details['discount_percentage'] / 100 );
						$item_deal_price = $price_details['discount'] - $item_deal_discount;
						$deal_price_det['item_deal_available'] = 1;
						$deal_price_det['deal_discount'] = $deal_details['discount_percentage'];
						$deal_price_det['deal_id'] = $deal_details['deal_id'];
						$deal_price_det['tipping_qty_for_deal'] = $deal_details['tipping_qty_for_deal'];
						$deal_price_det['normal_price'] = $price_details['discount'];
						$deal_price_det['deal_price'] = $item_deal_price;
						$deal_price_det['viewdeal_link'] = $deal_details['view_deal_url'];
						$price_details['deal_details'] = $deal_price_det;
						$price_details['discount'] = $item_deal_price;
					}
				}
			}
		}
		//echo "<pre>";print_r($price_details);echo "</pre>";exit;
		return $price_details;
	}

	/*public function checkIsShopPaypalUpdated()
	{
		$shop_paypal_id = UsersShopDetails::where('user_id', '=', $this->logged_user_id)->pluck('paypal_id');
		return ($shop_paypal_id == '')? false : true;
	}*/

	public function updateUserTotalProducts($user_id)
	{
		$p_count = $this->getTotalProduct($user_id);
		User::where('id', '=', $user_id)->update( array('total_products' => $p_count));
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function sendProductMailToUserAndAdmin($p_id, $user_notes = '')
	{
		$product = Products::initialize($p_id);
		unset($product::$_product_details[$p_id]);
		$product_details = $product->getProductDetails();

		$user_details = CUtil::getUserDetails($product_details['product_user_id']);
		$product_code = $product_details['product_code'];
		$url_slug = $product_details['url_slug'];
		$view_url = $this->getProductViewURLNew($p_id, $product_details);
		$subject = trans('product.product_created_published');
		if($product_details['product_status'] == 'ToActivate')
		{
			$subject = Config::get('generalConfig.site_name')." - ".trans('product.product_created_to_activate');
		}
		elseif($product_details['product_status'] == 'NotApproved')
		{
			$subject = Config::get('generalConfig.site_name')." - ".trans('product.product_created_disapprove');
		}

		$data = array(
			'product_code'	=> $product_details['product_code'],
			'product_name'  		=> $product_details['product_name'],
			'url_slug'  		=> $product_details['url_slug'],
			'product_description' => CUtil::wordWrap($product_details['product_description'], 300),
			'product_user_id' => $product_details['product_user_id'],
			'is_free_product'	  => $product_details['is_free_product'],
			'product_status'	  => $product_details['product_status'],
			'product_status_lang'	  => $this->getProductStatusLang($product_details['product_status']),
			'product_tags'	  => $product_details['product_tags'],
			'display_name'	 => $user_details['display_name'],
			'user_email'	 => $user_details['email'],
			'view_url'		=> $view_url,
			'subject'		=> $subject,
			'user_notes' => $user_notes
		);

		//Mail to User
		try {
			Mail::send('emails.productCreated', $data, function($m) use ($data) {
					$m->to($data['user_email']);
					$m->subject($data['subject']);
				});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
		$this->sendNotificationMailForAdmin($p_id, compact('product_details', 'user_details'));
	}

	public function sendNotificationMailForAdmin($p_id, $arr)
	{
		$product = Products::initialize($p_id);
		$product_details = $product->getProductDetails();
		$arr['product_details'] = isset($arr['product_details']) ?  $arr['product_details'] :
															 $product_details;
		$arr['user_details'] = isset($arr['user_details'])? $arr['user_details'] :
															 CUtil::getUserDetails($arr['product_details']->product_user_id);

		$view_url = $this->getProductViewURLNew($p_id, $arr['product_details']);
		$arr['product_details']['view_url'] = $view_url;
		$arr['product_details']['product_status_lang'] = $this->getProductStatusLang($arr['product_details']['product_status']);
		$arr['product_details']['product_notes'] = $product->getUserLastProductNote($this->logged_user_id);

		$category_list = $product->getCategoryArr($arr['product_details']['product_category_id']);
		$category_arr = array();
		foreach($category_list AS $cat)
		{
			$category_arr[] = $cat->category_name;
		}
		$category_arr = array_slice($category_arr, 1); //To remove root category
		$arr['product_details']['category'] = implode(' / ', $category_arr);
		$arr['product_details']['product_price'] = $arr['product_details']['product_price'];
		$arr['product_details']['product_discount_price'] = $arr['product_details']['product_discount_price'];
		$arr['product_details']['product_price_currency'] = $arr['product_details']['product_price_currency'];

		$subject = trans('product.product_created_published_admin');
		if($arr['product_details']['product_status'] == 'ToActivate')
		{
			$subject = Config::get('generalConfig.site_name')." - ".trans('product.product_created_to_activate_admin');
		}
		elseif($arr['product_details']['product_status'] == 'NotApproved')
		{
			$subject = Config::get('generalConfig.site_name')." - ".trans('product.product_created_disapprove_admin');
		}

		$arr['subject'] = $subject;
		$arr['admin_email'] = Config::get("generalConfig.invoice_email");//Config::get('webshoppack.admin_email');

		try {
			Mail::send('emails.productCreatedAdmin', $arr, function($m) use ($arr) {
				$m->to($arr['admin_email']);
				$m->subject($arr['subject']);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function getProductStatusLang($p_status)
	{
		$status = $p_status;
		if($p_status == 'Draft')
		{
			$status = trans("product.status_in_draft");
		}
		elseif($p_status == 'Ok')
		{
			$status = trans("product.status_active");
		}
		elseif($p_status == 'ToActivate')
		{
			$status = trans("product.status_to_activate");
		}
		elseif($p_status == 'NotApproved')
		{
			$status = trans("product.status_in_not_approved");
		}
		return $status;
	}

	public function updateProductResourceImageDisplayOrder($resourcednd)
	{
		$product = Products::initialize();//$p_id
		foreach($resourcednd as $display_order=>$resource_id_str)
		{
			$temp = explode("_", $resource_id_str);
			$resource_id = (isset($temp[1]) && $temp[1]) ? (int) $temp[1] : false;

			if($resource_id)
			{
				$product->updateProductResourceDisplayOrder($resource_id, $display_order);
			}
		}
	}

	public function populateOptionsArray()
	{
		$populateOptionsArray = array();
		$list_paging_sort_by = Config::get("webshoppack.list_paging_sort_by");
		$inc = 0;
		foreach ($list_paging_sort_by as $key => $filter)
		{
			if(($key == 'featured') && !(CUtil::chkIsAllowedModule('featuredproducts'))) {
				continue;
			}
			$populateOptionsArray[$inc]['href'] = Request::url().'?orderby_field='.$filter;
			$inner_txt = (trans('product.product_listing_'.$key) != '') ? trans('product.product_listing_'.$key) : $key;
			$populateOptionsArray[$inc]['innervalue'] = $filter;
			$populateOptionsArray[$inc]['innertext'] = $inner_txt;
			$inc++;
		}
		return 	$populateOptionsArray;
	}

	public function setProductOrderBy($product, $orderby_field)
	{
		if($orderby_field == '')
		{
			$orderby_field = 'id';
		}
		$product->setOrderByField($orderby_field);
	}

	public function remExcludeValuesFromSearchTags($searchheader_val)
	{
		$text_to_search = $searchheader_val;
		$symbol_arr = array(',', '.', '?', '*');
		$text_to_search = str_replace($symbol_arr, ' ', $text_to_search);
		//echo $text_to_search;
		$search_arr = explode(" ", $text_to_search);
		$search_arr = CUtil::arraytolower(array_filter($search_arr));
		//Fetch the allowed tags from csv file
		//$include_tagslist_arr = CUtil::arraytolower($this->fetchIncludeTagsFromCsv());
		//Checked the csv values matched the search values

		//$matched_array_values = array_intersect($include_tagslist_arr, $search_arr);
		$search_arr = array_filter($search_arr, 'CUtil::remminLength');
		//Merge the matched csv values into the array
		//$merged_arr = array_merge($search_arr, $matched_array_values);
		//$search_arr = array_unique(CUtil::arraytolower($merged_arr));
		$result_arr = $stop_words_arr = array();
		if(count($search_arr))
		{
			$stop_words_arr = $this->fetchExcludedTags($search_arr);

			if(!empty($stop_words_arr))
			{
				foreach($search_arr as $word)
				{
					if(!in_array($word, $stop_words_arr))
					{
						$word = strtolower($word);
						$result_arr[] = $word;
					}
				}
			}
			else
				$result_arr = array_values($search_arr);
		}
		return $result_arr;
	}

	public function fetchExcludedTags($search_arr)
	{
		$stop_words_arr = array();
		$tags = "";
		foreach($search_arr as $tag)
		{
			$tags .= "'".addslashes($tag)."'".',';
		}
		$tags = substr($tags, 0, strrpos($tags, ','));
		$tags_info = ApiExcludeTags::Select('tags')->whereRaw(DB::raw('tags IN ('. $tags .')'))->get();
		if(count($tags_info) > 0)
		{
			$stop_words_arr[] = $tags_info->tags;
		}
	}

	public function getProductStocks($p_id) {
		$prod_obj = Products::initialize($p_id);
		$stock_details_arr = $prod_obj->getProductStocksList($p_id);
		//echo "<pre>";print_r($stock_details_arr);echo "</pre>";
		$stock_details = array();
		foreach($stock_details_arr as $stock) {
			$stock_details['quantity'] = $stock['quantity'];
			$stock_details['serial_numbers'] = $stock['serial_numbers'];
//			if($stock['stock_country_id'] == 38) {
//				$stock_details['stock_country_id_china'] = $stock['stock_country_id'];
//				$stock_details['quantity_china'] = $stock['quantity'];
//				$stock_details['serial_numbers_china'] = $stock['serial_numbers'];
//			}
//			if($stock['stock_country_id'] == 153) {
//				$stock_details['stock_country_id_pak'] = $stock['stock_country_id'];
//				$stock_details['quantity_pak'] = $stock['quantity'];
//				$stock_details['serial_numbers_pak'] = $stock['serial_numbers'];
//			}
		}
		return $stock_details;
	}

	/*public function fetchIncludeTagsFromCsv()
	{
		$row = 1;
		$include_tags_list = array();

		$destinationPath = Config::get("webshoppack.tags_csv_file");
		$tagscsv_path = \public_path().'/'.$destinationPath;

		if (($handle = fopen($tagscsv_path, "r")) !== FALSE)
		{
		    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
			{
		        $num = count($data);
		        $row++;
		        for ($c=0; $c < $num; $c++)
				{
		            $include_tags_list[] = $data[$c];
		        }
		    }
		    fclose($handle);
		}
		return $include_tags_list;
	}*/

	public function getShippingTemplateDetails($cart_id, $shipping_template, $product_id = 0, $quantity = 1) {
		$shipping_template_service = new ShippingTemplateService();
		$shipping_company_id = 0;
		$shipping_company_name = '';
		$shipping_fee = 0;


		$CheckOutService = new CheckOutService();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$shipping_address_id = 0;
		$shipping_address_details = array();
		//$shipping_billing_address_ids_arr = $CheckOutService->getUserCartShippingAddress($logged_user_id);
		$shipping_billing_address_ids_arr = $CheckOutService->getUserShippingAddress($logged_user_id);
		if(count($shipping_billing_address_ids_arr) > 0)
		{
			$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];
			//$billing_address_id = $shipping_billing_address_ids_arr['billing_address_id'];
		}
		$zip_code = '';
		$country_id = 0;
		$address_obj = Webshopaddressing::Addressing();
		$shipping_address = $address_obj->getAddresses(array('id' => $shipping_address_id));
		if(count($shipping_address) > 0)
		{
			//echo "<pre>";print_r($shipping_address);echo "</pre>";exit;
			$country_id = isset($shipping_address[0]->country_id) ? $shipping_address[0]->country_id : 0;
			$zip_code = isset($shipping_address[0]->zip_code) ? $shipping_address[0]->zip_code : '';
		}

		$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($shipping_template, 0, $product_id, $quantity, array('country_id' => $country_id, 'zip_code' => $zip_code), array());

		$shipping_company_id_cookie = Config::get('generalConfig.site_cookie_prefix').'_shipping_company_'.$cart_id;
		$shipping_company_id = BasicCUtil::getCookie($shipping_company_id_cookie);

		if($shipping_company_id=='')
		{
			$min_index = $shipping_template_service->min_with_key($shipping_companies_list, 'shipping_fee');
			if($min_index!='')
				$shipping_company_id = 	$shipping_companies_list[$min_index]['company_id'];
		}
		else{
			$comp_found = false;
			foreach($shipping_companies_list as $shipping) {
				if($shipping['company_id']==$shipping_company_id)
				{
					$comp_found = true;
					break;
				}
			}
			if(!$comp_found)
			{
				$min_index = $shipping_template_service->min_with_key($shipping_companies_list, 'shipping_fee');
				if($min_index!='')
					$shipping_company_id = 	$shipping_companies_list[$min_index]['company_id'];
			}
		}

		if($shipping_company_id == "") {
			if(count($shipping_companies_list) > 0) {
				$shipping_company_id = isset($shipping_companies_list[0]['company_id']) ? $shipping_companies_list[0]['company_id'] : 0;
				$shipping_company_name = isset($shipping_companies_list[0]['company_name']) ? $shipping_companies_list[0]['company_name'] : '';
				$shipping_fee = isset($shipping_companies_list[0]['shipping_fee']) ? $shipping_companies_list[0]['shipping_fee'] : 0;
				foreach($shipping_companies_list as $shipping) {
					if(!isset($shipping['error_message']) || $shipping['error_message'] == '') {
						if($shipping['shipping_fee'] > 0 && $shipping['shipping_fee'] < $shipping_fee) {
							$shipping_company_id = $shipping['company_id'];
							$shipping_company_name = $shipping['company_name'];
							$shipping_fee = $shipping['shipping_fee'];
						}
					}
				}
			}
		}
		else {
			//Get sipping fee for this comapny and assign //Aboo
			foreach($shipping_companies_list as $shipping) {
				if($shipping['company_id'] == $shipping_company_id) {
					if(!isset($shipping['error_message']) || $shipping['error_message'] == '') {
						$shipping_fee = $shipping['shipping_fee'];
						$shipping_company_name = $shipping['company_name'];
					}
				}
			}
		}

		$d_arr['shipping_companies_list'] = $shipping_companies_list;
		$d_arr['shipping_company_id_selected'] = $shipping_company_id;
		$d_arr['shipping_company_name_selected'] = $shipping_company_name;
		$d_arr['shipping_company_fee_selected'] = $shipping_fee;
		return $d_arr;
	}
}