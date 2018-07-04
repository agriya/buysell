<?php namespace App\Plugins\Deals\Controllers;
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
use CUtil, BasicCUtil, URL, DB, Lang, View, Input, Validator, Products ;
use Session, Redirect, BaseController;

class DealsController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->deal_service = new \DealsService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->beforeFilter(function(){
			if(!CUtil::chkIsAllowedModule('deals'))
	    	{
				return Redirect::to('/');
			}
		});
		$this->beforeFilter(function(){
			if ($this->logged_user_id == 0) {
				return Redirect::to('/users/login');
			}
		}, array('except' => array('getDealsList', 'getIndex', 'getViewDeal', 'getDealsItemList')));
	}

	public function getIndex()
	{
		$d_arr = array();
		$deal_serviceobj = $this->deal_service;
		$featured_deal = $deal_serviceobj->getFeaturedDeal();
		$recent_deals = $deal_serviceobj->populateRecentDealsList();
		$get_common_meta_values = Cutil::getCommonMetaValues('deals');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('deals::index', compact('d_arr', 'featured_deal', 'deal_serviceobj', 'recent_deals'));
	}


	public function getAddDeal($deal_id = 0)
	{
		// To check if shop not set redirect to  shop details page.
		$shop = Products::initializeShops();
		$logged_user_id = $this->logged_user_id;
		if(!$shop->checkIsShopNameExist($logged_user_id) || !$shop->checkIsShopPaypalUpdated($logged_user_id))
    	{
			return Redirect::to('shop/users/shop-details');
		}

		$action = 'add';
		$deal_details = $d_arr = array();
		$d_arr['applicable_for'] = 'all_items';
		$prod_obj = Products::initialize();
		$prod_obj->setFilterProductExpiry(true);
		$prod_obj->setFilterProductFree('No');
		$product_list = $prod_obj->getProductsList($logged_user_id );
		if(COUNT($product_list) < 1)
		{
			return Redirect::to('myproducts')->with('error_message', Lang::get('deals::deals.product_none_err_msg'));
		}
		$shop_products = array();
		foreach($product_list as $key => $prd)
		{
			$shop_products[$prd->id] = $prd['product_name'];
		}
		$d_arr['shop_items'] = $shop_products;
		if(Input::has('setDealItem') && Input::get('setDealItem') != '')
		{
			if(isset($shop_products[Input::get('setDealItem')]))
			{
				$d_arr['applicable_for'] = 'single_item';
				$d_arr['assigned_items'] = Input::get('setDealItem');
			}
		}
		$d_arr['selected_items'] = array();
		$d_arr['tipping_apply_single_item'] = (\Config::has('plugin.deals_tipping_only_for_single_item') && \Config::get('plugin.deals_tipping_only_for_single_item') ) ? 1 : 0;
		$file_noteinfo = Lang::get('deals::deals.allowed_image_formats_size');
		$file_noteinfo = str_replace("VAR_ALLOWED_FORMAT", \Config::get("plugin.deal_img_format_arr"), $file_noteinfo);
		$file_noteinfo = str_replace("VAR_ALLOWED_SIZE",  \Config::get("plugin.deal_img_max_size")." MB", $file_noteinfo);
		$file_noteinfo1 = Lang::get('deals::deals.image_allowed_dimension_note');
		$file_noteinfo1 = str_replace("VAR_WIDTH", \Config::get("plugin.deal_img_width"), $file_noteinfo1);
		$file_noteinfo1 = str_replace("VAR_HEIGHT", \Config::get("plugin.deal_img_height"), $file_noteinfo1);
		$d_arr['file_noteinfo'] = $file_noteinfo;
		$d_arr['file_noteinfo1'] = $file_noteinfo1;		
		$get_common_meta_values = Cutil::getCommonMetaValues('add-deal');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('deals::addDeal', compact('deal_details', 'logged_user_id', 'action', 'deal_id', 'd_arr'));
	}


	public function getUpdateDeal($deal_id = 0)
	{
		$deal_serviceobj = $this->deal_service;
		$action = 'add';
		$deal_details = $d_arr = array();
		$d_arr['applicable_for'] = 'all_items';
		$logged_user_id = $this->logged_user_id;

		if($deal_id <= 0)
		{
			Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
			return Redirect::to('deals/add-deal');
		}
		else
		{
			$resp = $deal_serviceobj->chkIsValidDealEditAccess($deal_id, $logged_user_id);
			if(isset($resp['err_msg']) && $resp['err_msg'] != '' && $resp['status']  == 'error')
			{
				Session::flash('error', $resp['err_msg']);
				return Redirect::to('deals/add-deal');
			}
			$deal_details = $resp['deal_details'];
			if(!$deal_details || count($deal_details) <= 0)
			{
				Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
				return Redirect::to('deals/add-deal');
			}
			$action = 'edit';
			$deal_details->date_deal_from = date_format(date_create_from_format('Y-m-d', $deal_details->date_deal_from), 'd/m/Y');
			$deal_details->date_deal_to = date_format(date_create_from_format('Y-m-d', $deal_details->date_deal_to), 'd/m/Y');
		}

		$prod_obj = Products::initialize();
		$prod_obj->setFilterProductExpiry(true);
		$prod_obj->setFilterProductFree('No');
		$product_list = $prod_obj->getProductsList($logged_user_id );
		if(COUNT($product_list) < 1)
		{
			return Redirect::to('myproducts')->with('error_message', Lang::get('deals::deals.product_none_err_msg'));
		}
		$assigned_items = $this->deal_service->fetchAssignedItemsList($deal_id);
		$shop_products = array();
		foreach($product_list as $key => $prd)
		{
			$shop_products[$prd->id] = $prd['product_name'];
		}
		$d_arr['shop_items'] = $shop_products;
		$d_arr['assigned_items'] = null;
		if($deal_details->applicable_for == 'single_item')
		{
			$d_arr['assigned_items'] = (isset($assigned_items)) ? $assigned_items : null;
		}
		elseif($deal_details->applicable_for == 'selected_items')
		{
			$tmp_arr = array();
			if(count($assigned_items) > 0)
			{
				foreach($assigned_items as $items)
				{
					if(isset($shop_products[$items]))
						$tmp_arr[$items] = $shop_products[$items];
				}
			}
			$d_arr['assigned_items'] = $tmp_arr;
		}

		$d_arr['tipping_apply_single_item'] = (\Config::has('plugin.deals_tipping_only_for_single_item') && \Config::get('plugin.deals_tipping_only_for_single_item') ) ? 1 : 0;
		$file_noteinfo = Lang::get('deals::deals.allowed_image_formats_size');
		$file_noteinfo = str_replace("VAR_ALLOWED_FORMAT", \Config::get("plugin.deal_img_format_arr"), $file_noteinfo);
		$file_noteinfo = str_replace("VAR_ALLOWED_SIZE",  \Config::get("plugin.deal_img_max_size")." MB", $file_noteinfo);
		$file_noteinfo1 = Lang::get('deals::deals.image_allowed_dimension_note');
		$file_noteinfo1 = str_replace("VAR_WIDTH", \Config::get("plugin.deal_img_width"), $file_noteinfo1);
		$file_noteinfo1 = str_replace("VAR_HEIGHT", \Config::get("plugin.deal_img_height"), $file_noteinfo1);
		$d_arr['file_noteinfo'] = $file_noteinfo;
		$d_arr['file_noteinfo1'] = $file_noteinfo1;
		$get_common_meta_values = Cutil::getCommonMetaValues('update-deal');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}

		return View::make('deals::addDeal', compact('deal_details', 'logged_user_id', 'action', 'deal_id', 'd_arr', 'deal_serviceobj'));
	}


	public function postAddDeal()
	{
		$inputs = Input::all();
		$action = isset($inputs['action']) ? $inputs['action'] : 'add';
		$deal_id = isset($inputs['deal_id']) ? $inputs['deal_id'] : 0;

		$rules['deal_title'] = 'required';
		$rules['url_slug'] = 'required|unique:deal,url_slug';

		if($deal_id > 0)
			$rules['url_slug'] = 'required|IsValidSlugUrl|unique:deal,url_slug,'.$deal_id.',deal_id';
		else
			$rules['url_slug'] = 'required|IsValidSlugUrl|unique:deal,url_slug';

		if(\Config::has("plugin.deal_highlighttext_max_chars") && \Config::get("plugin.deal_highlighttext_max_chars") != 0)
			$rules['deal_short_description'] = 'required|max:'.\Config::get("plugin.deal_highlighttext_max_chars");
		else
			$rules['deal_short_description'] = 'required';
		$rules['deal_description'] = 'required';
		$message = array('name.required' =>'Required', 'url_slug.unique' =>Lang::get('deals::deals.slug_url_already_exists_err'));

		if($inputs['applicable_for'] == 'single_item')
		{
			$rules['deal_item'] = 'required';
			$message['deal_item'] = 'Required';
		}
		elseif($inputs['applicable_for'] == 'selected_items')
		{
			$rules['selected_items_list'] = 'required';
			$message['selected_items_list'] = Lang::get('deals::deal.select_product_none_err_msg');
		}

		$rules['discount_percentage'] = 'required|numeric|between:1,99';

		if($inputs['tipping_qty_for_deal'] != 0)
			$rules['tipping_qty_for_deal'] = 'numeric';

		if($inputs['date_deal_from']!='')
			$inputs['date_deal_from'] = date_format(date_create_from_format('d/m/Y', $inputs['date_deal_from']), 'Y-m-d');
		if($inputs['date_deal_to']!='')
			$inputs['date_deal_to'] = date_format(date_create_from_format('d/m/Y', $inputs['date_deal_to']), 'Y-m-d');

		$date_format = 'Y-m-d';
		$curr_date = date('Y-m-d');
		if($action == 'add')
		{
		//	$rules['date_deal_from'] = 	'required|after:'.date('Y-m-d',strtotime("-1 days"));
			$rules['date_deal_from'] = 'Required|date_format:'.$date_format.'|CustEqualOrAfter:'.$curr_date.','.$inputs['date_deal_from'];
		}
		else
		{
			$rules['date_deal_from'] = 	'required';
		}
		//$rules['date_deal_to'] 	= 	'required|after:'.date('Y-m-d',strtotime($inputs['date_deal_from'].' -1 days'));
		$rules['date_deal_to'] = 'Required|date_format:'.$date_format.'|CustEqualOrAfter:'.$inputs['date_deal_from'].','.$inputs['date_deal_to'];
		$message['date_deal_from.cust_equal_or_after'] = trans('deals::deals.start_date_err');
		$message['date_deal_to.cust_after'] = trans('deals::deals.end_date_err');
		$message['date_deal_to.cust_equal_or_after'] = trans('deals::deals.end_date_err');
		$message['url_slug.is_valid_slug_url'] = trans('deals::deals.invalid_deal_slug_msg');
		if (Input::hasFile('deal_image'))
		{
			if($_FILES['deal_image']['error'])
			{
				$error_msg = 'Invalid file size';
				return Redirect::back()->with('error_message', $error_msg);
			}
		}
		$allowed_formats = \Config::get("plugin.deal_img_format_arr");
		$allowed_size = \Config::get('plugin.deal_img_max_size') * 1024;
		if($action == 'add')
		{
			$rules['deal_image'] = 'required|mimes:'.$allowed_formats.'|max:'.$allowed_size;
		}
		else
		{
			if (Input::hasFile('deal_image'))
			{
				$rules['deal_image'] = 'mimes:'.$allowed_formats.'|max:'.$allowed_size;
			}
		}
		$validator = Validator::make($inputs, $rules, $message);
		if ($validator->fails())
		{
			if($action == 'add')
				return Redirect::to('deals/add-deal')->withInput()->withErrors($validator);
			else
				return Redirect::to('deals/update-deal/'.$deal_id)->withInput()->withErrors($validator);
		}
		else
		{
			$my_deals_link = '<a href='.URL::to('deals/my-deals').'>CLICK HERE </a>';
			if($action == 'add')
			{
				$deal_id = $this->deal_service->addDealEntry($inputs);
				if(\Config::has("plugin.deal_auto_approval") && \Config::get("plugin.deal_auto_approval"))
				{
					$succMsg = Lang::get('deals::deals.submitted_succ_msg');
				}
				else
				{
					$succMsg = Lang::get('deals::deals.submitted_pending_approval_succ_msg');
				}
				$succMsg = str_replace('VAR_MY_DEALS', $my_deals_link, $succMsg);
				Session::flash('success', $succMsg);
			}
			else
			{
				$deal_id = $this->deal_service->updateDealEntry($inputs);
				$succMsg = Lang::get('deals::deals.updated_success_msg');
				$succMsg = str_replace('VAR_MY_DEALS', $my_deals_link, $succMsg);
				Session::flash('success', $succMsg);
			}
			return Redirect::to('deals/add-deal');
		}
	}

	public function getMyDeals()
	{
		$err_msg = '';
		$deal_serviceobj = $this->deal_service;
		$user_id = BasicCUtil::getLoggedUserId();
		// Fetch purchased count
		$purchased_count = $deal_serviceobj->getDealPurchasedCount();
		$deals = $deal_serviceobj->getMyDeals($user_id, 'paginate', 10);
		$get_common_meta_values = Cutil::getCommonMetaValues('my-deal-list');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('deals::myDealsList', compact('deals', 'deal_serviceobj', 'purchased_count'));
	}

	public function postMyDeals()
	{
		$inputs = Input::all();
		$deal_id = Input::get('deal_id');
		$deal_action = Input::get('deal_action');

		if(is_null($deal_id) || $deal_id <=0)
			return Redirect::to('deals/my-deals')->with('error_message', Lang::get('deals::deals.select_valid_deal'));
		$user_id = BasicCUtil::getLoggedUserId();

		$resp = $this->deal_service->chkIsAndCloseDealByUser($deal_id, $user_id);
		if(isset($resp['success_msg']) && $resp['success_msg'] != '')
		{
			return Redirect::to('deals/my-deals')->with('success', $resp['success_msg']);
		}
		if(isset($resp['error_msg']) && $resp['error_msg'] != '')
		{
			return Redirect::to('deals/my-deals')->with('error', $resp['error_msg']);
		}
	}

	public function getMyFeaturedRequest()
	{
		$deal_serviceobj = $this->deal_service;
		$user_id = BasicCUtil::getLoggedUserId();
		$deals = $deal_serviceobj->getMyFeaturedRequests($user_id, 'paginate', 10);
		$get_common_meta_values = Cutil::getCommonMetaValues('my-deal-future-request');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('deals::myFeaturedRequests', compact('deals', 'deal_serviceobj'));
	}

	public function getDealsItemList($deal_id=0)
	{
		$deal_serviceobj = $this->deal_service;
		$deal_details = $this->deal_service->getDealsDetails($deal_id);
		if(!$deal_details || count($deal_details) <= 0)
		{
			Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
			return Redirect::to('deals');
		}
		$user_id = $this->logged_user_id;
		$assigned_items = $this->deal_service->fetchAssignedItemsList($deal_id);
		$product = Products::initialize();
		$shop_obj = Products::initializeShops();
		$list_prod_serviceobj = new \ProductService();
    	$product->setProductPagination(10);
    	$product->setFilterProductExpiry(true);
    	$product->setFilterProductFree('No');
		if($deal_details->applicable_for != 'all_items')
		{
			$product->setFilterProductIdsIn($assigned_items);
		}
		$product_details = $product->getProductsList($deal_details->user_id);
		$product_total_count = $product_details->getTotal();

		return View::make('deals::dealItemsList', compact('deal_details', 'deal_serviceobj', 'product_details', 'shop_obj', 'list_prod_serviceobj'));
	}

	public function getDealsList($list_type='New')
	{
		$allowed_type = array('new', 'new-deals', 'expiring', 'expired');
		if(!in_array($list_type, $allowed_type))
			 return Redirect::to('deals');

		$err_msg = '';
		$is_search_done = 0;
		$inputs=Input::all();
		$d_arr = $deal_list = array();
		$list_type = strtolower($list_type);
		$deal_serviceobj = $this->deal_service;
		switch($list_type)
		{
			case 'new-deals':
			case 'new':
				$get_common_meta_values = Cutil::getCommonMetaValues('recent-deals');
				$deal_list = $this->deal_service->getDealList('new', 'paginate', 12);
				$d_arr['page_title'] = Lang::get('deals::deals.new_deals_listing');
				break;

			case 'expiring':
				$get_common_meta_values = Cutil::getCommonMetaValues('expiring-deals');
				$deal_list = $this->deal_service->getDealList($list_type, 'paginate', 12);
				$d_arr['page_title'] = Lang::get('deals::deals.expiring_listing');
				break;

			case 'expired':
				$get_common_meta_values = Cutil::getCommonMetaValues('expired-deals');
				$deal_list = $this->deal_service->getDealList($list_type, 'paginate', 12);
				$d_arr['page_title'] = Lang::get('deals::deals.expired_listing');
				break;

			default:
				$get_common_meta_values = Cutil::getCommonMetaValues('recent-deals');
				$deal_list = $this->deal_service->getDealList('new', 'paginate', 10);
				$d_arr['page_title'] = Lang::get('deals::deals.new_deals_listing');
		}
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('deals::deal_listing', compact('deal_list', 'd_arr', 'deal_serviceobj'));
	}

	public function getViewDeal($url_slug = '')
	{
		$d_arr = array();
		$deal_serviceobj = $this->deal_service;
		$deal_id = $deal_serviceobj->getDealIdBySlug($url_slug);
		$user_id = $this->logged_user_id;

		if($deal_id && $deal_serviceobj->chkIsValidDealIdForView($deal_id, $user_id) )
		{
			$deal_details = $this->deal_service->getDealsDetails($deal_id);
			if(!$deal_details || count($deal_details) <= 0)
			{
				Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
				return Redirect::to('deals');
			}
		}
		else
		{
			Session::flash('error', Lang::get('deals::deals.invalid_acces_err_msg'));
			return Redirect::to('deals');
		}

		
		$get_common_meta_values = Cutil::getCommonMetaValues('view-deal');
		
		if($get_common_meta_values)
		{
			$meta_title_arug = ($deal_details->meta_title != "" ) ? $deal_details->meta_title : $get_common_meta_values['meta_title'];
			$meta_keyword_arug = ($deal_details->meta_keyword != "" ) ? $deal_details->meta_keyword : $get_common_meta_values['meta_keyword'];
			$meta_description_arug = ($deal_details->meta_description != "" ) ? $deal_details->meta_description : $get_common_meta_values['meta_description'];
			
			$this->header->setMetaKeyword(str_replace("DEAL_TITLE", $deal_details->deal_title, $meta_keyword_arug));
			$this->header->setMetaDescription(str_replace("DEAL_TITLE", $deal_details->deal_title, $meta_description_arug));
			$this->header->setMetaTitle(str_replace("DEAL_TITLE", $deal_details->deal_title, $meta_title_arug));
		}

		$d_arr['purchase_count'] = $deal_serviceobj->getDealPurchasedCountById($deal_details->deal_id);

		$shop_serviceobj = new \ShopService();
		$shop_obj = Products::initializeShops();

		if($deal_details->applicable_for != 'all_items')
		{
			$assigned_items = $deal_serviceobj->fetchAssignedItemsList($deal_id);
			$product = Products::initialize();
			$list_prod_serviceobj = new \ProductService();

	    	$product->setProductPagination(3);
	    	$product->setFilterProductExpiry(true);
			$product->setFilterProductIdsIn($assigned_items);
			$product->setFilterProductFree('No');
			$product_details = $product->getProductsList($deal_details->user_id);
			$product_total_count = $product_details->getTotal();
			$d_arr['product_details'] = $product_details;
			$d_arr['product_total_count'] = $product_total_count;
		}
		$d_arr['related_deals'] = $deal_serviceobj->populateMoreDealsFromShop($deal_details->user_id, $deal_id);

		$d_img_arr = array();
		$d_img_arr['deal_id'] = $deal_details->deal_id;
		$d_img_arr['deal_title'] = $deal_details->deal_title;
		$d_img_arr['img_name'] = $deal_details->img_name;
		$d_img_arr['img_ext'] = $deal_details->img_ext;
		$d_img_arr['img_width'] = $deal_details->img_width;
		$d_img_arr['img_height'] = $deal_details->img_height;
		$d_img_arr['l_width'] = $deal_details->l_width;
		$d_img_arr['l_height'] = $deal_details->l_height;
		$d_img_arr['t_width'] = $deal_details->t_width;
		$d_img_arr['t_height'] = $deal_details->t_height;
		$d_arr['d_img_arr'] = $d_img_arr;
		$d_arr['deal_share_image'] = $deal_serviceobj->getDealDefaultThumbImage($deal_details->deal_id, 'thumb', $d_img_arr);
		$d_arr['deal_thumb_image'] = $deal_serviceobj->getDealDefaultThumbImage($deal_details->deal_id, 'default', $d_img_arr);
		$d_arr['post_wall_img'] = addslashes($d_arr['deal_share_image']["image_url"]);
		$d_arr['post_wall_title'] = addslashes($deal_details->deal_title);
		$d_arr['post_wall_description'] = addslashes($deal_details->deal_short_description);
		$d_arr['shop_details']  =$shop_serviceobj->getShopDetails($deal_details->user_id);
		return View::make('deals::viewDeal', compact('deal_details', 'deal_serviceobj', 'shop_obj', 'list_prod_serviceobj', 'd_arr', 'shop_serviceobj'));
	}


	public function getSetFeatured($deal_id=0)
	{
		$deal_details = array();
		$deal_serviceobj = $this->deal_service;
		$user_id = $this->logged_user_id;
		$error_message = '';
		$resp = $deal_serviceobj->chkIsValidAccessForSetFeatured($deal_id, $user_id);
		$d_arr['act'] = 'add';
		$d_arr['deal_id'] = $deal_id;

		if(isset($resp['err_msg']) &&  $resp['status'] == 'error')
		{
			$error_message = $resp['err_msg'];
		}
		else
		{
			if($deal_id && $deal_serviceobj->chkIsValidDealId($deal_id, $user_id))
			{
				$deal_details = $this->deal_service->getDealsDetails($deal_id, $user_id);
				if(!$deal_details || count($deal_details) <= 0)
				{
					Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
					return Redirect::to('deals');
				}
				// If already closed deal then show as invalid access error
				if($deal_details->deal_status == 'closed')
				{
					Session::flash('error', Lang::get('deals::deals.invalid_acces_err_msg'));
					return Redirect::to('deals');
				}
			}
			else
			{
				Session::flash('error', Lang::get('deals::deals.invalid_acces_err_msg'));
				return Redirect::to('deals');
			}
		}
		return View::make('deals::dealFeatured', compact('deal_details', 'error_message', 'deal_serviceobj', 'd_arr'));
	}

	public function postSetFeatured()
	{
		$user_id = $this->logged_user_id;
		$inputs = Input::all();
		$deal_id = isset($inputs['deal_id']) ? $inputs['deal_id'] : 0;
		$deal_details = $this->deal_service->getDealsDetails($deal_id, $user_id);
		if(COUNT($deal_details) < 1)
		{
			Session::flash('error', Lang::get('deals::deals.invalid_deal_id_msg'));
			return Redirect::to('deals');
		}

		if(Input::has("edit_request"))
		{
			return Redirect::back()->withInput();
		}

		$deal_serviceobj = $this->deal_service;

		$message = array();

		if($inputs['date_featured_from']!='')
		{
			$inputs['date_featured_from_lbl'] = $inputs['date_featured_from'];
			$inputs['date_featured_from'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_from']), 'Y-m-d');

		}
		if($inputs['date_featured_to']!='')
		{
			$inputs['date_featured_to_lbl'] = $inputs['date_featured_to'];
			$inputs['date_featured_to'] = date_format(date_create_from_format('d/m/Y', $inputs['date_featured_to']), 'Y-m-d');
		}

//		$rules['date_featured_from'] 	= 'required|after:'.date('Y-m-d',strtotime("-1 days"));
//		$rules['date_featured_to'] 		= 'required|after:'.date('Y-m-d',strtotime($inputs['date_featured_from'].' -1 days'));
		$deal_from_date = date('d-m-Y', strtotime($deal_details->date_deal_from));
		$deal_to_date = date('d-m-Y', strtotime($deal_details->date_deal_to));
		$rules['date_featured_from'] = 'required|after:'.date('Y-m-d',strtotime($deal_from_date.' -1 days')).'|before:'.date('Y-m-d',strtotime($deal_to_date.' +1 days'));
		$rules['date_featured_to'] = 'required|after:'.date('Y-m-d',strtotime($inputs['date_featured_from'].' -1 days')).'|before:'.date('Y-m-d',strtotime($deal_to_date.' +1 days'));
		$from	= date('d-m-Y', strtotime($inputs['date_featured_from']));
		$to		= date('d-m-Y', strtotime($inputs['date_featured_to']));
		$deal_featured_days =((strtotime($to) - strtotime($from))/ (60 * 60 * 24)) + 1; //it will count no. of days
		$inputs['deal_featured_days'] = $deal_featured_days;

		$date_err_msg = Lang::get('deals::deals.deal_featured_date_err_msg');
		$date_err_msg = str_replace("START_DATE", $deal_from_date, $date_err_msg);
		$date_err_msg = str_replace("END_DATE", $deal_to_date, $date_err_msg);
		$message['date_featured_from.after'] = $message['date_featured_from.before'] = $message['date_featured_to.before'] = $date_err_msg;

		$validator = Validator::make($inputs, $rules, $message);
		if ($validator->fails())
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
		else
		{
			if(isset($inputs['pay_act']) && $inputs['pay_act'] == 'act_bal')
			{
				$details_arr = array();
				$details_arr['listing_fee'] = 0;

				if(\Config::has('plugin.deal_listing_fee') && \Config::get('plugin.deal_listing_fee') > 0)
				{
					$deal_listing_fee = \Config::get('plugin.deal_listing_fee') * $inputs['deal_featured_days'];
					$user_acc_balance = CUtil::getUserAccountBalance($user_id);

					if(isset($user_acc_balance['amount']) && $user_acc_balance['amount'] > $deal_listing_fee)
					{
						$details_arr['listing_fee'] = $deal_listing_fee;
					}
					else
					{
						Session::flash('error_message', Lang::get('deals::deals.insufficient_fund_err_msg'));
						return Redirect::to( URL::action('WalletAccountController@getAddAmount'));
					}
				}
				$details_arr['user_id'] = $user_id;
				$details_arr['deal_id'] = $inputs['deal_id'];
				$details_arr['date_featured_from'] = $inputs['date_featured_from'];
				$details_arr['date_featured_to'] = $inputs['date_featured_to'];
				$details_arr['deal_featured_days'] = $inputs['deal_featured_days'];
				$this->deal_service->payDealListFeeFromCredit($details_arr);
				Session::flash('success', Lang::get('deals::deals.featured_requested_suc_msg'));
				return Redirect::to('deals/my-featured-request');
			}
			elseif(isset($inputs['pay_confirm']) && $inputs['pay_confirm'] != '')
			{
				$deal_details = $this->deal_service->getDealsDetails($inputs['deal_id'], $user_id);

				$d_arr['act'] = 'edit';
				$d_arr['deal_id'] = $inputs['deal_id'];
				$d_arr['date_featured_from'] = $inputs['date_featured_from'];
				$d_arr['date_featured_from_lbl'] = $inputs['date_featured_from_lbl'];
				$d_arr['date_featured_to'] = $inputs['date_featured_to'];
				$d_arr['date_featured_to_lbl'] = $inputs['date_featured_to_lbl'];
				$d_arr['deal_featured_days'] = $inputs['deal_featured_days'];
				$d_arr['confirm'] = 'Yes';

				$deal_listing_fee = '';
				if(\Config::has('plugin.deal_listing_fee') && \Config::get('plugin.deal_listing_fee') > 0)
					$deal_listing_fee = \Config::get('plugin.deal_listing_fee') * $inputs['deal_featured_days'];

				$d_arr['deal_listing_fee'] = $deal_listing_fee;

				return View::make('deals::dealFeatured', compact('d_arr', 'deal_details', 'deal_serviceobj'));
			}
			elseif(isset($inputs['edit_request']) && $inputs['edit_request'] != '')
			{
				$d_arr['act'] = 'edit';
				return Redirect::back()->withInput();
			}
			else
			{
				$d_arr['act'] = 'add';
				return Redirect::back()->withInput();
			}
		}
	}

	public function getPurchasedDetails($deal_id=0)
	{
		$deal_details = array();
		$error_message = '';
		$deal_serviceobj = $this->deal_service;
		$purchase_details = array();
		$user_id = $this->logged_user_id;
		$purchase_details = $deal_serviceobj->getDealPurchasedDetails($deal_id, $user_id, 'paginate', 10);

		if(!$deal_serviceobj->chkIsValidDealIdForView($deal_id, $user_id))
		{
			$error_message = Lang::get('deals::deals.invalid_acces_err_msg');
		}
		return View::make('deals::dealPurchasedDetails', compact('purchase_details', 'error_message', 'd_arr'));
	}
}