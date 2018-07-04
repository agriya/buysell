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
class FavoritesController extends BaseController
{
	function __construct()
	{
		parent::__construct();
    }

	public function getIndex()
	{
		$productService = new ProductService();
		$prod_obj = Products::initialize();
		$user_id = BasicCUtil::getLoggedUserId();

		$wishlistService = new WishlistService();
		$product_ids = $wishlistService->wishlistproductids($user_id);

		if(!empty($product_ids))
		{
			//$productService->buildWishlistProductQuery($prod_obj);
			$prod_obj->setFilterProductIdsIn($product_ids);
			$prod_obj->setProductPagination(3);
			$product_list = $prod_obj->getProductsList();
		}
		else
			$product_list = array();

		/*$perPage	= Config::get('webshoppack.paginate');
		$product_list = $q->paginate($perPage);*/
		$get_common_meta_values = Cutil::getCommonMetaValues('my-wishlist');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		
		return View::make('wishlistProducts', compact('product_list', 'productService', 'is_search_done', 'prod_obj','user_id'));
	}

	public function viewFavorites($user_code = null)
	{
		if(is_null($user_code) || $user_code == '')
			$user_code = BasicCUtil::getLoggedUserId();
		$user_id = CUtil::getUserId($user_code);
		if(!ctype_digit($user_id) || $user_id <= 0)
		{
			App::abort(404);
		}
		$user_arr = User::where('id', '=', $user_id)->first(array('id', 'created_at'));
		if(count($user_arr) <= 0)
		{
			App::abort(404);
		}
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$inputs = Input::all();
		$params['favorites'] = $favorites = isset($inputs['favorites'])?$inputs['favorites']:'product';
		$favoriteservice = App::make('FavoriteInterface', $params);
		$favorite_details = $favoriteservice->getFavoriteDetails($user_id);

		$collectionservice = new CollectionService();
		//echo "<pre>";print_r($favorite_details);echo "</pre>";exit;
		if(($favorite_details && !empty($favorite_details)) || $favorites=='product')
		{
			if($favorites == 'product')
			{
				/*$perPage = 5;
				$currentPage = Input::get('page') - 1;
				$pagedData = array_slice($favorite_details, $currentPage * $perPage, $perPage);
				$favorite_details = Paginator::make($pagedData, count($favorite_details), $perPage);*/
				$favorite_details = $favoriteservice->getFavoriteListByUserId($user_id);

				//Items i love
				$first_items[0]['list_id'] = 0;
	            $first_items[0]['list_name'] = Config::get('generalConfig.favorite_default_list_name');
	            $first_items[0]['user_id'] = $user_id;
	            $first_items[0]['status'] = 1;
	            $first_items[0]['created_at'] = DB::Raw('NOW()');
	            $first_items[0]['updated_at'] = DB::Raw('NOW()');
				$favorite_details = array_merge($first_items, $favorite_details);
			}

			if($favorites == 'shop')
			{
				$shop_obj = Products::initializeShops();
				$shop_obj->setFilterShopIdsIn($favorite_details);
				$shop_obj->setShopPagination(5);
				$favorite_details = $shop_obj->getShopList();
			}

			if($favorites == 'collection')
			{
				$collectionservice->setCollectionFilterIds($favorite_details);
				$favorite_details = $collectionservice->getCollectionsList('paginate', 5);
			}
			//echo "<pre>";print_r($favorite_details);echo "</pre>";exit;
			//$favorite_details = Paginator::make($items, $totalItems, $perPage);
		}
		$wishlistservice = new WishlistService();
		$productService = new ProductService();
		$shop_obj = Products::initializeShops();
		$userCircleService = new UserCircleService();
		$prod_obj = Products::initialize();


		$user_details = CUtil::getUserDetails($user_id);
		$user_image_details = CUtil::getUserPersonalImage($user_id,'small');
		if($favorites == 'product')
		{
			$get_common_meta_values = Cutil::getCommonMetaValues('my-favorite-product');
			if($get_common_meta_values)
			{
				$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
				$this->header->setMetaDescription($get_common_meta_values['meta_description']);
				$this->header->setMetaTitle($get_common_meta_values['meta_title']);
			}
		}
		elseif($favorites == 'shop')
		{
			$get_common_meta_values = Cutil::getCommonMetaValues('my-favorite-shop');
			if($get_common_meta_values)
			{
				$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
				$this->header->setMetaDescription($get_common_meta_values['meta_description']);
				$this->header->setMetaTitle($get_common_meta_values['meta_title']);
			}
		}
		elseif($favorites == 'collection')
		{
			$get_common_meta_values = Cutil::getCommonMetaValues('my-favorite-collection');
			if($get_common_meta_values)
			{
				$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
				$this->header->setMetaDescription($get_common_meta_values['meta_description']);
				$this->header->setMetaTitle($get_common_meta_values['meta_title']);
			}			
		}
		return View::make('viewFavorites', compact('favorite_details', 'favorites', 'user_details', 'user_id', 'logged_user_id', 'user_image_details', 'favoriteservice', 'wishlistservice', 'productService', 'shop_obj', 'userCircleService', 'prod_obj', 'collectionservice'));
	}

	public function viewFavoritesProducts($user_code = null)
	{
		if(is_null($user_code) || $user_code == '')
			$user_code = BasicCUtil::getLoggedUserId();
		$user_id = CUtil::getUserId($user_code);
		if(!ctype_digit($user_id) || $user_id <= 0)
		{
			App::abort(404);
		}
		$user_arr = User::where('id', '=', $user_id)->first(array('id', 'created_at'));
		if(count($user_arr) <= 0)
		{
			App::abort(404);
		}

		$inputs = Input::all();
		$list_id = isset($inputs['list_id']) ? $inputs['list_id'] : 0;

		if($list_id == '' || !ctype_digit($list_id) || $list_id < 0)
		{
			App::abort(404);
		}

		$list_arr = array('list_id' => 0, 'list_name' => Config::get('generalConfig.favorite_default_list_name'));
		if($list_id > 0) {
			$list_arr = ProductFavoritesList::whereRaw('list_id = ? AND user_id = ?', array($list_id, $user_id))->first(array('list_id', 'list_name'));
			if(count($list_arr) <= 0)
			{
				App::abort(404);
			}
		}

		$logged_user_id = BasicCUtil::getLoggedUserId();
		$prod_fav_service = new ProductFavoritesService;
		$favorite_details = $prod_fav_service->favoriteProductIdsByList($user_id, $list_id);

		//echo "<pre>";print_r($favorite_details);echo "</pre>";exit;
		if($favorite_details && !empty($favorite_details))
		{
			$perPage = 16;
			$currentPage = Input::get('page') - 1;
			$pagedData = array_slice($favorite_details, $currentPage * $perPage, $perPage);
			$favorite_details = Paginator::make($pagedData, count($favorite_details), $perPage);
			//echo "<pre>";print_r($favorite_details);echo "</pre>";exit;
			//$favorite_details = Paginator::make($items, $totalItems, $perPage);
		}

		$productService = new ProductService();
		$shop_obj = Products::initializeShops();

		$user_details = CUtil::getUserDetails($user_id);
		$user_image = CUtil::getUserPersonalImage($user_id,'small');
		return View::make('viewFavoriteProducts', compact('favorite_details', 'list_arr', 'user_details', 'user_image', 'user_id', 'logged_user_id', 'prod_fav_service', 'productService', 'shop_obj'));
	}

	public function postToggleFavorite()
	{
		$inputs = Input::all();
		$params['favorites'] = isset($inputs['favorites'])?$inputs['favorites']:'product';
		$favoriteservice = App::make('FavoriteInterface', $params);
		$rules = $favoriteservice->getRules();
		$validator = Validator::make($inputs,$rules);
		if(!$validator->fails())
		{
			$favoriteservice->validate($inputs);
			$favoriteservice->update($inputs);
		}
		else
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> Lang::get('favorite.select_valid_favorite_to_modify', array('favorite_name' => $params['favorites'])) ));
	}

	public function postToggleFavoriteList()
	{
		$inputs = Input::all();
		$rules = array('user_id' => 'required|min:1');
		$validator = Validator::make($inputs, $rules);
		if(!$validator->fails())
		{
			$prod_fav_service = new ProductFavoritesService();
			$d_arr = array();
			$user_id = $inputs['user_id'];
			$user_code = BasicCutil::setUserCode($user_id);
			$product_id = $inputs['product_id'];
			$block = isset($inputs['block']) ? $inputs['block'] : '';
			$list_details = $prod_fav_service->getFavoriteListByUserId($user_id);
			//print_r($list_details);exit;
			$d_arr['list_details'] = $list_details;
			$op_html = View::make('showListDropdownBlock', compact('d_arr', 'user_id', 'user_code', 'product_id', 'block', 'prod_fav_service'));
			echo 'success|~~|'.$op_html;
		}
		else {
			$error_msg = Lang::get('favorite.select_valid_favorite_to_modify', array('favorite_name' => trans('favorite.product')));
			echo 'error|~~|'.$error_msg;
		}
	}

	public function postAddFavoriteProductToList()
	{
		$inputs = Input::all();
		$rules = array('user_id' => 'required|min:1', 'product_id' => 'required|min:1', 'list_id' => 'required|min:1');
		$validator = Validator::make($inputs, $rules);
		if(!$validator->fails())
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_id = $inputs['user_id'];
			$product_id = isset($inputs['product_id']) ? $inputs['product_id'] : 0;
			$list_id = $inputs['list_id'];
			if($logged_user_id != $user_id) {
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.you_are_not_authorized')));exit;
			}
			if(is_null($product_id) || $product_id == '' || $product_id < 0) {
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.invalid_product_have_been_selected')));exit;
			}

			$prod_fav_service = new ProductFavoritesService();
			$favorite_det = $prod_fav_service->getSingleProductFavoriteDetailsByList($product_id, $user_id, $list_id);
			if(!$favorite_det || count($favorite_det) <= 0)
			{
				$input_arr= array();
				$input_arr['user_id'] = $user_id;
				$input_arr['product_id'] = $product_id;
				$input_arr['list_id'] = $list_id;
				$favorite_id = $prod_fav_service->addToFavorite($input_arr);
				if($favorite_id)
					echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.product_have_been_added'), 'action_to_show' => 'remove'));
				else
					echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
			}
			else
			{
				$wishlist_id = $prod_fav_service->removeFromFavorite($favorite_det->id);
				echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.product_have_been_removed'), 'action_to_show' => 'add' ));
			}
		}
		else {
			$error_msg = Lang::get('favorite.select_valid_favorite_to_modify', array('favorite_name' => trans('favorite.product')));
			echo json_encode(array(	'result'=>'error', 'error_msg'=> $error_msg));
		}
	}

	public function postAddFavoriteListAndFavProduct()
	{
		$inputs = Input::all();
		$rules = array('user_id' => 'required|min:1', 'product_id' => 'required|min:1', 'list_name' => 'required');
		$validator = Validator::make($inputs, $rules);
		if(!$validator->fails())
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_id = $inputs['user_id'];
			$list_name = $inputs['list_name'];
			$product_id = isset($inputs['product_id']) ? $inputs['product_id'] : 0;
			if($logged_user_id != $user_id) {
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.you_are_not_authorized')));exit;
			}
			if(is_null($product_id) || $product_id == '' || $product_id < 0) {
				echo json_encode(array(	'result'=>'failed', 'error_msg'=>  trans('favorite.invalid_product_have_been_selected')));exit;
			}

			$prod_fav_service = new ProductFavoritesService();
			$list_det = $prod_fav_service->getSingleProductFavoriteListDetails($user_id, $inputs['list_name']);
			if(!$list_det || count($list_det) <= 0)
			{
				$input_list_arr = array();
				$input_list_arr['user_id'] = $user_id;
				$input_list_arr['list_name'] = $list_name;
				$list_id = $prod_fav_service->addToFavoriteList($input_list_arr);
				if($list_id) {
					$input_arr = array();
					$input_arr['user_id'] = $user_id;
					$input_arr['product_id'] = $product_id;
					$input_arr['list_id'] = $list_id;
					$favorite_id = $prod_fav_service->addToFavorite($input_arr);
					if($favorite_id)
						echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.product_have_been_added'), 'action_to_show' => 'remove'));
					else
						echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
				}
				else
					echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
			}
			else
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.list_has_been_already_created'), 'action_to_show' => 'add' ));
			}
		}
		else {
			$error_msg = Lang::get('favorite.please_enter_favorite_name', array('favorite_name' => trans('favorite.product')));
			echo json_encode(array(	'result'=>'error', 'error_msg'=> $error_msg));
		}
	}

	public function postAddFavoriteList()
	{
		$inputs = Input::all();
		$rules = array('user_id' => 'required|min:1', 'list_name' => 'required');
		$validator = Validator::make($inputs, $rules);
		if(!$validator->fails())
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_id = $inputs['user_id'];
			$list_name = $inputs['list_name'];
			if($logged_user_id != $user_id) {
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.you_are_not_authorized')));exit;
			}

			$prod_fav_service = new ProductFavoritesService();
			$list_det = $prod_fav_service->getSingleProductFavoriteListDetails($user_id, $inputs['list_name']);
			if(!$list_det || count($list_det) <= 0)
			{
				$input_list_arr = array();
				$input_list_arr['user_id'] = $user_id;
				$input_list_arr['list_name'] = $list_name;
				$list_id = $prod_fav_service->addToFavoriteList($input_list_arr);
				if($list_id) {
					echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('favorite.list_has_been_created'), 'action_to_show' => 'remove'));
				}
				else
					echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.there_are_some_problem')));
			}
			else
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('favorite.list_has_been_already_created'), 'action_to_show' => 'add' ));
			}
		}
		else {
			$error_msg = Lang::get('favorite.please_enter_favorite_name', array('favorite_name' => trans('favorite.product')));
			echo json_encode(array(	'result'=>'error', 'error_msg'=> $error_msg));
		}
	}
}