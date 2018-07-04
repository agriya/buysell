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
class WalletAccountController extends BaseController
{

	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
		if(CUtil::chkIsAllowedModule('sudopay')) {
			$this->sudopay_service = new \SudopayService();
			$mode = (Config::get('plugin.sudopay_payment_test_mode')) ? 'test' : 'live';
			$this->sudopay_credential = array(
			    'api_key' => Config::get('plugin.sudopay_'.$mode.'_api_key'),
			    'merchant_id' => Config::get('plugin.sudopay_'.$mode.'_merchant_id'),
			    'website_id' => Config::get('plugin.sudopay_'.$mode.'_website_id'),
			    'secret' => Config::get('plugin.sudopay_'.$mode.'_secret_string')
			);
			$this->sa = new \SudoPay_API($this->sudopay_credential);
			$this->sc = new \SudoPay_Canvas($this->sa);
		}
    }


	public function getIndex()
	{

		$credits_obj = Credits::initialize();
		$withdraw_obj = Credits::initializeWithDrawal();
		$user_id = BasicCUtil::getLoggedUserId();
		$credits_obj->setUserId($user_id);
		$credits_obj->setFilterUserId($user_id);

		$account_balance_arr = $credits_obj->getWalletAccountBalance();
		if(is_string($account_balance_arr) || count($account_balance_arr) <= 0)
		{
			$account_balance_arr = array();
		}

		$withdraw_obj->setFilterUserId($user_id);
		$withdrwal_request_details = $withdraw_obj->getWithdrwalRequests('paginate', 10);
		if(is_string($withdrwal_request_details) || count($withdrwal_request_details) <= 0)
			$withdrwal_request_details = array();
		else {
			//$withdrwal_request_details = $withdrwal_request_details->toArray();
		//	$withdrwal_request_details = $withdrwal_request_details['data'];
		}
		//Get user account balance list
		$user_account_balance = array();
		$myaccount_listing_service = new MyAccountListingService();
		$user_acc_bal_details = $myaccount_listing_service->fetchuserAccountBalance($credits_obj, $user_id);

		$main_currency_arr['USD'] = array('currency_symbol' => '$', 'amount' => 0.0);
		$main_currency_arr['INR'] = array('currency_symbol' => 'Rs', 'amount' => 0.0);
		$d_arr['main_currency_arr'] = $main_currency_arr;
		if(count($user_acc_bal_details) > 0) {
			$payments_arr = array();
			foreach($user_acc_bal_details as $acc_bal) {
				if(round($acc_bal['amount']) > 0) {
					if($acc_bal['currency'] == "USD" || $acc_bal['currency'] == "INR") {
						$payments_arr['main'][] = $acc_bal;
						$main_currency_arr[$acc_bal['currency']] = array('currency_symbol' => $acc_bal['currency_symbol'], 'amount' => $acc_bal['amount']);
					}
					else {
						$payments_arr['other'][] = $acc_bal;
					}
				}
			}

			$d_arr['main_currency_arr'] = $main_currency_arr;
			$d_arr['user_acc_bal_details'] = $payments_arr;
		}
		$user_details = DB::table('users')
						->leftjoin('users_groups','users.id','=','users_groups.user_id')
						->where('id', $user_id)
						->first();
		$get_common_meta_values = Cutil::getCommonMetaValues('my-wallet-account');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myWalletAccount', compact('user_details','account_balance_arr', 'withdrwal_request_details','d_arr'));
	}
	public function getAddAmount(){
		if(!CUtil::chkIsAllowedModule('sudopay')) {
			$error_msg = trans('common.invalid_action');
			return Redirect::to('walletaccount/index')->with('error_message', $error_msg);
		}
		/*$credits_obj = Credits::initialize();
		$withdraw_obj = Credits::initializeWithDrawal();
		$user_id = BasicCUtil::getLoggedUserId();
		$credits_obj->setUserId($user_id);
		$credits_obj->setFilterUserId($user_id);
		$account_balance_arr = $credits_obj->getWalletAccountBalance();

		$user_details = DB::table('users')
						->leftjoin('users_groups','users.id','=','users_groups.user_id')
						->where('id', $user_id)
						->first();*/
		$countries = array('' => trans('common.select_a_country'));
		$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc');
		$countries_arr = array_except($countries_arr, array('38'));//Remove china
		$countries_list = $countries+$countries_arr;
		$get_common_meta_values = Cutil::getCommonMetaValues('add-amount-to-wallet-account');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addWalletAmount', compact('countries_list'));
	}

	public function postAddAmount()
	{
		//print_r(Input::all());exit;
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$inputs = Input::all();
		if(Input::has("edit_request")){
			return Redirect::back()->withInput();
		}
		$rules = array('acc_balance' => 'required|numeric|min:'.intval(Config::get('generalConfig.minimum_amount_added_to_wallet')));
		$message = array('acc_balance.required' =>'Required', 'acc_balance.min' => trans('walletAccount.min_amount_to_be_added').' '.Config::get('generalConfig.site_default_currency').' '.Config::get('generalConfig.minimum_amount_added_to_wallet'));
		$validator = Validator::make(Input::all(), $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		else {
			if(Input::has("pay_confirm") || Input::get('pay_confirm') == 'Confirm'){
				$data_arr['acc_balance'] = Input::get('acc_balance');
				$data_arr['buyer_address_line1'] = $data_arr['address_line1'] = Input::get('address_line1');
				$data_arr['buyer_address_line2'] = $data_arr['address_line2'] = Input::get('address_line2');
				$data_arr['buyer_street'] = $data_arr['street'] = Input::get('street');
				$data_arr['buyer_city'] = $data_arr['city'] = Input::get('city');
				$data_arr['buyer_state'] = $data_arr['state'] = Input::get('state');
				$data_arr['country_id'] = Input::get('country_id');
				$data_arr['buyer_country'] = Input::get('country_iso');
				$data_arr['buyer_zip_code'] = $data_arr['zip_code'] = Input::get('zip_code');
				$data_arr['buyer_phone_no'] = $data_arr['phone_no'] = Input::get('phone_no');
				$data_arr['payment_method'] = Input::get('payment_method');
				$data_arr['confirm'] = 'Yes';

				//For sudopay
				$d_arr = array();
				if(CUtil::chkIsAllowedModule('sudopay')) {
					$d_arr['sudopay_service'] = $this->sudopay_service; //sudopay service obj
					$d_arr['sc'] = $this->sc; //sudopay canvas obj
					$d_arr['sa'] = $this->sa; //sudopay api obj
					$d_arr['sudopay_credential'] = $this->sudopay_credential;
					//echo '<pre>';print_r($d_arr['sudopay_credential']);echo '</pre>';exit;
					$plan_details = $this->sa->callGetPlan();
					$sudopay_brand = '';
					$sudopay_fees_payer = 'site';
					if(isset($plan_details['brand']) && $plan_details['brand'] != '') {
						$sudopay_brand = $plan_details['brand'];
						$sudopay_fees_payer = $plan_details['sudopay_fees_payer'];
					}
					$d_arr['sudopay_brand'] = $sudopay_brand;
					$d_arr['sudopay_fees_payer'] = $sudopay_fees_payer;
					if($sudopay_brand == 'SudoPay Branding') {
						$d_arr['is_credit'] = 'No';
						$d_arr['reference_type'] = 'Usercredits';
						$d_arr['logged_user_id'] = $logged_user_id;
						$sudopay_data_arr = $data_arr + $d_arr;
						$d_arr['sudopay_fields_arr'] = $this->sudopay_service->getSudopayFieldsUserCreditsArr(0, $sudopay_data_arr);
					}
				}

				$get_common_meta_values = Cutil::getCommonMetaValues('add-amount-to-wallet-account');
				if($get_common_meta_values)
				{
					$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
					$this->header->setMetaDescription($get_common_meta_values['meta_description']);
					$this->header->setMetaTitle($get_common_meta_values['meta_title']);
				}
				return View::make('addWalletAmount', compact('data_arr', 'd_arr'));
			}elseif(Input::has("edit_request") && Input::get('pay_confirm') == 'edit_request'){
				return Redirect::back()->withInput();
			}
		}
	}

	public function postAddUsersCredits()
	{

		$common_invoice_id = Input::get('common_invoice_id');
		$currency_code = Input::get('currency_code');
		$payment_gateway_chosen = Input::get('payment_gateway_chosen');
		$amount = Input::get('amount');
		$d_arr = Input::get('d_arr');
		//$is_credit = Input::has('is_credit') ? Input::get('is_credit') : ''; //Pay amount using credits and remaning amount by paypal

		$logged_user_id = BasicCUtil::getLoggedUserId();

		//Add credits to credits log
		$manage_credits_obj =  Products::initializeManageCredits();
		$common_invoice_obj =  Products::initializeCommonInvoice();

		$default_curreny = Config::get('generalConfig.site_default_currency');
		$manage_credits_obj->setCreditType('user_credits');
		$manage_credits_obj->setCurrency($default_curreny);
		$manage_credits_obj->setAmount($amount);
		$manage_credits_obj->setCreditedBy($logged_user_id);
		$manage_credits_obj->setCreditedTo($logged_user_id);
		$manage_credits_obj->setAdminNotes('Amount added to your account by you.');
		$manage_credits_obj->setUserNotes('Amount added to account by user.');
		$manage_credits_obj->setPaid('No');
		$manage_credits_obj->setGenerateInvoice('Yes');
		$manage_credits_obj->setCreditsDateAdded(DB::raw('NOW()'));
		$resp = $manage_credits_obj->Addcredits();

		$respd = json_decode($resp, true);
		//echo '<pre>';print_r($respd);echo '</pre>';exit;
		if ($respd['status'] == 'error') {
			$error_exists = true;
			$error_msg = '';
			if(count($respd['error_messages']) > 0) {
				foreach($respd['error_messages'] AS $err_msg) {
					$error_msg .= "<p>".$err_msg."</p>";
				}
			}
			$redirect_url = URL::to('walletaccount/add-amount');
			echo $redirect_url."|~~|".$error_exists;
			exit;
		}
		else {
			//Log::info(print_r($cart_details,1));
			//Log::info($input['generate_invoice']);
			//Add common invoice
			$invoice_info = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId('Usercredits', $respd['credit_id']);
			if($invoice_info) {
				$common_invoice_obj->setCommonInvoiceId($invoice_info['common_invoice_id']);
			}
			$common_invoice_obj->setUserId($logged_user_id);
			$common_invoice_obj->setReferenceType('Usercredits');
			$common_invoice_obj->setReferenceId($respd['credit_id']);
			$common_invoice_obj->setCurrency($default_curreny);
			$common_invoice_obj->setAmount(CUtil::formatAmount($amount));
			$common_invoice_obj->setStatus('Unpaid');
			$resp_invoice = $common_invoice_obj->addCommonInvoice();
			$resp_invoiced = json_decode($resp_invoice, true);
			if ($resp_invoiced['status'] == 'error') {
				$error_exists = true;
				$error_msg = '';
				if(count($resp_invoiced['error_messages']) > 0) {
					foreach($resp_invoiced['error_messages'] AS $err_msg) {
						$error_msg .= "<p>".$err_msg."</p>";
					}
				}
				$redirect_url = URL::to('walletaccount/add-amount');
				echo $redirect_url."|~~|".$error_exists;
				exit;
			}
			else {
				$checkout_obj = new PayCheckOutController();
				$common_invoice_id = $resp_invoiced['common_invoice_id'];
				$return = $checkout_obj->generateInvoice($common_invoice_id, $currency_code, $payment_gateway_chosen, $d_arr);
				return $return;
				exit;
			}
		}
	}
}