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
class ShopController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter(function(){
			if(!CUtil::isUserAllowedToAddProduct()) {
				//Session::flash('error_message', trans('common.invalid_action'));
				return Redirect::to('users/request-seller');
			}
		}, array('except' => array('')));

		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		$this->shopService = new ShopService();
		if(CUtil::chkIsAllowedModule('sudopay')) {
			$this->sudopay_service = new \SudopayService();
			$mode = (Config::get('plugin.sudopay_payment_test_mode')) ? 'test' : 'live';
			$sudopay_credential = array(
			    'api_key' => Config::get('plugin.sudopay_'.$mode.'_api_key'),
			    'merchant_id' => Config::get('plugin.sudopay_'.$mode.'_merchant_id'),
			    'website_id' => Config::get('plugin.sudopay_'.$mode.'_website_id'),
			    'secret' => Config::get('plugin.sudopay_'.$mode.'_secret_string')
			);
			$this->sa = new \SudoPay_API($sudopay_credential);
			$this->sc = new \SudoPay_Canvas($this->sa);
		}
	}

    public function getIndex()
    {
		$shop_obj = Products::initializeShops();
    	$shop_details = $shop_obj->getShopDetails($this->logged_user_id, false);
    	$shop_paypal_details = $shop_obj->getUsersShopDetails($this->logged_user_id);
		$country_arr = $this->shopService->getCountryList();
    	$shop_status = isset($shop_paypal_details['shop_status']) ? $shop_paypal_details['shop_status'] : 1;
    	$breadcrumb_arr = array(trans("shopDetails.shop_details") => '');
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$cancellation_policy = Products::initializeCancellationPolicy();
    	$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
		$shop_details['shop_country'] = (isset($shop_details['shop_country']) && $shop_details['shop_country'] != '') ? $shop_details['shop_country'] : 'IND';
		$d_arr = array();
		if(CUtil::chkIsAllowedModule('sudopay')) {
			$d_arr['sc'] = $this->sc; //sudopay canvas obj
		}
    	return View::make('shopDetails', compact('shop_details', 'cancellation_policy_details', 'shop_status', 'breadcrumb_arr', 'country_arr', 'shop_paypal_details', 'shop_obj', 'd_arr'));
	}
	public function validateShopAddress($input)
	{
	    $rules = array('shop_country' => 'required',
						'shop_address1' => 'required',
						'shop_city' => 'required',
						'shop_state' => 'required',
						'shop_zipcode' => 'required');

        return Validator::make($input, $rules);
    }

	public function postIndex()
	{
		$success_message = "";
		$input_arr = Input::All();
		if(Input::has('submit_form')) {
			$shop = Products::initializeShops();
			switch(Input::get('submit_form')) {
				case 'update_policy':
					$shop_status = 1;
					$input_arr = Input::All();
					$shop_details = $shop->getShopDetails($this->logged_user_id);
					$shop_id = '';
					if($shop_details) {
						$shop_id = $shop_details['id'];
						$shop->setShopId($shop_id);
						//$this->setShopDefaultValues($shop, $shop_details);
					}
					$shop->setShopOwnerId($this->logged_user_id);
					$shop->setShopName($input_arr['shop_name']);
					$shop->setShopUrlSlug($input_arr['url_slug']);
					$shop->setShopSlogan($input_arr['shop_slogan']);
					$shop->setShopDescription($input_arr['shop_desc']);
					$shop->setShopContactInfo($input_arr['shop_contactinfo']);
					$resp = $shop->save();
					$respd = json_decode($resp, true);

					if ($respd['status'] == 'error') {
						$error_message = '';
						if(count($respd['error_messages']) > 0) {
							foreach($respd['error_messages'] AS $err_msg) {
								$error_message .= "<p>".$err_msg."</p>";
							}
						}
						return View::make('shopPolicy', compact('error_message', 'shop_details', 'shop_status'));
					}

					if($shop_id != '') {
						$success_message = trans("shopDetails.shop_details_updated_success");
					}
					else {
						$success_message = trans("shopDetails.shopdetails_added_productadd_success");
						$product_add_url = URL::to('product/add');
						$success_message = str_replace('VAR_ADDPRODUCT_LINK', $product_add_url, $success_message);
					}
					return View::make('shopPolicy', compact('success_message', 'shop_details', 'shop_status'));

				break;
				case 'update_shop_paypal':
					$input_arr = Input::All();
					$shop->setShopOwnerId($this->logged_user_id);
					$shop->setPaypalEmailId($input_arr['paypal_id']);

					$resp = $shop->saveUsersShopDetails();
					$respd = json_decode($resp, true);
					if ($respd['status'] == 'error') {
						$error_message = '';
						if(count($respd['error_messages']) > 0) {
							foreach($respd['error_messages'] AS $err_msg) {
								$error_message .= "<p>".$err_msg."</p>";
							}
						}
						$shop_paypal_details = array();
						return View::make('shopPaypal', compact('error_message', 'shop_paypal_details'));
					}

					$shop_paypal_details = $shop->getUsersShopDetails($this->logged_user_id);
					$success_message = trans("shopDetails.shop_paypal_updated_success");
					return View::make('shopPaypal', compact('success_message', 'shop_paypal_details'));
				break;
				case 'update_address':
					$country_arr = $this->shopService->getCountryList();

					$input_arr = Input::All();
					$shop_details = $shop->getShopDetails($this->logged_user_id);
					if($shop_details) {
						$shop->setShopId($shop_details['id']);
						$shop->setShopName($shop_details['shop_name']);
						$shop->setShopUrlSlug($shop_details['url_slug']);
						//$this->setShopDefaultValues($shop, $shop_details);
					}
					$validator = $this->validateShopAddress($input_arr);
					if($validator->passes())
					{
						$shop->setShopOwnerId($this->logged_user_id);
						$shop->setShopCountry($input_arr['shop_country']);
						$shop->setShopAddress1($input_arr['shop_address1']);
						$shop->setShopAddress2($input_arr['shop_address2']);
						$shop->setShopCity($input_arr['shop_city']);
						$shop->setShopState($input_arr['shop_state']);
						$shop->setShopZipcode($input_arr['shop_zipcode']);
						$resp = $shop->save();
						$respd = json_decode($resp, true);
						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							return View::make('shopAddress', compact('error_message', 'shop_details', 'country_arr'));
						}
						$success_message = trans("shopDetails.shop_address_updated_success");
						return View::make('shopAddress', compact('success_message', 'shop_details', 'country_arr'));
					}
				break;
				case 'update_banner':
					$input_arr = Input::All();
					if (Input::hasFile('shop_banner_image'))
					{
						if($_FILES['shop_banner_image']['error'])
						{
							$shop_details = $shop->getShopDetails($this->logged_user_id);
							//$shop_details = $this->shopService->getShopDetails();
							$error_message = trans("common.uploader_max_file_size_err_msg");
							return View::make('shopBanner', compact('error_message', 'shop_details'));
						}
					}
					$rules = array('shop_banner_image' => 'Required|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_uploader_allowed_extensions")),
									//'shop_banner_image' => 'mimes:'.Config::get("shop.shop_uploader_allowed_extensions").'|size:'.Config::get("shop.shop_image_uploader_allowed_file_size")
								);
					$message = array('shop_banner_image.mimes' => trans('common.uploader_allow_format_err_msg'),
								'shop_banner_image.size' => trans('common.uploader_max_file_size_err_msg'),
							);
					$v = Validator::make(Input::all(), $rules, $message);
					if ($v->fails())
					{
						$shop_details = $shop->getShopDetails($this->logged_user_id);
						$errors = $v->errors();
						return View::make('shopBanner', compact('errors', 'shop_details'));
					}
					else
					{
						$file = Input::file('shop_banner_image');
						$file_size = $file->getClientSize();
						$allowed_size = Config::get("webshoppack.shop_image_uploader_allowed_file_size");
						$allowed_size = $allowed_size * 1024; //To convert KB to Byte
						if(($file_size > $allowed_size)  || $file_size <= 0)
						{
							$shop_details = $shop->getShopDetails($this->logged_user_id);
							$error_message = trans("common.uploader_max_file_size_err_msg");
							return View::make('shopBanner', compact('error_message', 'shop_details'));
						}

						$shop_details = $shop->getShopDetails($this->logged_user_id);
						if($shop_details) {
							$shop->setShopId($shop_details['id']);
							$shop->setShopName($shop_details['shop_name']);
							$shop->setShopUrlSlug($shop_details['url_slug']);
							//$this->setShopDefaultValues($shop, $shop_details);
						}

						$file = Input::file('shop_banner_image');
						$image_ext = $file->getClientOriginalExtension();
						$image_name = Str::random(20);
						$destinationpath = URL::asset(Config::get("webshoppack.shop_image_folder"));
						$img_arr = $this->shopService->updateBannerImage($file, $image_ext, $image_name, $destinationpath);

						$shop->setShopOwnerId($this->logged_user_id);
						$shop->setShopImageName($img_arr['image_name']);
						$shop->setShopImageExtension($img_arr['image_ext']);
						$shop->setShopImageServerUrl($img_arr['image_server_url']);
						$shop->setShopImageHeight($img_arr['t_width']);
						$shop->setShopImageWidth($img_arr['t_height']);
						$resp = $shop->save();
						$respd = json_decode($resp, true);
						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							return View::make('shopBanner', compact('error_message', 'shop_details', 'country_arr'));
						}
						$shop_details = $shop->getShopDetails($this->logged_user_id, false);
						$success_message = trans("shopDetails.shop_banner_updated_success");
						return View::make('shopBanner', compact('success_message', 'shop_details'));
					}
				break;


				case 'update_cancellation_policy':

					$cancellationPolicyService = new CancellationPolicyService();
					$cancellation_policy = Products::initializeCancellationPolicy();
					$logged_user_id = BasicCUtil::getLoggedUserId();
					$input_arr = Input::All();

					if(Input::has('id') && Input::get('id') >0)
						$cancellation_policy->setCancellationPolicyId(Input::get('id'));

					$logged_user_id = BasicCUtil::getLoggedUserId();


					$cancellation_policy->setUserId($logged_user_id);
					if (Input::hasFile('shop_cancellation_policy_file'))
					{
						if($_FILES['shop_cancellation_policy_file']['error'])
						{
							$error_message = trans("common.uploader_max_file_size_err_msg");
							$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
							return View::make('shopCancellationPolicy', compact('error_message', 'cancellation_policy_details'));
						}
					}
					$rules = array(
									'cancellation_policy_text' => 'required_without:shop_cancellation_policy_file',
									'shop_cancellation_policy_file' => 'required_without:cancellation_policy_text|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_cancellation_policy_allowed_extensions")).'|max:'.Config::get("webshoppack.shop_cancellation_policy_allowed_file_size"),
								);

					$message = array('shop_cancellation_policy_file.mimes' => trans('common.uploader_allow_format_err_msg'),
								'shop_cancellation_policy_file.max' => trans('common.uploader_max_file_size_err_msg'),
								'required_without' => trans('admin/cancellationpolicy.either_cancellation_text_or_file_required')
							);
					$v = Validator::make(Input::all(), $rules, $message);
					if ($v->fails())
					{
						$errors = $v->errors();
						$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
						return View::make('shopCancellationPolicy', compact('errors', 'cancellation_policy_details'));

					}
					else
					{

						if (Input::hasFile('shop_cancellation_policy_file'))
						{
							$file = Input::file('shop_cancellation_policy_file');
							$file_ext = $file->getClientOriginalExtension();
							$file_name = Str::random(20);
							$destinationpath = URL::asset(Config::get("webshoppack.shop_cancellation_policy_folder"));
							$img_arr = $cancellationPolicyService->updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath);

							$cancellation_policy->setCancellationPolicyFilename($img_arr['file_name']);
							$cancellation_policy->setCancellationPolicyFiletype($img_arr['file_ext']);
							$cancellation_policy->setCancellationPolicyServerUrl($img_arr['file_server_url']);


							$cancellation_policy->setCancellationPolicyText('');

						}
						elseif(Input::has('cancellation_policy_text'))
						{
							$cancellation_policy->setCancellationPolicyText(Input::get('cancellation_policy_text'));
							$cancellation_policy->setCancellationPolicyFilename('');
							$cancellation_policy->setCancellationPolicyFiletype('');
							$cancellation_policy->setCancellationPolicyServerUrl('');
							$cancellationPolicyService->deleteShopCancellationPolicyFile();
						}

						$resp = $cancellation_policy->save();
						$respd = json_decode($resp, true);
						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
							return View::make('shopCancellationPolicy', compact('error_message', 'cancellation_policy_details'));
						}
						$success_message = trans("shopDetails.shop_cancellation_policy_updated_success");
						$cancellation_policy_details = $cancellation_policy->getCancellationPolicyDetails($logged_user_id);
						return View::make('shopCancellationPolicy', compact('success_message', 'cancellation_policy_details'));
					}



					/*if (Input::hasFile('shop_cancellation_policy_file'))
					{
						if($_FILES['shop_cancellation_policy_file']['error'])
						{
							$shop_details = $shop->getShopDetails($this->logged_user_id);
							//$shop_details = $this->shopService->getShopDetails();
							$error_message = trans("common.uploader_max_file_size_err_msg");
							return View::make('shopCancellationPolicy', compact('error_message', 'shop_details'));
						}
					}
					$rules = array(
									'cancellation_policy_text' => 'required_without:shop_cancellation_policy_file',
									'shop_cancellation_policy_file' => 'required_without:cancellation_policy_text|mimes:'.Config::get("webshoppack.shop_cancellation_policy_allowed_extensions").'|max:'.Config::get("webshoppack.shop_cancellation_policy_allowed_file_size"),
								);

					$message = array('shop_cancellation_policy_file.mimes' => trans('common.uploader_allow_format_err_msg'),
								'shop_cancellation_policy_file.max' => trans('common.uploader_max_file_size_err_msg'),
								'required_without' => trans('admin/cancellationpolicy.either_cancellation_text_or_file_required')
							);
					$v = Validator::make(Input::all(), $rules, $message);
					if ($v->fails())
					{
						$shop_details = $shop->getShopDetails($this->logged_user_id);
						$errors = $v->errors();
						return View::make('shopCancellationPolicy', compact('errors', 'shop_details'));
					}
					else
					{

						$shop_details = $shop->getShopDetails($this->logged_user_id);
						if($shop_details) {
							$shop->setShopId($shop_details['id']);
							$shop->setShopName($shop_details['shop_name']);
							$shop->setShopUrlSlug($shop_details['url_slug']);
							//$this->setShopDefaultValues($shop, $shop_details);
						}
						$shop->setShopOwnerId($this->logged_user_id);
						if (Input::hasFile('shop_cancellation_policy_file'))
						{
							$file = Input::file('shop_cancellation_policy_file');
							$file_ext = $file->getClientOriginalExtension();
							$file_name = Str::random(20);
							$destinationpath = URL::asset(Config::get("webshoppack.shop_cancellation_policy_folder"));
							$img_arr = $this->shopService->updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath);

							$shop->setCancellationPolicyFilename($img_arr['file_name']);
							$shop->setCancellationPolicyFiletype($img_arr['file_ext']);
							$shop->setCancellationPolicyServerUrl($img_arr['file_server_url']);

						}
						if(Input::has('cancellation_policy_text'))
						{
							$shop->setCancellationPolicyText(Input::get('cancellation_policy_text'));
						}

						$resp = $shop->save();
						$respd = json_decode($resp, true);
						if ($respd['status'] == 'error') {
							$error_message = '';
							if(count($respd['error_messages']) > 0) {
								foreach($respd['error_messages'] AS $err_msg) {
									$error_message .= "<p>".$err_msg."</p>";
								}
							}
							return View::make('shopCancellationPolicy', compact('error_message', 'shop_details'));
						}
						$shop_details = $shop->getShopDetails($this->logged_user_id);
						$success_message = trans("shopDetails.shop_cancellation_policy_updated_success");
						return View::make('shopCancellationPolicy', compact('success_message', 'shop_details'));
					}*/
				break;
			}
		}
	}

	public function setShopDefaultValues($shop, $shop_details) {
		if($shop_details) {
			//echo 'I am here###'.$shop_details['id'].'######';
			$shop->setShopId($shop_details['id']);

			$shop->setShopOwnerId($this->logged_user_id);
			$shop->setShopName($shop_details['shop_name']);
			$shop->setShopUrlSlug($shop_details['url_slug']);
			$shop->setShopSlogan($shop_details['shop_slogan']);
			$shop->setShopDescription($shop_details['shop_desc']);
			$shop->setShopContactInfo($shop_details['shop_contactinfo']);

			//Set Address fields
			$shop->setShopCountry($shop_details['shop_country']);
			$shop->setShopAddress1($shop_details['shop_address1']);
			$shop->setShopAddress2($shop_details['shop_address2']);
			$shop->setShopCity($shop_details['shop_city']);
			$shop->setShopState($shop_details['shop_state']);
			$shop->setShopZipcode($shop_details['shop_zipcode']);

			//Set Banner fields
			$shop->setShopImageName($shop_details['image_name']);
			$shop->setShopImageExtension($shop_details['image_ext']);
			$shop->setShopImageServerUrl($shop_details['image_server_url']);
			$shop->setShopImageHeight($shop_details['t_width']);
			$shop->setShopImageWidth($shop_details['t_height']);

		}
	}

	public function getDeleteShopImage()
	{
		$this->shopService = new ShopService();

		$resource_id 	= Input::get("resource_id");
		$imagename 		= Input::get("imagename");
		$imageext 		= Input::get("imageext");
		$imagefolder 	= Input::get("imagefolder");

		if($imagename != "")
		{
			$delete_status = $this->shopService->deleteShopImage($resource_id, $imagename, $imageext, Config::get($imagefolder));
			if($delete_status)
			{
				return Response::json(array('result' => 'success'));
			}
		}
		return Response::json(array('result' => 'error'));
	}

	public function getDeleteCancellationPolicy()
	{

		$this->shopService = new CancellationPolicyService();;

		$resource_id 	= Input::get("resource_id");
		if($resource_id != "")
		{
			$delete_status = $this->shopService->deleteShopCancellationPolicyFile($resource_id, Config::get("webshoppack.shop_cancellation_policy_folder"));
			if($delete_status)
			{
				return Response::json(array('result' => 'success', 'success_message' => trans('shopDetails.cancellation_policy_removed_success')));
			}
		}
		return Response::json(array('result' => 'error', 'error_message' => trans('common.some_problem_try_later')));
	}
	public function getShopPoicyDetails()
	{
		$shop_obj = Products::initializeShops();
    	$shop_details = $shop_obj->getShopDetails($this->logged_user_id);
    	if(empty($shop_details))
    	{
			return Redirect::action('ShopController@getIndex')->with('error_message',trans('shopDetails.set_shop_at_first'));
		}
		$breadcrumb_arr = array(trans("shopDetails.shop_details") => '');
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$shop_policy_url = URL::action('ShopController@postShopPoicyDetails');
    	$action = 'add';
		$get_common_meta_values = Cutil::getCommonMetaValues('shop-policies');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
    	return View::make('shopPolicyDetails', compact('shop_details', 'breadcrumb_arr', 'shop_obj', 'shop_policy_url', 'action'));
	}
	public function postShopPoicyDetails()
	{
		if(Input::has('update_policy_details'))
		{
			$shop = Products::initializeShops();
			$shop_status = 1;
			$input_arr = Input::All();
			$shop_details = $shop->getShopDetails($this->logged_user_id);
			$shop_id = '';
			if($shop_details)
			{
				$shop->setShopId($shop_details['id']);
				$shop->setShopName($shop_details['shop_name']);
				$shop->setShopUrlSlug($shop_details['url_slug']);
			}

			$shop->setShopOwnerId($this->logged_user_id);
			$shop->setPolicyWelcome($input_arr['policy_welcome']);
			$shop->setPolicyPayment($input_arr['policy_payment']);
			$shop->setPolicyShipping($input_arr['policy_shipping']);
			$shop->setPolicyRefundExchange($input_arr['policy_refund_exchange']);
			$shop->setPolicyFaq($input_arr['policy_faq']);
			$resp = $shop->save();
			$respd = json_decode($resp, true);


			if ($respd['status'] == 'error') {
				$error_message = '';
				if(count($respd['error_messages']) > 0) {
					foreach($respd['error_messages'] AS $err_msg) {
						$error_message .= "<p>".$err_msg."</p>";
					}
				}
				return Redirect::action('ShopController@getShopPoicyDetails')->with('error_message',$error_message);
			}
			return Redirect::action('ShopController@getShopPoicyDetails')->with('success_message',trans('shopDetails.shop_policy_update_success'));
		}

	}
}
?>