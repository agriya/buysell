<?php

class UserCircleService
{
	public function getSingleUserCircleDetails($circle_user_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($circle_user_id) || $circle_user_id == '' || $circle_user_id <= 0 || $user_id <= 0)
			return false;

		$wishlist_details = UsersCircle::where('user_id', '=', $user_id)->where('circle_user_id', '=', $circle_user_id)->first();
		return $wishlist_details;
	}
	public function addToUserCircle($inputs)
	{
		$usersCircle =  new UsersCircle();
		return $usersCircle->addNew($inputs);
	}
	public function removeFromUserCircle($usercircle_id)
	{
		return UsersCircle::where('id', '=', $usercircle_id)->delete();
	}
	public function isUserInCircle($circle_user_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($circle_user_id) || $circle_user_id == '' || $circle_user_id <=0 || $user_id == '' || $user_id <= 0)
			return false;

		$cache_key = 'IUICCK'.$circle_user_id.'_'.$user_id;
		if (($wishlist_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$wishlist_details = UsersCircle::where('user_id', '=', $user_id)->where('circle_user_id', '=', $circle_user_id)->count();
			HomeCUtil::cachePut($cache_key, $wishlist_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $wishlist_details;
	}
	public function numberOfMembersInCircle($user_id = null){
		if(is_null($user_id))
			return 0;
		$cache_key = 'NOMICCK'.$user_id;
		if (($user_circle_count = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$user_circle_count = UsersCircle::where('circle_user_id', '=', $user_id)->count();
			HomeCUtil::cachePut($cache_key, $user_circle_count, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $user_circle_count;
	}

	public function getCircleUsers($user_id = '', $circle_type = 'followers'){
		if(is_null($user_id) || $user_id == '')
			return array();
		$userids = array();
		if($circle_type == 'followers')
			$userids = UsersCircle::where('circle_user_id', '=', $user_id)->lists('user_id');
		else
			$userids = UsersCircle::where('user_id', '=', $user_id)->lists('circle_user_id');
		return $userids;
	}

}