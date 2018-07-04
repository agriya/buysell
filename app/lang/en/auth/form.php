<?php
/**
 * Authentication files language
 *
 */

return array(

	'email'                 => 'Email',
	'email_help'            => 'Type your email address.',
	'password'              => 'Password',
	'password_help'         => 'Type your password.',
	'password_confirm'      => 'Confirm Password',
	'password_confirm_help' => 'Confirm your password.',
	'required' => 'Required',
	'changepassword_success_message' => 'Your password has been changed.',
	'signin_with_facebook' => 'Signin with facebook',
	'signin_with_twitter' => 'Signin with twitter',

	'login' => array(
		'legend'         => 'Sign in',
		'email'         => 'Email / User Name',
		'password'         => 'Password',
		'summary'        => 'Welcome Back! Please sign in below',
		'remember_me'    => 'Remember me',
		'forget_password'    => 'Forgot your password?',
		'login'    => 'Login',
		'signup'    => 'Signup',
		'submit'         => 'Sign in',
		'reset-password' => 'Reset password',
		'not_registered' => 'Not registered yet?',
		'activation_required' => 'This account is not confirmed. Your account will be activated after approval.',
		'resend_activation_code' => 'to resend activation code.',
		'account_not_confirmed' => 'This account is not confirmed. Your account will be activated after approval.',
		'account_blocked' => 'Your account has been blocked. Contact admin to unblock your account',
		'login_error' => 'Login error! Your account has been ',
		'invalid_login' => 'Invalid Email/Username or Password',
		'email_address' => 'Email Address',
		'or' => 'Or',
		'sign_in' => 'Sign in:',
		'sign_in_facebook' => 'Sign in with facebook',
		'sign_in_google' => 'Google plus',
		'sign_linked_in' => 'Sign in with LinkedIn',
		'activation_code_send' => 'Activation code has been resent to your email',
		'activate_sucess' => 'Your account has been activated successfully.',
		'invalid_activation_code' => 'Invalid activation code',
		'logged_out' => 'You are logged out',

),
	'edit-profile' => array(
		'merchant_signup_specialchars_not_allowed'	=> 'Special characters not allowed',
		'accept_only_alphanumeric_underscore' => 'Accept only lowercase alpha & numeric characters with underscore',
  ),
'forget_password' => array(
		'forgot_password'         => 'Forgot Password?',
		'password_mail_sent'      => 'An e-mail with the password reset has been sent.',
		'reset_your_password'      => 'We will send you an email with a link to reset your password',
		'enter_email_id'      	=> 'Enter your email id:',
		'password_reminder_mail_sub'      => 'Your Password Reminder',
		'recovery_password_mail_sub' => 'Account Password Recovery',
		'invalid_user' 			=> 'A user could not be found with a login email.',
		'forgot_password_instruction' => 'If you have forgotten your password, enter your account email address and we\'ll send you an email with a link that will allow you to set yourself a new password!'

),
'change_password' => array(
		'legend'         => 'Change Password',
		'new_password'         => 'New Password',
		'confirm_password'         => 'Confirm Password',
		'invalid_token'         => 'Invalid token',


),

	'register' => array(
		'legend'  => 'Sign up',
		'user_name' => 'User Name',
		'first_name'  => 'First name',
		'last_name'  => 'Last name',
		'email'  => 'Email',
		'password'  => 'Password',
		'confirm_password'  => 'Confirm Password',
		'contact_no'  => 'Contact number',
		'city_state_country'  => 'City, State Country',
		'i_agree'  => 'I agree to',
		'terms_conditions'  => Config::get('site.site_name').' terms & conditions',
		'captcha_image'  => 'Captcha',
		'security_code'  => 'Security Code',

		'summary' => 'Create your for free personal account',
		'submit'  => 'Create an account',

		'disabled' => 'Registration is disabled!',
		'validation_password_length_low' => 'Password length is too short. Minimum {0} chars required.',
		'validation_maxLength' => 'Maximum size is {0}',
		'validation_password_mismatch' => 'Password and confirm password do not match',
		'validation_phno' => 'Invalid phone number',
		'validation_password_mismatch' => 'Password and confirm password do not match',
		'restricted_keyword' => 'Input has restricted keyword: {0}',
		'signup_done' => 'You\'re almost done!',
		'signup_sent_email_1' => 'Please click the activation link in the email we sent to ',
		'signup_sent_email_2' => 'to complete your registration.',
		'signup_sent_email_3' => 'If you don\'t see our message make sure to check your spam folder.',
		'validation_api_length_low'	=> 'Hash length is too short. Minimum {0} chars required.',
		'validation_api_maxLength'	=> 'Maximum size is {0}',
		'create_account_success' => 'Account Created Successfully',
	),

	'reset-password' => array(
		'legend'                => 'Reset Password',
		'summary'               => 'An email will be sent with instructions',
		'password'              => 'New Password',
		'password_help'         => 'Type your new password.',
		'password_confirm'      => 'Confirm Password',
		'password_confirm_help' => 'Confirm your new password.',
		'submit'                => 'Change Password',
		'password_reset_success' => 'Success',
		'password_reset_success_msg' => 'New password has been updated successfully',
		'password_reset_failure'	=>	'Password reset failed'
	),

	'reset-password-confirm' => array(
		'legend'  => 'Reset Password',
		'summary' => 'Update your Password',
		'submit'  => 'Update your Password',
	),

);
