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
class FeaturedSellersService
{
	//Featured sellers plan functions start
	public function getFeaturedSellersPlanSettings($id = 0)
 	{
 		if($id) {
 			return DB::table('featured_sellers_plans')
			 			->where('featured_seller_plan_id', $id)
						->first();
 		}
 	}

 	public function buildFeaturedSellersPlanQuery()
	{
		return DB::table('featured_sellers_plans')
					->Select("featured_seller_plan_id", "featured_days", "featured_price", "status")
					->orderBy('featured_days', 'ASC');
	}

	public function featuredSellersPlansCount()
	{
		$sellers_plans_count = DB::table('featured_sellers_plans')->where('status', '=', 'Active')->orderBy('featured_days', 'ASC')->count();
	    return $sellers_plans_count;
	}

	public function updateFeaturedSellersPlan($input)
 	{
 		$featured_days = $input['featured_days'];
 		$featured_price = $input['featured_price'];
 		$status = $input['status'];
 		$featured_qry = DB::table('featured_sellers_plans')->whereRaw('featured_days = ? AND featured_price = ?', array($featured_days, $featured_price));
		if($input['feature_id']) {
			$featured_qry = $featured_qry->whereRaw('featured_seller_plan_id != ?', array($input['feature_id']));
		}
		$featured_id = $featured_qry->pluck('featured_seller_plan_id');

		if($featured_id > 0) {
			return json_encode(array('status' => 'error', 'error_message' => trans("featuredsellers::featuredsellers.already_exists")));
		}
		if($input['feature_id']) {
			DB::table('featured_sellers_plans')->where('featured_seller_plan_id', $input['feature_id'])->update(array('featured_days' => $featured_days, 'featured_price' => $featured_price, 'status' => $status));
			$id = $input['feature_id'];
		}
		else {
			$id = DB::table('featured_sellers_plans')->insertGetId(array('featured_days' => $featured_days, 'featured_price' => $featured_price, 'status' => $status));
		}
		return json_encode(array('status' => 'success', 'id' => $id));
 	}

	public function updateFeaturedSellersPlanStatus($id, $action)
	{
		switch($action)
		{
			case 'Active':
				DB::table('featured_sellers_plans')
					->where('featured_seller_plan_id', $id)
					->update(array('status' => $action));
				$success_msg = Lang::get('featuredsellers::featuredsellers.activated_suc_msg');
				break;
			case 'Inactive':
				DB::table('featured_sellers_plans')
					->where('featured_seller_plan_id', $id)
					->update(array('status' => $action));
				$success_msg = Lang::get('featuredsellers::featuredsellers.deactivated_suc_msg');
				break;
			default;
				$success_msg = Lang::get('featuredsellers::featuredsellers.select_valid_actiion');
				break;
		}
		return $success_msg;
	}

	public function getFeaturedSellersPlans()
	{
		$plans_list = array();
		$sellers_plans = DB::table('featured_sellers_plans')->where('status', '=', 'Active')->orderBy('featured_days', 'ASC')->get();
		if(count($sellers_plans) > 0) {
			foreach($sellers_plans as $key => $plan) {
				$lang_days = Ucfirst(Lang::get('common.for')).' '.$plan->featured_days.' '.strtolower(Lang::choice('featuredsellers::featuredsellers.day_choice', $plan->featured_days));
				$lang_price = Config::get('generalConfig.site_default_currency').' '.$plan->featured_price;
				$plans_list[$plan->featured_seller_plan_id] = $lang_days.': '.$lang_price;
			}
		}
		return $plans_list;
	}
	//Featured sellers plan functions end

	//Featured sellers list start
	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : "";
	}
	public function setFeaturedSellersFilterArr()
	{
		$this->filter_arr['user_name']= '';
		$this->filter_arr['id']= '';
		$this->filter_arr['user_code']= '';
		$this->filter_arr['name']= '';
		$this->filter_arr['user_email']= '';
		$this->filter_arr['user_group']= '';
		$this->filter_arr['group_name_srch']= '';
		$this->filter_arr['is_shop_owner']= '';
		$this->filter_arr['is_allowed_to_add_product']= '';
		$this->filter_arr['status']= '';
		$this->filter_arr['from_date']= '';
		$this->filter_arr['to_date']= '';
		$this->filter_arr['shop_status']= '';
		$this->filter_arr['shop_name']= '';
	}

	public function setFeaturedSellersSrchArr($input)
	{
		$this->srch_arr['user_name']= (isset($input['user_name']) && $input['user_name'] != '') ? $input['user_name'] : "";
		$this->srch_arr['id']= (isset($input['id']) && $input['id'] != '') ? $input['id'] : "";
		$this->srch_arr['user_code']= (isset($input['user_code']) && $input['user_code'] != '') ? $input['user_code'] : "";
		$this->srch_arr['name']= (isset($input['name']) && $input['name'] != '') ? $input['name'] : "";
		$this->srch_arr['user_email']= (isset($input['user_email']) && $input['user_email'] != '') ? $input['user_email'] : "";
		$this->srch_arr['user_group']= (isset($input['user_group']) && $input['user_group'] != '') ? $input['user_group'] : "";
		$this->srch_arr['group_name_srch']= (isset($input['group_name_srch']) && $input['group_name_srch'] != '') ? $input['group_name_srch'] : "";
		$this->srch_arr['is_shop_owner']= (isset($input['is_shop_owner']) && $input['is_shop_owner'] != '') ? $input['is_shop_owner'] : "";
		$this->srch_arr['is_allowed_to_add_product']= (isset($input['is_allowed_to_add_product']) && $input['is_allowed_to_add_product'] != '') ? $input['is_allowed_to_add_product'] : "";
		$this->srch_arr['status']= (isset($input['status']) && $input['status'] != '') ? $input['status'] : "";
		$this->srch_arr['from_date']= (isset($input['from_date']) && $input['from_date'] != '') ? $input['from_date'] : "";
		$this->srch_arr['to_date']= (isset($input['to_date']) && $input['to_date'] != '') ? $input['to_date'] : "";
		$this->srch_arr['shop_status']= (isset($input['shop_status']) && $input['shop_status'] != '') ? $input['shop_status'] : "";
		$this->srch_arr['shop_name']= (isset($input['shop_name']) && $input['shop_name'] != '') ? $input['shop_name'] : "";
	}

	public function buildFeaturedSellersQuery()
	{
		$this->qry = DB::table('shop_details') ->join('users','users.id', '=', 'shop_details.user_id')
									   ->Select("shop_details.*","users.first_name", "users.last_name", "users.user_name", "users.email",
													"users.activated", "users.is_banned", "users.is_requested_for_seller",
													"users.is_allowed_to_add_product", "users.is_shop_owner", "users.shop_status", "users.paypal_id",
													"users.is_featured_seller", "users.featured_seller_expires");
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if ($logged_user_id != 1)
			$this->qry->Where('users.id', '>', 1);

		//form the search query
		if($this->getSrchVal('user_code')) {
			$this->qry->whereRaw("users.id = ?", array(BasicCUtil::getUserIDFromCode($this->getSrchVal('user_code'))));
		}
		if($this->getSrchVal('id')) {
			$this->qry->whereRaw("users.id = ?", array($this->getSrchVal('id')));
		}
		if($this->getSrchVal('user_name')) {
			$name_arr = explode(" ",$this->getSrchVal('user_name'));
			if(count($name_arr) > 0) {
				$or_str = '(';
				foreach($name_arr AS $names) {
					if($or_str != '(')
						$or_str = $or_str.' OR ';
					$or_str = $or_str.' (users.first_name LIKE \'%'.addslashes($names).'%\' OR users.last_name LIKE \'%'.addslashes($names).'%\' )';
				}
				$or_str = $or_str.' )';
				$this->qry->whereRaw(DB::raw($or_str));
			}
		}
		if($this->getSrchVal('name')) {
			$this->qry->whereRaw(DB::raw('(users.user_name LIKE \'%'.addslashes($this->getSrchVal('name')).'%\')'));
		}

		if($this->getSrchVal('shop_name')) {
			$this->qry->whereRaw(DB::raw('(shop_details.shop_name LIKE \'%'.addslashes($this->getSrchVal('shop_name')).'%\')'));
		}
		if($this->getSrchVal('shop_status')) {
			if($this->getSrchVal('shop_status') == 'active')
				$this->qry->Where('users.shop_status', 1);
			else
				$this->qry->Where('users.shop_status', 0);
			//$this->qry->whereRaw(DB::raw('(users.user_name LIKE \'%'.addslashes($this->getSrchVal('name')).'%\')'));
		}
		if($this->getSrchVal('user_email')) {
			//$this->qry->WhereRaw('(users.paypal_id =? OR users.email =?)', array($this->getSrchVal('user_email'), $this->getSrchVal('user_email')));
			$this->qry->WhereRaw('(users.email =?)', array($this->getSrchVal('user_email')));
		}
		if($this->getSrchVal('group_name_srch')) {
			$this->qry->Where('users_groups.group_id', $this->getSrchVal('group_name_srch'));
		}
		if($this->getSrchVal('is_shop_owner')) {
			$this->qry->Where('users.is_shop_owner', $this->getSrchVal('is_shop_owner'));
		}
		if($this->getSrchVal('is_allowed_to_add_product')) {
			$this->qry->Where('users.is_allowed_to_add_product', $this->getSrchVal('is_allowed_to_add_product'));
		}
		if($this->getSrchVal('from_date')) {
			$this->qry->where('shop_details.created_at', '>=', $this->getSrchVal('from_date'));
		}
		if($this->getSrchVal('to_date')) {
			$this->qry->where('shop_details.created_at', '<=', $this->getSrchVal('to_date'));
		}
		if($this->getSrchVal('status')) {
			if($this->getSrchVal('status') == 'blocked') {
				$this->qry->Where('users.is_banned', 1);
			} else if($this->getSrchVal('status') == 'active') {
				$this->qry->Where('users.activated', 1);
			} else if($this->getSrchVal('status') == 'inactive') {
				$this->qry->Where('users.activated', 0);
			}
		}
		$this->qry->Where('users.is_featured_seller', 'Yes');
		$this->qry->orderBy('users.created_at', 'desc');
		return $this->qry;
	}

	public function changeFeaturedStatus($seller_id, $status)
	{
		if($status == 'Yes') {
			$affected_rows = \User::where('id', '=', $seller_id)->update( array('is_featured_seller' => $status));
		}
		else{
			$affected_rows = \User::where('id', '=', $seller_id)->update( array('is_featured_seller' => $status, 'featured_seller_expires' => '0000-00-00 00:00:00'));
		}
		if($affected_rows) {
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		return false;
	}
	//Featured prodcuts list end

	public function updateFeaturedSellerExpiryDate($usr_details, $plan_details) {
		$seller_id = $usr_details['id'];
		$number_of_days = $plan_details['featured_days'];
		if($number_of_days > 0)
			$date = date('Y-m-d', strtotime("+".$number_of_days." days"));
		User::where('id', '=', $seller_id)->update(array('featured_seller_expires' => $date, 'is_featured_seller' => 'Yes'));
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function setFeaturedSellerTransaction($usr_details, $plan_details) {
		$seller_id = $usr_details['id'];
		$featured_price = $plan_details['featured_price'];

		$credit_obj = \Credits::initialize();
		$credit_obj->setUserId($seller_id);
		$credit_obj->setCurrency(Config::get('generalConfig.site_default_currency'));
		$credit_obj->setAmount($featured_price);
		$credit_obj->creditAndDebit('amount', 'minus');

		$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
		$credit_obj->setCurrency(Config::get('generalConfig.site_default_currency'));
		$credit_obj->setAmount($featured_price);
		$credit_obj->creditAndDebit('amount', 'plus');

		//Add the site transaction
		$trans_obj = new \SiteTransactionHandlerService();
		$transaction_arr['date_added'] = new DateTime;
		$transaction_arr['user_id'] = $seller_id;
		$transaction_arr['transaction_type'] = 'debit';
		$transaction_arr['amount'] = $featured_price;
		$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
		$transaction_arr['transaction_key'] = 'seller_featured_fee';
		$transaction_arr['reference_content_table'] = 'users';
		$transaction_arr['reference_content_id'] = $seller_id;
		$transaction_arr['status'] = 'completed';
		$transaction_arr['transaction_notes'] = 'Debited seller featured fee from wallet';
		$transaction_arr['payment_type'] = 'wallet';
		$trans_id = $trans_obj->addNewTransaction($transaction_arr);

		$transaction_arr['user_id'] = Config::get('generalConfig.admin_id');
		$transaction_arr['transaction_type'] = 'credit';
		$transaction_arr['transaction_notes'] = 'Credited seller featured fee to wallet for seller id: '.$usr_details['id'];
		$trans_id = $trans_obj->addNewTransaction($transaction_arr);
	}

	public function featuredSellersCount()
	{
		$cache_key = 'feature_sellers_count_cache_key';
		$featured_sellers_cnt = 0;
		if (($featured_sellers_cnt = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$featured_sellers = DB::table('shop_details')->Select("users.id");
			$featured_sellers = $featured_sellers->join('users', function($join)		                         {
										 $join->on('shop_details.user_id', '=', 'users.id');
										 $join->where('users.activated', '=', 1);
										 $join->where('users.is_banned', '=', 0);
										 $join->where('users.shop_status', '=', 1);
									 });
			$featured_sellers = $featured_sellers->Where('users.is_featured_seller', 'Yes');
			$featured_sellers_cnt = $featured_sellers->count();
			HomeCUtil::cachePut($cache_key, $featured_sellers_cnt, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $featured_sellers_cnt;
	}

	public static function getFeaturedSellers($limit = 8, $load_view = false)
	{
		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$cache_key = 'feature_seller_key_'.$load_view;
			$featured_sellers_service = new FeaturedSellersService();
			$shop_obj = Products::initializeShops();

			$featured_sellers_query = DB::table('shop_details')->Select("shop_details.*","users.first_name", "users.last_name", "users.user_name", "users.email");

			$featured_sellers_query = $featured_sellers_query->join('users', function($join)
									 {
										 $join->on('shop_details.user_id', '=', 'users.id');
										 $join->where('users.activated', '=', 1);
										 $join->where('users.is_banned', '=', 0);
										 $join->where('users.shop_status', '=', 1);
									 });

			$featured_sellers_query = $featured_sellers_query->Where('users.is_featured_seller', 'Yes');
			$featured_sellers_query = $featured_sellers_query->orderBy('users.created_at', 'desc');

			if($limit != '' && $limit > 0){
				$cache_key .= '_L_'.$limit;
				$featured_sellers_query = $featured_sellers_query->take($limit);
			}
			if (($featured_sellers = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$featured_sellers = $featured_sellers_query->get();
				HomeCUtil::cachePut($cache_key, $featured_sellers, Config::get('generalConfig.cache_expiry_minutes'));
			}			
			
			if($load_view)
				return View::make('featuredsellers::featuredSellersIndex', compact('featured_sellers', 'featured_sellers_service', 'shop_obj'));
			else
				return $featured_sellers;
		}
	}

	public static function getFeaturedSellersAfterLogin($limit = 6)
	{
		$d_arr = array();
		$featured_sellers_arr = array();
		$featured_sellers_service = new FeaturedSellersService();
		$shop_obj = Products::initializeShops();
		$featured_sellers = $featured_sellers_service->getFeaturedSellers($limit, false);
		if(count($featured_sellers) > 0) {
			foreach($featured_sellers as $key => $seller) {
				$seller_arr = (array) $seller;
				$seller_id = $seller_arr['user_id'];
				$seller_user_code = BasicCUtil::setUserCode($seller_id);
				$seller_profile_url = CUtil::userProfileUrl($seller_user_code);
				$seller_shop_url = URL::to('shop/'.$seller_arr['url_slug']);
				$seller_image = CUtil::getUserPersonalImage($seller_id, "thumb");
				$display_name = '';
                if(isset($seller_arr['first_name']) && isset($seller_arr['last_name'])) {
					$display_name = ucfirst($seller_arr['first_name']).' '.ucfirst($seller_arr['last_name']);
				}
				$featured_sellers_arr[$key]['seller_details'] = $seller_arr;
				$featured_sellers_arr[$key]['seller_profile_url'] = $seller_profile_url;
				$featured_sellers_arr[$key]['seller_shop_url'] = $seller_shop_url;
				$featured_sellers_arr[$key]['seller_image'] = $seller_image;
				$featured_sellers_arr[$key]['seller_name'] = $display_name;
			}
		}
		$d_arr['featured_sellers'] = $featured_sellers_arr;
		$d_arr['featured_sellers_total'] = $featured_sellers_service->featuredSellersCount();
		$d_arr['featured_sellers_url'] = Url::to('shop?list_type=featured');
		return View::make('featuredsellers::featuredSellers', compact('d_arr'));
	}

	public static function getFeaturedSellersBlock($seller_id = 0)
	{
		if(CUtil::chkIsAllowedModule('featuredsellers')) {
			$featured_sellers_service = new FeaturedSellersService();
			$d_arr['seller_id'] = $seller_id;
			$d_arr['user_account_balance'] = \CUtil::getUserAccountBalance($seller_id);
			$plans_arr = $featured_sellers_service->getFeaturedSellersPlans();
			$d_arr['plans_arr'] = array('' => trans('common.select')) + $plans_arr;
			return View::make('featuredsellers::setAsFeaturedSellersBlock', compact('d_arr'));
		}
	}
}