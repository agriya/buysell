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
class ProcessPaypalAdaptivePaymentController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		//$this->paymentService = new ProcessPaypayAdaptivePaymentsService();
		$params['payment_method'] = 'paypal';
		$this->biller = App::make('PaymentInterface', $params);
	}

	public function processPaypalPost()
	{
		Log::info("========================= Notification Controller Starts ==================================");
		/* -------------------------------> Begin of code <------------------------------------ */
		$payobj = new ProcessPaypalAdaptivePaymentsService();

		$this->biller->setObject();

		//Set mode
		$test_mode = false;
		if (Config::get('payment.paypal_test_mode')) {
			$test_mode = true;
		}
		$this->biller->setTestMode($test_mode);

		//Set initialize
		$initialize_data = $pay_data = array();
		$initialize_data['api_username']  = Config::get('payment.paypal_adaptive_api_username');
		$initialize_data['api_password']  = Config::get('payment.paypal_adaptive_api_password');
		$initialize_data['api_signature'] = Config::get('payment.paypal_adaptive_api_signature');
		$initialize_data['api_appid'] 	  = Config::get('payment.paypal_adaptive_app_id');
		$initialize_data['fees_payer']    = Config::get('payment.paypal_adaptive_fees_payer');
		$this->biller->initialize($initialize_data);

		$paypal_reponse = $this->biller->validate($_REQUEST);
		$paypalAdaptivePaymentTransactionid = $this->biller->getPaypalAdaptivePaymentTransaction();
		$paypalAdaptivePaymentTransactionDetails = $this->biller->getPaypalAdaptivePaymentTransactionDetails();

		$payobj->setPaypalAdaptivePaymentTransactionDetails($paypalAdaptivePaymentTransactionDetails);

		//echo "<pre>";print_r($paypalAdaptivePaymentTransactionDetails);echo "</pre>";

		//echo "<br>paypalAdaptivePaymentTransactionid: ".$paypalAdaptivePaymentTransactionid;

		//exit;

		$payment_method = $this->biller->getPaymentMethod();
		$payobj->setPaymentMethod($payment_method);


		$payobj->createErrorLogFile('pay');
		$str = print_r($_REQUEST, 1);
		$payobj->writetoTempFile($str);


		Log::info($str);

		//$payobj->processIPN();
		if ($paypal_reponse)
		{

			$ipn_data = $this->biller->getIpnData();
			$payment_details = $this->biller->getPaymentDetailsData();

			$receiver_details = $this->biller->getPaymentReceiverDetailsData();
			//echo "<pre>";print_r($receiver_details);echo "</pre>";
			//echo "<pre>";print_r($receiver_details);echo "</pre>";
			//echo "<pre>";print_r($payment_details);echo "</pre>";//exit;
			$payobj->setIpnData($ipn_data);
			$payobj->setPaymentDetailsData($payment_details);

			$payobj->setCommonInvoiceDetails();
			$common_invoice_details = $payobj->getCommonInvoiceDetails();

			if(isset($common_invoice_details['status']) && $common_invoice_details['status'] == 'Unpaid')
			{
				//echo "<pre>";print_r($common_invoice_details);echo "</pre>";
				$credit_obj = Credits::initialize();
				if($common_invoice_details && $common_invoice_details['reference_type'] == 'Usercredits') {
					$common_invoice_data = array();
					$payobj->updateCreditsLogStatus();
					$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];

					$payobj->updateCommonInvoiceStatus($common_invoice_data);

					//update the common invoice status.. so the it will get
					$payobj->setCommonInvoiceDetails($common_invoice_details['common_invoice_id']);

					$receiver_details = $this->biller->getPaymentReceiverDetailsData();
					$payobj->setPaymentReceiverDetails($receiver_details);
					$payobj->updateReceiversTransactionId();

					if($common_invoice_details) {
						//Credit to seller account
	//					$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
	//					$credit_obj->setCurrency($common_invoice_details['currency']);
	//					$credit_obj->setAmount($common_invoice_details['paypal_amount']);
	//					$credit_obj->credit();

						$credit_obj->setUserId($common_invoice_details['user_id']);
						$credit_obj->setCurrency($common_invoice_details['currency']);
						$credit_obj->setAmount($common_invoice_details['paypal_amount']);
						$credit_obj->credit();
						$wallet_details = array(
												'user_id' => $common_invoice_details['user_id'],
												'amount' =>$common_invoice_details['paypal_amount'],
												'transaction_key' => 'walletaccount',
												'reference_content_id' => $common_invoice_details['common_invoice_id'],
												'reference_content_table' => 'common_invoice',
												'transaction_type' => 'credit',
												'status' => 'Completed',
												'transaction_notes' => 'Credited amount to your wallet account from paypal',
												'transaction_id' => isset($paypalAdaptivePaymentTransactionDetails['buyer_trans_id'])?$paypalAdaptivePaymentTransactionDetails['buyer_trans_id']:'',
												'paypal_adaptive_transaction_id' => isset($paypalAdaptivePaymentTransactionid)?$paypalAdaptivePaymentTransactionid:'',
												'payment_type' => 'paypal',
											);
						$payobj->setWalletTransaction($wallet_details);
						$payobj->sendCreditsInvoiceMailToUser();
					}
				}
				else
				{
					$has_sufficient_balance = true;
					$avail_balance = 0.00;
					$amount_paid_by_wallet = 0.00;

					$payobj->setOrderDetails();
					$order_det = $payobj->getOrderDetails();
					//echo "<pre>";print_r($order_det);echo "</pre>";
					$payobj->setPaymentAmountDetails();
					$payment_amount_details = $payobj->getPaymentAmountDetails();
					//echo "<pre>";print_r($payment_amount_details);echo "</pre>";
					//exit;

					$normal_payment_flow = FALSE;
					//echo "<pre>";print_r($common_invoice_details);echo "</pre>";exit;
					//if it is the paypal with wallet
					if($common_invoice_details && $common_invoice_details['reference_type'] == 'Products' && $common_invoice_details['is_credit_payment'] == 'Yes')
					{
						//First add the amount to the buyer wallet
						$credit_obj->setUserId($common_invoice_details['user_id']);
						$credit_obj->setCurrency($common_invoice_details['currency']);
						$credit_obj->setAmount($common_invoice_details['paypal_amount']);
						$credit_obj->credit('amount', 'plus');
						$wallet_details = array(
												'user_id' => $common_invoice_details['user_id'],
												'amount' =>	$common_invoice_details['paypal_amount'],
												'transaction_key' => 'walletaccount_purchase',
												'reference_content_id' => $order_det['id'],
												'reference_content_table' => 'shop_order',
												'transaction_type' => 'credit',
												'transaction_notes' => 'Credited amount to your wallet from paypal for the order: '.CUtil::setOrderCode($order_det['id']),
												'status' => 'Completed',
												'transaction_id' => isset($paypalAdaptivePaymentTransactionDetails['buyer_trans_id'])?$paypalAdaptivePaymentTransactionDetails['buyer_trans_id']:'',
												'paypal_adaptive_transaction_id' => isset($paypalAdaptivePaymentTransactionid)?$paypalAdaptivePaymentTransactionid:'',
												'payment_type' => 'paypal',
											);

						$payobj->setWalletTransaction($wallet_details);
						if($common_invoice_details['amount'] >  $common_invoice_details['paypal_amount'])
						{


							$user_account_balance = CUtil::getUserAccountBalance($common_invoice_details['user_id']);
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
	//							echo "<br>comes here";
	//							$has_sufficient_balance = false;
	//						}

							///echo "<br>has_sufficient_balance: ".$has_sufficient_balance;exit;
							if($has_sufficient_balance)
							{
								//update the order status as completed
								$payobj->updateOrderStatus();

								//for secuirity update this as as in common invoice
								$common_invoice_data = array();
								$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];
								$common_invoice_data['is_credit_payment'] = 'Yes';
								$common_invoice_data['status'] = 'Paid';
								$payobj->updateCommonInvoiceStatus($common_invoice_data);

								//update the transaction id and status in the order receiver. So that we will have transactio id
								$receiver_details = $this->biller->getPaymentReceiverDetailsData();
								$payobj->setPaymentReceiverDetails($receiver_details);
								$payobj->updateReceiversTransactionId();

								//Generate invoice now
								$invResp = $payobj->generateInvoiceForOrder();
								$payobj->getInvoicesForOrder();

								//Store in the log file
								$payobj->print_fields();

								//Update the order details so that it will reflect in mail
								$payobj->setOrderDetails();

								//debit from buyer
								$credit_obj->setUserId($common_invoice_details['user_id']);
								$credit_obj->setCurrency($common_invoice_details['currency']);
								$credit_obj->setAmount($common_invoice_details['amount']);
								$credit_obj->creditAndDebit('amount', 'minus');

								$wallet_details = array(
													'user_id' => $common_invoice_details['user_id'],
													'amount' =>	$common_invoice_details['amount'],
													'transaction_key' => 'purchase',
													'reference_content_id' => $order_det['id'],
													'reference_content_table' => 'shop_order',
													'transaction_type' => 'debit',
													'transaction_notes' => 'Debited amount from your wallet for the order: '.CUtil::setOrderCode($order_det['id']),
													'status' => 'Completed',
													'payment_type' => 'wallet',
												);
								$payobj->setWalletTransaction($wallet_details);

								//Calculate seller amount
								if(isset($invResp['primary']) && $invResp['primary'] != '')
								{
									$site_commission = $invResp['primary'];
									$seller_amount = (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0) ? $invResp['secondary'] : 0;
								}
								else
								{
									$site_commission = $order_det['site_commission'];
									$seller_amount =$order_det['total_amount']-$order_det['site_commission'];
								}
								//echo "<br>site_commission: ".$site_commission;
								//echo "<br>seller_amount: ".$seller_amount;exit;


								//credit to seller
								if($seller_amount > 0 )
								{
									$credit_obj->setUserId($order_det['seller_id']);
									$credit_obj->setCurrency($common_invoice_details['currency']);
									$credit_obj->setAmount($seller_amount);
									$credit_obj->creditAndDebit('amount', 'plus');

									$wallet_details = array(
														'user_id' => $order_det['seller_id'],
														'amount' =>	$seller_amount,
														'transaction_key' => 'purchase',
														'reference_content_id' => $order_det['id'],
														'reference_content_table' => 'shop_order',
														'transaction_type' => 'credit',
														'transaction_notes' => 'Credited amount to your wallet for the order: '.CUtil::setOrderCode($order_det['id']),
														'status' => 'Completed',
														'payment_type' => 'wallet',
													);
									$payobj->setWalletTransaction($wallet_details);
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
														'reference_content_id' => $order_det['id'],
														'reference_content_table' => 'shop_order',
														'transaction_type' => 'credit',
														'transaction_notes' => 'Credited site commission amount to wallet for the order: '.CUtil::setOrderCode($order_det['id']),
														'status' => 'Completed',
														'payment_type' => 'wallet',
													);
									$payobj->setWalletTransaction($wallet_details);
								}

								//send invoice mail to user
								$payobj->sendInvoiceMailToUser();

							}
							else
							{
								//if no sufficient balance

								//update the order status as payment cancelled
								$payobj->updateOrderStatus('payment_cancelled');

								//update the common invoice status as cancelled
								$common_invoice_data = array();
								$common_invoice_data['status'] = 'Cancelled';

								$payobj->updateCommonInvoiceDetails($common_invoice_data);


								//update the transaction id and status in the order receiver. So that we will have transactio id
								$receiver_details = $this->biller->getPaymentReceiverDetailsData();
								$payobj->setPaymentReceiverDetails($receiver_details);
								$payobj->updateReceiversTransactionId();


								//Send the mail to the users as it is cancelled
								$pay_to_email = (isset($payment_details['senderEmail']) && $payment_details['senderEmail']!='')?$payment_details['senderEmail']:( (isset($ipn_data['sender_email']) && $ipn_data['sender_email'])?$ipn_data['sender_email']: '' );

								$order_receivers = $payobj->getOrderReceiversForOrder($common_invoice_details['common_invoice_id']);

								$order_receiver_details = array();
								$order_receiver_details['buyer_details'] = $buyer_details = CUtil::getUserDetails($common_invoice_details['user_id']);
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
										$order_receiver_details['seller_details'] = $seller_details = CUtil::getUserDetails($order_receiver['seller_id']);
										$order_receiver_details['seller_amount'] = $order_receiver['amount'];
										$order_receiver_details['seller_transaction_id'] = $order_receiver['txn_id'];
										$order_receiver_details['seller_paypal_email'] = $order_receiver['receiver_paypal_email'];
										$order_receiver_details['seller_paypal_amount'] = $payment_amount_details['seller_amount'];
									}
								}
								$payobj->sendInsufficientBalanceMailToAdmin($order_receiver_details);
								$payobj->sendInsufficientBalanceMailToSeller($order_receiver_details);

								//sendmail to buyer as refund requested
								$payobj->sendInsufficientBalanceMailToBuyer($order_receiver_details);


							}
						}
					}
					//if it is normal payment from paypal
					else
					{
						//echo "cpmiomg"; exit;
						if($common_invoice_details && $common_invoice_details['reference_type'] == 'Products')
						{
							//update the order status as completed
							$payobj->updateOrderStatus();

							//update the common invoice status
							$common_invoice_data['paypal_amount'] = $common_invoice_details['paypal_amount'];
							$common_invoice_data['is_credit_payment'] = 'No';
							$common_invoice_data['status'] = 'Paid';

							$payobj->updateCommonInvoiceStatus($common_invoice_data);

							$receiver_details = $this->biller->getPaymentReceiverDetailsData();
							$payobj->setPaymentReceiverDetails($receiver_details);
							$payobj->updateReceiversTransactionId();


							$invResp = $payobj->generateInvoiceForOrder();

							$payobj->print_fields();

							//To get updated status set order details again
							$payobj->setOrderDetails();

							$site_commission = $order_det['site_commission'];
							//echo "<br>site_commission: ".$site_commission;
							//echo "<br>admin id: ".Config::get('generalConfig.admin_id');
							//exit;


							// If it is not refund transaction
							if(!$this->biller->getIsRefundTransaction())
							{
								if(isset($invResp['primary']) && $invResp['primary'] != '')
								{
									$site_commission = $invResp['primary'];
//									$seller_amount = (isset($invResp['secondary']) && $invResp['secondary'] != '' && $invResp['secondary'] > 0) ? $invResp['secondary'] : 0;
								}
								else
								{
									$site_commission = $order_det['site_commission'];
								}

								$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
								$credit_obj->setCurrency($common_invoice_details['currency']);
								$credit_obj->setAmount($site_commission);
								$response = $credit_obj->creditAndDebit('amount', 'plus');

								$fin_res = json_decode($response);

								$site_transactions = $payobj->setSiteTransactions();

								$payobj->sendInvoiceMailToUser();

							}
						}
					}
				}
			}
		}
		$payobj->closeErrorLogFile();
		Log::info("========================= Notification Controller Ends here ==================================");
	}
}
