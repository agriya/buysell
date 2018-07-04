<?php

use Cartalyst\Sentry\UserNotFoundException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;

class UserAccountService
{
	protected $filter_user_ids = array();
	public function setUserIds($userids = array()){
		$this->filter_user_ids  = $userids;
	}
	public function getUsersList($inputs = array(), $return_type ='paginate', $limit = 10)
	{
		$user = User::where('activated', 1)->where('is_banned', 0);
		if(isset($inputs['filter_by']) && $inputs['filter_by']!='')
		{
			if($inputs['filter_by'] =='shop_owner')
				$user->where('is_shop_owner', 'Yes');
			if($inputs['filter_by'] =='nonshop_owner')
				$user->where('is_shop_owner', 'No');
		}
		if(isset($inputs['uname']) && $inputs['uname']!='')
		{
			$uname = $inputs['uname'];
			$condition_string = '(users.first_name LIKE \'%'.addslashes($uname).'%\' OR users.last_name LIKE \'%'.addslashes($uname).'%\' OR users.user_name LIKE \'%'.addslashes($uname).'%\')';
			$user->whereRaw(DB::raw($condition_string));
		}

		if(isset($this->filter_user_ids) && $this->filter_user_ids!='' && !empty($this->filter_user_ids))
			$user->whereIn('id',$this->filter_user_ids);

		if($return_type == 'paginate')
			$users = $user->paginate($limit);
		else
			$users = $user->get();

		return $users;

	}
	public function doLogin($user, $remember, $data=array())
	{
		$error = '';
		try
		{
			Sentry::authenticate($user, $remember);
		}
		catch (Exception $e)
		{
			if($e instanceOf Cartalyst\Sentry\Users\UserNotFoundException)
				$error =  'Invalid';
			else if($e instanceOf Cartalyst\Sentry\Users\UserNotActivatedException)
				$error =  'ToActivate';
			else
				$error = $e->getMessage();
		}
		return $error;
	}

	//@added by Vasanthi_004at09
	public function getValidatorRule($field)
	{
		$rules = array(
				'first_name' => 'required|Min:'.Config::get('webshopauthenticate.fieldlength_name_min_length').
									'|Max:'.Config::get('webshopauthenticate.fieldlength_name_max_length').
									'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",

				'last_name' => 'required'.
									'|Min:'.Config::get('webshopauthenticate.fieldlength_name_min_length').
									'|Max:'.Config::get('webshopauthenticate.fieldlength_name_max_length').
									'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",

				'email' => 'Required|Email|unique:users,email',

				'user_name' => 'Required|unique:users,user_name'.'|Min:'.Config::get('webshopauthenticate.fieldlength_password_min').
							'|Max:'.Config::get('webshopauthenticate.fieldlength_password_max').
							'|regex:'."/^([-a-z0-9_ ])+$/i",

				'password' =>'Required|Min:'.Config::get('webshopauthenticate.fieldlength_password_min').
							'|Max:'.Config::get('webshopauthenticate.fieldlength_password_max').'|confirmed',
				'hash'		  =>  'required|Min:'.Config::get('webshopauthenticate.fieldlength_hash_min').
									'|Max:'.Config::get('webshopauthenticate.fieldlength_hash_max').'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",
		);
		return isset($rules[$field])? $rules[$field] : 'Required';
	}

	public function updateUserDetails($input)
	{
		try
		{
			// Find the user using the user id
			$user = Sentry::findUserById($input['user_id']);
			$user_details = User::where('id', $input['user_id'])->first();
			//print_r($user_details);exit;

			// Update the user details
			$user->email = $input['email'];
			$user->user_name = $input['user_name'];
			$user->first_name = $input['first_name'];
			$user->last_name = $input['last_name'];
			$changed_fields = array();
			if(isset($input['password']) && $input['password'] != '')
				$user->password = $input['password'];

			//Save status
			/*if(isset($input['status']) && $input['status'] != '') {
				$status = $input['status'];
				if (in_array($status, array('activate', 'deactivate'))) {
					$active_status = ($status == 'activate') ? 1 : 0;
					if($active_status != $user_details->activated) {
						$user->activated = $active_status;
					}
				}
				if (in_array($status, array('block', 'unblock'))) {
					$block_status = ($status == 'block') ? 1 : 0;
					if($block_status != $user_details->is_banned) {
						$user->is_banned = $block_status;
					}
				}
			}*/

			if ($user->save())
			{
				if(isset($input['email']) && $input['email'] != $user_details->email){
					$changed_fields['new_email'] = $input['email'];
				}
				if(isset($input['password']) && $input['password'] != ''){
					$changed_fields['password'] = $input['password'];
				}
				if((isset($input['user_name']) && $input['user_name'] != '') && ($input['user_name'] != $user_details->user_name)){
					$changed_fields['user_name'] = $input['user_name'];
				}
				// User information was updated
			}
			else
			{
				// User information was not updated
			}

			//Send status mail
			if(isset($input['status']) && $input['status'] != '') {
				$adminManageUserService = new AdminManageUserService();
				$status = $input['status'];
				if (in_array($status, array('activate', 'deactivate'))) {
					$active_status = ($status == 'activate') ? 1 : 0;
					if($active_status != $user_details->activated) {
						$adminManageUserService->updateUserActivationByAdmin($input['user_id'], $status);
					}
				}
				if (in_array($status, array('block', 'unblock'))) {
					$block_status = ($status == 'block') ? 1 : 0;
					if($block_status != $user_details->is_banned) {
						$adminManageUserService->updateUserActivationByAdmin($input['user_id'], $status);
					}
				}
			}

			if(!empty($changed_fields)){
				$changed_fields['old_email'] = $user_details->email;
				$changed_fields['first_name'] = $user_details->first_name;
				$this->sendEmailMailToUser($changed_fields);
			}

		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
			//echo 'User with this login already exists.';
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			//echo 'User was not found.';
		}


		/*$update_user_details = array('first_name' => $input['first_name'], 'last_name' => $input['last_name'], 'email' => $input['email'] );
		if(isset($input['password']) && $input['password'] != '')
		{
			//$bba_token = str_random(8);
			//$password = md5($input['password']. $bba_token);
			//$update_user_details['bba_token'] = $bba_token;
			$update_user_details['password'] = $input['password'];
		}
		User::where('id', $input['user_id'])->update($update_user_details);*/

		$group_exists = UsersGroups::whereRaw('user_id = ?', array($input['user_id']))->count('user_id');
		if($group_exists > 0) {
			UsersGroups::whereRaw('user_id = ?', array($input['user_id']))->update(array('group_id'=>$input['group_id']));
		} else {
			UsersGroups::insert(array('user_id'=>$input['user_id'], 'group_id'=>$input['group_id']));
		}
		return true;
	}

	public function addNewUser($input, $notify_user_create = false, $admin_user_create = false)
	{
		//$bba_token = str_random(8);
		//$password = md5($input['password']. $bba_token);
		$password = $input['password'];
		$is_allowed_to_add_product = 'No';//(Config::get('generalConfig.user_allow_to_add_product'))?'Yes':'No';
		$activated = 0;
		$api_key  = str_random(16);
		$user = Sentry::register(array(
				'first_name' => $input['first_name'],
				'last_name'  => $input['last_name'],
				'user_name' => $input['user_name'],
				'email'      => $input['email'],
				'password'   => $password,
				'activated'	  => $activated,
				'is_allowed_to_add_product' => $is_allowed_to_add_product,
				'subscribe_newsletter' => '1'
			));

		$group_id = '';
		/* ASSIGN USER TO PARTICULAR GROUP */
		if($group_id == '') {
			$group_id = 2;
		}

		if (isset($input['group_id'])) {
			Sentry::getUser()->addGroup( Sentry::getGroupProvider()->findById($input['group_id']) );
		} else {
			try {
				Sentry::getUser()->addGroup( Sentry::getGroupProvider()->findById($group_id) );
			}
			catch(exception $e){
				//$e->getMessage();
			}
		}

		if(!$admin_user_create)
		{
			//Update the user analytics info
			if($user->id)
			{
				$data_arr = $input;
				$data_arr['user_id'] = $user->id;
				//$this->addUserAnalyticsInfo($data_arr);
			}
			$this->sendActivationCode($user);
			$this->sendUserSignupMailToAdmin($user);
		}
		else
		{
			$this->sendUserCreatedMail($input['first_name'], $input['email'], $input['password']);
			$this->sendActivationCode($user, $admin_user_create);
		}

		/*$subscription_details  = NewsletterSubscriber::whereRaw('email = ?', array($input['email']))->first();
		if(sizeof($subscription_details) == 0)
		{
			//Subscribe in Newsletter
			$insert_arr['email'] = $input['email'];
			$insert_arr['ip'] = Request::getClientIp();
			$insert_arr['date_added'] = DB::Raw('Now()');
			$insert_arr['unsubscribe_code'] = str_random(10);
			$insert_arr['status'] = 'active';
			$insert_arr['first_name'] = $input['first_name'];
			$insert_arr['last_name'] = $input['last_name'];
			$insert_arr['user_id'] = $user->id;
			$newsletter = new NewsletterSubscriber();
			$newsletter->addNew($insert_arr);
		}
		else
		{
			$data_update = array('status' => 'active', 'unsubscribe_code' => str_random(10), 'user_id' => $user->id);
			NewsletterSubscriber::where('email', $input['email'])->update($data_update);
		}*/
		$this->subscribeUserForNewsletter($user->id, $input);
		return $user->id;
	}

	public function subscribeUserForNewsletter($user_id, $input) {
		$subscription_details  = NewsletterSubscriber::whereRaw('email = ?', array($input['email']))->first();
		if(sizeof($subscription_details) == 0)
		{
			//Subscribe in Newsletter
			$insert_arr['email'] = $input['email'];
			$insert_arr['ip'] = Request::getClientIp();
			$insert_arr['date_added'] = DB::Raw('Now()');
			$insert_arr['unsubscribe_code'] = str_random(10);
			$insert_arr['status'] = 'active';
			$insert_arr['first_name'] = $input['first_name'];
			$insert_arr['last_name'] = $input['last_name'];
			$insert_arr['user_id'] = $user_id;
			$newsletter = new NewsletterSubscriber();
			$newsletter->addNew($insert_arr);
		}
		else
		{
			$data_update = array('status' => 'active', 'unsubscribe_code' => str_random(10), 'user_id' => $user_id);
			NewsletterSubscriber::where('email', $input['email'])->update($data_update);
		}
	}

	public function updateApiKeyForUser($user_id)
	{
		$api_key_details = array();
		$api_key_count = UserApiKey::where('user_id', $user_id)->count();
		$api_key = str_random(16);
		$hash = str_random(8);
		if(!$api_key_count)
		{
			$api_key_details['user_id'] = $user_id;
			$api_key_details['api_key'] = $api_key;
			$api_key_details['hash'] = $hash;
			$api_key_details['date_added'] = date('Y-m-d H:i:s');
			$user_api_key = new UserApiKey;
			$user_api_key->addNew($api_key_details);
		}
	}

	public function addUserAnalyticsInfo($data_arr)
	{
		$data_arr['date_added'] = date('Y-m-d H:i:s');
		/* Region and countries entry starts */
		$maxmind_info = html_entity_decode($data_arr['maxmind_info']);
		$maxmind_arr = json_decode($maxmind_info,true);
		if(isset($maxmind_arr['region_name']) && $maxmind_arr['region_name']!='')
			$data_arr['region'] = $maxmind_arr['region_name'];
		if(isset($maxmind_arr['mx_countryName']) && $maxmind_arr['mx_countryName']!='')
			$data_arr['country'] = $maxmind_arr['mx_countryName'];
		if($data_arr['region'] == '' && $data_arr['country'] == '')
		{
			$geobyte_info = html_entity_decode($data_arr['geobyte_info']);
			$geocode_arr = json_decode($geobyte_info, true);
			if(isset($geocode_arr['region_name']) && isset($geocode_arr['gn_countryName']))
			{
				if($geocode_arr['region_name']!='')
					$data_arr['region'] = $geocode_arr['region_name'];
				if($geocode_arr['gn_countryName']!='')
					$data_arr['country'] = $geocode_arr['gn_countryName'];
			}
		}
		$user_geo_analytics = new UserGeoAnalytics();
		$user_geo_analytics->addNew($data_arr);
	}

	public function sendUserCreatedMail($first_name, $email, $password)
	{
		$data = array(
			'email'	=> $email,
			'password'	=> $password,
			'first_name' => $first_name
		);
		try {
			Mail::send('emails.auth.userCreated', $data, function($m) use ($data) {
					$m->to($data['email'], $data['first_name']);
					$subject = 'Welcome to '.Config::get('generalConfig.site_name');
					$m->subject($subject);
				});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function sendEmailMailToUser($changed_details)
	{
		if(isset($changed_details['password']) != '')
			$password = $changed_details['password'];
		else
			$password = '';
		if(isset($changed_details['user_name']) != '')
			$user_name = $changed_details['user_name'];
		else
			$user_name = '';
		if(isset($changed_details['new_email']) != '')
			$new_email = $changed_details['new_email'];
		else
			$new_email = '';

		$data = array(
			'email'	=> $changed_details['old_email'],
			'password'	=> $password,
			'first_name' => $changed_details['first_name'],
			'new_email' => $new_email,
			'user_name'  => $user_name,
			'subject' => str_replace('VAR_WEBSITE',Config::get('generalConfig.site_name'),trans("mail.your_account_has_been_changed")),
		);

		if(isset($changed_details['old_email'])){
			try {
				Mail::send('emails.auth.adminChangeUserEmailAndPasswordMailToUser', $data, function($m) use ($data) {
						$m->to($data['email'], $data['first_name']);
						$subject = $data['subject'];
						$m->subject($subject);
					});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
		if(isset($changed_details['new_email'])){
			try {
				Mail::send('emails.auth.adminChangeUserEmailAndPasswordMailToUser', $data, function($m) use ($data) {
						$m->to($data['new_email'], $data['first_name']);
						$subject = $data['subject'];
						$m->subject($subject);
					});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}

	public function sendActivationCode($user, $admin_user_create = false)
	{
		//If auto activate false
		if(!Config::get('webshopauthenticate.user_auto_activate') && !$admin_user_create)
		{
			Event::fire('send.activation.code', array($user));
//			$data = array('user'          => $user,
//					  'activationUrl' => URL::to('users/activation/'.$activation_code),
//					);
//			Mail::send('emails.auth.userActivation', $data, function($m) use ($user){
//				$m->to($user->email, $user->first_name);
//				$subject = Lang::get('email.userActivation');
//				$m->subject($subject);
//			});
		}
		else
		{
			$activation_code = $user->getActivationCode();
			if($admin_user_create)
				$this->activateUser($user, $activation_code, false, $admin_user_create);
			else
				$this->activateUser($user, $activation_code);
		}

	}
	public function resendActivationCode($email)
	{
		$user = User::where('email', $email)->first();
		if(isset($user['user_id']))
		{
			$this->sendActivationCode($user);
			return 'success';
		}
		return 'failed';

	}

	public function getUserForActivationCode($code)
	{
		try
		{
			$user = Sentry::getUserProvider()->findByActivationCode($code);
			return $user;
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return false;
		}

	}

	public function activateUser($user, $activationCode, $auto_login = true, $admin_user_create = false)
	{
		try
		{
			$user->attemptActivation($activationCode);
			Event::fire('send.welcome.mail', array($user));
			//$this->sendUserWelcomeMail($user, $admin_user_create);
			if($auto_login)
				$resp = Sentry::login($user, '');	// login once activated account
			return true;
		}
		catch(UserAlreadyActivatedException $e)
		{
			return false;
		}
	}

	/*
		Added periyasami_145at11
		To send welcome mail to user.
	*/
	public function sendUserWelcomeMail($user)
	{
		$mail_template = "emails.auth.welcomeMailForUser";
		// Data to be used on the email view
		$data = array('user'          => $user
					 );

		try {
			Mail::send($mail_template, $data, function($m) use ($user){
				$m->to($user->email, $user->first_name);
				$m->subject(Lang::get('email.welcomeMailForUser'));
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}

	}

	public function sendUserSignupMailToAdmin($user)
	{
		$mail_template = "emails.auth.userSignupMailToAdmin";
		// Data to be used on the email view
		$admin_email = Config::get("generalConfig.invoice_email");
		$data = array('user_details' => $user);
		try {
			Mail::send($mail_template, $data, function($m) use ($user, $admin_email){
				$m->to($admin_email);
				$m->subject(Lang::get('email.new_user_registration'));
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function isValidPasswordToken($token)
	{
		return DB::table('password_reminders')->whereRaw('token = ?', array($token))->count();
	}

	public function resetPassword($input)
	{
		//from the token get the user email and reset the password for the user id with the email
		$email = DB::table('password_reminders')->whereRaw('token = ?', array($input['token']))->pluck('email');
		if($email != '')
		{
			//generate new bba token and generate password and update the user table with email
			/*$data_arr['bba_token'] 		= $this->generateRandomCode();
			$data_arr['password'] 		= md5($input['password'].$data_arr['bba_token']);*/
			$data_arr['password'] = $input['password'];

			// Find the user using the user id
			//$user = Sentry::getUser();

			$user = User::where('email', $email)->first();

			$logged_user_id = $user->id;
    		$user = Sentry::getUserProvider()->findById($logged_user_id);
    		// Update the user details
    		/*$user->bba_token = $data_arr['bba_token'];
    		$user->password = md5($input['password'].$data_arr['bba_token']);*/
    		$user->password = $input['password'];

    // Update the user
    if ($user->save())
			DB::table('password_reminders')->whereRaw('token = ?', array($input['token']))->delete();
			return '';
		}
		else
		{
			return trans('auth/form.change_password.invalid_token');
		}
	}

	public function generateRandomCode($size = 8)
	{
		$text = microtime();
		$start = rand(0, 24);
		return substr(md5($text), $start, $size);
	}

	public function getUserinfo($user_id = 0)
	{
		$udetails = array();
		if(BasicCUtil::sentryCheck() && $user_id == Sentry::getUser()->id) {
			$udetails = Sentry::getUser();
		} else {
			$udetails = User::where('id', $user_id)->first();
		}
		return $udetails;
	}

	public function updateUserPersonalDetails($input)
	{
		$data_arr['first_name'] = $input['first_name'];
		$data_arr['last_name'] = $input['last_name'];
		$data_arr['about_me'] = $input['about_me'];
		$data_arr['subscribe_newsletter'] = (isset($input['subscribe_newsletter']) && $input['subscribe_newsletter'] != '') ? 1 : 0;
		User::where('id', $input['user_id'])->update($data_arr);
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		//$this->userImageUpload($input['user_id']);

		if($data_arr['subscribe_newsletter'] == 0)
		{
			$update_arr['status'] = 'inactive';
			$update_arr['date_unsubscribed'] = DB::Raw('Now()');
			$update_arr['unsubscribe_code'] = '';
		}
		else
		{
			$update_arr['unsubscribe_code'] = str_random(10);
			$update_arr['status'] = 'active';
		}
		NewsletterSubscriber::where('user_id', $input['user_id'])->update($update_arr);
	}


	public static function chkIsBannedIP($ip)
	{
		return DB::table('user_banned_ip')->whereRaw('banned_ip = ?', array($ip))->count();
	}



	public function chkAndCreateFolder($folderName)
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


	public function updateBasicDetails($input)
	{
		$success_message = "";
		if(isset($input['new_email']) && isset($input['email'])&&  $input['new_email'] != "" &&  $input['new_email'] != $input['email'])
		{
			// update email
			$this->changeEmail($input);
			$success_message .= Lang::get('myaccount/form.edit-profile.alternateEmail_newEmail_activation_msg');
		}
		if(isset($input['Oldpassword']) && isset($input['password']) && $input['password'] != "" && $input['Oldpassword'] != $input['password'])
		{
			//generate new bba token and generate password and update the user table with email
			/*$data_arr['bba_token'] 		= $this->generateRandomCode();
			$data_arr['password'] 		= md5($input['password'].$data_arr['bba_token']);*/
			$data_arr['password'] = $input['password'];

    		$user = Sentry::getUserProvider()->findById($input['user_id']);

    		// Update the user details
    		/*$user->bba_token = $data_arr['bba_token'];
    		$user->password = md5($input['password'].$data_arr['bba_token']);*/
    		$user->password = $input['password'];

    		// Update the user
    		$user->save();
    		$success_message .= Lang::get('auth/form.changepassword_success_message');
		}
		return $success_message;
	}


	public function changeEmail($input)
	{
		$user = User::where('id', $input['user_id'])->first();
		if (count($user))
		{
			$temp_userinfo = $user;
			$user_id = $input['user_id'];
			$activation_code = $user->getActivationCode();

			$user_data['new_email'] = $input['new_email'];
			$user_data['activation_code'] = $activation_code;

			User::where('id', $user_id)->update($user_data);
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);

			$data = array(
				'user'          => $user,
				'email'	=> $input['new_email'],
				'first_name' => $user->first_name,
				'activationUrl' => URL::to('users/newemailactivate/'.$activation_code),
			);

			try {
				Mail::send('emails.auth.newEmailActivation', $data, function($m) use ($data) {
					$m->to($data['email'], $data['first_name']);
					$subject = trans('email.newEmailActivation');
					$m->subject($subject);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}

	public function userImageUpload($user_id)
	{
		$file = Input::file('user_image');
		if($file != '')
		{
			$image_ext = $file->getClientOriginalExtension();
			$image_name = Str::random(20);
			$destinationpath = URL::asset(Config::get("generalConfig.user_image_folder"));

			$image_id = $this->uploadUserImage($file, $image_ext, $image_name, $destinationpath, $user_id);
		}
	}


	public function uploadUserImage($file, $image_ext, $image_name, $destinationpath, $user_id= 0)
	{
		$config_path = Config::get('generalConfig.user_image_folder');
		$this->chkAndCreateFolder($config_path);
		$logged_user_id = $user_id;
		if(!$logged_user_id)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
		}

		try {
			// open file a image resource
			//echo Config::get("generalConfig.user_image_folder").$image_name.'_O.'.$image_ext;exit;
			Image::make($file->getRealPath())->save(Config::get("generalConfig.user_image_folder").$image_name.'_O.'.$image_ext);

			list($width,$height)= getimagesize($file);
			list($upload_img['width'], $upload_img['height']) = getimagesize(base_path().'/public/'.$config_path.$image_name.'_O.'.$image_ext);

			$large_width = Config::get('generalConfig.user_image_large_width');
			$large_height = Config::get('generalConfig.user_image_large_height');
			if(isset($large_width) && isset($large_height))
			{
				$img_size = CUtil::DISP_IMAGE($large_width, $large_height, $upload_img['width'], $upload_img['height'], true);

				Image::make($file->getRealPath())
					->resize($large_width, $large_height, true, false)
					->save($config_path.$image_name.'_L.'.$image_ext);
			}
			$small_width = Config::get("generalConfig.user_image_small_width");
			$small_height = Config::get("generalConfig.user_image_small_height");
			if(isset($small_width) && isset($small_height))
			{
				$simg_size = CUtil::DISP_IMAGE($small_width, $small_height, $upload_img['width'], $upload_img['height'], true);
				Image::make($file->getRealPath())
					->resize($small_width, $small_height, true, false)
					->save($config_path.$image_name.'_S.'.$image_ext);
			}

			$thumb_width = Config::get("generalConfig.user_image_thumb_width");
			$thumb_height = Config::get("generalConfig.user_image_thumb_height");
			if(isset($thumb_width) && isset($thumb_height))
			{
				$timg_size = CUtil::DISP_IMAGE($thumb_width, $thumb_height, $upload_img['width'], $upload_img['height'], true);
				Image::make($file->getRealPath())
					->resize($thumb_width, $thumb_height, true, false)
					->save($config_path.$image_name.'_T.'.$image_ext);
			}

			$img_path = Request::root().'/'.$config_path;
			list($upload_input['small_width'], $upload_input['small_height']) = getimagesize($img_path.$image_name.'_S.'.$image_ext);
			list($upload_input['thumb_width'], $upload_input['thumb_height']) = getimagesize($img_path.$image_name.'_T.'.$image_ext);
			list($upload_input['large_width'], $upload_input['large_height']) = getimagesize($img_path.$image_name.'_L.'.$image_ext);

			//$user_image = new UserImage();

			$user_data = array(	'image_ext' => $image_ext,
								'image_name' => $image_name,
								//'image_server_url' => $destinationpath,
								'large_height' => $upload_input['large_height'],
	                            'large_width' => $upload_input['large_width'],
								'small_width' => $upload_input['small_width'],
	                            'small_height' => $upload_input['small_height'],
								'thumb_width' => $upload_input['thumb_width'],
	                            'thumb_height' => $upload_input['thumb_height']
								);

			//$user_image_details = UserImage::where('user_id',$logged_user_id)->first();
			$user_image_details = User::whereRaw('id = ? ', array($logged_user_id))->first();
			if(count($user_image_details) > 0)
			{
				$this->deleteImageFiles($user_image_details->image_name, $user_image_details->image_ext, $config_path);
				User::where('id', $logged_user_id)->update($user_data);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				//$id = $user_image_details->image_id;
			}//
		} catch (Exception $e) {
			Session::flash('image_upload_error', $e->getMessage());
			return false;
		}
		return true;
	}

	public function deleteImageFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_L.".$ext))
			unlink($folder_name.$filename."_L.".$ext);
		if (file_exists($folder_name.$filename."_L1.".$ext))
			unlink($folder_name.$filename."_L1.".$ext);
		if (file_exists($folder_name.$filename."_M.".$ext))
			unlink($folder_name.$filename."_M.".$ext);
		if (file_exists($folder_name.$filename."_T.".$ext))
			unlink($folder_name.$filename."_T.".$ext);
		if (file_exists($folder_name.$filename."_S.".$ext))
			unlink($folder_name.$filename."_S.".$ext);
		if (file_exists($folder_name.$filename."_O.".$ext))
			unlink($folder_name.$filename."_O.".$ext);
	}

	public function updateEmail($activation_code)
	{
		$status = 'fail';
		$user = User::where('activation_code', $activation_code)->where('new_email', '<>', '')->first();
		if(count($user) > 0)
		{
			$user_id = $user['id'];
			$temp_email = $user['new_email'];

			$CheckUser = User::where('email', $temp_email)->where('id', '<>', $user_id)->count();
			if($CheckUser > 0)
			{
				$status = 'fail';
			}
			else
			{
				$data_arr['email'] = $temp_email;
				$data_arr['new_email'] = '';
				$data_arr['activation_code'] = '';
				User::where('id', $user_id)->update($data_arr);
				$array_multi_key = array('featured_seller_banner_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$status = 'success';
			}
		}
		return $status;
	}

	public function setUserToBecomeSeller($user_id = 0){
		if(is_null($user_id) || $user_id<=0)
			return false;

		User::where('id', $user_id)->update(array('is_allowed_to_add_product' => 'Yes'));
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		return true;
	}
	public function getSellerRequestDetails($user_id = null){
		if(is_null($user_id))
			return false;

		$seller_request_details = SellerRequest::where('user_id', $user_id)->first();
		return $seller_request_details;
	}
	public function getSellerRequestDetailsById($request_id = null){
		if(is_null($request_id))
			return false;

		$seller_request_details = SellerRequest::select('seller_request.*', 'users.first_name', 'users.last_name', 'users.email', 'users.user_name')->leftJoin('users','users.id','=','seller_request.user_id')->where('seller_request.id', $request_id)->first();
		return $seller_request_details;
	}
	public function makeSellerRequest($user_id = 0, $inputs){
		if(is_null($user_id) || $user_id <=0)
			return false;
		$inputs['user_id'] = $user_id;
		$inputs['request_status'] = 'NewRequest';

		$sellerrequest = new SellerRequest();
		$request_id = $sellerrequest->addNew($inputs);
		return $request_id;
	}
	public function isSellerRequestAlreadyPosted($user_id = 0)
	{
		$is_requested = SellerRequest::where('user_id', $user_id)->count();
		if($is_requested)
			return true;
		else
			return false;
	}

	public function denyUserToBecomeSeller($user_id = 0){
		if(is_null($user_id) || $user_id<=0)
			return false;
		User::where('id', $user_id)->update(array('is_requested_for_seller' => 'No', 'is_allowed_to_add_product' => 'Blocked'));
		$array_multi_key = array('featured_seller_banner_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		return true;
	}

	public function checkEmailAlreadyExists($email)
	{
		$user_id = User::where('email', $email)->pluck('id');
		if(isset($user_id))
		{
			return true;
		}
		return false;
	}

	public function getAllSellerRequest($inputs = array())
	{
		$seller_requests= SellerRequest::select('seller_request.*', 'users.user_name', 'users.email')->leftjoin('users','users.id','=','seller_request.user_id');
		$view_type = (isset($inputs['view_type']) && $inputs['view_type']!='')?$inputs['view_type']:'new';
		if($view_type == 'new')
			$seller_requests->where('request_status', '!=', 'Allowed');

		if(isset($inputs['user_name']) && $inputs['user_name']!='')
			$seller_requests->where('users.user_name', 'like', '%'.$inputs['user_name'].'%');

		if(isset($inputs['request_status']) && $inputs['request_status']!='')
			$seller_requests->where('request_status', '=', $inputs['request_status']);

		if(isset($inputs['from_date']) && $inputs['from_date']!='')
			$seller_requests->where('seller_request.created_at', '>=', $inputs['from_date']);

		if(isset($inputs['to_date']) && $inputs['to_date']!='')
			$seller_requests->where('seller_request.created_at', '<=', $inputs['to_date']);

		$page = (isset($inputs['page']) && $inputs['page'] > 0)?$inputs['page']:1;
		Paginator::setCurrentPage($page);
		$seller_requests = $seller_requests->paginate(10);
		return $seller_requests;
	}
	public function bulkSellerRequestUpdate($request_ids = array(), $data = array()){
		try
		{
			SellerRequest::whereIn('id', $request_ids)->update($data);
			if(isset($data['request_status']) && $data['request_status'] == 'Allowed')
			{
				$this->setAllowToAddProductForRequests($request_ids);
			}
			$return = true;
		}
		catch(Exception $e)
		{
			$return = false;
		}
		return $return;
	}
	public function updateSellerRequest($request_id = 0, $data = array()){
		try
		{
			SellerRequest::where('id', $request_id)->update($data);
			if(isset($data['request_status']) && $data['request_status'] == 'Allowed')
				$this->setAllowToAddProductForRequests(array($request_id));

			if(isset($data['reply_message']) && $data['reply_message'] != 'Allowed')
			{
				$this->sendSellerRequestReplyMailToUser($request_id);
			}
			$return = true;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();exit;
			$return = false;
		}
		return $return;
	}

	public function setAllowToAddProductForRequests($request_ids = array())
	{
		if(is_array($request_ids) && !empty($request_ids))
		{
			$users = User::whereIn('id', function($query) use ($request_ids){
				$query->select('user_id')->from(with(new SellerRequest)->getTable())->whereIn('id',$request_ids);
			})->update(array('is_allowed_to_add_product' => 'Yes'));
			$array_multi_key = array('root_category_id_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			foreach($request_ids as $request_id)
			{
				$this->sendSellerRequestReplyMailToUser($request_id);
			}
		}
	}

	public function sendSellerRequestReplyMailToUser($request_id)
	{
		$request_details = $this->getSellerRequestDetailsById($request_id);
		//echo "<pre>";print_r($request_details);echo "</pre>";exit;
		if($request_details && count($request_details) > 0)
		{
			$data = array(
				'email'	=> $request_details->email,
				'first_name' => (isset($request_details->first_name) && $request_details->first_name!='')?$request_details->first_name:$request_details->user_name,
				'status_txt' => Lang::get('admin/sellerRequest.status_txt_'.$request_details->request_status),
				'reply_message' => $request_details->reply_message,
				'status' => $request_details->request_status,
				'reply_from' => str_replace('VAR_WEBSITE',Config::get('generalConfig.site_name'),trans("mail.reply_from_for_your_seller_request")),
				'your_request_to_become_a_seller' => str_replace('VAR_WEBSITE',Config::get('generalConfig.site_name'),trans("mail.your_request_to_become_a_seller")),
			);

			try {
				Mail::send('emails.auth.sellerRequestActionForUser', $data, function($m) use ($data) {
						$m->to($data['email'], $data['first_name']);
						$subject = ($data['status']!='Allowed')? $data['reply_from']:$data['your_request_to_become_a_seller'];
						$m->subject($subject);
					});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}

	}

	public function chkValidUnsubscribeCode($code)
	{
		$subscribe_count = NewsletterSubscriber::where('unsubscribe_code', $code)->count();
		if($subscribe_count > 0)
		{
			return true;
		}
		return false;
	}

	public function setAsUnsubscribed($code)
	{
		$subscribe_user_id = NewsletterSubscriber::where('unsubscribe_code', $code)->pluck('user_id');
		if($subscribe_user_id)
		{
			User::where('id', $subscribe_user_id)->update(array('subscribe_newsletter' => '0'));
			$array_multi_key = array('featured_seller_banner_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);

			$update_arr['status'] = 'inactive';
			$update_arr['date_unsubscribed'] = DB::Raw('Now()');
			$update_arr['unsubscribe_code'] = '';
			NewsletterSubscriber::where('user_id', $subscribe_user_id)->update($update_arr);
		}
	}
}