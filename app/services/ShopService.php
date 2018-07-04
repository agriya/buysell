<?php

class ShopService
{
	public function getPaymentAnalyticsDetails()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$details = array();
		$details = User::Select('paypal_id', 'shop_status')->where('id', $logged_user_id)->first();
		return $details;
	}

	public static $_shop_details;
	public function getShopDetails($user_id = 0)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($user_id == 0)
		{
			$user_id = $logged_user_id;
		}
		$shop_details = array();
		if (isset(ShopService::$_shop_details[$user_id])) {
			$shop_details = ShopService::$_shop_details[$user_id];
		} else {
			$shop_details = ShopDetails::Select('id', 'user_id', 'shop_name', 'url_slug', 'shop_slogan', 'shop_desc', 'shop_address1', 'shop_address2', 'shop_city', 'shop_state',
							'shop_zipcode', 'shop_country', 'shop_message', 'shop_contactinfo', 'image_name', 'image_ext', 'image_server_url', 't_height', 't_width')
							->where('user_id', $user_id)->first();
			ShopService::$_shop_details[$user_id] = $shop_details;
		}
		return $shop_details;
	}

	public function getShopPaypalDetails($user_id = 0)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($user_id == 0)
		{
			$user_id = $logged_user_id;
		}
		$user_id = $logged_user_id;

		$users = User::Select('paypal_id')->where('id', $user_id)->first();
		return $users;
	}

	public function getCountryList()
	{
		$country_list_arr = array();
		$country_list_arr = Products::getCountryList();
		return $country_list_arr;
	}

	/*
	public function updateShopAnalytics($input)
	{
		$data_arr['shop_analytics_code'] = $input['shop_analytics_code'];
		User::where('user_id', $this->logged_user_id)->update($data_arr);
	}*/

	public function processValidation($input_arr)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$rules = array(
				'shop_name' => 'Required|Min:'.Config::get('webshoppack.shopname_min_length').'|Max:'.Config::get('webshoppack.shopname_max_length').'|unique:shop_details,shop_name,'.$this->logged_user_id.',user_id',
				'url_slug' => 'Required|unique:shop_details,url_slug,'.$this->logged_user_id.',user_id',
				'shop_slogan' => 'Min:'.Config::get('webshoppack.shopslogan_min_length').'|Max:'.Config::get('webshoppack.shopslogan_max_length'),
				'shop_desc' => 'Min:'.Config::get('webshoppack.fieldlength_shop_description_min').'|Max:'.Config::get('webshoppack.fieldlength_shop_description_max'),
				'shop_contactinfo' => 'Min:'.Config::get('webshoppack.fieldlength_shop_contactinfo_min').'|Max:'.Config::get('webshoppack.fieldlength_shop_contactinfo_max'),
		);
		$message = array('shop_name.unique' => trans('shopDetails.shopname_already_exists'),
						'url_slug.unique' => trans('shopDetails.shopurlslug_already_exists'),
						);
		return array('rules' => $rules, 'messages' => $message);
	}

	public function updateShopOwnerStatus($input)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		User::whereRaw('id = ?', array($this->logged_user_id))->update($data_arr);
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function updateShopPaypal($input)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$data_arr['paypal_id'] = $input['paypal_id'];
		User::whereRaw('id = ?', array($logged_user_id))->update($data_arr);
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function isShopAlreadyAdded()
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_obj = Products::initializeShops();
		return $shop_obj->checkIsShopNameExist($this->logged_user_id);
	}

	public function updateShopDetails($input)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();

		$this->updateShopOwnerStatus($input);
		$already_added = true;
		if($this->isShopAlreadyAdded())
		{
			//Update shop details
			$data_arr['shop_name'] = $input['shop_name'];
			$data_arr['url_slug'] = $input['url_slug'];
			$data_arr['shop_slogan'] = $input['shop_slogan'];
			$data_arr['shop_desc'] = $input['shop_desc'];
		//	$data_arr['shop_message'] = $input['shop_message'];
			$data_arr['shop_contactinfo'] = $input['shop_contactinfo'];
			ShopDetails::whereRaw('user_id = ?', array($this->logged_user_id))->update($data_arr);
		}
		else
		{
			$already_added = false;
			$data_arr['shop_name'] = $input['shop_name'];
			$data_arr['url_slug'] = $input['url_slug'];
			$data_arr['shop_slogan'] = $input['shop_slogan'];
			$data_arr['shop_desc'] = $input['shop_desc'];
		//	$data_arr['shop_message'] = $input['shop_message'];
			$data_arr['shop_contactinfo'] = $input['shop_contactinfo'];
			$data_arr['user_id'] = $this->logged_user_id;
			$shop = new ShopDetails;
			$shop->insert($data_arr);
		}
		return $already_added;
	}

	public function updateShopAddress($input)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();

		if($this->isShopAlreadyAdded())
		{
			$data_arr['shop_country'] = $input['shop_country'];
			$data_arr['shop_address1'] = $input['shop_address1'];
			$data_arr['shop_address2'] = $input['shop_address2'];
			$data_arr['shop_city'] = $input['shop_city'];
			$data_arr['shop_state'] = $input['shop_state'];
			$data_arr['shop_zipcode'] = $input['shop_zipcode'];
			ShopDetails::whereRaw('user_id = ?', array($this->logged_user_id))->update($data_arr);
		}
		else
		{
			$data_arr['shop_country'] = $input['shop_country'];
			$data_arr['shop_address1'] = $input['shop_address1'];
			$data_arr['shop_address2'] = $input['shop_address2'];
			$data_arr['shop_city'] = $input['shop_city'];
			$data_arr['shop_state'] = $input['shop_state'];
			$data_arr['shop_zipcode'] = $input['shop_zipcode'];
			$data_arr['user_id'] = $this->logged_user_id;
			$shop = new ShopDetails;
			$shop->addNew($data_arr);
		}
	}



	public function updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath)
	{
		$return_arr = array();
		$config_path = Config::get('webshoppack.shop_cancellation_policy_folder');
		CUtil::chkAndCreateFolder($config_path);
		$file->move(Config::get("webshoppack.shop_cancellation_policy_folder"),$file_name.'.'.$file_ext);
		// open file a image resource
		//Image::make($file->getRealPath())->save(Config::get("webshoppack.shop_cancellation_policy_folder").$file_name.'_O.'.$file_ext);

		if($this->isShopAlreadyAdded())
		{
			$this->deleteShopCancellationPolicyFile();
		}

		$return_arr = array('file_ext' => $file_ext, 'file_name' => $file_name, 'file_server_url' => $destinationpath);
		return $return_arr;
	}
	public function deleteShopCancellationPolicyFile($id='', $folder_name = '')
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_obj = Products::initializeShops();
		$existing_file = $shop_obj->getShopDetails($this->logged_user_id);
		if(count($existing_file) > 0 && $existing_file['cancellation_policy_filename'] != '')
		{
			$filename = $existing_file['cancellation_policy_filename'];
			$ext = $existing_file['cancellation_policy_filetype'];

			$shop_obj->setShopOwnerId($this->logged_user_id);
			$shop_obj->setShopId($existing_file['id']);
			$shop_obj->setShopName($existing_file['shop_name']);
			$shop_obj->setShopUrlSlug($existing_file['url_slug']);

			$shop_obj->setCancellationPolicyFilename('');
			$shop_obj->setCancellationPolicyFiletype('');
			$shop_obj->setCancellationPolicyServerUrl('');

			$resp = $shop_obj->save();
			$respd = json_decode($resp, true);

			if ($respd['status'] == 'error') {
				return false;
			}
			if($folder_name == '')
				$folder_name = Config::get('webshoppack.shop_cancellation_policy_folder');

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



	public function updateShopBanner($input)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();

		$img_arr = array();
		if (Input::hasFile('shop_banner_image'))
		{
			$file = Input::file('shop_banner_image');
			$image_ext = $file->getClientOriginalExtension();
			$image_name = Str::random(20);
			$destinationpath = URL::asset(Config::get("webshoppack.shop_image_folder"));
			$img_arr = $this->updateBannerImage($file, $image_ext, $image_name, $destinationpath);
			if($this->isShopAlreadyAdded())
			{
				ShopDetails::whereRaw('user_id = ?', array($this->logged_user_id))->update($img_arr);
			}
			else
			{
				$shop = new ShopDetails;
				$shop->addNew($img_arr);
			}
		}
	}

	public function updateBannerImage($file, $image_ext, $image_name, $destinationpath)
	{
		$return_arr = array();
		$config_path = Config::get('webshoppack.shop_image_folder');
		CUtil::chkAndCreateFolder($config_path);

		// open file a image resource
		Image::make($file->getRealPath())->save(Config::get("webshoppack.shop_image_folder").$image_name.'_O.'.$image_ext);

		list($width,$height)= getimagesize($file);
		list($upload_img['width'], $upload_img['height']) = getimagesize(base_path().'/public/'.$config_path.$image_name.'_O.'.$image_ext);

		$thumb_width = Config::get("webshoppack.shop_image_thumb_width");
		$thumb_height = Config::get("webshoppack.shop_image_thumb_height");
		if(isset($thumb_width) && isset($thumb_height))
		{
			$timg_size = CUtil::DISP_IMAGE($thumb_width, $thumb_height, $upload_img['width'], $upload_img['height'], true);
			Image::make($file->getRealPath())
				->resize($thumb_width, $thumb_height, true, false)
				->save($config_path.$image_name.'_T.'.$image_ext);
		}

		$img_path = base_path().'/public/'.$config_path;
		list($upload_input['thumb_width'], $upload_input['thumb_height']) = getimagesize($img_path.$image_name.'_T.'.$image_ext);

		if($this->isShopAlreadyAdded())
		{
			$this->deleteExistingImageFiles();
		}

		$return_arr = array('image_ext' => $image_ext, 'image_name' => $image_name, 'image_server_url' => $destinationpath,
									't_width' => $upload_input['thumb_width'], 't_height' => $upload_input['thumb_height']);
		return $return_arr;
	}


	public function deleteExistingImageFiles()
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_obj = Products::initializeShops();
		$existing_images = $shop_obj->getShopDetails($this->logged_user_id);
		if(count($existing_images) > 0 && $existing_images['image_name'] != '')
		{
			$shop_obj->setShopOwnerId($this->logged_user_id);
			$shop_obj->setShopId($existing_images['id']);
			$shop_obj->setShopName($existing_images['shop_name']);
			$shop_obj->setShopUrlSlug($existing_images['url_slug']);
			$shop_obj->setShopImageName('');
			$shop_obj->setShopImageExtension('');
			$shop_obj->setShopImageServerUrl('');
			$shop_obj->setShopImageHeight('');
			$shop_obj->setShopImageWidth('');
			$shop_obj->setShopImageWidth('');
			$shop_obj->save();
			$this->deleteImageFiles($existing_images['image_name'], $existing_images['image_ext'], Config::get("webshoppack.shop_image_folder"));
		}
	}

	public function deleteShopImage($id, $filename, $ext, $folder_name)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_obj = Products::initializeShops();
		$existing_images = $shop_obj->getShopDetails($this->logged_user_id);
		if(count($existing_images) > 0 && $existing_images['image_name'] != '')
		{
			$shop_obj->setShopOwnerId($this->logged_user_id);
			$shop_obj->setShopId($existing_images['id']);
			$shop_obj->setShopName($existing_images['shop_name']);
			$shop_obj->setShopUrlSlug($existing_images['url_slug']);
			$shop_obj->setShopImageName('');
			$shop_obj->setShopImageExtension('');
			$shop_obj->setShopImageServerUrl('');
			$shop_obj->setShopImageHeight('');
			$shop_obj->setShopImageWidth('');
			$shop_obj->setShopImageWidth('');
			$resp = $shop_obj->save();
			$respd = json_decode($resp, true);
			if ($respd['status'] == 'error') {
				return false;
			}
			$this->deleteImageFiles($filename, $ext, $folder_name);
			return true;
		}
		return false;
	}

	public function deleteShopImageByAdmin($id, $filename, $ext, $folder_name, $user_id)
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$shop_obj = Products::initializeShops();
		$existing_images = $shop_obj->getShopDetails($user_id);
		if(count($existing_images) > 0 && $existing_images['image_name'] != '')
		{
			$shop_obj->setShopOwnerId($user_id);
			$shop_obj->setShopId($existing_images['id']);
			$shop_obj->setShopName($existing_images['shop_name']);
			$shop_obj->setShopUrlSlug($existing_images['url_slug']);
			$shop_obj->setShopImageName('');
			$shop_obj->setShopImageExtension('');
			$shop_obj->setShopImageServerUrl('');
			$shop_obj->setShopImageHeight('');
			$shop_obj->setShopImageWidth('');
			$shop_obj->setShopImageWidth('');
			$resp = $shop_obj->save();
			$respd = json_decode($resp, true);
			if ($respd['status'] == 'error') {
				return false;
			}
			$this->deleteImageFiles($filename, $ext, $folder_name);
			return true;
		}
		return false;
	}




	public function deleteImageFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_T.".$ext))
		{
			unlink($folder_name.$filename."_T.".$ext);
		}
		if (file_exists($folder_name.$filename."_O.".$ext))
		{
			unlink($folder_name.$filename."_O.".$ext);
		}
	}

	public static function getShopImage($shop_id, $image_size = "thumb", $shop_image_info = array(), $cache = true, $shop_obj)
	{
		$image_exists = false;
		$image_details = array();

		if(count($shop_image_info) == 0)
		{
			$shop_obj->setFilterShopId($shop_id);
			$shop_image_info = $shop_obj->getShopDetailsWithFilter();
		}
		if(count($shop_image_info) > 0 && $shop_image_info['image_name'] != '')
		{
			$image_exists = true;
			$image_details["image_id"] = $shop_image_info['id'];
			$image_details["image_ext"] = $shop_image_info['image_ext'];
			$image_details["image_name"] = $shop_image_info['image_name'];
			$image_details["image_server_url"] = $shop_image_info['image_server_url'];
			$image_details["image_thumb_width"] = $shop_image_info['t_width'];
			$image_details["image_thumb_height"] = $shop_image_info['t_height'];
			$image_details["image_folder"] = Config::get("webshoppack.shop_image_folder");
		}

		$image_path = "";
		$image_url = "";
		$image_attr = "";
		if($image_exists)
		{
			$image_path = URL::asset(Config::get("webshoppack.shop_image_folder"))."/";
		}
		$cfg_shop_img_thumb_width = Config::get("webshoppack.shop_image_thumb_width");
		$cfg_shop_img_thumb_height = Config::get("webshoppack.shop_image_thumb_height");

		switch($image_size)
		{
			case "thumb":

				$image_url = "";

				$image_attr = "";

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_T.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_shop_img_thumb_width, $cfg_shop_img_thumb_height, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
				}
				break;

			default:

				$image_url = "";
				$image_attr = "";

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_T.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE(90, 90, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
				}
		}
		$image_details['image_url'] = $image_url;
		$image_details['image_attr'] = $image_attr;
		return $image_details;
	}
	public function setSearchOrders($order_obj, $inputs = array())
	{

		if(isset($inputs['order_id']) && $inputs['order_id']!='')
		{
			$order_id = CUtil::getOrderId($inputs['order_id']);
			$order_obj->setFilterOrderId($order_id);
		}
		if(isset($inputs['buyer_user_code']) && $inputs['buyer_user_code']!='')
		{
			$buyer_id = CUtil::getUserId($inputs['buyer_user_code']);
			$order_obj->setFilterBuyerId($buyer_id);
		}
		if(isset($inputs['seller_user_code']) && $inputs['seller_user_code']!='')
		{
			$seller_id = CUtil::getUserId($inputs['seller_user_code']);
			$order_obj->setFilterSellerId($seller_id);
		}

		if(isset($inputs['order_status']) && $inputs['order_status']!='')
		{
			$order_obj->setFilterStaus($inputs['order_status']);
		}
		if(isset($inputs['from_date']) && $inputs['from_date']!='')
		{
			$order_obj->setFilterDateFrom($inputs['from_date']);
		}
		if(isset($inputs['to_date']) && $inputs['to_date']!='')
		{
			$order_obj->setFilterDateTo($inputs['to_date']);
		}
	}

}