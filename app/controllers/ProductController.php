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
class ProductController extends BaseController {

	function __construct()
	{
		parent::__construct();
	}

	public static function showCategoryList($cat_id = 0)
	{
		if($cat_id == 0) {
			App::abort(404);
		}
		$product_service = new ProductService();
		$prod_obj = Products::initialize();
		$shop_obj = Products::initializeShops();
		$root_category_id = Products::getRootCategoryId();
    	$category_id = ($cat_id == '' || $cat_id == 0) ? $root_category_id : $cat_id;

		$category_list = Products::getCategoriesList($category_id);
		$active_cat_details = Products::getCategoryDetails($category_id);
		$list_products = true;
		if(count($active_cat_details) > 0) {

			if($category_id != $root_category_id) {
	    		$category_details = $active_cat_details;

	    		$meta_title = (isset($category_details['category_meta_title']) && $category_details['category_meta_title']!='')?$category_details['category_meta_title']:'VAR_SITE_NAME - Marketplace - '.$category_details['category_name'];
	    		$meta_title = str_replace(array('VAR_TITLE'), array($category_details['category_name']), $meta_title);
	    		$meta_keyword = (isset($category_details['category_meta_keyword']) && $category_details['category_meta_keyword']!='')?$category_details['category_meta_keyword']:'';
	    		$meta_description = (isset($category_details['category_meta_description']) && $category_details['category_meta_description']!='')?$category_details['category_meta_description']:'';

	    		$header  = App::make('Header');
	    		$header->setMetaTitle($meta_title);
	    		if($meta_keyword!='')
			    	$header->setMetaKeyword($meta_keyword);

			    if($meta_description!='')
			    	$header->setMetaDescription($meta_description);
	    	}

			if($active_cat_details->category_level == 1)
				$list_products = false;
			if(count($category_list) == 0)
					$category_list = Products::getCategoriesList($active_cat_details->parent_category_id);
		}
		if($list_products) {
			$products = Product::Select(DB::raw('product.*'));
			$products = $products->join('users', function($join)
			                         {
			                             $join->on('product.product_user_id', '=', 'users.id');
			                             $join->where('users.is_banned', '=', 0);
			                             $join->where('users.shop_status', '=', 1);
			                         });
			$products = $products->whereRaw('product.product_category_id = ?', array($category_id))
								  ->whereRaw('product.product_status = ?', array('Ok'));

			$order_by = Input::has('order_by') ? Input::get('order_by') : 'all';
			$order_by_field = '';
			if($order_by != '') {
				if($order_by == 'all')	{
					$order_by_field = 'product.id';
				}
				else if($order_by == 'most_sold') {
					$order_by_field = 'product_sold';
					$products = $products->whereRaw("(product.product_sold > 0)");
				}
				else if($order_by == 'recently_added') {
					$order_by_field = 'date_activated';
				}
				else if($order_by == 'price') {
					$order_by_field = 'discount';
					$products->LeftJoin('product_price_groups', function($join)
                     {
                         $join->on('product.id', '=', 'product_price_groups.product_id');
                     });
					$products = $products->whereRaw("product_price_groups.group_id = 0");
					$products = $products->whereRaw("product_price_groups.range_start = 1 AND product_price_groups.range_end = -1");
				}
				else if($order_by == 'free') {
					$products = $products->whereRaw("( product.is_free_product = 'Yes')");
				}
			}
			if($order_by_field != '') {
				if($order_by_field == 'product_sold') {
					$products->orderby(DB::raw('FIELD(is_free_product, \'No\')'), 'desc');
				}
				$products = $products->orderBy($order_by_field, 'DESC');
			}
			$products = $products->paginate(20);
			//echo '<pre>';print_r($products);exit;
			return View::make('categoryItemsListing', compact('category_list', 'cat_id', 'active_cat_details', 'prod_obj', 'shop_obj', 'product_service', 'products', 'order_by'));
		}
		else {
			$sub_category_ids = $prod_obj->getSubCategoryIds($category_id);
			if(($key = array_search($category_id, $sub_category_ids)) !== false) {
			    unset($sub_category_ids[$key]);
			}
			$products = array();
			if(count($sub_category_ids) > 0) {
				$cache_key = 'SCLCK'.serialize($sub_category_ids);
				if (($products = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$products = Product::select('product.id', 'product.product_category_id')->join('users', 'product.product_user_id', '=', 'users.id')
										->whereIn('product.product_category_id', $sub_category_ids)
										->whereRaw('users.is_banned = ?', array(0))
										->whereRaw('users.shop_status = ?', array(1))
										->whereRaw('product.product_status = ?', array('Ok'))
										->groupBy('product.product_category_id')
										->take(11)->get()->toArray();
					HomeCUtil::cachePut($cache_key, $products, Config::get('generalConfig.cache_expiry_minutes'));
				}
			}
			return View::make('categorySubItemsListing', compact('category_list', 'cat_id', 'active_cat_details', 'prod_obj', 'product_service', 'products'));
		}
	}

	public static function showList($cat_id = 0)
	{
		$productService = new ProductService();
		$product = Products::initialize();
		$shop_obj = Products::initializeShops();
		$prod_fav_service = new ProductFavoritesService();

    	$root_category_id = Products::getRootCategoryId();
    	$category_id = ($cat_id == '' || $cat_id == 0) ? $root_category_id : $cat_id;

    	if($category_id != $root_category_id)
    	{
    		$category_details = Products::getCategoryDetails($category_id);
			$get_common_meta_values = Cutil::getCommonMetaValues('customer-category-view');
			if($get_common_meta_values)
			{
				$meta_title = (isset($category_details['category_meta_title']) && $category_details['category_meta_title']!='')?$category_details['category_meta_title']:$get_common_meta_values['meta_title'];
				$meta_title = str_replace(array('CATEGORY_NAME'), array($category_details['category_name']), $meta_title);
				$meta_keyword = (isset($category_details['category_meta_keyword']) && $category_details['category_meta_keyword']!='')?$category_details['category_meta_keyword']:str_replace('CATEGORY_NAME', $category_details['category_name'], $get_common_meta_values['meta_keyword']);
				$meta_description = (isset($category_details['category_meta_description']) && $category_details['category_meta_description']!='')?$category_details['category_meta_description']:str_replace('CATEGORY_NAME', $category_details['category_name'], $get_common_meta_values['meta_description']);
	    		$header  = App::make('Header');
				$header->setMetaTitle($meta_title);
				if($meta_keyword!='')
					$header->setMetaKeyword($meta_keyword);
				if($meta_description!='')
					$header->setMetaDescription($meta_description);
			}
    	}
		$category_attributes = $product->getSearchableAttributesList($category_id);
		$breadcrumb_arr = $productService->getProductBreadcrumbArr($category_id);
		//print_r($breadcrumb_arr);exit;
		if(!empty($category_attributes))
		{
			foreach($category_attributes as $key => $attributes)
			{
				$attribute = Products::initializeAttribute();
				$attribute_options = $attribute->getAttributeOptions($attributes['attribute_id']);
				$category_attributes[$key]['attribute_options'] = $attribute_options;
			}
		}
		$selected_attr = array();
		if(Input::has('attributes'))
		{
			$selected_attr = Input::get('attributes');
		}

		//$cat_list_arr = Products::getCategoriesList($category_id);
    	$cat_list = $productService->populateProductCategoryList($category_id);
    	$productService->setProductOrderBy($product, Input::get('orderby_field'));
    	$productService->buildProductQuery($product, $cat_id);
    	$list_prod_serviceobj = $productService;
    	$perPage = Config::get('webshoppack.product_per_page_list');
    	$product->setProductPagination($perPage);
    	$product->setFilterProductExpiry(true);
		$product_details = $product->getProductsList();
		$product_total_count = $product_details->getTotal();
		$category_name = "";
		if($cat_id > 0)
		{
			$category_name = Products::getCategoryName($cat_id);
		}
		$subcat = false;
		if($cat_id > 0)
		{
			$subcat = true;
		}
		$wishlistservice = new WishlistService();


		$top_cat = array();
		if(COUNT($breadcrumb_arr) > 1) {
			$last = array_slice($breadcrumb_arr, -2, 1);
			$top_cat = array_values(array_values($last));
		}
		elseif(COUNT($breadcrumb_arr) > 0)
		{
			$url = URL("/product");
			$top_cat = array(0 => $url);
		}

		//$queries = DB::getQueryLog();

		//echo "<pre>";print_r($queries);echo "</pre>";


    	return View::make('searchProductList', compact('cat_list', 'breadcrumb_arr','product_details', 'subcat', 'product_total_count', 'category_name', 'list_prod_serviceobj', 'wishlistservice', 'shop_obj', 'productService', 'category_attributes', 'selected_attr', 'prod_fav_service', 'top_cat'));
	}

	public function productList()
	{
		$productService = new ProductService();
		$prod_obj = Products::initialize();

		$is_search_done = 0;
		if(Input::has('srchproduct_submit'))
		{
			$is_search_done = 1;
			$productService->setSearchFields(Input::all());
		}
		$user_id = BasicCUtil::getLoggedUserId();
		$status_list = $productService->getProductStatusArr();
		$category_list =  $productService->getCategoryDropOptions();

		$productService->buildMyProductQuery($prod_obj);
		$prod_obj->setProductPagination(20);
		$prod_obj->setOrderByField('id');
		$prod_obj->setIncludeDeleted(true);
		$prod_obj->setIncludeBlockedUserProducts(true);
		//$prod_obj->setFilterProductExpiry(true);
		$product_list = $prod_obj->getProductsList($user_id, false);

		/*$perPage	= Config::get('webshoppack.paginate');
		$product_list = $q->paginate($perPage);*/
		$get_common_meta_values = Cutil::getCommonMetaValues('manage-products');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('productList', compact('product_list', 'productService', 'status_list', 'category_list', 'is_search_done', 'prod_obj'));
	}

	public function postProductAction()
	{
		$productService = new ProductService();
		$error_msg = Lang::get('myProducts.product_invalid_action');
		$sucess_msg = '';
		if(Input::has('product_action') && Input::has('p_id'))
		{
			$p_id = Input::get('p_id');
			$product_action = Input::get('product_action');

			//Validate product id
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$p_details = Product::whereRaw('id = ? AND product_status != ? AND product_user_id = ?', array($p_id, 'Deleted', $logged_user_id))->first();
			if(count($p_details) > 0)
			{
				switch($product_action)
				{
					# Delete product
					case 'delete':
						$error_msg = '';
						# Product status is changed as Deleted
						$status = $productService->deleteProduct($p_id, $p_details);
						# Display delete success msg
						if($status)
						{
							$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
							HomeCUtil::forgotMultiCacheKey($array_multi_key); // Clear cache for product details
							$sucess_msg = Lang::get('myProducts.product_success_deleted');
						}
						else
						{
							$error_msg = Lang::get('myProducts.product_error_on_action');
						}
						break;
				}
			}
		}
		if($sucess_msg != '')
		{
			return Redirect::to('myproducts')->with('success_message', $sucess_msg);
		}
		return Redirect::to('myproducts')->with('error_message', $error_msg);
	}

	public function updateCurrency()
	{
		$currency_code = Input::get("currency_code");
		if($currency_code != "")
		{
			$allowed_currencies_list = CUtil::fetchAllowedCurrenciesList();
			if(in_array($currency_code, $allowed_currencies_list))
			{
				$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_currency", $currency_code);
				return Response::make()->withCookie($cookie);
			}
		}
		return 0;
		/*$currency_details =  CurrencyExchangeRate::whereRaw('currency_code = ? AND status = "Active" AND display_currency = "Yes" ', array($currency_code))->first();
		if(count($currency_details) > 0)
		{
			$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_currency", $currency_details->currency_code);
			return Response::make()->withCookie($cookie);
		}
		return 0;*/
	}

	public function updateLanguage()
	{
		$lang_code = Input::get("lang_code");
		if($lang_code != "") {
			$allowed_languages_list = CUtil::fetchAllowedLanguagesList();
			if(array_key_exists($lang_code, $allowed_languages_list))
			{
				$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_language", $lang_code);
				return Response::make()->withCookie($cookie);
			}
		}
		return 0;
	}

	public function updateShippingCountry()
	{
		$country_id = Input::get("country_id");
		if($country_id != "")
		{
			$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $country_id);
			return Response::make()->withCookie($cookie);
		}
		return 0;
	}

	public function postReportItem(){
		$inputs = Input::all();
		$inputs['user_id'] = BasicCUtil::getLoggedUserId();
		$rules = array('product_id' => 'required', 'user_id' => 'required', 'report_thread' =>'required|min:1');
		$validator = Validator::make($inputs,$rules);
		if($validator->passes())
		{
			$inputs['report_thread'] = implode(',',$inputs['report_thread']);
			$reportedProductService = new ReportedProductService();
			$reported = $reportedProductService->checkReportExists($inputs['product_id'], $inputs['user_id']);
			//echo "<br>reported: ".$reported;exit;
			if(!$reported)
				$status = $reportedProductService->addProductReport($inputs);
			else
				$status = $reportedProductService->updateProductReport($inputs);
			if($status)
			{
				echo json_encode(array('result' => 'success','message' => trans('product.product_report_success')));exit;
			}else{
				echo json_encode(array('result' => 'failure','message' => trans('common.some_problem_try_later')));exit;
			}
		}
		else
		{
			echo json_encode(array('result' => 'failure','message' => trans('common.select_atleast_one_thread')));exit;
		}


	}
}