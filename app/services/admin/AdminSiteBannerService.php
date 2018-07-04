<?php
class AdminSiteBannerService
{
	public function getSiteBannerDetails($id = 0)
 	{
 		if($id) {
 			return Advertisement::where('add_id', $id)->first();
 		}
 	}

 	public function buildSiteBannerQuery()
	{
		return Advertisement::Select("add_id", "user_id", "post_from", "block", "about", "source", "start_date", "end_date", "allowed_impressions",
									 "completed_impressions", "status", "date_added")->orderBy('add_id','DESC');
	}

	public function updateSiteBanner($input)
 	{
 		$logged_user_id = BasicCUtil::getLoggedUserId();
 		$fileds = array("block", "about", "source", "start_date", "end_date", "allowed_impressions", "completed_impressions", "status");
		$arr = array();
		foreach ($fileds as $value) {
			if(isset($input[$value]) && $input[$value] != '') {
				$arr[$value] = $input[$value];
			}
		}
		$arr['post_from'] = 'Admin';
		$arr['user_id'] = $logged_user_id;
		if($input['banner_id']) {
			Advertisement::where('add_id', $input['banner_id'])->update($arr);
		}
		else {
			$arr['date_added'] = DB::raw('NOW()');
			$banner_id = Advertisement::insertGetId($arr);
		}
		$cache_key = 'banner_details_key';
		HomeCUtil::cacheForgot($cache_key);
 	}

	public function deleteSiteBanner($id)
	{
		if($id)
		{
			Advertisement::where('add_id', $id)->delete();
			$cache_key = 'banner_details_key';
			HomeCUtil::cacheForgot($cache_key);
		}
	}
}