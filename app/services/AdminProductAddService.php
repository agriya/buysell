<?php

class AdminProductAddService
{
	public $p_tab_arr = array();
	public $product_media_type = '';
	public $allowed_upload_formats = '';
	public $product_max_upload_size = 0;
	public $alert_message = '';
	public $validate_tab_arr = array();

	function __construct()
	{
		$this->p_tab_arr = array('basic' => false, 'price' => false, 'stocks' => false, 'meta' => false, 'shipping' => false, 'tax' => false, 'attribute' => false, 'preview_files' => false, 'variations' => false, 'download_files' => false, 'cancellation_policy' => false, 'status' => false);
		$this->productService = new ProductService();
    }

    public function populateProductResources($p_id, $resource_type='', $is_downloadable = 'No')
	{
	    $d_arr = ProductResource::where('product_id', '=', $p_id)->where('resource_type', '=', $resource_type)->where('is_downloadable', '=', $is_downloadable)
				 ->orderBy('display_order', 'ASC')
				 ->get(array('id', 'resource_type', 'filename', 'ext', 'title', 'is_downloadable', 'width', 'height', 't_width', 't_height', 'l_width', 'l_height'))
				 ->toArray();

		$resources_arr = array();
		$download_filename = '';
		$download_url = '';
		foreach($d_arr AS $data)
		{
			if ($is_downloadable == 'Yes')
			{
	    		$download_filename = preg_replace('/[^0-9a-z\.\_\-]/i','', $data['title']);
				if (empty($download_filename))
				{
		    		$download_filename = md5($p_id);
		    	}
				$download_url = URL::action('AdminProductAddController@getProductActions'). '?action=download_file&product_id=' . $p_id;
	    	}

			$product_preview_url = '';
			if($data['resource_type'] == 'Audio' || $data['resource_type'] == 'Video')
			{
				$product_preview_url = '';
			}

			$resources_arr[] = array(
				'resource_id' => $data['id'],
				'resource_type' => $data['resource_type'],
				'filename_thumb' => $data['filename'] . 'T.' . $data['ext'],
				'filename_large' => $data['filename'] . 'L.' . $data['ext'],
				'filename_original' => $data['filename'] . '.' . $data['ext'],
				'download_filename' => $download_filename . '.' . $data['ext'],
				'download_url' => $download_url,
				'width' => $data['width'],
				'height' => $data['height'],
				't_width' => $data['t_width'],
				't_height' => $data['t_height'],
				'l_width' => $data['l_width'],
				'l_height' => $data['l_height'],
				'ext' => $data['ext'],
				'title' => $data['title'],
				'is_downloadable' => $data['is_downloadable'],
				'product_preview_url' => $product_preview_url
			);
		}
		return $resources_arr;
	}

	public function getProductStatusDropList()
	{
		$status_arr = array();
		$product_status_arr = array('Draft', 'Ok', 'ToActivate', 'NotApproved');

		foreach($product_status_arr AS $value)
		{
			$status_arr[$value] = $this->productService->getProductStatusLang($value);
		}
		return $status_arr;
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

			if(isset($input_arr['is_free_product']) && strtolower($input_arr['is_free_product']) == 'yes')
			{
				unset($this->p_tab_arr['tax']);
			}

			////Check shipping option is avalilable for this product
			if(isset($input_arr['is_downloadable_product']) && strtolower($input_arr['is_downloadable_product']) == 'yes')
			{
				unset($this->p_tab_arr['stocks']);
				unset($this->p_tab_arr['shipping']);
				unset($this->p_tab_arr['cancellation_policy']);
			}

			//Variation start
			if( (!CUtil::chkIsAllowedModule('variations')) OR (isset($input_arr['use_variation']) && strtolower($input_arr['use_variation']) == 0))
			{
				unset($this->p_tab_arr['variations']);
			}

			if( (CUtil::chkIsAllowedModule('variations')) && (isset($input_arr['use_variation']) && strtolower($input_arr['use_variation']) == 1))
			{
				unset($this->p_tab_arr['stocks']);
			}
			//Variation end
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
			// Initally don't show variation tab.
			unset($this->p_tab_arr['variations']);
		 }
		 return $this->p_tab_arr;
	}

	public function getproductValidation($input_arr, $id = 0, $tab = 'basic', $action = 'add')
    {
		$rules_arr = $message_arr = array();
		if($tab == 'basic')
		{
			$rules_arr = array('product_name' => 'Required|min:'.Config::get("webshoppack.title_min_length").'|max:'.Config::get("webshoppack.title_max_length"),
								'url_slug' => 'Required|IsValidSlugUrl:'.$input_arr['url_slug'],
								'product_category_id' => 'Required',
								'product_tags' => 'Required',
								'product_highlight_text' => 'max:'.Config::get("webshoppack.summary_max_length"),
								'demo_url' => 'url',
			);

			if($action == 'add')
			{
				$input_user_id = CUtil::getUserId($input_arr['user_code']);
				if($input_user_id == '') {
					$input_user_id = 0;
				}
				//,user_status,"Ok"
				$rules_arr = array_merge($rules_arr, array('user_code' => 'Required|min:7|IsUserCodeExists:'.$input_user_id.',"Yes"'));
				$user_msg_arr = array('url_slug.is_valid_slug_url' => trans('admin/productAdd.invalid_slug_url'),
										'user_code.min' => trans("admin/productAdd.invalid_user_code"),
										'user_code.is_user_code_exists' => trans("admin/productAdd.invalid_user_code"),
									);
				$message_arr = array_merge($message_arr, $user_msg_arr);
			}
		}
		elseif($tab == 'price')
		{
			$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
			if($is_free_product != 'Yes')
			{
				$rules_arr = array(//'product_price_currency' => 'Required|exists:currency_exchange_rate,currency_code', // Commented to make US
								'product_price' => 'Required|IsValidPrice|numeric|Min:1',
								'product_discount_price' => 'IsValidPrice|numeric|Max:'.$input_arr['product_price'],
								'site_transaction_fee_type' => 'required_if:global_transaction_fee_used,"No"',
								'site_transaction_fee' => 'required_if:global_transaction_fee_used,"No"|required_if:site_transaction_fee_type,"Flat"|numeric',
								'site_transaction_fee_percent' => 'required_if:global_transaction_fee_used,"No"|required_if:site_transaction_fee_type,"Percentage"|numeric'
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
				$attr_arr = $this->productService->getAttributesList($input_arr['product_category_id']);
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
		elseif($tab == 'status')
		{
			$rules_arr = array('product_status' => 'Required');
		}
		return array('rules' => $rules_arr, 'messages' => $message_arr);
	}

	public function checkIsShopOwner($user_id)
	{
		$shop = Products::initializeShops();
		$user_shop_details = $shop->getUsersShopDetails($user_id);//UsersShopDetails::Select('is_shop_owner')->whereRaw('user_id = ?', array($user_id))->first();
		if(count($user_shop_details) > 0) {
			if($user_shop_details['is_shop_owner'] == 'Yes') {
				return true;
			}
		}
		return false;
	}

	public function getProductUserSections($user_id)
	{
		$section_list_arr = array();
		//getProductSections
		$q = Products::getProductSections($user_id);
		//$q = UserProductSection::where('status', '=', 'Yes')->where('user_id', '=', $user_id)->get();
		$i = 0;
		foreach($q AS $value)
		{
			$section_list_arr[$i]['id'] = $value->id;
			$section_list_arr[$i]['section_name'] = $value->section_name;
			$i++;
		}
		return $section_list_arr;
	}

	public function addProduct($input_arr)
	{
		$p_id = 0;
		if(count($input_arr) > 0 )
		{
			$user_id = CUtil::getUserId($input_arr['user_code']);
			$product_code = CUtil::generateRandomUniqueCode('P', 'product', 'product_code');
			$url_slug = Str::slug($input_arr['product_name']);
			$data_arr = array('product_code' => $product_code,
                            'product_name' => $input_arr['product_name'],
                            'product_description' => $input_arr['product_description'],
                            'meta_title' => '',
                            'meta_keyword' => '',
                            'meta_description' => '',
                            'product_highlight_text' => $input_arr['product_highlight_text'],
                            'demo_url' => $input_arr['demo_url'],
                            'demo_details' => $input_arr['demo_details'],
                            'product_tags' => $input_arr['product_tags'],
                            'user_section_id' => $input_arr['user_section_id'],
                            'product_preview_type' => $input_arr['product_preview_type'],
                            'product_status' => 'Draft',
                            'product_price_currency' => Config::get('webshoppack.site_default_currency'), //Make default USD format currency
                            'product_category_id' => $input_arr['my_category_id'],
                            'url_slug' => isset($input_arr['url_slug'])? $input_arr['url_slug'] : $url_slug,
                            'product_added_date' => DB::raw('NOW()'),
                            'last_updated_date' => DB::raw('NOW()'),
                            'product_user_id' => $user_id);

			$p_id = Product::insertGetId($data_arr);

			//To add dumb data for product image
			$p_img_arr = array('product_id' => $p_id);
			$p_img_id = ProductImage::insertGetId($p_img_arr);
		}
		return $p_id;
	}

	public function getNewTabKey($current_tab, $p_id)
	{
		$new_tab_key = '';
		if($current_tab != '')
		{
			$tab_keys = array_keys($this->p_tab_arr);
			$new_tab_index = array_search($current_tab, $tab_keys);
			$new_tab_key =  isset($tab_keys[$new_tab_index +1 ])? $tab_keys[$new_tab_index +1 ] : '';
			//Check Attribute & download are available for this product
			if($new_tab_key == 'stocks' || $new_tab_key == 'attribute' || $new_tab_key == 'download_files' || $new_tab_key == 'shipping' || $new_tab_key == 'tax' || $new_tab_key == 'cancellation_policy' || $new_tab_key == 'variations')
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
						else if($new_tab_key == 'shipping' || $new_tab_key == 'cancellation_policy')
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
						 else if($new_tab_key == 'variations')
						{
							if(CUtil::chkIsAllowedModule('variations') && $p_details['is_downloadable_product'] == 'No' &&  $p_details['use_variation'])
							{
								return $new_tab_key;
							}
							else
							{
								return $this->getNewTabKey($new_tab_key, $p_id);
							}
						}
						 else if($new_tab_key == 'stocks')
						{
							if(($p_details['is_downloadable_product'] == 'Yes') || (CUtil::chkIsAllowedModule('variations') &&  $p_details['use_variation']))
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

	public function updateProduct($input_arr, $tab = 'basic')
	{

		$return_arr = array('status' => false, 'validate_tab_arr' => array());

		if(count($input_arr) > 0)
		{
			$data_arr = array();
			if($tab == 'basic')
			{
				//To remove old category attribute values..
				$product_category_id = Product::whereRaw('id = ?', array($input_arr['id']))->pluck('product_category_id');
				if($product_category_id != $input_arr['my_category_id'])
				{
					$this->productService->removeProductCategoryAttribute($input_arr['id']);
				}
				$data_arr = array('product_name' => $input_arr['product_name'],
								   'url_slug' => $input_arr['url_slug'],
		                           'product_description' => $input_arr['product_description'],
		                           'product_highlight_text' => $input_arr['product_highlight_text'],
		                           'demo_url' => $input_arr['demo_url'],
		                           'demo_details' => $input_arr['demo_details'],
		                           'product_tags' => $input_arr['product_tags'],
		                           'user_section_id' => $input_arr['user_section_id'],
		                           'product_preview_type' => $input_arr['product_preview_type'],
		                           'product_category_id' => $input_arr['my_category_id'],
		                           'last_updated_date' => DB::raw('NOW()'),
			                      );
			}
			elseif($tab == 'price')
			{
				$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
				$data_arr = array('is_free_product' => $is_free_product);

		         if($is_free_product == 'No')
				 {
				 	if($input_arr['product_discount_price'] > 0)
					{
						$from_date = str_replace('/', '-', $input_arr['product_discount_fromdate']);
						$from_date = date('Y-m-d', strtotime($from_date));

						$to_date = str_replace('/', '-', $input_arr['product_discount_todate']);
						$to_date = date("Y-m-d", strtotime($to_date));

						$data_arr['product_discount_fromdate'] =  $from_date;
					 	$data_arr['product_discount_todate'] =  $to_date;
					}
					$site_transaction_fee_type = $input_arr['site_transaction_fee_type'];
					if(isset($input_arr['global_transaction_fee_used']) && $input_arr['global_transaction_fee_used'] == 'Yes')
					{
						$site_transaction_fee_percent = 0;
						$site_transaction_fee = 0;
						$global_transaction_fee_used = 'Yes';
						$site_transaction_fee_type = 'Flat'; // To reset default site transaction fee type as Flat
					}
					else
					{
						$site_transaction_fee_percent = isset($input_arr['site_transaction_fee_percent'])? $input_arr['site_transaction_fee_percent']: 0;
						$site_transaction_fee = isset($input_arr['site_transaction_fee'])? $input_arr['site_transaction_fee']: 0;
						$global_transaction_fee_used = 'No';
					}
					$data_arr['site_transaction_fee_type'] = $site_transaction_fee_type;
					$data_arr['site_transaction_fee'] = $site_transaction_fee;
					$data_arr['site_transaction_fee_percent'] = $site_transaction_fee_percent;
					$data_arr['global_transaction_fee_used'] = $global_transaction_fee_used;

					$data_arr['last_updated_date'] = DB::raw('NOW()');
					$data_arr['product_price_currency'] = Config::get('webshoppack.site_default_currency');//'USD';//$input_arr['product_price_currency'];
				 	$data_arr['product_price'] = $input_arr['product_price'];
				 	$data_arr['product_price_usd'] = CUtil::convertBaseCurrencyToUSD($input_arr['product_price'], Config::get('webshoppack.site_default_currency'));
				 	$data_arr['product_discount_price'] = $input_arr['product_discount_price'];
					$data_arr['product_discount_price_usd'] = CUtil::convertBaseCurrencyToUSD($input_arr['product_discount_price'], Config::get('webshoppack.site_default_currency'));
		            //$data_arr['allow_to_offer'] = isset($input_arr['allow_to_offer'])? $input_arr['allow_to_offer']: 'No';
				 }
			}
			elseif($tab == 'meta')
			{
				$data_arr = array( 'meta_title' => $input_arr['meta_title'],
		                            'meta_keyword' => $input_arr['meta_keyword'],
		                            'meta_description' => $input_arr['meta_description'],
		                            'last_updated_date' => DB::raw('NOW()')
			                      );
			}
			elseif($tab == 'attribute')
			{
				//To delete old attribute values..
				$this->productService->removeProductCategoryAttribute($input_arr['id']);
				$attr_status = $this->productService->addProductCategoryAttribute($input_arr);
				return array('status' => $attr_status, 'validate_tab_arr' => $this->validate_tab_arr);
			}
			elseif($tab == 'status')
			{
				if($input_arr['product_notes'] != '')
				{
					$note_arr = array('product_id' => $input_arr['id'], 'comment' => $input_arr['product_notes']);
					$c_id = $this->productService->addProductStatusComment($note_arr);
				}
				$data_arr['delivery_days'] = $input_arr['delivery_days'];
				$data_arr['last_updated_date'] = DB::raw('NOW()');

				//To update status
				if($input_arr['edit_product'] != '')
				{
					if($input_arr['product_status'] == 'Ok')
					{
						$validate_tab_arr = $this->checkProductForPublish($input_arr['id']);
						if($validate_tab_arr['allow_to_publish'])
						{
							$this->alert_message = 'product_publish_success';
							$data_arr['product_status'] = $input_arr['product_status'];
						}
						else
						{
							$this->validate_tab_arr = $validate_tab_arr['tab_arr'];
						}

					}
					else
					{
						$this->alert_message = 'products_updated_success_msg';
						$data_arr['product_status'] = $input_arr['product_status'];
					}

				}
			}
			elseif($tab == 'preview_files')
			{
				//No need any update for preview tab..
				return array('status' => true, 'validate_tab_arr' => $this->validate_tab_arr);

			}

			if(count($data_arr) > 0)
			{
				Product::whereRaw('id = ?', array($input_arr['id']))->update($data_arr);
				//To update product status
				if(isset($data_arr['product_status']) && $data_arr['product_status'] == 'Ok')
				{
					$product_user_id = Product::whereRaw('id = ?', array($input_arr['id']))->pluck('product_user_id');
					$this->productService->updateUserTotalProducts($product_user_id);
				}
				//Send mail alert to user for publish and submit for approval products...
				if(isset($data_arr['product_status']))
				{
					if($data_arr['product_status'] == 'Ok')
					{
						//To send published mail, when user publish product at first time
						$date_activated = Product::whereRaw('id = ?', array($input_arr['id']))->pluck('date_activated');
						if($date_activated == '0')
						{
							//To update prouduct activated date time.
							Product::whereRaw('id = ?', array($input_arr['id']))->update( array('date_activated' => DB::raw('UNIX_TIMESTAMP(NOW())')));
							$this->productService->sendProductMailToUserAndAdmin($input_arr['id'], $input_arr['product_notes']);
						}
					}
					else if($data_arr['product_status'] == 'ToActivate' || $data_arr['product_status'] == 'NotApproved')
					{
						$this->productService->sendProductMailToUserAndAdmin($input_arr['id'], $input_arr['product_notes']);
					}
				}

			    return array('status' => true, 'validate_tab_arr' => $this->validate_tab_arr);
			}
		}
		return $return_arr;
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
		$s_width = 0;
		$s_height = 0;
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

				try {

					Image::make($file->getRealPath())->save($file_path.$file_original);;

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
				list($s_width, $s_height) 	= getimagesize($file_path . $file_small);
				break;
			case 'swap_image':
				$file_path = Config::get("variations::variations.swap_img_folder");
				$server_url = URL::asset($file_path);
				$file_original  = $filename_no_ext . '.' . $ext;
				$file_thumb = $filename_no_ext . 'T.' . $ext;
				$file_large = $filename_no_ext . 'L.' . $ext;

				CUtil::chkAndCreateFolder($file_path);

				@chmod($file_original, 0777);
				@chmod($file_thumb, 0777);
				@chmod($file_large, 0777);

				try {

					Image::make($file->getRealPath())->save($file_path.$file_original);;

					//Resize original image for large image
					Image::make($file->getRealPath())
						->resize(Config::get("variations::variations.swap_img_large_width"), Config::get("variations::variations.swap_img_large_height"), true, false)
						->save($file_path.$file_large);

					 //Resize original image for thump image
					Image::make($file->getRealPath())
						->resize(Config::get("variations::variations.swap_img_thumb_width"), Config::get("variations::variations.swap_img_thumb_height"), true, false)
						->save($file_path.$file_thumb);

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
			's_width'			=> $s_width,
			's_height'			=> $s_height,
			't_width'			=> $t_width,
			't_height'			=> $t_height,
			'l_width'			=> $l_width,
			'l_height'			=> $l_height,
			'server_url'		=> $server_url,
			'is_downloadable'	=> $is_downloadable);

		 return array('status'=>'success');
	}

	public function setProductPreviewType($p_id)
	{
		$product = Products::initialize($p_id);
	 	$this->product_media_type = $product->setProductPreviewType();

		//$this->product_media_type = Product::where('id', '=', $p_id)->pluck('product_preview_type');
	}

	public function downloadProductResouceFile($product_id = 0, $use_title = false)
	{
		$allowed_download = false;
		$q = DB::select('SELECT filename, ext, resource_type, title, product_user_id, is_free_product FROM product_resource AS IRS, product AS MPI WHERE IRS.product_id = '.$product_id.' AND IRS.product_id = MPI.id AND is_downloadable = "Yes"');

		if(count($q) > 0)
		{
			//check if the logged in user has access
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($q[0]->product_user_id == $logged_user_id || $q[0]->is_free_product == 'Yes')
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

				// Any errors from this function is suppressed to avoid path begin revealed.
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

	public function checkProductForPublish($p_id)
	{
		$rtn_arr = array('allow_to_publish' => false, 'tab_arr' => array());
		$p_details = Product::whereRaw('id = ?', array($p_id))->first()->toArray();
		if(count($p_details) > 0)
		{
			$tab_arr = $this->validateTabList($p_id, $p_details);
			//Check manually for download tab..
			$available_tab_arr = array_filter($tab_arr, function ($val){ return (($val) ? true: false);});
			$allow_to_publish = (count($tab_arr) == count($available_tab_arr))? true : false;
			$rtn_arr = array('allow_to_publish' => $allow_to_publish, 'tab_arr' => $tab_arr);
		}
		return $rtn_arr;
	}

	public function validateTabList($p_id, $input_arr = array())
	{
		 if($input_arr['product_discount_price'] > 0)
		 {
			$input_arr['product_discount_fromdate'] = date('d/m/Y', strtotime($input_arr['product_discount_fromdate']));
			$input_arr['product_discount_todate'] = date('d/m/Y', strtotime($input_arr['product_discount_todate']));
		 }

		 $tab_arr = array_map(function ($val){ return false;}, $this->p_tab_arr);
		 if(count($input_arr) > 0)
		 {
			$p_id = ($p_id == '')? 0 : $p_id;
			//check prodcut category has attributes..
			if(isset($input_arr['product_category_id'])) //No need to check for add product page
			{
				 $has_attr_tab = $this->productService->checkProductHasAttributeTab($input_arr['product_category_id']);
				 if(!$has_attr_tab)
				 {
				 	unset($tab_arr['attribute']);
				 }
			}
			//Check download option are avalilable for this product
			if(isset($input_arr['is_downloadable_product']) && $input_arr['is_downloadable_product'] == 'No')
			{
				unset($tab_arr['download_files']);
			}
	 		foreach($tab_arr AS $key => $name)
		 	{
				if($key == 'download_files')
				{
					if($this->productService->validateDownloadTab($p_id))
					{
						$tab_arr[$key] = true;
					}
					/*else
					{
						break;
					}*/
				}
				else
				{
					$temp_input_arr = $input_arr;
					if($key == 'attribute')
					{
						$temp_input_arr = $this->productService->getProductCategoryAttributeValue($p_id, $input_arr['product_category_id']);
					}
					$validator_arr = $this->getproductValidation($temp_input_arr, $p_id, $key, '');
					$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
					if($validator->passes())
					{
						$tab_arr[$key] = true;
					}
					/*else
					{
						break;
					}*/
				}
			}
		 }
		 return $tab_arr;
	}

	/*public function deleteProductCancellation($p_id = '')
	{
		if($p_id == '')
			return false;

		$product = Products::initialize($p_id);
		$product->setCancellationPolicyFileName('');
		$product->setCancellationPolicyFileType('');
		$product->setCancellationPolicyServerUrl('');
		$details = $product->save();
		return $details;
	}*/

	public function updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath, $p_id)
	{
		$return_arr = array();
		$config_path = Config::get('webshoppack.product_cancellation_policy_folder');
		CUtil::chkAndCreateFolder($config_path);
		$file->move(Config::get("webshoppack.product_cancellation_policy_folder"),$file_name.'.'.$file_ext);
		// open file a image resource
		//Image::make($file->getRealPath())->save(Config::get("webshoppack.shop_cancellation_policy_folder").$file_name.'_O.'.$file_ext);

		$this->deleteProductCancellationPolicyFile($p_id);

		$return_arr = array('file_ext' => $file_ext, 'file_name' => $file_name, 'file_server_url' => $destinationpath);
		return $return_arr;
	}
	public function deleteProductCancellationPolicyFile($p_id,$folder_name = '')
	{
		$product = Products::initialize($p_id);
		$p_details = $product->getProductDetails();

		if(count($p_details) > 0 && $p_details['cancellation_policy_filename'] != '')
		{
			$filename = $p_details['cancellation_policy_filename'];
			$ext = $p_details['cancellation_policy_filetype'];

			$product->setCancellationPolicyFileName('');
			$product->setCancellationPolicyFileType('');
			$product->setCancellationPolicyServerUrl('');
			$details = $product->save();

			if($folder_name == '')
				$folder_name = Config::get('webshoppack.product_cancellation_policy_folder');

			$this->deleteCancellationPolicyFiles($filename, $ext, $folder_name);
			return true;
		}
		return false;
	}
	public function deleteCancellationPolicyFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename.".".$ext))
		{
			unlink($folder_name.$filename.".".$ext);
		}
	}
}