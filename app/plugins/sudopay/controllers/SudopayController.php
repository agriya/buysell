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
namespace App\Plugins\Sudopay\Controllers;

use CUtil, BasicCUtil, URL, DB, Lang, View, Input, Validator, Products, Config;
use Session, Redirect, BaseController;

class SudopayController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->PayCheckOutService = new \PayCheckOutService();
		$this->common_invoice_obj =  Products::initializeCommonInvoice();
		$this->show_cart_service = new \ShowCartService();
		$this->sudopay_service = new \SudopayService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
		if(!\CUtil::chkIsAllowedModule('sudopay'))
		{
			return Redirect::to('/');
		}

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

	public function getConnectReciverAccount()
	{
		$gid = Input::has('gid') ? Input::get('gid') : 0;
		$action = Input::has('action') ? Input::get('action') : 'connect';
		$seller_deails = \Cutil::getUserDetails($this->logged_user_id);
		$receiver_account_fields_arr = array(
		    'name' => $seller_deails['display_name'],
		    'email' => $seller_deails['email'],
		    'return_url' => URL::to('sudopay/connect-reciever-return-url'),
		    'notify_url' => URL::to('sudopay/connect-reciever-notify-url').'?user_id='.$this->logged_user_id,
		    'gateway_id' => $gid, //Gateway id selected by user
		);
		$this->sc->createReceiver($receiver_account_fields_arr);
	}

	public function anyConnectRecieverNotifyUrl()
	{
		//\Log::info('##@@# Signature ==>'.Input::get('signature'));
		//\Log::info('##@@# Utils Signature ==>'.\SudoPay_Utils::getSignature($this->sudopay_credential['merchant_id'], Input::all()));
		//if (Input::has('signature') && Input::get('signature') == \SudoPay_Utils::getSignature($this->sudopay_credential['merchant_id'], Input::all())) {
			if(Input::has('error_code') && Input::get('error_code') == 0) {
				$this->sudopay_service->createReceiverAccount(Input::all());
			}
		//}
		/*if (isset($_POST)) {
		    // check signed POST to see if it's really posted from SudoPay...
		    if (@$_POST['signature'] == SudoPay_Utils::getSignature(MERCHANT_SECRET, $_POST)) {
		        if (@$_POST['error']['code'] == 0) {
		            // Payment success

		        } else {
		            // Payment failed

		        }
		    } else {
		        // Posted from unknown (security attack?) or wrong secret

		    }
		    // log for debugging
		    if (DEBUG) {
		        // relative to this app...
		        $log_filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'debug.log';
		        $content = date('Y-m-d H:i:s') . __FILE__ . '#' . __LINE__ . "\n";
		        $content.= print_r($_POST, true) . "\n";
		        error_log($content, 3, $log_filename);
		    }
		}*/
	}

	public function anyConnectRecieverReturnUrl()
	{
		//\Log::info("return success");
		Session::flash('success_message', trans('sudopay::sudopay.gateway_connected_successfully'));
		return Redirect::to('shop/users/shop-details');
	}

	public function getDisconnectReciverAccount()
	{
		$seller_id = $this->logged_user_id;
		$gid = Input::has('gid') ? Input::get('gid') : 0;
		$action = Input::has('action') ? Input::get('action') : 'disconnect';
		$sudopay_receiver_id = $this->sudopay_service->getSellerSudopayReceiverId($seller_id);
		if($sudopay_receiver_id != '') {
			$response = $this->sa->callDeleteReceiverAccounts($sudopay_receiver_id, $gid);
			if (empty($response['error']['code'])) {
				$this->sudopay_service->deleteReceiverAccount($seller_id, $gid);
			}
		}
		return Redirect::to('shop/users/shop-details');
	}

	public function postSudopayPaymentNotifyUrl()
	{
		$notify = $ipn_response = Input::all();
		$order_details = array();
		$this->sudopay_service->updateSudopayIpnLogs($ipn_response);
		$this->sudopay_service->updateSudopayTransactionLogs($ipn_response);

		if(isset($ipn_response['status']) && $ipn_response['status'] == 'Error') { //Captured
			exit;
		}

		\Log::info('Notification start');
		\Log::info(print_r($notify, 1));

		if($ipn_response['status'] == 'Pending' || $ipn_response['status'] == 'Captured')
		{
			$order_id 			= $common_invoice_id = 0;
			$order_id 			= $notify['x-order_id'];
			$buyer_id 			= $notify['x-buyer_id'];
			$common_invoice_id 	= $notify['x-common_invoice_id'];
			$reference_type 	= $notify['x-reference_type'];
			$pay_key 			= $notify['paykey'];
			$track_id 			= $notify['id'];
			$paypal_amount 		= $notify['amount'];
			$origional_amount_provided = 0;
			if(isset($notify['original_amount']))
			{
				$origional_amount_provided = 1;
				$paypal_amount = $notify['original_amount'];
			}
			$is_credit 			= $notify['x-is_credit'];
			$gateway_name 		= $notify['gateway_name'];
			$action 			= $notify['action'];

			$order_det = array();
			$order_det['pay_key'] 		= $pay_key;
			$order_det['tracking_id'] 	= $track_id;
			$order_det['payment_gateway_type'] = $gateway_name;
			if($reference_type == 'Products') {
				$this->PayCheckOutService->updatePaymentOrderDetails($order_id, $order_det);
			}
			$this->PayCheckOutService->updatePaymentOrderReceiversDetails($common_invoice_id, array('pay_key' => $pay_key));

			//\Log::info('Notification step1');
			$com_invoice_data = array('pay_key' => $pay_key, 'tracking_id' => $track_id);
			$com_invoice_data['is_credit_payment'] = 'No';
			if($ipn_response['status'] == 'Pending' )
				$com_invoice_data['status'] = 'Unpaid';	// if nottification received as pending then update as unpaid status for invoice entry.
			$com_invoice_data['paypal_amount'] = $paypal_amount;
			if($is_credit == 'Yes') {
				$com_invoice_data['is_credit_payment'] = 'Yes';
				$com_invoice_data['paypal_amount'] = $paypal_amount;
			}
			$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, $com_invoice_data);

			//\Log::info('Notification step2');
			if($reference_type == 'Products')
			{
				\Log::info('order_id ==>'.$order_id.' buyer_id ====>'.$buyer_id);
				$order_details = $this->PayCheckOutService->checkValidOrderId($order_id, $buyer_id);
				//return false if buyer_id not match and order_status eq draft (or) order_status eq not_paid
				if(empty($order_details))
					exit;
				//\Log::info('order_details ---------------------->');
				//\Log::info(print_r($order_details, 1));

				//Empty cart
				$this->show_cart_service->emptyCart($order_details['seller_id']);
				//Empty user shipping cookie
				$removed_cookies = $this->PayCheckOutService->emptyUserShippingCookie();
				//Change order status as not paid
				$this->updateOrderStatus($order_id, 'not_paid');
				$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, array('status' => 'Unpaid'));
			}
		}

		if($ipn_response['status'] == 'Captured')
		{
			\Log::info('Notification step3');
			//Paypal adaptive contorller
			$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);
			if(isset($common_invoice_details['status']) && $common_invoice_details['status'] == 'Unpaid') {
				//\Log::info('Notification step4');
				$credit_obj = \Credits::initialize();

				$plan_details = $this->sa->callGetPlan();
				$sudopay_fees_payer = 'Merchant';//Merchant or Buyer
				if(isset($plan_details['sudopay_fees_payer']) && $plan_details['sudopay_fees_payer'] != '') {
					$sudopay_fees_payer = $plan_details['sudopay_fees_payer'];
				}
				$this->sudopay_service->setSudopayFeesPayer($sudopay_fees_payer);

				if($common_invoice_details && $common_invoice_details['reference_type'] == 'Usercredits') {
					//\Log::info('Notification step5');
					$common_invoice_data = array();
					$this->sudopay_service->updateCreditsLogStatus('Paid', $common_invoice_details);

					$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];

					$this->sudopay_service->updateCommonInvoiceStatus($common_invoice_data, $common_invoice_id, 0);

					$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);

					$this->sudopay_service->updateReceiversDetails($common_invoice_id, $ipn_response);

					if($common_invoice_details) {
						//\Log::info('Notification step6');

						$usercredits = $common_invoice_details['paypal_amount'];
						//\Log::info('Usercredits Step 1:'. $usercredits);
						if($sudopay_fees_payer == 'Buyer') {
							$usercredits = $common_invoice_details['amount'];
							//\Log::info('Usercredits Step 2:'. $usercredits);
						}
						$credit_obj->setUserId($common_invoice_details['user_id']);
						$credit_obj->setCurrency($common_invoice_details['currency']);
						$credit_obj->setAmount($usercredits);
						$credit_obj->credit();

						$wallet_details = array('user_id' => $common_invoice_details['user_id'],
												'amount' => $usercredits,
												'transaction_key' => 'walletaccount',
												'reference_content_id' => $common_invoice_details['common_invoice_id'],
												'reference_content_table' => 'common_invoice',
												'transaction_type' => 'credit',
												'status' => 'Completed',
												'transaction_notes' => 'Credited amount to your wallet account from '.$gateway_name,
												'transaction_id' => isset($ipn_response['id']) ? $ipn_response['id'] : '',
												'paypal_adaptive_transaction_id' => '',
												'payment_type' => $gateway_name		);

						//\Log::info(print_r($wallet_details, 1));
						$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);
						if($sudopay_fees_payer == 'Buyer' && $origional_amount_provided == 0)
						{
							// No need to add entry for gateway fee, otherwise have to add the amount with gateway fee as well as debit it.
							/*
							$gateway_fee = $common_invoice_details['paypal_amount'] - $common_invoice_details['amount'];
							//\Log::info('Usercredits Step 3:'. $gateway_fee);
							$wallet_details['amount'] = $gateway_fee;
							$wallet_details['transaction_key'] = 'gateway_fee';
							$wallet_details['transaction_notes'] = 'Debited for gateway fee.';
							$wallet_details['transaction_type'] = 'debit';
							//$wallet_details = $wallet_details + $fees_payer_details;
							//\Log::info(print_r($wallet_details, 1));
							$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);
							*/
						}
						$this->sudopay_service->sendCreditsInvoiceMailToUser($common_invoice_details);
					}
				}
				else {
					//\Log::info('Notification step7');
					$has_sufficient_balance = true;
					$avail_balance = 0.00;
					$amount_paid_by_wallet = 0.00;

					//\Log::info('setPayment order_details ---------------------->');
					//\Log::info(print_r($order_details, 1));

					$this->sudopay_service->setPaymentAmountDetails($order_details, $ipn_response);

					//if it is the paypal with wallet
					if($common_invoice_details && $common_invoice_details['reference_type'] == 'Products' && $common_invoice_details['is_credit_payment'] == 'Yes') {
						//\Log::info('Notification step8');
						// if fee paid by buyer and txn amount alone sent back not provided the purchase item then handle it here.
						$usercredits = $common_invoice_details['paypal_amount'];
						if($sudopay_fees_payer == 'Buyer' && $origional_amount_provided == 0)
						{
							$usercredits = $common_invoice_details['amount'];
							if($common_invoice_details['amount'] >  $common_invoice_details['paypal_amount'])
							{
								$user_account_balance = \CUtil::getUserAccountBalance($common_invoice_details['user_id']);
								if($user_account_balance['amount'] > 0) {
									$avail_balance = $user_account_balance['amount'];
								}
								$usercredits = $usercredits-$avail_balance;
							}

							$inv_data = array();
							$inv_data['paypal_amount'] = $paypal_amount;
							$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, $inv_data);

							\Log::info('Usercredits Step 2: '. $usercredits);
						}

						//First add the amount to the buyer wallet
						$credit_obj->setUserId($common_invoice_details['user_id']);
						$credit_obj->setCurrency($common_invoice_details['currency']);
					//	$credit_obj->setAmount($common_invoice_details['paypal_amount']);
						$credit_obj->setAmount($usercredits);
						$credit_obj->credit('amount', 'plus');
						$wallet_details = array('user_id' => $common_invoice_details['user_id'],
												'amount' =>	$common_invoice_details['paypal_amount'],
												'amount' =>	$usercredits,
												'transaction_key' => 'walletaccount_purchase',
												'reference_content_id' => $order_id,
												'reference_content_table' => 'shop_order',
												'transaction_type' => 'credit',
												'transaction_notes' => 'Credited amount to your wallet from paypal for the order: '.\CUtil::setOrderCode($order_id),
												'status' => 'Completed',
												'transaction_id' => isset($ipn_response['id']) ? $ipn_response['id'] : '',
												'paypal_adaptive_transaction_id' => '',
												'payment_type' => $gateway_name		);
						$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);
						if($common_invoice_details['amount'] >  $common_invoice_details['paypal_amount'])
						{
							//\Log::info('Notification step9');
							$user_account_balance = \CUtil::getUserAccountBalance($common_invoice_details['user_id']);
							if($user_account_balance['amount'] > 0) {
								$avail_balance = $user_account_balance['amount'];
							}

							//$amount_paid_by_wallet = $common_invoice_details['amount'] - $common_invoice_details['paypal_amount'];
							//$avail_balance = floatval($avail_balance);
							$amount_paid_by_wallet = floatval($common_invoice_details['amount']);

							//This is the way to check float values
							if($avail_balance > $amount_paid_by_wallet)
							{
								$has_sufficient_balance = true;
							}
							else
							{
								if(abs($avail_balance-$amount_paid_by_wallet) < 0.000001)
									$has_sufficient_balance = true;
								else
									$has_sufficient_balance = false;
							}

							//This wont work if both are same float value
	//						if($avail_balance < $amount_paid_by_wallet) {
	//							$has_sufficient_balance = false;
	//						}

							///echo "<br>has_sufficient_balance: ".$has_sufficient_balance;exit;
							if($has_sufficient_balance)
							{
								//\Log::info('Notification step10');
								$order_det['payment_gateway_type'] = 'wallet';
								if($reference_type == 'Products') {
									$this->PayCheckOutService->updatePaymentOrderDetails($order_id, $order_det);
								}
								//update the order status as payment_completed
								$this->updateOrderStatus($order_id, 'payment_completed');

								//for secuirity update this as as in common invoice
								$common_invoice_data = array();
								$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];
								$common_invoice_data['is_credit_payment'] = 'Yes';
								$common_invoice_data['status'] = 'Paid';
								$this->sudopay_service->updateCommonInvoiceStatus($common_invoice_data, $common_invoice_id, $order_details['total_amount']);

								$this->sudopay_service->updateReceiversDetails($common_invoice_id, $ipn_response);

								//Generate invoice now
								$invResp = $this->sudopay_service->generateInvoiceForOrder($order_id);
								//$this->sudopay_service->getInvoicesForOrder($order_id);

								$order_details = $this->PayCheckOutService->checkValidOrderId($order_id, $buyer_id);

								//debit from buyer
								$credit_obj->setUserId($common_invoice_details['user_id']);
								$credit_obj->setCurrency($common_invoice_details['currency']);
								$credit_obj->setAmount($common_invoice_details['amount']);
								$credit_obj->creditAndDebit('amount', 'minus');

								$wallet_details = array('user_id' => $common_invoice_details['user_id'],
														'amount' =>	$common_invoice_details['amount'],
														'transaction_key' 		=> 'purchase',
														'reference_content_id' 	=> $order_id,
														'reference_content_table' => 'shop_order',
														'transaction_type' 		=> 'debit',
														'transaction_notes' 	=> 'Debited amount from your wallet for the order: '.\CUtil::setOrderCode($order_id),
														'status' => 'Completed',
														'payment_type' => 'wallet'		);
								$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);

								//Calculate seller amount
								if(isset($invResp['primary']) && $invResp['primary'] != '')
								{
									$site_commission = $invResp['primary'];
									$seller_amount = (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0) ? $invResp['secondary'] : 0;
								}
								else
								{
									$site_commission = $order_details['site_commission'];
									$seller_amount = $order_details['total_amount'] - $order_details['site_commission'];
								}
								//echo "<br>site_commission: ".$site_commission;
								//echo "<br>seller_amount: ".$seller_amount;exit;

								//credit to seller
								if($seller_amount > 0 )
								{
									$credit_obj->setUserId($order_details['seller_id']);
									$credit_obj->setCurrency($common_invoice_details['currency']);
									$credit_obj->setAmount($seller_amount);
									$credit_obj->creditAndDebit('amount', 'plus');

									$wallet_details = array('user_id' => $order_details['seller_id'],
															'amount' =>	$seller_amount,
															'transaction_key' => 'purchase',
															'reference_content_id' => $order_id,
															'reference_content_table' => 'shop_order',
															'transaction_type' => 'credit',
															'transaction_notes' => 'Credited amount to your wallet for the order: '.\CUtil::setOrderCode($order_id),
															'status' => 'Completed',
															'payment_type' => 'wallet'		);
									$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);
								}

								//credit to site
								if($site_commission > 0 )
								{
									$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
									$credit_obj->setCurrency($common_invoice_details['currency']);
									$credit_obj->setAmount($site_commission);
									$credit_obj->creditAndDebit('amount', 'plus');

									$wallet_details = array(
														'user_id' => Config::get('generalConfig.admin_id'),
														'amount' =>	$site_commission,
														'transaction_key' => 'purchase_fee',
														'reference_content_id' => $order_id,
														'reference_content_table' => 'shop_order',
														'transaction_type' => 'credit',
														'transaction_notes' => 'Credited site commission amount to wallet for the order: '.\CUtil::setOrderCode($order_id),
														'status' => 'Completed',
														'payment_type' => 'wallet',
													);
									$this->sudopay_service->setWalletTransaction($wallet_details, 'purchase', $common_invoice_details, $order_details);
								}

								//send invoice mail to user
								$this->sudopay_service->sendInvoiceMailToUser($common_invoice_details, $ipn_response);
							}
							else {
								//if no sufficient balance
								//\Log::info('Notification step11');

								//update the order status as payment cancelled
								$this->updateOrderStatus($order_id, 'payment_cancelled');

								//update the common invoice status as cancelled
								$common_invoice_data = array();
								$common_invoice_data['status'] = 'Cancelled';
								$this->PayCheckOutService->updateCommonInvoiceDetails($common_invoice_id, $com_invoice_data);

								//update the transaction id and status in the order receiver. So that we will have transactio id
								$this->sudopay_service->updateReceiversDetails($common_invoice_id, $ipn_response);

								//Send the mail to the users as it is cancelled
								$pay_to_email = (isset($payment_details['senderEmail']) && $payment_details['senderEmail']!='')?$payment_details['senderEmail']:( (isset($ipn_data['sender_email']) && $ipn_data['sender_email'])?$ipn_data['sender_email']: '' );

								$order_receivers = $this->sudopay_service->getOrderReceiversForOrder($common_invoice_id);

								$order_receiver_details = array();
								$order_receiver_details['buyer_details'] = $buyer_details = \CUtil::getUserDetails($common_invoice_details['user_id']);
								$order_receiver_details['buyer_paypal_email'] = $pay_to_email;
								$order_receiver_details['buyer_available_balance'] = $avail_balance;
								foreach($order_receivers as $order_receiver)
								{
									if($order_receiver['is_admin'] == 'Yes')
									{
										$order_receiver_details['admin_paypl_email'] = $order_receiver['receiver_paypal_email'];
										$order_receiver_details['admin_amount'] = $order_receiver['amount'];
										$order_receiver_details['admin_email'] = Config::get("generalConfig.invoice_email");
										$order_receiver_details['admin_transaction_id'] = $order_receiver['txn_id'];
										$order_receiver_details['admin_paypal_amount'] = $payment_amount_details['credit_to_admin'];
									}
									else
									{
										//$seller_det = array();
										$order_receiver_details['seller_details'] = $seller_details = \CUtil::getUserDetails($order_receiver['seller_id']);
										$order_receiver_details['seller_amount'] = $order_receiver['amount'];
										$order_receiver_details['seller_transaction_id'] = $order_receiver['txn_id'];
										$order_receiver_details['seller_paypal_email'] = $order_receiver['receiver_paypal_email'];
										$order_receiver_details['seller_paypal_amount'] = $payment_amount_details['seller_amount'];
									}
								}
								$this->sudopay_service->sendInsufficientBalanceMailToAdmin($order_receiver_details, $common_invoice_details);
								$this->sudopay_service->sendInsufficientBalanceMailToSeller($order_receiver_details, $common_invoice_details);

								//sendmail to buyer as refund requested
								$this->sudopay_service->sendInsufficientBalanceMailToBuyer($order_receiver_details, $common_invoice_details);
							}
						}
					}
					//if it is normal payment from paypal
					else {
						//\Log::info('Notification step12');
						//echo "cpmiomg"; exit;
						if($common_invoice_details && $common_invoice_details['reference_type'] == 'Products')
						{
							//\Log::info('Notification step13');
							//update the order status as completed
							$this->updateOrderStatus($order_id, 'payment_completed');

							//update the common invoice status
							$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];
							$common_invoice_data['is_credit_payment'] = 'No';
							$common_invoice_data['status'] = 'Paid';

							$this->sudopay_service->updateCommonInvoiceStatus($common_invoice_data, $common_invoice_id, $order_details['total_amount']);

							//update the transaction id and status in the order receiver. So that we will have transactio id
							$this->sudopay_service->updateReceiversDetails($common_invoice_id, $ipn_response);

							$invResp = $this->sudopay_service->generateInvoiceForOrder($order_id);

							//\Log::info('Notification step14');
							//\Log::info(print_r($invResp, 1));
							//To get updated status set order details again
							$order_details = $this->PayCheckOutService->checkValidOrderId($order_id, $buyer_id);

							$site_commission = $order_details['site_commission'];
							//echo "<br>site_commission: ".$site_commission;
							//echo "<br>admin id: ".Config::get('generalConfig.admin_id');
							//exit;

							// If it is not refund transaction
							$is_refund_transaction = false;
							if(!$is_refund_transaction)
							{
								if(isset($order_details['coupon_code']) && isset($order_details['discount_amount']) && $order_details['discount_amount'] > 0)
								{
									// Caluclate and update site commission and seller amount for shop_order and shop_order_item
									$net_amount = $order_details['sub_total'] - $order_details['discount_amount'];
									$site_commission = $this->PayCheckOutService->calculateSiteCommission($net_amount);
									$seller_amount = $net_amount - $site_commission;

									//Update into corresponding shop_order and shop_order_item
									$this->sudopay_service->updateOrderDiscountDetails($order_id, $site_commission, $seller_amount, $net_amount);
								}
								else
								{
									if(isset($invResp['primary']) && $invResp['primary'] != '')
									{
										$site_commission = $invResp['primary'];
										$seller_amount = (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0) ? $invResp['secondary'] : 0;
									}
									else
									{
										$site_commission = $order_details['site_commission'];
										$seller_amount = $order_details['total_amount'] - $order_details['site_commission'];
									}
								}

								if($ipn_response['action'] == 'Capture')
								{
									//\Log::info('in action ===>'.$ipn_response['action']);
									//\Log::info('total_amount ===>'.$order_details['total_amount']);
									//\Log::info('seller_amount ===>'.$seller_amount);

									//1. site credit total amount - 1000
									//\Log::info('1. site credit total amount - 1000');
									$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
									$credit_obj->setCurrency($common_invoice_details['currency']);
									$credit_obj->setAmount($order_details['total_amount']);//Total amount
									$response = $credit_obj->creditAndDebit('amount', 'plus');

									//2. Site debit seller amount - 1000 - 950
									if($seller_amount > 0) {
										//\Log::info('1. site credit total amount - 1000');
										$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
										$credit_obj->setCurrency($common_invoice_details['currency']);
										$credit_obj->setAmount($seller_amount);//Total amount
										$response = $credit_obj->creditAndDebit('amount', 'minus');

										//\Log::info('3. Credit seller amount - 950');
										//3. Credit seller amount - 950
										$credit_obj->setUserId($order_details['seller_id']);
										$credit_obj->setCurrency($common_invoice_details['currency']);
										$credit_obj->setAmount($seller_amount);//Total amount
										$response = $credit_obj->creditAndDebit('amount', 'plus');
									}
								}
								else {
									//\Log::info('in action ===>'.$ipn_response['action']);
									//\Log::info('site_commission ===>'.$site_commission);
									//\Log::info('seller_amount ===>'.$seller_amount);
									//1. Site credit commision amount - 50
									if($site_commission > 0) {
										$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
										$credit_obj->setCurrency($common_invoice_details['currency']);
										$credit_obj->setAmount($site_commission);//Total amount
										$response = $credit_obj->creditAndDebit('amount', 'plus');
									}

									//2. Crdit seller amount - 950
									########### NOT NEEDED AS SELLER GET THE AMOUNT DIRECTLY IN THE PAYMENT GATEWAY ############
									#if($seller_amount > 0) {
									#	$credit_obj->setUserId($order_details['seller_id']);
									#	$credit_obj->setCurrency($common_invoice_details['currency']);
									#	$credit_obj->setAmount($seller_amount);//Total amount
									#	$response = $credit_obj->creditAndDebit('amount', 'plus');
									#}
									########### NOT NEEDED AS SELLER GET THE AMOUNT DIRECTLY IN THE PAYMENT GATEWAY ############
								}

								$fin_res = json_decode($response);
								$site_transactions = $this->sudopay_service->setSiteTransactions($action, $order_details, $ipn_response, $invResp);
								$this->sudopay_service->sendInvoiceMailToUser($common_invoice_details, $ipn_response);
							}
						}
					}
				}
			}
		}
	}

	public function updateOrderStatus($order_id, $status)
	{
		$shop_order_obj = \Webshoporder::initialize();
		$shop_order_obj->setOrderId($order_id);
		$shop_order_obj->setOrderStatus($status);
		$shop_order_obj->add();
	}
}