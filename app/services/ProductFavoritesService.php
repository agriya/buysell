<?php

class ProductFavoritesService implements FavoriteInterface
{
	public function getRules()
	{
		return array('user_id' => 'required|min:1', 'product_id' => 'required|min:1');
	}
	public function validate($inputs = array())
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$user_id = $inputs['user_id']; $product_id = isset($inputs['product_id'])?$inputs['product_id']:0;
		if($logged_user_id != $user_id) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.you_are_not_authorized')));	exit;
		}
		if(is_null($product_id) || $product_id == '' || $product_id < 0) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.invalid_product_have_been_selected'))); exit;
		}
	}
	public function update($inputs = array())
	{
		$user_id = $inputs['user_id']; $product_id = $inputs['product_id'];
		$favorite_det = $this->getSingleProductFavoriteDetailsByList($product_id, $user_id);
		if(!$favorite_det || count($favorite_det) <=0)
		{
			$input_arr= array();
			$input_arr['user_id'] = $user_id;
			$input_arr['product_id'] = $product_id;

			$favorite_id = $this->addToFavorite($input_arr);
			if($favorite_id)
				echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.product_have_been_added'), 'action_to_show' => 'remove'));//Lang::get("viewProduct.remove_from_wishlist")
			else
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
		}
		else
		{
			$wishlist_id = $this->removeFromFavorite($favorite_det->id);
			echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.product_have_been_removed'), 'action_to_show' => 'add' ));//Lang::get("viewProduct.add_to_wishlist")
		}
	}
	public function getSingleProductFavoriteDetails($product_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($product_id) || $product_id == '' || $product_id <= 0 || $user_id == '' || $user_id <= 0)
			return false;

		$wishlist_details = ProductFavorites::where('user_id', '=', $user_id)->where('product_id', '=', $product_id)->first();
		return $wishlist_details;
	}
	public function addToFavorite($inputs)
	{
		$userproductwishlist =  new ProductFavorites();
		return $userproductwishlist->addNew($inputs);
	}
	public function removeFromFavorite($wishlist_id)
	{
		return ProductFavorites::where('id', '=', $wishlist_id)->delete();
	}
	public function isFavoriteProduct($product_id = '', $user_id = '')
	{
		$cache_key = 'FPC_'.$product_id;
		if(is_null($user_id) || $user_id == '')
		{
			$user_id = BasicCUtil::getLoggedUserId();
			$cache_key .= '_UI_'.$user_id;
		}

		if(is_null($product_id) || $product_id == '' || $product_id <=0 || $user_id == '' || $user_id <= 0)
			return false;
			
		if (($wishlist_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$wishlist_details = ProductFavorites::where('user_id', '=', $user_id)->where('product_id', '=', $product_id)->count();
			HomeCUtil::cachePut($cache_key, $wishlist_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $wishlist_details;
	}
	public function favoriteProductIds($user_id = '', $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		$cache_key = 'favorite_product_ids'.$user_id;
		if (($favorite_product_ids = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$favorite_product_ids = ProductFavorites::whereRaw('user_id = ? AND list_id = ?', array($user_id, 0))->orderby('id','desc');
			if(!is_null($limit) && $limit!='' && $limit > 0)
				$favorite_product_ids = $favorite_product_ids->take($limit);		
			$favorite_product_ids = $favorite_product_ids->lists('product_id');
			HomeCUtil::cachePut($cache_key, $favorite_product_ids, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $favorite_product_ids;
	}
	public function getFavoriteDetails($user_id = null, $limit = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$favorite_product_ids = $this->favoriteProductIds($user_id, $limit);
		//echo "<pre>";print_r($favorite_product_ids);echo "</pre>";exit;
		return $favorite_product_ids;

	}
	public function totalFavorites($user_id = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();
			$cache_key = 'product_favorite_count'.$user_id;
			if (($favorite_count = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$favorite_count = ProductFavorites::whereRaw('user_id = ? AND list_id = ?', array($user_id, 0))->count();
				HomeCUtil::cachePut($cache_key, $favorite_count, Config::get('generalConfig.cache_expiry_minutes'));
			}
		return $favorite_count;
	}

	public function totalFavoritesByProduct($product_id = ''){
		if(is_null($product_id) || $product_id == '' || $product_id <= 0)
			return false;
		$cache_key = 'TFBPCK_'.$product_id;
		if (($favorite_count = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$favorite_count = ProductFavorites::whereRaw('product_id = ?', array($product_id))->count();
			HomeCUtil::cachePut($cache_key, $favorite_count, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $favorite_count;
	}

	public function totalFavoritesByListId($user_id = null, $list_id = 0){
		if(is_null($user_id) || $user_id == '' || $user_id <= 0)
			return false;
		if(is_null($list_id) || $list_id < 0)
			return false;
		$favorite_count = ProductFavorites::join('product', 'product.id', '=', 'product_favorites.product_id')
								->join('users', 'users.id', '=', 'product.product_user_id')
								->whereRaw('product_favorites.user_id = ? AND list_id = ?', array($user_id, $list_id))
								->where('product.product_status', '!=', 'Deleted')
								->where('users.shop_status', '=', '1')
								->where('users.is_banned', '=', '0')->count();
		return $favorite_count;
	}

	public function favoriteProductListIds($user_id = '', $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		$fav_product_list_ids = ProductFavorites::whereRaw('user_id = ?', array($user_id))
													->groupBy('list_id')
													->orderby('list_id', 'ASC');
		if(!is_null($limit) && $limit!='' && $limit > 0)
			$fav_product_list_ids = $fav_product_list_ids->take($limit);

		$fav_product_list_ids = $fav_product_list_ids->lists('list_id');
		return $fav_product_list_ids;
	}

	public function getSingleProductFavoriteDetailsByList($product_id = '', $user_id = '', $list_id = 0)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($product_id) || $product_id == '' || $product_id <= 0 || $user_id == '' || $user_id <= 0)
			return false;

		if(is_null($list_id) || $list_id < 0)
			return false;

		$fav_details = ProductFavorites::whereRaw('user_id = ? AND product_id = ? AND list_id = ?', array($user_id, $product_id, $list_id))->first();
		return $fav_details;
	}

	public function favoriteProductIdsByList($user_id = '', $list_id = 0, $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		if(is_null($list_id) || $list_id < 0)
			return array();

		$favorite_product_ids = ProductFavorites::join('product', 'product.id', '=', 'product_favorites.product_id')
								->join('users', 'users.id', '=', 'product.product_user_id')
								->whereRaw('product_favorites.user_id = ? AND list_id = ?', array($user_id, $list_id))
								->where('product.product_status', '!=', 'Deleted')
								->where('users.shop_status', '=', '1')
								->where('users.is_banned', '=', '0')
								->orderby('product_favorites.id', 'desc');
		if(!is_null($limit) && $limit!='' && $limit > 0)
			$favorite_product_ids = $favorite_product_ids->take($limit);

		$favorite_product_ids = $favorite_product_ids->lists('product_id');
		return $favorite_product_ids;
	}

	public function getListName($list_id = 0)
	{
		if(is_null($list_id) || $list_id < 0)
			return false;

		if($list_id == 0)
			return Config::get('generalConfig.favorite_default_list_name');

		$list_name = ProductFavoritesList::whereRaw('list_id = ?', array($list_id))->pluck('list_name');
		return $list_name;
	}

	public function recentFavoriteProductIds($limit = null)
	{
		$favorite_product_ids = ProductFavorites::orderby('id','desc');
		if(!is_null($limit) && $limit!='' && $limit > 0)
			$favorite_product_ids = $favorite_product_ids->take($limit);

		$favorite_product_ids = $favorite_product_ids->lists('product_id');
		return $favorite_product_ids;
	}

	public function getFavoriteProductsList($limit = null)
	{
		$fav_prods = ProductFavorites::Select(DB::raw('product.*'))
							->join('product', 'product.id', '=', 'product_favorites.product_id')
							->join('users', 'users.id', '=', 'product.product_user_id')
							->where('product.product_status', '!=', 'Deleted')
							->where('users.shop_status', '=', '1')
							->where('users.is_banned', '=', '0')
							->groupBy('product_favorites.product_id')
							->orderBy('id', 'DESC');
		if($limit != '' && $limit > 0)
			$fav_prods = $fav_prods->paginate($limit);
		else
			$fav_prods = $fav_prods->get();
		return $fav_prods;
	}

	public function getFavoriteListByUserId($user_id = '', $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		$fav_list = ProductFavoritesList::Select('list_id' ,'list_name', 'user_id', 'status', 'created_at', 'updated_at')
							->whereRaw('user_id = ?', array($user_id))
							->orderBy('list_id', 'ASC');
		if($limit != '' && $limit > 0)
			$fav_list = $fav_list->paginate($limit);
		else
			$fav_list = $fav_list->get()->toArray();
		return $fav_list;
	}

	public function getSingleProductFavoriteListDetails($user_id = '', $list_name = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return false;

		$fav_list_details = ProductFavoritesList::whereRaw('user_id = ? AND list_name = ?', array($user_id, $list_name))->first();
		return $fav_list_details;
	}

	public function addToFavoriteList($inputs)
	{
		$fav_list =  new ProductFavoritesList();
		return $fav_list->addNew($inputs);
	}
}