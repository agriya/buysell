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
class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	function __construct()
	{
        parent::__construct();
    }

	public function showWelcome()
	{
		return View::make('hello');
	}
	public function getIndex(){
		$get_common_meta_values = Cutil::getCommonMetaValues('home');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		if(CUtil::isMember()) {
			$list_type = (Input::has('list_type') && Input::get('list_type') != 'recently_added') ? Input::get('list_type') : '';
			$productService = new ProductService();
			$prod_fav_service = new ProductFavoritesService();
			$product = Products::initialize();
			$shop_obj = Products::initializeShops();
			$perPage = Config::get('webshoppack.product_per_page_list');
	    	$category_id = Products::getRootCategoryId();
	    	$cat_list = $productService->populateProductCategoryList($category_id);

			$productService->setProductOrderBy($product, $list_type);
	    	$productService->buildProductQuery($product, 0);
	    	$product->setProductPagination($perPage);
	    	$product->setFilterProductExpiry(true);
			$product_details = $product->getProductsList();

			$subcat = false;
			$is_index_page = true;
	    	return View::make('index', compact('cat_list', 'product_details', 'subcat', 'list_prod_serviceobj', 'shop_obj', 'productService', 'selected_attr', 'is_index_page', 'list_type', 'prod_fav_service'));
		}
		else {
			$recent_fav = HomeCUtil::populateRecentFavoriteProducts();
			$top_picks = HomeCUtil::populateTopPickers();
			return View::make('home', compact('recent_fav', 'top_picks'));
		}
	}

	/*public static function getAfterLoginIndex()
	{
		$productService = new ProductService();
		$product = Products::initialize();
		$shop_obj = Products::initializeShops();

    	$category_id = Products::getRootCategoryId();
    	$cat_list = $productService->populateProductCategoryList($category_id);
    	$productService->setProductOrderBy($product, Input::get('orderby_field'));
    	$productService->buildProductQuery($product, $cat_id);
    	$list_prod_serviceobj = $productService;
    	$perPage = Config::get('webshoppack.product_per_page_list');
    	$product->setProductPagination($perPage);
		$product_details = $product->getProductsList();
		$product_total_count = $product_details->getTotal();

		$queries = DB::getQueryLog();
		//echo "<pre>";print_r($queries);echo "</pre>";

    	return View::make('index', compact('cat_list', 'product_details', 'subcat', 'product_total_count', 'list_prod_serviceobj', 'shop_obj', 'productService', 'selected_attr'));
	}*/

	public function postSubscribeNewsletter()
	{
		$inputs = Input::all();
		$subscribers = new NewsletterSubscriber;

		$rules['email'] = 'Email';
		$messages = array();
		$validator = Validator::make(Input::all(), $rules, $messages);
		$staus = 'success';
		$status_msg = '';
		// Email address validation
		if ($validator->fails()) {
			$status_msg = trans('common.invalid_email_err_msg');
			$staus = 'error';
		}
		else {
			$email = $inputs['email'];
			$user_id = 0;
			$user_info = User::select('id')->where('email', $email)->first();
			if(count($user_info) > 0) {
				$user_id = $user_info['id'];
			}
			$subscription_details  = NewsletterSubscriber::whereRaw('email = ?', array($email))->first();
			if(sizeof($subscription_details) == 0)
			{
				$data_arr = array('email' => $email, 'ip' => Request::getClientIp(), 'date_added' => DB::Raw('Now()'), 'unsubscribe_code' => str_random(10),
									'status' => 'active', 'user_id' => $user_id);
				$subscribers->addNew($data_arr);
				$status_msg = trans('common.subscribed_succ_msg');
			}
			else
			{
				$data_update = array('status' => 'active', 'unsubscribe_code' => str_random(10), 'user_id' => $user_id);
				NewsletterSubscriber::where('email', $email)->update($data_update);
				$status_msg = trans('common.already_subscribed');
			}
		}
		echo json_encode(array('status' => $staus, 'status_msg' => $status_msg));
	}

	public static function showStaticPage($page_slug = '')
	{
		if($page_slug == '') {
			App::abort(404);
		}
		$static_page_service = new StaticPageService;
		$page_details = array();
		$view_page_name = '';

		$page_details = $static_page_service->getPageDetailsBySlug($page_slug);
		if(count($page_details) > 0)
		{
			$view_page_name = 'static/showStaticContent';
		}

		if($view_page_name == '') {
			App::abort(404);
		}
		$header  = App::make('Header');
		$header->setMetaTitle(ucwords($page_slug).' - VAR_SITE_NAME');
		return View::make($view_page_name, compact('page_details'));
	}
}