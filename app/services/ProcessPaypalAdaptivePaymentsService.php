<?php
class ProcessPaypalAdaptivePaymentsService
{
	private $ipn_data 				= array();
	private $payment_details		= array();
	private $paypal_adaptive_transaction_details		= array();
	private $payment_amount_details	= array();
	private $wallet_transaction_details = array();
	private $payment_method			= 'Chained';
	private $paypal_trans			= array();
	private $invoice_item_details	= array();
	private $receiver_details       = array();
	private $order_details			= array();
	private $common_invoice_details	= array();
	private $errno					= 0;
	private $primary_receiver		= 0; //by default for parallel
	private $secondary_receiver		= 1; //by default for parallel
	private $seller_transaction_id	= '';
	private $site_transaction_id		= '';
	public  $authorRefundType = '';
	public  $siteRefundType = '';
	public  $log_details = true;

	/**
	 * PaypayAdaptivePaymentsProcess::__construct()
	 * Constructor to update the DB and CFG vars
	 *
	 */
	function __construct()
	{
		//$this->PaypalAdaptivePayments = Paypaladaptive::initialize();
		$this->paypal_transaction = Paypaladaptive::initializePaypalTransaction();
		$this->shop_order_obj = Webshoporder::initialize();
		$this->invoice_obj = Webshoporder::initializeInvoice();
		$this->product_obj = Products::initialize();
		$this->common_invoice_obj =  Products::initializeCommonInvoice();
		$this->manage_credits_obj =  Products::initializeManageCredits();
	}

	public function setIpnData($ipn_data){
		$this->ipn_data = $ipn_data;
	}

	public function setPaymentDetailsData($payment_details){
		$this->payment_details = $payment_details;
	}

	public function setPaypalAdaptivePaymentTransactionDetails($paypalAdaptivePaymentTransactionDetails)
	{
		//echo "sdfsadfsdfds<pre>";print_r($paypalAdaptivePaymentTransactionDetails);echo "</pre>";exit;
		$this->paypal_adaptive_transaction_details = $paypalAdaptivePaymentTransactionDetails;
	}
	public function getPaypalAdaptivePaymentTransactionDetails()
	{
		return $this->paypal_adaptive_transaction_details;
	}

	public function setPaymentMethod($payment_method)
	{
		$this->payment_method = $payment_method;
	}

	public function getPaymentMethod()
	{
		return $this->payment_method;
	}

	public function setOrderDetails($order_id = null){

		if(is_null($order_id))
		{
			$paypal_adaptive_log['pay_key'] = $this->ipn_data['pay_key'];
			$paypal_adaptive_log['tracking_id'] = $this->payment_details['trackingId'];

			$this->shop_order_obj->resetFilterKeys();
			$this->shop_order_obj->setFilterPayKey($this->ipn_data['pay_key']);
			$this->shop_order_obj->setFilterTrackingId($this->payment_details['trackingId']);
			$order_details = $this->shop_order_obj->getShopOrderDetails();
			//$order_details = ShopOrder::whereRaw('pay_key = ? and tracking_id = ?', array($this->ipn_data['pay_key'], $this->payment_details['trackingId']))->first()->toArray();
		}
		else
		{
			$this->shop_order_obj->resetFilterKeys();
			$this->shop_order_obj->setFilterOrderId($order_id);
			$order_details = $this->shop_order_obj->getShopOrderDetails();
			//$order_details = ShopOrder::whereRaw('id = ?', array($order_id))->first()->toArray();
		}
		if(!empty($order_details))
		{
			$this->order_details = $order_details;
			return true;
		}
		return false;
	}

	public function getOrderDetails(){
		return $this->order_details;
	}

	public function getOrderReceiver(){
		$user_id = isset($this->order_details['seller_id'])?$this->order_details['seller_id']:0;
		return $user_id;
	}

	public function updateOrderStatus($status='payment_completed')
	{
		//echo "<pre>";print_r($this->order_details);echo "</pre>";
		//echo "order_id: ".$this->order_details['id'];
		//exit;
		$this->shop_order_obj->setOrderId($this->order_details['id']);
		$this->shop_order_obj->setOrderStatus($status);
		$this->shop_order_obj->add();
		//ShopOrder::where('id','=',$this->order_details['id'])->update(array('order_status' => $status));
		//$order = MpOrder::where('id','=',$this->order_details['id'])->get()->toArray();//update(array('order_status' => $status));
		//echo "<pre>";print_r($order);echo "</pre>";
	}

	public function setPaymentReceiverDetails($receivers) {
		$this->receiver_details = $receivers;
	}

	public function getPaymentReceiversTransaction()
	{
		$receivers = $this->receiver_details;
		if(is_array($receivers) && !empty($receivers))
		{

		}
	}

	public function updateReceiversTransactionId(){

		$receivers = $this->receiver_details;
		$common_invoice_id = $this->common_invoice_details['common_invoice_id'];
		$pay_key = $this->ipn_data['pay_key'];
		$this->shop_order_obj->updateReceiversTransactionId($receivers, $common_invoice_id, $this->ipn_data['pay_key']);
	}

	public function generateInvoiceForOrder(){

		$resp = array();
		$order_item_details = $this->shop_order_obj->getOrderItemDetailsForOrder($this->order_details['id']);
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
			$this->common_invoice_obj->setUserId($this->order_details['buyer_id']);
			$this->common_invoice_obj->setReferenceType('Products');
			$this->common_invoice_obj->setReferenceId($this->order_details['id']);
			$this->common_invoice_obj->setCurrency($default_curreny);
			$this->common_invoice_obj->setAmount($this->order_details['total_amount']);
			$this->common_invoice_obj->setStatus('Paid');
			$this->common_invoice_obj->addCommonInvoice();*/
		}
	}

	public function updateProductSold($product_id, $item_qty){
		$this->product_obj->updateProductSold($product_id, $item_qty);
	}

	public function print_fields()
	{
	//			print('<pre>');
	//			echo "<br>** IPN data<br>";
	//			print_r($this->ipn_data);
	//			echo "<br>** payment details<br>";
	//			print_r($this->payment_details);
	//			echo "<br>** invoice details<br>";
	//			print_r($this->invoice_item_details);
	//			print('</pre>');
		$str = print_r($this->ipn_data, 1);
		$str .=  print_r($this->payment_details, 1);
		$str .= print_r($this->invoice_item_details, 1);
		$str .= print_r($this->common_invoice_details, 1);
		$this->writetoTempFile($str);
	}

	public function getInvoicesForOrder(){
		$invoices_details = $this->invoice_obj->getInvoicesForOrder($this->order_details['id']);
		$this->order_invoices = $invoices_details;
		return $invoices_details;
	}

	public function setWalletTransactionDetails($wallet_transaction_details = array()){
		$this->wallet_transaction_details = $wallet_transaction_details;
	}

	public function setSiteTransactions($payment_method = 'Chained')
	{
		$payment_method = $this->getPaymentMethod();

		$order_receivers = $this->getOrderReceiversForOrder();

		$site_transaction_id = $seller_transaction_id =  '';
		foreach($order_receivers as $receiver)
		{
			if($receiver['is_admin'] == 'Yes')
				$site_transaction_id = $receiver['txn_id'];
			else
				$seller_transaction_id = $receiver['txn_id'];
		}
		$this->site_transaction_id = $site_transaction_id;
		$this->seller_transaction_id = $seller_transaction_id;

		if($payment_method == 'Chained')
		{
			//$this->addSiteTransactionDetails('BuyerCredit');
			$this->addSiteTransactionDetails('BuyerDebit');
			$this->addSiteTransactionDetails('SiteCreditFromBuyer');
			$this->addSiteTransactionDetails('SiteDebitSellerAmount');
			$this->addSiteTransactionDetails('SellerCreditFromSite');
		}
		else
		{
			$this->addSiteTransactionDetails('BuyerCredit');
			$this->addSiteTransactionDetails('BuyerDebit');
			$this->addSiteTransactionDetails('ParallelSellerCreditFromBuyer');
			$this->addSiteTransactionDetails('ParallelSiteCreditFromBuyer');
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

	public function setWalletAccountTransaction($wallet_details, $transaction_key = 'addwalletamount')
	{
		$details_arr = 	array ('date_added' => date('Y-m-d H:i:s'),
								'user_id' => '',
								'transaction_type' => 'Debit',
								'amount' => '',
								'currency' => $this->common_invoice_details['currency'],
								'transaction_key' => $transaction_key,
								'reference_content_id' => '',
								'reference_content_table' => 'common_invoice',
								'invoice_id' => '',
								'purchase_code' => '',
								'related_transaction_id' => '',
								'status' => '',
								'transaction_notes' => ''	);
		//$details_arr = array();
		if(!empty($wallet_details))
		{
			foreach($wallet_details as $wallet)
			{
				$details_arr['user_id'] = $wallet['user_id'];
				$details_arr['amount'] = $wallet['amount'];
				$details_arr['transaction_type'] = ucfirst($wallet['credit_or_debit']);
				if($wallet['credit_or_debit']=='credit')
					$details_arr['transaction_notes'] = 'Amount credited to your wallet account';
				else
					$details_arr['transaction_notes'] = 'Amount debited from your wallet account';

				$SiteTransactionDetails = new SiteTransactionDetails();
				$site_transaction_id = $SiteTransactionDetails->addNew($details_arr);
			}
		}
	}

	public function setWalletTransaction($wallet_details = array(), $transaction_key='purchase')
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();
		$details_arr = 	array ('date_added' => new DateTime,
								'user_id' => '',
								'transaction_type' => 'Debit',
								'amount' => '',
								'currency' => isset($this->order_details['currency'])?$this->order_details['currency']:$common_invoice_details['currency'],
								'transaction_key' => $transaction_key,
								'reference_content_id' => isset($this->order_details['id'])?$this->order_details['id']:$common_invoice_details['common_invoice_id'],
								'reference_content_table' => 'shop_order',
								'invoice_id' => $common_invoice_details['common_invoice_id'],
								'purchase_code' => '',
								'related_transaction_id' => '',
								'status' => isset($this->order_details['order_status'])?$this->order_details['order_status']:$common_invoice_details['status'],
								'transaction_notes' => '',
								'transaction_id' => '',
								'paypal_adaptive_transaction_id' => ''	);
		//$details_arr = array();
		if(!empty($wallet_details))
		{
			$updated_transactions = $wallet_details+$details_arr;
			//echo "<pre>";print_r($updated_transactions);echo "</pre>";exit;
			$SiteTransactionDetails = new SiteTransactionDetails();
				$site_transaction_id = $SiteTransactionDetails->addNew($updated_transactions);
			//echo "<pre>";print_r($updated_transactions);echo "</pre>";
		}
	}

	public function setPaymentAmountDetails(){
		$payment_method = $this->getPaymentMethod();
		$primary_amount = $this->getPaymentInfo('receiver.amount', 'primary');
		$secondary_amount = $this->getPaymentInfo('receiver.amount', 'secondary');
		if($payment_method == 'Chained')
		{
			$total_amount = $primary_amount;
			$seller_amount = $secondary_amount;
			$site_commission = $primary_amount-$secondary_amount;
			$credit_to_admin = $primary_amount;
			$credit_to_seller = $secondary_amount;
		}
		else
		{
			$total_amount = $primary_amount+$secondary_amount;
			$seller_amount = $secondary_amount;
			$site_commission = $primary_amount;
			$credit_to_admin = $primary_amount;
			$credit_to_seller = $secondary_amount;
		}
		$this->payment_amount_details = compact('total_amount','seller_amount','site_commission', 'credit_to_admin', 'credit_to_seller');
	}

	public function getPaymentAmountDetails(){
		return $this->payment_amount_details;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::addSiteTransactionDetails()
	 *
	 * @param string $transaction_key
	 * @return
	 */
	public function addSiteTransactionDetails($transaction_key = '')
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
									'payment_type' => 'paypal'	);

		$purchaseDetails = $this->getItemTransactionDetail($transaction_key);

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

	/**
	 * PaypayAdaptivePaymentsProcess::getItemTransactionDetail()
	 * Get item transactoin detail
	 *
	 * @param mixed $transaction_key
	 * @return
	 */
	private function getItemTransactionDetail($transaction_key)
	{
		$data = array();
		$data['currency'] 					= $this->payment_details['currencyCode'];
		$data['reference_content_id'] 		= $this->order_details['id'];
		$data['invoice_id'] 				= $this->order_details['id'];
		//$data['item_id'] 					= $this->invoice_detail['item_id'];
		$data['reference_content_table']	= 'shop_order';
		$data['status'] 					= $this->order_details['order_status'];

		//echo "<pre>";print_r($this->order_details);echo "</pre>";exit;
		$total_item_amount	= $this->payment_amount_details['total_amount'];

		//This is to include the wallet amount (if purchased using both wallet and paypal) while debit from buyer
		if(!isset($this->wallet_transaction_details['buyer']['amount']))
			$this->wallet_transaction_details['buyer']['amount'] = 0;
		$buyer_debit_amount = $total_item_amount + $this->wallet_transaction_details['buyer']['amount'];

		$receiver_amount	= $this->payment_amount_details['seller_amount'];
		$site_commission	= $this->payment_amount_details['site_commission'];
		$credit_to_admin	= $this->payment_amount_details['credit_to_admin'];
		$credit_to_seller	= $this->payment_amount_details['credit_to_seller'];
		//$receiver_amount	= $total_item_amount - $site_commission;
		//echo "dsds<pre>";print_r($this->paypal_adaptive_transaction_details);echo "</pre>";exit;
		$payment_type = 'purchase';
		$site_payment_type = 'purchase_fee';
		switch ($transaction_key)  {

			case 'BuyerCredit':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Amount credited to wallet account for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $total_item_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->order_details['buyer_id'];
					$data['transaction_id']			= isset($this->paypal_adaptive_transaction_details['buyer_trans_id'])?$this->paypal_adaptive_transaction_details['buyer_trans_id']:'';
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'BuyerDebit':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Debited amount from paypal account for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $buyer_debit_amount;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->order_details['buyer_id'];
					$data['transaction_id']			= isset($this->paypal_adaptive_transaction_details['buyer_trans_id'])?$this->paypal_adaptive_transaction_details['buyer_trans_id']:'';
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'SiteCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $site_payment_type;
					$data['transaction_notes'] 		= 'Credited amount to paypal account from buyer for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_admin;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['transaction_id']			= $this->site_transaction_id;
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'SiteDebitSellerAmount':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $site_payment_type;
					$data['transaction_notes'] 		= 'Debited amount from your paypal account to transfer seller amount except site commission for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'SellerCreditFromSite':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Credited amount to your paypal account for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->order_details['seller_id'];
					$data['transaction_id']			= $this->seller_transaction_id;
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;


			case 'ParallelSellerCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Credited amount to your paypal acccount for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_seller;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= $this->order_details['seller_id'];
					$data['transaction_id'] 		= $this->seller_transaction_id;
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'ParallelSiteCreditFromBuyer':
					$data['transaction_type'] 		= 'Credit';
					$data['transaction_key'] 		= $site_payment_type;
					$data['transaction_notes'] 		= 'Credited site commission amount to paypal account for the order: '.CUtil::setOrderCode($this->order_details['id']);
					$data['related_transaction_id'] = 0;
					$data['amount'] 				= $credit_to_admin;//$this->getPaymentInfo('receiver.amount', 'primary');
					$data['user_id'] 				= Config::get('generalConfig.admin_id');
					$data['transaction_id']			= $this->site_transaction_id;
					$data['paypal_adaptive_transaction_id']	= isset($this->paypal_adaptive_transaction_details['id'])?$this->paypal_adaptive_transaction_details['id']:'';
				break;

			case 'PurchaseItem':
					$data['transaction_type'] 		= 'Debit';
					$data['transaction_key'] 		= $payment_type;
					$data['transaction_notes'] 		= 'Purchase Item Debit';
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
					$data['user_id'] 				= Config::get('generalConfig.admin_id');;
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

	/**
	 * PaypayAdaptivePaymentsProcess::getPaymentInfo()
	 *
	 * @param mixed $key
	 * @param mixed $receiver
	 * @return
	 */
	private function getPaymentInfo($key, $receiver) {

		$pkey = 'paymentInfoList.paymentInfo(' ;

		if (strcmp($receiver, 'primary') == 0) {
			$pkey .= $this->getPrimaryReceiverIndex();
		} elseif (strcmp($receiver, 'secondary') == 0) {
			$pkey .= $this->getSecondaryReceiverIndex();
		}

		$pkey .= ').' . $key;

		if (isset($this->payment_details[$pkey])) {
			return $this->payment_details[$pkey];
		} else {
			return false;
		}
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getPrimaryReceiverIndex()
	 * Get primary receiver index
	 *
	 * @return
	 */
	public function getPrimaryReceiverIndex()
	{
		return $this->primary_receiver;
	}


	/**
	 * PaypayAdaptivePaymentsProcess::getSecondaryReceiverIndex()
	 * Get secondary receiver index
	 *
	 * @return
	 */
	public function getSecondaryReceiverIndex()
	{
		return $this->secondary_receiver;
	}

	/**
	 * PaypayAdaptivePaymentsProcess::getRelativeTransactionId()
	 * Get relative transaction ID
	 *
	 * @param mixed $invoice_id
	 * @param mixed $transaction_key
	 * @return
	 */
	public function getRelativeTransactionId($invoice_id, $transaction_key)
	{
		$sql = 	' SELECT site_transaction_id FROM '.$this->CFG['db']['tbl']['site_transaction_details'].
				' WHERE invoice_id = '.$this->dbObj->Param('invoice_id').
				' AND transaction_key = '.$this->dbObj->Param('transaction_key');

		$stmt = $this->dbObj->Prepare($sql);
		$rs = $this->dbObj->Execute($stmt, array($invoice_id, $transaction_key));

		if (!$rs)
	        trigger_error($this->dbObj->ErrorNo() . ' ' . $this->dbObj->ErrorMsg(), E_USER_ERROR);

		if ($row = $rs->FetchRow()) {
			return $row['site_transaction_id'];
		}

		return false;
	}

	public function updateReceiversPaymentStatus($status = 'completed', $payment_type = 'Paypal'){
		$common_invoice_id = $this->common_invoice_details['common_invoice_id'];
		$this->shop_order_obj->updateReceiversStatus($common_invoice_id, $status, $payment_type);
	}

	public function closeErrorLogFile()
	{
		if($this->log_details)
		{
			if ($this->fp)
			{
				fclose($this->fp);
			}
		}
	}

	public function chkAndCreateFolder($folderName)
	{
		//echo $folderName;//exit;
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

	public function createErrorLogFile($prefixFileName='')
	{
		if($this->log_details)
		{
			$temp_dir = public_path().'/files/temp_payment_log'.'/';
			$this->chkAndCreateFolder($temp_dir);
			$temp_file_path = $temp_dir.$prefixFileName.'_'.time().'_'.rand(1,100).'.txt';
			@$this->fp = fopen($temp_file_path, 'w');
		}
	}

	public function writetoTempFile($str)
	{
		if($this->log_details)
		{
			if(@!$this->fp)
			{
				$this->createErrorLogFile();
			}
			@fwrite($this->fp, $str);
		}
	}

	public function sendInvoiceMailToUserOld()
	{
		$invoice_obj = Webshoporder::initializeInvoice();

		$details = array();
		//$this->service = new MyAccountListingService();
		$invoice_products = $invoice_obj->getOrderUserInvoicesList($this->order_details['id']);
		foreach($invoice_products as $key => $product)
		{
			//$arr = $this->service->getSelectedProductServicesList($this->order_details['id'], $product['product_id']);
			$invoice_products[$key]['product_service'] = '';
			/*if(!empty($arr))
				$invoice_products[$key]['product_service'] = implode(', ', array_map(function($el){ return ucfirst($el['service_name']); }, $arr));*/
		}
		$details['order'] = $this->order_details;

		$details['order']['buyer_name'] = CUtil::getUserFields($this->order_details['buyer_id'], 'first_name');

		//mail to buyer
		$details['to_email'] = CUtil::getUserFields($this->order_details['buyer_id'], 'email');
		$details['subject'] = Lang::get('email.new_order_placed');
		$details['invoices'] = $invoice_products;
		$this->sendPurchaseMail('sale','emails.orderNotificationToBuyer', $details);

		//if(CUtil::isUserAllowedToAddProduct()) {
			//mail to admin
			$details['to_email'] = Config::get("generalConfig.invoice_email");
			$details['subject'] = Lang::get('email.new_order_placed');
			$details['invoices'] = $invoice_products;
			$this->sendPurchaseMail('sale','emails.orderNotificationToAdmin', $details);
		//}

		//mail to sellers
		$seller_details = array();
		foreach($invoice_products as $invoices)
		{
			$seller_details[$invoices['item_owner_id']][] = $invoices;
		}
		foreach($seller_details as $key => $invoices)
		{
			$details['to_email'] = $invoices[0]['seller_email'];
			$details['subject'] = Lang::get('email.new_order_placed');
			$this->sendPurchaseMail('sale','emails.orderNotificationToSeller', $details);
		}
	}

	public function sendInvoiceMailToUser()
	{
		//$this->setCommonInvoiceDetails();
		$common_invoice_details = $this->getCommonInvoiceDetails();
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

				if($common_invoice_details['pay_key']!='')
				{
					$payment_gateway = 'paypal';
					$payment_gateway_text= 'Paypal';
					if($common_invoice_details['is_credit_payment']=='Yes')
					{
						$payment_gateway= 'paypal_with_wallet';
						$payment_gateway_text= 'Paypal With Wallet';
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
		$d_arr['from_email']= $from_email;
		$d_arr['to_email'] 	= $data['to_email'];
		$d_arr['subject'] 	= Config::get('generalConfig.site_name')." - ".$data['subject'];
		$d_arr['content'] 	= $view;
		$d_arr['key_type'] 	= $key;
		$d_arr['method'] 	= $method;
		$d_arr['status'] 	= 'pending';
		$d_arr['data'] 		= serialize($data);
		$d_arr['date_added']= new DateTime;
		/*$mailer = new MailSystemAlert;
		$arr = $mailer->filterTableFields($d_arr);
		$id = $mailer->insertGetId($arr);*/

		$data1['to_email'] 	= $d_arr['to_email'];
		$data1['from_name'] = $d_arr['from_name'];
		$data1['from_email']= $d_arr['from_email'];
		$data1['subject'] 	= $d_arr['subject'];
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

	//Credits function start
	public function setCommonInvoiceDetails($common_invoice_id = null){

		if(is_null($common_invoice_id))
		{
			$this->common_invoice_obj->resetFilterKeys();
			$this->common_invoice_obj->setFilterPayKey($this->ipn_data['pay_key']);
			$this->common_invoice_obj->setFilterTrackingId($this->payment_details['trackingId']);
			$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);
		}
		else
		{
			$this->common_invoice_obj->resetFilterKeys();
			$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);
		}
		if(!empty($common_invoice_details))
		{
			$this->common_invoice_details = $common_invoice_details;
			return true;
		}
		return false;
	}

	public function getCommonInvoiceDetails(){
		if(isset($this->common_invoice_details['amount']) && isset($this->common_invoice_details['paypal_amount']) && !isset($this->common_invoice_details['wallet_credit_used']))
		{
			$wallet_amount = $this->common_invoice_details['amount'] - $this->common_invoice_details['paypal_amount'];
			$wallet_amount = ($wallet_amount > 0)?$wallet_amount:0;
			$this->common_invoice_details['wallet_credit_used'] = $wallet_amount;
		}
		return $this->common_invoice_details;
	}

	public function setCommonInvoiceDetailsByReference($reference_type, $reference_id) {
		$this->common_invoice_obj->resetFilterKeys();
		$this->common_invoice_obj->setFilterReferenceType($reference_type);
		$this->common_invoice_obj->setFilterReferenceId($reference_id);
		$common_invoice_details = $this->common_invoice_obj->getCommonInvoiceDetailsById();
		if(!empty($common_invoice_details))
		{
			$this->common_invoice_details = $common_invoice_details;
			return true;
		}
		return false;
	}

	public function updateCommonInvoiceStatus($input_data = array())
	{
		$common_invoice_id = $this->common_invoice_details['common_invoice_id'];
		$data['is_credit_payment'] = isset($input_data['is_credit_payment']) ? $input_data['is_credit_payment'] : 'No';
		$data['paypal_amount'] = isset($input_data['paypal_amount']) ? $input_data['paypal_amount'] : $this->order_details['total_amount'];
		$data['status'] = 'Paid';
		$data['date_paid'] = DB::raw('NOW()');
		$this->common_invoice_obj->updateCommonInvoiceDetails($common_invoice_id, $data);
	}

	public function updateCommonInvoiceDetails($data)
	{
		$common_invoice_id = $this->common_invoice_details['common_invoice_id'];
		$this->common_invoice_obj->updateCommonInvoiceDetails($common_invoice_id, $data);
	}

	public function updateCreditsLogStatus($status = 'Paid')
	{
		$reference_type = $this->common_invoice_details['reference_type'];
		if($reference_type == 'Credits' || $reference_type == 'Usercredits') {
			$credit_id = $this->common_invoice_details['reference_id'];
			$data['paid'] = 'Yes';
			$data['date_paid'] = DB::raw('NOW()');
			$this->manage_credits_obj->updateCreditsLogDetails($credit_id, $data);
		}
	}

	public function sendCreditsInvoiceMailToUser()
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();
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

	/*public function sendInsufficientBalanceMailToUser()
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();

		if(count($common_invoice_details) > 0) {
			//mail to user
			$details['user_name'] = CUtil::getUserFields($common_invoice_details['user_id'], 'first_name');
			$details['to_email'] = CUtil::getUserFields($common_invoice_details['user_id'], 'email');
			$details['subject'] = 'Insufficient balance';
			$details['invoices'] = $common_invoice_details;
			Log::info('Ready to send mail start');
			$this->sendPurchaseMail('credit','emails.creditsInsufficientNotificationToUser', $details);

			//mail to admin
			$details['to_email'] = Config::get("generalConfig.invoice_email");
			$details['subject'] = 'Insufficient balance';
			$details['invoices'] = $common_invoice_details;
			$this->sendPurchaseMail('credit','emails.creditsInsufficientNotificationToAdmin', $details);
		}
	}*/

	public function sendInsufficientBalanceMailToAdmin($order_receiver_details)
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();
		$details['to_email'] = Config::get("generalConfig.invoice_email");
		$details['subject'] = Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('debit','emails.purchaseInsufficientNotificationToAdmin', $details);
	}

	public function sendInsufficientBalanceMailToSeller($order_receiver_details)
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();
		$details['to_email'] = $order_receiver_details['seller_details']['email'];
		$details['subject'] = Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('debit','emails.purchaseInsufficientNotificationToSeller', $details);
	}

	public function sendInsufficientBalanceMailToBuyer($order_receiver_details)
	{
		$common_invoice_details = $this->getCommonInvoiceDetails();
		$details['to_email'] = $order_receiver_details['buyer_details']['email'];
		$details['subject'] = Lang::get('email.order_cancelled_due_to_insufficient_balance');
		$details['invoices'] = $common_invoice_details;
		$details['receiver_details'] = $order_receiver_details;

		$this->sendPurchaseMail('credit','emails.purchaseInsufficientNotificationToBuyer', $details);
	}

	public function addWithdrawalRequest($inputs)
	{
		$txn_fee = 0;
		$transfer_thru = 'paypal';
		$payment_type = '';

		if($transfer_thru == "paypal") {
			$payment_type = 'Paypal';
			$txn_fee = Config::get("payment.withdraw_paypal_transaction_fee_usd");
		}
		$user_id = $inputs['user_id'];

		$withdraw_obj = Credits::initializeWithDrawal();
		$withdraw_obj->setUserId($user_id);
		$withdraw_obj->setCurrency($inputs['currency']);
		$withdraw_obj->setAmount($inputs['paypal_amount']);
		$withdraw_obj->setFee($txn_fee);
		$withdraw_obj->setPaymentType($payment_type);
		$withdraw_obj->setPayToUserAccountInfo($inputs['sender_email']);
		$withdraw_obj->setDateAdded(DB::raw('NOW()'));
		$withdraw_obj->setStatus('Active');
		$withdrwal_requests = $withdraw_obj->addWithdrawalRequest();
		return $withdrwal_requests;
	}

	public function getOrderReceiversForOrder($common_invoice_id = '', $pay_key = '')
	{
		if($pay_key=='')
			$pay_key = $this->ipn_data['pay_key'];

		$receivers = $this->shop_order_obj->getOrderReceiversForOrder($common_invoice_id, $pay_key);
		$this->order_receivers = $receivers;
		return $receivers;
	}

	public function getSingleOrderReceiverDetails($email, $pay_key = '')
	{
		if($pay_key == '')
			$pay_key = $this->ipn_data['pay_key'];

		//$pay_key
		$receiver = $this->shop_order_obj->getSingleOrderReceiverDetails($email, $pay_key);
		return $receiver;
	}
}
?>