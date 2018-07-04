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

class AccountController extends BaseController {

	/**
	 * To edit member profile form
	 * AccountController::index()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
        $this->manageWithdrawalService = new AdminManageWithdrawalService();

    }
	public function getIndex()
	{
		$userService = new UserAccountService();
		$udetails = $d_arr = array();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$udetails = $userService->getUserinfo($logged_user_id);
		$get_common_meta_values = Cutil::getCommonMetaValues('my-account');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		$image_details = CUtil::getUserPersonalImage($logged_user_id, 'thumb');
		return View::make('myaccount.editProfile', compact('udetails', 'request_id', 'd_arr', 'image_details'));
	}

	/**
	 * To edit member profile action
	 * AccountController::postIndex()
	 *
	 * @return
	 */
	public function postIndex()
	{
		if(BasicCUtil::checkIsDemoSite() && CUtil::isAdmin()) {
			return Redirect::back()->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
		$this->userService = new UserAccountService();
		$success_message = "";
		$user = CUtil::getAuthUser();
		$logged_user_id = $user->id;
		$input = Input::all();
		$input['user_id'] = $logged_user_id;
		$input['email'] = $user['email'];

		if(Input::has('edit_basic'))
		{
			$rules = array();
			$messages = array();
			if(Input::has('new_email') && Input::get('new_email') != $user['email'])
			{
				$rules['new_email'] = $this->userService->getValidatorRule('email');
			}
			if(Input::get('password') != "" || Input::get('password_confirmation') != "" )
			{
				$rules['Oldpassword'] = 'Required';
			}
			if(Input::has('Oldpassword') && Input::has('password') && Input::get('password') != "" && Input::get('Oldpassword') != Input::get('password'))
			{
				$rules['Oldpassword'] = $this->userService->getValidatorRule('Oldpassword');
				$messages['Oldpassword.is_valid_old_password'] = trans("myaccount/form.edit-profile.wrong_password");
				$rules['password'] = $this->userService->getValidatorRule('password');
				$rules['password_confirmation'] = 'Required|same:password';
			}

			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			else
			{
				$credential = array('email' => Sentry::getUser()->email,
								'password' => Input::get('Oldpassword')
								);
				try	{
					$user = Sentry::findUserByCredentials($credential);
					$success_message = $this->userService->updateBasicDetails($input);
				}
				catch (sentrycheck $e) {
					return Redirect::back()->withInput()->with('valid_user', Lang::get('myaccount/form.current_password') );
				}
			}
		}
		else if(Input::has('edit_personal'))
		{
			$rules = array();
			$rules['first_name'] = $this->userService->getValidatorRule('first_name');
			$rules['last_name'] = $this->userService->getValidatorRule('last_name');
			$messages = array();

			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$this->userService->updateUserPersonalDetails($input);
			$success_message = Lang::get('myaccount/form.edit-profile.personal_details_update_sucess');
		}
		else if(Input::has('edit_profile_image'))
		{ 
			$input = Input::all();
			//echo "<pre>";print_r($input);echo "</pre>";	echo "<pre>";print_r($_FILES);echo "</pre>";exit;
			if (Input::hasFile('file'))
			{
				if($_FILES['file']['error'])
				{
					$errMsg = trans("common.uploader_max_file_size_err_msg");
					return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
				$allowed_ext = Config::get("generalConfig.user_image_uploader_allowed_extensions");
				$file = Input::file('file');
				$file_size = $file->getClientSize();
				$image_ext = $file->getClientOriginalExtension();
				$allowed_size = Config::get("generalConfig.user_image_uploader_allowed_file_size");
				$allowed_size = $allowed_size * 1024; //To convert KB to Byte
				if(stripos($allowed_ext, $image_ext) === false)
				{

					$errMsg = trans("common.uploader_allow_format_err_msg");
					return Redirect::back()->withInput()->with('error_message',$errMsg);
					//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
				else if(($file_size > $allowed_size)  || $file_size <= 0)
				{
					$errMsg = trans("common.uploader_max_file_size_err_msg");
					return Redirect::back()->withInput()->with('error_message',$errMsg);
					//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
				else
				{
					$resize_image = "true";

					$image_id = "";
					$field_name = Input::get("field_name");
					$image_folder = Input::get("image_folder");
					$image_name = Str::random(20);
					$destinationpath = URL::asset(Config::get("generalConfig.user_image_folder"));
					$upload_input = array();
					$upload_input['image_ext'] = $image_ext;
					$upload_input['image_name'] = $image_name;
					$upload_input['image_server_url'] = $destinationpath;
					$result = $this->userService->uploadUserImage($file, $image_ext, $image_name, $destinationpath);
					if($result)
					{
						$success_message = Lang::get('myaccount/form.edit-profile.profile_image_update_success');
					}
					else
					{
						$errMsg = (Session::has('image_upload_error')) ? Session::get('image_upload_error') : trans("common.uploader_invalid_img_err_msg");
						return Redirect::back()->withInput()->with('error_message', $errMsg);
					}
				}
			}
		}
		return Redirect::to('users/myaccount')->with('success_message', $success_message);
	}

	/**
	 * Email activation
	 * AccountController::emailActivation()
	 *
	 * @return
	 */
	public function emailActivation($activationCode)
	{
		$status = $this->userService->updateEmail($activationCode);
		$url = Url::action('ProfileController@emailActivationResponse', $status);
		return Redirect::to($url);
	}

	/**
	 * Email activation response
	 * AccountController::emailActivationResponse()
	 *
	 * @return
	 */
	public function emailActivationResponse($status)
	{		
		$get_common_meta_values = Cutil::getCommonMetaValues('email-activation');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		
		if($status == 'fail')
		{
			$error_msg = trans("myaccount/form.email-activation.alternateEmail_invalid_activation");
			return View::make('myaccount/alternateEmailActivation', array('error_msg' => $error_msg));
		}
		elseif($status == 'success')
		{
			$success_msg = trans("myaccount/form.email-activation.alternateEmail_newEmail_update_suc_msg");
			return View::make('myaccount/alternateEmailActivation', array('success_msg' => $success_msg));
		}
	}
	public function getSellerRequest()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$this->userService = new UserAccountService();

		$is_shop_owner = CUtil::isShopOwner($logged_user_id);
		$is_already_requested = false;
		$request_posted_details = array();
		if($is_shop_owner)
			return Redirect::to('users/myaccount')->with('error_message', Lang::get('myaccount/form.seller_request.you_are_already_seller'));
		if(Config::get('generalConfig.user_allow_to_add_product'))
		{
			$this->userService->setUserToBecomeSeller($logged_user_id);
			return Redirect::to('users/shop-details')->with('success_message', Lang::get('myaccount/form.seller_request.you_are_allowed_to_seller'));
		}
		else
		{
			$is_already_requested = $this->userService->isSellerRequestAlreadyPosted($logged_user_id);
			if($is_already_requested)
			{
				$request_posted_details = $this->userService->getSellerRequestDetails($logged_user_id);
			}
		}
		return View::make('myaccount/sellerRequest',compact('is_already_requested','request_posted_details'));

	}

	public function postSellerRequest()
	{
		$inputs = Input::all();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$userAccountService = new UserAccountService();
		$rules = array('request_message' => 'required');
		if(Config::get('generalConfig.seller_request_captcha_display'))
		{
			if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
			{
				$rules['captcha'] = 'Required|Captcha';
			}
			else
			{
				$rules['adcopy_response'] = 'Required|IsValidSolveMedia:'.Input::get('adcopy_response').','.Input::get('adcopy_challenge');
			}
		}
		$validator = Validator::make($inputs,$rules);
		if($validator->passes())
		{
			$seller_request_id = $userAccountService->makeSellerRequest($logged_user_id, $inputs);
			if($seller_request_id)
				return Redirect::action('AccountController@getSellerRequest')->with('success_message', Lang::get('myaccount/form.seller_request.request_posted_successfully'));
			else
				return Redirect::action('AccountController@getSellerRequest')->withInput(Input::except(array('password', 'captcha', 'adcopy_response')))->with('error_message', Lang::get('myaccount/form.seller_request.some_problem_posting_request'));
		}
		else
		{
			return Redirect::action('AccountController@getSellerRequest')->withInput(Input::except(array('password', 'captcha', 'adcopy_response')))->withErrors($validator)->with('error_message',Lang::get('myaccount/form.seller_request.check_and_resubmit_request'));
		}
		exit;

	}
}