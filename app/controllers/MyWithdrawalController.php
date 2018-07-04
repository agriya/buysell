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
class MyWithdrawalController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		//$this->beforeFilter('auth', array());
        $this->myaccount_listing_service = new MyAccountListingService();
        //$this->sitetransactionservice = new SiteTransactionHandlerService();
    }

    public function getIndex()
	{
		$credit_obj = Credits::initialize();
		$withdraw_obj = Credits::initializeWithDrawal();
		$request_details = $request_details_arr = array();
		$user_acc_bal_details = $this->myaccount_listing_service->fetchuserAccountBalance($credit_obj);

		$d_arr['allow_withdrawal'] = 1;
		//store initially a 0 balance in USD AND INR
		$main_currency_arr['USD'] = array('currency_symbol' => '$', 'amount' => 0.0);
		$main_currency_arr['INR'] = array('currency_symbol' => 'Rs', 'amount' => 0.0);

		$d_arr['main_currency_arr'] = $main_currency_arr;
		$allowed_wtihdrawal_details = $this->myaccount_listing_service->fetchAllowedWithdrawals();
		$d_arr['allowed_withdrawals_arr'] = $allowed_wtihdrawal_details;
		if(count($user_acc_bal_details) > 0)
		{
			$d_arr['allow_withdrawal'] = 1;
			$payments_arr = $other_payments_arr = array();
			foreach($user_acc_bal_details as $acc_bal)
			{
				if(round($acc_bal['amount']) > 0)
				{
					if($acc_bal['currency'] == "USD" || $acc_bal['currency'] == "INR")
					{
						$payments_arr['main'][] = $acc_bal;
						$main_currency_arr[$acc_bal['currency']] = array('currency_symbol' => $acc_bal['currency_symbol'], 'amount' => $acc_bal['amount']);
					}
					else
						$payments_arr['other'][] = $acc_bal;
				}
			}
			$d_arr['main_currency_arr'] = $main_currency_arr;
			$d_arr['user_acc_bal_details'] = $payments_arr;
			$withdraw_currency_arr = array();
			foreach($user_acc_bal_details as $ac_bal)
			{
				if($ac_bal['amount'] > 0)
					$withdraw_currency_arr[$ac_bal['currency']] = $ac_bal['currency'];
			}
			$d_arr['withdraw_currency_arr'] = $withdraw_currency_arr;
		}
		$request_details = $this->myaccount_listing_service->fetchUserWithdrawalsRequestList($withdraw_obj);
		if(count($request_details) > 0)
		{
			foreach($request_details AS $reqKey => $req)
			{
				$request_details_arr[$reqKey]['id']= $req['withdraw_id'];
				$request_details_arr[$reqKey]['date_added']= $req['added_date'];
				$request_details_arr[$reqKey]['user_id']= $req['user_id'];
				$request_details_arr[$reqKey]['amount']= $req['amount'];
				$request_details_arr[$reqKey]['currency']= $req['currency'];
				$request_details_arr[$reqKey]['payment_type']= $req['payment_type'];
				$request_details_arr[$reqKey]['fee']= (round($req['fee'], 2) > 0 ) ? $req['fee'] : "-";
				$request_details_arr[$reqKey]['pay_to_user_account']= $req['pay_to_user_account'];
				$request_details_arr[$reqKey]['paid_notes']= $req['paid_notes'];
				$request_details_arr[$reqKey]['admin_notes']= $req['admin_notes'];
				$request_details_arr[$reqKey]['set_as_paid_by']= $req['set_as_paid_by'];
				$request_details_arr[$reqKey]['site_transaction_id']= $req['site_transaction_id'];
				$request_details_arr[$reqKey]['status']= $req['status'];
				$request_details_arr[$reqKey]['date_paid']= $req['date_paid'];
				$request_details_arr[$reqKey]['date_cancelled']= $req['date_cancelled'];
				$request_details_arr[$reqKey]['cancelled_reason']= $req['cancelled_reason'];
				$request_details_arr[$reqKey]['cancelled_by']= $req['cancelled_by'];
				$net_amount= $req['amount'] - $req['fee'];
				$formatted_amt = number_format ($net_amount, 2, '.','');
				$request_details_arr[$reqKey]['net_amount']= $formatted_amt;
			}
		}

		//Set Meta details
		$get_common_meta_values = Cutil::getCommonMetaValues('my-withdrawals');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myaccount/myWithdrawals', compact('request_details', 'd_arr', 'request_details_arr'));
	}

	public function postAddRequest()
	{
		$rules = array();
		// validation - allowed only one request for an currency in pending. if already request in "Active" then will not allow to post another request in that currency
		// amount should be lower than available cleared amount. pending amount will not caluclated
		$withdraw_amount = Input::get('withdraw_amount');
		$withdraw_currency = Input::get('withdraw_currency');
		$rules['withdraw_amount'] = 'required|IsValidPrice|AllowedWithdrawalCurrency:'.$withdraw_currency.'|AllowedWithdrawalAmount:'.$withdraw_amount.",".$withdraw_currency;
		$rules['pay_to_details'] = 'required';
		$messages = array(
		    'withdraw_amount.allowed_withdrawal_amount' => trans('myaccount/form.my-withdrawals.withdraw_amount_err_msg'),
		    'withdraw_amount.allowed_withdrawal_currency' => trans('myaccount/form.my-withdrawals.withdraw_request_exist_err_msg'),
		    'withdraw_amount.is_valid_price' => trans('myaccount/form.my-withdrawals.withdraw_request_invalid_amount'),
		);

		$validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->fails())
		{
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$this->myaccountService->addWithdrawalRequest(Input::all());
		return Redirect::to('users/payment/my-withdrawals')->with('success_message', trans('myaccount/form.my-withdrawals.withdraw_request_success'));
	}


	public function getCancelRequest($request_id = '')
	{
		if(!BasicCUtil::sentryCheck())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		if($request_id != '')
		{
			$withdraw_obj = Credits::initializeWithDrawal();
			$request_details = $this->myaccount_listing_service->chkIsValidCancelRequest($request_id, $withdraw_obj);
			if(count($request_details) > 0)
			{
				$show_form = true;
				return View::make('myaccount/manageWithdrawalAction', compact('request_id', 'request_details'));
			}
		}
	}

	public function postCancelRequest()
	{
		if(!BasicCUtil::sentryCheck())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		if(Input::get('request_id') != '')
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$rules = array('cancel_reason' => 'Required');
			$messages = array();
			$v = Validator::make(Input::all(), $rules, $messages);
			if($v->passes())
			{
				$withdraw_obj = Credits::initializeWithDrawal();
				$request_id = Input::get('request_id');
				$request_details = $this->myaccount_listing_service->chkIsValidCancelRequest($request_id, $withdraw_obj);
				if(count($request_details) > 0)
				{
					$this->myaccount_listing_service->cancelWithdrawalRequest(Input::get('request_id'), Input::get('cancel_reason'));

					$admin_id = Config::get('generalConfig.admin_id');
					$admin_details = CUtil::getUserDetails($admin_id);
					$user_details = CUtil::getUserDetails($logged_user_id);

					if($admin_details!= '') {
						$subject = Lang::get('email.user_cancelled_withdraw_request');
						$msg = Lang::get('email.user_cancelled_withdraw_request');

						$data = array(
							'request_id' => Input::get('request_id'),
							'currency' => $request_details['currency'],
							'amount' => $request_details['amount'],
							'cancel_reason' => Input::get('cancel_reason'),
							'admin_name' => $admin_details['user_name'],
							'user_name'	 => $user_details['user_name'],
							'user_email' => $user_details['email'],
							'admin_email' => Config::get("generalConfig.invoice_email"),//$admin_details['email'],
							'subject' => Config::get('generalConfig.site_name')." - ".$subject,
							'msg' => $msg,
						);
						try {
							//Mail to User
							Mail::send('emails.myWithdrawalCancelMailToAdmin', $data, function($m) use ($data) {
									$m->to($data['admin_email']);
									$m->subject($data['subject']);
							});
						} catch (Exception $e) {
							//return false
							CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
						}
					}
					if($user_details!= '') {
						$subject = Lang::get('email.your_withdraw_request_cancelled');
						$msg = Lang::get('email.your_withdraw_request_cancelled');

						$data = array(
							'request_id' => Input::get('request_id'),
							'currency' => $request_details['currency'],
							'amount' => $request_details['amount'],
							'cancel_reason' => Input::get('cancel_reason'),
							'admin_name'	 => $admin_details['user_name'],
							'user_name'	 => $user_details['user_name'],
							'user_email'	 => $user_details['email'],
							'admin_email'	 => Config::get("generalConfig.invoice_email"),//$admin_details['email'],
							'subject' => Config::get('generalConfig.site_name')." - ".$subject,
							'msg' => $msg,
						);
						try {
							//Mail to User
							Mail::send('emails.myWithdrawalCancelMailToUser', $data, function($m) use ($data) {
									$m->to($data['user_email']);
									$m->subject($data['subject']);
							});
						} catch (Exception $e) {
							//return false
							CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
						}
					}
					$success_msg = trans('myaccount/form.my-withdrawals.cancel_request_submitted_suc_msg');
					$show_form = false;
					return View::make('myaccount/manageWithdrawalAction', compact('request_id', 'success_msg', 'show_form'));
				}
			}
			else
			{
				//return Redirect::to('users/cancel-booking/'.Input::get('request_id'))->withInput()->withErrors($v);
				return Redirect::to('users/my-withdrawals')->with('error_message', trans('common.not_authorize'));
			}
		}
	}

	public function convertFunds()
	{
		$d_arr = array();

		$request_details = $currency_details = array();
		$user_acc_bal_details = $this->myaccountService->fetchuserAccountBalance();
		$d_arr['allow_convert_funds'] = $d_arr['add_form'] = $d_arr['preview_form'] = 0;
		if(count($user_acc_bal_details) > 0)
		{
			$d_arr['allow_convert_funds'] = $d_arr['add_form'] = 1;
			$available_currency = array();
			$available_currency[''] = trans('common.select_option');
			foreach($user_acc_bal_details as $user_curr)
			{
				if(round($user_curr['cleared_amount']) > 0)
				{
					$available_currency[$user_curr['currency']] = $user_curr['currency'];
					$currency_details[] = $user_curr;
				}
				else
				{
					if($user_curr['currency'] == "INR" || $user_curr['currency'] == "USD")
					{
						$currency_details[] = array('id' => $user_curr['id'], 'currency' => $user_curr['currency'],
													'pending_amount' => $user_curr['pending_amount'], 'cleared_amount' => 0,
													'currency_symbol' => $user_curr['currency_symbol']);
					}
				}
			}
			$d_arr['currency_from_arr'] = $available_currency;
			$d_arr['currency_to_arr'] = array("" => trans('common.select_option'), "INR" => "INR", "USD" => "USD" );
			$d_arr['user_acc_bal_details'] = $currency_details;
			$d_arr['show_currency_to_holder'] = 0;
			$d_arr['currency_from'] = "";
			$d_arr['currency_to'] = "";
			$d_arr['currency_symbol_from'] = "";
			$d_arr['currency_symbol_to'] = "";
			$d_arr['amount_from'] = "";
			$d_arr['converted_amt'] = "";
			$d_arr['exchange_amt'] = "";
			$d_arr['currency_details'] = Products::setCurrencyDetails();
		}
		//Set Meta details
		$get_common_meta_values = Cutil::getCommonMetaValues('convert-funds');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}

		return View::make('myaccount/payment/convertFunds', compact('d_arr'));
	}

	public function postConvertFunds()
	{
		if(!BasicCUtil::sentryCheck())
		{
			$url = url('/');
			return Redirect::to($url);
		}

		$input = Input::all();

		$withdraw_amount = Input::get('amount_from');
		$withdraw_currency = Input::get('currency_from');
		$to_currency = Input::get('currency_to');

		$rules['currency_from'] = 'Required';
		$rules['currency_to'] = 'Required';
		$rules['amount_from'] = 'required|IsValidPrice|AllowedWithdrawalCurrency:'.$withdraw_currency.'|AllowedWithdrawalAmount:'.$withdraw_amount.",".$withdraw_currency;
		$messages = array(
		    'amount_from.allowed_withdrawal_amount' => trans('myaccount/form.my-withdrawals.withdraw_amount_err_msg'),
		    'amount_from.allowed_withdrawal_currency' => trans('myaccount/form.my-withdrawals.withdraw_request_exist_err_msg'),
		    'amount_from.is_valid_price' => trans('myaccount/form.my-withdrawals.withdraw_request_invalid_amount'),
		);

		$validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->fails())
		{
			return Redirect::back()->withInput()->withErrors($validator)->with('error_message', trans('common.enter_valid_inputs'));
		}

		if(Input::has("convert_currency"))
		{
			$logged_user_id = getAuthUser()->user_id;
			$txn_id = $this->sitetransactionservice->handleCurrencyConversion($logged_user_id, $withdraw_amount, $withdraw_currency, $to_currency);
			if($txn_id)
				return Redirect::to('users/my-transactions')->with('success_message', trans('myaccount/form.my-withdrawals.currency_conversion_done'));
			else
				return Redirect::back()->withInput()->with('error_message', trans('common.errors_found'));
		}
		elseif(Input::has("edit_request"))
		{
			return Redirect::back()->withInput();
		}
		$d_arr = array();
		$d_arr['add_form'] = 0;
		$d_arr['allow_convert_funds'] = $d_arr['preview_form'] = 1;
		$d_arr['currency_from'] = $withdraw_currency;
		$d_arr['currency_to'] = $to_currency;
		$d_arr['amount_from'] = Input::get('amount_from');
		$d_arr['currency_details'] = Products::setCurrencyDetails();
		$d_arr['exchange_amt'] = "";

		//Set Meta details
		$get_common_meta_values = Cutil::getCommonMetaValues('convert-funds');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}

		return View::make('myaccount/payment/convertFunds', compact('d_arr'));



	}

	public function getWithdrawals($transfer_thru='')
	{
		$credit_obj = Credits::initialize();
		if(!BasicCUtil::sentryCheck())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		$d_arr = array();
		$d_arr['title'] = $d_arr['note'] = "";
		$d_arr['allow_withdrawal'] = 0;
		if($transfer_thru == "paypal" || $transfer_thru == "wire_transfer" || $transfer_thru == "neft")
		{
			$d_arr['title'] = trans('myaccount/form.my-withdrawals.withdrawal_'.$transfer_thru.'_title');
			$d_arr['note'] = trans('myaccount/form.my-withdrawals.withdrawal_'.$transfer_thru.'_note');
			$d_arr['paypal_usd_fee'] = Config::get("payment.withdraw_paypal_transaction_fee_usd");
			$d_arr['wire_transfer_usd_fee']= Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
			$d_arr['neft_inr_fee'] = Config::get("payment.withdraw_neft_transaction_fee_inr");

			if($transfer_thru == "neft")
			{
				$withdraw_currency = "INR";
				$txn_fee = Config::get("payment.withdraw_neft_transaction_fee_inr");
			}
			elseif($transfer_thru == "paypal")
			{
				$withdraw_currency = "USD";
				$txn_fee = Config::get("payment.withdraw_paypal_transaction_fee_usd");
			}
			elseif($transfer_thru == "wire_transfer")
			{
				$withdraw_currency = "USD";
				$txn_fee = Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
			}
			$user_balance = $this->myaccount_listing_service->fetchuserAccountBalanceByCurrency($withdraw_currency, $credit_obj);
			$d_arr['user_balance'] = $user_balance;
			$d_arr['withdraw_currency'] = $withdraw_currency;
			$d_arr['transfer_thru'] = $transfer_thru;
			$d_arr['transfer_thru_lbl'] = ucwords(str_replace("_", " ", $transfer_thru));
			$d_arr['withdraw_fee'] = $txn_fee;
			$d_arr['currency_details'] = Products::setCurrencyDetails();
			if(round($user_balance) > 0)
			{
				$d_arr['add_form'] = $d_arr['allow_withdrawal'] = 1;
			}
		}
		else
		{
			return Redirect::to('users/my-withdrawals')->with('error_message', trans('common.not_authorize'));
		}

		//Set Meta details
		$get_common_meta_values = Cutil::getCommonMetaValues('my-withdrawals');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myaccount/manageWithdrawal', compact('d_arr'));
	}

	public function postWithdrawals()
	{
		if(!BasicCUtil::sentryCheck())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		$credit_obj = Credits::initialize();
		$withdraw_obj = Credits::initializeWithDrawal();

		$logged_user_id = BasicCUtil::getLoggedUserId();
		$withdraw_amount = Input::get('request_amount');
		$withdraw_currency = Input::get('withdraw_currency');
		$pay_to_details = Input::get('pay_to_details');
		$rules['request_amount'] = 'required|IsValidPrice|AllowedWithdrawalCurrency:'.$logged_user_id.",".$withdraw_currency.'|AllowedWithdrawalAmount:'.$logged_user_id.",".$withdraw_amount.",".$withdraw_currency.'|AllowedMinWithdrawalAmount:'.$withdraw_amount.",".$withdraw_currency;
		$rules['pay_to_details'] = 'required';
		$min_amount_err_msg = trans('myaccount/form.my-withdrawals.withdraw_amount_min_err_msg');
		if($withdraw_currency == 'USD')
			$amount_lbl = "USD ".Config::get("payment.minimum_withdrawal_amount");
		else
			$amount_lbl = "INR ".Config::get("payment.minimum_withdrawal_amount_inr");
		$min_amount_err_msg = str_replace("VAR_MIN_AMOUNT", $amount_lbl, $min_amount_err_msg);

		$messages = array(
		    'request_amount.allowed_withdrawal_amount' => trans('myaccount/form.my-withdrawals.withdraw_amount_err_msg'),
		    'request_amount.allowed_withdrawal_currency' => trans('myaccount/form.my-withdrawals.withdraw_request_exist_err_msg'),
		    'request_amount.is_valid_price' => trans('myaccount/form.my-withdrawals.withdraw_request_invalid_amount'),
		    'request_amount.allowed_min_withdrawal_amount' => $min_amount_err_msg
		);

		$validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->fails())
		{
			$errors = $validator->errors()->all();
			return Redirect::back()->withInput()->withErrors($validator);
		}

		if(Input::has("submit_request"))	// For submit request
		{
			//echo '<pre>';print_r(Input::all());die;
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$user_balance_amount = DB::table('user_account_balance')->where('user_id',$logged_user_id)->pluck('amount');
			$total_credit_amount = 0;
			/*$total_credit_amount = DB::table('common_invoice')->where('user_id',$logged_user_id)
										->where('reference_type','=','Credits')
										->where('status', '=', 'Unpaid')
										->SUM('amount');*/
			//echo $total_credit_amount;exit;
			$reduce_ammout = $user_balance_amount - $total_credit_amount;
			if($withdraw_amount > $reduce_ammout)
			{
				return Redirect::back()->withInput()->with('error_message',trans('myaccount/form.my-withdrawals.Check_withdraw_balance_message'));
			}
			$this->myaccount_listing_service->addWithdrawalRequest(Input::all(), $withdraw_obj);
			$admin_id = Config::get('generalConfig.admin_id');
			$admin_details = CUtil::getUserDetails($admin_id);
			$user_details = CUtil::getUserDetails($logged_user_id);
			if(Input::get('transfer_thru') == "neft") {
				$payment_type = 'NEFT';
			}
			elseif(Input::get('transfer_thru') == "paypal") {
				$payment_type = 'Paypal';
			}
			elseif(Input::get('transfer_thru') == "wire_transfer") {
				$payment_type = 'WireTransfer';
			}
			if($user_details != ''){
				$subject = Lang::get('email.your_withdraw_request_submitted');
				$msg = Lang::get('email.your_withdraw_request_submitted');
				$data = array(
					'withdraw_amount' => $withdraw_amount,
					'withdraw_currency' => $withdraw_currency,
					'user_name'	 => $user_details['user_name'],
					'payment_method'	 => $payment_type,
					'pay_to_details'	 => $pay_to_details,
					'user_email'	 => $user_details['email'],
					'subject' => Config::get('generalConfig.site_name')." - ".$subject,
					'msg' => $msg,
				);
				try {
					//Mail to User
					Mail::send('emails.myWithdrawalRequestMailToUser', $data, function($m) use ($data) {
							$m->to($data['user_email']);
							$m->subject($data['subject']);
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}
			}
			if($admin_details != ''){
				$subject = Lang::get('email.withdraw_request_added');
				$msg = Lang::get('email.withdraw_request_added');
				$data = array(
					'withdraw_amount' => $withdraw_amount,
					'withdraw_currency' => $withdraw_currency,
					'admin_name'	 => $user_details['user_name'],
					'user_name'	 => $user_details['user_name'],
					'payment_method'	 => $payment_type,
					'pay_to_details'	 => $pay_to_details,
					'user_email'	 => $user_details['email'],
					'admin_email'	 => Config::get("generalConfig.invoice_email"),//$admin_details['email'],
					'subject' => Config::get('generalConfig.site_name')." - ".$subject,
					'msg' => $msg,
				);
				try {
					//Mail to User
					Mail::send('emails.myWithdrawalRequestMailToAdmin', $data, function($m) use ($data) {
							$m->to($data['admin_email']);
							$m->subject($data['subject']);
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}
			}

			return Redirect::to('users/my-withdrawals')->with('success_message', trans('myaccount/form.my-withdrawals.withdraw_request_success'));
		}
		elseif(Input::has("edit_request"))
		{
			return Redirect::back()->withInput();
		}

		$d_arr = array();
		$d_arr['title'] = $d_arr['note'] = "";
		$transfer_thru = Input::get("transfer_thru");

		$d_arr['add_form'] = 0;
		$d_arr['preview_form'] = 1;
		$d_arr['allow_withdrawal'] = 1;
		$d_arr['title'] = trans('myaccount/form.my-withdrawals.withdrawal_'.$transfer_thru.'_title');
		$d_arr['note'] = trans('myaccount/form.my-withdrawals.withdrawal_'.$transfer_thru.'_note');
		$d_arr['paypal_usd_fee'] = Config::get("payment.withdraw_paypal_transaction_fee_usd");
		$d_arr['wire_transfer_usd_fee']= Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
		$d_arr['neft_inr_fee'] = Config::get("payment.withdraw_neft_transaction_fee_inr");
		$d_arr['transfer_thru'] = $transfer_thru;

		if($transfer_thru == "neft")
		{
			$withdraw_currency = "INR";
			$txn_fee = Config::get("payment.withdraw_neft_transaction_fee_inr");
		}
		elseif($transfer_thru == "paypal")
		{
			$withdraw_currency = "USD";
			$txn_fee = Config::get("payment.withdraw_paypal_transaction_fee_usd");
		}
		elseif($transfer_thru == "wire_transfer")
		{
			$withdraw_currency = "USD";
			$txn_fee = Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
		}

		$user_balance = $this->myaccount_listing_service->fetchuserAccountBalanceByCurrency($withdraw_currency, $credit_obj);
		$d_arr['withdraw_fee'] = $txn_fee;
		$d_arr['user_balance'] = $user_balance;
		$d_arr['request_amount'] = Input::get('request_amount');
		$d_arr['withdraw_currency'] = Input::get('withdraw_currency');
		$d_arr['pay_to_details'] = Input::get('pay_to_details');
		$d_arr['currency_details'] = Products::setCurrencyDetails();
		$d_arr['amount_to_withdraw'] = Products::setCurrencyDetails();
		$d_arr['balance_amount'] =  $d_arr['request_amount'] - $txn_fee;

		$get_common_meta_values = Cutil::getCommonMetaValues('my-withdrawals');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}

		return View::make('myaccount/manageWithdrawal', compact('d_arr'));
	}
}