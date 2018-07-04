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
class SudopayService
{
	private $payment_amount_details	= array();
	private $sudopay_fees_payer	= 'Merchant';//Merchant or Buyer

	public function __construct()
	{
		$this->manage_credits_obj =  Products::initializeManageCredits();
		$this->common_invoice_obj =  Products::initializeCommonInvoice();
		$this->shop_order_obj = Webshoporder::initialize();
		$this->invoice_obj = Webshoporder::initializeInvoice();
		$this->product_obj = Products::initialize();
	}

	public function getPlanDetails($sa)
 	{
		$plan_details = array();
		$details = $sa->callGetPlan();
		$plan_details['subscription_plan'] = isset($details['name']) ? $details['name'] : '';
		$plan_details['branding'] = isset($details['brand']) ? $details['brand'] : '';
		return $plan_details;
 	}

	public function getPaymentGatewayDetailsLive($sa)
 	{
 		$enabled_gateways = array();
		$action = '';
		$gateways_arr = $sa->callGetGateways(array(   'supported_actions' => $action     ));
		//echo '<pre>';print_r($gateways_arr);echo '</pre>';die;
		if (!empty($gateways_arr) && empty($gateways_arr['error']) && !empty($gateways_arr['gateways'])) {
			DB::table('sudopay_payment_groups')->delete();
			DB::table('sudopay_payment_gateways')->delete();
			foreach($gateways_arr['gateways'] as $parent_gateway) {
				/*foreach($parent_gateway['gateways'] as $gateway) {
					$enabled_gateways[] = $gateway;
				}*/

				$group_insert_arr['created'] =  DB::raw('NOW()');
				$group_insert_arr['modified'] =  DB::raw('NOW()');
				$group_insert_arr['sudopay_group_id'] = $parent_gateway['id'];
				$group_insert_arr['name'] = $parent_gateway['name'];
				$group_insert_arr['thumb_url'] = $parent_gateway['thumb_url'];
				$sudopay_payment_group_id = DB::table('sudopay_payment_groups')->insertGetId($group_insert_arr);

				foreach($parent_gateway['gateways'] as $gateway) {
					$marketplace = 0;
					$marketplace_arr = $gateway['supported_features']['0']['actions'];
					$gateway_insert_arr['created'] =  DB::raw('NOW()');
					$gateway_insert_arr['modified'] =  DB::raw('NOW()');
					$gateway_insert_arr['sudopay_gateway_name'] =  $gateway['name'];
					$gateway_insert_arr['sudopay_gateway_id'] =  $gateway['id'];
					$gateway_insert_arr['sudopay_payment_group_id'] =  $sudopay_payment_group_id;
					$gateway_insert_arr['sudopay_gateway_details'] =  serialize($gateway);
					$gateway_insert_arr['days_after_amount_paid'] =  0;
					foreach($marketplace_arr as $market_arr){
						if(starts_with($market_arr, 'Marketplace')){
							$marketplace = 1;
						}
					}
					$gateway_insert_arr['is_marketplace_supported'] =  $marketplace;
					$gateway_insert_arr['name'] =  $gateway['name'];
					$sudopay_payment_group_id = DB::table('sudopay_payment_gateways')->insertGetId($gateway_insert_arr);
				}
			}
		}
 	}

	public function getPaymentGatewayDetails()
 	{
 		$enabled_gateways = array();
 		$payment_gateways = DB::table('sudopay_payment_gateways')->get();
 		if(count($payment_gateways) > 0) {
 			//echo '<pre>';print_r($payment_gateways);echo '</pre>';die;
			foreach($payment_gateways as $key => $payment_gateway) {
				//echo '<br>'.$payment_gateway->sudopay_gateway_name;
				$sudopay_gateway_details = $payment_gateway->sudopay_gateway_details;
				$enabled_gateways[] = unserialize($sudopay_gateway_details);
				/*echo '=====================>';
				echo '<pre>';print_r($sudopay_gateway_details);echo '</pre>';*/
			}
		}


		//echo '<pre>';print_r($gateways_arr);echo '</pre>';die;
		/*if (!empty($gateways_arr) && empty($gateways_arr['error']) && !empty($gateways_arr['gateways'])) {
			foreach($gateways_arr['gateways'] as $parent_gateway) {
				foreach($parent_gateway['gateways'] as $gateway) {
					$enabled_gateways[] = $gateway;
				}
			}
		}*/
		return $enabled_gateways;
 	}

	public function updatePaymentGatewayDetails($input)
 	{
 		foreach($input as $key => $val) {
			if(Config::has('plugin.'.$key) || Config::has('payment.'.$key)) {
				DB::table('config_data')
					->whereRaw('config_var = ?', array($key))
					->update(array('config_value' => $val));
			}
		}
		$cache_key = 'config_data_key';
		$forget_key = HomeCUtil::cacheForgot($cache_key);
		if($forget_key)
		{
			$data = DB::table('config_data')->get();
			HomeCUtil::cachePut($cache_key, $data);
		}
		return json_encode(array('status' => 'success'));
 	}

	public function updateConfigStatus($file_name, $config_var, $action)
	{
		$cache_key = 'config_data_key';
		switch($action)
		{
			case 'Active':
				DB::table('config_data')
					->whereRaw('file_name = ? AND config_var = ?', array($file_name, $config_var))
					->update(array('config_value' => 1));
				$forget_key = HomeCUtil::cacheForgot($cache_key);
				if($forget_key)
				{
					$data = DB::table('config_data')->get();
					HomeCUtil::cachePut($cache_key, $data);
				}
				$success_msg = Lang::get('sudopay::sudopay.activated_suc_msg');
				break;

			case 'Inactive':
				DB::table('config_data')
					->whereRaw('file_name = ? AND config_var = ?', array($file_name, $config_var))
					->update(array('config_value' => 0));
				$forget_key = HomeCUtil::cacheForgot($cache_key);
				if($forget_key)
				{
					$data = DB::table('config_data')->get();
					HomeCUtil::cachePut($cache_key, $data);
				}
				$success_msg = Lang::get('sudopay::sudopay.deactivated_suc_msg');
				break;

			default;
				$success_msg = Lang::get('sudopay::sudopay.select_valid_actiion');
				break;
		}
		return $success_msg;
	}

	public function updateSudopayIpnLogs($input = array())
 	{
		$ipn_log['created'] =  DB::raw('NOW()');
		$ipn_log['modified'] =  DB::raw('NOW()');
		$ipn_log['ip'] = $_SERVER['REMOTE_ADDR'];
		$ipn_log['post_variable'] = serialize($input);
		$ipn_log_id = DB::table('sudopay_ipn_logs')->insertGetId($ipn_log);
	}

	public function updateSudopayTransactionLogs($input = array())
 	{
		$trans_log['created'] =  DB::raw('NOW()');
		$trans_log['modified'] =  DB::raw('NOW()');
		$trans_log['amount'] = isset($input['amount']) ? $input['amount'] : 0;
		$trans_log['payment_id'] = isset($input['id']) ? $input['id'] : 0;
		$trans_log['class'] = isset($input['class']) ? $input['class'] : '';
		$trans_log['foreign_id'] = isset($input['foreign_id']) ? $input['foreign_id'] : 0;
		$trans_log['sudopay_pay_key'] = isset($input['paykey']) ? $input['paykey'] : '';
		$trans_log['merchant_id'] = isset($input['merchant_id']) ? $input['merchant_id'] : 0;
		$trans_log['gateway_id'] = isset($input['gateway_id']) ? $input['gateway_id'] : 0;
		$trans_log['gateway_name'] = isset($input['gateway_name']) ? $input['gateway_name'] : '';
		$trans_log['status'] = isset($input['status']) ? $input['status'] : '';
		$trans_log['payment_type'] = isset($input['action']) ? $input['action'] : '';
		$trans_log['buyer_id'] = isset($input['x-buyer_id']) ? $input['x-buyer_id'] : 0;
		$trans_log['buyer_email'] = isset($input['buyer_email']) ? $input['buyer_email'] : '';
		$trans_log['buyer_address'] = isset($input['buyer_address']) ? $input['buyer_address'] : '';
		$trans_log_id = DB::table('sudopay_transaction_logs')->insertGetId($trans_log);
	}

	public function buildSudopayIpnLogsQuery()
	{
		return DB::table('sudopay_ipn_logs')
					->Select("id", "created", "modified", "ip", "post_variable")
					->orderBy('id', 'DESC');
	}

	public function createReceiverAccount($input)
	{
		$gateway_id = $input['gateway_id'];
		$seller_id = $input['user_id'];
		if(!$this->isGatewayConnected($seller_id, $gateway_id)) {
			$reciver_arr['created'] =  DB::raw('NOW()');
			$reciver_arr['modified'] =  DB::raw('NOW()');
			$reciver_arr['user_id'] = $seller_id;
			$reciver_arr['sudopay_payment_gateway_id'] = $gateway_id;
			$sudopay_payment_gateways_users = DB::table('sudopay_payment_gateways_users')->insertGetId($reciver_arr);
		}

		$sudopay_receiver_id = $this->getSellerSudopayReceiverId($seller_id);
		if($sudopay_receiver_id == '') {
			$users = DB::table('users')
						->whereRaw('id = ?', array($seller_id))
						->update(array('sudopay_receiver_id' => $input['id']));
		}
	}

	public function getSellerSudopayReceiverId($seller_id) {
		$sudopay_receiver_id = DB::table('users')->whereRaw('id = ?', array($seller_id))->pluck('sudopay_receiver_id');
		return $sudopay_receiver_id;
	}

	public function deleteReceiverAccount($seller_id, $gateway_id)
	{
		DB::table('sudopay_payment_gateways_users')
			->whereRaw('user_id = ? AND sudopay_payment_gateway_id = ?', array($seller_id, $gateway_id))
			->delete();
	}

	public function isGatewayConnected($seller_id, $gateway_id)
 	{
 		$is_connected = DB::table('sudopay_payment_gateways_users')
		 					->whereRaw('user_id = ? AND sudopay_payment_gateway_id = ?', array($seller_id, $gateway_id))
							->count();
 		if($is_connected > 0) {
 			return true;
		}
		return false;
 	}

 	public function getSudopayFieldsArr($order_id, $order_details, $common_invoice_details, $d_arr) {
		$is_credit = $d_arr['is_credit'];
		$reference_type = $d_arr['reference_type'];
		$logged_user_id = $d_arr['logged_user_id'];
		$sudopay_credential = $d_arr['sudopay_credential'];
		$this->sudopay_service = new \SudopayService();
		$this->PayCheckOutService = new \PayCheckOutService();
		$common_invoice_id = $common_invoice_details['common_invoice_id'];

		$credit_id = 0;

		if($reference_type == 'Products') {
			$receivers_details = $this->PayCheckOutService->addOrderReceiversDetails($common_invoice_id, $order_id);
		}
		else {
			$receivers_details = $this->PayCheckOutService->addCreditReceiversDetails($common_invoice_id, $common_invoice_details);
		}

		$deal_item = 0;
		if(CUtil::chkIsAllowedModule('deals'))
		{
			$deal_service = new DealsService();
			$order_obj = Webshoporder::initialize();
			$order_item_details = $order_obj->getOrderitemDetails($order_id);
			$resp = $deal_service->fetchDealBasedReceiverAmount($order_item_details);
			if(COUNT($resp) > 0 &&  isset($resp['primary_amount']) &&  $resp['primary_amount'] >0 )
				$deal_item = 1;
		}

		//Get required details
		$buyer_details = \CUtil::getUserDetails($logged_user_id);
		if($reference_type == 'Products') {
			$billing_address = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_details['id'], 'user_id' => $logged_user_id));
			$billing_address_details = array();
			if(isset($billing_address[0]->shipping_address)) {
				//$shipping_address_id = isset($billing_address[0]->address_id)?$billing_address[0]->address_id:0;
				$billing_address_id = isset($shipping_address_details[0]->billing_address_id) ? $shipping_address_details[0]->billing_address_id : 0;
				$billing_address_details = $billing_address[0]->shipping_address[0];
				//echo '<pre>';print_r($billing_address_details->address_line1);echo '</pre>';exit;
			}
			$iso2_country_code = CurrencyExchangeRate::whereRaw('id = ?', array($billing_address_details->country_id))->pluck('iso2_country_code');
		}

		$sudopay_fees_payer = 'site';//site(merchant) or buyer
		$payment_method = 'simple';

		$sudopay_data = array();
		if(isset($d_arr)) {
//			Log::info('Input has d_arr[0]');
			$sudopay_data = $d_arr;
			if(isset($sudopay_data['sudopay_fees_payer'])) {
				$sudopay_fees_payer = $sudopay_data['sudopay_fees_payer'];
			}
		}
		//Sudo pay

		$amount = 0.00;
		$paypal_amount = 0.00;
		$reciver_amount = 0.00; //Seller amount
		$merchant_amount = 0.00;

		//Deal block start
		if($deal_item && $is_credit != 'Yes') {
			if($payment_method == 'marketplace') {
				$merchant_amount = $resp['primary_amount'];
				$reciver_amount = $resp['secondary_amount'];
			}
			$paypal_amount = $resp['primary_amount']+(isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0);
		}
		//Deal block end
		else if($is_credit == 'Yes') {
			//if the payment is payal with wallet then use only one receiver.
			//Transfer all fund to admin, Then add credits to buyer in wallet, Then credit it for purchase
			$tot_amount = $common_invoice_details['amount'];
			$user_account_balance = \CUtil::getUserAccountBalance($logged_user_id);
			$amount_to_pay = $tot_amount - $user_account_balance['amount'];
			$paypal_amount = $amount_to_pay;
		}
		else {
			foreach($receivers_details as $receiver) {
				$amount = \CUtil::formatAmount($receiver['amount']);
				if($receiver['is_admin'] == "Yes")	{
					$merchant_amount = $amount;
				}
				else {
					$reciver_amount = $reciver_amount + $amount;
				}
				$paypal_amount = $paypal_amount + $amount;
			}
		}

		$pay_data = array(	'merchant_id' => $sudopay_credential['merchant_id'],
						    'website_id' => $sudopay_credential['website_id'],
						    'amount' => $paypal_amount,
						    'currency_code' => $common_invoice_details['currency'],
						    'item_name' => 'Simple payment',
						    'item_description' => 'Simple description',
						    'x-common_invoice_id' => $common_invoice_id,
						    'x-buyer_id' => $logged_user_id,
						    'x-order_id' => $order_id,
						    'x-reference_type' => $reference_type,
						    'x-is_credit' => $is_credit,
						    'notify_url' => URL::to('sudopay/sudopay-payment-notify-url'),
						    'success_url' => URL::action("PayCheckOutController@paymentSuccess"),
						    'cancel_url' => URL::action("PayCheckOutController@paymentcancel")	);

		if($payment_method == 'simple') {
			$pay_data['action'] = 'capture';

			//if($sudopay_fees_payer != 'site')
				//$pay_data['fees_payer'] = $sudopay_fees_payer;
		}
		else {
			//Common $_marketplace_fields_arr for make markatplace payment it will be override on submit payment
			// basic fields for marketplace related actions...
			$pay_data = array_merge($pay_data, array(	'marketplace_receiver_id' => $receiver_id,//$receiver_id,
													    'marketplace_receiver_amount' => $reciver_amount,
													    'marketplace_fixed_merchant_amount' => $merchant_amount,
													    'action' => 'marketplace-capture'	));

			//if($sudopay_fees_payer != 'site')
				//$pay_data['marketplace_fees_payer'] = $sudopay_fees_payer; //Should check gateway support fees_payer (sudopay now support paypal only)
		}

		$pay_data['buyer_email'] = $buyer_details['email'];
		if($reference_type == 'Products') {
			$pay_data['buyer_phone'] = $billing_address_details->phone_no;
			$pay_data['buyer_address'] = $billing_address_details->address_line1;
			$pay_data['buyer_city'] = $billing_address_details->city;
			$pay_data['buyer_state'] = $billing_address_details->state;
			$pay_data['buyer_country'] = $iso2_country_code;//iso2_currency_code
			$pay_data['buyer_zip_code'] = $billing_address_details->zip_code;
		}
		else {
			$pay_data['buyer_phone'] = isset($sudopay_data['buyer_phone_no']) ? $sudopay_data['buyer_phone_no'] : '';
			$buyer_address_line1 = isset($sudopay_data['buyer_address_line1']) ? $sudopay_data['buyer_address_line1'] : '';
			$buyer_address_line2 = isset($sudopay_data['buyer_address_line2']) ? $sudopay_data['buyer_address_line2'] : '';
			$buyer_street = isset($sudopay_data['buyer_street']) ? $sudopay_data['buyer_street'] : '';
			$pay_data['buyer_address'] = $buyer_address_line1.' '.$buyer_address_line2.' '.$buyer_street;
			$pay_data['buyer_city'] = isset($sudopay_data['buyer_city']) ? $sudopay_data['buyer_city'] : '';
			$pay_data['buyer_state'] = isset($sudopay_data['buyer_state']) ? $sudopay_data['buyer_state'] : '';
			$pay_data['buyer_country'] = isset($sudopay_data['buyer_country_iso']) ? $sudopay_data['buyer_country_iso'] : '';
			$pay_data['buyer_zip_code'] = isset($sudopay_data['buyer_zip_code']) ? $sudopay_data['buyer_zip_code'] : '';
		}

		//$pay_data['preferred_gateways'] = 1;
		//$pay_data['marketplace_fees_payer'] = 'receiver';
		/*if($sudopay_data['parent_gateway_id'] == 4922 ) {
			$pay_data['credit_card_number'] = isset($sudopay_data['credit_card_number']) ? $sudopay_data['credit_card_number'] : '';
			$pay_data['credit_card_expire'] = isset($sudopay_data['credit_card_expire']) ? $sudopay_data['credit_card_expire'] : '';
			$pay_data['credit_card_name_on_card'] = isset($sudopay_data['credit_card_name_on_card']) ? $sudopay_data['credit_card_name_on_card'] : '';
			$pay_data['credit_card_code'] = isset($sudopay_data['credit_card_code']) ? $sudopay_data['credit_card_code'] : '';
		}*/

		$pay_data['buyer_ip'] = $_SERVER['REMOTE_ADDR'];
//		Log::info('I am pre variable');
//		Log::info(print_r($pay_data, 1));
		//$pay_data['gateway_id'] = 1;
        return $pay_data;
 	}

 	public function getSudopayFieldsUserCreditsArr($common_invoice_id = 0, $d_arr) {

 		//Add credits to credits log
		$manage_credits_obj =  Products::initializeManageCredits();
		$common_invoice_obj =  Products::initializeCommonInvoice();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$default_curreny = Config::get('generalConfig.site_default_currency');
		$amount = $d_arr['acc_balance'];

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

		if ($respd['status'] != 'error') {
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

			if ($resp_invoiced['status'] != 'error') {
				$common_invoice_id = $resp_invoiced['common_invoice_id'];
			}
		}

		////
		$is_credit = 'No';
		$reference_type = 'Usercredits';
		$sudopay_credential = $d_arr['sudopay_credential'];
		$this->sudopay_service = new \SudopayService();
		$this->PayCheckOutService = new \PayCheckOutService();
		$common_invoice_details = $common_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);
		$receivers_details = $this->PayCheckOutService->addCreditReceiversDetails($common_invoice_id, $common_invoice_details);

		//Get required details
		$buyer_details = \CUtil::getUserDetails($logged_user_id);

		$sudopay_fees_payer = 'site';//site(merchant) or buyer
		$payment_method = 'simple';

		$sudopay_data = array();
		if(isset($d_arr)) {
			Log::info('Input has d_arr[0]');
			$sudopay_data = $d_arr;
			if(isset($sudopay_data['sudopay_fees_payer'])) {
				$sudopay_fees_payer = $sudopay_data['sudopay_fees_payer'];
			}
		}
		//Sudo pay
		$amount = 0.00;
		$paypal_amount = 0.00;
		$reciver_amount = 0.00; //Seller amount
		$merchant_amount = 0.00;

		foreach($receivers_details as $receiver) {
			$amount = \CUtil::formatAmount($receiver['amount']);
			if($receiver['is_admin'] == "Yes")	{
				$merchant_amount = $amount;
			}
			else {
				$reciver_amount = $reciver_amount + $amount;
			}
			$paypal_amount = $paypal_amount + $amount;
		}

		$pay_data = array(	'merchant_id' => $sudopay_credential['merchant_id'],
						    'website_id' => $sudopay_credential['website_id'],
						    'amount' => $paypal_amount,
						    'currency_code' => $common_invoice_details['currency'],
						    'item_name' => 'Simple payment',
						    'item_description' => 'Simple description',
						    'x-common_invoice_id' => $common_invoice_id,
						    'x-buyer_id' => $logged_user_id,
						    'x-order_id' => 0,
						    'x-reference_type' => $reference_type,
						    'x-is_credit' => $is_credit,
						    'notify_url' => URL::to('sudopay/sudopay-payment-notify-url'),
						    'success_url' => URL::action("PayCheckOutController@paymentSuccess"),
						    'cancel_url' => URL::action("PayCheckOutController@paymentcancel")		);

		if($payment_method == 'simple') {
			$pay_data['action'] = 'capture';

			//if($sudopay_fees_payer != 'site')
				//$pay_data['fees_payer'] = $sudopay_fees_payer;
		}
		else {
			//Common $_marketplace_fields_arr for make markatplace payment it will be override on submit payment
			// basic fields for marketplace related actions...
			$pay_data = array_merge($pay_data, array(
				'marketplace_receiver_id' => $receiver_id,//$receiver_id,
			    'marketplace_receiver_amount' => $reciver_amount,
			    'marketplace_fixed_merchant_amount' => $merchant_amount,

			    'action' => 'marketplace-capture',
			));

			//if($sudopay_fees_payer != 'site')
				//$pay_data['marketplace_fees_payer'] = $sudopay_fees_payer; //Should check gateway support fees_payer (sudopay now support paypal only)
		}

		$pay_data['buyer_email'] = $buyer_details['email'];
		$pay_data['buyer_phone'] = isset($sudopay_data['buyer_phone_no']) ? $sudopay_data['buyer_phone_no'] : '';
		$buyer_address_line1 = isset($sudopay_data['buyer_address_line1']) ? $sudopay_data['buyer_address_line1'] : '';
		$buyer_address_line2 = isset($sudopay_data['buyer_address_line2']) ? $sudopay_data['buyer_address_line2'] : '';
		$buyer_street = isset($sudopay_data['buyer_street']) ? $sudopay_data['buyer_street'] : '';
		$pay_data['buyer_address'] = $buyer_address_line1.' '.$buyer_address_line2.' '.$buyer_street;
		$pay_data['buyer_city'] = isset($sudopay_data['buyer_city']) ? $sudopay_data['buyer_city'] : '';
		$pay_data['buyer_state'] = isset($sudopay_data['buyer_state']) ? $sudopay_data['buyer_state'] : '';
		$pay_data['buyer_country'] = isset($sudopay_data['buyer_country_iso']) ? $sudopay_data['buyer_country_iso'] : '';
		$pay_data['buyer_zip_code'] = isset($sudopay_data['buyer_zip_code']) ? $sudopay_data['buyer_zip_code'] : '';
		$pay_data['buyer_ip'] = $_SERVER['REMOTE_ADDR'];

//		Log::info('I am pre variable');
//		Log::info(print_r($pay_data, 1));
		//$pay_data['gateway_id'] = 1;
        return $pay_data;
 	}

 	//Transfered functions
 	public function updateCreditsLogStatus($status = 'Paid', $common_invoice_details)
	{
		$reference_type = $common_invoice_details['reference_type'];
		if($reference_type == 'Credits' || $reference_type == 'Usercredits') {
			$credit_id = $common_invoice_details['reference_id'];
			$data['paid'] = 'Yes';
			$data['date_paid'] = DB::raw('NOW()');
			$this->manage_credits_obj->updateCreditsLogDetails($credit_id, $data);
		}
	}

	public function updateCommonInvoiceStatus($input_data = array(), $invoice_id, $total_amount)
	{
		$common_invoice_id = $invoice_id;
		$data['is_credit_payment'] = isset($input_data['is_credit_payment']) ? $input_data['is_credit_payment'] : 'No';
		$data['paypal_amount'] = isset($input_data['paypal_amount']) ? $input_data['paypal_amount'] : $total_amount;
		$data['status'] = 'Paid';
		$data['date_paid'] = DB::raw('NOW()');
		$this->common_invoice_obj->updateCommonInvoiceDetails($common_invoice_id, $data);
	}

	public function updateCommonInvoiceDetails($data)
	{
		$common_invoice_id = $this->common_invoice_details['common_invoice_id'];
		$this->common_invoice_obj->updateCommonInvoiceDetails($common_invoice_id, $data);
	}

	public function updateReceiversDetails($common_invoice_id, $resp){
		$d_arr['pay_key'] = $resp['paykey'];
		$d_arr['txn_id'] = $resp['id'];
		$d_arr['status'] = $resp['status'];
		$this->shop_order_obj->updateOrderReceiverData($common_invoice_id, $d_arr);
	}

	public function setWalletTransaction($wallet_details = array(), $transaction_key = 'purchase', $common_invoice_details, $order_details)
	{
		$details_arr = 	array ('date_added' => new DateTime,
								'user_id' => '',
								'transaction_type' => 'Debit',
								'amount' => '',
								'currency' => isset($order_details['currency']) ? $order_details['currency'] : $common_invoice_details['currency'],
								'transaction_key' => $transaction_key,
								'reference_content_id' => isset($order_details['id']) ? $order_details['id'] : $common_invoice_details['common_invoice_id'],
								'reference_content_table' => 'shop_order',
								'invoice_id' => $common_invoice_details['common_invoice_id'],
								'purchase_code' => '',
								'related_transaction_id' => '',
								'status' => isset($order_details['order_status']) ? $order_details['order_status'] : $common_invoice_details['status'],
								'transaction_notes' => '',
								'transaction_id' => '',
								'paypal_adaptive_transaction_id' => ''	);
		//$details_arr = array();
		if(!empty($wallet_details))
		{
			$updated_transactions = $wallet_details + $details_arr;
			//echo "<pre>";print_r($updated_transactions);echo "</pre>";exit;
			$SiteTransactionDetails = new SiteTransactionDetails();
				$site_transaction_id = $SiteTransactionDetails->addNew($updated_transactions);
			//echo "<pre>";print_r($updated_transactions);echo "</pre>";
		}
	}

	public function sendCreditsInvoiceMailToUser($common_invoice_details)
	{
		if(count($common_invoice_details) > 0) {
			//mail to user
			$details['user_name'] = CUtil::getUserFields($common_invoice_details['user_id'], 'first_name');
			$details['to_email'] = CUtil::getUserFields($common_invoice_details['user_id'], 'email');
			$details['invoices'] = $common_invoice_details;
			Log::info('Ready to send mail start');
			if($common_invoice_details['reference_type'] == 'Usercredits') {
				$details['subject'] = Lang::get('email.credit_added');
				$this->sendPurchaseMail('credit','emails.userCreditsNotificationToUser', $details);
			}
			else {
				$details['subject'] = Lang::get('email.invoice_paid');
				$this->sendPurchaseMail('credit','emails.creditsNotificationToUser', $details);
			}

			//mail to admin
			$details['to_email'] = Config::get("generalConfig.invoice_email");
			$details['invoices'] = $common_invoice_details;
			if($common_invoice_details['reference_type'] == 'Usercredits') {
				$details['subject'] = Lang::get('email.credits_added_by_user');
				$this->sendPurchaseMail('credit','emails.userCreditsNotificationToAdmin', $details);
			}
			else {
				$details['subject'] = Lang::get('email.invoice_paid_by_user');'';
				$this->sendPurchaseMail('credit','emails.creditsNotificationToAdmin', $details);
			}
		}
	}

	public function sendPurchaseMail($key, $view, $data)
	{
		$method   = 'send';
		$from_name = '';
		$from_email = '';
		if(!$from_email)
		{
			$from_email = Config::get("mail.from_email");
		}
		if(!$from_name)
		{
			$from_name = Config::get("mail.from_name");
		}
		//add an entry to the table
		$d_arr['from_name'] = $from_name;
		$d_arr['from_email'] = $from_email;
		$d_arr['to_email'] = $data['to_email'];
		$d_arr['subject'] = Config::get('generalConfig.site_name')." - ".$data['subject'];
		$d_arr['content'] = $view;
		$d_arr['key_type'] = $key;
		$d_arr['method'] = $method;
		$d_arr['status'] = 'pending';
		$d_arr['data'] = serialize($data);
		$d_arr['date_added']= new DateTime;
		/*$mailer = new MailSystemAlert;
		$arr = $mailer->filterTableFields($d_arr);
		$id = $mailer->insertGetId($arr);*/

		$data1['to_email'] = $d_arr['to_email'];
		$data1['from_name'] = $d_arr['from_name'];
		$data1['from_email'] = $d_arr['from_email'];
		$data1['subject'] = $d_arr['subject'];
		if($method == 'send')
		{
			try {
				Mail::send($view, $data,  function($message) use ($data1)
				{
					$to_arr = explode(',',  $data1['to_email']);
					foreach($to_arr as $to)
					{
						if($to != '')
							$message->to($to);
					}
					$message->from($data1['from_email'], $data1['from_name']);
					$message->subject($data1['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}

	public function sendInvoiceMailToUser($common_invoice_details, $ipn_response)
	{
		if(count($common_invoice_details) > 0)
		{
			if($common_invoice_details['reference_type'] == 'Products')
			{
				$order_obj = Webshoporder::initialize();
				$order_details = $order_obj->getOderDetailsForInvoiceMail($common_invoice_details['reference_id']);
				if(count($order_details) > 0)
				{
					$details['order_details'] = $order_details;
					$details['seller_details'] = CUtil::getUserDetails($order_details->seller_id);
				}
				$details['buyer_details'] = CUtil::getUserDetails($order_details['buyer_id']);

				if($common_invoice_details['tracking_id']!='')
				{
					$gateway_name = $ipn_response['gateway_name'];

					$payment_gateway = 'paypal';
					$payment_gateway_text= $gateway_name;
					if($common_invoice_details['is_credit_payment']=='Yes')
					{
						$payment_gateway= 'paypal_with_wallet';
						$payment_gateway_text= $gateway_name.' With Wallet';
						$common_invoice_details['wallet_credit_used'] = $common_invoice_details['amount'] - $common_invoice_details['paypal_amount'];
					}
				}
				elseif($common_invoice_details['is_credit_payment']=='Yes')
				{
					$payment_gateway= 'wallet';
					$payment_gateway_text= 'Wallet';
				}
				else
				{
					$payment_gateway= 'Free';
					$payment_gateway_text= 'Free';
				}
				$common_invoice_details['payment_gateway'] = $payment_gateway;
				$common_invoice_details['payment_gateway_text'] = $payment_gateway_text;
				$details['invoices'] = $common_invoice_details;

				//buyer
				$details['to_email'] = $details['buyer_details']['email'];
				$details['subject'] = Lang::get('email.new_order_placed');
				//echo "<pre>";print_r($details);echo "</pre>";exit;
				$this->sendPurchaseMail('sale','emails.orderNotificationToBuyer', $details);

				//admin
				$details['to_email'] = Config::get("generalConfig.invoice_email");
				$details['subject'] = Lang::get('email.new_order_placed');
				//echo "<pre>";print_r($details);echo "</pre>";exit;
				$this->sendPurchaseMail('sale','emails.orderNotificationToAdmin', $details);

				//seller
				$details['to_email'] = $details['seller_details']['email'];
				$details['subject'] = Lang::get('email.new_order_placed');
				//echo "<pre>";print_r($details);echo "</pre>";exit;
				$this->sendPurchaseMail('sale','emails.orderNotificationToSeller', $details);
			}
		}
	}

	public function generateInvoiceForOrder($order_id){

		$resp = array();
		$order_item_details = $this->shop_order_obj->getOrderItemDetailsForOrder($order_id);
		//echo "<pre>"; print_r($order_item_details); echo "</pre>";exit;
		if(!empty($order_item_details))
		{
			$deal_allowed = $variation_allowed = 0;
			$primary_amount = $secondary_amount = 0;
			if(CUtil::chkIsAllowedModule('deals'))
			{
				$deal_service = new DealsService();
				$deal_allowed = 1;
			}
			if(CUtil::chkIsAllowedModule('variations'))
			{
				$variation_service = new VariationsService();
				$variation_allowed = 1;
			}

			foreach($order_item_details as $order_item_det)
			{
				$is_already_added = $this->invoice_obj->checkInvoiceGeneratedForOrderItem($order_item_det['order_id'], $order_item_det['item_id']);
				if(!$is_already_added)
				{
					$this->invoice_obj->setOrderId($order_item_det['order_id']);
					$this->invoice_obj->setBuyerId($order_item_det['buyer_id']);
					$this->invoice_obj->setItemId($order_item_det['item_id']);
					$this->invoice_obj->setItemOwnerId($order_item_det['item_owner_id']);
					$this->invoice_obj->setOrderItemId($order_item_det['id']);
					$this->invoice_obj->setOrderReceiverId($order_item_det['order_receiver_id']);
					$this->invoice_obj->setInvoiceStatus((strtolower($order_item_det['order_status'])=="payment_completed")?'completed':'pending');
					$this->invoice_obj->setTransactionId($order_item_det['txn_id']);
					$this->invoice_obj->setPayKey($order_item_det['pay_key']);
					$details = $this->invoice_obj->add();
					$json_data = json_decode($details, true);
					if($json_data['status'] == 'success')
					{
						$invoice_id = $json_data['invoice_id'];
						$item_id = $order_item_det['item_id'];
						$item_qty = $order_item_det['item_qty'];

						$this->updateProductSold($item_id, $item_qty);

						/** Variation related block start ***/
						$matrix_id = isset($order_item_det['matrix_id']) ? $order_item_det['matrix_id'] : 0;
						if($variation_allowed && isset($variation_service) && $matrix_id > 0)
						{
							$variation_service->updateProductVariationSold($item_id, $item_qty, $matrix_id);
						}
						/** Variation related block end ***/

						/** Deal related block start ***/
						// Update deal tipping status, deal item purchased details
						if($deal_allowed && isset($deal_service) && isset($order_item_det['deal_id']) && $order_item_det['deal_id'] > 0)
						{
							$data_arr = array();
							$data_arr['deal_id'] = $order_item_det['deal_id'];
							$data_arr['item_id'] = $order_item_det['item_id'];
							$data_arr['item_qty'] = $order_item_det['item_qty'];
							$data_arr['order_id'] = $order_item_det['order_id'];
							// Add deal item purchased entry
							$deal_service->addDealItemPurchasedEntry($data_arr);
							$tipping_limit = $deal_service->isTippingLimitExist($order_item_det['deal_id']);

							if($tipping_limit > 0 && !$deal_service->chkIsDealTipped($order_item_det['deal_id']))
							{
								// Admin alone receive fund
								$primary_amount += $order_item_det['site_commission'] + $order_item_det['seller_amount'] - $order_item_det['discount_ratio_amount'];
								$secondary_amount += 0;
							}
							else
							{
								$primary_amount += $order_item_det['site_commission'];
								$secondary_amount += $order_item_det['seller_amount'] - $order_item_det['discount_ratio_amount'];
							}
							// Update deal tipping status if applicable$order_item_det['deal_tipping_status'] == 'pending_tipping'$order_item_det['deal_tipping_status'] == 'pending_tipping'
							$deal_service->updateDealTippingStatus($data_arr);
						}
						else
						{
							$primary_amount += $order_item_det['site_commission'];
							$secondary_amount += $order_item_det['seller_amount'] - $order_item_det['discount_ratio_amount'];
						}
						/** Deal related block end ***/
					}
				}
			}
			$resp['primary'] = $primary_amount;
			$resp['secondary'] = $secondary_amount;

			return $resp;
			//Log::info('\ninvoice_id===>'.$invoice_id);
			//Update common invoice
			/*$default_curreny = Config::get('generalConfig.site_default_currency');
			$this->common_invoice_obj->setUserId($order_details['buyer_id']);
			$this->common_invoice_obj->setReferenceType('Products');
			$this->common_invoice_obj->setReferenceId($order_id);
			$this->common_invoice_obj->setCurrency($default_curreny);
			$this->common_invoice_obj->setAmount($order_details['total_amount']);
			$this->common_invoice_obj->setStatus('Paid');
			$this->common_invoice_obj->addCommonInvoice();*/
		}
	}

	public function updateProductSold($product_id, $item_qty){
		$this->product_obj->updateProductSold($product_id, $item_qty);
	}

	public function getInvoicesForOrder($order_id){
		$invoices_details = $this->invoice_obj->getInvoicesForOrder($order_id);
		return $invoices_details;
	}

	public function getOrderReceiversForOrder($common_invoice_id = '', $pay_key = '')
	{
		$receivers = $this->shop_order_obj->getOrderReceiversForOrder($common_invoice_id, $pay_key);
		return $receivers;
	}

	public function sendInsufficientBalanceMailToAdmin($order_receiver_details, $common_invoice_details)
	{
		$details['to_email'] = \Config::get("generalConfig.invoice_email");
		$details['subject'] = \Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('debit','emails.purchaseInsufficientNotificationToAdmin', $details);
	}

	public function sendInsufficientBalanceMailToSeller($order_receiver_details, $common_invoice_details)
	{
		$details['to_email'] = $order_receiver_details['seller_details']['email'];
		$details['subject'] = \Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('debit','emails.purchaseInsufficientNotificationToSeller', $details);
	}

	public function sendInsufficientBalanceMailToBuyer($order_receiver_details, $common_invoice_details)
	{
		$details['to_email'] = $order_receiver_details['buyer_details']['email'];
		$details['subject'] = \Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('credit','emails.purchaseInsufficientNotificationToBuyer', $details);
	}

	public function setSiteTransactions($payment_method = 'Capture', $order_details, $ipn_response, $invResp = array())
	{
		//echo "here"; exit;
		//$payment_method = $this->getPaymentMethod();

		//$order_receivers = $this->getOrderReceiversForOrder($common_invoice_id);

		//$site_transaction_id = $seller_transaction_id =  '';
		/*foreach($order_receivers as $receiver)
		{
			if($receiver['is_admin'] == 'Yes')
				$site_transaction_id = $receiver['txn_id'];
			else
				$seller_transaction_id = $receiver['txn_id'];
		}*/
		//$this->site_transaction_id = $site_transaction_id;
		//$this->seller_transaction_id = $seller_transaction_id;
		$sudopay_fees_payer = $this->sudopay_fees_payer;
		if($payment_method == 'Capture')
		{
			//$this->addSiteTransactionDetails('BuyerCredit');
			$this->addSiteTransactionDetails('BuyerDebit', $order_details, $ipn_response, $invResp);
			if($sudopay_fees_payer == 'Buyer') {
				$this->addSiteTransactionDetails('GatewayFee', $order_details, $ipn_response, $invResp);
			}
			$this->addSiteTransactionDetails('SiteCreditFromBuyer', $order_details, $ipn_response, $invResp);
			if(!isset($invResp['secondary']) || (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0)) {
				$this->addSiteTransactionDetails('SiteDebitSellerAmount', $order_details, $ipn_response, $invResp);
				$this->addSiteTransactionDetails('SellerCreditFromSite', $order_details, $ipn_response, $invResp);
			}
		}
		else
		{
			//$this->addSiteTransactionDetails('BuyerCredit', $order_details, $ipn_response, $invResp);
			$this->addSiteTransactionDetails('BuyerDebit', $order_details, $ipn_response, $invResp);
			if($sudopay_fees_payer == 'Buyer') {
				$this->addSiteTransactionDetails('GatewayFee', $order_details, $ipn_response, $invResp);
			}
			$this->addSiteTransactionDetails('ParallelSellerCreditFromBuyer', $order_details, $ipn_response, $invResp);
			$this->addSiteTransactionDetails('ParallelSiteCreditFromBuyer', $order_details, $ipn_response, $invResp);
		}
//		if(isset($this->order_invoices) && !empty($this->order_invoices))
//		{
//			foreach($this->order_invoices as $invoice)
//			{
//				$this->invoice_detail = $invoice;
//				$this->addSiteTransactionDetails('PurchaseItem');
//				$this->addSiteTransactionDetails('ParallelAuthorPayment');
//				$this->addSiteTransactionDetails('ParallelBuyerSitePayment');
//				$this->addSiteTransactionDetails('SiteCommission');
//			}
//		}
	}

	public function addSiteTransactionDetails($transaction_key = '', $order_details, $ipn_response, $invResp = array())
	{
		$table_fields_arr = array (	'date_added' => date('Y-m-d H:i:s'),
									'user_id' => '',
									'transaction_type' => 'Debit',
									'amount' => '',
									'currency' => '',
									'transaction_key' => 'purchase',
									'reference_content_id' => '',
									'reference_content_table' => '',
									'invoice_id' => '',
									'purchase_code' => '',
									'related_transaction_id' => '',
									'status' => '',
									'transaction_notes' => '',
									'transaction_id' => '',
									'paypal_adaptive_transaction_id' => '',
									'payment_type' => 'paypal'		);

		$purchaseDetails = $this->getItemTransactionDetail($transaction_key, $order_details, $ipn_response, $invResp);

		if($purchaseDetails['amount'] > 0)
		{
			foreach ($table_fields_arr as $key => $value)
			{
				$details_arr[$key] = isset($purchaseDetails[$key]) ? $purchaseDetails[$key] : $value;
			}
			$details_arr['purchase_code'] = ($details_arr['purchase_code'] == '')?$details_arr['invoice_id']:$details_arr['purchase_code'];

			$SiteTransactionDetails = new SiteTransactionDetails();
			$site_transaction_id = $SiteTransactionDetails->addNew($details_arr);
			return  $site_transaction_id;
		}
	}

	private function getItemTransactionDetail($transaction_key, $order_details, $ipn_response, $invResp = array())
	{
//		Log::info('transaction_key ==>'.$transaction_key);
//		Log::info('payment_amount_details ==>'.print_r($this->payment_amount_details, 1));
		$data = array();

		//exit;
		$data['currency'] 					= $order_details['currency'];
		$data['reference_content_id'] 		= $order_details['id'];
		$data['invoice_id'] 				= $order_details['id'];
		//$data['item_id'] 					= $this->invoice_detail['item_id'];
		$data['reference_content_table']	= 'shop_order';
		$data['status'] 					= $order_details['order_status'];

		//echo "<pre>";print_r($order_details);echo "</pre>";exit;
		$total_item_amount	= $this->payment_amount_details['total_amount'];

		//This is to include the wallet amount (if purchased using both wallet and paypal) while debit from buyer
		if(!isset($this->wallet_transaction_details['buyer']['amount']))
			$this->wallet_transaction_details['buyer']['amount'] = 0;
		$buyer_debit_amount = $total_item_amount + $this->wallet_transaction_details['buyer']['amount'];

		$sudopay_fees_payer = $this->sudopay_fees_payer;
		$gateway_fee = 0;
		if($sudopay_fees_payer == 'Buyer') {
			if(isset($ipn_response['x-common_invoice_id']) && $ipn_response['x-common_invoice_id'] != '')
				$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($ipn_response['x-common_invoice_id']);
//			\Log::info('common_invoice_details ==>'.print_r($common_invoice_details));
			if(isset($common_invoice_details) && count($common_invoice_details) > 0) {
//				\Log::info('I am here man');
				$gateway_fee = $common_invoice_details['paypal_amount'] - $common_invoice_details['amount'];
			}
//			\Log::info('gateway_fee ==>'.$gateway_fee);
			$buyer_debit_amount = $buyer_debit_amount - $gateway_fee;
		}

		$receiver_amount	= $this->payment_amount_details['seller_amount'];
		$site_commission	= $this->payment_amount_details['site_commission'];
		$credit_to_admin	= $this->payment_amount_details['credit_to_admin'];
		$credit_to_seller	= $this->payment_amount_details['credit_to_seller'];


		if(isset($invResp['primary']) && $invResp['primary'] != '')
		{
//			\Log::info('I am coming here for non deal tooooooooooooooooo.');
			$site_commission = $invResp['primary'];
			$receiver_amount = (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0) ? $invResp['secondary'] : 0;

			$total_amount = $total_item_amount;
			$seller_amount = $receiver_amount;
			$credit_to_admin = (isset($ipn_response['action']) && $ipn_response['action'] == 'Capture') ? $total_item_amount : $site_commission;
			$credit_to_seller = $receiver_amount;

			$this->payment_amount_details = compact('total_amount', 'seller_amount', 'site_commission', 'credit_to_admin', 'credit_to_seller');
		}

		//$receiver_amount	= $total_item_amount - $site_commission;
		//echo "dsds<pre>";print_r($this->paypal_adaptive_transaction_details);echo "</pre>";exit;
		$payment_type = 'purchase';
		$site_payment_type = 'purchase_fee';
		switch ($transaction_key)  {

			case 'BuyerCredit':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Amount credited to wallet account for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $total_item_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $order_details['buyer_id'];
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
				break;

			case 'BuyerDebit':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Debited amount from '.$ipn_response['gateway_name'].' account for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $buyer_debit_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $order_details['buyer_id'];
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					$data['payment_type']		    = $ipn_response['gateway_name'];
				break;

			case 'SiteCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $site_payment_type;
					$data['transaction_notes'] 		= 'Credited amount to '.$ipn_response['gateway_name'].' account from buyer for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_admin;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					$data['payment_type']		    = $ipn_response['gateway_name'];
				break;

			case 'SiteDebitSellerAmount':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $site_payment_type;
					//$data['transaction_notes'] 	= 'Debited amount from your '.$ipn_response['gateway_name'].' account to transfer seller amount except site commission for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['transaction_notes'] 		= 'Debited amount from site account to transfer seller amount except site commission for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['paypal_adaptive_transaction_id']	= '';
					if(isset($ipn_response['action']) && $ipn_response['action'] == 'Capture')
						$data['payment_type']	= 'wallet';
				break;

			case 'SellerCreditFromSite':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					//$data['transaction_notes'] 	= 'Credited amount to your '.$ipn_response['gateway_name'].' account for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['transaction_notes'] 		= 'Credited amount to your wallet account for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $order_details['seller_id'];
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					if(isset($ipn_response['action']) && $ipn_response['action'] == 'Capture') {
						$data['payment_type']	= 'wallet';
						$data['transaction_id']	= '';
					}
				break;

			case 'ParallelSellerCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Credited amount to your '.$ipn_response['gateway_name'].' acccount for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $order_details['seller_id'];
					$data['transaction_id'] 		= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					$data['payment_type']		    = $ipn_response['gateway_name'];
				break;

			case 'ParallelSiteCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $site_payment_type;
					$data['transaction_notes'] 		= 'Credited site commission amount to '.$ipn_response['gateway_name'].' account for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_admin;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					$data['payment_type']		    = $ipn_response['gateway_name'];
				break;

			case 'GatewayFee':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= 'gateway_fee_purchase';
					$data['transaction_notes'] 		= 'Debited amount from VAR_USER as gateway fee for the order: '.CUtil::setOrderCode($order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $gateway_fee;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $order_details['buyer_id'];
					$data['transaction_id']			= $ipn_response['id'];
					$data['paypal_adaptive_transaction_id']	= '';
					$data['payment_type']		    = $ipn_response['gateway_name'];
				break;

			case 'PurchaseItem':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 			= 'Purchase Item Debit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $receiver_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->invoice_detail['buyer_id'];
				break;

			//in case of parrallel payment, for buyer there will be 2 debits
		     case 'ParallelBuyerSitePayment':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= 'SiteCommission';
					$data['transaction_notes'] 		= 'ParallelBuyerSitePayment- Debit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $site_commission;//$this->getPaymentInfo('receiver.amount', 'secondary');
					$data['user_id'] 				= $this->invoice_detail['buyer_id'];
				break;

			case 'ParallelAuthorPayment':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= 'AuthorPayment';
					$data['transaction_notes'] 		= 'ParallelAuthorPayment - Credit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $receiver_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->invoice_detail['item_owner_id'];
				break;

			case 'ChainedAuthorPayment':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= 'AuthorPayment';
					$data['transaction_notes'] 		= 'ChainedAuthorPayment - Credit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->invoice_detail['item_owner_id'];
				break;

			//in case of chained payment, for seller there will be 1 additional debit
			case 'ChainedAuthorSitePayment':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= 'SiteCommission';
					$data['transaction_notes'] 		= 'ChainedAuthorSitePayment-Debit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $this->invoice_detail['item_site_commission'];
					$data['user_id'] 				= $this->invoice_detail['item_owner_id'];
				break;

			case 'SiteCommission':
					$data['transaction_type']		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes']		= 'SiteCommission-Credit';
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $this->invoice_detail['item_site_commission'];
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
				break;

			//	Debit for seller
			case 'AuthorRefund':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= 'RefundPayment';
					$data['transaction_notes'] 		= 'AuthorRefund - Debit';
					$data['related_transaction_id']	= $this->getRelativeTransactionId($data['invoice_id'], 'AuthorPayment', $this->invoice_detail['item_owner_id']);
					$data['amount'] 				= $this->getPaymentInfo('refundedAmount', 'primary');
					$data['user_id'] 				= $this->invoice_detail['item_owner_id'];
					break;

			//	Credit for buyer
			case 'BuyerRefund':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= 'RefundPayment';
					$data['transaction_notes']      = 'BuyerRefund - Credit';
					$data['related_transaction_id']	=$this->getRelativeTransactionId($data['invoice_id'], 'PurchaseItem', $this->invoice_detail['buyer_id']);
					$data['amount'] 				= $this->getPaymentInfo('refundedAmount', 'primary');
					$data['user_id'] 				= $this->invoice_detail['buyer_id'];
					break;

			//in case of chained payment, site refund will be to the seller, Credit for seller
			case 'ChainedSiteRefund':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= 'RefundSiteFee';
					$data['transaction_notes'] 	    = 'ChainedSiteRefund - Credit';
					$data['related_transaction_id']	= $this->getRelativeTransactionId($data['invoice_id'], 'SiteCommission', $this->invoice_detail['item_owner_id']);
					$data['amount'] 				= $this->getPaymentInfo('refundedAmount', 'secondary');
					$data['user_id'] 				= $this->invoice_detail['item_owner_id'];
					break;

			//in case of parallel payment, site refund will be to the buyer,  Credit for buyer
			case 'ParallelSiteRefund':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= 'RefundSiteFee';
					$data['transaction_notes']  	= 'ParallelSiteRefund - Credit';
					$data['related_transaction_id']	= $this->getRelativeTransactionId($data['invoice_id'], 'SiteCommission', $this->invoice_detail['buyer_id']);
					$data['amount'] 				= $this->getPaymentInfo('refundedAmount', 'secondary');
					$data['user_id'] 				= $this->invoice_detail['buyer_id'];
					break;

			//in case of site refund for both , site refund will be debit to the admin
			case 'SiteCommissionRefund':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= 'RefundSiteFee';
					$data['transaction_notes']      = 'SiteCommissionRefund - Debit';
					$data['related_transaction_id']	= $this->getRelativeTransactionId($data['invoice_id'], 'SiteCommission', Config::get('generalConfig.admin_id'));
					$data['amount'] 				= $this->getPaymentInfo('refundedAmount', 'secondary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					break;
		}
		return $data;
	}

	public function setPaymentAmountDetails($order_details, $ipn_resp){
		\Log::info('setPaymentAmountDetails starts');
		\Log::info(print_r($ipn_resp, 1));
		\Log::info('setPaymentAmountDetails ends');
		\Log::info('setPaymentAmountDetails order_details starts');
		\Log::info(print_r($order_details, 1));
		\Log::info('setPaymentAmountDetails  order_details ends');
		//$payment_method = $this->getPaymentMethod();
		$payment_method = isset($ipn_resp['action']) ? $ipn_resp['action'] : 'Capture';
		Log::info('setPaymentAmountDetails function start');
		if($payment_method == 'Capture')
		{
			\Log::info('payment_methodC ===>'.$payment_method);
			$primary_amount = isset($ipn_resp['amount']) ? $ipn_resp['amount'] : 0;//$this->getPaymentInfo('receiver.amount', 'primary');
			$secondary_amount = 0;

			$total_amount = $primary_amount;
			$seller_amount = $primary_amount - $order_details['site_commission'];
			$site_commission = $order_details['site_commission'];//$primary_amount - $secondary_amount;
			$credit_to_admin = $primary_amount;
			$credit_to_seller = $primary_amount - $site_commission;
		}
		else
		{
			\Log::info('payment_methodE ===>'.$payment_method);
			$primary_amount = isset($ipn_resp['marketplace_fixed_merchant_amount']) ? $ipn_resp['marketplace_fixed_merchant_amount'] : 0;
			$secondary_amount = isset($ipn_resp['marketplace_receiver_amount']) ? $ipn_resp['marketplace_receiver_amount'] : 0;

			$total_amount = $primary_amount + $secondary_amount;
			$seller_amount = $secondary_amount;
			$site_commission = $primary_amount;
			$credit_to_admin = $primary_amount;
			$credit_to_seller = $secondary_amount;
		}

		$sudopay_fees_payer = $this->sudopay_fees_payer;
		if($sudopay_fees_payer == 'Buyer') {
			if(isset($ipn_response['x-common_invoice_id']) && $ipn_response['x-common_invoice_id'] != '')
				$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($ipn_response['x-common_invoice_id']);
			if(isset($common_invoice_details) && count($common_invoice_details) > 0) {
				$credit_to_admin = $common_invoice_details['paypal_amount'] - $common_invoice_details['amount'];
			}
		}
		$this->payment_amount_details = compact('total_amount', 'seller_amount', 'site_commission', 'credit_to_admin', 'credit_to_seller');
	}

	public function setSudopayFeesPayer($sudopay_fees_payer) {
		$this->sudopay_fees_payer = $sudopay_fees_payer;
	}

	public function updateOrderDiscountDetails($order_id, $site_commission, $seller_amount, $net_amount)
	{
		DB::table('shop_order')->whereRaw('id = ?', array($order_id))->update(array('site_commission' => $site_commission));
		DB::table('shop_order_item')->whereRaw('order_id = ?', array($order_id))->update(array('site_commission' => $site_commission, 'seller_amount' => $seller_amount, 'total_amount' => $net_amount));
	}
}