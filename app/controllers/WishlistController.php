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
class WishlistController extends BaseController
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

	public function postToggleWishlist()
	{
		$inputs = Input::all();
		$rules = array('user_id' => 'required', 'product_id' => 'required');
		$validator = Validator::make($inputs,$rules);
		if(!$validator->fails())
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_id = Input::get('user_id'); $product_id = Input::get('product_id');
			if($logged_user_id != $user_id)
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('common.not_authorize')));	exit;
			}
			if(is_null($product_id) || $product_id == '' || $product_id < 0)
			{
				echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('wishlistProduct.invalid_product_id')));exit;
			}
			$wishlistService = new WishlistService();
			$wihslist_det = $wishlistService->getSingleProductWishlistDetails(Input::get('product_id'), Input::get('user_id'));
			if(!$wihslist_det)
			{
				$input_arr= array();
				$input_arr['user_id'] = $user_id;
				$input_arr['product_id'] = $product_id;
				$input_arr['created_at'] = date('Y-m-d');
				$input_arr['updated_at'] = date('Y-m-d');
				$wishlist_id = $wishlistService->addToWishlist($input_arr);
				if($wishlist_id)
					echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('wishlistProduct.product_added_wishlist_success'), 'action_to_show' => 'remove'));//Lang::get("viewProduct.remove_from_wishlist")
				else
					echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('common.some_problem_try_later')));
			}
			else
			{
				$wishlist_id = $wishlistService->removeFromWishlist($wihslist_det->id);
				echo json_encode(array(	'result'=>'success', 'success_msg'=> trans('wishlistProduct.product_removed_wishlist_success'), 'action_to_show' => 'add' ));//Lang::get("viewProduct.add_to_wishlist")
			}
		}
		else
			echo json_encode(array(	'result'=>'failed', 'error_msg'=> trans('wishlistProduct.invalid_product_id')));
	}

}