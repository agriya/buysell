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
class AuthController extends BaseController
{
	public function getIndex(){

		$userService = new UserAccountService();
		$productService = new ProductService();
		$userCircleService = new UserCircleService();
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";
		$users_list = $userService->getUsersList($inputs);

		if(count($users_list) > 0)
		{
			$favoriteservice = App::make('FavoriteInterface', array('favorites' => 'product'));
			foreach($users_list as $user)
			{
				$user->favorite_products = $favoriteservice->getFavoriteDetails($user->id, 2);
				$user->total_favorites = $favoriteservice->totalFavorites($user->id);
				$user->profile_image = CUtil::getUserPersonalImage($user->id, 'small');
				$user->members_in_circle = $userCircleService->numberOfMembersInCircle($user->id);
				//echo "<pre>";print_r($user);echo "</pre>";
			}
		}
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$get_common_meta_values = Cutil::getCommonMetaValues('people-list');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('usersList', compact('users_list', 'favoriteservice', 'userCircleService', 'productService', 'logged_user_id'));
	}

	public function postIndex(){
		$params = Input::all();
		$request = Request::create('users', 'GET', $params);
		return Route::dispatch($request)->getContent();
	}
	/**
	 * AdminUserController::index()
	 *
	 * @return
	 */
	public function getLogin($form_type = '', $ref_type = '', $return_thread = '')
	{
		if(isset($_GET['form_type'])) {
			$form_type = $_GET['form_type'];
		}
		if($form_type == "selLogin") {
			$reference_msg = ($ref_type != '')?'' : '';
			Session::forget('return_thread');
			if($return_thread != "") {
				Session::put('return_thread', $return_thread);
			}
			$act = "";
			$page = Input::has('page')?((Input::get('page')=='login' || Input::get('page'))?Input::get('page'):'login'):'signup';
			return View::make('users.loginPopup', compact('page'))->with('reference_msg', $reference_msg)->with('act', $act);
		}

		if (BasicCUtil::sentryCheck()) {
           	return Redirect::intended('admin');
		}
		return View::make('users.login');
	}

	/**
	 * AdminUserController::postLogin()
	 *
	 * @return
	 */
	public function postLogin()
	{
		$userService = new UserAccountService();
		$rules = array( 'email' => 'Required',
						//'user_name' => 'Required',
						'password' => 'Required' );
		if(Config::get('generalConfig.login_captcha_display'))
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
		$validator = Validator::make(Input::all(), $rules);
		if (!$validator->fails())
		{
			$email = Input::get('email');
			$user_email = User::whereRaw('user_name = ?', array(Input::get('email')))
							->where('activated', '=', 1)->pluck('email');
			//echo $count; exit;
			if ($user_email != '') {
				$email = $user_email;
			}

			$is_banned = User::where('email', '=', $email)
							->where('activated', '=', 1)->pluck('is_banned');
			if($is_banned == 1)
			{
				return Redirect::to('users/login')->with('error',Lang::get('auth/form.login.account_blocked'))->withInput(Input::except(array('password', 'captcha', 'adcopy_response')));
			}
			$user = array('email' => $email,
						  'password' => Input::get('password')    	);
			$remember = Input::get('remember');
			$error = $userService->doLogin($user, $remember);
			if ($error == '') {
				$session_username = User::where('email', '=', $email)
							->where('activated', '=', 1)->pluck('user_name');
				session_start();
				$_SESSION['loged_username'] = $session_username;
				$_SESSION['loged_email'] = $email;
        		$redirect = '';
	        	if (Sentry::getUser()->hasAnyAccess(['system'])) {
					$_SESSION['loged_by'] = 'admin';
	        		$redirect = 'admin';
				}
				else {
					$_SESSION['loged_by'] = 'seller_user';
					$redirect = 'users/myaccount';
				}
           		return Redirect::intended($redirect);
        	}

        	$error_msg = '';
        	if($error == 'Invalid'){
				$error_msg = Lang::get('auth/form.login.invalid_login');
			}
			else if($error == 'ToActivate') {
				$error_msg = Lang::get('auth/form.login.account_not_confirmed');
			}
        	Session::flash('error', $error_msg);
	        return Redirect::to('users/login')->withInput(Input::except(array('password', 'captcha', 'adcopy_response')));
        }
        else
        {
        	return Redirect::to('users/login')->withErrors($validator->messages())->withInput()->withInput(Input::except(array('password', 'captcha', 'adcopy_response')));
		}
	}

	/**
	 * LoginRegisterController::signupPopup()
	 * Added the params for the reference type and the return thread optional
	 * Popup pages for the login, forgotpassword
	 * @param mixed $form_type
	 * @param string $reference_type
	 * @param string $return_thread
	 * @return
	 */
	public function signupPopup($form_type, $ref_type = '', $return_thread = '')
	{
		$form_type = (Input::has('form_type') && Input::get('form_type') == 'selLogin') ? Input::get('form_type'):'selSignup';
		if($form_type == "selLogin")
		{
			$reference_msg = ($ref_type != '')?trans('common.login_ref_type_'.$ref_type):'';
			Session::forget('return_thread');
			if($return_thread != "")
			{
				Session::put('return_thread', $return_thread);
			}
			$page = Input::has('page')?((Input::get('page')=='login' || Input::get('page'))?Input::get('page'):'login'):'signup';
			return View::make('users.loginPopup',compact('form_type', 'page'))->with('reference_msg', $reference_msg);
		}
		elseif($form_type == "selForgotPassword")
		{
			return View::make('users.forgotPasswordPopup');
		}
	}

	/**
	 * LoginRegisterController::PostSignupPopup()
	 * Added - periyasami_145at11
	 * Function to be called for the login and fogot password [Data Post Pages]
	 * @param mixed $form_type
	 * @return
	 */
	public function PostSignupPopup($form_type)
	{
		$this->userService = new UserAccountService();
		if($form_type == "selLogin")
		{
			$page = 'login';
			$rules = array(	'login_email' => 'Required',
							//'user_name' => 'Required',
							'login_password' => 'Required');

			if(Config::get('generalConfig.login_captcha_display'))
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
			$validator = Validator::make(Input::all(), $rules);
			if (!$validator->fails())
			{
				$email = Input::get('login_email');
				$user_email = User::whereRaw('user_name = ?', array(Input::get('login_email')))
								->where('activated', '=', 1)->pluck('email');
				//echo $count; exit;
				if ($user_email != '') {
					$email = $user_email;
				}
				$user = array(	'email' => $email,
		            			//'user_name' => Input::get('email'),
		            			'password' => Input::get('login_password')   	);
		        $remember = Input::get( 'remember' );
		        $error = $this->userService->doLogin($user, $remember);
		        if ($error == '')
				{
					if(Input::has('act') && (Input::get('act') != "" && Input::get('act') == "disqus"))
					{
						return Redirect::intended('users/edit-account');
					}

		       		return View::make('users.loginPopup', compact('page'))->with('flash_notice', trans('common.successfully_logged_in'))->with('page', 'login');
		        }
		        Input::flash();
		        return View::make('users.loginPopup', compact('page'))->with('error', $error)->with('page', 'login');
		    }else{
		    	Input::flash();
	        	return View::make('users.loginPopup', compact('page'))->with('page', 'login')->withErrors($validator);
			}
	   	}
	   	elseif($form_type == "selSignup")
		{
			$page = 'signup';
			$rules = array(	'first_name' => $this->userService->getValidatorRule('first_name'),
							'last_name' => $this->userService->getValidatorRule('last_name'),
							'user_name' => $this->userService->getValidatorRule('user_name'),
							'email' => $this->userService->getValidatorRule('email'),
							'password' =>$this->userService->getValidatorRule('password'),
							'password_confirmation'=>'Required|same:password',
							'terms_conditions' =>'Required'	);

			if(Config::get('generalConfig.signup_captcha_display'))
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
			$messages = array();
			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->fails()) {
				Input::flash();
				return Redirect::back()->withInput()->with('page', 'signup')->withErrors($validator);
			}
			$this->userService->addNewUser(Input::all());
			if(config::get('webshopauthenticate.user_auto_activate'))
				return View::make('users.loginPopup', compact('page'))->with('flash_notice', trans('common.successfully_logged_in'));
			else
				return View::make('users.loginPopup', compact('page'))->with('success', 1)->with('email', Input::get('email'));
	   	}
		elseif($form_type == "selForgotPassword")
		{
			$rules = array('email' => 'required|email',	);
			$validator = Validator::make(Input::all(), $rules);
			if ($validator->fails())
			{
				return Redirect::to('users/signup-pop/selForgotPassword')->withInput()->withErrors($validator);
			}
			else
			{
				$emailExists = User::whereRaw('email = ? AND activated = ?', array(Input::get('email'), '1'))->count();
				if($emailExists == 0) {
					$error =  Lang::get('auth/form.forget_password.invalid_user');
					Session::flash('reason', $error);
					return Redirect::to('users/signup-pop/selForgotPassword')->withInput()->with('error', $error);
				}
				$user = Sentry::getUserProvider()->findByLogin(Input::get('email'));
				$token = $user->getResetPasswordCode();
				// Data to be used on the email view
				$data = array(	'user'  => $user,
								'token' => $token	);

				PasswordReminders::insert(array('email' => Input::get('email'), 'token' => $token, 'created_at' => DB::raw('NOW()')));

				try {
					// Send the activation code through email
					Mail::send('emails.auth.reminder', $data, function($m) use ($user)
					{
						$m->to($user->email, $user->first_name . ' ' . $user->last_name);
						$m->subject(Lang::get('auth/form.forget_password.recovery_password_mail_sub'));
					});
					Session::flash('success', Lang::get('auth/form.forget_password.password_mail_sent'));
				} catch (Exception $e) {
					Session::flash('error', $e->getMessage());
				}
				return View::make('users.forgotPasswordPopup');
	 		}
		}
	}

	/**
	 * AdminUserController::signUp()
	 *
	 * @return
	*/
	public function getSignup()
	{
		return View::make('users.signup');
	}

	/**
	 * AdminUserController::postSignup()
	 *
	 * @return
	*/
	public function postSignup()
	{
		$userService = new UserAccountService();
		$rules = array(	'email' => 'required|between:10,40|email|unique:users',
						'first_name' => $userService->getValidatorRule('first_name'),
						'user_name' => $userService->getValidatorRule('user_name'),
						'last_name' => $userService->getValidatorRule('last_name'),
						'password' => $userService->getValidatorRule('password'),
						'password_confirmation' =>'Required|same:password',
						'terms_conditions' =>'Required'		);

		if(Config::get('generalConfig.signup_captcha_display'))
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
		$validator = Validator::make(Input::all(), $rules);
		if($validator->fails()) {
			return Redirect::to('users/signup')
								->with('errors', $validator->messages())
                             	->withInput(Input::except(array('password', 'captcha', 'adcopy_response')));
	    } else {
	    	$user_id = $userService->addNewUser(Input::all());
	    	if($user_id) {
				$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
				if($group_exists == 0) {
					UsersGroups::insert(array('user_id' => $user_id, 'group_id' => 0));
				}
			}
	    	if(Config::get('webshopauthenticate.user_auto_activate')) {
				return Redirect::to('users/login')->with('success_message', Lang::get('auth/form.register.create_account_success'));
			}
			else {
				return View::make('users.signup')->with('success', 1)->with('email', Input::get('email'));
			}
	  	}
	}

	/**
	 * AdminUserController::getActivate()
	 *
	 * @return
	*/
	public function getActivate($activationCode)
	{
		$userService = new UserAccountService();
		$user = $userService->getUserForActivationCode($activationCode);
		if($user AND $userService->activateUser($user, $activationCode))
		{
			return Redirect::to('users/myaccount')->with('success', Lang::get('auth/form.login.activate_sucess'));
	    }
	    else
	    {
	       return Redirect::to('users/login')->with('error', Lang::get('auth/form.login.invalid_activation_code'));
	    }
	}

	/**
	 * AdminUserController::forgotPassword()
	 *
	 * @return
	*/
	public function getForgotpassword()
	{
		return View::make('users/forgotPassword');
	}

	/**
	 * AdminUserController::postForgotpassword()
	 *
	 * @return
	*/
	public function postForgotpassword()
	{
		$rules = array('email' => 'required|email',	);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('users/forgotpassword')->withInput()->withErrors($validator);
		}
		else
		{
			$emailExists = User::whereRaw('email = ? AND activated = ?', array(Input::get('email'), '1'))->count();
			if($emailExists == 0) {
				$error =  Lang::get('auth/form.forget_password.invalid_user');
				return Redirect::to('users/forgotpassword')->withInput()->with('error', $error);
			}
			$user = Sentry::getUserProvider()->findByLogin(Input::get('email'));
			$token = $user->getResetPasswordCode();
			// Data to be used on the email view
			$data = array(	'user'  => $user,
							'token' => $token );

			PasswordReminders::insert(array('email' => Input::get('email'), 'token' => $token, 'created_at' => DB::raw('NOW()')));
			Event::fire('send.forgetpassword.mail', array($data));

			// Send the activation code through email
			//Mail::send('emails.auth.reminder', $data, function($m) use ($user)
			//{
			//	$m->to($user->email, $user->first_name . ' ' . $user->last_name);
			//	$m->subject(Lang::get('auth/form.forget_password.recovery_password_mail_sub'));
			//});
			Session::flash('success', Lang::get('auth/form.forget_password.password_mail_sent'));
			return Redirect::to('users/forgotpassword');
		}
	}

	/**
	 * AdminUserController::getResetPassword()
	 *
	 * @return
	*/
	public function getResetPassword($token)
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$is_valid = $userService->isValidPasswordToken($token);
		if($is_valid)
		{
			return Redirect::to('users/change-password/'.$token);
		}
		else
		{
			return Redirect::to('users/change-password/'.$token)->with('error', Lang::get('auth/form.change_password.invalid_token'));
		}
	}

	/**
	 * AdminUserController::getChangePassword()
	 *
	 * @return
	*/
	public function getChangePassword($token)
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$is_valid = $userService->isValidPasswordToken($token);
		if($is_valid)
		{
			return View::make('users/changePassword')->with('token', $token);
		}
		else
		{
			return View::make('users/changePassword')->with('token', $token)->with('error', Lang::get('auth/form.change_password.invalid_token'));
		}
	}

	public function postChangePassword()
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$rules = array('password' => $userService->getValidatorRule('password').'|Confirmed');
		$token = Input::get('token');
		$v = Validator::make(Input::all(), $rules);
		if($v->passes())
		{
			$ret_msg = $userService->resetPassword(Input::all());
			if($ret_msg == '')
			{
				return Redirect::to('users/login')->with('success_message', Lang::get('auth/form.change_password.password_success_message'));
			}
			else
			{
				return Redirect::to('users/change-password/'.$token)->withInput()->with('change_password_error', $ret_msg);
			}
		}
		else
		{
			return Redirect::to('users/change-password/'.$token)->withInput()->withErrors($v);
		}
	}

	/**
	 * AdminUserController::getLogout()
	 *
	 * @return
	*/
	public function getLogout()
    {
    	if (Sentry::check()) {
	        Sentry::logout();
	    }
        return Redirect::to('users/login');
    }

    public function getExternalSignup()
	{
		$attributes = Session::get('attributes');

		if(!empty($attributes)) {
			Session::put('external_attributes', $attributes);
		}
		$attributes = Session::get('external_attributes');
		//echo "External atttributes<pre>";print_r($attributes);echo "</pre>";exit;
		if(!empty($attributes)) {
			if($attributes['entry_type'] == 'map')
				return Redirect::to('users/external-signup-map')->with('attributes', $attributes);

			return View::make('users/externalSignup', compact('attributes'));
		}
		Redirect::to(url('/'));
	}

	public function postExternalSignup()
	{
		$userService = new UserAccountService();
		$attributes = Session::get('external_attributes');
		$serviceName = $attributes['service_name'];
		$rules['email'] = $userService->getValidatorRule('email');
		$rules['first_name'] = $userService->getValidatorRule('first_name');
		$rules['user_name'] = $userService->getValidatorRule('user_name');
		$rules['last_name'] = $userService->getValidatorRule('last_name');

	/*	$validator = Validator::make(Input::all(), $rules);
		if($validator->fails()) {
				return Redirect::back()->withInput()->withErrors($validator);

	    } else {
	    	$user_id = $userService->addNewUser(Input::all());
	    	if($user_id) {
				$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
				if($group_exists == 0) {
					UsersGroups::insert(array('user_id' => $user_id, 'group_id' => 0));
				}
			}
	    	if(Config::get('webshopauthenticate.user_auto_activate')) {
				return Redirect::to('users/login')->with('success_message', 'account_created');
			}
			else {
				return View::make('users.signup')->with('success', 1)->with('email', Input::get('email'));
			}
	  	}*/

		$messages = array();
		$validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		} else {
			$attributes = Session::get('external_attributes');
			$serviceName = $attributes['service_name'];
			$access = $attributes['access'];
			$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));

			// Handle first name empty checking.
			$inputs = Input::all();
			if(!isset($inputs["first_name"]))
			{
				$first_name = '';
				if(isset($inputs["email"]) && $inputs["email"] != '')
				{
					$parts = explode('@', $inputs["email"]);
					$first_name = isset($parts[0]) ? $parts[0] : '';
				}
				$inputs['first_name'] = $first_name;
			}
			$inputs['last_name'] = isset($inputs['last_name']) ? $inputs['last_name'] : '';

			if($user_id = SentrySocial::addNewSentryUser($service, $access, $inputs)) {
				$is_allowed_to_add_product = 'No';//(Config::get('generalConfig.user_allow_to_add_product'))?'Yes':'No';
				User::where('id', $user_id)->update(array(//'user_code' => CUtil::generateRandomUniqueCode('', 'users', 'user_code'),
																'last_login' => date('Y-m-d H:i:s'),
																//'signup_ip'	  => $_SERVER['REMOTE_ADDR'],
																'is_allowed_to_add_product' => $is_allowed_to_add_product
																//'openid_used'	  => 'Yes'
																));
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);

				// To assign user to particular group
				try {
					Sentry::getUser()->addGroup( Sentry::getGroupProvider()->findById(2) );
				}
				catch(exception $e){
					//$e->getMessage();
				}

				/*$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
				if($group_exists <= 0) {
					UsersGroups::insert(array('user_id'=>$input['user_id'], 'group_id'=>2));
				} else {

				}*/
				$userService->subscribeUserForNewsletter($user_id, $inputs);
				Session::forget('external_attributes');
				Session::flash('success', trans('auth/form.register.create_account_success'));
				return Redirect::to('users/myaccount');
			}
			else
			{
				return Redirect::back()->withInput()->with('error_message', trans('common.some_problem_try_later'));
			}
		}
	}

	public function getExternalSignupMap()
	{
		$attributes = Session::get('external_attributes');
		$serviceName = $attributes['service_name'];
		$access = $attributes['access'];
		$service = SentrySocial::make($serviceName, URL::to("oauth/callback/{$serviceName}"));
		// Handle first name empty checking.
		$inputs = Input::all();
		if(!isset($inputs["first_name"]))
		{
			$first_name = '';
			if(isset($inputs["email"]) && $inputs["email"] != '')
			{
				$parts = explode('@', $inputs["email"]);
				$first_name = isset($parts[0]) ? $parts[0] : '';
			}
			$inputs['first_name'] = $first_name;
		}
		$inputs['last_name'] = isset($inputs['last_name']) ? $inputs['last_name'] : '';

		if($user_id = SentrySocial::addNewSentryUser($service, $access, $inputs)) {
			User::where('id', $user_id)->update(array('last_login' => date('Y-m-d H:i:s'),
															//'openid_used'	  => 'Yes'
															));
			// To assign user to particular group
			//Sentry::getUser()->addGroup( Sentry::getGroupProvider()->findByName('Default') );
			Session::flash('success', trans('auth/form.register.map_account_success'));
			return Redirect::to('users/myaccount');
		}
	}
}
?>