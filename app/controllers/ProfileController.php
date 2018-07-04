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
use Cartalyst\Sentry\Users\UserNotFoundException as sentrycheck;
//@added by bindu_139at10
class ProfileController extends BaseController
{
	private $shop_product_list_limit = 8;
	function __construct()
	{
		$this->beforeFilter('auth', array('except' => array('viewProfile', 'emailActivation')));
        $this->userService = new UserAccountService();
        parent::__construct();
        //$this->myaccountService = new MyAccountListService();
    }

	public function viewProfile($user_code_seo_title)
    {
		$error_msg = trans('myaccount/viewProfile.invalid_user');
		$d_arr = $breadcrumb_arr = $user_arr = array();
		$shop_obj = Products::initializeShops();
		$prod_obj = Products::initialize();

		$userCircleService = new UserCircleService();

		$user_id = CUtil::getUserId($user_code_seo_title);
		$shop_url = '';

		if($user_id != '' && ctype_digit($user_id))
		{
			$first_arr = array('id', 'created_at', 'about_me', 'is_banned', 'shop_status');
			if(CUtil::chkIsAllowedModule('featuredsellers')) {
				$first_arr = array_merge($first_arr, array('is_featured_seller', 'featured_seller_expires'));
			}
			$user_arr = User::where('id', '=', $user_id)
							->first($first_arr);
			if(count($user_arr) > 0)
			{
				$error_msg = '';
				$user_details = array();
				if($user_arr['is_banned'] == 1)
					$error_msg = trans('shop.shopowner_blocked');

				$user_details = CUtil::getUserDetails($user_id); //CUtil::getUserDetails($user_id);
				$user_image_details = CUtil::getUserPersonalImage($user_id, 'small');

				if($user_details['is_banned'] == '1')
					$error_msg = trans('shop.shopowner_blocked');

				$breadcrumb_arr[] = $user_details['display_name'];
				$title = str_replace('VAR_USER_NAME', $user_details['display_name'], trans('meta.viewprofile_title'));
				$user_arr['is_shop_owner'] = CUtil::isShopOwner($user_id, $shop_obj);
		    }
		    else
		    {
		    	//Instead of showing the invalid user error message we through 404 error page
		    	App::abort(404);
		    	$error_msg = "Invalid user";
			}
	    }
	    $d_arr['error_msg'] = $error_msg;
	    //$d_arr['shop_url'] = $shop_url;

	    $logged_user_id = BasicCUtil::getLoggedUserId();
		$get_common_meta_values = Cutil::getCommonMetaValues('view-profile');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myaccount.userProfile', compact('user_details', 'user_image_details', 'userCircleService', 'breadcrumb_arr', 'd_arr', 'user_id', 'user_arr', 'logged_user_id', 'shop_obj', 'prod_obj'));
	}

}