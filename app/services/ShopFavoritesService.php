<?php

class ShopFavoritesService implements FavoriteInterface
{
	public function getRules()
	{
		return array('user_id' => 'required|min:1', 'shop_id' => 'required|min:1', 'shop_user_id' => 'required|min:1');
	}
	public function validate($inputs = array())
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$user_id = $inputs['user_id']; $shop_id = isset($inputs['shop_id'])?$inputs['shop_id']:0;
		$shop_user_id = isset($inputs['shop_user_id'])?$inputs['shop_user_id']:0;

		if($shop_user_id <=0 || $shop_id <=0 || $user_id <=0)
		{
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.select_valid_shop_products')));	exit;
		}

		if($logged_user_id != $user_id) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.you_are_not_authorized')));	exit;
		}
		if($user_id == $shop_user_id)
		{
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.can_not_favorite_own_shop')));	exit;
		}
		if(is_null($shop_id) || $shop_id == '' || $shop_id < 0) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.invalid_shop_have_been_selected')));exit;
		}
	}
	public function update($inputs = array())
	{
		$user_id = $inputs['user_id']; $shop_id = $inputs['shop_id']; $shop_user_id = $inputs['shop_user_id'];
		$favorite_det = $this->getSingleShopFavoriteDetails($shop_id, $user_id);
		if(!$favorite_det || count($favorite_det) <=0)
		{
			$input_arr= array();
			$input_arr['user_id'] = $user_id;
			$input_arr['shop_id'] = $shop_id;
			$input_arr['shop_user_id'] = $shop_user_id;

			$favorite_id = $this->addToFavorite($input_arr);
			if($favorite_id)
				echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.shop_added_favorite'), 'action_to_show' => 'remove'));//Lang::get("viewShop.remove_from_wishlist")
			else
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
		}
		else
		{
			$wishlist_id = $this->removeFromFavorite($favorite_det->id);
			echo json_encode(array(	'result'=>'success', 'success_msg'=>  trans('favorite.shop_removed_favorite'), 'action_to_show' => 'add' ));//Lang::get("viewShop.add_to_wishlist")
		}
	}
	public function getSingleShopFavoriteDetails($shop_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($shop_id) || $shop_id == '' || $shop_id <= 0 || $user_id == '' || $user_id <= 0)
			return false;
		$wishlist_details = ShopFavorites::where('user_id', '=', $user_id)->where('shop_id', '=', $shop_id)->first();
		return $wishlist_details;
	}
	public function addToFavorite($inputs)
	{
		$userShopwishlist =  new ShopFavorites();
		return $userShopwishlist->addNew($inputs);
	}
	public function removeFromFavorite($wishlist_id)
	{
		return ShopFavorites::where('id', '=', $wishlist_id)->delete();
	}
	public function isFavoriteShop($shop_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($shop_id) || $shop_id == '' || $shop_id <=0 || $user_id == '' || $user_id <= 0)
			return false;		
		$cache_key = 'IFSCK_'.$shop_id.'_'.$user_id;
		if (($wishlist_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$wishlist_details = ShopFavorites::where('user_id', '=', $user_id)->where('shop_id', '=', $shop_id)->count();
			HomeCUtil::cachePut($cache_key, $wishlist_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $wishlist_details;
	}
	public function favoriteShopIds($user_id = '', $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		$favorite_shop_ids = ShopFavorites::join('users', 'users.id', '=', 'shop_favorites.shop_user_id')
							->where('users.is_banned', '=', '0')
							->where('users.shop_status', '=', '1')
							->where('shop_favorites.user_id', '=', $user_id)->orderby('shop_favorites.id','desc');
		if(!is_null($limit) && $limit!='' && $limit > 0)
			$favorite_shop_ids = $favorite_shop_ids->take($limit);

		$favorite_shop_ids = $favorite_shop_ids->lists('shop_id');
		return $favorite_shop_ids;
	}
	public function getFavoriteDetails($user_id = null, $limit = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$favorite_shop_ids = $this->favoriteShopIds($user_id, $limit);
		return $favorite_shop_ids;

	}
	public function totalFavorites($user_id = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();
		$favorite_count = ShopFavorites::join('users', 'users.id', '=', 'shop_favorites.shop_user_id')
							->where('users.is_banned', '=', '0')
							->where('users.shop_status', '=', '1')
							->where('shop_favorites.user_id', '=', $user_id)->count();
		return $favorite_count;
	}
	public function totalFavoritesForShop($shop_id = null){
		if(is_null($shop_id) || $shop_id == '' || $shop_id <= 0)
			return false;
		$favorite_count = ShopFavorites::where('shop_id', '=', $shop_id)->count();
		return $favorite_count;
	}

	public function getListWhoFavoritesThisShop($shop_id = null, $res_type = 'get', $limit = 0){
		if(is_null($shop_id) || $shop_id == '' || $shop_id <= 0)
			return false;
		$favorite = ShopFavorites::where('shop_id', '=', $shop_id);
		if($res_type == 'paginate')
			$favorite = $favorite->paginate($limit);
		else {
			$favorite = $favorite->get();
			if($limit > 0)
				$favorite = $favorite->take($limit);
		}
		return $favorite;
	}
}