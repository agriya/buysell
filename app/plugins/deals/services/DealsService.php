<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
//use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products, Image;
class DealsService
{
	public function getUrlSlugTargetText($given_text)
	{
		$target_text = strtolower($given_text);
		$target_text = trim($target_text);
		$target_text = preg_replace('/(\-){2,}/', '-', $target_text);
		$target_text = preg_replace('/[^a-z0-9\-]/', '', $target_text);
		$target_text = trim($target_text, '-');

		return $target_text;
	}

	public function addDealEntry($inputs = array())
	{
		$logged_user_id = \BasicCUtil::getLoggedUserId();

		$deal_id = isset($inputs['deal_id']) ? $inputs['deal_id'] : 0;
		$data_arr['user_id'] = $logged_user_id;
		$data_arr['deal_title'] = isset($inputs['deal_title']) ? $inputs['deal_title'] : '';
		$url_slug = isset($inputs['url_slug']) ? $inputs['url_slug'] : $this->getUrlSlugTargetText($inputs['deal_title']);
		$data_arr['url_slug'] = $url_slug;
		$data_arr['deal_short_description'] = isset($inputs['deal_short_description']) ? $inputs['deal_short_description'] : '';
		$data_arr['deal_description'] = isset($inputs['deal_description']) ? $inputs['deal_description'] : '';
		$data_arr['meta_title'] = isset($inputs['meta_title']) ? $inputs['meta_title'] : '';
		$data_arr['meta_keyword'] = isset($inputs['meta_keyword']) ? $inputs['meta_keyword'] : '';
		$data_arr['meta_description'] = isset($inputs['meta_description']) ? $inputs['meta_description'] : '';
		$data_arr['discount_percentage'] = isset($inputs['discount_percentage']) ? $inputs['discount_percentage'] : '';
		$data_arr['date_deal_from'] = isset($inputs['date_deal_from']) ? $inputs['date_deal_from'] : '';
		$data_arr['date_deal_to'] = isset($inputs['date_deal_to']) ? $inputs['date_deal_to'] : '';
		$data_arr['applicable_for'] = isset($inputs['applicable_for']) ? $inputs['applicable_for'] : '';

		$tipping_apply_single_item = 0;
		if(\Config::has("plugin.deals_tipping_only_for_single_item") && \Config::get("plugin.deals_tipping_only_for_single_item"))
			$tipping_apply_single_item = 1;

		if($tipping_apply_single_item  == 0)
		{
			$data_arr['tipping_qty_for_deal'] = isset($inputs['tipping_qty_for_deal']) ? $inputs['tipping_qty_for_deal'] : 0;
		}
		else
		{
			$data_arr['tipping_qty_for_deal'] = ($inputs['applicable_for'] == 'single_item') ? $inputs['tipping_qty_for_deal'] : 0;
		}

		$data_arr['listing_fee_paid'] = isset($inputs['listing_fee_paid']) ? $inputs['listing_fee_paid'] : '';

		if(\Config::has("plugin.deal_auto_approval") && \Config::get("plugin.deal_auto_approval"))
			$data_arr['deal_status'] = 'active';
		else
			$data_arr['deal_status'] = 'to_activate';

		$data_arr['date_added'] = DB::raw('NOW()');
		$data_arr['listing_fee_paid'] = 'No';


		$file_info = array();
		$file = Input::file('deal_image');
		$upload_file_name = $file->getClientOriginalName();

		$upload_status = $this->uploadMediaFile('deal_image', $file_info);

		if ($upload_status['status'] == 'success')
		{
			$data_arr['img_name'] = $file_info['filename_no_ext'];
			$data_arr['img_ext'] = $file_info['ext'];
			$data_arr['img_width'] = $file_info['width'];
			$data_arr['img_height'] = $file_info['height'];
			$data_arr['l_width'] = $file_info['l_width'];
			$data_arr['l_height'] = $file_info['l_height'];
			$data_arr['t_width'] = $file_info['t_width'];
			$data_arr['t_height'] = $file_info['t_height'];
			$data_arr['server_url'] = $file_info['server_url'];
		}

		$deal_id = DB::table('deal')->insertGetId($data_arr);

		// Insert into deal items related changes
		$selItemIds = array();
		if($inputs['applicable_for'] == 'single_item')
		{
			$data_arr['item_ids']	=  $inputs['deal_item'];
			$selItemIds[]			= $inputs['deal_item'];
		}
		elseif($inputs['applicable_for'] == 'selected_items')
		{
			$itemIds	= $inputs['selected_items_list'];
			foreach($itemIds as $ids)
			{
				$selItemIds[] = $ids;
			}
		}
		if(count($selItemIds) > 0)
		{
			foreach($selItemIds AS $itemid)
			{
				$deal_item_id = DB::table('deal_item')->insertGetId(array('deal_id' => $deal_id, 'item_id' => $itemid));
			}
		}

		// Add email functionality for notification to the admin regarding the deal added details.
		$mail_det_arr = array();

		$mail_det_arr['deal_id'] 					= $deal_id;
		$mail_det_arr['deal_title'] 				= $data_arr['deal_title'];
		$mail_det_arr['url_slug'] 					= $data_arr['url_slug'];
		//$mail_det_arr['viewDealLink'] 				= URL::to('deals/view-deal/'.$data_arr['url_slug']);
		$mail_det_arr['viewDealLink'] 				= URL::to('admin/deals/manage-deals').'?deal_id_from='.$deal_id.'&deal_id_to='.$deal_id.'&search_deal=search_deal';
		$mail_det_arr['deal_added_by'] 				= \CUtil::getUserDetails($logged_user_id);
		$mail_det_arr['deal_user_profile_link']		= URL::to('admin/users/user-details').'/'.$logged_user_id;
		$mail_det_arr['deal_added_date'] 			= date('Y-m-d H:m:s');
		$mail_det_arr['deal_discount_percentage'] 	= $data_arr['discount_percentage'];
		$mail_det_arr['to_email'] 					= \Config::get("generalConfig.invoice_email");
		$mail_det_arr['status_msg'] 				= (Lang::has('deals::deals.DEAL_STATUS_'.strtoupper($data_arr['deal_status']))) ? Lang::get('deals::deals.DEAL_STATUS_'.strtoupper($data_arr['deal_status'])): str_replace('_', ' ', $data_arr['deal_status']);
		if($data_arr['deal_status'] == 'to_activate')
			$mail_det_arr['admin_msg']				= Lang::get('deals::deals.deal_added_pending_approval_mail_sub');
		else
			$mail_det_arr['admin_msg']				= Lang::get('deals::deals.deal_added_success_mail_sub');;

		# Send mail to admin regarding deal listing fee paid
		$template = "deals::emails.dealAddedNotificationToAdmin";
		try {
			\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr) {
				$m->to(\Config::get("generalConfig.invoice_email"));
				$subject = Lang::get('deals::deals.deal_added_notify_mail_sub');
				$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
				$m->subject($subject);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}

		return $deal_id;
	}

	public function updateDealEntry($inputs)
	{
		$logged_user_id = \BasicCUtil::getLoggedUserId();

		$deal_id = $inputs['deal_id'];
		$data_arr['deal_title'] = $inputs['deal_title'];
		$data_arr['deal_short_description'] = $inputs['deal_short_description'];
		$data_arr['deal_description'] = $inputs['deal_description'];
		$data_arr['meta_title'] = isset($inputs['meta_title']) ? $inputs['meta_title'] : '';
		$data_arr['meta_keyword'] = isset($inputs['meta_keyword']) ? $inputs['meta_keyword'] : '';
		$data_arr['meta_description'] = isset($inputs['meta_description']) ? $inputs['meta_description'] : '';
		$data_arr['discount_percentage'] = $inputs['discount_percentage'];
		$data_arr['date_deal_from'] = $inputs['date_deal_from'];
		$data_arr['date_deal_to'] = $inputs['date_deal_to'];
		$data_arr['applicable_for'] = $inputs['applicable_for'];

		$tipping_apply_single_item = 0;
		if(\Config::has("plugin.deals_tipping_only_for_single_item") && \Config::get("plugin.deals_tipping_only_for_single_item"))
			$tipping_apply_single_item = 1;

		if($tipping_apply_single_item  == 0)
		{
			$data_arr['tipping_qty_for_deal'] = isset($inputs['tipping_qty_for_deal']) ? $inputs['tipping_qty_for_deal'] : 0;
		}
		else
		{
			$data_arr['tipping_qty_for_deal'] = ($inputs['applicable_for'] == 'single_item') ? $inputs['tipping_qty_for_deal'] : 0;
		}

		if(!empty($_FILES['deal_image']['name']))
		{
			$this->removeDealImageEntry($inputs['deal_id']);
			$file_info = array();
			$file = Input::file('deal_image');
			$upload_file_name = $file->getClientOriginalName();
			$upload_status = $this->uploadMediaFile('deal_image', $file_info);
			if ($upload_status['status'] == 'success')
			{
				$data_arr['img_name'] = $file_info['filename_no_ext'];
				$data_arr['img_ext'] = $file_info['ext'];
				$data_arr['img_width'] = $file_info['width'];
				$data_arr['img_height'] = $file_info['height'];
				$data_arr['l_width'] = $file_info['l_width'];
				$data_arr['l_height'] = $file_info['l_height'];
				$data_arr['t_width'] = $file_info['t_width'];
				$data_arr['t_height'] = $file_info['t_height'];
				$data_arr['server_url'] = $file_info['server_url'];
			}
		}
		DB::table('deal')->whereRaw('deal_id = ?', array($deal_id))->update($data_arr);

		// Insert into deal items related changes
		$selItemIds = array();
		if($inputs['applicable_for'] == 'single_item')
		{
			$data_arr['item_ids']	=  $inputs['deal_item'];
			$selItemIds[]			= $inputs['deal_item'];
		}
		elseif($inputs['applicable_for'] == 'selected_items')
		{
			$itemIds	= $inputs['selected_items_list'];
			foreach($itemIds as $ids)
			{
				$selItemIds[] = $ids;
			}
		}

		switch($inputs['applicable_for'])
		{
			case 'all_item':
				$this->removeDealItemsEntry($inputs['deal_id']);
				break;

			case'single_item':
			case 'selected_items':
				$existing_items = $this->fetchAssignedItemsList($inputs['deal_id']);
				$new_items		= $selItemIds;

				$add_items_arr = array();
				$add_items_arr = array_diff($new_items, $existing_items);
				// Add new item entry if exist
				if(COUNT($add_items_arr) > 0)
				{
					$item_data_arr = array();
					$item_data_arr['deal_id'] = $inputs['deal_id'];
					$item_data_arr['item_ids'] = $add_items_arr;
					$this->addDealItemListEntry($item_data_arr);
				}

				$remove_items_arr = array();
				$remove_items_arr = array_diff($existing_items, $new_items);
				// Remove unwanted entry if exist
				if(COUNT($remove_items_arr) > 0)
				{
					$item_data_arr = array();
					$item_data_arr['deal_id'] = $inputs['deal_id'];
					$item_data_arr['item_ids'] = $remove_items_arr;
					$this->removeDealItemListEntry($item_data_arr);
				}
				break;
		}
	}

	public function removeDealItemsEntry($deal_id)
	{
		DB::table('deal_item')->whereRaw('deal_id = ?', array($deal_id))->delete();
	}

	public function addDealItemListEntry($data_arr)
	{
		if(isset($data_arr['item_ids']) && COUNT($data_arr['item_ids']) > 0)
		{
			foreach($data_arr['item_ids'] AS $selIds)
			{
				$deal_item_id = DB::table('deal_item')->insertGetId(array('deal_id' => $data_arr['deal_id'], 'item_id' => $selIds));
			}
		}
	}

	public function removeDealItemListEntry($data_arr)
	{
		if(isset($data_arr['item_ids']) && COUNT($data_arr['item_ids']) > 0)
		{
			foreach($data_arr['item_ids'] AS $selIds)
			{
				DB::table('deal_item')->whereRaw('deal_id = ? AND item_id = ?', array($data_arr['deal_id'], $selIds))->delete();
			}
		}
	}


	public function removeDealImageEntry($deal_id)
	{
		$deal_details = DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->first();
		if(count($deal_details) > 0)
		{
			$folder_name = \Config::get("plugin.deal_img_folder");
			$filename = $deal_details->img_name;
			$ext = $deal_details->img_ext;

			if (file_exists($folder_name.$filename.".".$ext))
				unlink($folder_name.$filename.".".$ext);
			if (file_exists($folder_name.$filename."_L.".$ext))
				unlink($folder_name.$filename."_L.".$ext);
			if (file_exists($folder_name.$filename."_T.".$ext))
				unlink($folder_name.$filename."_T.".$ext);
		}
	}

	public static function getDealDefaultThumbImage($deal_id, $image_size = "thumb", $d_image_info = array())
	{
		$image_exists = false;
		$image_details = array();
		$image_title = Lang::get('deals::deals.no_image');
		$no_image = true;
		$config_folder = \Config::get("plugin.deal_img_folder");
		$image_details["image_folder"] 	= $config_folder;

		if(count($d_image_info) > 0 && isset($d_image_info['img_name']) && $d_image_info['img_name'] != '' )
		{
			switch($image_size)
			{
				case 'default':
					$image_exists = true;
					$image_details["default_img"] = $d_image_info['img_name'];
					$image_details["default_ext"] = $d_image_info['img_ext'];
					$image_details["default_width"] = $d_image_info['img_width'];
					$image_details["default_height"] = $d_image_info['img_height'];
					break;

				case 'large':
					$image_exists = true;
					$image_details["thumbnail_img"] = $d_image_info['img_name'];
					$image_details["thumbnail_ext"] = $d_image_info['img_ext'];
					$image_details["thumbnail_width"] = $d_image_info['l_width'];
					$image_details["thumbnail_height"] = $d_image_info['l_height'];
					break;

				case "thumb":
					$image_exists = true;
					$image_details["thumbnail_img"] = $d_image_info['img_name'];
					$image_details["thumbnail_ext"] = $d_image_info['img_ext'];
					$image_details["thumbnail_width"] = $d_image_info['t_width'];
					$image_details["thumbnail_height"] = $d_image_info['t_height'];
					break;
			}
		}

		$image_path = $image_url = $image_attr = "";
		if($image_exists)
		{
			$image_path = URL::asset($config_folder)."/";
		}

		$cfg_deal_img_large_width = \Config::get("plugin.deal_img_large_width");
		$cfg_deal_img_large_height = \Config::get("plugin.deal_img_large_height");
		$cfg_deal_img_thumb_width = \Config::get("plugin.deal_img_thumb_width");
		$cfg_deal_img_thumb_height = \Config::get("plugin.deal_img_thumb_height");

		switch($image_size)
		{
			case 'default':
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
				$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_large_width, $cfg_deal_img_large_height, $cfg_deal_img_large_width, $cfg_deal_img_large_height);
				if($image_exists)
				{
					$image_url =  $image_path . $image_details["default_img"].".".$image_details["default_ext"];
					$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_large_width, $cfg_deal_img_large_height, $image_details["default_width"], $image_details["default_height"]);
					$image_title = $d_image_info["deal_title"];
					$no_image = false;
				}
				break;

			case 'large':
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_large_no_image");
				$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_large_width, $cfg_deal_img_large_height, $cfg_deal_img_large_width, $cfg_deal_img_large_height);
				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."L.".$image_details["thumbnail_ext"];
					$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_large_width, $cfg_deal_img_large_height, $image_details["thumbnail_width"], $image_details["thumbnail_height"]);
					$image_title = $d_image_info["deal_title"];
					$no_image = false;
				}
				break;

			case "thumb":
				$image_url = URL::asset("images/no_image").'/'.Config::get("webshoppack.photos_thumb_no_image");
				$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_thumb_width, $cfg_deal_img_thumb_height, $cfg_deal_img_thumb_width, $cfg_deal_img_thumb_height);
				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					$image_attr = \BasicCUtil::TPL_DISP_IMAGE($cfg_deal_img_thumb_width, $cfg_deal_img_thumb_height, $image_details["thumbnail_width"], $image_details["thumbnail_height"]);
					$image_title = $d_image_info["deal_title"];
					$no_image = false;
				}
				break;

			default:
				$image_url = URL::asset("images/no_image").'/prodnoimage-215x170.jpg';
				$image_attr = \BasicCUtil::TPL_DISP_IMAGE(215, 170, 215, 170);
				if($image_exists)
				{
					$image_url =  $image_path . $image_details["thumbnail_img"]."T.".$image_details["thumbnail_ext"];
					$image_attr = \BasicCUtil::TPL_DISP_IMAGE(215, 170, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
					$image_title = $d_image_info["deal_title"];
					$no_image = false;
				}
		}
		$image_details['image_url'] = $image_url;
		$image_details['image_attr'] = $image_attr;
		$image_details['title'] = $image_title;
		$image_details['no_image'] = $no_image;
		return $image_details;
	}


	public function dealExpiryDetails($from_date, $to_date)
	{
		$resp_arr = array();
		if($from_date != 0 || $from_date != '' || $to_date != 0 || $to_date != '')
		{
			$deal_from_date = strtotime($from_date);
			$deal_end_date = strtotime($to_date);
			$curr_date = strtotime(date('Y-m-d'));
			$resp_arr['expired'] = 1;
			$resp_arr['label'] =  Lang::get('deals::deals.expired_on_lbl');
			if($deal_end_date >= $curr_date && $deal_from_date <= $curr_date)
			{
				$resp_arr['expired'] = 0;
				$resp_arr['label'] = Lang::get('deals::deals.expires_on_lbl');
			}
		}
		return $resp_arr;
	}


	public function getDealViewUrl($details)
	{
		$view_url = '';
		if(COUNT($details) > 0 && isset($details->url_slug))
			$view_url = URL::to('deals/view-deal/'.$details->url_slug);
		return $view_url;
	}

	public function getDealUrl($deal_id)
	{
		$view_url = '';
		$deal_slug = DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->pluck('url_slug');
		$view_url = URL::to('deals/view-deal/'.$deal_slug);
		return $view_url;
	}

	public function uploadMediaFile($file_ctrl_name, &$file_info)
	{
		if (!isset($_FILES[$file_ctrl_name]))
			return array('status'=>'error', 'error_message' => Lang::get('deals::deals.file_none_err_msg'));

		// default settings
		$file_original = $file_thumb = $file_large = $server_url = '';
		$width = $height = $t_width = $t_height = $l_width = $l_height = $s_width = $s_height = 0;

		$file = Input::file('deal_image');
		$file_size = $file->getClientSize();
		if($file_size == 0)
		{
			return array('status'=>'error', 'error_message' => Lang::get('deals::deals.invalid_file_size_err_msg'));
		}

		$upload_file_name = $file->getClientOriginalName();
		$ext_index = strrpos($upload_file_name, '.') + 1;
		$ext = substr($upload_file_name, $ext_index, strlen($upload_file_name));
		$title = substr($upload_file_name, 0, $ext_index - 1);
		$filename_no_ext = uniqid(); // generate filename
		//$file = $filename_no_ext . '.' . $ext;
		$allowed_size = \Config::get('plugin.deal_img_max_size') * 1024 * 1024;
		if (!($file_size  <= $allowed_size))// size in MB
		{
			return array('status'=>'error', 'error_message' => Lang::get('deals::deals.invalid_file_size_err_msg'));
		}

		$file_path = \Config::get("plugin.deal_img_folder");
		$server_url = URL::asset($file_path);
		$file_original  = $filename_no_ext . '.' . $ext;
		$file_thumb = $filename_no_ext . 'T.' . $ext;
		$file_large = $filename_no_ext . 'L.' . $ext;

		$this->chkAndCreateFolder($file_path);

		@chmod($file_original, 0777);
		@chmod($file_thumb, 0777);
		@chmod($file_large, 0777);

		try {
			Image::make($file->getRealPath())->save($file_path.$file_original);
			//Resize original image for large image
			Image::make($file->getRealPath())
				->resize(Config::get("plugin.deal_img_large_width"), Config::get("plugin.deal_img_large_height"), true, false)
				->save($file_path.$file_large);
			 //Resize original image for thump image
			Image::make($file->getRealPath())
				->resize(Config::get("plugin.deal_img_thumb_width"), Config::get("plugin.deal_img_thumb_height"), true, false)
				->save($file_path.$file_thumb);
		}
		catch(\Exception $e){
			return array('status'=>'error','error_message' => $e->getMessage());
		}

		list($width, $height) 		= getimagesize($file_path . $file_original);
		list($l_width, $l_height) 	= getimagesize($file_path . $file_large);
		list($t_width, $t_height) 	= getimagesize($file_path . $file_thumb);

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
			'server_url'		=> $server_url);

		 return array('status'=>'success');
	}

	public function getDealsDetails($deal_id, $user_id=0)
	{
		$deal_details = DB::table('deal')->whereRaw('deal_id = ?', array($deal_id));
		if($user_id)
		{
			$deal_details->where('user_id', $user_id);
		}
		$deal_details = $deal_details->first();

		if(count($deal_details) > 0)
			return $deal_details;
		else
			return false;
	}

	public function chkAndCreateFolder($folderName)
	{
		$folder_arr = explode('/', $folderName);
		$folderName = '';
		foreach($folder_arr as $key=>$value)
		{
			$folderName .= $value.'/';
			if($value == '..' or $value == '.')
				continue;
			if (!is_dir($folderName))
			{
				mkdir($folderName);
				@chmod($folderName, 0777);
			}
		}
	}

	public function getMyDeals($user_id, $return_type = 'get', $limit = 10)
	{
		$deals = DB::table('deal')->Where('user_id', $user_id);
		$deals->orderBy('deal.deal_id', 'desc');
		if($return_type == 'paginate')
			return $deals->paginate($limit);
		else
			return $deals->get();
	}

	public function getMyFeaturedRequests($user_id, $return_type = 'get', $limit = 10)
	{
		$deals = DB::table('deal');
			$deals->select('deal_featured_request.deal_id', 'url_slug', 'deal_status', 'deal_title', 'deal.user_id', 'discount_percentage',
									 'date_deal_from', 'date_deal_to', 'deal_featured_request.date_featured_from', 'deal_featured_request.date_added',
									 'deal_featured_request.date_featured_to', 'deal_featured_request.request_status',
									 'deal_featured_request.deal_featured_days',
									 'deal_featured_request.date_added AS date_requested', 'deal_featured_request.admin_comment');
			$deals->join('deal_featured_request', 'deal.deal_id', '=', 'deal_featured_request.deal_id');
			$deals->Where('deal.user_id', $user_id);
			$deals->orderBy('deal_featured_request.date_added', 'desc');

		if($return_type == 'paginate')
			return $deals->paginate($limit);
		else
			return $deals->get();
	}


	public function getDealList($list_type, $return_type = 'get', $limit = 10)
	{
		$deals = DB::table('deal');
		switch($list_type)
		{
			case 'new-deals':
			case 'new':
				$deals->where('deal.deal_status', 'active');
				$deals->whereRaw('deal.deal_status <> \'to_activate\' AND deal.deal_status <> \'deactivated\' ');
				$deals->whereRaw('( CURRENT_DATE( ) >= deal.date_deal_from AND CURRENT_DATE( ) <= deal.date_deal_to ) '.' AND ( CURRENT_DATE( ) >= deal.date_deal_from AND CURRENT_DATE( ) <= deal.date_deal_to ) ');
				$deals->orderBy('deal.deal_id', 'desc');
				break;

			case 'expiring':
				$expiring_soon_days = \Config::get('plugin.deal_expiring_soon_days', 0);
				$deals->where('deal.deal_status', 'active');
				$deals->whereRaw(' ( CURRENT_DATE( ) >= date_deal_from AND CURRENT_DATE( ) <= date_deal_to ) ');
				if($expiring_soon_days > 0)
					$deals->whereRaw(' DATE_SUB( deal.date_deal_to, INTERVAL '.$expiring_soon_days.' DAY ) <= CURDATE( ) ');
				$deals->orderBy('deal.date_deal_to', 'desc');
				break;

			case 'expired':
				$deals->whereRaw('deal.deal_status <> \'to_activate\' AND deal.deal_status <> \'deactivated\' ');
				$deals->whereRaw('( deal.deal_status = \'expired\' OR DATE_FORMAT(date_deal_to, \'%Y-%m-%d\') < CURDATE() )');
				$deals->orderBy('deal.date_deal_to', 'desc');
				break;
		}
		if($return_type == 'paginate')
			return $deals->paginate($limit);
		else
			return $deals->get();
	}

	public function getDealIdBySlug($slug)
	{
		$deal_id = DB::table('deal')->whereRaw('url_slug = ?', array($slug))->pluck('deal_id');

		return $deal_id;
	}

	public function chkIsValidDealId($deal_id, $user_id=0)
    {
        $deal_id = is_numeric($deal_id) ? $deal_id : 0;
        if (!$deal_id)
            return false;

		$deal_details = DB::table('deal')->whereRaw('deal_id = ? AND deal_status != \'to_activate\' AND deal_status != \'deactivated\' ', array($deal_id));
		if($user_id)
		{
			$deal_details->where('user_id', $user_id);
		}
		$deal_details = $deal_details->first();
		if(count($deal_details) > 0)
			return true;
		else
			return false;
    }

    public function chkIsValidDealIdForView($deal_id, $user_id)
    {
    	$deal_id = is_numeric($deal_id) ? $deal_id : 0;
    	$user_id = is_numeric($deal_id) ? $user_id : 0;
        if (!$deal_id)
            return false;

        $deal_details = DB::table('deal');
        if(CUtil::isAdmin())
        {
        	$deal_details = $deal_details->whereRaw('deal_id = ?', array($deal_id));
		}
		else
		{
			$deal_details = $deal_details->whereRaw('deal_id = ? AND ( user_id = ? OR deal_status != \'to_activate\' AND deal_status != \'deactivated\' )', array($deal_id, $user_id));
		}

		$deal_details = $deal_details->first();
		if(count($deal_details) > 0)
			return true;
		else
			return false;
	}

	public function chkIsAllowToCloseDeal($deal, $purchase_count=0)
	{
		if(COUNT($deal) > 0)
		{
			$logged_user_id = \BasicCUtil::getLoggedUserId();
			if($deal->user_id != $logged_user_id || $deal->deal_status != 'active')
				return false;

			if(($deal->tipping_qty_for_deal == 0) || ($purchase_count >= $deal->tipping_qty_for_deal)) // || $deal->deal_tipping_status == 'tipping_reached'
				return true;

			return false;
		}
		return false;
	}

	public function chkIsAndCloseDealByUser($deal_id, $user_id)
	{
		$resp = array();
		$deal_details = DB::table('deal');
		$deal = $deal_details->whereRaw('deal_id = ? AND user_id = ? AND deal_status = \'active\'', array($deal_id, $user_id))->first();
		if(COUNT($deal) > 0)
		{
			$purchase_count = $this->getDealPurchasedCountById($deal_id);
			if(($deal->tipping_qty_for_deal == 0) || ($purchase_count >= $deal->tipping_qty_for_deal))
			{
				DB::table('deal')->whereRaw('deal_id = ?', array($deal_id))->update(array('deal_status' => 'closed'));
				$this->unFeatureDealByAdmin($deal_id); // Remove deal feature entry.
				// If any featured request avail then set as unapproved.
				$this->removeFeatureRequestEntry($deal_id, "Deal closed by seller request");
				$resp['success_msg'] = Lang::get('deals::deals.deal_closed_success_msg');
			}
			else
			{
				$resp['error_msg'] = Lang::get('deals::deals.invalid_action_msg');
			}
		}
		else
		{
			$resp['error_msg'] = Lang::get('deals::deals.invalid_deal_id_msg');
		}

		return $resp;
	}

	public function removeFeatureRequestEntry($deal_id, $admin_comment)
	{
		$update_arr = array();
		$update_arr['request_status'] = 'un_approved';
		$update_arr['admin_comment'] = $admin_comment;
		DB::table('deal_featured_request')->whereRaw('deal_id = ? AND request_status = \'pending_for_approval\'', array($deal_id))->update($update_arr);
	}

	public function chkIsValidDealEditAccess($deal_id, $user_id)
	{
		$respDet = array();
		$deal_id = is_numeric($deal_id) ? $deal_id : 0;
    	$user_id = is_numeric($deal_id) ? $user_id : 0;

		// Check is deal id is not empty
		if($deal_id == '' && $deal_id == 0)
		{
			$respDet['err_msg'] = Lang::get('deals::deals.invalid_deal_id_msg');
			$respDet['status'] = 'error';
			return $respDet;
		}
		$deal_details = DB::table('deal')->whereRaw('deal_id = ? AND user_id = ? AND deal_status != \'deactivated\' AND deal_status != \'expired\' AND deal_status != \'closed\'', array($deal_id, $user_id))->first();

		if(COUNT($deal_details) == 0)
		{
			$respDet['err_msg'] = Lang::get('deals::deals.invalid_deal_id_msg');
			$respDet['status'] = 'error';
			return $respDet;
		}

		// Check it already purchase made and tipping started
		if($deal_details->tipping_qty_for_deal > 0 && $this->getDealPurchasedCountById($deal_id) > 0)
		{
			$respDet['err_msg'] = Lang::get('deals::deals.invalid_edit_access_msg');
			$respDet['status'] = 'error';
			return $respDet;
		}
		$respDet['status'] = 'success';
		$respDet['deal_details'] = $deal_details;

		return $respDet;
	}

	public function chkIsValidFeaturedRequest($deal_id)
	{
		$deal_details = DB::table('deal_featured_request')
			->whereRaw('deal_id = ? AND ( request_status = \'pending_for_approval\' OR ( CURRENT_DATE( ) >= date_featured_from AND CURRENT_DATE( ) <= date_featured_to AND request_status = \'approved\' )) ', array($deal_id))->first();
		if(COUNT($deal_details))
			return false;
		return true;
	}

	public function chkIsFeaturedDeal($deal_id)
	{
		$det = DB::table('deal_featured')->whereRaw('deal_id = ? AND CURRENT_DATE( ) >= date_featured_from AND CURRENT_DATE( ) <= date_featured_to', array($deal_id))->pluck('deal_featured_id');
		return $det;
	}

	public function getDealPurchasedCountById($deal_id=0)
	{
		$cnt= 0;
		$cnt = DB::table('deal_item_purchased_details')->where('deal_id', $deal_id)->select(DB::raw('sum(qty) as cnt'))->groupBy('deal_id')->pluck('cnt');
		return $cnt;
	}

	public function getDealPurchasedCount()
	{
		$item_details = DB::table('deal_item_purchased_details')->select(DB::raw('sum(qty) as cnt, deal_id'))->groupBy('deal_id')->lists('cnt', 'deal_id');
		return $item_details;
	}


	public function fetchAssignedItemsList($deal_id)
	{
		$assignedItems = array();
		$assignedItems = DB::table('deal_item')->Where('deal_id', $deal_id)->lists('item_id','item_id');
		return $assignedItems;
	}


	public function getFeaturedDeal()
	{
		$deal_details = DB::table('deal')
							->join('deal_featured', 'deal.deal_id', '=', 'deal_featured.deal_id')
							->whereRaw(' ( CURRENT_DATE( ) >= deal_featured.date_featured_from AND CURRENT_DATE( ) <= deal_featured.date_featured_to )  AND ( CURRENT_DATE( ) >= deal.date_deal_from AND CURRENT_DATE( ) <= deal.date_deal_to ) ')
							->first();
		return $deal_details;
	}

	public function populateRecentDealsList()
	{
		$deals = DB::table('deal');
		$deals->select(DB::raw('DISTINCT(deal.deal_id), deal_title, deal_short_description, deal_description, user_id, img_name, img_ext, img_width, img_height, l_width, l_height, t_width, t_height, server_url, discount_percentage, date_deal_from, date_deal_to, applicable_for, tipping_qty_for_deal, date_added, listing_fee_paid, url_slug, date_deal_to'));
		$deals->where('deal.deal_status', 'active');
		$deals->whereRaw(' ( CURRENT_DATE( ) >= deal.date_deal_from AND CURRENT_DATE( ) <= deal.date_deal_to) ');
		$deals->whereRaw('deal.deal_status <> \'to_activate\' AND deal.deal_status <> \'deactivated\' ');
		$deals->orderBy('deal.deal_id', 'desc')->take(10);

		return $deals->get();
	}

	public function populateMoreDealsFromShop($user_id, $deal_id=0)
	{
		$deals = DB::table('deal');
		$deals->select(DB::raw('DISTINCT(deal.deal_id), deal_title, deal_short_description, deal_description, user_id, img_name, img_ext, img_width, img_height, l_width, l_height, t_width, t_height, server_url, discount_percentage, date_deal_from, date_deal_to, applicable_for, tipping_qty_for_deal, date_added, listing_fee_paid, url_slug, date_deal_to'));
		$deals->where('deal.deal_status', 'active');
		$deals->where('deal.user_id', $user_id);
		$deals->where('deal.deal_id', '<>', $deal_id);
		$deals->whereRaw('deal.deal_status <> \'to_activate\' AND deal.deal_status <> \'deactivated\' ');
		$deals->orderBy('deal.deal_id', 'desc')->take(6);

		return $deals->get();
	}

	public function getDealPurchasedDetails($deal_id, $user_id=0, $return_type = 'get', $limit = 10)
	{
		$rec = DB::table('shop_order');
		$rec->select('shop_order_item.item_id', 'shop_order_item.buyer_id', 'shop_order.payment_status', 'shop_order.order_status', 'shop_order.date_created', 'shop_order.id', 'shop_order_item.item_qty', 'shop_order_item.order_id');
		$rec->join('shop_order_item', 'shop_order.id', '=', 'shop_order_item.order_id');
		if($user_id > 0)
			$rec->where('shop_order.seller_id', $user_id);
		$rec->where('shop_order_item.deal_id', $deal_id);
		$rec->where('shop_order.order_status', 'payment_completed');
		$rec->orderBy('shop_order.date_created', 'desc');

		if($return_type=='get')
			return $rec->get();
		else
			return $rec->paginate($limit);

	}

	public function chkValidMyDealId($deal_id, $user_id)
	{
		if (!$deal_id)
            return false;

		$deal_details = DB::table('deal')->whereRaw('deal_id = ? AND deal_status != \'expired\' AND deal_status != \'deactivated\' AND user_id = ? ', array($deal_id, $user_id))->first();

		if(count($deal_details) > 0)
			return true;
		else
			return false;
	}

	public function chkIsValidAccessForSetFeatured($deal_id, $user_id)
	{
		$respDet = array();
		// Check is deal id is not empty
		if($deal_id == '' && $deal_id == 0)
		{
			$respDet['err_msg'] = Lang::get('deals::deals.invalid_deal_id_msg');
			$respDet['status'] = 'error';
			return $respDet;
		}
		// check is valid my deal id
		if(!$this->chkValidMyDealId($deal_id, $user_id))
 		{
		 	$respDet['err_msg'] = Lang::get('deals::deals.invalid_deal_id_msg');
			$respDet['status'] = 'error';
			return $respDet;
		}
		// Check is request for set featured already exist on this deal
		if($this->chkIsValidFeaturedRequest($deal_id))
		{
			$respDet['err_msg'] = '';
			$respDet['status'] = 'success';
			return $respDet;
		}
		else
		{
			$mydealLink =  URL::to('deals/my-featured-request');
			$link_URL = '<a href='.$mydealLink.'>Click here</a>';
			$errMsg = str_replace('VAR_LISTING', $link_URL, Lang::get('deals::deals.featured_request_exist_err_msg'));
			$respDet['err_msg'] = $errMsg;
			$respDet['status'] = 'error';
			return $respDet;
		}
	}


	public function payDealListFeeFromCredit($data_arr)
	{
		// if listing fee config variable set and listing fee is greater then 0 then only listing fee process related changes will be done.
		if(\Config::has('plugin.deal_listing_fee') && \Config::get('plugin.deal_listing_fee') > 0 && isset($data_arr['listing_fee']) && $data_arr['listing_fee'] > 0 )
		{
			# 1. Update users account balance
			$credit_obj = \Credits::initialize();
			//debit from buyer
			$credit_obj->setUserId($data_arr['user_id']);
			$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
			$credit_obj->setAmount($data_arr['listing_fee']);
			$credit_obj->creditAndDebit('amount', 'minus');
			//Credit to buyer
			$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
			$credit_obj->setCurrency(Config::get('generalConfig.site_default_currency'));
			$credit_obj->setAmount($data_arr['listing_fee']);
			$credit_obj->creditAndDebit('amount', 'plus');

			# 2. Add site transaction entry
			$trans_obj = new \SiteTransactionHandlerService();
			$transaction_arr['date_added'] = new \DateTime;
			$transaction_arr['user_id'] = $data_arr['user_id'];
			$transaction_arr['transaction_type'] = 'debit';
			$transaction_arr['amount'] = $data_arr['listing_fee'];
			$transaction_arr['currency'] = \Config::get('generalConfig.site_default_currency');
			$transaction_arr['transaction_key'] = 'deal_listfee_paid';
			$transaction_arr['reference_content_table'] = 'deal';
			$transaction_arr['reference_content_id'] = $data_arr['deal_id']; // deal_id
			$transaction_arr['status'] = 'completed';
			$transaction_arr['related_transaction_id'] = '';
			$transaction_arr['payment_type'] = 'wallet';
			$transaction_arr['transaction_notes'] = 'Deal listing fee paid from account balance';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			$transaction_arr['user_id'] = Config::get('generalConfig.admin_id');
			$transaction_arr['transaction_type'] = 'credit';
			$transaction_arr['transaction_notes'] = 'Credited deal listing fee to wallet for listed deal id: '.$data_arr['deal_id'];
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			# Update listing fee paid status as Yes in deal
			DB::table('deal')->whereRaw('deal_id = ?', array($data_arr['deal_id']))->update(array('listing_fee_paid' => 'Yes'));

			$dealDet = $this->fetchDealDetailsById($data_arr['deal_id']);

			# Send mail to the admin
			$mail_det_arr = array();
			$mail_det_arr['deal_id'] 					= $data_arr['deal_id'];
			$mail_det_arr['deal_title'] 				= $dealDet['deal_title'];
			$mail_det_arr['viewDealLink'] 				= $dealDet['viewDealLink'];
			$mail_det_arr['dealInfoLink'] 				= Url::to('admin/deals/manage-featured-requests', array('request_type' => 'pending'));
			$mail_det_arr['transaction_date'] 			= date('Y-m-d H:m:s');
			$mail_det_arr['deal_added_by'] 				= \CUtil::getUserDetails($data_arr['user_id']);
			$mail_det_arr['deal_added_user_link']		= URL::to('admin/users/user-details').'/'.$data_arr['user_id'];
			$mail_det_arr['to_email'] 					= Config::get("generalConfig.invoice_email");
			$tpl_name 									= 'deal_listing_fee_paid_admin_mail';

			# Send mail to admin regarding deal listing fee paid
			$template = "deals::emails.dealListingFeePaidNotificationToAdmin";
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr) {
					$m->to(\Config::get("generalConfig.invoice_email"));
					$subject = Lang::get('deals::deals.listing_fee_paid_admin_notify_mail_sub');
					$subject = str_replace("VAR_SITE_NAME", \Config::get('generalConfig.site_name'), $subject);
					$subject = str_replace("VAR_DEAL", $mail_det_arr['deal_title'], $subject);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
		$input_arr = array();
		$input_arr['deal_id'] = $data_arr['deal_id'];
		$input_arr['user_id'] = $data_arr['user_id'];
		$input_arr['date_featured_from'] = $data_arr['date_featured_from'];
		$input_arr['date_featured_to'] = $data_arr['date_featured_to'];
		$input_arr['fee_paid_status'] = 'Yes';
		$input_arr['deal_featured_days'] = $data_arr['deal_featured_days'];
		$input_arr['date_added'] = DB::Raw('now()');

		$deal_id = DB::table('deal_featured_request')->insertGetId($input_arr);

		return true;
	}


	// Admin function

	public function getAllDealsAdmin($inputs = array(), $return_type='paginate', $limit = 10)
	{
		$all_deals = DB::table('deal');
		$all_deals->select('deal.*', 'deal_featured.deal_featured_id',DB::Raw('IFNULL( deal_featured.deal_featured_id, 0 ) AS is_featured_deal'));
		$all_deals->leftjoin('deal_featured', function($join){
							 	$join->on('deal_featured.deal_id', '=', 'deal.deal_id');
							 	$join->on(DB::raw('(CURRENT_DATE( ) >= date_featured_from AND CURRENT_DATE( ) <= date_featured_to)'), DB::raw(''), DB::raw(''));
							 });

		if(isset($inputs['deal_id_from']) && $inputs['deal_id_from'] > 0)
			$all_deals->where('deal.deal_id', '>=', $inputs['deal_id_from']);

		if(isset($inputs['deal_id_to']) && $inputs['deal_id_to'] > 0)
			$all_deals->where('deal.deal_id', '<=', $inputs['deal_id_to']);

		if(isset($inputs['deal_title']) && $inputs['deal_title'] != '')
			$all_deals->whereRaw('deal.deal_title LIKE "%'.$inputs['deal_title'].'%"');

		if(isset($inputs['srch_user_id']) && $inputs['srch_user_id'] != '')
			$all_deals->where('deal.user_id', '=', $inputs['srch_user_id']);

		if(isset($inputs['deal_author']) && $inputs['deal_author'] != '')
			$all_deals->where('deal.user_id', '=', BasicCUtil::getUserIDFromCode($inputs['deal_author']));

		if(isset($inputs['slug_url']) && $inputs['slug_url'] != '')
			$all_deals->whereRaw('deal.url_slug LIKE "%'.$inputs['slug_url'].'%"');

		if(isset($inputs['featured']) && $inputs['featured'] != '')
		{
			if($inputs['featured'] == "Yes")
			{
				$all_deals->whereRaw('deal_featured_id IS NOT NULL');
			}
			elseif($inputs['featured'] == "No")
			{
				$all_deals->whereRaw('deal_featured_id IS NULL');
			}
		}

		if(isset($inputs['deal_status']) && $inputs['deal_status'] != '')
		{
			if($inputs['deal_status'] == 'expired')
			{
				$all_deals->whereRaw('( deal.deal_status = \'expired\' OR DATE_FORMAT(date_deal_to, \'%Y-%m-%d\') < CURDATE() )');
			}
			else
			{
				$all_deals->where('deal.deal_status', '=', $inputs['deal_status']);
			}
		}

		$all_deals->where('deal.user_id', '<>', 0);
		$all_deals->groupby('deal.deal_id')->orderby('deal.deal_id','desc');

		if($return_type=='get')
			return $all_deals->get();
		else
			return $all_deals->paginate($limit);
	}


	public function getAllFeaturedDealsAdmin($return_type='paginate', $limit = 10)
	{
		$all_deals = DB::table('deal');
		$all_deals->select('deal.*', 'deal_featured.deal_featured_id', 'deal_featured.date_featured_from', 'deal_featured.date_featured_to');
		$all_deals->join('deal_featured', 'deal.deal_id', '=', 'deal_featured.deal_id');
		$all_deals->groupby('deal.deal_id')->orderby('deal.deal_id','desc');

		if($return_type=='get')
			return $all_deals->get();
		else
			return $all_deals->paginate($limit);
	}


	public function getAllFeaturedDealsRequestsAdmin($request_type = 'all', $return_type='paginate', $limit = 10)
	{
		$deals = DB::table('deal');
		$deals->select('deal_featured_request.deal_id', 'url_slug', 'deal_status', 'deal_title', 'deal.user_id', 'discount_percentage',
								 'date_deal_from', 'date_deal_to', 'deal_featured_request.date_featured_from', 'deal_featured_request.date_added',
								 'deal_featured_request.date_featured_to', 'deal_featured_request.request_status', 'deal_featured_request.deal_featured_days',
								 'deal_featured_request.date_added AS date_requested', 'deal_featured_request.admin_comment');
		$deals->join('deal_featured_request', 'deal.deal_id', '=', 'deal_featured_request.deal_id');

		switch(strtolower($request_type))
		{
			case 'pending':
				$deals->whereRaw('deal_featured_request.request_status = \'pending_for_approval\' ');
				break;

			case 'approved':
				$deals->whereRaw('deal_featured_request.request_status = \'approved\' ');
				break;

			case 'unapproved':
				$deals->whereRaw('deal_featured_request.request_status = \'un_approved\' ');
				break;
		}
		$deals->orderBy('deal_featured_request.date_added', 'desc');
		if($return_type == 'paginate')
			return $deals->paginate($limit);
		else
			return $deals->get();
	}

	public function getFeaturedDealRequestDetailsByAdmin($deal_id=0, $type='view')
	{
		if(!$deal_id)
			return false;

		$deal_details = DB::table('deal')
							->select('deal_featured_request.deal_id', 'url_slug', 'deal_status', 'deal_title', 'deal.user_id', 'discount_percentage',
								 'date_deal_from', 'date_deal_to', 'deal_featured_request.date_featured_from', 'deal_featured_request.date_added',
								 'deal_featured_request.date_featured_to', 'deal_featured_request.request_status',
								 'deal_featured_request.deal_featured_days', 'deal_featured_request.request_id',
								 'deal_featured_request.date_added AS date_requested', 'deal_featured_request.admin_comment')
							->join('deal_featured_request', 'deal.deal_id', '=', 'deal_featured_request.deal_id')
							->where('deal.deal_id', $deal_id);

		if($type == 'update')
		{
			$deal_details->whereRaw('deal_featured_request.request_status = \'pending_for_approval\' ');
		}
		$deal_details = $deal_details->first();
		if(count($deal_details) > 0)
			return $deal_details;
		else
			return false;
	}


	public function chkIsValidFeaturedDate($deal_id, $date_featured_from, $date_featured_to)
	{
		$rec = DB::table('deal_featured');
		$rec->whereRaw('deal_id <> ?  AND ( DATE_FORMAT(date_featured_from,  \'%Y-%m-%d\') <= \''.$date_featured_to.'\' '.
				' AND DATE_FORMAT(date_featured_to,  \'%Y-%m-%d\') >= \''.$date_featured_from.'\' )', array($deal_id));
		$deal = $rec->first();
		if(count($deal) > 0)
		{
			return false;
		}
		return true;
	}

	public function updateFeaturedDealByAdmin($inputs)
	{
		$existRequest = DB::table('deal_featured_request')->whereRaw('deal_id = ?', array($inputs['deal_id']))->first();
		$record_exit = $request_id = 0;
		if(COUNT($existRequest) > 0)
		{
			$record_exit = 1;
			$update_arr = array();
			$update_arr['date_approved_on'] = DB::raw('NOW()');
			$update_arr['request_status'] = $inputs['request_status'];
			$update_arr['admin_comment'] = $inputs['admin_comment'];

			DB::table('deal_featured_request')->whereRaw('deal_id = ?', array($inputs['deal_id']))->update($update_arr);
			$request_id = $existRequest->request_id;
		}
		$insert_arr = array();
		$insert_arr['deal_id'] = $inputs['deal_id'];
		$insert_arr['date_featured_from'] = $inputs['date_featured_from'];
		$insert_arr['date_featured_to'] = $inputs['date_featured_to'];
		$insert_arr['request_id'] = $request_id;

		$deal_id = DB::table('deal_featured')->insertGetId($insert_arr);

		if($inputs['request_status'] = 'approved')
		{
			# Send mail to the user
			$mail_det_arr = array();
			$dealDet = $this->fetchDealDetailsById($inputs['deal_id']);
			$userDetails 				= \CUtil::getUserDetails($inputs['user_id']);
			$mail_det_arr['date_featured_from'] 		= $inputs['date_featured_from'];
			$mail_det_arr['date_featured_to'] 			= $inputs['date_featured_to'];
			$mail_det_arr['user_name'] 					= $userDetails['display_name'];
			$mail_det_arr['deal_id'] 					= $inputs['deal_id'];
			$mail_det_arr['deal_title'] 				= $dealDet['deal_title'];
			$mail_det_arr['viewDealLink'] 				= $dealDet['viewDealLink'];
			$mail_det_arr['transaction_date'] 			= date('Y-m-d H:m:s');
			$mail_det_arr['deal_added_by'] 				= $userDetails['display_name'];
			$mail_det_arr['to_email'] 					= $userDetails['email'];

			if($record_exit == 0 )
			{
				$template 								= 'deals::emails.dealFeaturedByAdminNotification';
				$subject 								= Lang::get('deals::deals.deal_set_featured_subject');
			}
			else
			{
				$template 								= 'deals::emails.dealFeaturedRequestApproved';
				$subject								= Lang::get('deals::deals.deal_featured_request_approved_subject');
			}
			$subject = str_replace("VAR_DEAL", $dealDet['deal_title'], $subject);
			$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
			try {
				# Send mail to user
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
		return true;
	}


	public function revertFundToUserAccountBalance($details_arr)
	{
		$listing_fee_revert = \Config::get('plugin.deal_listing_fee', 0) * $details_arr['deviation_days'];
		if($listing_fee_revert > 0)
		{
			# 1. Update users account balance
			$credit_obj = \Credits::initialize();
			//Debit from site
			$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
			$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
			$credit_obj->setAmount($listing_fee_revert);
			$credit_obj->creditAndDebit('amount', 'minus');

			//credit to buyer
			$credit_obj->setUserId($details_arr['requested_user']);
			$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
			$credit_obj->setAmount($listing_fee_revert);
			$credit_obj->creditAndDebit('amount', 'plus');

			# 2. Add site transaction entry
			$trans_obj = new \SiteTransactionHandlerService();
			$transaction_arr['date_added'] = new \DateTime;


			$transaction_arr['amount'] = $listing_fee_revert;
			$transaction_arr['currency'] = \Config::get('generalConfig.site_default_currency');
			$transaction_arr['transaction_key'] = 'deal_listfee_paid';
			$transaction_arr['reference_content_table'] = 'deal';
			$transaction_arr['reference_content_id'] = $details_arr['deal_id']; // deal_id
			$transaction_arr['status'] = 'completed';
			$transaction_arr['related_transaction_id'] = '';
			$transaction_arr['payment_type'] = 'wallet';

			$transaction_arr['user_id'] = Config::get('generalConfig.admin_id');
			$transaction_arr['transaction_type'] = 'debit';
			$transaction_arr['transaction_notes'] = 'Debited variated deal listing fee to wallet for listed deal id: '.$details_arr['deal_id'];
			$transaction_arr['transaction_notes'] .= 'Since admin set lesser number of days for the deals to be featured';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			$transaction_arr['transaction_type'] = 'credit';
			$transaction_arr['user_id'] = $details_arr['requested_user'];
			$transaction_arr['transaction_notes'] = $details_arr['transaction_comment'];
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);
		}
		return true;
	}

	public function unapproveFeatureRequest($data_arr)
	{
		$update_arr = array();
		$update_arr['request_status'] = $data_arr['request_status'];
		$update_arr['admin_comment'] = $data_arr['admin_comment'];

		DB::table('deal_featured_request')->whereRaw('deal_id = ?', array($data_arr['deal_id']))->update($update_arr);
		$dealDet = $this->fetchDealDetailsById($data_arr['deal_id']);
		$input_det_arr = array();
		$input_det_arr['deal_id'] 			= $data_arr['deal_id'];
		$input_det_arr['deviation_days'] 	= $data_arr['deal_featured_days'];
		$input_det_arr['requested_user'] 	= $data_arr['requested_user'];
		$input_det_arr['transaction_type'] 	= 'Credit';
		$txn_comment = str_replace('VAR_DEAL', $dealDet['deal_title'], Lang::get('deals::deals.featured_req_unapprove_refund_msg'));
		$input_det_arr['transaction_comment'] 	= $txn_comment;
		$this->revertFundToUserAccountBalance($input_det_arr);
		$this->sendDisapproveMailToUser($input_det_arr);

		return true;
	}

	public function sendDisapproveMailToUser($details_arr)
	{
		$mail_det_arr = array();

		$dealDet = $this->fetchDealDetailsById($details_arr['deal_id']);

		$userDetails 				= \CUtil::getUserDetails($details_arr['requested_user']);;
		$mail_det_arr['user_name'] 					= $userDetails['display_name'];
		$mail_det_arr['comment'] 					= $details_arr['transaction_comment'];
		$mail_det_arr['deal_id'] 					= $details_arr['deal_id'];
		$mail_det_arr['viewDealLink'] 				= $dealDet['viewDealLink'];
		$mail_det_arr['deal_title'] 				= $dealDet['deal_title'];
		$mail_det_arr['transaction_date'] 			= date('Y-m-d H:m:s');
		$mail_det_arr['to_email'] 					= $userDetails['email'];
		$template 									= 'deals::emails.dealFeaturedRequestDisapprovedNotification';

		$subject								= Lang::get('deals::deals.deal_featured_request_unapproved_subject');
		$subject = str_replace("VAR_DEAL", $dealDet['deal_title'], $subject);
		$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
		try {
			# Send mail to user
			\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
				$m->to($mail_det_arr['to_email']);
				$m->subject($subject);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function unFeatureDealByAdmin($deal_id=0)
	{
		if($deal_id)
		{
			DB::table('deal_featured')->whereRaw('deal_id = ?', array($deal_id))->delete();
			return true;
		}
		return false;
	}

	public function closeDealByAdmin($deal_id=0)
	{
		if($deal_id)
		{
			$deal_details = DB::table('deal');
			$deal = $deal_details->whereRaw('deal_id = ? AND deal_status = \'active\'', array($deal_id))->first();
			if(COUNT($deal) > 0)
			{
//				$purchase_count = $this->getDealPurchasedCountById($deal_id);
				DB::table('deal')->whereRaw('deal_id = ?', array($deal_id))->update(array('deal_status' => 'closed'));

				// Remove deal featured entry if it gettign closed
				$this->unFeatureDealByAdmin($deal_id);
				// Remove feature request entry
				$this->removeFeatureRequestEntry($deal_id, "Deal set as closed by admin");

				$input_det_arr = array();
				$input_det_arr['deal_id'] 			= $deal->deal_id;
				$input_det_arr['user_id'] 		 	= $deal->user_id;
				$input_det_arr['deal_det'] 		 	= $deal;
				$admin_comment = str_replace('VAR_DEAL', $deal->deal_title, Lang::get('deals::deals.deal_closed_by_admin_msg'));
				$admin_comment = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $admin_comment);
				$input_det_arr['admin_comment'] 	= $admin_comment;

				$this->sendDealClosedNotificationMailToUser($input_det_arr);
				return true;
			}
		}
		return false;
	}

	public function sendDealClosedNotificationMailToUser($details_arr)
	{
		$mail_det_arr 	= array();
		$dealDet 		= $details_arr['deal_det'];
		$userDetails 	= \CUtil::getUserDetails($details_arr['user_id']);;
		$mail_det_arr['user_name'] 	= $userDetails['display_name'];
		$mail_det_arr['comment'] 	= $details_arr['admin_comment'];
		$mail_det_arr['deal_id'] 	= $details_arr['deal_id'];
		$mail_det_arr['viewDealLink'] = $this->getDealViewUrl($dealDet);
		$mail_det_arr['deal_title'] = $dealDet->deal_title;
		$mail_det_arr['to_email'] 	= $userDetails['email'];
		$mail_det_arr['transaction_date'] = date('Y-m-d H:m:s');
		$template 					= 'deals::emails.dealClosedNotificationToSeller';

		$subject								= Lang::get('deals::deals.deal_closed_by_admin_msg');
		$subject = str_replace("VAR_DEAL", $dealDet->deal_title, $subject);
		$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
		try {
			# Send mail to user
			\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
				$m->to($mail_det_arr['to_email']);
				$m->subject($subject);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function removeFeaturedDealsByAdmin($input)
	{
		if(isset($input['ids']) && COUNT($input['ids']))
		{
			$deal_arr = array_unique($input['ids']);
			foreach($deal_arr AS $deal_index => $feature_id)
			{
				DB::table('deal_featured')->whereRaw('deal_featured_id = ?', array($feature_id))->delete();
			}
			return true;
		}
		return false;
	}

	public function changeDealStatusByAdmin($input, $change_status)
	{
		if(isset($input['deal_ids']) && COUNT($input['deal_ids']))
		{
			$deal_arr = array_unique($input['deal_ids']);
			foreach($deal_arr AS $deal_index => $deal_id)
			{
				if($change_status == 'active')
				{
					$dealDet = DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->first();
					if(COUNT($dealDet) > 0 && $dealDet->deal_status != 'active')	// Already active then don't needs to update and send mail.
					{
						DB::table('deal')->whereRaw('deal_id = ?', array($deal_id))->update(array('deal_status' => 'active'));
						// Send deal activation mail to seller
						$this->sendDealActivationMailToUser($deal_id);
					}
				}
				elseif($change_status == 'de-activate')
				{
					DB::table('deal')->whereRaw('deal_id = ?', array($deal_id))->update(array('deal_status' => 'deactivated'));
					$this->unFeatureDealByAdmin($deal_id); // Remove featured deal entry

					// Remove feature request entry
					$this->removeFeatureRequestEntry($deal_id, "Deal set as closed by admin");
				}
			}
			return true;
		}
		return false;
	}

	public function sendDealActivationMailToUser($deal_id)
	{
		$dealDet = $this->fetchDealDetailsById($deal_id);
		if($deal_id > 0 && COUNT($dealDet) > 0)
		{
			// Add email functionality for notification to the admin regarding the deal added details.
			$seller_details = \CUtil::getUserDetails($dealDet['deal_user_id']);
			$mail_det_arr = array();
			$mail_det_arr['deal_id'] 					= $deal_id;
			$mail_det_arr['deal_title'] 				= $dealDet['deal_title'];
			$mail_det_arr['viewDealLink'] 				= $dealDet['viewDealLink'];
			$mail_det_arr['deal_added_by'] 				= $seller_details['display_name'];
			$mail_det_arr['user_name'] 					= $seller_details['display_name'];
			$mail_det_arr['deal_user_profile_link']		= $seller_details['profile_url'];
			$mail_det_arr['deal_approved_date'] 		= date('Y-m-d H:m:s');
			$mail_det_arr['deal_discount_percentage'] 	= $dealDet['discount_percentage'];
			$mail_det_arr['to_email'] 					= $seller_details['email'];
			$mail_det_arr['admin_msg']				= Lang::get('deals::deals.deal_approved_success_admin_msg');
			$mail_det_arr['admin_msg'] = str_replace("VAR_DEAL",  $dealDet['deal_title'], $mail_det_arr['admin_msg']);
			# Send mail to seller regarding deal activated by admin
			$template = "deals::emails.dealApprovedNotificationToSeller";
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr) {
					$m->to($mail_det_arr['to_email']);
					$subject = Lang::get('deals::deals.deal_approved_success_mail_sub');
					$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}

	// For existing pages
	public function fetchItemDealDetails($det_arr)
	{
		// check if the shop having any deals on it.
		$deal_details = array();
		$deal_details['deal_available'] = 0;
		// If item owner id passed then add condition for that.
		$cond ='';
		if(isset($det_arr['item_owner_id']) && $det_arr['item_owner_id'] > 0)
		{
			$cond = ' OR ( deal.user_id = '.$det_arr['item_owner_id'].' AND deal.applicable_for = \'all_items\')  ';
		}

		$rec = DB::table('deal');
		$rec->select('deal.deal_id', 'deal.user_id', 'deal.discount_percentage', 'deal.applicable_for', 'deal_item.item_id', 'deal.url_slug',
				'deal.img_name', 'deal.img_ext', 'deal.img_width', 'deal.img_height', 'deal.l_width', 'deal.l_height', 'deal.t_width', 'deal.t_height', 'deal.deal_title', 'deal.deal_short_description', 'deal.tipping_qty_for_deal', 'deal.deal_tipping_status');
		$rec->leftjoin('deal_item', 'deal.deal_id', '=', 'deal_item.deal_id');
		$rec->whereRaw('deal.deal_status = \'active\' AND ( deal_item.item_id = ? '. $cond . ' ) AND CURRENT_DATE( ) >= date_deal_from AND CURRENT_DATE( ) <= date_deal_to ', array($det_arr['item_id']));
		$rec->orderBy('deal.discount_percentage', 'desc');
		$deal = $rec->first();
		if(COUNT($deal) > 0)
		{
			$deal_details['deal_available'] = 1;
			$deal_details['deal_id'] = $deal->deal_id;
			$deal_details['discount_percentage'] = $this->formatDiscountPercentage($deal->discount_percentage);
			$deal_details['deal_title'] = $deal->deal_title;
			$deal_details['short_desc'] = $deal->deal_short_description;
			$deal_details['tipping_qty_for_deal'] = $deal->tipping_qty_for_deal;
			$deal_details['deal_tipping_status'] = $deal->deal_tipping_status;
			$deal_details['view_deal_url'] = URL::to('deals/view-deal/'.$deal->url_slug);

			$d_img_arr['deal_id'] = $deal->deal_id;
			$d_img_arr['deal_title'] = $deal->deal_title;
			$d_img_arr['img_name'] = $deal->img_name;
			$d_img_arr['img_ext'] = $deal->img_ext;
			$d_img_arr['img_width'] = $deal->img_width;
			$d_img_arr['img_height'] = $deal->img_height;
			$d_img_arr['l_width'] = $deal->l_width;
			$d_img_arr['l_height'] = $deal->l_height;
			$d_img_arr['t_width'] = $deal->t_width;
			$d_img_arr['t_height'] = $deal->t_height;
			$p_thumb_img = $this->getDealDefaultThumbImage($deal->deal_id, 'thumb', $d_img_arr);

			$deal_details['deal_image_details'] = $p_thumb_img;
		}
		return $deal_details;
	}

	public function fetchDealDetailsById($deal_id)
	{
		// Fetch deal discount amount details by deal id.
		$deal_details = array();
		$deal = DB::table('deal')->where('deal_id', $deal_id)->first();
		if(COUNT($deal) > 0)
		{
			$deal_details['deal_title'] = $deal->deal_title;
			$deal_details['deal_user_id'] = $deal->user_id;
			$deal_details['deal_status'] = $deal->deal_status;
			$deal_details['deal_tipping_status'] = $deal->deal_tipping_status;
			$tipping_status_lbl = '';
			switch($deal->deal_tipping_status)
			{
				case 'pending_tipping':
					$tipping_status_lbl = Lang::get('deals::deals.pending_tipping_lbl');
					break;
				case 'tipping_reached':
					$tipping_status_lbl = Lang::get('deals::deals.tipping_reached_lbl');
					break;
				case 'tipping_failed':
					$tipping_status_lbl = Lang::get('deals::deals.tipping_failed_lbl');
					break;
				default:
					$tipping_status_lbl = Lang::get('deals::deals.notstarted_tipping_lbl');
			}
			$deal_details['deal_tipping_status_lbl'] = ($deal->tipping_qty_for_deal > 0) ? $tipping_status_lbl : "";
			$deal_details['discount_percentage'] = $this->formatDiscountPercentage($deal->discount_percentage);
			$deal_details['tipping_qty'] = $deal->tipping_qty_for_deal;
			if($deal->tipping_qty_for_deal == 0){
				$value = Lang::get('deals::deals.deal_tipping_info');
			}else{
				$value = Lang::get('deals::deals.deal_tipping_info_quantity');
				$value = str_replace("VAR_QUANTITY_VAL", $deal->tipping_qty_for_deal, $value);
			}
			$deal_details['tipping_note_msg'] = ($deal->tipping_qty_for_deal > 0) ? $value : '';
			//$deal_details['tipping_note_msg'] = ($deal->tipping_qty_for_deal > 0) ? Lang::get('deals::deals.deal_tipping_info') : '';
			$deal_details['viewDealLink'] = URL::to('deals/view-deal/'.$deal->url_slug);
		}
		return $deal_details;
	}

	public function fetchDealBasedReceiverAmount($order_item_details)
	{
		$receivers_details = array();
		$deal_found = false;
		//1. check tipping status, if reached - seller - seller amount, site - site_amount, No, Seller = 0, Site = product amount
		if(COUNT($order_item_details) > 0)
		{
			$receivers_details['primary_amount'] = '';
			$receivers_details['secondary_amount'] = '';
			$primary_amount = $secondary_amount = 0;
			foreach($order_item_details AS $item)
			{
				$item->seller_amount = (isset($item->discount_ratio_amount) && $item->discount_ratio_amount >0 && $item->seller_amount > $item->discount_ratio_amount)?($item->seller_amount-$item->discount_ratio_amount):$item->seller_amount;
				// item_id, item_owner_id, deal_id
				if(isset($item->deal_id) && $item->deal_id > 0)
				{
					$deal_found = true;
					// Item found with deal
					$tipping_limit = $this->isTippingLimitExist($item->deal_id);
					if($tipping_limit == 0 || ($this->chkIsTippingReached($item->deal_id, $item->item_id)))
					{
						// Tipping reached / Tipping not found
						$primary_amount += $item->site_commission;
						$secondary_amount +=$item->seller_amount;
					}
					else
					{
						// Tipping not reached
						$primary_amount += $item->site_commission+$item->seller_amount;
						$secondary_amount +=0;
					}
				}
				else
				{
					$primary_amount += $item->site_commission;
					$secondary_amount +=$item->seller_amount;
				}
			}
			$receivers_details['primary_amount'] = $primary_amount;
			$receivers_details['secondary_amount'] = $secondary_amount;
		}
		if($deal_found)
			return $receivers_details;
		else
			return array();
	}

	public function addDealItemPurchasedEntry($data_arr)
	{
		$cond = '';
		$exRec = DB::table('deal_item_purchased_details')
			->whereRaw('deal_id = ? AND item_id = ?',array($data_arr['deal_id'], $data_arr['item_id']))->first();

		$insert_arr['qty'] = $data_arr['item_qty'];
		$insert_arr['deal_id'] = $data_arr['deal_id'];
		$insert_arr['item_id'] = $data_arr['item_id'];
		$insert_arr['order_id'] = $data_arr['order_id'];
		$insert_arr['seller_amount_credited'] = ( $this->chkIsDealTipped($data_arr['deal_id']) )? 1 : 0;
		$deal_id = DB::table('deal_item_purchased_details')->insertGetId($insert_arr);
	}

	public function chkIsDealTipped($deal_id)
	{
		$rec = DB::table('deal')->whereRaw('deal_id = ? AND deal_tipping_status = \'tipping_reached\' ', array($deal_id))->get();
		if(COUNT($rec) > 0)
		{
			return true;
		}
		return false;
	}

	public function isTippingLimitExist($deal_id)
	{
		if($deal_id == 0)
			return 0;

		$tipping_qty = DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->pluck('tipping_qty_for_deal');

		if(COUNT($tipping_qty) > 0)
			return $tipping_qty;

		return 0;
	}


	public function getDealItemPurchasedCount($deal_id, $item_id = 0)
	{
		$dealRec = DB::table('deal_item_purchased_details');
		$dealRec->select(DB::raw('sum(qty) as cnt'));
		$dealRec->where('deal_id', $deal_id);
		if($item_id > 0)
		{
			$dealRec->where('item_id', $item_id);
		}
		$dealRec->groupBy('deal_id');
		$dealDet = $dealRec->pluck('cnt');
		if($dealDet)
			return $dealDet;
		return 0;
	}

	public function updateDealTippingStatus($data_arr)
	{
		$deal_id = $data_arr['deal_id'];
		$item_id = $data_arr['item_id'];
		$order_id = $data_arr['order_id'];

		// Check this deal id have any tipping level. if tipping validation exist then check the tipping
		$tipping_limit = $this->isTippingLimitExist($deal_id);

		if($tipping_limit > 0)
		{
			// Check is deal reached its tipping status
			if(!$this->chkIsTippingReached($deal_id, $item_id))
			{
				$this->chkAndUpdateDealTippingStatus($deal_id, $item_id, $tipping_limit);
			}
			else
			{
				// If already tipping reached then update deal tipping status alone.
				$update_arr = array();
				$update_arr['deal_tipping_status'] = 'tipping_reached';
				DB::table('shop_order_item')->whereRaw('deal_id = ? ', array($deal_id))->update($update_arr);
				DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->update($update_arr);
			}
		}

		$selRec = DB::table('shop_order_item')->whereRaw('order_id = ? AND deal_tipping_status <> \'\' ', array($order_id))->pluck('deal_tipping_status');
		if(COUNT($selRec) > 0)
		{
			$tip_status_arr[] = $selRec;
		}

		if(isset($tip_status_arr) && COUNT($tip_status_arr) > 0)
		{
			$tip_pending = 0;
			$tip_reached = 0;

			foreach($tip_status_arr AS $tip_val)
			{
				if($tip_val == 'tipping_reached')
					$tip_reached = $tip_reached+1;
				elseif($tip_val == 'pending_tipping')
					$tip_pending = $tip_pending + 1;
			}

			if($tip_pending == 0)
			{
				$update_arr = array();
				$update_arr['deal_tipping_status'] = 'tipping_reached';
				DB::table('shop_order')->whereRaw('id = ? ', array($order_id))->update($update_arr);
			}
		}
		return true;
	}

	public function chkAndUpdateDealTippingStatus($deal_id, $item_id=0, $tipping_limit)
	{
		$reachedTip = 0;
		$deal_purchased_count = $this->getDealItemPurchasedCount($deal_id, $item_id);

		// if deal purchased count reached its limit then update the deal id status as 'tipping_reached'
		$update_arr = array();
		if($deal_purchased_count >= $tipping_limit)
		{
			$reachedTip = 1;
			$update_arr['deal_tipping_status'] = 'tipping_reached';

		}
		else
		{
			$update_arr['deal_tipping_status'] = 'pending_tipping';
		}

		if($item_id > 0)
			DB::table('shop_order_item')->whereRaw('deal_id = ? AND item_id = ?', array($deal_id, $item_id))->update($update_arr);
		else
			DB::table('shop_order_item')->whereRaw('deal_id = ?', array($deal_id))->update($update_arr);

		DB::table('deal')->whereRaw('deal_id = ? ', array($deal_id))->update($update_arr);
		return $reachedTip;
	}

	public function chkIsTippingReached($deal_id, $item_id)
	{
		$rec = DB::table('shop_order_item')->whereRaw('deal_id = ? AND item_id = ? AND deal_tipping_status = \'tipping_reached\' ', array($deal_id, $item_id))->get();
		if(COUNT($rec) > 0)
		{
			return true;
		}
		return false;
	}

	public function getTippingNotifiedStatus($deal_id)
	{
		$tipping_notified = DB::table('deal')->where('deal_id')->pluck('tipping_notified');
		return $tipping_notified;
	}

	public function updateDealTippedDetails($deal)
	{
		/*
			Send notification to
			Admin - Tipping reached and transfer fund to seller a/c ..
			Seller -Tipping reached please ship the product items, with the deal purchased details and you will receive the fund the product purchasee soon,
			Buyer - Tipping reached for your purchase you will shortly receive your items.
		  - Update as notified to avoid repeated notification
		*/
		$cron_status = $this->getTippingNotifiedStatus($deal->deal_id);

		if($cron_status == 0)
		{
			// Update status as progress
			DB::table('deal')->whereRaw('deal_id = ?', array($deal->deal_id))->update(array('tipping_notified' => 1));

			$deal_url = $this->getDealViewUrl($deal);

			$purchaseList = DB::table('deal_item_purchased_details')
							->join('shop_order_item', function($join){
							 	$join->on('shop_order_item.order_id', '=', 'deal_item_purchased_details.order_id');
							 	$join->on('shop_order_item.item_id', '=', 'deal_item_purchased_details.item_id');
							 });
			$purchaseList->where('deal_item_purchased_details.deal_id', $deal->deal_id)->where('seller_amount_credited', 0);
			$orderList = $purchaseList->get();
			$orderData = $txn_data = array();
			if(COUNT($orderList) > 0)
			{
				foreach($orderList AS $order)
				{
					//$order_details = $order;
					$order_details = array();
					$order_details['seller_amount'] = $order->seller_amount;
					$order_details['buyer_id'] = $order->buyer_id;
					$order_details['seller_id'] = $order->item_owner_id;
					$order_details['order_id'] = $order->order_id;
					$order_details['deal_id'] = $order->deal_id;
					$order_details['purchased_id'] = $order->deal_item_purchased_id;
					$order_details['item_id'] = $order->item_id;
					$order_details['deal_title'] = $deal->deal_title;
					$order_details['deal_link'] = $deal_url;

					// Transfer fund to seller account from site account
					$resp = $this->sendDealTippingSuccessPayment($order_details);
					$txn_data[] = $resp;
					$orderData[] = $order_details;
				}
			}

			// Check and update deal expired status
			$dealExpired = DB::table('deal')->whereRaw('DATE_FORMAT(date_deal_to, \'%Y-%m-%d\') < CURDATE() ')
							->where('deal_status', '<>', 'closed')->where('deal_id', $deal->deal_id)->first();
			if(COUNT($dealExpired) > 0)
			{
				DB::table('deal')->whereRaw('deal_id = ?', array($deal->deal_id))->update(array('deal_status' => 'expired'));
			}
			// Send mail to seller as deal has tipped and tipping amount will be creditted to you

			# Send mail to the seller
			$seller_details = \CUtil::getUserDetails($deal->user_id);
			$mail_det_arr = array();

			$mail_det_arr['to_email']			= $seller_details['email'];
			$mail_det_arr['user_name']			= $seller_details['display_name'];
			$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tipped_msg_to_seller');
			$mail_det_arr['view_sales_link'] 	= Url::to('purchases/my-sales');
			$mail_det_arr['view_deal_link'] 	= $deal_url;
			$mail_det_arr['deal_id'] 			= $deal->deal_id;
			$mail_det_arr['deal_title'] 		= $deal->deal_title;
			$mail_det_arr['transaction_date'] 	= date('Y-m-d H:m:s');

			$subject = Lang::get('deals::deals.tipping_reached_notification_to_seller');
			$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
			$template = "deals::emails.dealTippingStatusReachedNotificationToSeller";
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
			// Send mai to admin as deal tipped and tipping amount transferred if paid via wallet, if paid via paypal needs to transfer.
			# Send mail to the admin
			$mail_det_arr['to_email']			= \Config::get("generalConfig.invoice_email");
			$mail_det_arr['user_name']			= "Admin";
			$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tipped_msg_to_admin');
			$mail_det_arr['order_txn_details'] 	= isset($txn_data) ? $txn_data : array();
			$mail_det_arr['view_deal_link'] 	= URL::to('admin/deals/manage-deals').'?deal_id_from='.$deal->deal_id.'&deal_id_to='.$deal->deal_id.'&search_deal=search_deal';;

			$subject = Lang::get('deals::deals.tipping_reached_notification_to_admin');
			$subject = str_replace("VAR_SITE_NAME", \Config::get('generalConfig.site_name'), $subject);
			$subject = str_replace("VAR_DEAL", $deal->deal_title, $subject);
			$template = "deals::emails.dealTippingStatusReachedNotificationAdmin";
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}


	public function sendDealTippingSuccessPayment($data_arr)
	{
		$resp = array();
		if(COUNT($data_arr) > 0)
		{
			$admin_id = Config::get('generalConfig.admin_id');

			#  debit admin from admin account
			$credit_obj = \Credits::initialize();
			$credit_obj->setUserId($admin_id); // Admin
			$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
			$credit_obj->setAmount($data_arr['seller_amount']);
			$credit_obj->creditAndDebit('amount', 'minus');

			# Credit seller account
			$credit_obj->setUserId($data_arr['seller_id']); // seller
			$credit_obj->setAmount($data_arr['seller_amount']); // order seller amount
			$credit_obj->creditAndDebit('amount', 'plus');

			# Add site transaction entry for debit admin account
			$trans_obj = new \SiteTransactionHandlerService();
			$transaction_arr['date_added'] = new \DateTime;
			$transaction_arr['user_id'] = $admin_id; // Admin id
			$transaction_arr['transaction_type'] = 'debit';
			$transaction_arr['amount'] = $data_arr['seller_amount'];	// order seller amount
			$transaction_arr['currency'] = \Config::get('generalConfig.site_default_currency');
			$transaction_arr['transaction_key'] = 'deal_tipping_success'; // deal_tipping_amount
			$transaction_arr['reference_content_table'] = 'order';
			$transaction_arr['reference_content_id'] = $data_arr['deal_id']; // order id
			$transaction_arr['related_transaction_id'] = $data_arr['order_id']; // deal id
			$transaction_arr['status'] = 'completed';
			$transaction_arr['payment_type'] = 'wallet';
			$transaction_arr['transaction_notes'] = 'Deal tipped - seller amount debitted';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			# Add site transaction entry for credit seller account
			$transaction_arr['user_id'] = $data_arr['seller_id'];
			$transaction_arr['transaction_type'] = 'credit';
			$transaction_arr['transaction_notes'] = 'Deal tipped - seller amount credited';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);
			$resp['reference_id'] = $trans_id;
			$resp['txn_amount'] = $data_arr['seller_amount'];
			$resp['txn_type'] 	= "Transfer";
			$resp['txn_method'] = "Wallet";
			$resp['txn_status'] = "Transferred";

			# Update listing fee paid status as Yes in deal
			DB::table('deal_item_purchased_details')->whereRaw('deal_item_purchased_id = ?', array($data_arr['purchased_id']))
				->update(array('seller_amount_credited' => 1));

			# Send mail to the buyer
			$buyer_details = \CUtil::getUserDetails($data_arr['buyer_id']);
			$mail_det_arr = array();
			$mail_det_arr['deal_id'] 			= $data_arr['deal_id'];
			$mail_det_arr['transaction_date'] 	= date('Y-m-d H:m:s');
			$mail_det_arr['deal_title'] 		= $data_arr['deal_title'];
			$mail_det_arr['view_deal_link'] 	= $data_arr['deal_link'];

			$mail_det_arr['to_email']			= $buyer_details['email'];
			$mail_det_arr['user_name']			= $buyer_details['display_name'];
			$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tipped_msg_to_buyer');
			$mail_det_arr['view_order_link'] 	= URL::action('PurchasesController@getOrderDetails', $data_arr['order_id']);

			$template = "deals::emails.dealTippingStatusReachedNotificationToBuyer";
			$subject = Lang::get('deals::deals.tipping_reached_notification_to_buyer');
			$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
			# Update deal as tipping notified
			DB::table('deal')->whereRaw('deal_id = ?', array($data_arr['deal_id']))->update(array('tipping_notified' => 2));
		}
		return $resp;
	}

	public function fetchPaypalTransactionId($order_id)
	{
		$txnId = DB::table('order_receivers')
						->join('common_invoice', 'order_receivers.common_invoice_id', '=', 'common_invoice.common_invoice_id')
						->where('common_invoice.reference_id', $order_id)
						->where('common_invoice.reference_type', 'Products')
						->where('order_receivers.is_admin', 'Yes')
						->pluck('txn_id'); // paypal txn id
		return $txnId;
	}


	public function sendDealTippingFailureRefund($data_arr)
	{
		$resp = array();
		if(COUNT($data_arr) > 0)
		{
			$admin_id = Config::get('generalConfig.admin_id');
			// Refund  amount based on type of gateway paypal / wallet.
			$refundType = $this->fetchPaymentTypeForTxn($data_arr['order_id']);
			$refundAmount = $data_arr['refund_amount'];
			$paypal_txn_id = '';
			$resp['txn_amount'] = $refundAmount;
			$resp['txn_type'] 	= "Refund";
			$resp['txn_method'] = $refundType;
			$refund_flag = 1;
			if($refundType == 'paypal')
			{
				// Include paypal transaction ID
				$paypal_txn_id = $this->fetchPaypalTransactionId($data_arr['order_id']);
				$resp['reference_id'] = $paypal_txn_id;
				$resp['txn_status'] = 'Pending Transfer';
				// Send notification mail to admin regards payment needs to transfer to member paypal account.
				// Send noptification mail to buyer as tipping failed and refund process started reach shortly.
			}
			elseif($refundType == 'wallet')
			{
				// Notification mail for tipping failed and refund processed and transferred to wallet.
				#  debit admin from admin account
				$credit_obj = \Credits::initialize();
				$credit_obj->setUserId($admin_id); // Admin
				$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
				$credit_obj->setAmount($refundAmount);
				$credit_obj->creditAndDebit('amount', 'minus');

				# Credit buyer account
				$credit_obj->setUserId($data_arr['buyer_id']); // buyer
				$credit_obj->setCurrency(\Config::get('generalConfig.site_default_currency'));
				$credit_obj->setAmount($refundAmount); // order total amount
				$credit_obj->creditAndDebit('amount', 'plus');

				# Add site transaction entry for debit admin account
				$trans_obj = new \SiteTransactionHandlerService();
				$transaction_arr['date_added'] = new \DateTime;
				$transaction_arr['user_id'] = $admin_id; // Admin id
				$transaction_arr['transaction_type'] = 'debit';
				$transaction_arr['amount'] = $refundAmount;	// order seller amount
				$transaction_arr['currency'] = \Config::get('generalConfig.site_default_currency');
				$transaction_arr['transaction_key'] = 'deal_tipping_failure'; // deal_tipping_amount
				$transaction_arr['reference_content_table'] = 'order';
				$transaction_arr['reference_content_id'] = $data_arr['deal_id']; // order id
				$transaction_arr['related_transaction_id'] = $data_arr['order_id']; // deal id
				$transaction_arr['status'] = 'completed';
				$transaction_arr['payment_type'] = 'wallet';
				$transaction_arr['transaction_notes'] = 'Deal tip failed - seller amount debitted';
				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

				# Add site transaction entry for credit seller account
				$transaction_arr['user_id'] = $data_arr['seller_id'];
				$transaction_arr['transaction_type'] = 'credit';
				$transaction_arr['transaction_notes'] = 'Deal tip failed - order amount refunded';
				$trans_id = $trans_obj->addNewTransaction($transaction_arr);
				$resp['reference_id'] = $trans_id;
				$resp['txn_status'] = "Transferred";
				$refund_flag = 2;
			}
			# Update listing fee paid status as Yes in deal
			DB::table('deal_item_purchased_details')->whereRaw('deal_item_purchased_id = ?', array($data_arr['purchased_id']))
				->update(array('seller_amount_credited' => 1, 'is_refund_processed' => $refund_flag));

			#Update shop order item - tipping status as 'tipping_failed'
			$update_arr = array();
			$update_arr['deal_tipping_status'] = 'tipping_failed';
			DB::table('shop_order_item')->whereRaw('deal_id = ? ', array($data_arr['deal_id']))->update($update_arr);

			$buyer_details = \CUtil::getUserDetails($data_arr['buyer_id']);
			$mail_det_arr = array();
			$mail_det_arr['deal_id'] 			= $data_arr['deal_id'];
			$mail_det_arr['transaction_date'] 	= date('Y-m-d H:m:s');
			$mail_det_arr['deal_title'] 		= $data_arr['deal_title'];
			$mail_det_arr['view_deal_link'] 	= $data_arr['deal_link'];
			$mail_det_arr['view_order_link'] 	= URL::action('PurchasesController@getOrderDetails', $data_arr['order_id']);

			# Send mail to the buyer
			$mail_det_arr['to_email']			= $buyer_details['email'];
			$mail_det_arr['user_name']			= $buyer_details['display_name'];
			$mail_det_arr['mail_for'] 			= 'Buyer';
			$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tip_failed_msg_to_buyer');

			$template = "deals::emails.dealTippingStatusReachedNotificationToBuyer";
			$subject = Lang::get('deals::deals.tipping_failed_notification_to_buyer');
			$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
			# Update deal as tipping notified
			$update_arr = array('deal_status' => 'expired', 'deal_tipping_status' => 'tipping_failed', 'tipping_notified' => 2);
			DB::table('deal')->whereRaw('deal_id = ?', array($data_arr['deal_id']))->update($update_arr);

			# update invoices invoice_status = refunded where order_id = ? and item_id = ?
			DB::table('invoices')->whereRaw('order_id = ? AND item_id = ?', array($data_arr['order_id'], $data_arr['item_id']))->update(array('invoice_status' => 'refunded'));
		}
		return $resp;
	}


	public function updateDealExpiredDetails($deal)
	{
		$send_notification = 0;
		if(COUNT($deal) > 0)
		{
			$cron_status = $this->getTippingNotifiedStatus($deal->deal_id);
			if($cron_status == 0)
			{
				// Update status as progress
				DB::table('deal')->whereRaw('deal_id = ?', array($deal->deal_id))->update(array('tipping_notified' => 1));
				$deal_url = $this->getDealViewUrl($deal);

				$update_arr = array();
				$tipping_limit = isset($deal->tipping_qty_for_deal) ? $deal->tipping_qty_for_deal : 0;
				if($tipping_limit > 0)
				{
					// if not reached tipping
					if(!$this->chkAndUpdateDealTippingStatus($deal->deal_id, 0, $tipping_limit))
					{
						// Do refund process
						$purchaseList = DB::table('deal_item_purchased_details')
							->join('shop_order_item', function($join){
							 	$join->on('shop_order_item.order_id', '=', 'deal_item_purchased_details.order_id');
							 	$join->on('shop_order_item.item_id', '=', 'deal_item_purchased_details.item_id');
							 });
						$purchaseList->where('deal_item_purchased_details.deal_id', $deal->deal_id)->where('seller_amount_credited', 0);
						$orderList = $purchaseList->get();
						$orderData = $txn_data = array();
						if(COUNT($orderList) > 0)
						{
							foreach($orderList AS $order)
							{
								$order_details = array();
								$order_details['buyer_id'] = $order->buyer_id;
								$order_details['seller_id'] = $order->item_owner_id;
								$order_details['order_id'] = $order->order_id;
								$order_details['deal_id'] = $order->deal_id;
								$order_details['purchased_id'] = $order->deal_item_purchased_id;
								$order_details['item_id'] = $order->item_id;
								$order_details['deal_title'] = $deal->deal_title;
								$order_details['deal_link'] = $this->getDealViewUrl($deal);
								$order_details['refund_amount'] = ($order->discount_ratio_amount != "" && $order->discount_ratio_amount > 0 && ($order->total_amount-$order->discount_ratio_amount) > 0 ) ? ($order->total_amount-$order->discount_ratio_amount) : $order->total_amount;

								// Transfer fund to seller account from site account
								$resp = $this->sendDealTippingFailureRefund($order_details);
								$txn_data[] = $resp;
								$orderData[] = $order_details;
							}
						}
						else
						{
							# Update deal as tipping status tipping_failed, deal_status as expired
							$update_arr = array('deal_tipping_status' => 'tipping_failed', 'deal_status' => 'expired');
						}
						$send_notification = 1;
					}
					else
					{
						# Update deal as tipping status tipping_reached
						$update_arr = array('deal_tipping_status' => 'tipping_reached', 'tipping_notified' => 0);
					}
				}
				else
				{
					# Update deal as tipping notified
					$update_arr = array('deal_status' => 'expired', 'tipping_notified' => 2);
				}
				if($deal->deal_status == 'closed')
				{
					$update_arr['deal_status'] = 'closed';
					$update_arr['tipping_notified'] = 2;
					$this->unFeatureDealByAdmin($deal->deal_id);
					// Remove feature request entry
					$this->removeFeatureRequestEntry($deal->deal_id, "Deal set as closed");
				}
				if(isset($update_arr['deal_status']) && $update_arr['deal_status'] == 'expired')
				{
					$this->unFeatureDealByAdmin($deal->deal_id);	// If deal expired then remove fetured entry.
					// Remove feature request entry
					$this->removeFeatureRequestEntry($deal->deal_id, "Deal set as closed");
				}
				if(COUNT($update_arr) > 0)
					DB::table('deal')->whereRaw('deal_id = ?', array($deal->deal_id))->update($update_arr);

				if($send_notification)
				{
					# Send mail to the seller
					$seller_details = \CUtil::getUserDetails($deal->user_id);
					$mail_det_arr = array();

					$mail_det_arr['to_email']			= $seller_details['email'];
					$mail_det_arr['user_name']			= $seller_details['display_name'];
					$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tip_failed_msg_to_seller');
					$mail_det_arr['tip_message'] 		= str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $mail_det_arr['tip_message']);
					$mail_det_arr['view_sales_link'] 	= Url::to('purchases/my-sales');
					$mail_det_arr['view_deal_link'] 	= $deal_url;
					$mail_det_arr['deal_id'] 			= $deal->deal_id;
					$mail_det_arr['deal_title'] 		= $deal->deal_title;
					$mail_det_arr['transaction_date'] 	= date('Y-m-d H:m:s');

					$subject = Lang::get('deals::deals.tipping_failed_notification_to_seller');
					$subject = str_replace("VAR_SITE_NAME",  \Config::get('generalConfig.site_name'), $subject);
					$template = "deals::emails.dealTippingStatusReachedNotificationToSeller";
					try {
						\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
							$m->to($mail_det_arr['to_email']);
							$m->subject($subject);
						});
					} catch (Exception $e) {
						//return false
						CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
					}
					# Send mail to the admin
					$mail_det_arr['to_email']			= \Config::get("generalConfig.invoice_email");
					$mail_det_arr['user_name']			= "Admin";
					$mail_det_arr['tip_message'] 		= Lang::get('deals::deals.deal_tip_failed_msg_to_admin');
					$mail_det_arr['order_txn_details'] 	= isset($txn_data) ? $txn_data : array();

					$subject = Lang::get('deals::deals.tipping_failed_notification_to_admin');
					$subject = str_replace("VAR_SITE_NAME", \Config::get('generalConfig.site_name'), $subject);
					$subject = str_replace("VAR_DEAL", $deal->deal_title, $subject);
					$template = "deals::emails.dealTippingStatusReachedNotificationAdmin";
					try {
						\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
							$m->to($mail_det_arr['to_email']);
							$m->subject($subject);
						});
					} catch (Exception $e) {
						//return false
						CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
					}
				}
			}
		}
	}

	public function fetchFeaturedApprovalDetailsByID($request_id)
	{
		$details = array();
		if($request_id)
		{
			$details = DB::table('deal_featured')->where('request_id', $request_id)->first();
			if(COUNT($details) > 0)
			{
				return $details;
			}
		}
		return $details;
	}

	public function fetchPaymentTypeForTxn($order_id)
	{
		$payment_type = DB::table('shop_order')->where('id', $order_id)->pluck('payment_gateway_type'); // paypal / wallet

		return strtolower($payment_type);
	}

	public function getDealUserDetailsById($deal_id)
	{
		$dealUserDetails = array();

		$dealUser = DB::table('deal')->where('deal_id', $deal_id)->pluck('user_id');
		if($dealUser != '')
		{
			$uDet = \CUtil::getUserDetails($dealUser);
			$dealUserDetails['email'] = $uDet['email'];
			$dealUserDetails['user_name'] = $uDet['display_name'];
			$dealUserDetails['user_id'] = $uDet['user_id'];
		}
		return $dealUserDetails;
	}

	public function fetchDealInvoiceDetails($deal_id)
	{
		$inv_details_arr = array(); // invoice_id, item_id, view_item_url, qty, buyer,member_profile_url
		$rec = DB::table('shop_order');
		$rec->select('shop_order.id', 'shop_order.buyer_id', 'shop_order.date_created', 'shop_order_item.deal_id', 'shop_order_item.item_id', 'shop_order_item.item_qty');
		$rec->leftjoin('shop_order_item', 'shop_order.id', '=', 'shop_order_item.order_id');
		$rec->whereRaw('shop_order.order_status = \'payment_completed\' AND shop_order_item.deal_tipping_status = \'tipping_reached\' AND shop_order_item.deal_id = ? ', array($deal_id));
		$rec->orderBy('shop_order.id');

		$invoices = $rec->get();
		if(COUNT($invoices) > 0)
		{
			$inc  = 0;
			foreach($invoices AS $inv)
			{
				$inv_details_arr[$inc]['order_id'] = $inv->id;
				$inv_details_arr[$inc]['item_id'] = $inv->item_id;
				$inv_details_arr[$inc]['qty'] = $inv->item_qty;
				$uDet = \CUtil::getUserDetails($inv->buyer_id);
				$inv_details_arr[$inc]['buyer'] = $uDet['display_name'];
				$inv_details_arr[$inc]['buyer_profile_url'] = $uDet['profile_url'];
				$inv_details_arr[$inc]['view_link'] = URL::action('PurchasesController@getSalesOrderDetails', $inv->id);
				$inc++;
			}
		}
		return $inv_details_arr;
	}


	public function allowedToCancelPurchasedItem($deal_id, $item_qty)
	{
		// 1. check is deal with tipping qty > 0, if not return true
		// 2. purchasedqty - itemqty  >= tipping qty then true, false
		$deal_details = DB::table('deal')
						->select(DB::raw('sum(qty) as purchase_cnt, deal.tipping_qty_for_deal'))
						->join('deal_item_purchased_details', 'deal.deal_id', '=', 'deal_item_purchased_details.deal_id')
						->where('deal.deal_id', $deal_id)
						->first();

		if(COUNT($deal_details) > 0)
		{
			if($deal_details->tipping_qty_for_deal > 0)
			{
				$impact_qty = $deal_details->purchase_cnt - $item_qty;
				if($impact_qty >= $deal_details->tipping_qty_for_deal)
					return true;
				else
					return false;
			}
			return true;
		}
		return true;
	}


	public function allowToDownloadDealItem($deal_id, $item_id=0)
	{
		$deal_details = DB::table('deal')->where('deal_id', $deal_id)->where('tipping_qty_for_deal', '>', 0)->first();
		if(COUNT($deal_details) > 0)
		{
			if($deal_details->deal_tipping_status == 'tipping_reached')
				return true;
			return false;
		}
		return true;
	}


	public function formatDiscountPercentage($discount_percent)
	{
		$formatted_amt = "";
		if(is_numeric($discount_percent))
			$formatted_amt = number_format ($discount_percent, 2, '.','');
		else
			$formatted_amt = $discount_percent;
		$formatted_amt = str_replace(".00", "", $formatted_amt);
		return $formatted_amt;
	}


	public function getDealItemPurchasedRefundStatus($order_id, $item_id)
	{
		$refund_status = 0;
		$refund_status = DB::table('deal_item_purchased_details')->whereRaw('item_id = ? AND order_id = ?', array($item_id, $order_id))->pluck('is_refund_processed');
		return $refund_status;
	}


	public function updateDealPurchasedRefundStatus($data_arr)
	{
		// Add site transaction entry for user as amount creditted.
	//	$data_arr['deal_id'] = $deal_id;
	//	$data_arr['order_id'] = $order_id;
	//	$data_arr['item_id'] = $item_id;

		$order_details = DB::table('shop_order_item')->whereRaw('order_id = ? AND item_id = ? ', array($data_arr['order_id'], $data_arr['item_id']))->first();
		if(COUNT($order_details) > 0)
		{
			$admin_id = Config::get('generalConfig.admin_id');
			$buyer_details = \CUtil::getUserDetails($order_details->buyer_id);

			$refundAmount = $order_details->total_amount - $order_details->discount_ratio_amount;

			# Add site transaction entry for debit admin account - refund transaction
			$trans_obj = new \SiteTransactionHandlerService();
			$transaction_arr['date_added'] = new \DateTime;
			$transaction_arr['user_id'] = $admin_id;
			$transaction_arr['transaction_type'] = 'debit';
			$transaction_arr['amount'] = $refundAmount;	// order seller amount
			$transaction_arr['currency'] = \Config::get('generalConfig.site_default_currency');
			$transaction_arr['transaction_key'] = 'deal_tipping_failure';
			$transaction_arr['reference_content_table'] = 'order';
			$transaction_arr['reference_content_id'] = $data_arr['deal_id']; // order id
			$transaction_arr['related_transaction_id'] = $data_arr['order_id']; // deal id
			$transaction_arr['status'] = 'completed';
			$transaction_arr['payment_type'] = 'paypal';
			$transaction_arr['transaction_notes'] = 'Deal tip failed - buyer paypal amount debitted';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			# Add site transaction entry for refund buyer account
			$transaction_arr['user_id'] = $order_details->buyer_id;
			$transaction_arr['transaction_type'] = 'credit';
			$transaction_arr['transaction_notes'] = 'Deal tip failed - order paypal amount refunded';
			$trans_id = $trans_obj->addNewTransaction($transaction_arr);

			# Send mail to the buyer as amount refunded.
			$dealDet = $this->fetchDealDetailsById($data_arr['deal_id']);
			$buyer_details = \CUtil::getUserDetails($order_details->buyer_id);
			$mail_det_arr = array();
			$mail_det_arr['deal_id'] 			= $data_arr['deal_id'];
			$mail_det_arr['transaction_date'] 	= date('Y-m-d H:m:s');
			$mail_det_arr['deal_title'] 		= $dealDet['deal_title'];
			$mail_det_arr['view_deal_link'] 	= $dealDet['viewDealLink'];

			$mail_det_arr['to_email']			= $buyer_details['email'];
			$mail_det_arr['user_name']			= $buyer_details['display_name'];
			$tip_message 		= Lang::get('deals::deals.deal_tip_failed_paypal_refund_msg_to_buyer');
			$tip_message 		= str_replace("VAR_DEAL", $dealDet['deal_title'], $tip_message);
			$mail_det_arr['tip_message'] 		= str_replace("VAR_AMOUNT", $refundAmount, $tip_message);

			$mail_det_arr['view_order_link'] 	= URL::action('PurchasesController@getOrderDetails', $data_arr['order_id']);

			$template = "deals::emails.dealTippingStatusReachedNotificationToBuyer";
			$subject = Lang::get('deals::deals.tipping_failed_refunded_notification_to_buyer');
			$subject = str_replace("VAR_SITE_NAME", \Config::get('generalConfig.site_name'), $subject);
			$subject = str_replace("VAR_DEAL", $dealDet['deal_title'], $subject);
			try {
				\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr, $subject) {
					$m->to($mail_det_arr['to_email']);
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
			# update deal_item_purchased_details table - is_refund_processed to 2
			DB::table('deal_item_purchased_details')->whereRaw('deal_id = ? AND item_id = ? AND order_id = ? ', array($data_arr['deal_id'], $data_arr['item_id'], $data_arr['order_id']))->update(array('is_refund_processed' => 2));

		}
	}
}