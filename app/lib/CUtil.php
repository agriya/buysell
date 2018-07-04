<?php
/**
 * Common Utils
 *
 * @package
 * @author Ahsan
 * @copyright Copyright (c) 2014
 * @version $Id$
 * @access public
 */
class CUtil
{
	public static $u_details = array();
	public static $u_image = array();

	/**
	 * CUtil::generateRandomUniqueCode()
	 *
	 * @param mixed $prefix_code
	 * @param mixed $table_name
	 * @param mixed $field_name
	 * @return
	 */
	public static function generateRandomUniqueCode($prefix_code, $table_name, $field_name)
	{
		if($table_name == 'users')
			$unique_code = $prefix_code.mt_rand(10000000,99999999);
		else
			$unique_code = $prefix_code.mt_rand(100000,999999);
		$code_count = 	DB::table($table_name)->whereRaw($field_name." = ? ", array($unique_code))->count();
		if($code_count > 0)
		{
			return CUtil::generateRandomUniqueCode($prefix_code, $table_name, $field_name);
		}
		else
		{
			return $unique_code;
		}
		return $unique_code;
	}

	/**
	 * CUtil::getWalletAccountDetails()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static function getWalletAccountDetails($user_id = 0)
	{
		$credits_obj = Credits::initialize();
		$withdraw_obj = Credits::initializeWithDrawal();

		if($user_id == '' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();
		$credits_obj->setUserId($user_id);
		$credits_obj->setFilterUserId($user_id);
		$credits_obj->setFilterCurrency(Config::get('generalConfig.site_default_currency'));

		$account_balance_arr = $credits_obj->getWalletAccountBalance();
		if(is_string($account_balance_arr) || count($account_balance_arr) <= 0)
		{
			$account_balance_arr[] = array('amount'=>'0.00','currency' => Config::get('generalConfig.site_default_currency'));
		}
		return $account_balance_arr;
	}

	/**
	 * CUtil::getUserAccountBalance()
	 *
	 * @param mixed $user_id
	 * @param string $currency
	 * @return
	 */
	public static function getUserAccountBalance($user_id, $currency = 'USD')
	{
		$currency = Config::get('generalConfig.site_default_currency');
		$acc_balance_arr = array('amount'=>'0.00','currency' => $currency);
		$credits_obj = Credits::initialize();
		$credits_obj->setUserId($user_id);
		$credits_obj->setFilterUserId($user_id);
		$credits_obj->setFilterCurrency($currency);
		$account_balance_arr = $credits_obj->getWalletAccountBalance();
		//echo '<pre>';print_r($account_balance_arr);echo '</pre>';
		if(count($account_balance_arr) > 0) {
			if(isset($account_balance_arr[0]['amount']) && $account_balance_arr[0]['amount'] > 0) {
				$acc_balance_arr = array('amount' => $account_balance_arr[0]['amount'], 'currency' => $currency);
			}
		}
		return $acc_balance_arr;
	}

	/**
	 * CUtil::getUserDetails()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static $_user_details;
	public static function getUserDetails($user_id)
	{
		if (isset(CUtil::$_user_details[$user_id])) {
			$user_details = CUtil::$_user_details[$user_id];
		} else {
			$cache_key = 'UDVCK_'.$user_id;
			$user_details = array('id' => $user_id,
								 'user_code' => '',
								 'first_name' => '',
								 'last_name' => '',
								 'email' => '',
								 'user_name' => '',
								 'display_name' => '',
								 'profile_url' => '',
								 'favorites_url' => '',
								 'user_group_id' => '',
								 'user_group_name' => '',
								 'is_shop_owner' => 'No',
								 'shop_status' => '0',
								 'paypal_id' => '',
								 'total_products' => '0',
								 'is_banned' => '0',
								 );
			if (($user_info = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$user_info = DB::select('SELECT id, email, first_name, last_name, user_name, activated, is_shop_owner, shop_status, paypal_id, total_products, is_banned
										 FROM users WHERE users.id = '.$user_id);
				HomeCUtil::cachePut($cache_key, $user_info, Config::get('generalConfig.cache_expiry_minutes'));
			}
			if(count($user_info) > 0) {
				foreach($user_info AS $user_det) {
					$user_details['user_code'] = BasicCUtil::setUserCode($user_id);
					if(isset($user_det->fname)) {
						$user_details['first_name'] = $user_det->first_name;
					}
					if(isset($user_det->lname)) {
						$user_details['last_name'] = $user_det->last_name;
					}
					if(isset($user_det->user_name)) {
						$user_details['user_name'] = $user_det->user_name;
					}
					if(isset($user_det->email)) {
						$user_details['email'] = $user_det->email;
					}
					if(isset($user_det->first_name) && isset($user_det->last_name)) {
						//$user_details['display_name'] = ucfirst($user_det->first_name).' '.ucfirst(substr($user_det->last_name, 0, 1));
						$user_details['display_name'] = ucfirst($user_det->first_name).' '.ucfirst($user_det->last_name);
					}
					if(isset($user_det->is_shop_owner) && isset($user_det->is_shop_owner)) {
						$user_details['is_shop_owner'] = ucfirst($user_det->is_shop_owner);
					}
					if(isset($user_det->shop_status) && isset($user_det->shop_status)) {
						$user_details['shop_status'] = $user_det->shop_status;
					}
					if(isset($user_det->paypal_id) && isset($user_det->paypal_id)) {
						$user_details['paypal_id'] = $user_det->paypal_id;
					}
					if(isset($user_det->total_products) && isset($user_det->total_products)) {
						$user_details['total_products'] = $user_det->total_products;
					}

					if(isset($user_det->is_banned)) {
						$user_details['is_banned'] = $user_det->is_banned;
					}
				}
				$group_id = BasicCUtil::getUserGroupId($user_id);
				$user_details['user_group_id'] = $group_id;
				$user_details['user_group_name'] = BasicCUtil::getUserGroupName($group_id);
			}
			$user_details['profile_url'] = CUtil::userProfileUrl($user_details['user_code']);//URL::to('user/'.$user_details['user_code']);//."-". strtolower(str_replace(" ","", $user_details['first_name']));
			$user_details['favorites_url'] = CUtil::userFavoritesUrl($user_details['user_code']);
			CUtil::$_user_details[$user_id] = $user_details;
		}
		return $user_details;
	}

	/**
	 * CUtil::getIndexUserDetails()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static $_index_user_details;
	public static function getIndexUserDetails($user_id)
	{
		if (isset(CUtil::$_user_details[$user_id])) {
			$user_details = CUtil::$_user_details[$user_id];
		} else {
			$cache_key = 'user_info_'.$user_id;
			if (($user_info = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$user_details = array('id' => $user_id,
										 'user_code' => '',
										 'first_name' => '',
										 'last_name' => '',
										 'email' => '',
										 'user_name' => '',
										 'display_name' => '',
										 'profile_url' => '',
										 'favorites_url' => '',
										 'user_group_id' => '',
										 'user_group_name' => '',
										 'is_shop_owner' => 'No',
										 'shop_status' => '0',
										 'paypal_id' => '',
										 'total_products' => '0',
										 'is_banned' => '0',
										 );
				$user_info = DB::select('SELECT id, email, first_name, last_name, user_name, activated, is_shop_owner, shop_status, paypal_id, total_products, is_banned
										 FROM users WHERE users.id = '.$user_id.' AND is_banned = 0 AND activated = 1');
				HomeCUtil::cachePut($cache_key, $user_info, Config::get('generalConfig.cache_expiry_minutes'));
			}
			if(count($user_info) > 0) {
				foreach($user_info AS $user_det) {
					$user_details['user_code'] = BasicCUtil::setUserCode($user_id);
					if(isset($user_det->fname)) {
						$user_details['first_name'] = $user_det->first_name;
					}
					if(isset($user_det->lname)) {
						$user_details['last_name'] = $user_det->last_name;
					}
					if(isset($user_det->user_name)) {
						$user_details['user_name'] = $user_det->user_name;
					}
					if(isset($user_det->email)) {
						$user_details['email'] = $user_det->email;
					}
					if(isset($user_det->first_name) && isset($user_det->last_name)) {
						//$user_details['display_name'] = ucfirst($user_det->first_name).' '.ucfirst(substr($user_det->last_name, 0, 1));
						$user_details['display_name'] = ucfirst($user_det->first_name).' '.ucfirst($user_det->last_name);
					}
					if(isset($user_det->is_shop_owner) && isset($user_det->is_shop_owner)) {
						$user_details['is_shop_owner'] = ucfirst($user_det->is_shop_owner);
					}
					if(isset($user_det->shop_status) && isset($user_det->shop_status)) {
						$user_details['shop_status'] = $user_det->shop_status;
					}
					if(isset($user_det->paypal_id) && isset($user_det->paypal_id)) {
						$user_details['paypal_id'] = $user_det->paypal_id;
					}
					if(isset($user_det->total_products) && isset($user_det->total_products)) {
						$user_details['total_products'] = $user_det->total_products;
					}

					if(isset($user_det->is_banned)) {
						$user_details['is_banned'] = $user_det->is_banned;
					}
				}
				$user_details['profile_url'] = CUtil::userProfileUrl($user_details['user_code']);//URL::to('user/'.$user_details['user_code']);//."-". strtolower(str_replace(" ","", $user_details['first_name']));
				$user_details['favorites_url'] = CUtil::userFavoritesUrl($user_details['user_code']);
			} else {
				$user_details = array();
			}
			CUtil::$_user_details[$user_id] = $user_details;
		}
		return $user_details;
	}

	/**
	 * CUtil::userProfileUrl()
	 *
	 * @param mixed $user_code
	 * @return
	 */
	public static function userProfileUrl($user_code = null){
		if(is_null($user_code))
			return '#';

		if(is_int($user_code))
			$user_code = BasicCUtil::setUserCode($user_code);
		return URL::to('user/'.$user_code);
	}

	/**
	 * CUtil::userProfileUrl()
	 *
	 * @param mixed $user_code
	 * @return
	 */
	public static function userFavoritesUrl($user_code = null, $type = 'product'){
		if(is_null($user_code))
			return '#';

		if(is_int($user_code))
			$user_code = BasicCUtil::setUserCode($user_code);
		return URL::to('favorite/'.$user_code);
	}

	/**
	 * CUtil::wordWrap()
	 * added by ravikumar_131at10
	 *
	 * @param mixed $text
	 * @param integer $textLimit
	 * @return
	 */
	public static function wordWrap($text, $textLimit = 100, $extra_char = '...')
	{	if(strlen($text) > $textLimit)
		{
			$return_str = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $textLimit));
			return $return_str.$extra_char;
		}
		return $text;
	}

	/**
	 * CUtil::isMember()
	 * added by periyasami_145at11
	 *
	 * @return boolean
	 */
	public static $_is_member = NULL;
	public static function isMember()
	{
		if (isset(CUtil::$_is_member) && CUtil::$_is_member != NULL) {
			$ok = CUtil::$_is_member;
		} else {
			$ok = false;
			if(BasicCUtil::sentryCheck()) {
				if(Sentry::getUser() AND Sentry::getUser()->id) {
					$ok = true;
				}
			}
			CUtil::$_is_member = $ok;
		}
		return $ok;
	}

	/**
	 * CUtil::isAdmin()
	 * added by manikandan_133at10
	 *
	 * @return boolean
	 */
	public static $_is_admin = NULL;
	public static function isAdmin()
	{
		if (isset(CUtil::$_is_admin) && CUtil::$_is_admin != NULL) {
			$ok = CUtil::$_is_admin;
		} else {
			$ok = false;
			if(BasicCUtil::sentryCheck()) {
				if(Sentry::getUser()->hasAnyAccess(['system', 'system.Admin']))
				{
					$ok = true;
				}
			}
			CUtil::$_is_admin = $ok;
		}

		return $ok;
	}

	/**
	 * CUtil::isStaff()
	 * added by periyasami_145at11
	 *
	 * @return boolean
	 */
	public static function isStaff()
	{
		return hasAdminAccess();
	}

	/**
	 * CUtil::isSuperAdmin()
	 * added by periyasami_145at11
	 * To check whether the logged user is admin
	 * @return boolean
	 */
	public static function isSuperAdmin()
	{
		return isSuperAdmin();
	}


	/**
	 * CUtil::FMTDate()
	 * Added by ravikumar_131at10
	 *
	 * @param mixed $value
	 * @param mixed $in
	 * @param mixed $out
	 * @return
	 */
	public static function FMTDate($value, $in, $out)
	{
		//laravel created_at field returns -0001-11-30 00:00:00 for 0000-00-00 00:00:00
		if(!$value OR $value == '0000-00-00' OR $value == '0000-00-00 00:00:00' OR $value == '-0001-11-30 00:00:00')
		{
			return '';
		}
		if($out == '')
		{
			$out = Config::get('generalConfig.display_date_format');
		}
		if($out == 'ago')
		{
			return CUtil::timeElapsedString($value);
		}
		return DateTime::createFromFormat($in, $value)->format($out);
	}

	/**
	 * CUtil::populateAnalyticsHiddenFields()
	 *
	 * @return
	 */
	public static function populateAnalyticsHiddenFields()
	{
		foreach(Config::get('generalConfig.user_geo_analytics') as $analytics_val)
		{
			?>
			<input type="hidden" name="<?php echo $analytics_val; ?>" id="<?php echo $analytics_val; ?>">
	<?php
		}
	}

	/**
	 * Added by: ravikumar_131at10
	 *
	 * @return 		void
	 * @access 		public
	 */
	public static function getMemberbreadCramb()
	{
		//Login
		$page_name = 'user_home';
		if(Request::is('*/login')) {
			$page_name = 'login';
		}
		if(Request::is('*/forgotpassword')) {
			$page_name = 'users_forgotpassword';
		}

		//Signup
		if(Request::is('*/signup')) {
			$page_name = 'signup';
		}

		//View Cart
		if(Request::is('cart')) {
			$page_name = 'cart';
		}
		if(Request::is('checkout')) {
			$page_name = 'checkout';
		}
		if(Request::is('pay-checkout/*')) {
			$page_name = 'pay-checkout';
		}

		return $page_name;
	}

	/**
	 * CUtil::getAdminBreadCrumb()
	 * Added by: ravikumar_131at10
	 *
	 * @return
	 */
	public static function getAdminBreadCrumb()
	{
		//Manage Member
		$page_name = 'home';
		if(Request::is('*/users/add'))
		{
			$page_name = 'users_add';
		}
		if(Request::is('*/users/edit/*'))
		{
			$page_name = 'users_edit';
		}

		//Manage Group
		if(Request::is('*/group'))
		{
			$page_name = 'group';
		}
		if(Request::is('*/group/add'))
		{
			$page_name = 'add_group';
		}
		if(Request::is('*/group/edit'))
		{
			$page_name = 'edit_group';
		}
		if(Request::is('*/group/list-group-members'))
		{
			$page_name = 'member_group';
		}

		//Manage Product
		if(Request::is('*/product/list'))
		{
			$page_name = 'product_list';
		}
		if(Request::is('*/product/add') && !Input::has('id'))
		{
			$page_name = 'product_add';
		}
		if(Request::is('*/product/add') && Input::has('id'))
		{
			$page_name = 'product_edit';
		}
		if(Request::is('*/product/view/*'))
		{
			$page_name = 'product_view';
		}
		if(Request::is('*/manage-stocks'))
		{
			$page_name = 'manage_stocks';
		}

		//Manage Product Categories
		if(Request::is('*/manage-product-catalog'))
		{
			$page_name = 'manage-product-catalog';
		}

		//Product Attributes Management
		if(Request::is('*/product-attributes'))
		{
			$page_name = 'product-attributes';
		}

		//Manage Taxations
		if(Request::is('*/taxations/index'))
		{
			$page_name = 'taxations_index';
		}
		if(Request::is('*/taxations/add-taxation'))
		{
			$page_name = 'taxations_add';
		}
		if(Request::is('*/taxations/update-taxation/*'))
		{
			$page_name = 'taxations_update';
		}

		//Cancellation Policy
		if(Request::is('*/cancellation-policy/index'))
		{
			$page_name = 'cancellation_policy';
		}

		//My Purchases List
		if(Request::is('*/purchases/index'))
		{
			$page_name = 'purchases_index';
		}
		if(Request::is('*/purchases/order-details/*'))
		{
			$page_name = 'purchases_orderdetails';
		}

		//Unpaid Invoice List
		if(Request::is('*/unpaid-invoice-list/index'))
		{
			$page_name = 'unpaid_invoices_list';
		}

		//Withdrawal Request List
		if(Request::is('*/withdrawals/index'))
		{
			$page_name = 'withdrawals_index';
		}

		if(Request::is('*/config-manage'))
		{
			$page_name = 'config_manage';
		}

		if(Request::is('*/site-logo/index'))
		{
			$page_name = 'site_logo';
		}

		if(Request::is('*/users'))
		{
			$page_name = 'users';
		}

		if(Request::is('*/seller-requests/*'))
		{
			$page_name = 'seller_requests';
		}

		if(Request::is('*/static-page/*'))
		{
			$page_name = 'manage_static_page';
		}

		if(Request::is('*/newsletter/*'))
		{
			$page_name = 'newsletter';
		}

		if(Request::is('*/newsletter/add'))
		{
			$page_name = 'add';
		}

		if(Request::is('*/sales-report/index'))
		{
			$page_name = 'sales_report';
		}

		if(Request::is('*/sales-report/product/*'))
		{
			$page_name = 'product_sales_report';
		}

		if(Request::is('*/sales-report/member-wise'))
		{
			$page_name = 'owner_wise_sales_report';
		}

		if(Request::is('*/taxations'))
		{
			$page_name = 'taxations';
		}

		if(Request::is('*/feedback/index'))
		{
			$page_name = 'feedback';
		}

		if(Request::is('*/users/user-details/*'))
		{
			$page_name = 'user_details';
		}


		if(Request::is('*/transactions/index'))
		{
			$page_name = 'transactions';
		}

		if(Request::is('*/product-category/category-meta-details'))
		{
			$page_name = 'category_meta_details';
		}

		if(Request::is('*/collections/index'))
		{
			$page_name = 'manage_collection';
		}

		if(Request::is('*/reported-products/index'))
		{
			$page_name = 'reported_products';
		}


		if(Request::is('*/sales-report/member/*'))
		{
			$page_name = 'view_owner_wise_sales_report';
		}

		if(Request::is('*/sell-static-page'))
		{
			$page_name = 'edit_sell_page_static_content';
		}

		if(Request::is('*/manage-credits'))
		{
			$page_name = 'manage_credits';
		}

		if(Request::is('*/site-wallet/index'))
		{
			$page_name = 'site_wallet';
		}

		//Manage Shipping template
		if(Request::is('*/shipping-template/index'))
		{
			$page_name = 'shipping_template_list';
		}
		if(Request::is('*/shipping-template/add'))
		{
			$page_name = 'shipping_template_add';
		}
		if(Request::is('*/shipping-template/edit/*'))
		{
			$page_name = 'shipping_template_edit';
		}
		if(Request::is('*/shipping-template/view-template/*'))
		{
			$page_name = 'shipping_template_view';
		}

		//Currency exchange rate
		if(Request::is('*/currency-exchange-rate'))
		{
			$page_name = 'currency_exchange_rate';
		}

		if(Request::is('*/manage-banner'))
		{
			$page_name = 'banner_manage';
		}
		if(Request::is('*/manage-favorite-sellers'))
		{
			$page_name = 'featured_sellers';
		}
		if(Request::is('*/manage-toppicks-users'))
		{
			$page_name = 'toppicks_users';
		}
		if(Request::is('*/manage-favorite-products'))
		{
			$page_name = 'favorite_produts';
		}
		if(Request::is('*/newsletter-subscriber/*'))
		{
			$page_name = 'newsletter_subscriber';
		}
		if(Request::is('*/mass-email/*'))
		{
			$page_name = 'mass_email';
		}
		return $page_name;
	}

 	/**
 	 * CUtil::DISP_IMAGE()
 	 * Added by: ravikumar_131at10
 	 *
 	 * @param integer $cfg_width
 	 * @param integer $cfg_height
 	 * @param integer $img_width
 	 * @param integer $img_height
 	 * @param mixed $as_array
 	 * @return
 	 */
 	public static function DISP_IMAGE($cfg_width = 0, $cfg_height = 0, $img_width = 0, $img_height = 0, $as_array = false)
	{
		$img_attrib = array('width'=>'', 'height'=>'');

		if ($cfg_width > 0 AND $cfg_height > 0 AND ($cfg_width < $img_width) AND ($cfg_height < $img_height))
			{
				$tmpHeight = ( $cfg_width / $img_width ) * $img_height;

				if( $tmpHeight <= $cfg_height )
					{
						$attr = " width=\"".$cfg_width."\"";
						$img_attrib['width'] = $cfg_width;
					}
				else
					{
						$height = $tmpHeight - ( $tmpHeight - $cfg_height );
						$attr = " height=\"".$height."\"";
						$img_attrib['height'] = $height;
					}
			}
		else if ($cfg_width > 0 AND $cfg_width < $img_width)
			{
				$attr = " width=\"".$cfg_width."\"";
				$img_attrib['width'] = $cfg_width;
			}
		else if ($cfg_height > 0 AND $cfg_height < $img_height)
			{
				$attr = " height=\"".$cfg_height."\"";
				$img_attrib['height'] = $cfg_height;
			}
		else
			{
				$attr = "";
			}

		if ($as_array)
			{
				return $img_attrib;
			}

		return $attr;
	}

	/* Auth functions start */

	/**
	 * CUtil::getAuthUser()
	 *
	 * @return
	 */
	public static function getAuthUser()
	{
		return  Sentry::getUser();
	}
	/* Auth functions end */

	/**
	 * CUtil::getUserIdFromSlug()
	 *
	 * @param mixed $seo_title
	 * @return
	 */
	public static function getUserIdFromSlug($seo_title)
	{
		$user_id = '';
		$matches = null;
		if($seo_title == '')
			return '';
		//preg_match('/^(U[0-9]{6})\-/', $seo_title, $matches);
		//if (!isset($matches[1])) {
		//	preg_match('/^(U[0-9]{3})$/', $seo_title, $matches);
		//}
		//if (isset($matches[1])){

		$user_id = $seo_title;
		$user_id = ltrim($user_id, 'U');
		$user_id = ltrim($user_id, '0');

		//}
		return $user_id;
	}

	/**
	 * CUtil::chkAndCreateFolder()
	 *
	 * @param mixed $folderName
	 * @return
	 */
	public static function chkAndCreateFolder($folderName)
	{
		$folder_arr = explode('/', $folderName);
		$folderName = '';
		foreach($folder_arr as $key=>$value)
			{
				$folderName .= $value.'/';
				if($value == '..' or $value == '.')
					continue;
				if (!is_dir($folderName))
					{
						mkdir($folderName);
						@chmod($folderName, 0777);
					}
			}
	}

	/**
	 * CUtil::getBaseAmountToDisplay()
	 *
	 * @param mixed $price
	 * @param mixed $currency
	 * @param mixed $return_as_arr
	 * @return
	 */
	public static function getBaseAmountToDisplay($price, $currency, $return_as_arr = false)
	{
		$currency_symbol = "USD";
		$currency_symbol_font = "$";

		$currency_details = Products::chkIsValidCurrency($currency);
		if(count($currency_details) > 0)
		{
			$currency_symbol = $currency_details["currency_code"];
			$currency_symbol_font = $currency_details["currency_symbol"];
			if($currency_symbol == "INR")
				$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
		}
		$formatted_amt = "";
		if(is_numeric($price))
			$formatted_amt = number_format ($price, 2, '.','');
		else
			$formatted_amt = $price;
		$formatted_amt = str_replace(".00", "", $formatted_amt);
		$formatted_amt = str_replace("Rs.", "", $formatted_amt);

		$amt = $formatted_amt;
		if($return_as_arr)
			return compact('currency_symbol','amt','currency_symbol_font');
		else
			return '<small class="text-muted">'.$currency_symbol. '</small> <strong>' . $formatted_amt.'</strong>';
	}

	/**
	 * CUtil::convertBaseCurrencyToUSD()
	 *
	 * @param mixed $amount
	 * @param string $base_currency
	 * @param mixed $exchange_rate_allow
	 * @return
	 */
	public static function convertBaseCurrencyToUSD($amount, $base_currency = "", $exchange_rate_allow = false)
	{
		if($amount == "")
			$amount = "0";

		if(doubleval($amount) > 0)
		{
			$amt = $amount;
			if($base_currency != "USD")
			{
				$currency_details = Products::chkIsValidCurrency($base_currency);

				if(count($currency_details) > 0)
				{
					$static_exchange_rate_curr = Config::get('payment.static_exchange_rate_currencies');
					$exchange_rate = doubleval($currency_details['exchange_rate']);
					if(in_array($base_currency, $static_exchange_rate_curr)) {
						$exchange_rate = doubleval($currency_details['exchange_rate_static']);
					}

					if($exchange_rate_allow)
					{
						$exchange_price = $exchange_rate * (doubleval(Config::get("webshoppack.site_exchange_rate")) * 0.01);
						$exchange_rate = $exchange_rate - $exchange_price;
					}
					$amt = $amt / $exchange_rate;
				}
			}
			return $amt;
		}
		return $amount;
	}

	/**
	 * CUtil::getUserId()
	 *
	 * @param mixed $user_code
	 * @return
	 */
	public static function getUserId($user_code)
	{
		$user_id = preg_replace("/U(0)*/", '', $user_code);
		return $user_id;
	}

	/**
	 * CUtil::setOrderCode()
	 *
	 * @param mixed $order_id
	 * @return
	 */
	public static function setOrderCode($order_id)
	{
		$order_code = str_pad($order_id, 6, "0", STR_PAD_LEFT);
		return "O".$order_code;
	}

	/**
	 * CUtil::getOrderId()
	 *
	 * @param mixed $order_code
	 * @return
	 */
	public static function getOrderId($order_code)
	{
		$order_id = preg_replace("/O(0)*/", '', $order_code);
		return $order_id;
	}

	/**
	 * CUtil::formatAmount()
	 *
	 * @param mixed $amount
	 * @return
	 */
	public static function formatAmount($amount)
	{
		if(is_null($amount) || $amount=='')
			$amount = 0;
		return number_format ($amount, 2, '.',''); //with out thousand separator
	}

	/**
	 * CUtil::isShopOwner()
	 * added by manikandan_133at10
	 *
	 * @return boolean
	 */
	public static function isShopOwner($user_id = null, $shop_obj = null)
	{
		if(is_null($user_id))
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
		}
		else
			$logged_user_id = $user_id;

		if(is_null($shop_obj))
			$shop_obj = Products::initializeShops();
		if($logged_user_id > 0)
		{
			$details = $shop_obj->getUsersShopDetails($logged_user_id);
			//$details = UsersShopDetails::Select('is_shop_owner', 'paypal_id')->where('user_id', $logged_user_id)->first();
			if(count($details)) {
				$is_shop_owner = $details['is_shop_owner'];
				$paypal_id = $details['paypal_id'];
				//if($is_shop_owner == 'Yes' && $paypal_id != '') {
				if($is_shop_owner == 'Yes') {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * CUtil::getCurrencyBasedAmount()
	 *
	 * @param mixed $base_amount
	 * @param mixed $usd_amount
	 * @param mixed $base_currency
	 * @param mixed $return_as_arr
	 * @return
	 */
	public static function getCurrencyBasedAmount($base_amount, $usd_amount, $base_currency, $return_as_arr = false)
	{
		if($usd_amount != "")
		{
			$amt = $usd_amount;
			$currency_symbol = "USD";
			$currency_symbol_font = "$";
			$fetched_api_currency = "";
			$return_arr = compact('amt','currency_symbol','currency_symbol_font');
			$currency_code = Config::get("generalConfig.site_cookie_prefix")."_selected_currency";
			if(BasicCUtil::getCookie($currency_code) == "")
				$fetched_api_currency = BasicCUtil::getLocatorApiCurrencyCode();

			if(Config::get("generalConfig.currency_is_multi_currency_support"))
			{
				if(BasicCUtil::getCookie($currency_code) != "" || $fetched_api_currency != "")
				{
					$currency_details = array();
					//Check whether the currency in coookie is Active status
					if($fetched_api_currency != "")
						$currency_details = Products::chkIsValidCurrency($fetched_api_currency);
					else
						$currency_details = Products::chkIsValidCurrency(BasicCUtil::getCookie($currency_code));
					if(count($currency_details) > 0)
					{
						if($fetched_api_currency != "")
						{
							if($base_currency == $fetched_api_currency) {
								$amt = $base_amount;
								$currency_symbol = $currency_details["currency_code"];
								$currency_symbol_font = $currency_details["currency_symbol"];
								$return_arr = compact('amt','currency_symbol','currency_symbol_font');
								if($currency_symbol == "INR")
									$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
							}
							else
							{
								if($currency_details["currency_code"] != "USD")
								{
									$static_exchange_rate_curr = Config::get('payment.static_exchange_rate_currencies');
									$exchange_rate = doubleval($currency_details['exchange_rate']);
									if(in_array($base_currency, $static_exchange_rate_curr)) {
										$exchange_rate = doubleval($currency_details['exchange_rate_static']);
									}

									//Currency 2 = currency1 x exchange rate.
									$amt = $amt * $exchange_rate;
									$currency_symbol = $currency_details["currency_code"];
									$currency_symbol_font = $currency_details["currency_symbol"];
									$return_arr = compact('amt','currency_symbol','currency_symbol_font');
									if($currency_symbol == "INR")
										$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
								}
							}
						}
						else
						{
							if($base_currency == BasicCUtil::getCookie($currency_code))
							{
								//$amt = $base_amount.toDouble;
								$amt = $base_amount;
								$currency_symbol = $currency_details["currency_code"];
								$currency_symbol_font = $currency_details["currency_symbol"];
								$return_arr = compact('amt','currency_symbol','currency_symbol_font');
								if($currency_symbol == "INR")
									$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
							}
							else
							{
								if($currency_details["currency_code"] != "USD")
								{
									$static_exchange_rate_curr = Config::get('payment.static_exchange_rate_currencies');
									$exchange_rate = doubleval($currency_details['exchange_rate']);
									if(in_array($base_currency, $static_exchange_rate_curr)) {
										$exchange_rate = doubleval($currency_details['exchange_rate_static']);
									}

									//Currency 2 = currency1 x exchange rate.
									$amt = $amt * $exchange_rate;
									$currency_symbol = $currency_details["currency_code"];
									$currency_symbol_font = $currency_details["currency_symbol"];
									$return_arr = compact('amt','currency_symbol','currency_symbol_font');
									if($currency_symbol == "INR")
										$currency_symbol_font = "<em class=\"clsWebRupe\">".$currency_details["currency_symbol"]."</em>";
								}
							}
						}
					}
				}
			}

			$formatted_amt = "";
			$formatted_amt = number_format ($amt, 2, '.','');
			$formatted_amt = str_replace(".00", "", $formatted_amt);
			$formatted_amt = str_replace("Rs.", "", $formatted_amt);

		//	$currencyFormatter = NumberFormat.getCurrencyInstance(new Locale("en", "IN"));
		//	formatted_amt = currencyFormatter.format(amt);
		//	formatted_amt = formatted_amt.replace(".00","");
		//	formatted_amt = formatted_amt.replace("Rs.","");
		//	return "<span class=\"clsPriSym\">"+currency_symbol+"</span>" + " " + currency_symbol_font + formatted_amt;
			if($return_as_arr)
			{
				$return_arr['amt'] = (isset($return_arr['amt']) && $return_arr['amt'] >=0)?number_format ($return_arr['amt'], 2, '.',''):0;
				return $return_arr;
			}
			else
			{
				return '<strong>'.$currency_symbol_font.$formatted_amt.'</strong> <small class=\'text-muted\'>'.$currency_symbol.'</small>';
				//return '<strong>' . $currency_symbol_font .''. $formatted_amt.'</strong>';
			}
		}
		return "";
	}

	/**
	 * CUtil::getContents()
	 *
	 * @param mixed $url
	 * @return
	 */
	public static function getContents($url)
	{
		$result = '';

		if(!strstr($url, '://'))
			$url = 'http://'.$url;

		if (function_exists('curl_init'))
			{
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2');
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    $result = curl_exec($ch);
			    if (!curl_errno($ch))
			        curl_close($ch);
			     else
			        $result = false;
			}
		else
			{
				set_time_limit(180);
				$result = file_get_contents($url) ;
			}
		return $result;
	}

	/**
	 * CUtil::getUserPersonalImage()
	 *
	 * @param mixed $user_id
	 * @param string $image_size
	 * @param mixed $cache
	 * @return
	 */
	public static $_u_image = array();
	public static function getUserPersonalImage($user_id, $image_size = "small", $cache = true)
	{
		if(isset(CUtil::$_u_image[$user_id]))
			return CUtil::$_u_image[$user_id];

		$image_exists = false;
		$image_details = array();

		if(BasicCUtil::sentryCheck() && $user_id == Sentry::getUser()->id) {
			$user_imageInfo = Sentry::getUser();
		} else {
			$cache_key = 'user_personal_image_'.$user_id;
			if (($user_imageInfo = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$user_imageInfo = User::whereRaw('id = ? ', array($user_id))->first();
				HomeCUtil::cachePut($cache_key, $user_imageInfo, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		if(count($user_imageInfo) > 0 && $user_imageInfo->image_name!='')
		{
			$image_exists = true;
			$image_details["image_id"] = $user_imageInfo->image_id;
			$image_details["image_ext"] = $user_imageInfo->image_ext;
			$image_details["image_name"] = $user_imageInfo->image_name;
			$image_details["image_server_url"] = $user_imageInfo->image_server_url;
			$image_details["image_large_width"] = $user_imageInfo->large_width;
			$image_details["image_large_height"] = $user_imageInfo->large_height;
			$image_details["image_small_width"] = $user_imageInfo->small_width;
			$image_details["image_small_height"] = $user_imageInfo->small_height;
			$image_details["image_thumb_width"] = $user_imageInfo->thumb_width;
			$image_details["image_thumb_height"] = $user_imageInfo->thumb_height;
			$image_details["image_folder"] = Config::get("generalConfig.user_image_folder");
		}
		$image_details["image_exists"] = $image_exists;

		$image_path = "";
		$image_url = "";
		$image_attr = "";
		if($image_exists)
			$image_path = URL::asset(Config::get("generalConfig.user_image_folder"))."/";

		$cfg_user_img_large_width = Config::get("generalConfig.user_image_large_width");
		$cfg_user_img_large_height = Config::get("generalConfig.user_image_large_height");
		$cfg_user_img_thumb_width = Config::get("generalConfig.user_image_thumb_width");
		$cfg_user_img_thumb_height = Config::get("generalConfig.user_image_thumb_height");
		$cfg_user_img_small_width = Config::get("generalConfig.user_image_small_width");
		$cfg_user_img_small_height = Config::get("generalConfig.user_image_small_height");

		switch($image_size)
		{
			case 'large':
				$image_url = URL::asset("images/no_image").'/usernoimage-140x140.jpg';

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $cfg_user_img_large_width, $cfg_user_img_large_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_L.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_large_width, $cfg_user_img_large_height, $image_details["image_large_width"], $image_details["image_large_height"]);
				}
				break;

			case "thumb":

				$image_url = URL::asset("images/no_image").'/usernoimage-90x90.jpg';

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $cfg_user_img_thumb_width, $cfg_user_img_thumb_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_T.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_thumb_width, $cfg_user_img_thumb_height, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
				}
				break;

			case "small":

				$image_url = URL::asset("images/no_image").'/usernoimage-50x50.jpg';

				$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $cfg_user_img_small_width, $cfg_user_img_small_height);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_S.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE($cfg_user_img_small_width, $cfg_user_img_small_height, $image_details["image_small_width"], $image_details["image_small_height"]);
				}
				break;

			default:

				$image_url = URL::asset("images/no_image").'/usernoimage-90x90.jpg';

				$image_attr = BasicCUtil::TPL_DISP_IMAGE(90, 90, 90, 90);

				if($image_exists)
				{
					$image_url =  $image_path . $image_details["image_name"]."_T.".$image_details["image_ext"];
					$image_attr = BasicCUtil::TPL_DISP_IMAGE(90, 90, $image_details["image_thumb_width"], $image_details["image_thumb_height"]);
				}
				break;
		}
		$image_details['image_url'] = $image_url;
		$image_details['image_attr'] = $image_attr;
		CUtil::$_u_image[$user_id] = $image_details ;
		return CUtil::$_u_image[$user_id];

	}

	/**
	 * CUtil::makeClickableLinks()
	 * added by manikandan_133at10
	 *
	 * @param mixed $text
	 * @return
	 */
	public static function makeClickableLinks($text)
	{
		$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
		$ret = ' ' . $text;
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
		$ret = substr($ret, 1);
		return $ret;
	}

	/**
	 * CUtil::arraytolower()
	 *
	 * @param mixed $array
	 * @param integer $round
	 * @return
	 */
	public static function arraytolower(array $array, $round = 0)
	{
	  	return unserialize(serialize($array));
	}

	/**
	 * CUtil::remminLength()
	 *
	 * @param mixed $val
	 * @return
	 */
	public static function remminLength($val)
	{
		if(strlen($val)>=4)
			return $val;
	}

	/**
	 * CUtil::getShopDetailsView()
	 *
	 * @param mixed $user_id
	 * @param mixed $load_view
	 * @param mixed $shop_obj
	 * @return
	 */
	public static function getShopDetailsView($user_id = null, $load_view = false, $shop_obj)
	{
		$ProductService = new ProductService;
		$prod_obj = Products::initialize();

		if(is_null($user_id))
			return '';
		$d_arr = array();
		$d_arr['shop_details'] = $shop_obj->getShopDetails($user_id);
		$prod_obj->setProductPagination(12);
		$prod_obj->setFilterProductStatus('Ok');
		$prod_obj->setFilterProductExpiry(true);
		$d_arr['shop_product_list'] = $prod_obj->getProductsList($user_id);
		if(!empty($d_arr['shop_details']))
			$d_arr['shop_url'] = $ProductService->getProductShopURL($d_arr['shop_details']['id'], $d_arr['shop_details']);
		else
			$d_arr['shop_url'] = '#';
		if($load_view)
			return View::make('showShop', compact('d_arr', 'prod_obj'));
		else
			return $d_arr;
	}

	/**
	 * CUtil::getCollectionDetailsView()
	 *
	 * @param mixed $user_id
	 * @param mixed $load_view
	 * @return
	 */
	public static function getCollectionDetailsView($user_id = null, $load_view = false)
	{

		$collectionService = new CollectionService;
		$collectionService->setCollectionFilter(array('user_id' => $user_id));
		$collections = $collectionService->getCollectionsList('get', 2);

		foreach($collections as $key =>$collection)
		{
			$collection->product_ids = $collectionService->getCollectionProductIds($collection->id, 2);
		}
		$d_arr['collections']=$collections;
		$d_arr['collections_url'] = URL::action('CollectionsController@getIndex', array('user_id'=>$user_id));

		//echo "<pre>";print_r($d_arr);echo "</pre>";exit;

		if($load_view)
			return View::make('showUserProfileCollection', compact('d_arr'));
		else
			return $d_arr;
	}

	/**
	 * CUtil::getFavoriteShopDetailsView()
	 *
	 * @param mixed $user_id
	 * @param mixed $load_view
	 * @return
	 */
	public static function getFavoriteShopDetailsView($user_id = null, $load_view = false)
	{

		$product_service = new ProductService;
		$shop_fav_service = new ShopFavoritesService;

		if(is_null($user_id))
			return '';

		$d_arr = array();
		$user_code = BasicCutil::setUserCode($user_id);

		$fav_shop_ids = $shop_fav_service->favoriteShopIds($user_id, 3);
		$d_arr['fav_shop_ids'] = $fav_shop_ids;
		$d_arr['fav_shop_url'] = Url::to('favorite/'.$user_code.'?favorites=shop');

		if($load_view)
			return View::make('showFavoritesShop', compact('d_arr', 'product_service'));
		else
			return $d_arr;
	}

	/**
	 * CUtil::getFavoriteItemsDetailsView()
	 *
	 * @param mixed $user_id
	 * @param mixed $load_view
	 * @param mixed $shop_obj
	 * @return
	 */
	public static function getFavoriteItemsDetailsView($user_id = null, $load_view = false, $list_limit = 0, $list_prod_limit = 0)
	{
		$product_service = new ProductService;
		$prod_fav_service = new ProductFavoritesService;
		if(is_null($user_id))
			return '';
		$d_arr = $fav_items = array();
		$user_code = BasicCutil::setUserCode($user_id);

		$fav_items_list_ids = $prod_fav_service->favoriteProductListIds($user_id, $list_limit);
		//echo '<pre>';print_r($fav_items_list_ids);exit;
		if($fav_items_list_ids && !empty($fav_items_list_ids)) {
			foreach($fav_items_list_ids as $key => $list_id) {
				$fav_items[$key]['list_name'] = $prod_fav_service->getListName($list_id);
				$fav_items[$key]['list_fav_prod_ids'] = $prod_fav_service->favoriteProductIdsByList($user_id, $list_id, $list_prod_limit);
				$fav_items[$key]['list_fav_total'] = $prod_fav_service->totalFavoritesByListId($user_id, $list_id);
				$fav_items[$key]['list_items_url'] = Url::to('favorite-list/'.$user_code.'?list_id='.$list_id);
			}
		}
		else {
			//Add default list if no list found
			$fav_items[0]['list_name'] = $prod_fav_service->getListName(0);
			$fav_items[0]['list_fav_prod_ids'] = $prod_fav_service->favoriteProductIdsByList($user_id, 0, $list_prod_limit);
			$fav_items[0]['list_fav_total'] = $prod_fav_service->totalFavoritesByListId($user_id, 0);
			$fav_items[0]['list_items_url'] = Url::to('favorite-list/'.$user_code.'?list_id=0');
		}
		//echo '<pre>';print_r($fav_items);exit;
		$d_arr['fav_items'] = $fav_items;
		$d_arr['fav_items_url'] = Url::to('favorite/'.$user_code.'?favorites=product');
		if($load_view)
			return View::make('showFavoriteListItems', compact('d_arr', 'product_service'));
		else
			return $d_arr;
	}

	/**
	 * CUtil::isUserBlockedToAddProduct()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static $_is_user_blocked_to_add_product = NULL;
	public static function isUserBlockedToAddProduct($user_id = 0)
	{
		if(is_null($user_id) || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();
		if($user_id <=0)
			return false;

		if (CUtil::$_is_user_blocked_to_add_product != NULL) {
			$is_user_blocked_to_add_product = CUtil::$_is_user_blocked_to_add_product;
		} else {
			$is_allowed_to_add_product = User::where('id', $user_id)->pluck('is_allowed_to_add_product');
			$is_user_blocked_to_add_product = false;
			if($is_allowed_to_add_product == 'Blocked')
				$is_user_blocked_to_add_product = true;
			CUtil::$_is_user_blocked_to_add_product = $is_user_blocked_to_add_product;
		}
		return $is_user_blocked_to_add_product;
	}

	/**
	 * CUtil::isUserAllowedToAddProduct()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static function isUserAllowedToAddProduct($user_id = 0)
	{
		if(is_null($user_id) || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();

		if(Config::get('generalConfig.user_allow_to_add_product') || BasicCUtil::isValidToAddProduct())
			return true;
		else
			return false;
	}

	/**
	 * CUtil::isUserShopOwner()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static function isUserShopOwner($user_id = 0){
		if(is_null($user_id) || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$is_shop_owner = User::where('id', $user_id)->pluck('is_shop_owner');
		if($is_shop_owner == 'No')
			return false;
		else
			return true;
	}

	/**
	 * CUtil::fetchAllowedCurrenciesList()
	 *
	 * @return
	 */
	public static function fetchAllowedCurrenciesList()
	{
		$allowed_currencies = Products::fetchAllowedCurrenciesList();

		/*$allowed_currencies = array(Config::get('generalConfig.site_default_currency') => Config::get('generalConfig.site_default_currency'));
		if(Config::get('generalConfig.currency_is_multi_currency_support'))
		{
			$currencies = Config::get('generalConfig.allowed_currencies');
			if($currencies && is_array(Config::get('generalConfig.allowed_currencies')))
			{
				$allowed_currencies = Config::get('generalConfig.allowed_currencies');
				$allowed_currencies = array_combine($allowed_currencies, $allowed_currencies);
			}
			elseif($currencies && is_string($currencies))
			{
				$allowed_currencies = explode(',',$currencies);
				$allowed_currencies = array_map('trim',$allowed_currencies);
				$allowed_currencies = array_combine($allowed_currencies, $allowed_currencies);
			}
		}*/
		return $allowed_currencies;
	}

	/**
	 * CUtil::getCurrencyToDisplay()
	 *
	 * @return
	 */
	public static function getCurrencyToDisplay()
	{
		$currency_code = "";
		$fetched_api_currency = "";

		$currency_code = Config::get('generalConfig.site_cookie_prefix')."_selected_currency";

		if(BasicCUtil::getCookie($currency_code) != "")
		{
			//$currency_code = $cookie_api_currency;
		}
		else
		{
			$fetched_api_currency = BasicCUtil::getLocatorApiCurrencyCode();
			$currency_code = Config::get('generalConfig.site_cookie_prefix'). "_selected_currency";
			if($fetched_api_currency != "")
				$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_currency", $fetched_api_currency);
		}
		$details = array();
		$details['country'] = "United States";
		$details['currency_code'] = "USD";
		$details['exchange_rate'] = "1";
		$details['currency_symbol'] = "$";

		if(Config::get('generalConfig.currency_is_multi_currency_support'))
		{
			if(BasicCUtil::getCookie($currency_code) != "" || $fetched_api_currency != "")
			{
				$currency_details = array();
				//Check whether the currency in coookie is Active status
				if($fetched_api_currency != "")
				{
					$currency_details = Products::chkIsValidCurrency($fetched_api_currency);//CUtil::chkIsValidCurrency($fetched_api_currency);
				}
				else
				{
					$currency_details = Products::chkIsValidCurrency(BasicCUtil::getCookie($currency_code));
				}
				if(count($currency_details) > 0)
				{
					$exchange_rate = $currency_details['exchange_rate'];
					//$static_exchange_rate_curr = Config::get('payment.static_exchange_rate_currencies');

					//if(in_array($base_currency, $static_exchange_rate_curr)) {
					//	$exchange_rate = $currency_details['exchange_rate_static'];
					//}
					$currency_code = $currency_details["currency_code"];
					$currency_symbol = $currency_details["currency_symbol"];
					if($currency_code == 'INR')
						$currency_symbol = "<em class=\"clsWebRupe\">".$currency_symbol."</em>";
					$details['country'] = $currency_details["country"];
					$details['currency_code'] = $currency_code;
					$details['exchange_rate'] = $exchange_rate;
					$details['currency_symbol'] = $currency_symbol;
					//echo "<pre>";print_r($details);echo "</pre>";exit;
				}
			}
		}
		return $details;
	}

	/**
	* CUtill::getCheckDefaultCurrencyActivate()
	*
	* @prams int $user_id
	* @params mixed $field(s)
	* @return
	*/
	public static function getCheckDefaultCurrencyActivate()
	{

		if(Config::get("generalConfig.currency_is_multi_currency_support"))
		{
			$def_currency = CUtil::getCurrencyToDisplay();
			if($def_currency['currency_code'] != Config::get("generalConfig.site_default_currency"))
				return true;
			else
				return false;
		}
		return false;
	}

	/**
	* CUtill::getUserFields()
	*
	* @prams int $user_id
	* @params mixed $field(s)
	* @return
	*/
	public static function getUserFields($user_id = null, $fields = null)
	{
		if(is_null($user_id))
			$user_id = (Sentry::getUser() AND Sentry::getUser()->id)? Sentry::getUser()->id:0;
		if(!$user_id)
			return false;

		if(is_string($fields))
			$user_fields = User::whereRaw('id = ? ', array($user_id))->pluck($fields);
		elseif(is_array($fields))
			$user_fields = User::whereRaw('id = ? ', array($user_id))->get($fields);
		else
			$user_fields = User::whereRaw('id = ? ', array($user_id))->first();

		return $user_fields;

	}

	/**
	 * CUtil::getNumbersListForSelectBox()
	 *
	 * @param integer $start
	 * @param integer $limit
	 * @return
	 */
	public static function getNumbersListForSelectBox($start = 1, $limit = 10)
	{
		$numbers_arr = array();
		for ($i = $start; $i <= $limit; $i++) {
		    $numbers_arr[$i] = $i;
		}
		return $numbers_arr;
	}

	/**
	 * CUtil::getSippingTemplateName()
	 *
	 * @param mixed $template_id
	 * @return
	 */
	public static function getSippingTemplateName($template_id)
	{
		$shipping_template_name = DB::table('shipping_templates')->join('product','shipping_templates.id','=','product.shipping_template')->where('shipping_templates.id', $template_id)->pluck('template_name');
		return $shipping_template_name;
	}

	/**
	 * CUtil::getSippingCompanyName()
	 *
	 * @param mixed $company_id
	 * @return
	 */
	public static function getSippingCompanyName($company_id)
	{
		$shipping_company_name = DB::table('shipping_companies')->where('id', $company_id)->pluck('company_name');
		return $shipping_company_name;
	}

	/**
	 * CUtil::fetchAllowedCountriesList()
	 *
	 * @return
	 */
	public static function fetchAllowedCountriesList()
	{
		$allowed_countries = array();
		if(Config::get('generalConfig.allowed_countries') && is_array(Config::get('generalConfig.allowed_countries')))
		{
			$allowed_countries = Config::get('generalConfig.allowed_countries');
		}
		return $allowed_countries;
	}

	/**
	 * CUtil::getArrayFirstKey()
	 *
	 * @param mixed $data_arr
	 * @return
	 */
	public static function getArrayFirstKey($data_arr)
	{
		$first_key = '';
		if(is_array($data_arr)) {
			if(count($data_arr) > 0) {
				foreach($data_arr as $key => $val) {
					$first_key = $key;
				}
			}
		}
		return $first_key;
	}

	/**
	 * CUtil::getSiteCountry()
	 *
	 * @return
	 */
	public static function getSiteCountry()
	{
		$country = isset($_COOKIE['stock_country'])?$_COOKIE['stock_country']:'';
		//echo "dfdf".$country;
		if($country == '')
		{
			$country = key(Config::get('generalConfig.site_default_country'));
		}
		return $country;
	}

	/**
	 * CUtil::getShippingCountry()
	 *
	 * @return
	 */
	public static function getShippingCountry()
	{
		$country = BasicCUtil::getCookie(Config::get('generalConfig.site_cookie_prefix').'_shipping_country');
		if($country == '') {
			//echo "<br>Step3";
		 	$country = BasicCUtil::getLocatorApiShippingCountryId();
		 	if($country != '') {
		 		$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_country', $country);
		 	}
		}
		return $country;
	}

	/**
	 * CUtil::checkIsCustomOnlyCompany()
	 *
	 * @param mixed $company_id
	 * @return
	 */
	public static function checkIsCustomOnlyCompany($company_id)
	{
		$custom_companies = array(7, 9, 10, 11, 18, 20, 25, 27);
		if(in_array($company_id, $custom_companies)) {
			return true;
		}
		return false;
	}

	/**
	 * CUtil::isAssociativeArr()
	 *
	 * @param mixed $arr
	 * @return
	 */
	public static function isAssociativeArr($arr)
	{
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * CUtil::populateCollectionsOrderbyArray()
	 *
	 * @return
	 */
	public static function populateCollectionsOrderbyArray()
	{
		$populateOptionsArray = array();
		$list_paging_sort_by = Config::get("webshoppack.collections_list_sort_by");
		//echo "sort by<pre>";print_r($list_paging_sort_by);echo "</pre>";exit;
		$inc = 0;
		foreach ($list_paging_sort_by as $key => $filter)
		{
			$populateOptionsArray[$inc]['href'] = BasicCUtil::getCurrentUrl(true, 'orderby_field='.$filter); //Request::url().'orderby_field='.$filter;
			$inner_txt = (trans('collection.collection_listing_'.$key) != '') ? trans('collection.collection_listing_'.$key) : $key;
			$populateOptionsArray[$inc]['innervalue'] = $filter;
			$populateOptionsArray[$inc]['innertext'] = $inner_txt;
			$inc++;
		}
		return 	$populateOptionsArray;
	}

	public static function getCountryISOCode($id){

		$country_iso = CurrencyExchangeRate::where('id', $id)->pluck('iso2_country_code');
		return $country_iso;
	}

	/**
	 * CUtil::populateFavoritesHeaderArray()
	 *
	 * @return
	 */
	public static function populateFavoritesHeaderArray()
	{
		$populateOptionsArray = array();
		$list_paging_sort_by = Config::get("webshoppack.favorites_header_arr");
		//echo "sort by<pre>";print_r($list_paging_sort_by);echo "</pre>";exit;
		$inc = 0;
		foreach ($list_paging_sort_by as $key => $filter)
		{
			$populateOptionsArray[$inc]['href'] = BasicCUtil::getCurrentUrl(true, 'favorites='.$filter); //Request::url().'orderby_field='.$filter;
			$inner_txt = (trans('favorite.favorite_listing_'.$key) != '') ? trans('favorite.favorite_listing_'.$key) : $key;
			$populateOptionsArray[$inc]['innervalue'] = $filter;
			$populateOptionsArray[$inc]['innertext'] = $inner_txt;
			$inc++;
		}
		return 	$populateOptionsArray;
	}

	/**
	 * CUtil::populateCirclesHeaderArray()
	 *
	 * @param integer $followers_count
	 * @param string $user_name
	 * @return
	 */
	public static function populateCirclesHeaderArray($followers_count, $user_name ='')
	{
		$populateOptionsArray = array();
		$list_paging_sort_by = array('followers'=>'followers', 'following' => 'following');
		//echo "sort by<pre>";print_r($list_paging_sort_by);echo "</pre>";exit;
		$inc = 0;
		foreach ($list_paging_sort_by as $key => $filter)
		{
			$populateOptionsArray[$inc]['href'] = BasicCUtil::getCurrentUrl(true, 'circle_type='.$filter); //Request::url().'orderby_field='.$filter;

			if($filter == 'followers')
				if(count($followers_count) > 0 && $followers_count != 0)
					$inner_txt = (trans('myaccount/viewProfile.circle_listing_'.$key) != '') ? Lang::get('myaccount/viewProfile.circle_listing_'.$key, array('memincircle' => $followers_count)) : 'In circle';
				else
					$inner_txt = (trans('myaccount/viewProfile.not_in_circle'));
			else
				$inner_txt = (trans('myaccount/viewProfile.circle_listing_'.$key) != '' && $user_name!='') ? Lang::get('myaccount/viewProfile.circle_listing_'.$key, array('memname' => $user_name)) : 'User\'s circle';

			//$inner_txt = (trans('myaccount/viewProfile.circle_listing_'.$key) != '') ? trans('myaccount/viewProfile.circle_listing_'.$key) : $key;$inner_txt = (trans('myaccount/viewProfile.circle_listing_'.$key) != '') ? trans('myaccount/viewProfile.circle_listing_'.$key) : $key;
			$populateOptionsArray[$inc]['innervalue'] = $filter;
			$populateOptionsArray[$inc]['innertext'] = $inner_txt;
			$inc++;
		}
		return 	$populateOptionsArray;
	}

	public static function getSiteLogo($logo_type ='logo')
	{
		$image_details = array();
		$image_details["image_exists"] = false;
		//$sitelogo = SiteLogo::where('id','>','0');
		if($logo_type =='favicon')
		{
			$image_details["image_url"] = URL::asset('/images/header/favicon/favicon.ico');
			$image_path = URL::asset(Config::get("generalConfig.sitefavicon_folder"))."/";
			$cache_key = 'site_logo_favicon_cache_key';
			if (($sitelogo = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$sitelogo = SiteLogo::where('id', '>', '0')->where('logo_type', 'favicon')->first();
				HomeCUtil::cachePut($cache_key, $sitelogo);
			}
		}
		else
		{
			$image_details["image_url"] = URL::asset('images/header/logo/logo.png');
			$image_path = URL::asset(Config::get("generalConfig.sitelogo_folder"))."/";
			$cache_key = 'site_logo_cache_key';
			if (($sitelogo = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$sitelogo = SiteLogo::where('id','>','0')->where('logo_type', 'logo')->first();
				HomeCUtil::cachePut($cache_key, $sitelogo);
			}
		}
		//$sitelogo = $sitelogo->first();
		if(count($sitelogo) > 0)
		{
			//$image_path = URL::asset(Config::get("generalConfig.sitelogo_folder"))."/";
			$image_details["image_id"] = $sitelogo->id;
			$image_details["image_exists"] = true;
			$image_details["image_ext"] = $sitelogo->logo_image_ext;
			$image_details["image_name"] = $sitelogo->logo_image_name;
			$image_details["image_server_url"] = $sitelogo->logo_server_url;
			$image_details["image_width"] = $sitelogo->logo_width;
			$image_details["image_height"] = $sitelogo->logo_height;
			$image_details["image_folder"] = $image_path;
			$image_url =  $image_path . $image_details["image_name"]."_L.".$image_details["image_ext"];
			$image_details["image_url"] = $image_url;
		}
		return $image_details;
	}

	/**
	 * CUtil::getUserName()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static function getUserName($user_id)
	{
		$user_name = '';
		if($user_id > 0)
			$user_name = User::whereRaw('id = ? ', array($user_id))->pluck('user_name');
		return $user_name;
	}

	public static function getSellerCityCountry($seller_shop_details) {
		$seller_location = array();
		if(count($seller_shop_details) > 0)
		{
			$shop_country_code = $seller_shop_details['shop_country'];
			$shop_country = Products::getCountryNameByCountryCode($shop_country_code);
			$shop_city = $seller_shop_details['shop_city'];
			if($shop_city != '')
				$seller_location[] = $shop_city;
			if($shop_country != '')
				$seller_location[] = $shop_country;
		}
		return $seller_location;
	}

	public static function calculateFeedbackRate($feed_back = array()) {
		$feed_back_rate = 0;
		$feed_back_cnt = array_sum($feed_back);
		$positive_feed = $feed_back['Positive'] + $feed_back['Neutral'];
		if($positive_feed > 0) {
			$feed_back_rate = round( ($feed_back_cnt / $positive_feed ) * 100 );
		}
		return $feed_back_rate;
	}

	public static $_product_code;
	public static function getProductCodeUsingID($product_id)
	{
		$product_code = 0;
		if (isset(CUtil::$_product_code[$product_id])) {
			$product_code = CUtil::$_product_code[$product_id];
		} else {
			$product_code = Product::where('id', $product_id)->pluck('product_code');
			CUtil::$_product_code[$product_id] = $product_code;
		}
		return $product_code;
	}

	public static $_product_id;
	public static function getProductIdUsingCode($product_code)
	{
		$product_id = 0;
		if (isset(CUtil::$_product_id[$product_code])) {
			$product_id = CUtil::$_product_id[$product_code];
		} else {
			$product_id = Product::where('product_code', $product_code)->pluck('id');
			CUtil::$_product_id[$product_code] = $product_id;
		}
		return $product_id;
	}

	public static $_product_details;
	public static function getProductDetails($product_id)
	{
		if (isset(CUtil::$_product_details[$product_id])) {
			$product = CUtil::$_product_details[$product_id];
		} else {
			$product = array();
			$product_details = Product::where('id', $product_id)->first();
			$product_service = new ProductService;
			if(count($product_details) > 0) {
				$view_url = $product_service->getProductViewURL($product_id, $product_details);
				$product['details'] = $product_details;
				$product['view_url'] = $view_url;
				CUtil::$_product_details[$product_id] = $product;
			}
		}
		return $product;
	}

	public static $_order_details;
	public static function getOrderDetails($order_id)
	{
		if (isset(CUtil::$_order_details[$order_id])) {
			$order_details = CUtil::$_order_details[$order_id];
		} else {
			$order_details = ShopOrder::where('id', $order_id)->first();
			CUtil::$_order_details[$order_id] = $order_details;
		}
		return $order_details;
	}

	public static function createSlug($slug) {

	    $lettersNumbersSpacesHyphens = '/[^\-\s\pN\pL]+/u';
	    $spacesDuplicateHypens = '/[\-\s]+/';

	    $slug = preg_replace($lettersNumbersSpacesHyphens, '', $slug);
	    $slug = preg_replace($spacesDuplicateHypens, '-', $slug);

	    $slug = trim($slug, '-');

	    return mb_strtolower($slug, 'UTF-8');
	}

	public static function getStaticPageFooterLinks()
	{
		$footer_links = array();
		$static_pg_service = new StaticPageService;
		$footer_links_arr = $static_pg_service->getFooterPages('getarray');
		if(count($footer_links_arr) > 0) {
			foreach($footer_links_arr as $key => $val) {
				$pg_name = $val['page_name'];
				$pg_name_ucfirst = ucfirst($pg_name);
				$pg_name_url = URL::to('static/'.$val['url_slug']);

				$footer_links[$key]['page_name'] = $pg_name;
				$footer_links[$key]['page_name_ucfirst'] = $pg_name_ucfirst;
				$footer_links[$key]['page_link'] = $pg_name_url;
				$footer_links[$key]['page_type'] = $val['page_type'];
				$footer_links[$key]['external_link'] = ($val['external_link'] != '') ? $val['external_link'] : '#';
			}
		}
		return $footer_links;
	}

	public static function chkIsValidPaypalBusinessEmail($email)
	{
		$is_valid = false;
		$adaptive_obj = Paypaladaptive::initializePaypalProcess();
    	$initialize_data = array();
		$initialize_data['api_username']  = Config::get('payment.paypal_adaptive_api_username');
		$initialize_data['api_password']  = Config::get('payment.paypal_adaptive_api_password');
		$initialize_data['api_signature'] = Config::get('payment.paypal_adaptive_api_signature');
		$initialize_data['api_appid'] 	  = Config::get('payment.paypal_adaptive_app_id');
		$initialize_data['fees_payer']    = Config::get('payment.paypal_adaptive_fees_payer');

    	$adaptive_obj->setPaymentMode(true);
    	$adaptive_obj->initializePayment($initialize_data);
		$details = $adaptive_obj->VerifyPaypalEmail($email);
		if(isset($details['responseEnvelope.ack'])) {
			if($details['responseEnvelope.ack'] == 'Success' && $details['accountStatus'] == 'VERIFIED' && $details['userInfo.accountType'] = 'BUSINESS') {
				//echo "Valid paypal email";
				$is_valid = true;
			}
		}
		return $is_valid;
	}

	public static function chkIsAllowedModule($module)
	{
		if(\Config::has('plugin.'.$module) && \Config::get('plugin.'.$module))
		{
			if (is_dir(base_path().'/app/plugins/'.strtolower($module)))
				return true;
			return false;
		}
		return false;
	}

	public static function chkValidEmailAddress($email)
	{
		$is_ok = (preg_match("/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/i", $email));
		if (!$is_ok)
		{
			return false;
		}
		return true;
	}

	public static function showFeaturedProductIcon($p_id, $p_details = array())
	{
		//var_dump($p_details); exit;
		return HomeCUtil::showFeaturedProductIcon($p_id, $p_details);
	}

	public static function chkIsStaticPageAvailable($slug = '')
	{

		$static_pg_service = new StaticPageService;
		$page_details = $static_pg_service->getPageDetailsBySlug($slug);
		return ($page_details && count($page_details) >0);
	}

	public static function showFeaturedSellersIcon($user_id, $user_details = array())
	{
		return HomeCUtil::showFeaturedSellersIcon($user_id, $user_details);
	}

	/**
	 * read Directory
	 *
	 * read to the file specified in the path.
	 *
	 * @access	public
	 * @param	string	$dir_path to file
	 * @param	string	return (dir, file, both)
	 * @return	bool
	 */
	public static function readDirectory($dir_path, $return = 'both')
	{
		$return_arr = array();
		if(is_dir(($dir_path)))
			{
				if ($handle = opendir($dir_path))
					{
		    			while (false !== ($file = readdir($handle)))
							{
						        if ($file != "." && $file != ".." && $file != ".svn")
									{
						            	if((is_file($dir_path.$file) and $return=='file') or (is_dir($dir_path.$file) and $return=='dir') or ($return=='both'))
											{
												$return_arr[] = $file;
											}
		        					}
		    				}
		    			closedir($handle);
					}
			}
		return $return_arr;
	}

	public static function read_file($file)
	{
		//echo $file;die;
		//$file = 'E:\xampp\htdocs\buysell\app/lang/en/addressing.php';
		if ( is_dir($file) || ! file_exists($file))
			{
				return FALSE;
			}

		if (function_exists('file_get_contents'))
			{
				return file_get_contents($file);
			}

		if ( ! $fp = @fopen($file, 'rb'))
			{
				return FALSE;
			}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
			{
				$data =& fread($fp, filesize($file));
			}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}

	public static function write_file($path, $data, $mode = 'w+')
	{
		if ( ! $fp = @fopen($path, $mode))
			{
				return FALSE;
			}
		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		@chmod($path, 0777);
		flock($fp, LOCK_UN);
		fclose($fp);
		return TRUE;
	}

	public static function force_download($filename = '', $data = '')
	{
		//global $CFG;
		if ($filename == '' OR $data == '')
			{
				return FALSE;
			}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
			{
				return FALSE;
			}

		// Grab the file extension
		$x = explode('.', $filename);
		$extension = end($x);

		// Load the mime types
		//@include($CFG['site']['project_path'].'common/configs/config_mimes.inc..php');

		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
			{
				$mime = 'application/octet-stream';
			}
		else
			{
				$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
			}

		// Generate the server headers
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
			{
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header("Content-Transfer-Encoding: binary");
				header('Pragma: public');
				header("Content-Length: ".strlen($data));
			}
		else
			{
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Pragma: no-cache');
				header("Content-Length: ".strlen($data));
			}
		echo $data;
		exit;
	}

	public static function getModulesList()
	{
		$plugins_list = array();
		$plugins_path = base_path().'/app/plugins/';
		if(is_dir($plugins_path)) {
			$plugins_list = CUtil::readDirectory($plugins_path, 'dir');
		}
		return $plugins_list;
	}

	public static function getModulesTransFolderList()
	{
		$module_folder_path_arr = array();
		$plugins_list = CUtil::getModulesList();
		foreach($plugins_list as $module) {
			$module_folder_path_arr[] = 'plugins/'.$module.'/lang/%s/';
		}
		return $module_folder_path_arr;
	}

	public static function fetchAllowedLanguagesList()
	{
		$languages_arr = array();
		$languages_service = new AdminManageLanguageService();
		$languages = $languages_service->getActiveLanguagesList();
		if(count($languages) > 0) {
			foreach($languages as $key => $val) {
				if (is_dir(app_path() . '/lang/'.$val['code'])
					&& file_exists(app_path() . '/lang/'.$val['code'].'/common.php')) {
					$lang_base_path = base_path().'/public/'.Config::get("generalConfig.language_image_folder").$val['languages_id'].'.gif';
					if(file_exists($lang_base_path)) {
						$flag_src = URL::asset(Config::get("generalConfig.language_image_folder")).'/'.$val['languages_id'].'.gif';
					} else {
						$flag_src = URL::asset(Config::get("generalConfig.language_image_folder").'flag.gif');
					}
					$languages_arr[$val['code']] = $flag_src;
				}
			}
		}
		return $languages_arr;
	}

	public static function getLanguageToDisplay()
	{
		$details = array();
		$details['code'] = "en";
		$details['name'] = "English";
		$details['flag_src'] = URL::asset(Config::get("generalConfig.language_image_folder")).'/1.gif';
		$languages_service = new AdminManageLanguageService();
		if(Config::get("generalConfig.is_multi_lang_support")) {
			$lang_code = Config::get('generalConfig.site_cookie_prefix')."_selected_language";
			if(BasicCUtil::getCookie($lang_code) == "") {
				$language = Config::get('generalConfig.lang');
				$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_language", $language);
				$lang_details = $languages_service->getLanguageSettingsByCode($language);
			}
			if(BasicCUtil::getCookie($lang_code) != '') {
				$lang_details = $languages_service->getLanguageSettingsByCode(BasicCUtil::getCookie($lang_code));
			}
			$lang_base_path = base_path().'/public/'.Config::get("generalConfig.language_image_folder").$lang_details['languages_id'].'.gif';
			if(file_exists($lang_base_path)) {
				$flag_src = URL::asset(Config::get("generalConfig.language_image_folder").$lang_details['languages_id'].'.gif');
			} else {
				$flag_src = URL::asset(Config::get("generalConfig.language_image_folder").'flag.gif');
			}
			$details['code'] = $lang_details['code'];
			$details['path'] = $lang_base_path;
			$details['name'] = $lang_details['name'];
			$details['flag_src'] = $flag_src;
		}
		return $details;
	}

	public static function convertAmountToCurrency($amount, $from_currency, $to_currency, $with_currency = false, $apply_exchange_fee = false, $return_details = false)
	{
		$amount = round($amount, 2);
		if($to_currency == '') {
			$display_currency = CUtil::getCurrencyToDisplay();
			$to_currency = $display_currency['currency_code'];
		}

		if($from_currency == $to_currency)
		{
			if(!$return_details) {
				if(!$with_currency)
					return $amount;
				else
					return CUtil::getBaseAmountToDisplay($amount, $to_currency);
			}
			else
				return CUtil::getBaseAmountToDisplay($amount, $to_currency, true);
		}

		$ex_det = Products::chkIsValidCurrency($from_currency);
		if(count($ex_det) < 0) {
			if(!$return_details) {
				if(!$with_currency)
					return $amount;
				else
					return CUtil::getBaseAmountToDisplay($amount, $to_currency);
			}
			else
				return CUtil::getBaseAmountToDisplay($amount, $to_currency, true);
		}
		$arr['amount'] = 0;
		$arr['exchange_rate']  = $ex_det['exchange_rate'];
		$arr['site_service_rate'] = 0;
		//if the conversion fee has to be paid by buyer, apply the conversion rate
		if($apply_exchange_fee)
		{
			$arr['site_service_rate'] = ($arr['exchange_rate'] * (doubleval(Config::get("webshoppack.site_exchange_rate")) * 0.01));
		}
		$rate = $arr['exchange_rate'] - $arr['site_service_rate'];
		$arr['amount_usd'] = $arr['amount'] = $amount / $rate;
		if($to_currency != 'USD')
		{
			$ex_det = Products::chkIsValidCurrency($to_currency);
			if(count($ex_det) > 0)
			{
				$rate  = $ex_det['exchange_rate'];
				$arr['non_usd_exchange_rate'] = $rate;
				$arr['amount'] = $arr['amount_usd'] * $rate;
			}
			else
				$arr['amount'] = 0;
		}
		if(!$return_details) {
			if(!$with_currency)
				return $arr['amount'];
			else
				return CUtil::getBaseAmountToDisplay($arr['amount'], $to_currency);
		}
		else
			return CUtil::getBaseAmountToDisplay($arr['amount'], $to_currency, true);
	}

	public static function allowGiftwrap()
	{
		if(Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap'))
			return true;
		return false;
	}

	public static function allowVariation()
	{
		if(Config::has('plugin.variations') && Config::get('plugin.variations'))
			return true;
		return false;
	}

	public static function sendMailerSettingsErrorToAdmin($err_msg)
	{
		Log::error($err_msg);
		$admin_email = User::whereRaw('id = ?', array(1))->pluck('email');
		if(isset($admin_email) && $admin_email != '') {
			$headers = 'From: '.Config::get('mail.from_email'). "\r\n" .
					    'Reply-To: '.Config::get('mail.from_email') . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();
			$content = "Please check you mailer settings as mails not sent from your server due to following error\r\n\r\n".$err_msg;
			mail($admin_email, "Mailer Error", $content, $headers);
		}
	}

	public static function addCachedQry($key, $details, $minutes=1440)
	{
		Cache::put($key, $details, $minutes);
		//Cache::forever($key, $details);
	}

	public static function clearCachedQry($cache_key)
	{
		//Cache::flush($qry_name);
		if(Cache::has($cache_key))	// Clears the cache if exist.
		{
		   Cache::forget($cache_key);
		}
	}

	public static function getCommonMetaValues($page_name = NULL)
	{
	  $language_name = (Cookie::has(Config::get('generalConfig.site_cookie_prefix')."_selected_language"))?Cookie::get(Config::get('generalConfig.site_cookie_prefix')."_selected_language"):Config::get('generalConfig.lang');
	  $meta_array_detail = array();
	  $cache_key = 'meta_details_key_'.$language_name;
	  if (($meta_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
		   $get_meta_details = DB::table('meta_details')->where('language', $language_name)
										                ->select('page_name', 'meta_title', 'meta_description', 'meta_keyword')
										                ->orderBy('id', 'asc')
										                ->get();
		   $meta_details = array();
		   if(count($get_meta_details) > 0){
			   foreach($get_meta_details as $each_meta){
			    $meta_details[$each_meta->page_name] = $each_meta;
			   }
		   }
		   HomeCUtil::cachePut($cache_key, $meta_details);
	  }
	  if(count($meta_details) > 0){
	  	  $default_meta_title = ( isset($meta_details['default']) && $meta_details['default']->meta_title != '') ? $meta_details['default']->meta_title : '';
		  $default_meta_keyword = ( isset($meta_details['default']) && $meta_details['default']->meta_keyword != '' ) ? $meta_details['default']->meta_keyword : '';
		  $default_meta_description = ( isset($meta_details['default']) && $meta_details['default']->meta_description != '' ) ? $meta_details['default']->meta_description : '';
	      $meta_array_detail['meta_title'] = ( isset($meta_details[$page_name]) && $meta_details[$page_name]->meta_title != '') ? $meta_details[$page_name]->meta_title : $default_meta_title;
	      $meta_array_detail['meta_keyword'] = ( isset($meta_details[$page_name]) && $meta_details[$page_name]->meta_keyword != '') ? $meta_details[$page_name]->meta_keyword : $default_meta_keyword;
	      $meta_array_detail['meta_description'] = ( isset($meta_details[$page_name]) && $meta_details[$page_name]->meta_description != '') ? $meta_details[$page_name]->meta_description : $default_meta_description;
	      return $meta_array_detail;
	  }
	  return false;
	}
}
?>