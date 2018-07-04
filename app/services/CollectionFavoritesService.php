<?php

class CollectionFavoritesService implements FavoriteInterface
{
	public function getRules()
	{
		return array('user_id' => 'required|min:1', 'collection_id' => 'required|min:1', 'collection_owner_id' => 'required|min:1');
	}
	public function validate($inputs = array())
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$user_id = $inputs['user_id']; $collection_id = isset($inputs['collection_id'])?$inputs['collection_id']:0;
		$collection_owner_id = isset($inputs['collection_owner_id'])?$inputs['collection_owner_id']:0;

		if($collection_owner_id <=0 || $collection_id <=0 || $user_id <=0)
		{
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> 'Please select valid collection and user details to favorite'));	exit;
		}

		if($logged_user_id != $user_id) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> 'You are not authorized to change favorite for another user'));	exit;
		}
		if($user_id == $collection_owner_id)
		{
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> 'You can not favorite your own collection'));	exit;
		}
		if(is_null($collection_id) || $collection_id == '' || $collection_id < 0) {
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> 'Invalid collection have been selected'));exit;
		}
	}
	public function update($inputs = array())
	{
		$user_id = $inputs['user_id']; $collection_id = $inputs['collection_id']; $collection_owner_id = $inputs['collection_owner_id'];
		$favorite_det = $this->getSingleCollectionFavoriteDetails($collection_id, $user_id);
		if(!$favorite_det || count($favorite_det) <=0)
		{
			$input_arr= array();
			$input_arr['user_id'] = $user_id;
			$input_arr['collection_id'] = $collection_id;
			$input_arr['collection_owner_id'] = $collection_owner_id;

			$favorite_id = $this->addToFavorite($input_arr);
			if($favorite_id)
				echo json_encode(array(	'result'=>'success', 'success_msg'=> 'Collection have been added to favorite', 'action_to_show' => 'remove'));//Lang::get("viewCollection.remove_from_wishlist")
			else
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> 'There are some problem. Please try again later'));
		}
		else
		{
			$wishlist_id = $this->removeFromFavorite($favorite_det->id);
			echo json_encode(array(	'result'=>'success', 'success_msg'=> 'Collection have been removed from favorite', 'action_to_show' => 'add' ));//Lang::get("viewCollection.add_to_wishlist")
		}
	}
	public function getSingleCollectionFavoriteDetails($collection_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($collection_id) || $collection_id == '' || $collection_id <= 0 || $user_id == '' || $user_id <= 0)
			return false;

		$wishlist_details = CollectionFavorites::where('user_id', '=', $user_id)->where('collection_id', '=', $collection_id)->first();
		return $wishlist_details;
	}
	public function addToFavorite($inputs)
	{
		$userCollectionwishlist =  new CollectionFavorites();
		return $userCollectionwishlist->addNew($inputs);
	}
	public function removeFromFavorite($wishlist_id)
	{
		return CollectionFavorites::where('id', '=', $wishlist_id)->delete();
	}
	public function isFavoriteCollection($collection_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($collection_id) || $collection_id == '' || $collection_id <=0 || $user_id == '' || $user_id <= 0)
			return false;

		$wishlist_details = CollectionFavorites::where('user_id', '=', $user_id)->where('collection_id', '=', $collection_id)->count();
		return $wishlist_details;
	}
	public function favoriteCollectionIds($user_id = '', $limit = null)
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)
			return array();

		$favorite_collection_ids = CollectionFavorites::where('user_id', '=', $user_id)->orderby('id','desc');
		if(!is_null($limit) && $limit!='' && $limit > 0)
			$favorite_collection_ids = $favorite_collection_ids->take($limit);

		$favorite_collection_ids = $favorite_collection_ids->lists('collection_id');
		return $favorite_collection_ids;
	}
	public function getFavoriteDetails($user_id = null, $limit = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$favorite_collection_ids = $this->favoriteCollectionIds($user_id, $limit);
		return $favorite_collection_ids;

	}
	public function totalFavorites($user_id = null){
		if(is_null($user_id) || $user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();
		$favorite_count = CollectionFavorites::where('user_id', '=', $user_id)->count();
		return $favorite_count;
	}

}