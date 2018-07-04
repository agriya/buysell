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
class BasicCUtil
{

	/**
	 * BasicCUtil::getLoggedUserId()
	 * added by mohamed_158at11
	 *
	 * @return boolean
	 */
	public static function checkIsDemoSite()
	{
		return false;
	}

	/**
	 * BasicCUtil::checkIsSriptAllowedInPage()
	 * added by mohamed_158at11
	 *
	 * @return boolean
	 */
	public static function checkIsSriptAllowedInPage($script='')
	{
		if(is_null($script) || $script=='')
			return true;
		switch ($script) {
			case 'star-rating':
				return Request::is('/','product/view/*', 'shop/*', 'feedback/update-feedback/*', 'feedback/add-feedback/*');
				break;
			case 'tinymce':
				return Request::is('product/add','product/add/*', 'messages/compose', 'messages/compose/*', 'shop/users/shop-policy-details', 'shop/users/shop-policy-details/*', 'deals/add-deal', 'deals/add-deal/*', 'deals/update-deal/*');
				break;
			case 'readmore':
				return Request::is('shop/*', 'deals/view-deal/*', 'product/view/*');
				break;
			case 'select2':
				return Request::is('buy', 'buy/*', 'messages/compose', 'messages/compose/*', 'product/add','product/add/*', 'cart', 'cart/*');
				break;
			case 'datepicker':
				return Request::is('transactions', 'transactions/*', 'deals/add-deal', 'deals/add-deal/*', 'deals/update-deal/*', 'coupons/update/*', 'coupons/add', 'coupons/add/*', 'product/add', 'product/add/*', 'purchases', 'purchases/*', 'deals/set-featured/*');
				break;
			default:
				return true;
				break;
		}
		return true;
	}

	public static $_sentry_check = NULL;
	public static function sentryCheck(){
		if (isset(BasicCUtil::$_sentry_check) && BasicCUtil::$_sentry_check != NULL) {
			$check = BasicCUtil::$_sentry_check;
		} else {
			$check = BasicCUtil::$_sentry_check = Sentry::check();
		}
		return $check;
	}

	/**
	 * BasicCUtil::getLoggedUserId()
	 * added by manikandan_133at10
	 *
	 * @return boolean
	 */
	public static $_logged_user_id = NULL;
	public static function getLoggedUserId()
	{
		if (isset(BasicCUtil::$_logged_user_id) && BasicCUtil::$_logged_user_id != NULL) {
			$user_id = BasicCUtil::$_logged_user_id;
		} else {
			$user_id = 0;
			if(BasicCUtil::sentryCheck()) {
				$user_id = Sentry::getUser()->id;
			}
			BasicCUtil::$_logged_user_id = $user_id;
		}
		return $user_id;
	}

	/**
	 * BasicCUtil::getUserGroupId()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static $_user_group_id;
	public static function getUserGroupId($user_id)
	{
		if (isset(BasicCUtil::$_user_group_id[$user_id])) {
			$user_group_id = BasicCUtil::$_user_group_id[$user_id];
		} else {
			$cache_key = 'GUGIDCK'.$user_id;
			if (!Cache::has($cache_key)) {
				$user_group_id = UsersGroups::whereRaw('user_id = ?', array($user_id))->pluck('group_id');
				HomeCUtil::cachePut($cache_key, $user_group_id, Config::get('generalConfig.cache_expiry_minutes'));
				BasicCUtil::$_user_group_id[$user_id] = $user_group_id;
			}else{
				$user_group_id = Cache::get($cache_key);
			}
		}
		return $user_group_id;
	}

	/**
	 * BasicCUtil::getUserGroupName()
	 *
	 * @param mixed $group_id
	 * @return
	 */
	public static $_group_name_details;
	public static function getUserGroupName($group_id)
	{
		if($group_id)
		{
			if (isset(BasicCUtil::$_group_name_details[$group_id])) {
				$group_name = BasicCUtil::$_group_name_details[$group_id];
			} else {
				$cache_key = 'UGNCK_'.$group_id;
				if (($group_name = HomeCUtil::cacheGet($cache_key)) === NULL) {
					$group_name = Groups::whereRaw('id = ?', array($group_id))->pluck('name');
					HomeCUtil::cachePut($cache_key, $group_name, Config::get('generalConfig.cache_expiry_minutes'));
					BasicCUtil::$_group_name_details[$group_id] = $group_name;
				}
			}
			return $group_name;
		}
	}

	/**
	 * BasicCUtil::setUserCode()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static function setUserCode($user_id)
	{
		$user_code = str_pad($user_id, 6, "0", STR_PAD_LEFT);
		return "U".$user_code;
	}

	/**
	 * BasicCUtil::setUserCode()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public static function getUserIDFromCode($user_code)
	{
		if (strlen($user_code) != 7) {
			$user_id = 0;
		} else {
			$user_id = str_ireplace('U', '', $user_code);
			$user_id = ltrim($user_id, '0');
			if (!is_numeric($user_id)) {
				$user_id = 0;
			}
		}
		return $user_id;
	}

	/**
	 * BasicCUtil::getCookie()
	 *
	 * @param mixed $cookie_name
	 * @return
	 */
	public static function getCookie($cookie_name)
	{
		$value = "";
		if(Cookie::has($cookie_name) && Cookie::get($cookie_name)!=null)
		{
			$value = Cookie::get($cookie_name);
		}
		return $value;
	}

	/**
	 * BasicCUtil::getCurrentUrl()
	 *
	 * @param mixed $with_query_string
	 * @param string $append_query_string
	 * @return
	 */
	public static function getCurrentUrl($with_query_string = false, $append_query_string = ''){

		$return_url = Request::url();
		$append_arr = array();
		$query_strings = array();
		$return_qry_string = false;
		if(!is_null($append_query_string) && is_string($append_query_string) || is_array($append_query_string))
		{
			$return_qry_string = true;
			if(is_string($append_query_string))
			{
				parse_str($append_query_string, $append_arr);
			}
			if(is_array($append_query_string))
			{
				$append_arr = $append_query_string;
			}
		}
		if($with_query_string)
		{
			$return_qry_string = true;
			unset($_GET['page']);
			$query_strings = $_GET;
		}
		if($return_qry_string)
		{
			$final_query_string_arr = $append_arr+$query_strings;
			$final_query_string = http_build_query($final_query_string_arr);
			if($final_query_string!='')
				$return_url = $return_url.'?'.$final_query_string;
		}
		return $return_url;
	}

	/**
	 * BasicCUtil::getLocatorApiCurrencyCode()
	 *
	 * @return
	 */
	public static function getLocatorApiCurrencyCode()
	{
		$currencyCode = (Config::get("generalConfig.site_default_currency")!='')?Config::get("generalConfig.site_default_currency"):"USD";
		$ipaddresslist = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0';

		$country_name = '';
		try{
			if(isset($_SERVER['HTTP_COOKIE']) && !in_array($ipaddresslist, array('::1', '127.0.0.1', '0')))
			{
				$cache_key = 'IP_'.$ipaddresslist;
				if ((HomeCUtil::cacheGet($cache_key)) === NULL) {
					$php_extension = get_loaded_extensions();
					if(in_array("geoip", $php_extension)) {
						$country_name = geoip_country_name_by_name($ipaddresslist);
					} else {
						if(file_exists(base_path().'/workbench/geoip/src/geoipcity.inc')) {
							include_once(base_path().'/workbench/geoip/src/geoipcity.inc');
							$gi = geoip_open(base_path()."/workbench/geoip/src/GeoLiteCity.dat",GEOIP_STANDARD);
							$details = GeoIP_record_by_addr($gi, $ipaddresslist);
							if(isset($details->country_name) && $details->country_name != "") {
								$country_name = $details->country_name;
							}
							geoip_close($gi);
						}
						if($country_name != "" && $country_name != "-")
						{
							$currencyCode = Products::getCurrencyCodeByCountry($country_name);
							HomeCUtil::cachePut($cache_key, $currencyCode, Config::get('generalConfig.cache_expiry_minutes'));
						}
					}
				}else{
					$currencyCode = HomeCUtil::cacheGet($cache_key);
				}
			}
		} catch(Exception $e){
			//Log::info("While fetching locator API currency code".$ip.", error occured. Error Msg: ".$e->getMessage());
		}
		return $currencyCode;
	}

	 /**
	  * BasicCUtil::getLocatorApiShippingCountryId()
	  *
	  * @return
	  */
	public static function getLocatorApiShippingCountryId()
	{
	 	$locatorhq_username = Config::get("webshoppack.locatorhq_api_username");
	 	$locatorhq_apikey = Config::get("webshoppack.locatorhq_api_key");
	 	$shipping_country = key(Config::get("generalConfig.site_default_shipping_country"));
		if($locatorhq_username != "" && $locatorhq_apikey != "" && isset($_SERVER['HTTP_COOKIE']))
	 	{
	 		$ipaddresslist = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';
			$cache_key = 'SIP_'.$ipaddresslist;
	 		if ($ipaddresslist && (HomeCUtil::cacheGet($cache_key)) === NULL)
	 		{
				$locator_url = "http://api.locatorhq.com/?user=".$locatorhq_username."&key=".$locatorhq_apikey."&ip=".$ipaddresslist."&format=text";
				$result = explode(',', CUtil::getContents($locator_url));
				if(sizeof($result) > 1)
				{
					$country_name = $result[1];
					if($country_name != "" && $country_name != "-")
					{
						$shipping_country = Products::getCountryIdByCountry($country_name);
						HomeCUtil::cachePut($cache_key, $shipping_country, Config::get('generalConfig.cache_expiry_minutes'));
					}
				}
			}else{
				$shipping_country = HomeCUtil::cacheGet($cache_key);
			}
		}
		return $shipping_country;
	}

	/**
	 * BasicCUtil::getShopDetails()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static function getShopDetails($user_id = 0)
	{
		$shop_arr = ShopDetails::whereRaw('user_id = ?', array($user_id))->first(array('id', 'shop_name', 'url_slug', 'shop_slogan'));
		if(count($shop_arr) > 0)
		{
			$ProductService = new ProductService;
			$shop_arr['shop_url'] = $ProductService->getProductShopURL($shop_arr['id'],$shop_arr);
		}
		return $shop_arr;
	}

	/**
	 * BasicCUtil::isValidToAddProduct()
	 *
	 * @param integer $user_id
	 * @return
	 */
	public static $_is_valid_to_add_product = NULL;
	public static function isValidToAddProduct($user_id = 0){
		if(is_null($user_id) || $user_id <=0)
			$user_id = BasicCUtil::getLoggedUserId();
		if($user_id == 0)
			return false;

		if (BasicCUtil::$_is_valid_to_add_product != NULL) {
			$is_valid_to_add_product = BasicCUtil::$_is_valid_to_add_product;
		} else {
			$user_det = User::where('id', $user_id)->select('is_allowed_to_add_product')->first();
			$is_valid_to_add_product = false;
			if(count($user_det)>0)
			{
				if($user_det->is_allowed_to_add_product == 'Yes')
					$is_valid_to_add_product = true;
			}
			BasicCUtil::$_is_valid_to_add_product = $is_valid_to_add_product;
		}
		return $is_valid_to_add_product;
	}

	/**
	 * BasicCUtil::TPL_DISP_IMAGE()
	 *
	 * @param integer $cfg_width
	 * @param integer $cfg_height
	 * @param integer $img_width
	 * @param integer $img_height
	 * @return
	 */
	public static function TPL_DISP_IMAGE($cfg_width = 0, $cfg_height = 0, $img_width = 0, $img_height = 0)
	{
		$attr = "";
		if ($cfg_width > 0 && $cfg_height > 0 && ($cfg_width < $img_width) && ($cfg_height < $img_height))
		{
			$tmpHeight = ( $cfg_width / $img_width ) * $img_height;
			if( $tmpHeight <= $cfg_height )
			{
				$attr = " width=".$cfg_width;
			}
			else
			{
				$height = $tmpHeight - ( $tmpHeight - $cfg_height );
				$attr = " height=".$height;
			}
		}
		else if ($cfg_width > 0 && $cfg_width < $img_width)
		{
			$attr = " width=".$cfg_width;
		}
		else if ($cfg_height > 0 && $cfg_height < $img_height)
		{
			$attr = " height=".$cfg_height;
		}
		else
		{
			$attr = "";
		}
		return $attr;
	}


}

?>