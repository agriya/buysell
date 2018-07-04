<?php

class WishlistService
{
	public function getSingleProductWishlistDetails($product_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($product_id) || $product_id == '' || $product_id <= 0 || $user_id == '' || $user_id <= 0)	
			return false;

		$wishlist_details = UserProductsWishlist::where('user_id', '=', $user_id)->where('product_id', '=', $product_id)->first();
		return $wishlist_details;
	}
	public function addToWishlist($inputs)
	{
		$userproductwishlist =  new UserProductsWishlist();
		return $userproductwishlist->addNew($inputs);
	}
	public function removeFromWishlist($wishlist_id)
	{
		return UserProductsWishlist::where('id', '=', $wishlist_id)->delete();
	}
	public function isWishlistproduct($product_id = '', $user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if(is_null($product_id) || $product_id == '' || $product_id <=0 || $user_id == '' || $user_id <= 0)	
			return false;

		$wishlist_details = UserProductsWishlist::where('user_id', '=', $user_id)->where('product_id', '=', $product_id)->count();
		return $wishlist_details;
	}
	public function wishlistproductids($user_id = '')
	{
		if(is_null($user_id) || $user_id == '')
			$user_id = BasicCUtil::getLoggedUserId();

		if($user_id == '' || $user_id <= 0)	
			return array();

		$wishlist_product_ids = UserProductsWishlist::where('user_id', '=', $user_id)->lists('product_id','product_id');
		return $wishlist_product_ids;	
	}

}