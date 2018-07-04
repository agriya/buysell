<?php
class AdminManageBannerService
{
	public function getBannerSettings($id = 0)
 	{
 		if($id) {
 			return BannerImages::where('id', $id)->first();
 		}
 	}

 	public function buildBannerImageQuery()
	{
		return BannerImages::Select("id", "title", "content", "filename", "ext",
									 "width", "height", "large_width", "large_height", "server_url",
									 "display", "date_added")->orderBy('id','DESC');
	}

	public function updateBannerImage($input)
 	{
 		$uploadRes = '';
 		$fileds = array("title", "content", "display");
		$arr = array();
		foreach ($fileds as $value) {
			if(isset($input[$value]) && $input[$value] != '') {
				$arr[$value] = $input[$value];
			}
		}
		if($input['settings_id']) {
			BannerImages::where('id',$input['settings_id'])->update($arr);
			$image_id = $input['settings_id'];
		}
		else {
			$arr['date_added'] = DB::Raw('Now()');
			$image_id = BannerImages::insertGetId($arr);
		}
		$file = Input::file('image_name');

		if($file != '')
		{
			$image_ext = $file->getClientOriginalExtension();
			$image_name = Str::random(20);
			$destinationpath = URL::asset(Config::get("generalConfig.banner_image_folder"));
			$uploadRes = $this->uploadBannerImage($file, $image_ext, $image_name, $destinationpath, $image_id);
		}
		$cache_key = 'banner_image_key';
		HomeCUtil::cacheForgot($cache_key);
		return $uploadRes;
 	}

 	public function uploadBannerImage($file, $image_ext, $image_name, $destinationpath, $image_id)
	{
		$errMsg = '';

		$folder_path = Config::get('generalConfig.banner_image_folder');
		$obj = new UserAccountService();
		$obj->chkAndCreateFolder($folder_path);
		try {
			// open file a image resource
			Image::make($file->getRealPath())->save(Config::get("generalConfig.banner_image_folder").$image_name.'.'.$image_ext);
			list($width, $height)= getimagesize($file);

			$large_width = Config::get('generalConfig.banner_image_large_width');
			$large_height = Config::get('generalConfig.banner_image_large_height');
			if(isset($large_width) && isset($large_height)) {
				Image::make($file->getRealPath())
					->resize($large_width, $large_height,true, false)
					->save($folder_path.$image_name.'_L.'.$image_ext);
			}
			list($large_width, $large_height) = getimagesize($folder_path.$image_name.'_L.'.$image_ext);

			$image_details = BannerImages::where('id', $image_id)->first();
			if($image_details['filename']) {
				$this->deleteBannerImageFiles($image_details->filename, $image_details->ext, $folder_path);
			}

			$arr = array('ext' => $image_ext,
							'filename' => $image_name,
							'width' => $width,
							'height' => $height,
							'large_width' => $large_width,
							'large_height' => $large_height,
							'server_url' => $destinationpath);
			BannerImages::where('id', $image_id)->update($arr);
			$cache_key = 'banner_image_key';
			HomeCUtil::cacheForgot($cache_key);
			return '';
		}
		catch (Exception $e) {

			$errMsg = $e->getMessage();
		}
		return $errMsg;
	}

	public function deleteIndexSlider($id)
	{
		if($id) {
			$folder_path = Config::get('generalConfig.banner_image_folder');
			$image_details = BannerImages::where('id', $id)->first();
			if($image_details->filename != '' && $image_details->ext != '')
				$this->deleteBannerImageFiles($image_details->filename, $image_details->ext, $folder_path);
			BannerImages::where('id', $id)->delete();
			$cache_key = 'banner_image_key';
			HomeCUtil::cacheForgot($cache_key);
		}
	}

	public function deleteBannerImageFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_L.".$ext))
			unlink($folder_name.$filename."_L.".$ext);
		if (file_exists($folder_name.$filename.".".$ext))
			unlink($folder_name.$filename.".".$ext);
	}
}