<?php

class AdminSiteLogoController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
    }

    public function getIndex()
	{
		$image_details = CUtil::getSiteLogo();
		$logo_type = 'logo';
		return View::make('admin.siteLogo',compact('image_details','logo_type'));
	}
	public function postIndex()
	{
		if(!BasicCUtil::checkIsDemoSite())
		{
			$inputs = Input::all();
			if (Input::hasFile('attachment'))
			{
				if($_FILES['attachment']['error'])
					{
						$errMsg = trans("common.uploader_max_file_size_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
					}
					$allowed_ext = str_replace(' ', '', Config::get("generalConfig.sitelogo_allowed_extension"));
					$file = Input::file('attachment');
					$file_size = $file->getClientSize();
					$image_ext = $file->getClientOriginalExtension();
					$allowed_size = Config::get("generalConfig.sitelogo_allowed_file_size");
					$allowed_size = $allowed_size * 1024 * 1024; //To convert MB to Byte
					if(stripos($allowed_ext, $image_ext) === false)
					{
						$errMsg = trans("common.uploader_allow_format_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
						//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
					}
					else if(($file_size > $allowed_size)  || $file_size <= 0)
					{
						$errMsg = trans("common.uploader_max_file_size_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
						//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
					}
					else
					{
						$resize_image = "true";

						$image_id = "";
						$field_name = Input::get("field_name");
						$image_folder = Input::get("image_folder");
						$image_name = Str::random(20);
						$destinationpath = URL::asset(Config::get("generalConfig.sitelogo_folder"));
						$upload_input = array();
						$upload_input['image_ext'] = $image_ext;
						$upload_input['image_name'] = $image_name;
						$upload_input['image_server_url'] = $destinationpath;

						$image_id = $this->uploadSiteLogo($file, $image_ext, $image_name, $destinationpath);
						if($image_id > 0)
						{
							$success_message = Lang::get('admin/siteLogo.logo_update_success');
							$cache_key = 'site_logo_cache_key';
							if(HomeCUtil::cacheForgot($cache_key))
							{
								$sitelogo = SiteLogo::where('id','>','0')->where('logo_type', 'logo')->first();
								HomeCUtil::cachePut($cache_key, $sitelogo);
							}
							return Redirect::action('AdminSiteLogoController@getIndex')->with('success_message',$success_message);
						}
						else
						{
							$errMsg = trans("common.uploader_invalid_img_err_msg");
							return Redirect::back()->withInput()->with('error_message',$errMsg);
							//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
						}
					}
			}
			$errMsg = Lang::get('admin/siteLogo.logo_file_empty_error');
		}
		else
		{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
		}
		return Redirect::back()->withInput()->with('error_message',$errMsg);
	}

	public function getFavIcon()
	{
		$image_details = CUtil::getSiteLogo('favicon');
		$logo_type = 'favicon';
		return View::make('admin.siteLogo',compact('image_details', 'logo_type'));
	}
	public function postFavIcon()
	{
		if(!BasicCUtil::checkIsDemoSite())
		{
			$inputs = Input::all();
			if (Input::hasFile('attachment'))
			{
				if($_FILES['attachment']['error'])
					{
						$errMsg = trans("common.uploader_max_file_size_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
					}
					$allowed_ext = Config::get("generalConfig.sitefavicon_allowed_extension");
					$file = Input::file('attachment');
					$file_size = $file->getClientSize();
					$image_ext = $file->getClientOriginalExtension();
					$allowed_size = Config::get("generalConfig.sitefavicon_allowed_file_size");
					$allowed_size = $allowed_size * 1024; //To convert KB to Byte

					list($width,$height)= getimagesize($file);
					$large_width = Config::get('generalConfig.sitefavicon_width');
					$large_height = Config::get('generalConfig.sitefavicon_height');

					if(stripos($allowed_ext, $image_ext) === false)
					{
						$errMsg = trans("common.uploader_allow_format_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
						//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
					}
					else if(($file_size > $allowed_size)  || $file_size <= 0)
					{
						$errMsg = trans("common.uploader_max_file_size_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
						//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
					}
					elseif($width!=$large_width || $height!=$large_height)
					{
						$errMsg = trans("common.uploader_dimension_err_msg");
						return Redirect::back()->withInput()->with('error_message',$errMsg);
					}
					else
					{


						$resize_image = "true";

						$image_id = "";
						$field_name = Input::get("field_name");
						$image_folder = Input::get("image_folder");
						$image_name = Str::random(20);
						$destinationpath = URL::asset(Config::get("generalConfig.sitefavicon_folder"));
						$upload_input = array();
						$upload_input['image_ext'] = $image_ext;
						$upload_input['image_name'] = $image_name;
						$upload_input['image_server_url'] = $destinationpath;

						$image_id = $this->uploadSiteLogo($file, $image_ext, $image_name, $destinationpath,'favicon');
						if($image_id > 0)
						{
							$success_message = Lang::get('admin/siteLogo.logo_update_success');
							$cache_key = 'site_logo_favicon_cache_key';
							if(HomeCUtil::cacheForgot($cache_key))
							{
								$sitelogo = SiteLogo::where('id','>','0')->where('logo_type', 'favicon')->first();
								HomeCUtil::cachePut($cache_key, $sitelogo);
							}
							return Redirect::action('AdminSiteLogoController@getFavIcon')->with('success_message',$success_message);
						}
						else
						{
							$errMsg = trans("common.uploader_invalid_img_err_msg");
							return Redirect::back()->withInput()->with('error_message',$errMsg);
							//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
						}
					}
			}
			$errMsg = Lang::get('admin/siteLogo.logo_file_empty_error');
		}
		else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
		}

		return Redirect::back()->withInput()->with('error_message',$errMsg);
	}


	public function uploadSiteLogo($file, $image_ext, $image_name, $destinationpath, $logo_type='logo')
	{
		if($logo_type == 'favicon')
		{
			$config_path = Config::get('generalConfig.sitefavicon_folder');
			$large_width = Config::get('generalConfig.sitefavicon_width');
			$large_height = Config::get('generalConfig.sitefavicon_height');
		}
		else
		{
			$config_path = Config::get('generalConfig.sitelogo_folder');
			$large_width = Config::get('generalConfig.sitelogo_width');
			$large_height = Config::get('generalConfig.sitelogo_height');
		}
		$this->chkAndCreateFolder($config_path);

		// open file a image resource


		if($logo_type!='favicon')
		{
			Image::make($file->getRealPath())->save($config_path.$image_name.'_O.'.$image_ext);
			list($width,$height)= getimagesize($file);
			list($upload_img['width'], $upload_img['height']) = getimagesize(base_path().'/public/'.$config_path.$image_name.'_O.'.$image_ext);


			if(isset($large_width) && isset($large_height))
			{
				$img_size = CUtil::DISP_IMAGE($large_width, $large_height, $upload_img['width'], $upload_img['height'], true);

				Image::make($file->getRealPath())
					->resize($large_width, $large_height, false)
					->save($config_path.$image_name.'_L.'.$image_ext);
			}

			$img_path = Request::root().'/'.$config_path;
			list($upload_input['large_width'], $upload_input['large_height']) = getimagesize($img_path.$image_name.'_L.'.$image_ext);
		}
		else
		{
			//Input::file('attachment')->move($config_path, $image_name.'.'.$image_ext);
			$file->move($config_path, $image_name.'_L.'.$image_ext);
			$img_path = Request::root().'/'.$config_path;
			list($upload_input['large_width'], $upload_input['large_height']) = getimagesize($img_path.$image_name.'_L.'.$image_ext);
		}
		$site_logo = new SiteLogo();

		$user_data = array(	'logo_image_ext' => $image_ext,
							'logo_image_name' => $image_name,
							'logo_server_url' => $destinationpath,
							'logo_height' => $upload_input['large_height'],
                            'logo_width' => $upload_input['large_width'],
							'logo_type' => $logo_type
							);

		$user_image_details = SiteLogo::where('id', '>', 0);
		if($logo_type=='favicon')
		{
			$user_image_details->where('logo_type','favicon');
		}
		else
		{
			$user_image_details->where('logo_type','logo');
		}
		$user_image_details = $user_image_details->first();
		if(count($user_image_details) > 0)
		{
			$this->deleteLogoFiles($user_image_details->logo_image_name, $user_image_details->logo_image_ext, $config_path);
			SiteLogo::where('id', $user_image_details->id)->update($user_data);
			$id = $user_image_details->id;
		}
		else
		{
			$id = $site_logo->insertGetId($user_data);
		}
		return $id;
	}
	public function deleteLogoFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_L.".$ext))
			unlink($folder_name.$filename."_L.".$ext);
		if (file_exists($folder_name.$filename."_O.".$ext))
			unlink($folder_name.$filename."_O.".$ext);
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




}