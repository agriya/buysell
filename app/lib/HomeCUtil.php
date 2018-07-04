<?php
/**
 * Basic Common Utils
 *
 * @package
 * @author Ahsan
 * @copyright Copyright (c) 2014
 * @version $Id$
 * @access public
 */
class HomeCUtil
{
	/*
	 * Method to strip tags globally.
	 */
	public static function globalXssClean()
	{
	    // Recursive cleaning for array [] inputs, not just strings.
	    $sanitized = static::arrayStripTags(Input::get());
	    Input::merge($sanitized);
	}

	public static function arrayStripTags($array)
	{
	    $result = array();
	    foreach ($array as $key => $value) {
	        // Don't allow tags on key either, maybe useful for dynamic forms.
	        $key = strip_tags($key);

	        // If the value is an array, we will just recurse back into the
	        // function to keep stripping the tags out of the array,
	        // otherwise we will set the stripped value.
	        if (is_array($value)) {
	            $result[$key] = static::arrayStripTags($value);
	        } else {
	            // I am using strip_tags(), you may use htmlentities(),
	            // also I am doing trim() here, you may remove it, if you wish.
	            $result[$key] = trim(strip_tags($value, '<div><b><strong><i><em><a><ul><ol><li><p><br><span><img>'));
	        }
	    }
	    return $result;
	}

	public static function populateRecentFavoriteProducts()
	{
		$fav_products = array();
		$cache_key = 'users_favorites_products_key';
		if (($users_favorites_products = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$users_favorites_products = UsersFavoritesProducts::Select("favorite_id", "product_id", "date_added")
																->get()->toArray();
			HomeCUtil::cachePut($cache_key, $users_favorites_products);
		}
		if(count($users_favorites_products) > 0) {
			$rand_keys = array_keys($users_favorites_products);
			$users_favorites_products[0]['product_id'];
			$product_service = new ProductService;
			$feedback_service = new ProductFeedbackService;
			$prod_obj = Products::initialize();
			$shop_obj = Products::initializeShops();
			$key = 0;
			$cnt = 0;
			foreach($users_favorites_products as $randkey => $val) {
				$rand_key = array_rand($rand_keys, 1);
				unset($rand_keys[$rand_key]);

				$product_id = $users_favorites_products[$rand_key]['product_id'];
				$cache_key = 'users_products_key_'.$product_id;
				if (($product = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$product = Product::select(DB::raw('product.*'))
													->join('users', 'product.product_user_id', '=', 'users.id')
													->where('product.product_status', '=', 'Ok')
													->where('users.is_banned', '=', 0)
													->where('users.shop_status', '=', 1)
													->WhereRaw('product.id = ?', array($product_id))
													->first();
					HomeCUtil::cachePut($cache_key, $product, Config::get('generalConfig.cache_expiry_minutes'));
				}
				if(count($product) > 0) {
					$product = $product->toArray();
					$fav_product['product_name'] = $product['product_name'];
					$fav_product['product_url'] = $product_service->getProductViewURL($product_id, $product);
					$fav_product['product_price'] = $product_service->formatProductPriceNew($product);
					//$fav_product['product_price'] = array('disp_price' =>0, 'disp_discount'=>0);
					$fav_product['is_free_product'] = $product['is_free_product'];

					$p_img_arr = $prod_obj->getProductImage($product_id);
					$p_thumb_img = $product_service->getProductDefaultThumbImage($product_id, 'large', $p_img_arr);
					$fav_product['p_thumb_img'] = $p_thumb_img;
					$fav_products[$key]['fav_product'] = $fav_product;

					//$feed_back = $feedback_service->getFeedbackCountBySellerId($product['product_user_id']);
					//$feed_back_cnt = array_sum($feed_back);
					//$feed_back_rate = CUtil::calculateFeedbackRate($feed_back);
					//$fav_products[$key]['feed_back_cnt'] = $feed_back_cnt;
					//$fav_products[$key]['feed_back_rate'] = $feed_back_rate;

					$feed_back = $feedback_service->getAvgRatingForSeller($product['product_user_id']);
					$fav_products[$key]['feed_back_cnt'] = $feed_back['rating_count'];
					$fav_products[$key]['feed_back_rate'] = $feed_back['avg_rating'];

					//Seller details
					$seller_id = $product['product_user_id'];
					$seller_details = CUtil::getIndexUserDetails($seller_id);
					$fav_products[$key]['seller_shop_details'] = $shop_obj->getShopDetails($seller_id);
					$fav_products[$key]['seller_id'] = $seller_id;
					$fav_products[$key]['seller_details'] = $seller_details;
					$fav_products[$key]['seller_image'] = CUtil::getUserPersonalImage($seller_id, "small");

					$prod_obj->setFilterProductStatus('Ok');
					$prod_obj->setFilterProductExpiry(true);
					$prod_obj->setProductsLimit(3);
					$seller_products = $prod_obj->getProductsList($seller_id);
					if(count($seller_products) > 0) {
						$seller_products = $seller_products->toArray();
						foreach($seller_products as $key2 => $prod) {
							$seller_products[$key2]['product_url'] = $product_service->getProductViewURL($prod['id'], $prod);
							$p_img_arr = $prod_obj->getProductImage($prod['id']);
							$p_thumb_img = $product_service->getProductDefaultThumbImage($prod['id'], 'small', $p_img_arr);
							$seller_products[$key2]['p_thumb_img'] = $p_thumb_img;
						}
					}
					$fav_products[$key]['seller_products'] = $seller_products;
					$fav_products[$key]['seller_products_total'] = $prod_obj->getTotalProducts($seller_id);
					$key++;

					$cnt++;
				}
				if ($cnt >= 3) break;
			}
		}
		if (count($fav_products) > 3) {
			$rand_keys = array_rand($fav_products, 3);
			foreach($rand_keys as $each_key){
				$rand_products[$each_key] = $fav_products[$each_key];
			}
			$fav_products = $rand_products;
		}
		return $fav_products;
	}

	public static function populateTopPickers()
	{
		$top_picks = array();
		$cache_key = 'users_top_picks_key';
		if (($users_top_picks = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$users_top_picks = UsersTopPicks::Select("users_top_picks.top_pick_id", "users_top_picks.user_id", "users_top_picks.date_added")
										->join('users', 'users_top_picks.user_id', '=', 'users.id')
										->where('users.is_banned', '=', 0)
										->where('users.shop_status', '=', 1)
										->get()->toArray();
			HomeCUtil::cachePut($cache_key, $users_top_picks);
		}
		if(count($users_top_picks) > 0) {
			$rand_keys = array_keys($users_top_picks);
			$prod_fav_service = new ProductFavoritesService;
			$product_service = new ProductService;
			$prod_obj = Products::initialize();
			$key = $cnt = 0;
			foreach($users_top_picks as $randkey => $val) {
				$rand_key = array_rand($rand_keys, 1);
				unset($rand_keys[$rand_key]);

				$fav_product = array();
				$user_id = $users_top_picks[$rand_key]['user_id'];
				$user_details = CUtil::getIndexUserDetails($user_id);
				if ($user_details) {
					$top_picks[$key]['user_id'] = $user_id;
					$top_picks[$key]['user_details'] = $user_details;
					$top_picks[$key]['user_image'] = CUtil::getUserPersonalImage($user_id, "small");
					$top_picks[$key]['user_total_fav_produts'] = $prod_fav_service->totalFavorites($user_id);

					$fav_prod_ids = $prod_fav_service->favoriteProductIds($user_id, 4);
					if($fav_prod_ids && !empty($fav_prod_ids)) {
						foreach($fav_prod_ids as $pkey => $product_id) {
							$p_img_arr = $prod_obj->getProductImage($product_id);
							$p_thumb_img = $product_service->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
							$fav_product[$pkey]['p_thumb_img'] = $p_thumb_img;
						}
					}
					$top_picks[$key]['fav_product'] = $fav_product;
					$key++;

					$cnt++;
				}
				if ($cnt > 3) break; 	// Home page will show 4 users randomly picked.
			}
		}
		if (count($top_picks) > 4) {
			$rand_keys = array_rand($top_picks, 4);
			foreach($rand_keys as $each_key){
				$rand_picks[$each_key] = $top_picks[$each_key];
			}
			$top_picks = $rand_picks;
		}
		return $top_picks;
	}

	public static function getFeaturedSellersIndex()
	{
		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$featured_sellers_service = new FeaturedSellersService();
			return $featured_sellers_service->getFeaturedSellers(8, true);
		}
		return;
	}

	public static function getFeaturedSellersAfterLoginIndex()
	{
		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$featured_sellers_service = new FeaturedSellersService();
			return $featured_sellers_service->getFeaturedSellersAfterLogin(6);
		}
		return;
	}

	public static function getRecentFavorites($user_id = null)
	{
		if(is_null($user_id))
			return '';

		$recent_fav = $fav_product = array();
		$user_code = BasicCutil::setUserCode($user_id);
		$prod_fav_service = new ProductFavoritesService;
		$product_service = new ProductService;

		$fav_prod_ids = $prod_fav_service->favoriteProductIds($user_id, 6);
		if($fav_prod_ids && !empty($fav_prod_ids)) {
			foreach($fav_prod_ids as $pkey => $product_id) {
				$fav_products = Products::initialize();
				$fav_products->setProductId($product_id);
				//$fav_products->setFilterProductStatus('Ok');
				//$fav_products->setIncludeDeleted(true);
				//$fav_products->setIncludeBlockedUserProducts(true);
				$product_details = $fav_products->getProductDetails();
				if(count($product_details) > 0) {
					$view_url = $product_service->getProductViewURL($product_id, $product_details);
					$p_img_arr = $fav_products->getProductImage($product_id);
					$p_thumb_img = $product_service->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);

					$fav_product[$pkey]['p_product_details'] = $product_details;
					$fav_product[$pkey]['p_view_url'] = $view_url;
					$fav_product[$pkey]['p_thumb_img'] = $p_thumb_img;
				}
			}
		}
		$recent_fav['fav_product'] = $fav_product;
		$recent_fav['fav_product_total'] = count($fav_product);//$prod_fav_service->totalFavorites($user_id);
		$recent_fav['fav_product_url'] = Url::to('favorite/'.$user_code.'?favorites=product');

		return $recent_fav;
	}

	public static function showFeaturedProductIcon($p_id, $p_details = array())
	{
		$featured_icon = '';
		if(CUtil::chkIsAllowedModule('featuredproducts')) {
			if(!isset($p_details['id']) && $p_id > 0) {
				$product = Products::initialize($p_id);
				$p_details = $product->getProductDetails();
			}
			if(count($p_details) > 0) {
				if($p_details['is_featured_product'] == 'Yes' && strtotime($p_details['featured_product_expires']) >= strtotime(date('Y-m-d'))) {
					$featured_icon = '<span class="featured-blk" title="'.Lang::get('featuredproducts::featuredproducts.featured').'"><small>'.Lang::get('featuredproducts::featuredproducts.featured').'</small></span>';
				}
			}
		}
		return $featured_icon;
	}

	public static function showFeaturedSellersIcon($user_id, $user_details = array())
	{
		$featured_icon = '';
		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			if(!isset($user_details['id']) && $user_id > 0) {
				$cache_key = 'SFSICK_'.$user_id;
				if (($user_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$user_details = User::select('id', 'is_featured_seller', 'featured_seller_expires')->where('id', $user_id)->first();
					HomeCUtil::cachePut($cache_key, $user_details, Config::get('generalConfig.cache_expiry_minutes'));
				}
			}
			if(count($user_details) > 0) {
				if($user_details['is_featured_seller'] == 'Yes' && strtotime($user_details['featured_seller_expires']) >= strtotime(date('Y-m-d'))) {
					$featured_icon = '<span class="featured-seller" title="'.Lang::get('featuredsellers::featuredsellers.featured').'"><span class="featrseller-title">'.Lang::get('featuredsellers::featuredsellers.featured').'</span></span>';
				}
			}
		}
		return $featured_icon;
	}

	public static function getIndexFeaturedSellers()
	{
		$result_arr = array();
		$cache_key = 'featured_seller_banner_key';
		if (($all_users_featured = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$all_users_featured = UsersFeatured::Select(DB::raw('users_featured.*'))
												->join('users', 'users_featured.user_id', '=', 'users.id')
												->where('users.is_banned', '=', 0)
												->where('users.shop_status', '=', 1)
												->get()->toArray();
			HomeCUtil::cachePut($cache_key, $all_users_featured);
		}
		if ($all_users_featured) {
			$i = 0;
			$rand_keys = array_keys($all_users_featured);

			$cnt  = 0;
			foreach($all_users_featured as $users_featured) {
				$rand_key = array_rand($rand_keys, 1);
				unset($rand_keys[$rand_key]);

				$i++;
				$product_service = new ProductService;
				$prod_obj = Products::initialize();
				$shop_obj = Products::initializeShops();
				$featured_seller_id = $all_users_featured[$rand_key]['user_id'];
				$featured_seller_details = CUtil::getIndexUserDetails($featured_seller_id);
				if ($featured_seller_details) {
					$result_arr['seller_shop_details'] = $shop_obj->getShopDetails($featured_seller_id);
					$result_arr['seller_id'] = $featured_seller_id;
					$result_arr['seller_details'] = $featured_seller_details;
					$result_arr['seller_image'] = CUtil::getUserPersonalImage($featured_seller_id, "small");

					$prod_obj->setFilterProductStatus('Ok');
					$prod_obj->setFilterProductExpiry(true);
					$prod_obj->setProductsLimit(4);
					$featured_seller_products = $prod_obj->getProductsList($featured_seller_id);
					if(count($featured_seller_products) > 0) {
						$featured_seller_products = $featured_seller_products->toArray();
						foreach($featured_seller_products as $key => $prod) {
							$featured_seller_products[$key]['product_url'] = $product_service->getProductViewURL($prod['id'], $prod);
							$p_img_arr = $prod_obj->getProductImage($prod['id']);
							$p_thumb_img = $product_service->getProductDefaultThumbImage($prod['id'], 'small', $p_img_arr);
							$featured_seller_products[$key]['p_thumb_img'] = $p_thumb_img;
						}
					}
					$result_arr['seller_products'] = $featured_seller_products;
					$result_arr['seller_products_total'] = $prod_obj->getTotalProducts($featured_seller_id);
					$cnt++;
					break;
				}
			}
		}
		return $result_arr;
	}

	/**
	 * CUtil::getIndexBannerImage()
	 *
	 * @param mixed $user_id
	 * @param string $image_size
	 * @param mixed $cache
	 * @return
	 */
	public static function getIndexBannerImage()
	{
		$banner_exists = false;
		$banner_details = array();
		$cache_key = 'banner_image_key';
		if (($all_banner_images = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$all_banner_images = BannerImages::whereRaw('display = ?', array('1'))->get();
			HomeCUtil::cachePut($cache_key, $all_banner_images);
		}
		$image_details["image_title"] = 'Banner';
		if ($all_banner_images) {
			$i = 0;
			$rand = rand(1, count($all_banner_images));
			foreach($all_banner_images as $banner_images){
				$i++;
				if($i == $rand && $banner_images->filename != '')
				{
					$banner_exists = true;
					$image_details["image_id"] = $banner_images->id;
					$image_details["image_title"] = $banner_images->title;
					$image_details["image_content"] = $banner_images->content;
					$image_details["image_name"] = $banner_images->filename;
					$image_details["image_ext"] = $banner_images->ext;
					$image_details["image_width"] = $banner_images->width;
					$image_details["image_height"] = $banner_images->height;
					$image_details["image_large_width"] = $banner_images->large_width;
					$image_details["image_large_height"] = $banner_images->large_height;
					$image_details["image_server_url"] = $banner_images->server_url;
					$image_details["image_folder"] = Config::get("generalConfig.banner_image_folder");
				}
			}
		}
		$image_details["image_exists"] = $banner_exists;

		$cfg_user_img_large_width = Config::get("generalConfig.banner_image_large_width");
		$cfg_user_img_large_height = Config::get("generalConfig.banner_image_large_height");

		$image_src = URL::asset("images/no_image").'/banner-1600x420.jpg';
		$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $cfg_user_img_large_width, $cfg_user_img_large_height);
		if($banner_exists){
			$image_path = URL::asset(Config::get("generalConfig.banner_image_folder"))."/";
			$image_src =  $image_path . $image_details["image_name"]."_L.".$image_details["image_ext"];
			$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $image_details["image_large_width"], $image_details["image_large_height"]);
		}
		$image_details['image_src'] = $image_src;
		$image_details['image_attr'] = $image_attr;
		return $image_details;
	}

	public static function cachePut($key, $value, $minutes=0)
	{
		if(HomeCUtil::cacheAllowed())
		{
			if ($value === NULL) $value = array();
			if($minutes != '0')
				Cache::put($key, $value, $minutes);
			else
				Cache::forever($key, $value);
			return true;
		}
		return false;
	}

	public static function cacheForgot($cache_key)
	{
		if(HomeCUtil::cacheAllowed() || $cache_key == 'banner_details_key' || $cache_key == 'config_data_key')
		{
			if(Cache::has($cache_key))
		    	Cache::forget($cache_key);
			return true;
		}
		return false;
	}

	public static function forgotMultiCacheKey($keys)
	{
		if(HomeCUtil::cacheAllowed())
		{
			foreach($keys as $cache_key){
				if(Cache::has($cache_key))
				   Cache::forget($cache_key);
			}
			return true;
		}
		return false;
	}

	public static function cacheGet($cache_key)
	{
		if(HomeCUtil::cacheAllowed())
		{
			if(Cache::has($cache_key)) {
				HomeCUtil::logit('FROM CACHE $cache_key: '.$cache_key.' for '.Request::url());
				return Cache::get($cache_key);
			} else
				HomeCUtil::logit('------ NOT CACHED $cache_key: '.$cache_key.' for '.Request::url());
		}
		return NULL;
	}

	public static function cacheAllowed()
	{
		$return_text = false;
		if(Config::get('generalConfig.cache') == 1)
			$return_text = true;
		return $return_text;
	}

	public static function logit($writingContent)
	{
		return true;
		/* Writting to file */
		if(file_exists('/home/nidevdev/html/buysell/app/logs/cache-log.txt')) {
			File::append('/home/nidevdev/html/buysell/app/logs/cache-log.txt', htmlentities($writingContent."\n"));
		}
		/* Writting to file */
	}

}
?>