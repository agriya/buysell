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
class AdminPurchasesController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }

	public function getIndex()
    {
    	$inputs = Input::all();

		$search_order_statuses = array('' => trans('common.select_option'),
							'payment_completed' => Lang::get('myPurchases.status_txt_payment_completed'),
							'not_paid' => Lang::get('myPurchases.status_unpaid'),
							'payment_cancelled' => Lang::get('myPurchases.status_txt_payment_cancelled'),
							'refund_completed' => Lang::get('myPurchases.status_txt_refund_completed'),
							'refund_rejected' => Lang::get('myPurchases.status_txt_refund_rejected'),
							'refund_requested' => Lang::get('myPurchases.status_txt_refund_requested'));
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$order_obj = Webshoporder::initialize();
		//$order_obj->setFilterBuyerId($logged_user_id);
		$order_obj->setFilterNotStaus('draft');
		$order_obj->setOrderPagination(20);
		$shopService = new ShopService();
		$shopService->setSearchOrders($order_obj, $inputs);
		$order_details = $order_obj->contents();
		if(count($order_details) > 0)
		{
			foreach($order_details as $key => $order)
			{
				$order_items = $order_obj->getOrderitemDetails($order->id);
				foreach($order_items as $ikey => $item)
				{
					try{
						$prod_obj = Products::initialize($item->item_id);
						$product_details = $prod_obj->getProductDetails();
					}
					catch(Exception $e)
					{
						$product_details = array();
					}
					$order_items[$ikey]->product_details = $product_details;

				}
				$order_details[$key]->order_items = $order_items;
			}
		}
		if(count($order_details) > 0)
		{
			$order_details_pr = $order_details->toArray();
			//echo "<pre>";print_r($order_details_pr);echo "</pre>";
		}
		$product_obj = Products::initialize();
		$productService = new ProductService();
		$this->header->setMetaTitle(trans('meta.admin_purchases_list_title'));
		return View::make('admin.purchasesList', compact('order_details','order_obj','product_obj','productService','$order_items', 'search_order_statuses'));
	}
	public function getOrderDetails($order_id = null)
	{
		if(is_null($order_id))
			return Redirect::action('AdminPurchasesController@getIndex')->with('error_message','Select a valid order');
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$order_obj = Webshoporder::initialize();
		//$order_obj->setFilterBuyerId($logged_user_id);
		$order_obj->setFilterOrderId($order_id);
		$order_details = $order_obj->contents();

		$invoice_obj = Webshoporder::initializeInvoice();
		if(count($order_details) > 0)
		{
			foreach($order_details as $key => $order)
			{
				if(isset($order->order_status) &&  $order->order_status == 'draft')
					return Redirect::action('AdminPurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));
				$has_invoice = false;
				$invoices = $invoice_obj->getInvoicesForOrder($order->id);
				if(count($invoices) > 0) { // If invoice exits then take product values from invoice table
					//echo "<pre>";print_r($invoices);echo "</pre>";
					$has_invoice = true;
					foreach($invoices as $ikey => $item)
					{
						try
						{
							$prod_obj = Products::initialize($item['item_id']);
							$prod_obj->setIncludeBlockedUserProducts(true);
							$prod_obj->setIncludeDeleted(true);
							$product_details = $prod_obj->getProductDetails();
						}
						catch(Exception $e)
						{
							$product_details = array();
						}
						$invoices[$ikey]['product_details'] = $product_details;
					}
					//if(Config::get('generalConfig.user_allow_to_add_product')) {
					$shop_obj = Products::initializeShops();
					$shop_details = $shop_obj->getShopDetails($order->seller_id);
					//}
					$order_details[$key]->shop_details = $shop_details;
					$order_details[$key]->order_invoices = $invoices;
				}
				else { // If invoice not exits then take product values from order items table
					$order_items = $order_obj->getOrderitemDetails($order->id);
					foreach($order_items as $ikey => $item)
					{
						$prod_obj = Products::initialize($item->item_id);
						$product_details = $prod_obj->getProductDetails();
						$order_items[$ikey]['product_details'] = $product_details;
					}
					$order_details[$key]->order_invoices = $order_items;
				}
				$order_details[$key]->has_invoice = $has_invoice;

				$order_items = $order_obj->getOrderitemDetails($order->id);
				$order_details[$key]->order_items = $order_items;

				$order_details[$key]->shipping_details = $shipping_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_id));
			}
		}else{
			return Redirect::action('AdminPurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));
		}

		$product_obj = Products::initialize();
		$productService = new ProductService();
		$this->header->setMetaTitle(trans('meta.view_purchases'));
		return View::make('admin.orderDetails', compact('order_details','order_obj','product_obj','productService', 'logged_user_id'));
	}

	public function getResponseCancel()
	{
		$invoice_id = Input::has('invoice_id') ? Input::get('invoice_id') : '';
		$refund_action = Input::has('refund_action') ? Input::get('refund_action') : '';
		$invoice_details = $product_details = array();
		$productService = new ProductService();
		if($invoice_id > 0) {
			/*$common_invoice_obj = Products::initializeCommonInvoice();
			$common_invoice_details = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId($order_id);*/

			$invoice_obj = Webshoporder::initializeInvoice();
			$invoice_details = $invoice_obj->getInvoiceDetails($invoice_id);
			if(count($invoice_details) > 0) {
				$prod_obj = Products::initialize($invoice_details['item_id']);
				$product_details = $prod_obj->getProductDetails();
			}
		}
		return View::make('admin.responseCancellationPopup',compact('invoice_id', 'invoice_details', 'product_details', 'refund_action', 'productService'));
	}

	public function postResponseCancel()
	{
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";//exit;
		$rules = array('refund_action' => 'required');
		$messages = array('refund_action.required' => 'Action required');
//		if(strtolower($inputs['refund_action']) == "yes") {
//			$rules += array('seller_refund_amount' => 'required_without:seller_refund_paypal_amount|IsValidPrice|numeric|Min:0', 'seller_refund_paypal_amount' => 'required_without:seller_refund_amount|IsValidPrice|numeric|Min:0');
//			$messages += array('required_without' => 'Either Paypal amount or Credit amount mandatory');
//			$messages += array('seller_refund_amount.is_valid_price' => 'Credit Amount invalid format');
//			$messages += array('seller_refund_amount.numeric' => 'Credit Amount invalid format');
//			$messages += array('seller_refund_amount.min' => 'Credit Amount must be atleast 0.');
//
//			$messages += array('seller_refund_paypal_amount.is_valid_price' => 'Paypal Amount invalid format');
//			$messages += array('seller_refund_paypal_amount.numeric' => 'Paypal Amount invalid format');
//			$messages += array('seller_refund_paypal_amount.min' => 'Paypal Amount must be atleast 0.');
//		}
		//echo '<pre>';print_r($messages);
		$validator = Validator::make($inputs, $rules, $messages);
		if ($validator->fails()) {
			echo "error|~~|".$validator->messages()->first();exit;
		}


		if(is_null($inputs['invoice_id']) || $inputs['invoice_id'] == ""){
			echo "error|~~|Select valid invoice";exit;
		}
		$invoice_id = $inputs['invoice_id'];
		$item_id = $inputs['item_id'];
		$invoice_obj = Webshoporder::initializeInvoice();
		$invoice_det = $invoice_obj->getInvoiceDetails($invoice_id);
		//echo "<pre>";print_r($invoice_det);echo "</pre>";exit;
		if(!isset($invoice_det) || count($invoice_det) <=0){
			echo "error|~~|Select valid invoice";exit;
		}
		if($invoice_det && count($invoice_det) > 0)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$group_id = BasicCUtil::getUserGroupId($logged_user_id);
			$admin_id = Config::get('generalConfig.admin_id');
			$user_allow_to_add_product = Config::get('generalConfig.user_allow_to_add_product');
			$invoice_det['item_total_amount'] = round($invoice_det['item_total_amount'], 2);
			$invoice_det['seller_amount'] = round($invoice_det['seller_amount'], 2);
			$invoice_det['item_site_commission'] = round($invoice_det['item_site_commission'], 2);

			//$seller_refund_amount = ($inputs['seller_refund_amount'] != '') ? $inputs['seller_refund_amount'] : 0;
			//$seller_refund_paypal_amount = ($inputs['seller_refund_paypal_amount'] != '') ? $inputs['seller_refund_paypal_amount'] : 0;

			if($group_id != $admin_id){
				echo "error|~~|You are not authorized to access this invoice";exit;
			}

			if(strtolower($invoice_det['is_refund_approved_by_admin']) != "no"){
				echo "error|~~|Cancel action already taken";exit;
			}

//			if($user_allow_to_add_product)
//			{
				//if(strtolower($invoice_det['is_refund_approved_by_seller']) == "rejected")
				//{
					//echo "error|~~|Seller already rejected this cancellation request";exit;
				//}

				//if($inputs['refund_amount'] > $invoice_det['item_site_commission'])
				//{
				//	echo "error|~~|Maximum cancel amount from site commission is ".$invoice_det['item_site_commission'];exit;
				//}

				//if($inputs['refund_amount'] > $invoice_det['seller_amount'])
				//{
				//	echo "error|~~|Maximum cancel amount from seller is ".$invoice_det['item_site_commission'];exit;
				//}
//			}
//			else
//			{
				//if($inputs['refund_amount'] > $invoice_det['item_total_amount'])
				//{
				//	echo "error|~~|Maximum cancel amount from site commission is ".$invoice_det['item_total_amount'];exit;
				//}
//			}
			//echo "<pre>";print_r($invoice_det);echo "</pre>";exit;
			$refund_action = $inputs['refund_action'];
			$invoice_status = 'refund_requested';
			$order_status = 'refund_requested';
			$seller_refund_amount = $invoice_det['seller_amount'] - $invoice_det['discount_ratio_amount'];
			if(strtolower($refund_action) == "yes" && strtolower($invoice_det['payment_gateway_type']) == 'wallet')
			{

//				if(($seller_refund_amount + $seller_refund_paypal_amount) > $invoice_det['seller_amount']) {
//					echo "error|~~|Refund amount should not greater than purchased amount.";exit;
//				}




				$admin_id = Config::get('generalConfig.admin_id');
				$trans_obj = new SiteTransactionHandlerService();
				$transaction_arr['date_added'] = new DateTime;
				$transaction_arr['user_id'] = $admin_id;
				$transaction_arr['transaction_type'] = 'debit';
				$transaction_arr['amount'] = $invoice_det['item_site_commission'];
				$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
				$transaction_arr['transaction_key'] = 'purchase_fee_refunded';
				$transaction_arr['reference_content_table'] = 'shop_order';
				$transaction_arr['reference_content_id'] = $invoice_det['order_id'];
				$transaction_arr['status'] = 'completed';
				$transaction_arr['related_transaction_id'] = $invoice_det['item_id'];
				$transaction_arr['payment_type'] = 'wallet';
				$transaction_arr['transaction_notes'] = 'Debited amount from wallet for cancellation of product: '.$invoice_det['item_id'].' in the order : '.$invoice_det['order_id'];
				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

				$transaction_arr['date_added'] = new DateTime;
				$transaction_arr['user_id'] = $invoice_det['buyer_id'];
				$transaction_arr['transaction_type'] = 'credit';
				$transaction_arr['amount'] = $invoice_det['item_site_commission'];
				$transaction_arr['transaction_notes'] = 'Credited amount to your wallet for cancellation of product: '.$invoice_det['item_id'].' in the order : '.$invoice_det['order_id'];
				$trans_id = $trans_obj->addNewTransaction($transaction_arr);



				$credits_obj = Credits::initialize();
				$credits_obj->setUserId($admin_id);
				$credits_obj->setCurrency($invoice_det['currency']);
				$credits_obj->setAmount($invoice_det['item_site_commission']);
				$credits_obj->creditAndDebit('amount','minus');

				$credits_obj = Credits::initialize();
				$credits_obj->setUserId($invoice_det['buyer_id']);
				$credits_obj->setCurrency($invoice_det['currency']);
				$credits_obj->setAmount($invoice_det['item_site_commission']);
				$credits_obj->creditAndDebit('amount','plus');				
				
				//seller amount debit and buyer amount credit
				
				$credits_obj = Credits::initialize();
				$credits_obj->setUserId($invoice_det['item_owner_id']);
				$credits_obj->setCurrency($invoice_det['currency']);
				$credits_obj->setAmount($seller_refund_amount);
				$credits_obj->creditAndDebit('amount','minus');

				$credits_obj = Credits::initialize();
				$credits_obj->setUserId($invoice_det['buyer_id']);
				$credits_obj->setCurrency($invoice_det['currency']);
				$credits_obj->setAmount($seller_refund_amount);
				$credits_obj->creditAndDebit('amount','plus');



				$trans_obj = new SiteTransactionHandlerService();
				$transaction_arr['date_added'] = new DateTime;
				$transaction_arr['user_id'] = $invoice_det['item_owner_id'];
				$transaction_arr['transaction_type'] = 'debit';
				$transaction_arr['amount'] = $seller_refund_amount;
				$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
				$transaction_arr['transaction_key'] = 'purchase_refunded';
				$transaction_arr['reference_content_table'] = 'shop_order';
				$transaction_arr['reference_content_id'] = $invoice_det['order_id'];
				$transaction_arr['status'] = 'completed';
				$transaction_arr['related_transaction_id'] = $invoice_det['item_id'];
				$transaction_arr['payment_type'] = 'wallet';
				$transaction_arr['transaction_notes'] = 'Debited from wallet amount for the cancellation of product '.$invoice_det['item_id'].' in the order: '.$invoice_det['order_id'];

				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

				$transaction_arr['date_added'] = new DateTime;
				$transaction_arr['user_id'] = $invoice_det['buyer_id'];
				$transaction_arr['transaction_type'] = 'credit';
				$transaction_arr['amount'] = $seller_refund_amount;
				$transaction_arr['transaction_notes'] = 'Credited to wallet amount for the cancellation of product '.$invoice_det['item_id'].' in the order: '.$invoice_det['order_id'];
				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

				//Debit, credit payment amount from seller
//				$credits_obj = Credits::initialize();
//				$credits_obj->setUserId($invoice_det['item_owner_id']);
//				$credits_obj->setCurrency($invoice_det['currency']);
//				$credits_obj->setAmount($seller_refund_amount);
//				$credits_obj->creditAndDebit('amount','minus');
//
//				//Credit, credit payment amount to buyer
//				$credits_obj = Credits::initialize();
//				$credits_obj->setUserId($invoice_det['buyer_id']);
//				$credits_obj->setCurrency($invoice_det['currency']);
//				$credits_obj->setAmount($seller_refund_amount);
//				$credits_obj->creditAndDebit('amount','plus');
//
//				if($seller_refund_paypal_amount > 0) {
//					//Debit, paypal payment amount from seller
//					$credits_obj = Credits::initialize();
//					$credits_obj->setUserId($invoice_det['item_owner_id']);
//					$credits_obj->setCurrency($invoice_det['currency']);
//					$credits_obj->setAmount($seller_refund_paypal_amount);
//					$credits_obj->creditAndDebit('amount','minus');
//
//					//Credit, paypal payment amount to buyer
//					/*$credits_obj = Credits::initialize();
//					$credits_obj->setUserId($invoice_det['buyer_id']);
//					$credits_obj->setCurrency($invoice_det['currency']);
//					$credits_obj->setAmount($seller_refund_paypal_amount);
//					$credits_obj->creditAndDebit('amount','plus');*/
//				}



				//Site transaction
//				$trans_obj = new SiteTransactionHandlerService();
//				$transaction_arr['date_added'] = new DateTime;
//				$transaction_arr['user_id'] = $invoice_det['item_owner_id'];
//				$transaction_arr['transaction_type'] = 'debit';
//				$transaction_arr['amount'] = $seller_refund_amount;
//				$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
//				$transaction_arr['transaction_key'] = 'cancellation_amount';
//				$transaction_arr['reference_content_table'] = 'invoices';
//				$transaction_arr['reference_content_id'] = $invoice_det['id'];
//				$transaction_arr['status'] = 'completed';
//				$transaction_arr['transaction_notes'] = 'Debit, credit payment amount from seller for order cancellation invoice id: '.$invoice_det['id'];
//				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

//				$transaction_arr['date_added'] = new DateTime;
//				$transaction_arr['user_id'] = $invoice_det['buyer_id'];
//				$transaction_arr['transaction_type'] = 'credit';
//				$transaction_arr['amount'] = $seller_refund_amount;
//				$transaction_arr['transaction_notes'] = 'Credit, credit payment amount to buyer for order cancellation for invoice id: '.$invoice_det['id'];
//				$trans_id = $trans_obj->addNewTransaction($transaction_arr);

//				if($seller_refund_paypal_amount > 0) {
//					$transaction_arr['date_added'] = new DateTime;
//					$transaction_arr['user_id'] = $invoice_det['item_owner_id'];
//					$transaction_arr['transaction_type'] = 'debit';
//					$transaction_arr['amount'] = $seller_refund_paypal_amount;
//					$transaction_arr['transaction_notes'] = 'Debit, paypal payment amount from seller for order cancellation invoice id: '.$invoice_det['id'];
//					$trans_id = $trans_obj->addNewTransaction($transaction_arr);
//
//					/*$transaction_arr['date_added'] = new DateTime;
//					$transaction_arr['user_id'] = $invoice_det['buyer_id'];
//					$transaction_arr['transaction_type'] = 'credit';
//					$transaction_arr['amount'] = $seller_refund_paypal_amount;
//					$transaction_arr['transaction_notes'] = 'Credit, paypal payment amount to buyer for order cancellation for invoice id: '.$invoice_det['id'];
//					$trans_id = $trans_obj->addNewTransaction($transaction_arr);*/
//				}
			}

			if(strtolower($refund_action) == "rejected")
			{
				$invoice_status = 'refund_rejected';
				$order_status = 'refund_rejected';
			}
			elseif(strtolower($refund_action) == "yes")
			{
				$invoice_status = 'refunded';
				$order_status = 'refund_completed';

				$variation_allowed = 0;
				if(CUtil::chkIsAllowedModule('variations'))
				{
					$variation_service = new VariationsService();
					$variation_allowed = 1;
				}

				$shop_order_obj = Webshoporder::initialize();
				$order_item_details = $shop_order_obj->getOrderItemDetailsForOrderRefund($invoice_det['order_id'], $invoice_det['item_id']);
				if(COUNT($order_item_details) > 0)
				{
					$item_qty = 0;
					foreach($order_item_details as $order_item_det)
					{
						$matrix_id = isset($order_item_det['matrix_id']) ? $order_item_det['matrix_id'] : 0;
						if($order_item_det['item_id'] == $item_id)
						{
							$item_qty = $order_item_det['item_qty'];
						}
					}
					if($variation_allowed && isset($variation_service) && $matrix_id > 0 )
					{
						// Restore variation stock
						$variation_service->updateProductVariationSold($item_id, $item_qty, $matrix_id, 'Refund');
					}
				}

			}

			$refund_response = $inputs['refund_response'];
			$refund_action = $inputs['refund_action'];
//			$invoice_status = 'refund_requested';
//			$order_status = 'refund_requested';
//
//			if(strtolower($refund_action) == "rejected")
//			{
//				$invoice_status = 'refund_rejected';
//				$order_status = 'refund_rejected';
//			}
//			elseif(strtolower($refund_action) == "yes")
//			{
//				$invoice_status = 'refunded';
//				$order_status = 'refund_completed';
//			}


			//Make request for cancel
			$invoice_obj->setInvoiceId($invoice_id);
			$invoice_obj->setIsRefundApprovedByAdmin($inputs['refund_action']);
			$invoice_obj->setRefundResponseByAdmin($inputs['refund_response']);
			$invoice_obj->setRefundRespondedByAdminId($logged_user_id);
			//$invoice_obj->setRefundResponseToUserByAdmin($inputs['user_notes']);
			if(strtolower($refund_action) == "yes"){
				$invoice_obj->setRefundAmountByAdmin($invoice_det['item_site_commission']);
				$invoice_obj->setRefundAmountBySeller($seller_refund_amount);
			}

			//if(strtolower($refund_action) == "yes") {
				//
				//$invoice_obj->setRefundPaypalAmountBySeller($seller_refund_paypal_amount);
			//}


			//if(!$user_allow_to_add_product)
			//{
				//$invoice_obj->setIsRefundApprovedBySeller($inputs['refund_action']);
				//$invoice_obj->setRefundResponseBySeller('Admin processed the request');
				//$invoice_obj->setRefundAmountBySeller(0);
			//}

			$invoice_obj->setInvoiceStatus($invoice_status);

			$invoice_obj->add();

			//Update order status
			$order_id = $invoice_det['order_id'];
			$order_obj = Webshoporder::initialize();
			$order_obj->setOrderId($order_id);
			$order_obj->setOrderStatus($order_status);
			$order_obj->add();

			$order_status_msg = trans('myPurchases.cancellation_accepted');
			if($refund_action == 'rejected')
				$order_status_msg = trans('myPurchases.cancellation_rejected');

			$user_details = CUtil::getUserDetails($invoice_det['buyer_id']);
			$data = array(
				'subject'		=> $order_status_msg,
				'user_name'	 => $user_details['user_name'],
				'user_email'	 => $user_details['email'],
				'order_id'	=> $invoice_det['order_id'],
				'order_status'  	=> $order_status_msg,
				'refund_action'  	=> strtolower($refund_action),
				'user_notes' => '',//$inputs['user_notes'],
				'admin_notes' => $inputs['refund_response'],
				'seller_refund_amount' => ($seller_refund_amount + $invoice_det['item_site_commission']),
				'seller_refund_paypal_amount' => 0,//$seller_refund_paypal_amount,
				'item_site_commission' => $invoice_det['item_site_commission'],
				'payment_gateway_type' => strtolower($invoice_det['payment_gateway_type']),
				'mail_from_send' => 'admin',
				'currency' => Config::get('generalConfig.site_default_currency'),
			);
			try {
				//Mail to User
				Mail::send('emails.adminCancellationNotificationToBuyer', $data, function($m) use ($data) {
					$m->to($data['user_email']);
					$m->subject($data['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
			$admin_id = Config::get('generalConfig.admin_id');
			$user_details = CUtil::getUserDetails($admin_id);
			$data += array('user_name'	 => $user_details['user_name'],
							'user_email'	 => $user_details['email']
							);
			try {
				//Mail to Admin
				Mail::send('emails.adminCancellationNotificationToAdmin', $data, function($m) use ($data) {
					$m->to($data['user_email']);
					$m->subject($data['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}

			$success_masg = trans('admin/cancellationpolicy.cancellation_request_accepted_amount_refunded_successfully');
			if(strtolower($refund_action) == "rejected")
				$success_masg = trans('admin/cancellationpolicy.cancellation_request_rejected_successfully');

			echo "success|~~|".$success_masg;exit;
		}
		exit;
	}
	public function getSetAsPaidPopup()
	{
		//echo "hello";
		$buyer_id = Input::has('buyer_id') ? Input::get('buyer_id') : '';
		$order_id = Input::has('order_id') ? Input::get('order_id') : '';
		$order_code = CUtil::setOrderCode($order_id);
		$user_det = CUtil::getUserDetails($buyer_id);
		if($user_det !='' )
			$user_name = $user_det['display_name'];
		$invoice_details = DB::table('common_invoice')->where('reference_id',$order_id)->get();
		//print_r($invoice_details); echo "fg".$buyer_id."sdf".$order_id."dfsd".$order_code;
		return View::make('admin.setAsPaidPopup',compact('user_name','order_id','order_code','invoice_details'));
	}
	public function postSetAsPaidPopup()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$order_id = Input::has('order_id') ? Input::get('order_id') : '';
			$transaction_type = Input::has('transaction_type') ? Input::get('transaction_type') : '';
			$amount_value = Input::has('amount_value') ? Input::get('amount_value') : '';
			$description_value = Input::has('description_value') ? Input::get('description_value') : '';
			//Credit to seller account
			$currency = Config::get('generalConfig.site_default_currency');
			$credit_obj = Credits::initialize();
			$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
			$credit_obj->setCurrency($currency);
			$credit_obj->setAmount($amount_value);
			$credit_obj->credit();
			if($transaction_type == 'pay_pal'){
				$invoice_details = DB::table('common_invoice')
									->where('reference_id',$order_id)
									->update(array('status' => 'Paid'));
				$shop_order_details = DB::table('shop_order')
									->where('id',$order_id)
									->update(array('order_status' => 'payment_completed','set_as_paid_notes' => $description_value, 'set_as_paid_transaction_type' => $transaction_type, 'set_as_paid_amount' => $amount_value));
				echo "success";
			}
			if($transaction_type == 'credit'){
				$user_id = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
				$user_amount = DB::table('user_account_balance')->where('user_id',$user_id)->pluck('amount');
				if($user_amount != '0'){
					$user_id = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
					$credits_obj = Credits::initialize();
					$credits_obj->setUserId($user_id);
					$credits_obj->setCurrency($currency);
					$credits_obj->setAmount($amount_value);
					$credits_obj->creditAndDebit('amount','minus');
					$invoice_details = DB::table('common_invoice')
										->where('reference_id',$order_id)
										->update(array('status' => 'Paid'));
					$shop_order_details = DB::table('shop_order')
										->where('id',$order_id)
										->update(array('order_status' => 'payment_completed','set_as_paid_notes' => $description_value, 'set_as_paid_transaction_type' => $transaction_type, 'set_as_paid_amount' => $amount_value));
					echo "success";
				}
				else{
					echo "Insufficient user balance";
				}
			}

			if($transaction_type == 'others'){
				$is_credit_payment = DB::table('common_invoice')->where('reference_id',$order_id)->pluck('is_credit_payment');
				if($is_credit_payment == 'Yes'){
					$paypal_amount = DB::table('common_invoice')->where('reference_id',$order_id)->select('paypal_amount','amount')->get();
					foreach($paypal_amount As $p_amount){
						if($p_amount->paypal_amount != $p_amount->amount && $p_amount->amount != '0'){
							$user_id = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
							$user_amount = DB::table('user_account_balance')->where('user_id',$user_id)->pluck('amount');
							if($user_amount != '0'){
								$reduce_amount = $p_amount->amount - $p_amount->paypal_amount;
								//print_r($reduce_amount);
								$credits_obj = Credits::initialize();
								$credits_obj->setUserId($user_id);
								$credits_obj->setCurrency($currency);
								$credits_obj->setAmount($reduce_amount);
								$credits_obj->creditAndDebit('amount','minus');
								$invoice_details = DB::table('common_invoice')
													->where('reference_id',$order_id)
													->update(array('status' => 'Paid'));
								$shop_order_details = DB::table('shop_order')
													->where('id',$order_id)
													->update(array('order_status' => 'payment_completed','set_as_paid_notes' => $description_value, 'set_as_paid_transaction_type' => $transaction_type, 'set_as_paid_amount' => $amount_value));
								echo "success";
							}
							else{
								echo "Insufficient user balance";
							}
						}
					}
				}else{
					$invoice_details = DB::table('common_invoice')
										->where('reference_id',$order_id)
										->update(array('status' => 'Paid'));
					$shop_order_details = DB::table('shop_order')
										->where('id',$order_id)
										->update(array('order_status' => 'payment_completed','set_as_paid_notes' => $description_value, 'set_as_paid_transaction_type' => $transaction_type, 'set_as_paid_amount' => $amount_value));
					echo "success";
				}
			}
		} else {
			echo Lang::get('common.demo_site_featured_not_allowed');
		}
	}
	public function getSetAsShippingPopup(){
		//$item_id = Input::has('item_id') ? Input::get('item_id') : '';
		$is_redirect = 0;
		$is_redirect = (Session::has('is_redirect'))?Session::get('is_redirect'):0;
		Session::forget('is_redirect');
		$order_id = Input::has('order_id') ? Input::get('order_id') : '';
		$item_id = Input::has('item_id') ? Input::get('item_id') : '';
		$order_code = CUtil::setOrderCode($order_id);
		$invoice_details = DB::table('common_invoice')->where('reference_id',$order_id)->first();
		$shop_order = DB::table('shop_order_item')
							->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
							->whereNotIn('invoices.invoice_status', array('pending', 'refund_requested'))
							->where('shop_order_item.order_id',$order_id)
							->where('shop_order_item.item_id',$item_id)
							->groupBy('invoices.id')
							->get();
		//print_r($shop_order);
		return View::make('admin.setAsShippingPopup',compact('is_redirect','order_id','item_id','shop_order','order_code','invoice_details'));
	}
	public function postSetAsShippingPopup()
	{
		$arr_ser = '';
		//$inputs = Input::all();
		$valid_item_ids = array();
		$order_id =Input::get('order_id');
		if(!BasicCUtil::checkIsDemoSite())
		{
			$order_id =Input::has('order_id') ? Input::get('order_id') : '';
			$item_id = Input::has('item_id') ? Input::get('item_id') : '';
			$tracking_id = Input::get('tracking_id_'.$item_id);

			if($order_id=='' || $item_id=='')
				return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',trans('myPurchases.invalid_invoice_details'));

			if($tracking_id!='')
			{
				$shop_order_details = DB::table('shop_order_item')
								->where('order_id',$order_id)
								->where('item_id',$item_id)
								->get();

				if(count($shop_order_details) > 0)
				{
					//echo "<pre>"; print_r($shop_order_details); echo "</pre>";//exit;
					$valid_serial_num = $invalid_serial_num = array();
					foreach($shop_order_details As $shop_ord){

						if($shop_ord->shipping_status != 'shipped'){

							$company_name = Input::get('shipping_company_'.$shop_ord->item_id);
							$tracking_id = Input::get('tracking_id_'.$shop_ord->item_id);
							$select_country = Input::get('select_country_'.$shop_ord->item_id);
							$serial_number = Input::get('serial_number_'.$shop_ord->item_id);
							$shop_update = DB::table('shop_order_item')
										->whereRaw('order_id = ?',array($order_id))
										->whereRaw('item_id = ?',array($shop_ord->item_id))
										->update(array('shipping_status' => 'shipped',
														'shipping_date' => DB::raw('now()'),
														'shipping_serial_number' =>$serial_number,
														'shipping_stock_country' =>$select_country,
														'shipping_tracking_id' =>$tracking_id,
														'shipping_company_name' =>$company_name ));

							//Remove the selected serial numbes
							$serial_number_details = DB::table('product_stocks')
													->where('product_id', $shop_ord->item_id)
													->select('serial_numbers','quantity')
													->first();

							$serial_split = array();
							if($serial_number !='' && count($serial_number_details) > 0){

								$arr_serial_numbers = $serial_number_details->serial_numbers;
								$serial_split = explode("\r\n", $arr_serial_numbers);
								$trim_value = rtrim($serial_number);
								$user_serial_number = explode("\n", $trim_value);

								$serial_split = array_map('trim', $serial_split);
								$user_serial_number = array_map('trim', $user_serial_number);
								foreach($user_serial_number As $a_serial){
									if(!in_array($a_serial, $serial_split)){
										$invalid_serial_num[] = $a_serial;
									}
									else {
										$valid_serial_num[] = $a_serial;
									}
								}
							}

							$final_serial = array_diff($serial_split, $valid_serial_num);
							$final_serial_str = implode("\r\n", $final_serial);
							$quantity_count = $serial_number_details->quantity - $shop_ord->item_qty;
							$quantity_count = ($quantity_count >= 0)?$quantity_count:0;

							$product_details_update = DB::table('product_stocks')
												->where('product_id', $shop_ord->item_id)
												->update(array('serial_numbers' => $final_serial_str,'quantity' => $quantity_count));

							$trim_value = rtrim($serial_number);
							$user_serial_number = explode("\n", $trim_value);
						}
					}

					$shop_order_det = DB::table('shop_order_item')
								->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
								->where('shop_order_item.order_id',$order_id)
								->where('shop_order_item.item_id',$item_id)
								->groupBy('invoices.id')
								->get();
					//buyer
					$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
					$user = DB::table('users')->where('id',$shop_order)->select('email', 'first_name', 'last_name')->first();

					$data = array('email' => $user->email, 'first_name' => $user->first_name, 'shop_order_details' => $shop_order_det, 'order_id' => $order_id, 'order_view_url' => URL::action('PurchasesController@getOrderDetails', $order_id));
					//echo "<pre>"; print_r($data); echo "</pre>";exit;
					try {
						Mail::send('emails.setAsShippingMailToBuyer', $data, function($m) use ($data) {
							$m->to($data['email'], $data['first_name']);
							$subject = Config::get('generalConfig.site_name')." - ".Lang::get('mail.your_product_has_been_shipped');
							$m->subject($subject);
						});
					} catch (Exception $e) {
						//return false
						CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
					}

					//seller
					$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('seller_id');
					$seller = DB::table('users')->where('id',$shop_order)->select('email', 'first_name', 'last_name')->first();
					$data = array('email' => $seller->email, 'first_name' => $seller->first_name, 'shop_order_details' => $shop_order_det, 'order_id' => $order_id, 'user_first_name' => $user->first_name, 'user_last_name' => $user->last_name, 'order_view_url' => URL::action('PurchasesController@getSalesOrderDetails', $order_id));
					try {
						Mail::send('emails.setAsShippingMailToBuyer', $data, function($m) use ($data) {
							$m->to($data['email'], $data['first_name']);
							$subject = Config::get('generalConfig.site_name')." - ".Lang::get('mail.product_has_been_shipped_to').' '.$data['user_first_name'].' '.$data['user_last_name'];
							$m->subject($subject);
						});
					} catch (Exception $e) {
						//return false
						CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
					}

					return Redirect::to('admin/purchases/set-as-shipping-popup?order_id='.$order_id)->with('success_message','Shipping status updated successfully')->with('is_redirect',1);
				}
				else{
					return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',trans('myPurchases.invalid_invoice_details'));
				}
			}
			else{
				return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',trans('myPurchases.invalid_tracking_id'));
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',$errMsg);
		}
	}
	public function getViewShippingPopup(){
		$order_id = Input::has('order_id') ? Input::get('order_id') : '';
		$order_code = CUtil::setOrderCode($order_id);
		$invoice_details = DB::table('common_invoice')->where('reference_id',$order_id)->first();
		$shop_order = DB::table('shop_order_item')->where('order_id',$order_id)->get();
		return View::make('admin.viewShippingPopup',compact('order_id','order_code','invoice_details','shop_order'));
	}
	public function getSetAsDelivered($order_id){
		if(!BasicCUtil::checkIsDemoSite()) {
			$item_id = Input::has('item_id')?Input::get('item_id'):'';
			$page = Input::has('page')?Input::get('page'):'';
			if($order_id != '' && $item_id != '')
			{
				$shop_order = DB::table('shop_order_item')
								->where('order_id',$order_id)
								->where('item_id',$item_id)
								->update(array('shipping_status' => 'delivered', 'delivered_date' => DB::raw('now()')));

				$delivered_date = date('Y-m-d H:i:s');//DB::table('shop_order')->where('id',$order_id)->where('set_as_delivered','yes')->pluck('delivered_date');
				$shop_order_det = DB::table('shop_order_item')
								->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
								->select('invoices.id','shop_order_item.item_qty', 'shop_order_item.shipping_company_name', 'shop_order_item.shipping_serial_number', 'shop_order_item.shipping_tracking_id', 'shop_order_item.item_id')
								->where('shop_order_item.order_id',$order_id)
								->where('shop_order_item.item_id',$item_id)
								->groupBy('invoices.id')
								->get();
				//buyer
				$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
				$user = DB::table('users')->where('id',$shop_order)->select('email', 'first_name', 'last_name')->first();
				$data = array('email' => $user->email, 'first_name' => $user->first_name, 'shop_order_details' => $shop_order_det, 'order_id' => $order_id, 'feedback' => 'yes', 'delivered_date' => $delivered_date, 'order_view_url' => URL::action('PurchasesController@getOrderDetails', $order_id));
				try {
					Mail::send('emails.setAsDeliveredMailToBuyer', $data, function($m) use ($data) {
						$m->to($data['email'], $data['first_name']);
						$subject = Config::get('generalConfig.site_name')." - ".Lang::get('mail.your_product_has_been_delivered');
						$m->subject($subject);
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}

				//seller
				$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('seller_id');
				$seller = DB::table('users')->where('id',$shop_order)->select('email', 'first_name', 'last_name')->first();
				$data = array('email' => $seller->email, 'first_name' => $seller->first_name, 'shop_order_details' => $shop_order_det, 'order_id' => $order_id, 'user_first_name' => $user->first_name, 'user_last_name' => $user->last_name, 'feedback' => 'no', 'delivered_date' => $delivered_date, 'order_view_url' => URL::action('PurchasesController@getSalesOrderDetails', $order_id));
				try {
					Mail::send('emails.setAsDeliveredMailToBuyer', $data, function($m) use ($data) {
						$m->to($data['email'], $data['first_name']);
						$subject = Config::get('generalConfig.site_name')." - ".Lang::get('mail.product_has_been_delivered_to').' '.$data['user_first_name'].' '.$data['user_last_name'];
						$m->subject($subject);
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}
			}
			if($page == 'sale_order')
				return Redirect::to('admin/purchases/order-details/'.$order_id)->with('success_message','Delivered status updated successfully');
			else
				return  Redirect::to('admin/purchases/index')->with('success_message','Delivered status updated successfully');
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return  Redirect::to('admin/purchases/index')->withInput()->with('error_message',$errMsg);
		}
	}
}