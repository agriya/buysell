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
class UsersCircleController extends BaseController
{

	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
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

	public function viewCircle($user_code = null){

		if(is_null($user_code) || $user_code == '')
			$user_code = BasicCUtil::getLoggedUserId();
		$user_id = CUtil::getUserId($user_code);
		if($user_id<=0)
		{
			App::abort(404);
		}
		$user_arr = User::where('id', '=', $user_id)->first(array('id', 'created_at'));
		if(count($user_arr) <= 0)
		{
			App::abort(404);
		}

		$inputs = Input::all();
		$circle_type = (isset($inputs['circle_type']) && $inputs['circle_type']!='')?$inputs['circle_type']:'followers';
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$user_details = CUtil::getUserDetails($user_id);
		$user_image_details = CUtil::getUserPersonalImage($user_id,'large');

		$userCircleService = new UserCircleService();
		$userService = new UserAccountService();
		$productService = new ProductService();

		$user_ids = $userCircleService->getCircleUsers($user_id, $circle_type);

		$userService->setUserIds($user_ids);
		$users_list = array();
		if(count($user_ids) > 0) {
			$users_list = $userService->getUsersList($inputs,'paginate',10);
		}
		if(count($users_list) > 0)
		{
			$favoriteservice = App::make('FavoriteInterface', array('favorites' => 'product'));
			foreach($users_list as $user)
			{
				$user->favorite_products = $favoriteservice->getFavoriteDetails($user->id, 2);
				$user->total_favorites = $favoriteservice->totalFavorites($user->id);
				$user->profile_image = CUtil::getUserPersonalImage($user->id, 'small');
				$user->members_in_circle = $userCircleService->numberOfMembersInCircle($user->id);
			}

		}
		$get_common_meta_values = Cutil::getCommonMetaValues('circle');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('viewCircle', compact('users_list', 'user_details', 'user_id', 'logged_user_id', 'user_image_details', 'userCircleService', 'productService'));




	}

	public function postToggleUserCircle()
	{
		$inputs = Input::all();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if( !isset($inputs['user_id']) || (isset($inputs['user_id']) && $inputs['user_id']=='') )
			$inputs['user_id'] = $logged_user_id;
		$rules = array('user_id' => 'required', 'circle_user_id' => 'required');
		$validator = Validator::make($inputs,$rules);
		if(!$validator->fails())
		{
			//$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_id = Input::get('user_id'); $circle_user_id = Input::get('circle_user_id');
			if($logged_user_id != $user_id)
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('common.not_authorize')));	exit;
			}
			if(is_null($circle_user_id) || $circle_user_id == '' || $circle_user_id < 0)
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('myaccount/viewProfile.invalid_user')));exit;
			}
			$userCircleService = new UserCircleService();
			$usercircle_det = $userCircleService->getSingleUserCircleDetails($circle_user_id, $user_id);
			if(!$usercircle_det || count($usercircle_det) <=0)
			{
				$input_arr= array();
				$input_arr['user_id'] = $user_id;
				$input_arr['circle_user_id'] = $circle_user_id;
				//$input_arr['created_at'] = date('Y-m-d');
				//$input_arr['updated_at'] = date('Y-m-d');
				$usercircle_id = $userCircleService->addToUserCircle($input_arr);
				if($usercircle_id)
					echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('myaccount/viewProfile.added_to_circle'), 'action_to_show' => 'remove'));//Lang::get("viewProduct.remove_from_wishlist")
				else
					echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('common.some_problem_try_later')));
			}
			else
			{
				$wishlist_id = $userCircleService->removeFromUserCircle($usercircle_det->id);
				echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('myaccount/viewProfile.removed_from_circle'), 'action_to_show' => 'add' ));//Lang::get("viewProduct.add_to_wishlist")
			}
		}
		else
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('myaccount/viewProfile.invalid_user')));
	}

}