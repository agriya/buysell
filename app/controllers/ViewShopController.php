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
class ViewShopController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->viewShopService = new ViewShopService();
	}

    public function getIndex($url_slug)
    {
    	$shop_obj = Products::initializeShops();
    	$product_obj = Products::initialize();
    	$product_obj->setFilterProductStatus('Ok');
    	$product_obj->setFilterProductExpiry(true);

    	$service_obj =  new ProductService();

    	//Get user id from url slug
    	$this->viewShopService->setUserId($shop_obj, $url_slug);

    	$shop_owner_id = $this->viewShopService->shop_owner_id;

		if($shop_owner_id <= 0)
			App::abort(404);

    	//Get shop details
    	$shop_obj->setFilterShopOwnerId($shop_owner_id);
    	//$shop_obj->setIncludeBlockedUserShop(false);
    	$shop_details = $shop_obj->getShopDetailsWithFilter();

    	//Get shop status
    	$shop_status = $shop_err_msg = '';
    	$shop_details_extra = $shop_obj->getUsersShopDetails($shop_owner_id);
    	if($shop_details_extra) {

    		$shop_status = $shop_details_extra['shop_status'];
    		if($shop_details_extra['is_banned'] == 1) {
    			$shop_err_msg = trans('shop.shopowner_blocked');
    			$shop_status = false;
    		}
			else if($shop_details_extra['shop_status'] == 0) {
    			$shop_err_msg = trans('shop.shop_blocked');
    			$shop_status = false;
    		}
    	}

    	//Get total products of shop owner
    	$total_products = $this->viewShopService->getTotalProducts($product_obj);
    	$product_sales_count = $this->viewShopService->getShopProductSalesCount($product_obj);

    	//Get product section details
    	$default_section_details = $this->viewShopService->getDefaultsectionDetails($url_slug);
    	$section_details = $product_obj->getShopProductSectionDetails($shop_owner_id);

		if(Input::get("section_id") != ""){
			if(is_numeric(Input::get("section_id"))){
				$product_obj->setFilterSectionId(Input::get("section_id"));
			}
		}

		$product_obj->setFilterProductName(Input::get('search_product_name'));
		$service_obj->setProductOrderBy($product_obj, Input::get('orderby_field'));
		$product_obj->setProductPagination(12);
		$product_details = $product_obj->getProductsList($shop_owner_id);

    	$shop_view_url = URL::to('shop/'.$url_slug);
    	$shop_fav_view_url = URL::to('shop/favorites/'.$url_slug);

		$viewShopServiceObj = $this->viewShopService;
		$feedback_service = new ProductFeedbackService();
		$shop_fav_service = new ShopFavoritesService();
		$prod_fav_service = new ProductFavoritesService;
		$logged_user_id = $this->logged_user_id;
		$view_type = (Input::has('view_type')) ? Input::get('view_type') : 'grid';
		$is_search_done = $this->viewShopService->checkIsSearchDone(Input::all());
		$get_common_meta_values = Cutil::getCommonMetaValues('view-shop');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_keyword']));
			$this->header->setMetaDescription(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_description']));
			$this->header->setMetaTitle(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_title']));
		}
    	return View::make('viewShop', compact('shop_details', 'shop_status', 'default_section_details', 'section_details', 'shop_view_url', 'viewShopServiceObj', 'service_obj', 'product_details', 'breadcrumb_arr', 'url_slug', 'shop_obj', 'product_obj', 'feedback_service', 'shop_fav_service', 'logged_user_id', 'view_type', 'is_search_done', 'shop_fav_view_url', 'prod_fav_service', 'shop_err_msg', 'rating_det'));
	}

	public function getShopPolicy($url_slug)
	{
		$shop_obj = Products::initializeShops();
		$product_obj = Products::initialize();
		$product_obj->setFilterProductStatus('Ok');
		$product_obj->setFilterProductExpiry(true);

		$this->viewShopService = new ViewShopService();
    	//Get user id from url slug
    	$this->viewShopService->setUserId($shop_obj, $url_slug);

    	$shop_owner_id = $this->viewShopService->shop_owner_id;

		if($shop_owner_id <= 0)
			App::abort(404);

		$shop_details = $shop_obj->getShopDetails($shop_owner_id);

		$shop_status = $shop_err_msg = '';
    	$shop_details_extra = $shop_obj->getUsersShopDetails($shop_owner_id);
    	if($shop_details_extra) {
    		$shop_status = $shop_details_extra['shop_status'];
    		if($shop_details_extra['is_banned'] == 1)
    			$shop_err_msg = trans('shop.shopowner_blocked');
			if(!$shop_status)
    			$shop_err_msg = trans('shop.shop_blocked');
    	}
		$viewShopServiceObj = $this->viewShopService;

    	$shop_view_url = URL::to('shop/'.$url_slug);
    	$shop_fav_view_url = URL::to('shop/favorites/'.$url_slug);

    	$total_products = $this->viewShopService->getTotalProducts($product_obj);
    	$product_sales_count = $this->viewShopService->getShopProductSalesCount($product_obj);

    	//Get product section details
    	$default_section_details = $this->viewShopService->getDefaultsectionDetails($url_slug);
    	$section_details = $product_obj->getShopProductSectionDetails($shop_owner_id);

    	//echo "<br>product_sales_count: ".$product_sales_count;
    	//echo "<br>product_sales_count: ".$product_sales_count;

		$feedback_service = new ProductFeedbackService;
		$shop_fav_service = new ShopFavoritesService();
		$logged_user_id = $this->logged_user_id;
		$get_common_meta_values = Cutil::getCommonMetaValues('view-shop-policy');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_keyword']));
			$this->header->setMetaDescription(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_description']));
			$this->header->setMetaTitle(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_title']));
		}
		return View::make('viewShopPolicy', compact('shop_details', 'shop_status', 'viewShopServiceObj', 'shop_obj', 'default_section_details', 'section_details', 'shop_view_url', 'feedback_service', 'shop_fav_service', 'logged_user_id', 'shop_fav_view_url', 'shop_err_msg'));
	}

	public function getShopReviews($url_slug)
	{
		$shop_obj = Products::initializeShops();
		$product_obj = Products::initialize();
		$product_obj->setFilterProductStatus('Ok');
		$product_obj->setFilterProductExpiry(true);

		$this->viewShopService = new ViewShopService();
    	//Get user id from url slug
    	$this->viewShopService->setUserId($shop_obj, $url_slug);

    	$shop_owner_id = $this->viewShopService->shop_owner_id;

		if($shop_owner_id <= 0)
			App::abort(404);

		$shop_details = $shop_obj->getShopDetails($shop_owner_id);

		$shop_status = $shop_err_msg = '';
    	$shop_details_extra = $shop_obj->getUsersShopDetails($shop_owner_id);
    	if($shop_details_extra) {
    		$shop_status = $shop_details_extra['shop_status'];
    		if($shop_details_extra['is_banned'] == 1)
    			$shop_err_msg = trans('shop.shopowner_blocked');
			if(!$shop_status)
    			$shop_err_msg = trans('shop.shop_blocked');
    	}
		$viewShopServiceObj = $this->viewShopService;

    	$shop_view_url = URL::to('shop/'.$url_slug);
    	$shop_fav_view_url = URL::to('shop/favorites/'.$url_slug);

    	$total_products = $this->viewShopService->getTotalProducts($product_obj);
    	$product_sales_count = $this->viewShopService->getShopProductSalesCount($product_obj);

    	//Get product section details
    	$default_section_details = $this->viewShopService->getDefaultsectionDetails($url_slug);
    	$section_details = $product_obj->getShopProductSectionDetails($shop_owner_id);

		$feedback_service = new ProductFeedbackService;
    	$feed_back = $feedback_service->getFeedbackCountBySellerId($shop_owner_id);
		$feed_back_cnt = array_sum($feed_back);
		$feed_back_rate = 0;
		$positive_feed = $feed_back['Positive'] + $feed_back['Neutral'];
		if($positive_feed > 0) {
			$feed_back_rate = round( ($feed_back_cnt / $positive_feed ) * 100 );
		}
		$d_arr['feed_back_cnt'] = $feed_back_cnt;
		$d_arr['feed_back_rate'] = $feed_back_rate;
		//$d_arr['prod_fav_cnt'] = $productFavoritesService->totalFavoritesByProduct($p_details['id']);
		$feed_back_list = $feedback_service->getFeedbackListBySellerId($shop_owner_id, Config::get('generalConfig.prod_view_review_list_count'), true);
		//$d_arr['is_favorite_product'] = $productFavoritesService->isFavoriteProduct($p_details['id'], $logged_user_id);
		$shop_fav_service = new ShopFavoritesService();
		$productService = new ProductService();
		$logged_user_id = $this->logged_user_id;
		$get_common_meta_values = Cutil::getCommonMetaValues('view-shop-reviews');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_keyword']));
			$this->header->setMetaDescription(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_description']));
			$this->header->setMetaTitle(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_title']));
		}
		return View::make('viewShopReviews', compact('shop_details', 'shop_status', 'viewShopServiceObj', 'shop_obj', 'default_section_details', 'section_details', 'shop_view_url', 'feedback_service', 'shop_fav_service', 'logged_user_id', 'shop_fav_view_url', 'd_arr', 'productService', 'feed_back_list', 'shop_err_msg'));
	}

	public function viewFavorites($url_slug = null)
	{
		$shop_fav_service = new ShopFavoritesService();
		$product_service =  new ProductService();
		$prod_fav_service = new ProductFavoritesService;
		$product_obj = Products::initialize();
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$shop_obj = Products::initializeShops();
		//Get user id from url slug
    	$this->viewShopService->setUserId($shop_obj, $url_slug);
    	$shop_owner_id = $this->viewShopService->shop_owner_id;

		if($shop_owner_id <= 0)
			App::abort(404);

    	//Get shop details
    	$shop_obj->setFilterShopOwnerId($shop_owner_id);
    	$shop_details = $shop_obj->getShopDetailsWithFilter();

    	//Get shop status
    	$shop_status = $shop_err_msg = '';
    	$shop_details_extra = $shop_obj->getUsersShopDetails($shop_owner_id);
    	if($shop_details_extra) {
    		$shop_status = $shop_details_extra['shop_status'];
    		if($shop_details_extra['is_banned'] == 1)
    			$shop_err_msg = trans('shop.shopowner_blocked');
			if(!$shop_status)
    			$shop_err_msg = trans('shop.shop_blocked');
    	}

		$shop_view_url = URL::to('shop/'.$url_slug);

		$list_whos_fav_shop = $shop_fav_service->getListWhoFavoritesThisShop($shop_details['id'], 'paginate', 10);

		$viewShopServiceObj = $this->viewShopService;
    	$logged_user_id = $this->logged_user_id;
		$get_common_meta_values = Cutil::getCommonMetaValues('view-shop-favorites');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_keyword']));
			$this->header->setMetaDescription(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_description']));
			$this->header->setMetaTitle(str_replace("SHOP_NAME", $shop_details['shop_name'], $get_common_meta_values['meta_title']));
		}
    	return View::make('viewShopFavorites', compact('shop_details', 'shop_status', 'list_whos_fav_shop', 'shop_view_url', 'logged_user_id', 'shop_fav_service', 'product_service', 'product_obj', 'prod_fav_service', 'shop_err_msg', 'viewShopServiceObj'));
	}
}
?>