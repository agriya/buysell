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
class PayCheckOutController extends BaseController
{
	public $item_owner_id = 0;
	public $checkout_currency = "";
	function __construct()
	{
		parent::__construct();
        $this->PayCheckOutService = new PayCheckOutService();
		$this->invoiceService = new InvoiceService();
		$this->show_cart_service = new ShowCartService();
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

    public function payCheckOutItems($order_id)
    {
    	Session::forget('error_message');
    	$order_item_details = array();
    	$discount_amount = 0;
    	$discounted_amount = 0;
    	$details = array();
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	if(Session::has('success_free_message') && Session::get('success_free_message') != '')
    	{
    		$error_msg = "";
			return View::make('payCheckOut', compact('error_msg'));
		}
		$order_details = $this->PayCheckOutService->checkValidOrderId($order_id);
		$get_common_meta_values = Cutil::getCommonMetaValues('pay-check-out');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}

		if(count($order_details) > 0)
    	{
			$order_id = $order_details['id'];
    		if($this->PayCheckOutService->chkItemsAddedWithOrder($order_id))
    		{
    			//Get item details added in order
    			$order_item_details = $this->PayCheckOutService->setItemDetails($order_id, $order_details);
    			$amount = $this->PayCheckOutService->getDiscountAmount($order_details);
				$amt = explode('::', $amount);
				$discount_amount = $amt[0];
				$discounted_amount = $amt[1];

    			if($discounted_amount > 0)
    			{
					$details = $this->PayCheckOutService->getPaymentDetails($order_details['currency'], $discounted_amount);
					$payment_curr_options 	= $details['payment_curr_options'];
					$disp_amount 			= $details['disp_amount'];
					$currency_pay_note_msg 	= $details['currency_pay_note_msg'];
				}
				$PayCheckOutService = $this->PayCheckOutService;
				$error_msg = "";

				$shipping_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_id));

				$common_invoice_obj =  Products::initializeCommonInvoice();
				$common_invoice_details = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId('Products', $order_id);

				$user_account_balance = CUtil::getUserAccountBalance($logged_user_id);
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
						$d_arr['reference_type'] = 'Products';
						$d_arr['logged_user_id'] = $logged_user_id;
						$d_arr['sudopay_fields_arr'] = $this->sudopay_service->getSudopayFieldsArr($order_id, $order_details, $common_invoice_details, $d_arr);
					}
				}
				return View::make('payCheckOut', compact('PayCheckOutService', 'error_msg', 'order_item_details', 'order_details', 'discount_amount', 'discounted_amount', 'payment_curr_options', 'disp_amount', 'currency_pay_note_msg', 'shipping_details', 'common_invoice_details', 'user_account_balance', 'd_arr'));
			}
			else
			{
				//Set error message item not added for order
				$error_msg = trans('payCheckOut.paycheckout_invlaid_order_item');
				return View::make('payCheckOut', compact('error_msg', 'order_details'));
			}
		}
		else
		{
			//invalid order id
			$error_msg = trans('payCheckOut.paycheckout_invlaid_order');
			return View::make('payCheckOut', compact('error_msg', 'order_details'));
		}
	}

	public function payCheckOutItemsAct() {
		//Common $_marketplace_fields_arr for make markatplace payment it will be override on submit payment
		// basic fields for marketplace related actions...
		$_marketplace_fields_arr = array(
		    //'merchant_id' => $this->sudopay_credential['merchant_id'],
		    //'website_id' => $this->sudopay_credential['website_id'],
		    'amount' => 2,
		    'currency_code' => 'USD',
		    'item_name' => 'SudoPay demo',
		    'item_description' => '$3 will be captured on confirmation',
		    'notify_url' => URL::to('sudopay/sudopay-payment-notify-url'),
		    //'x-product_id' => '123456',
		    //'z-category' => 'Demo',
		    'marketplace_receiver_id' => '19899',
		    'marketplace_receiver_amount' => 1,
		    'marketplace_fixed_merchant_amount' => 1,
		    'success_url' => URL::action("PayCheckOutController@paymentSuccess"),
		    'cancel_url' => URL::action("PayCheckOutController@paymentcancel"),
		);

		// fields for marketplace-auth action with Merchant fees payer...
		/*$marketplace_auth_merchant_fields_arr = array_merge($_marketplace_fields_arr, array(
		    'action' => 'marketplace-auth',
		    'marketplace_fees_payer' => 'merchant', //Should check gateway support fees_payer (sudopay now support paypal only)

		));*/

		// fields for marketplace-capture action with Merchant fees payer...
		$marketplace_capture_merchant_fields_arr = array_merge($_marketplace_fields_arr, array(
		    'action' => 'marketplace-capture',
		    'marketplace_fees_payer' => 'receiver'
		    //Should check gateway support fees_payer (sudopay now support paypal only)
		));

		Log::info('checkOut post start ===========================>');
		Log::info(print_r(Input::All(), 1));
		Log::info('checkOut post end ===========================>');
		if (Input::has('gateway_id') && Input::get('gateway_id')) {
			$input = Input::All();

		    // to set gatway id for transparent payment
		    $action = $input['action'];
		    unset($input['action']); // should remove action from post array(action set in API URL)
		    unset($input['submit']);
			$input['buyer_ip'] = $_SERVER['REMOTE_ADDR'];
		    //Merge posted buyer details
		    if ($action == 'auth') {
		        unset($simple_auth_merchant_fields_arr['action']);
		        $simple_auth_merchant_fields_arr = array_merge($simple_auth_merchant_fields_arr, $_POST);
		        $this->sc->makeAuthPayment($simple_auth_merchant_fields_arr);
		    } else if ($action == 'capture') {
		    	Log::info('I am a capture');
		        unset($simple_capture_merchant_fields_arr['action']);
		        $simple_capture_merchant_fields_arr = array_merge($simple_capture_merchant_fields_arr, $_POST);
		        $this->sc->makeCapturePayment($simple_capture_merchant_fields_arr);
		    } else if ($action == 'marketplace-auth') {
		    	Log::info('I ma here');
		        unset($marketplace_auth_merchant_fields_arr['action']);
		        $marketplace_auth_merchant_fields_arr = array_merge($marketplace_auth_merchant_fields_arr, $input);
		        $this->sc->makeMarketplaceAuthPayment($marketplace_auth_merchant_fields_arr);
		    } else if ($action == 'marketplace-capture') {
		    	Log::info('post action marketplace-capture');
		        unset($marketplace_capture_merchant_fields_arr['action']);
		        $marketplace_capture_merchant_fields_arr = array_merge($marketplace_capture_merchant_fields_arr, $_POST);
		        $this->sc->makeMarketplaceCapturePayment($marketplace_capture_merchant_fields_arr);
		    }
		}
	}

	public function payCheckOutFreeItems($order_id)
    {
    	$error_exists = false;
		$error_msg = '';
		$common_invoice_id = Input::get('common_invoice_id');
		$payment_gateway_chosen = Input::get('payment_gateway_chosen');
		$is_credit = Input::has('is_credit') ? Input::get('is_credit') : ''; //Pay amount using credits and remaning amount by paypal
		$common_invoice_details = $this->PayCheckOutService->checkValidCommonInvoiceId($common_invoice_id);
		$logged_user_id = BasicCUtil::getLoggedUserId();
		//Log::info(print_r($common_invoice_details,1));
		$order_id = $credit_id = 0;
		if(count($common_invoice_details) > 0) {
			//echo '<pre>';print_r($common_invoice_details);echo '</pre>';
			$reference_type = $common_invoice_details['reference_type'];
			$reference_id = $common_invoice_details['reference_id'];
			if($reference_type == 'Products') {
				$order_id = $reference_id;
				$order_details = $this->PayCheckOutService->checkValidOrderId($order_id);
				if(count($order_details) > 0)
    			{
    				$order_id = $order_details['id'];
		    		if($this->PayCheckOutService->chkItemsAddedWithOrder($order_id))
		    		{
						//Get item details added in order
    					$order_item_details = $this->PayCheckOutService->setItemDetails($order_id, $order_details['is_custom_order']);
    				}
		    		else
					{
						//item not added for order
						$error_exists = true;
						Session::set('error_message',trans('common.some_problem_try_later'));
						$error_msg = 'item_not_added';
					}
    			}
    			else
				{
					//Invalid order
					$error_exists = true;
					Session::set('error_message',trans('common.some_problem_try_later'));
					$error_msg = 'invalid order';
				}
			}
			else if($reference_type == 'Credits') {
				$credit_id = $reference_id;
				$credit_details = $this->PayCheckOutService->checkValidCreditId($credit_id);
				if(empty($credit_details)){
				    //Invalid credit
					$error_exists = true;
					Session::set('error_message',trans('common.some_problem_try_later'));
					$error_msg = 'invalid credit';
				}
			}
			//echo "<br>error_exists: ".$error_exists;
			//If error exist
			if($error_exists) {
				return Redirect::to('invoice?status=Unpaid')->with('error_message', $error_msg);
				//$redirect_url = URL::to();	echo $redirect_url."|~~|".$error_exists;exit;
			}

			if(!$error_exists) {
				$receivers_details = $this->PayCheckOutService->addOrderReceiversDetails($common_invoice_id, $order_id, 'dummy');

				if($receivers_details) {
					//get the binding
					$params['payment_method'] = $payment_gateway_chosen;
					$this->biller = App::make('PaymentInterface', $params);

					if(true)
					{ // If wallet payment mode selected
						//Log::info('reference_type ====>'.$reference_type);
						if($reference_type == 'Products') {
							//Set object
							$this->biller->setObject();
							//Initialize
							$initialize_data = array();
							$initialize_data['order_id']  = $order_id;
							$this->biller->initialize($initialize_data);
							$this->biller->validate($order_id);

							$order_det['payment_gateway_type'] = 'Dummy';

							# Update the pay_key and tracking_id into invoice table
							$this->PayCheckOutService->updatePaymentOrderDetails($order_id, $order_det);

							//Empty cart
							$this->show_cart_service->emptyCart($order_details->seller_id);

							//Empty user shipping cookie
							$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();

							$credit_obj = Credits::initialize();

							$update_arr = array('date_paid' => DB::Raw('now()'), 'status' => 'Paid');
							$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, $update_arr);

							//Add amount to user wallet
							/*foreach($receivers_details as $receiver)
							{
								$credit_obj->setUserId($logged_user_id);
								$credit_obj->setCurrency($receiver['currency']);
								$credit_obj->setAmount($receiver['amount']);
								$credit_obj->creditAndDebit('amount', 'minus');

								if($receiver['is_admin'] != "Yes") {
									$credit_obj->setUserId($receiver['seller_id']);
									$credit_obj->setCurrency($receiver['currency']);
									$credit_obj->setAmount($receiver['amount']);
									$credit_obj->credit();
								}
							}*/

							//Log::info('reference_type ====>'.$reference_type);

							Session::set('success_message',trans('payCheckOut.payment_success'));
							//$redirect_url =  Url::to('pay-checkout/'.$order_id);
							$return =  Redirect::action("PayCheckOutController@paymentSuccess", array('is_free'=>'yes'));//$redirect_url
							//$return = Response::make($redirect_url);
							//echo $redirect_url."|~~|".$error_exists;
							if(isset($removed_cookies['ship_cookie']))
								$return = $return->withCookie($removed_cookies['ship_cookie']);
							if(isset($removed_cookies['bill_cookie']))
								$return = $return->withCookie($removed_cookies['bill_cookie']);
							return $return;
							//exit;
						}
					}
				}
			}
		}
		else
		{
			return Redirect::action("PayCheckOutController@paymentcancel")->with('error_message', trans('common.some_problem_try_later'));
		}


		/*
    	$error_exists = false;
		$payment_gateway_chosen = 'dummy';
		$invoice_amount = 0;
		$order_details = $this->PayCheckOutService->checkValidOrderId($order_id);


		if(count($order_details) > 0)
    	{
    		$order_id = $order_details['id'];

    		if($this->PayCheckOutService->chkItemsAddedWithOrder($order_id))
    		{

				//Get item details added in order
    			$order_item_details = $this->PayCheckOutService->setItemDetails($order_id, $order_details['is_custom_order']);

    			$arr['payment_gateway_chosen'] = $payment_gateway_chosen;

				$receivers_details = $this->PayCheckOutService->addOrderReceiversDetails($order_id, $payment_gateway_chosen);

				//echo "<pre>";print_r($order_item_details);echo "</pre>";
				//echo "<pre>";print_r($receivers_details);echo "</pre>";
				//exit;

				//if every receiver have the paypal id and the amount
				if($receivers_details)
				{
					$params['payment_method'] = $payment_gateway_chosen;
					$this->biller = App::make('PaymentInterface', $params);


					$this->biller->setObject();
					//Initialize
					$initialize_data = array();
					$initialize_data['order_id']  = $order_id;
					$this->biller->initialize($initialize_data);
					$this->biller->validate();
					$this->show_cart_service->emptyCart();
					//Empty user shipping cookie
					$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();

					//Empty user shipping cookie
					$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();

					$credit_obj = Credits::initialize();

					foreach($receivers_details as $receiver)
					{
						if($receiver['is_admin'] != "Yes") {
							$credit_obj->setUserId($receiver['seller_id']);
							$credit_obj->setCurrency($receiver['currency']);
							$credit_obj->setAmount($receiver['amount']);
							$credit_obj->credit();
						}
					}
					$success_message = 'Payment success';
					Session::set('success_message','Payment success');
					$redirect = Redirect::action("PayCheckOutController@paymentSuccess")->with('success_free_message', $success_message);
					if(isset($removed_cookies['ship_cookie']))
						$redirect = $redirect->withCookie($removed_cookies['ship_cookie']);
					if(isset($removed_cookies['bill_cookie']))
						$redirect = $redirect->withCookie($removed_cookies['bill_cookie']);
					return $redirect;
				}
				else
				{
					Session::set('error_message','There are some problem in processing checkout.');
					return Redirect::action("PayCheckOutController@paymentcancel")->with('error_message', 'There are some problem in processing checkout.');
				}
			}
			else
			{
				//Set error message item not added for order
				$error_exists = true;
				$redirect_url = URL('/');
				return Redirect::action("PayCheckOutController@paymentcancel")->with('error_message', 'There are some problem in processing checkout.');


			}
		}
		else
		{
			//Invalid order
			$error_exists = true;
			$redirect_url = URL('/');
			return Redirect::action("PayCheckOutController@paymentcancel")->with('error_message', 'There are some problem in processing checkout.');
		}
		*/
	}


	public function generateInvoice($common_invoice_id = '', $currency_code = '', $payment_gateway_chosen = '')
	{
		$error_exists = false;
		$error_msg = '';
		if($common_invoice_id == '')
			$common_invoice_id = Input::get('common_invoice_id');
		if($currency_code == '')
			$currency_code = Input::get('currency_code');
		if($payment_gateway_chosen == '')
			$payment_gateway_chosen = Input::get('payment_gateway_chosen');
		$is_credit = Input::has('is_credit') ? Input::get('is_credit') : ''; //Pay amount using credits and remaning amount by paypal
		$common_invoice_details = $this->PayCheckOutService->checkValidCommonInvoiceId($common_invoice_id);
		$logged_user_id = BasicCUtil::getLoggedUserId();
		//Log::info(print_r($common_invoice_details,1));
		$order_id = $credit_id = 0;
		if(count($common_invoice_details) > 0) {

			$reference_type = $common_invoice_details['reference_type'];
			$reference_id = $common_invoice_details['reference_id'];
			if($reference_type == 'Products') {
				$order_id = $reference_id;
				$order_details = $this->PayCheckOutService->checkValidOrderId($order_id);

				if(count($order_details) > 0)
    			{
    				$order_id = $order_details['id'];
		    		if($this->PayCheckOutService->chkItemsAddedWithOrder($order_id))
		    		{
						//Get item details added in order
    					$order_item_details = $this->PayCheckOutService->setItemDetails($order_id, $order_details['is_custom_order']);
    					if($payment_gateway_chosen == 'wallet')
    					{
	    					$amount = $this->PayCheckOutService->getDiscountAmount($order_details);
							$amt = explode('::', $amount);
							$discount_amount = $amt[0];
							$discounted_amount = $amt[1];

							$user_account_balance = CUtil::getUserAccountBalance($logged_user_id);
							$wallet_amount = $user_account_balance['amount'];
							$wallet_amount = floatval($wallet_amount);
							$discounted_amount = floatval($discounted_amount);
							$has_sufficient_balance = TRUE;
							//right way to check float values
							if($wallet_amount > $discounted_amount)
							{
								$has_sufficient_balance = true;
							}
							else
							{
								if(abs($wallet_amount-$discounted_amount) < 0.000001)
									$has_sufficient_balance = true;
								else
								{
									$has_sufficient_balance = false;
									$error_exists = true;
									Session::set('error_message',trans('payCheckOut.insufficient_balance_to_complete_payment'));
									$error_msg = 'insufficient_balance';
								}
							}
						}
		    		}
		    		else
					{
						//item not added for order
						$error_exists = true;
						Session::set('error_message',trans('common.some_problem_try_later'));
						$error_msg = 'item_not_added';
					}
    			}
    			else
				{
					//Invalid order
					$error_exists = true;
					Session::set('error_message',trans('common.some_problem_try_later'));
					$error_msg = 'invalid_order';
				}
			}
			else if($reference_type == 'Credits' || $reference_type == 'Usercredits') {
				$credit_id = $reference_id;
				$credit_details = $this->PayCheckOutService->checkValidCreditId($credit_id);
				if(empty($credit_details)){
				    //Invalid credit
					$error_exists = true;
					Session::set('error_message',trans('common.some_problem_try_later'));
					$error_msg = 'invalid_credit';
				}
			}

			//If error exist
			if($error_exists) {
				if($payment_gateway_chosen == 'sudopay') {
					$redirect_url = URL('/');
					return View::make('paypalForm',compact('error_exists', 'redirect_url'));
				}
				else if($payment_gateway_chosen == 'paypal') {
					$redirect_url = URL('/');
					return View::make('paypalForm',compact('error_exists', 'redirect_url'));
				}
				else {
					$redirect_url = URL::to('invoice?status=Unpaid');
					echo $redirect_url."|~~|".$error_exists;
					exit;
				}
			}

			if(!$error_exists) {
				if($reference_type == 'Products') {
					$receivers_details = $this->PayCheckOutService->addOrderReceiversDetails($common_invoice_id, $order_id);
				}
				else {
					$receivers_details = $this->PayCheckOutService->addCreditReceiversDetails($common_invoice_id, $common_invoice_details);
				}
				$order_obj = Webshoporder::initialize();
				$order_item_details = $order_obj->getOrderitemDetails($order_id);
				//Log::info(print_r($receivers_details,1));
				//if every receiver have the paypal id and the amount
				if($receivers_details) {
					//get the binding
					$params['payment_method'] = $payment_gateway_chosen;
					$this->biller = App::make('PaymentInterface', $params);

					$deal_item = 0;
					if(CUtil::chkIsAllowedModule('deals') && $reference_type == 'Products')
					{
						$deal_service = new DealsService();
						$resp = $deal_service->fetchDealBasedReceiverAmount($order_item_details);
						if(COUNT($resp) > 0 &&  isset($resp['primary_amount']) &&  $resp['primary_amount'] > 0 )
							$deal_item = 1;
					}

					if($payment_gateway_chosen == 'sudopay') {
						$this->biller->setObject();
						$this->biller->setTestMode();

						$initialize_data = $pay_data = array();
						$this->biller->initialize($initialize_data);

						//Sudo pay
						//Get required details
						//$prod_obj = Products::initialize($common_invoice_details['reference_id']);
						//$p_details = $prod_obj->getProductDetails();
						$buyer_details = CUtil::getUserDetails($logged_user_id);

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

						$gateway_id = 0;
						$sudopay_fees_payer = 'site';//site = merchant or buyer
						$payment_method = 'simple';

						if(empty($d_arr)) {
							if(Input::has('d_arr')) {
								Log::info('Input has d_arr');
								$d_arr = Input::get('d_arr');
							}
						}

						Log::info(print_r($d_arr, 1));

						$sudopay_data = array();
						if(isset($d_arr[0])) {
							Log::info('Input has d_arr[0]');
							$sudopay_data = json_decode($d_arr[0], true);
							if(isset($sudopay_data['sudopay_fees_payer'])) {
								$sudopay_fees_payer = $sudopay_data['sudopay_fees_payer'];
							}
							if(isset($sudopay_data['gateway_id'])) {
								$gateway_id = $sudopay_data['gateway_id'];
							}
							// gateway id empty then set as manual payment
							if (empty($gateway_id)){
								$gateway_id = 3286;
							}
						}

						if($reference_type == 'Products') {
							$receiver_id = $this->sudopay_service->getSellerSudopayReceiverId($order_details['seller_id']);
							$is_gateway_connected = $this->sudopay_service->isGatewayConnected($order_details['seller_id'], $gateway_id);
							if($receiver_id != '' && $is_gateway_connected) {
								$payment_method = 'marketplace';
							}
						}
						//Sudo pay

						$amount = 0.00;
						$paypal_amount = 0.00;
						$reciver_amount = 0.00; //Seller amount
						$merchant_amount = 0.00;

						//Deal block start
						if($deal_item && $is_credit != 'Yes')
						{
							/*foreach($receivers_details as $receiver)
							{
								if($receiver['is_admin'] == "Yes")
								{
									$admin_account = $receiver['receiver_paypal_email'];
								}
								else
								{
									$seller_account = $receiver['receiver_paypal_email'];
								}
							}*/

							if($payment_method == 'marketplace')
							{
								$merchant_amount = $resp['primary_amount'];
								$reciver_amount = $resp['secondary_amount'];

								/*if(isset($resp['secondary_amount']) && $resp['secondary_amount'] > 0)
								{
									$pay_data['secondary_reciever'][] = array('paypal_email' => $seller_account, 'amount' => $resp['secondary_amount']);
								}
								if(isset($resp['primary_amount']) && $resp['primary_amount'] > 0)
								{
									$pay_data['primary_reciever'] = array('paypal_email' => $admin_account, 'amount' => $resp['primary_amount']);
								}*/

							}
							else
							{
								/*if(isset($resp['secondary_amount']) && $resp['secondary_amount'] > 0)
								{
									$pay_data['secondary_reciever'][] = array('paypal_email' => $seller_account, 'amount' => $resp['secondary_amount']);
								}
								if(isset($resp['primary_amount']) && $resp['primary_amount'] > 0)
								{
									$pay_data['primary_reciever'] = array('paypal_email' => $admin_account, 'amount' =>
									$resp['primary_amount']+(isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0));
								}*/
							}
							$paypal_amount = $resp['primary_amount']+(isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0);
						}
						//Deal block end
						else if($is_credit == 'Yes') {
							//if the payment is payal with wallet then use only one receiver.
							//Transfer all fund to admin, Then add credits to buyer in wallet, Then credit it for purchase
							$tot_amount = $common_invoice_details['amount'];
							$user_account_balance = CUtil::getUserAccountBalance($logged_user_id);
							$amount_to_pay = $tot_amount-$user_account_balance['amount'];
							$paypal_amount = $amount_to_pay;
						}
						else {
							foreach($receivers_details as $receiver) {
								$amount = CUtil::formatAmount($receiver['amount']);
								if($receiver['is_admin'] == "Yes")	{
									$merchant_amount = $amount;
								}
								else {
									$reciver_amount = $reciver_amount + $amount;
								}
								$paypal_amount = $paypal_amount + $amount;
							}
						}


						$pay_data = array(  //'merchant_id' => 'Enter your Merchant ID Here',
										    //'website_id' => 'Enter your Website ID Here',
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
						//Log::info('Hi check this new one '.$payment_method);
						if($payment_method == 'simple') {
							$pay_data['action'] = 'capture';
							$payment_note = "Product Purchase Add Credits by ". $buyer_details['first_name'] ."(".$buyer_details['email'].")";
							//For manual offline payment, if user provided payment note then append with it.
							if(isset($sudopay_data['parent_gateway_id']) && $sudopay_data['parent_gateway_id'] == 5346 &&
								isset($sudopay_data['payment_note']) && $sudopay_data['payment_note'] != '')
							{
								$payment_note .= " user note: ".$sudopay_data['payment_note'];
							}

							$pay_data['payment_note'] = Config::get("site.site_name").' '.$payment_note;
							//if($sudopay_fees_payer != 'site')
								//$pay_data['fees_payer'] = $sudopay_fees_payer;
						}
						else {
							//Common $_marketplace_fields_arr for make markatplace payment it will be override on submit payment
							// basic fields for marketplace related actions...
							$pay_data = array_merge($pay_data, array(	'marketplace_receiver_id' => $receiver_id,//$receiver_id,
																	    'marketplace_receiver_amount' => $reciver_amount,
																	    'marketplace_fixed_merchant_amount' => $merchant_amount,
																	    'action' => 'marketplace-capture'		));

							//if($sudopay_fees_payer != 'site')
								//$pay_data['marketplace_fees_payer'] = $sudopay_fees_payer; //Should check gateway support fees_payer (sudopay now support paypal only)
						}

						$pay_data['buyer_email'] = $buyer_details['email'];
						if($reference_type == 'Products') {
							$pay_data['buyer_phone'] = ($billing_address_details->phone_no != '') ? $billing_address_details->phone_no : 000;							$pay_data['buyer_address'] = $billing_address_details->address_line1;
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

						if($sudopay_data['parent_gateway_id'] == 4922 ) {
							$pay_data['credit_card_number'] = isset($sudopay_data['credit_card_number']) ? $sudopay_data['credit_card_number'] : '';
							$pay_data['credit_card_expire'] = isset($sudopay_data['credit_card_expire']) ? $sudopay_data['credit_card_expire'] : '';
							$pay_data['credit_card_name_on_card'] = isset($sudopay_data['credit_card_name_on_card']) ? $sudopay_data['credit_card_name_on_card'] : '';
							$pay_data['credit_card_code'] = isset($sudopay_data['credit_card_code']) ? $sudopay_data['credit_card_code'] : '';
						}
						$pay_data['gateway_id'] = $gateway_id;
						\Log::info("Sudopay_fees_payer => ".$sudopay_fees_payer);
						if($sudopay_fees_payer == 'Buyer' && isset($sudopay_data['fees_payer_token']) && $sudopay_data['fees_payer_token'] != '')
						{
							$pay_data['fees_payer'] = strtolower($sudopay_fees_payer);
							$pay_data['buyer_fees_payer_confirmation_token'] = $sudopay_data['fees_payer_token'];
						}

						// For CC Avenue, EBS payment gateway
						if($gateway_id == 6034 || $gateway_id == 6005) {
						 	$pay_data['action'] = 'auth';
						}
				        $pay_data = array_merge($pay_data, Input::All());
						$this->biller->initialize();
						$response = $this->biller->pay($pay_data);
						\Log::info(" Passing data =>".print_r($pay_data, 1));
						\Log::info(" Response Receuived =>".print_r($response, 1));
						# checking sudopay related response handling
						if($response['error']['code'] > 0)
						{
							//$_SESSION['payment_error_message'] = str_replace('error@', '', $pay_key_response);
							//$paypal_response_err_msg =  $pay_key_respone_arr[1];
							// @todo -c needs add a function send mail with the error message to admin
							//todo  use get url ..
							Session::set('error_message', $response['error']['message']);
							if($reference_type == 'Usercredits')
								$cart_url = URL::to('walletaccount/add-amount');
							else
								$cart_url = URL::to('cart/');

							//Log::info("==================error There are some problem in processing checkout one==========================");
							$error_exists = true;
							//echo $cart_url."|~~|".$error_exists;
							return Response::make($cart_url."|~~|".$error_exists);
							exit;
						}
						else {
							if (!empty($response['gateway_callback_url'])) { // redirect to callback URL...
								//header('location: ' . $response['gateway_callback_url']);
				                $error_exists = true;
								$redirect_url =  $response['gateway_callback_url'];
								//echo $redirect_url."|~~|".$error_exists;
								//code added ask for TL
								$cookie_id = Config::get('addtocart.site_cookie_prefix')."_mycart";
								$cart_cookie_id = BasicCUtil::getCookie($cookie_id);
								$cache_key_forgot = 'cart_count_cache_key_'.$cart_cookie_id;
								HomeCUtil::cacheForgot($cache_key_forgot);
								//$this->show_cart_service->emptyCart($order_details->seller_id);
								// Update invoice status as unpaid instead of draft inorder to show in listing of invoice page
								if($reference_type == 'Products')
								{
									//Empty cart
									$this->show_cart_service->emptyCart($order_details->seller_id);
									//Empty user shipping cookie
									$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();
								}
								if( (isset($response['status']) && $response['status'] == 'Pending') ||
									($gateway_id == 3286 || $gateway_id == 5958 || $gateway_id == 5991 || $gateway_id == 6004 || $gateway_id == 5973))
								{
								  //Change order status as not pending when response status is pending or manual payments
								  	if($reference_type == 'Products')	// update order status for product purchase
										$this->updateOrderStatus($order_id, 'not_paid');
								  	$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, array('status' => 'Unpaid'));
								}
								$return = Response::make($redirect_url."|~~|".$error_exists);
								if(isset($removed_cookies['ship_cookie']))
									$return = $return->withCookie($removed_cookies['ship_cookie']);
								if(isset($removed_cookies['bill_cookie']))
									$return = $return->withCookie($removed_cookies['bill_cookie']);
								return $return;
								exit;
							}
							else
							{
								Session::set('success_message',trans('payCheckOut.payment_success'));
								//$redirect_url =  Url::to('pay-checkout/'.$order_id);
								$redirect_url =  URL::action("PayCheckOutController@paymentSuccess");
								$return = Response::make($redirect_url."|~~|".$error_exists);
								//echo $redirect_url."|~~|".$error_exists;
								if(isset($removed_cookies['ship_cookie']))
									$return = $return->withCookie($removed_cookies['ship_cookie']);
								if(isset($removed_cookies['bill_cookie']))
									$return = $return->withCookie($removed_cookies['bill_cookie']);
								//code added ask for TL
								$cookie_id = Config::get('addtocart.site_cookie_prefix')."_mycart";
								$cart_cookie_id = BasicCUtil::getCookie($cookie_id);
								$cache_key_forgot = 'cart_count_cache_key_'.$cart_cookie_id;
								HomeCUtil::cacheForgot($cache_key_forgot);
								//$this->show_cart_service->emptyCart($order_details->seller_id);
								//code added ask for TL
								// Update invoice status as unpaid instead of draft inorder to show in listing of invoice page
								if($reference_type == 'Products')
								{
									//Empty cart
									$this->show_cart_service->emptyCart($order_details->seller_id);
									//Empty user shipping cookie
									$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();
								}
								if( (isset($response['status']) && $response['status'] == 'Pending') ||
									($gateway_id == 3286 || $gateway_id == 5958 || $gateway_id == 5991 || $gateway_id == 6004 || $gateway_id == 5973))
								{
								  //Change order status as not paid
									if($reference_type == 'Products')	// update order status for product purchase
										$this->updateOrderStatus($order_id, 'not_paid');

								  	$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, array('status' => 'Unpaid'));
								}
								return $return;
								exit;
							}
			            }
					}
					else if($payment_gateway_chosen == 'paypal') {
						//Log::info('Log message: Paypal payment selected!!!');
						$test_mode = false;
						if (Config::get('payment.paypal_test_mode')) {
							$test_mode = true;
						}

						$this->biller->setObject();
						$this->biller->setTestMode($test_mode);

						$initialize_data = $pay_data = array();
						$initialize_data['api_username']  = Config::get('payment.paypal_adaptive_api_username');
						$initialize_data['api_password']  = Config::get('payment.paypal_adaptive_api_password');
						$initialize_data['api_signature'] = Config::get('payment.paypal_adaptive_api_signature');
						$initialize_data['api_appid'] 	  = Config::get('payment.paypal_adaptive_app_id');
						$initialize_data['fees_payer']    = Config::get('payment.paypal_adaptive_fees_payer');

						$this->biller->initialize($initialize_data);

						$paypal_amount = 0.00;
						$amount = 0.00;

//						Log::info("==================receivers_details==========================");
//						Log::info(print_r($receivers_details,1));
//						Log::info("============================================");

						$paypal_amount = 0;
						$only_admin_amount = false;

						if($deal_item && $is_credit != 'Yes')
						{
							foreach($receivers_details as $receiver)
							{
								if($receiver['is_admin'] == "Yes")
								{
									$admin_account = $receiver['receiver_paypal_email'];
								}
								else
								{
									$seller_account = $receiver['receiver_paypal_email'];
								}
							}

							if(Config::get('payment.paypal_adaptive_payment_method') == 'parallel')
							{
								if(isset($resp['secondary_amount']) && $resp['secondary_amount'] > 0)
								{
									$pay_data['secondary_reciever'][] = array('paypal_email' => $seller_account, 'amount' => $resp['secondary_amount']);
								}
								if(isset($resp['primary_amount']) && $resp['primary_amount'] > 0)
								{
									$pay_data['primary_reciever'] = array('paypal_email' => $admin_account, 'amount' => $resp['primary_amount']);
								}
							}
							else
							{
								if(isset($resp['secondary_amount']) && $resp['secondary_amount'] > 0)
								{
									$pay_data['secondary_reciever'][] = array('paypal_email' => $seller_account, 'amount' => $resp['secondary_amount']);
								}
								if(isset($resp['primary_amount']) && $resp['primary_amount'] > 0)
								{
									$pay_data['primary_reciever'] = array('paypal_email' => $admin_account, 'amount' =>
									$resp['primary_amount']+(isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0));
								}
							}
							$paypal_amount = $resp['primary_amount']+(isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0);
						}
						elseif($is_credit == 'Yes')
						{
							//if the payment is payal with wallet then use only one receiver.
							//Transfer all fund to admin, Then add credits to buyer in wallet, Then credit it for purchase
							$tot_amount = $common_invoice_details['amount'];
							$user_account_balance = CUtil::getUserAccountBalance($logged_user_id);
							$amount_to_pay = $tot_amount-$user_account_balance['amount'];

							if(Config::get('payment.paypal_adaptive_payment_method') == 'parallel')
								$pay_data['primary_reciever'] = array('paypal_email' => $user_paypal_ac, 'amount' => $amount_to_pay);
							$paypal_amount = $amount_to_pay;
						}
						else
						{
							foreach($receivers_details as $receiver)
							{
								$user_paypal_ac = $receiver['receiver_paypal_email'];
								$amount = CUtil::formatAmount($receiver['amount']);

								if($receiver['is_admin'] == "Yes")
								{
									if(Config::get('payment.paypal_adaptive_payment_method') == 'parallel')
										$pay_data['primary_reciever'] = array('paypal_email' => $user_paypal_ac, 'amount' => $amount);
								}
								else
								{
//									if($is_credit == 'Yes')
//									{
//										$user_account_balance = CUtil::getUserAccountBalance($logged_user_id);
//										if($amount > 0 && $user_account_balance['amount'] > 0) {
//											$amount = $amount - $user_account_balance['amount'];
//										}
//									}
									$pay_data['secondary_reciever'][] = array('paypal_email' => $user_paypal_ac, 'amount' => $amount);
								}
								$paypal_amount = $paypal_amount+$amount;
							}
						}
						if(!$deal_item && Config::get('payment.paypal_adaptive_payment_method') == 'chained')
							$pay_data['primary_reciever'] = array('paypal_email' => Config::get('payment.paypal_merchant_email'),'amount' => $paypal_amount);

						$payment_note = ($reference_type != 'Usercredits') ? 'Product Purchase' : 'Add Credits To Wallet';
						$pay_data['return_url'] = URL::action("PayCheckOutController@paymentSuccess");
						$pay_data['cancel_url'] = URL::action("PayCheckOutController@paymentcancel");
						$pay_data['notification_url'] = URL::to('payment/process-paypal-adaptive');
						$pay_data['payment_note'] = Config::get("site.site_name").' '.$payment_note;
						$pay_data['currency_code'] = $currency_code;

						$pay_key_response = $this->biller->pay($pay_data);
						$pay_key_respone_arr = explode("@", $pay_key_response);
						//echo "<pre>";print_r($pay_key_respone_arr);echo "</pre>";exit;

						# 3 checking paypal related response handling
						if($pay_key_respone_arr[0] == 'error')
						{
							$_SESSION['payment_error_message'] = str_replace('error@', '', $pay_key_response);
							$paypal_response_err_msg =  $pay_key_respone_arr[1];
							// @todo -c needs add a function send mail with the error message to admin
							//todo  use get url ..
							Session::set('error_message',trans('common.some_problem_try_later'));
							$cart_url = URL::to('cart/');
							//Log::info("==================error There are some problem in processing checkout one==========================");
							$error_exists = true;
							//echo $cart_url."|~~|".$error_exists;
							return Response::make($cart_url."|~~|".$error_exists);
							exit;
						}
						else
						{
							$pay_key = $pay_key_respone_arr[1];
							$track_id = $pay_key_respone_arr[2];
							$order_det['pay_key'] = $pay_key;
							$order_det['tracking_id'] = $track_id;
							$order_det['payment_gateway_type'] = 'Paypal';

							# Update the pay_key and tracking_id into invoice table
							if($reference_type == 'Products') {
								$this->PayCheckOutService->updatePaymentOrderDetails($order_id, $order_det);
							}
							$this->PayCheckOutService->updatePaymentOrderReceiversDetails($common_invoice_id, array('pay_key' => $pay_key));

							$com_invoice_data = array('pay_key' => $pay_key, 'tracking_id' => $track_id);
							$com_invoice_data['is_credit_payment'] = 'No';
							$com_invoice_data['paypal_amount'] = $paypal_amount;
							if($is_credit == 'Yes') {
								$com_invoice_data['is_credit_payment'] = 'Yes';
								$com_invoice_data['paypal_amount'] = $paypal_amount;
							}
							$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, $com_invoice_data);

							if($reference_type == 'Products') {
								//Empty cart
								$this->show_cart_service->emptyCart($order_details->seller_id);
								//Empty user shipping cookie
								$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();
								//Change order status as not paid
								$this->updateOrderStatus($order_id, 'pending_payment');
								$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, array('status' => 'Unpaid'));
							}

							# Redirect to paypal with the pay key
							$error_exists = true;
							$redirect_url =  $pay_key_respone_arr[3];
							//echo $redirect_url."|~~|".$error_exists;
							$return = Response::make($redirect_url."|~~|".$error_exists);
							if(isset($removed_cookies['ship_cookie']))
								$return = $return->withCookie($removed_cookies['ship_cookie']);
							if(isset($removed_cookies['bill_cookie']))
								$return = $return->withCookie($removed_cookies['bill_cookie']);
							return $return;
							exit;
						}
					}
					else { // If wallet payment mode selected
						//Log::info('reference_type ====>'.$reference_type);
						if($reference_type == 'Products') {
							//Set object
							$this->biller->setObject();
							//Initialize
							$initialize_data = array();
							$initialize_data['order_id']  = $order_id;
							$this->biller->initialize($initialize_data);
							$this->biller->validate($order_id);

							$order_det['payment_gateway_type'] = 'Wallet';

							# Update the pay_key and tracking_id into invoice table
							$this->PayCheckOutService->updatePaymentOrderDetails($order_id, $order_det);

							//Empty cart
							$this->show_cart_service->emptyCart($order_details->seller_id);

							//Empty user shipping cookie
							$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();

							$payobj = $this->biller->getObject();
							$order_det = $payobj->getOrderDetails();

							$credit_obj = Credits::initialize();

							//debit from buyer
							$credit_obj->setUserId($common_invoice_details['user_id']);
							$credit_obj->setCurrency($common_invoice_details['currency']);
							$credit_obj->setAmount($common_invoice_details['amount']);
							$credit_obj->creditAndDebit('amount', 'minus');

							$wallet_details = array('user_id' => $common_invoice_details['user_id'],
													'amount' =>	$common_invoice_details['amount'],
													'transaction_key' => 'purchase',
													'reference_content_id' => $order_det['id'],
													'reference_content_table' => 'shop_order',
													'transaction_type' => 'debit',
													'transaction_notes' => 'Debited amount from your wallet for the order: '.CUtil::setOrderCode($order_det['id']),
													'status' => 'Completed',
													'payment_type' => 'wallet' );
							$payobj->setWalletTransaction($wallet_details);

							//Calculate seller amount
							$site_commission = 0.00;
							$seller_amount   = 0.00;

							if($deal_item)
							{
								$site_commission = (isset($resp['primary_amount']) ? $resp['primary_amount'] : 0);
								$seller_amount = (isset($resp['secondary_amount']) ? $resp['secondary_amount'] : 0);
							}
							else
							{
								foreach($receivers_details as $receiver)
								{
									$amount = CUtil::formatAmount($receiver['amount']);
									if($receiver['is_admin'] == "Yes")
										$site_commission = $amount;
									else
										$seller_amount = $seller_amount+$amount;
								}
							}
							//echo "<br>site_commission: ".$site_commission; echo "<br>seller_amount: ".$seller_amount;exit;

							//credit to seller
							if($seller_amount > 0 )
							{
								$credit_obj->setUserId($order_det['seller_id']);
								$credit_obj->setCurrency($common_invoice_details['currency']);
								$credit_obj->setAmount($seller_amount);
								$credit_obj->creditAndDebit('amount', 'plus');

								$wallet_details = array('user_id' => $order_det['seller_id'],
														'amount' =>	$seller_amount,
														'transaction_key' => 'purchase',
														'reference_content_id' => $order_det['id'],
														'reference_content_table' => 'shop_order',
														'transaction_type' => 'credit',
														'transaction_notes' => 'Credited amount to your wallet for the order: '.CUtil::setOrderCode($order_det['id']),
														'status' => 'Completed',
														'payment_type' => 'wallet'			);
								$payobj->setWalletTransaction($wallet_details);
							}

							//credit to site
							if($site_commission > 0 )
							{
								$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
								$credit_obj->setCurrency($common_invoice_details['currency']);
								$credit_obj->setAmount($site_commission);
								$credit_obj->creditAndDebit('amount', 'plus');

								$wallet_details = array('user_id' => Config::get('generalConfig.admin_id'),
														'amount' =>	$site_commission,
														'transaction_key' => 'purchase_fee',
														'reference_content_id' => $order_det['id'],
														'reference_content_table' => 'shop_order',
														'transaction_type' => 'credit',
														'transaction_notes' => 'Credited site commission amount to wallet for the order: '.CUtil::setOrderCode($order_det['id']),
														'payment_type' => 'wallet'		);
								$payobj->setWalletTransaction($wallet_details);
							}

							Session::set('success_message',trans('payCheckOut.payment_success'));
							//$redirect_url =  Url::to('pay-checkout/'.$order_id);
							$redirect_url =  URL::action("PayCheckOutController@paymentSuccess");
							$return = Response::make($redirect_url."|~~|".$error_exists);
							//echo $redirect_url."|~~|".$error_exists;
							if(isset($removed_cookies['ship_cookie']))
								$return = $return->withCookie($removed_cookies['ship_cookie']);
							if(isset($removed_cookies['bill_cookie']))
								$return = $return->withCookie($removed_cookies['bill_cookie']);
							$cookie_id = Config::get('addtocart.site_cookie_prefix')."_mycart";
							$cart_cookie_id = BasicCUtil::getCookie($cookie_id);
							$cache_key_forgot = 'cart_count_cache_key_'.$cart_cookie_id;
							HomeCUtil::cacheForgot($cache_key_forgot);
							return $return;
							exit;
						}
					}
				}
				else {
					$error_exists = true;
					//$redirect_url = url::to('pay-checkout/'.$order_id);
					Session::set('error_message',trans('common.some_problem_try_later'));
					Log::info("==================error There are some problem in processing checkout==========================");
					$redirect_url = URL::to('cart/');
					//$redirect_url =  Url::to('pay-checkout/'.$order_id);
					//echo $redirect_url."|~~|".$error_exists;
					return Response::make($redirect_url."|~~|".$error_exists);
					exit;
					//return View::make('paypalForm',compact('error_exists',  'redirect_url' ));
				}
			}
		}
		else
		{
			//Invalid order
			$error_exists = true;
			if($payment_gateway_chosen == 'paypal') {
				$redirect_url = URL('/');
				return View::make('paypalForm',compact('error_exists', 'redirect_url'));
			}
			else {
				$redirect_url = URL::to('cart/');
				//echo $redirect_url."|~~|".$error_exists;
				return Response::make($redirect_url."|~~|".$error_exists);
				exit;
			}
		}
	}

	public function reviewOrder($order_id = 0)
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->setFilterOrderId($order_id);
		$order_details = $order_obj->contents();
		if($order_details && count($order_details) >0)
		{
			$this->CheckOutService = new PayCheckOutService();
			$order_details = $order_details{0};
			$item_owner_id = $order_details->seller_id;

			if (CUtil::isAdmin()) {
	    		return Redirect::to('');
			}
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($logged_user_id<=0){
				return Redirect::to('');
			}
	    	$cart_obj = Addtocart::initialize();
	    	$prod_obj = Products::initialize();

	    	$input = Input::all();
			$this->item_owner_id = $item_owner_id;
			$this->checkout_currency = $order_details->currency;//Config::get('generalConfig.site_default_currency');
			if($this->checkout_currency == "")
			{
				$this->checkout_currency = (Session::has('checkout_currency'))? Session::get('checkout_currency') : "";
			}
			$this->CheckOutService->setOrderItems($order_id);
			exit;
//			$this->cart_item_details_arr[] = array(	 'item_id' => $cart['item_id'],
//												 'item_owner_id' => $cart['item_owner_id'],
//												 'item_qty' => $cart['qty'],
//												 'item_type' => 'product',
//												 );
			$this->CheckOutService->populateCheckedItems($this->checkout_currency, $pid, $type, $cart_obj, $prod_obj, $this->item_owner_id);//$this->item_owner_id,
			/*if($pid > 0)
			{
				$this->item_owner_id = $this->CheckOutService->item_owner_id;
				$this->checkout_currency = $this->CheckOutService->checkout_currency;
			}*/

			$billing_details = array();
			//$billing_details = $this->CheckOutService->getBillingDetails(0, $logged_user_id);
			if(count($billing_details) < 1)
			{
				if($logged_user_id != 0)
				{
					$user = Sentry::getUser();
					$billing_details['name'] = $user->first_name.' '.$user->last_name;;
					$billing_details['contact_no'] = $user->phone;
				}
			}
			if($logged_user_id == 0)
			{
				if($pid > 0)
				{
					$qry_str = '?pid='.$pid.'&type='.$type;
				}
				else
				{
					$qry_str = '?checkout_currency='.$this->checkout_currency;//.'&item_owner_id='.$this->item_owner_id;
				}
				$checkout_url = URL::to('checkout/'.$item_owner_id).$qry_str;
				Session::put('login_redirect_url', urlencode($checkout_url));
			}
			$CheckOutService = $this->CheckOutService;
			$no_item_msg = "";
			//echo "<pre>";print_r($CheckOutService->cart_item_details_arr);echo "</pre>";
			if(count($CheckOutService->cart_item_details_arr) == 0)
			{
				$no_item_msg = trans('checkOut.items_not_found_cart');
				if($pid > 0)
				{
					$no_item_msg = trans('checkOut.checkout_buynow_invalid_item');
				}
			}

			//Adress details
			$shipping_address_id = $billing_address_id = $last_shipping_country_id = 0;
			$shipping_address_details = array();
			//$shipping_billing_address_ids_arr = $CheckOutService->getUserCartShippingAddress($logged_user_id);
			$shipping_billing_address_ids_arr = $CheckOutService->getUserShippingAddress($logged_user_id);
			//echo "<pre>";print_r($shipping_billing_address_ids_arr);echo "</pre>";
			if(count($shipping_billing_address_ids_arr) > 0)
			{
				$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];
				$billing_address_id = $shipping_billing_address_ids_arr['billing_address_id'];
			}
			$address_obj = Webshopaddressing::Addressing();
			$shipping_address = $address_obj->getAddresses(array('id' => $shipping_address_id));
			if($shipping_address && count($shipping_address) > 0)
			{
				$last_shipping_country_id = isset($shipping_address[0]->country_id) ? $shipping_address[0]->country_id : 0;
				if($last_shipping_country_id!='' && $last_shipping_country_id > 0)
					Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $last_shipping_country_id);
			}

			$countries = array('' => trans('common.select_a_country'));
			$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc');
			$countries_list = $countries+$countries_arr;
			$d_arr['countries_list'] = $countries_list;
			$d_arr['item_owner_id'] = $this->item_owner_id;
			$d_arr['checkout_currency'] = $this->checkout_currency;
			$d_arr['pid'] = $pid;
			$d_arr['type'] = $type;
			//echo "<pre>";print_r($d_arr);echo "</pre>";
			$breadcrumb_arr = array(trans("checkOut.checkout_title"));
			return View::make('reviewOrder', compact('CheckOutService', 'breadcrumb_arr', 'd_arr', 'billing_details', 'no_item_msg', 'shipping_address_details', 'shipping_address_id', 'billing_address_id', 'last_shipping_country_id', 'item_owner_id'));
		}
	}

	//Change order status as not paid
	public function updateOrderStatus($order_id, $status)
	{
		$shop_order_obj = Webshoporder::initialize();
		$shop_order_obj->setOrderId($order_id);
		$shop_order_obj->setOrderStatus($status);
		$shop_order_obj->add();
	}

	public function paymentSuccess()
	{
		$cancel_message = "";
		$is_free = Input::has('is_free');
		if($is_free)
			$success_message = trans('payCheckOut.paycheckout_free_payment_success');
		else
			$success_message = trans('payCheckOut.paycheckout_payment_success');
		//is_free
		return View::make('paymentNotification',compact('success_message', 'cancel_message','is_free'));
	}

	public function paymentcancel()
	{
		$success_message = "";
		$is_free = false;
		$cancel_message = trans('payCheckOut.paycheckout_payment_cancel');
		return View::make('paymentNotification',compact('cancel_message', 'success_message', 'is_free'));
	}
}