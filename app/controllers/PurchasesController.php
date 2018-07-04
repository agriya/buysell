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
class PurchasesController extends BaseController
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
    	$inputs = Input::all();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$order_obj = Webshoporder::initialize();
		$order_obj->setFilterBuyerId($logged_user_id);
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
					try
					{
						$prod_obj = Products::initialize($item->item_id);
						$prod_obj->setIncludeBlockedUserProducts(true);
						$prod_obj->setIncludeDeleted(true);
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
		$search_order_statuses = array('' => trans('common.select_option'),
							'payment_completed' => Lang::get('myPurchases.status_txt_payment_completed'),
							'not_paid' => Lang::get('myPurchases.status_unpaid'),
							'payment_cancelled' => Lang::get('myPurchases.status_txt_payment_cancelled'),
							'refund_completed' => Lang::get('myPurchases.status_txt_refund_completed'),
							'refund_rejected' => Lang::get('myPurchases.status_txt_refund_rejected'),
							'refund_requested' => Lang::get('myPurchases.status_txt_refund_requested'));
		$product_obj = Products::initialize();
		$productService = new ProductService();
		$get_common_meta_values = Cutil::getCommonMetaValues('my-purchases');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myPurchasesList', compact('order_details','order_obj','product_obj','productService', 'search_order_statuses'));
	}
	public function getOrderDetails($order_id = null)
	{
		if(is_null($order_id))
			return Redirect::action('PurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$order_obj = Webshoporder::initialize();
		$order_obj->setFilterBuyerId($logged_user_id);
		$order_obj->setFilterOrderId($order_id);
		$order_details = $order_obj->contents();
		$product_obj = Products::initialize();
		$productService = new ProductService();
		$common_invoice_obj =  Products::initializeCommonInvoice();
		$common_invoice_details = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId('Products', $order_id);
		$d_arr = array();
		$invoice_obj = Webshoporder::initializeInvoice();
		if(count($order_details) > 0)
		{
			foreach($order_details as $key => $order)
			{
				if(count($order) <=0 || (isset($order->buyer_id) && $order->buyer_id !=$logged_user_id))
					return Redirect::action('PurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));

				if(isset($order->order_status) &&  $order->order_status == 'draft')
					return Redirect::action('PurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));

				//For sudopay
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
					if($sudopay_brand == 'SudoPay Branding') {
						$d_arr['is_credit'] = 'No';
						$d_arr['reference_type'] = 'Products';
						$d_arr['logged_user_id'] = $logged_user_id;
						$d_arr['sudopay_fields_arr'] = $this->sudopay_service->getSudopayFieldsArr($order_id, $order, $common_invoice_details, $d_arr);
					}
					$d_arr['sudopay_brand'] = $sudopay_brand;
					$d_arr['sudopay_fees_payer'] = $sudopay_fees_payer;
				}

				$has_invoice = false;

				//if(Config::get('generalConfig.user_allow_to_add_product')) {
					$shop_obj = Products::initializeShops();
					$shop_details = $shop_obj->getShopDetails($order->seller_id);
				//}
				$order_details[$key]->shop_details = $shop_details;
				//echo "<pre>";print_r($shop_details);echo "</pre>";exit;
				//order item details
				$invoices = $invoice_obj->getInvoicesForOrder($order->id);
				//echo "<pre>";print_r($invoices);echo "</pre>";exit;
				//$order_items = $order_obj->getOrderitemDetails($order->id);
				if(count($invoices) > 0) { // If invoice exits then take product values from invoice table
					$has_invoice = true;
					foreach($invoices as $ikey => $item)
					{
						//echo "<br>item id: ".$item['item_id'];//item_id;
						//Product details
						$prod_obj = Products::initialize($item['item_id']);
						try
						{
							$prod_obj->setIncludeBlockedUserProducts(true);
							$prod_obj->setIncludeDeleted(true);
							$product_details = $prod_obj->getProductDetails();
							if($product_details['use_cancellation_policy'] == 'Yes' && $product_details['use_default_cancellation'] == 'Yes')
							{
								if(Config::get('generalConfig.user_allow_to_add_product')) {
									$shop_obj = Products::initializeShops();
									$shop_details = $shop_obj->getShopDetails($product_details['product_user_id']);
								}else{
									$cancellation_policy = Products::initializeCancellationPolicy();
									$shop_details = $cancellation_policy->getCancellationPolicyDetails(Config::get('generalConfig.admin_id'));
								}

								$product_details['cancellation_policy_text'] = isset($shop_details['cancellation_policy_text'])?$shop_details['cancellation_policy_text']:'';
								$product_details['cancellation_policy_filename'] = isset($shop_details['cancellation_policy_filename'])?$shop_details['cancellation_policy_filename']:'';
								$product_details['cancellation_policy_filetype'] = isset($shop_details['cancellation_policy_filetype'])?$shop_details['cancellation_policy_filetype']:'';
								$product_details['cancellation_policy_server_url'] = isset($shop_details['cancellation_policy_server_url'])?$shop_details['cancellation_policy_server_url']:'';

							}
						}
						catch(Exception $e)
						{
							$product_details = array();
						}
						$invoices[$ikey]['product_details'] = $product_details;

						//$order_item_det = $order_obj->getShopOrderitemDetails($item['order_item_id']);
						//$invoices[$ikey]['order_item'] = $order_item_det;
					}
					$order_details[$key]->order_invoices = $invoices;
				}
				else { // If invoice not exits then take product values from order items table
					$order_items = $order_obj->getOrderitemDetails($order->id);
					foreach($order_items as $ikey => $item)
					{
						$prod_obj = Products::initialize($item->item_id);
						$prod_obj->setIncludeBlockedUserProducts(true);
						$prod_obj->setIncludeDeleted(true);
						$product_details = $prod_obj->getProductDetails();
						if($product_details['use_cancellation_policy'] == 'Yes' && $product_details['use_default_cancellation'] == 'Yes')
						{
							if(Config::get('generalConfig.user_allow_to_add_product')) {
								$shop_obj = Products::initializeShops();
								$shop_details = $shop_obj->getShopDetails($product_details['product_user_id']);
							}else{
								$cancellation_policy = Products::initializeCancellationPolicy();
								$shop_details = $cancellation_policy->getCancellationPolicyDetails(Config::get('generalConfig.admin_id'));
							}
							$product_details['cancellation_policy_text'] = isset($shop_details['cancellation_policy_text'])?$shop_details['cancellation_policy_text']:'';
								$product_details['cancellation_policy_filename'] = isset($shop_details['cancellation_policy_filename'])?$shop_details['cancellation_policy_filename']:'';
								$product_details['cancellation_policy_filetype'] = isset($shop_details['cancellation_policy_filetype'])?$shop_details['cancellation_policy_filetype']:'';
								$product_details['cancellation_policy_server_url'] = isset($shop_details['cancellation_policy_server_url'])?$shop_details['cancellation_policy_server_url']:'';

						}
						$order_items[$ikey]['product_details'] = $product_details;
					}
					$order_details[$key]->order_invoices = $order_items;
				}
				$order_details[$key]->has_invoice = $has_invoice;
				//echo "<pre>";print_r($order_details);echo "</pre>";exit;
				//exit;
				//$order_details[$key]->order_invoices = $invoices;

				$order_items = $order_obj->getOrderitemDetails($order->id);
				$order_details[$key]->order_items = $order_items;

				$order_details[$key]->shipping_details = $shipping_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_id));
				//echo "<pre>";print_r($order_details[$key]->shipping_details);echo "</pre>";
			}
			//echo "<pre>";print_r($order_details->toArray());echo "</pre>";exit;
		}
		$get_common_meta_values = Cutil::getCommonMetaValues('order-details');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('orderDetails', compact('order_details','order_obj','product_obj','productService','common_invoice_details', 'd_arr'));
	}

	public function postRequestCancel()
	{
		$inputs = Input::all();

		if(is_null($inputs['invoice_id']) || $inputs['invoice_id'] == ""){
			echo "error|~~|Select valid invoice";exit;
		}
		$invoice_id = $inputs['invoice_id'];
		$invoice_obj = Webshoporder::initializeInvoice();
		$invoice_det = $invoice_obj->getInvoiceDetails($invoice_id);
		if(!isset($invoice_det) || count($invoice_det) <=0){
			echo "error|~~|Select valid invoice";exit;
		}
		if($invoice_det && count($invoice_det) > 0)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($invoice_det['buyer_id'] != $logged_user_id){
				echo "error|~~|".trans('myPurchases.not_authorized_to_access_this_invoice');exit;
			}
			if($invoice_det['is_refund_requested'] == "yes"){
				echo "error|~~|".trans('myPurchases.cancellation_already_requested');exit;
			}

			//Make request for cancel
			$invoice_obj->setInvoiceId($invoice_id);
			$invoice_obj->setRefundReason($inputs['refund_reason']);
			$invoice_obj->setInvoiceStatus('refund_requested');
			$invoice_obj->setIsRefundRequested('Yes');
			$invoice_obj->add();

			//Update order status
			$order_id = $invoice_det['order_id'];
			$order_obj = Webshoporder::initialize();
			$order_obj->setOrderId($order_id);
			$order_obj->setOrderStatus('refund_requested');
			$order_obj->add();


			//$this->service->updateInvoiceDetails($invoice_id, $refund_status);
			//$this->service->updateOrderDetails($invoice_det['order_id'], array('order_status' => 'refund_requested'));

			echo "success|~~|".trans('myPurchases.cancellation_requested_successfully');exit;
		}
		exit;
	}


	//Sales
	public function getMySales()
    {
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$order_obj = Webshoporder::initialize();
		$order_obj->setOrderPagination(20);
		$shopService = new ShopService();
		$inputs = Input::all();
		$shopService->setSearchOrders($order_obj, $inputs);
		$order_details = $order_obj->getSalesOrder($logged_user_id);
		if(count($order_details) > 0)
		{
			foreach($order_details as $key => $order)
			{
				$order_items = $order_obj->getOrderitemDetails($order->id);
				foreach($order_items as $ikey => $item)
				{
					$prod_obj = Products::initialize($item->item_id);
					try
					{
						$prod_obj->setIncludeBlockedUserProducts(true);
						$prod_obj->setIncludeDeleted(true);
						$product_details = $prod_obj->getProductDetails();
					}
					catch(exception $e)
					{
						$product_details = array();
					}
					$order_items[$ikey]->product_details = $product_details;
				}
				$order_details[$key]->order_items = $order_items;
			}
		}

		$search_order_statuses = array('' => trans('common.select_option'),
							'payment_completed' => Lang::get('myPurchases.status_txt_payment_completed'),
							'not_paid' => Lang::get('myPurchases.status_unpaid'),
							'payment_cancelled' => Lang::get('myPurchases.status_txt_payment_cancelled'),
							'refund_completed' => Lang::get('myPurchases.status_txt_refund_completed'),
							'refund_rejected' => Lang::get('myPurchases.status_txt_refund_rejected'),
							'refund_requested' => Lang::get('myPurchases.status_txt_refund_requested'));
		$product_obj = Products::initialize();
		$productService = new ProductService();
		$get_common_meta_values = Cutil::getCommonMetaValues('my-sales');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('mySalesList', compact('order_details','order_obj','product_obj','productService', 'search_order_statuses'));
	}

	public function getSalesOrderDetails($order_id = null)
	{
		if(is_null($order_id))
			return Redirect::action('PurchasesController@getIndex')->with('error_message',trans('myPurchases.select_valid_order'));
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
				if(count($order) <=0 || (isset($order->seller_id) && $order->seller_id != $logged_user_id))
					return Redirect::action('PurchasesController@getMySales')->with('error_message',trans('myPurchases.select_valid_order'));

				if(isset($order->order_status) &&  $order->order_status == 'draft')
					return Redirect::action('PurchasesController@getMySales')->with('error_message',trans('myPurchases.select_valid_order'));
				$has_invoice = false;

				$shop_obj = Products::initializeShops();
				$shop_details = $shop_obj->getShopDetails($order->seller_id);

				$order_details[$key]->shop_details = $shop_details;
				//echo "<pre>";print_r($shop_details);echo "</pre>";exit;
				//order item details
				$invoices = $invoice_obj->getInvoicesForOrder($order->id);
				//echo "<pre>";print_r($invoices);echo "</pre>";exit;
				//$order_items = $order_obj->getOrderitemDetails($order->id);
				if(count($invoices) > 0) { // If invoice exits then take product values from invoice table
					$has_invoice = true;
					foreach($invoices as $ikey => $item)
					{
						//echo "<br>item id: ".$item['item_id'];//item_id;
						//Product details
						$prod_obj = Products::initialize($item['item_id']);
						try
						{
							$prod_obj->setIncludeBlockedUserProducts(true);
							$prod_obj->setIncludeDeleted(true);
							$product_details = $prod_obj->getProductDetails();
							if($product_details['use_cancellation_policy'] == 'Yes' && $product_details['use_default_cancellation'] == 'Yes')
							{
								if(Config::get('generalConfig.user_allow_to_add_product')) {
									$shop_obj = Products::initializeShops();
									$shop_details = $shop_obj->getShopDetails($product_details['product_user_id']);
								}else{
									$cancellation_policy = Products::initializeCancellationPolicy();
									$shop_details = $cancellation_policy->getCancellationPolicyDetails(Config::get('generalConfig.admin_id'));
								}

								$product_details['cancellation_policy_text'] = isset($shop_details['cancellation_policy_text'])?$shop_details['cancellation_policy_text']:'';
								$product_details['cancellation_policy_filename'] = isset($shop_details['cancellation_policy_filename'])?$shop_details['cancellation_policy_filename']:'';
								$product_details['cancellation_policy_filetype'] = isset($shop_details['cancellation_policy_filetype'])?$shop_details['cancellation_policy_filetype']:'';
								$product_details['cancellation_policy_server_url'] = isset($shop_details['cancellation_policy_server_url'])?$shop_details['cancellation_policy_server_url']:'';

							}
						}
						catch(Exception $e)
						{
							$product_details = array();
						}
						$invoices[$ikey]['product_details'] = $product_details;

						//$order_item_det = $order_obj->getShopOrderitemDetails($item['order_item_id']);
						//$invoices[$ikey]['order_item'] = $order_item_det;
					}
					$order_details[$key]->order_invoices = $invoices;
				}
				else { // If invoice not exits then take product values from order items table
					$order_items = $order_obj->getOrderitemDetails($order->id);
					foreach($order_items as $ikey => $item)
					{
						$prod_obj = Products::initialize($item->item_id);
						try
						{
							$prod_obj->setIncludeBlockedUserProducts(true);
							$prod_obj->setIncludeDeleted(true);
							$product_details = $prod_obj->getProductDetails();
							if($product_details['use_cancellation_policy'] == 'Yes' && $product_details['use_default_cancellation'] == 'Yes')
							{
								if(Config::get('generalConfig.user_allow_to_add_product')) {
									$shop_obj = Products::initializeShops();
									$shop_details = $shop_obj->getShopDetails($product_details['product_user_id']);
								}else{
									$cancellation_policy = Products::initializeCancellationPolicy();
									$shop_details = $cancellation_policy->getCancellationPolicyDetails(Config::get('generalConfig.admin_id'));
								}
								$product_details['cancellation_policy_text'] = isset($shop_details['cancellation_policy_text'])?$shop_details['cancellation_policy_text']:'';
									$product_details['cancellation_policy_filename'] = isset($shop_details['cancellation_policy_filename'])?$shop_details['cancellation_policy_filename']:'';
									$product_details['cancellation_policy_filetype'] = isset($shop_details['cancellation_policy_filetype'])?$shop_details['cancellation_policy_filetype']:'';
									$product_details['cancellation_policy_server_url'] = isset($shop_details['cancellation_policy_server_url'])?$shop_details['cancellation_policy_server_url']:'';

							}
						}
						catch(Exception $e){
							$product_details = array();
						}
						$order_items[$ikey]['product_details'] = $product_details;
					}
					$order_details[$key]->order_invoices = $order_items;
				}
				$order_details[$key]->has_invoice = $has_invoice;

				$order_items = $order_obj->getOrderitemDetails($order->id);
				$order_details[$key]->order_items = $order_items;

				$order_details[$key]->shipping_details = $shipping_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_id));
			}
		}
		$product_obj = Products::initialize();
		$productService = new ProductService();
		$common_invoice_obj =  Products::initializeCommonInvoice();
		$common_invoice_details = $common_invoice_obj->getCommonInvoiceDetailsByReferenceId('Products', $order_id);
		$get_common_meta_values = Cutil::getCommonMetaValues('order-details');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('salesOrderDetails', compact('order_details','order_obj','product_obj','productService','common_invoice_details'));
	}


	public function postResponseCancel()
	{
		$inputs = Input::all();
		$rules = array('refund_action' => 'required');
		$messages = array('refund_action.required' => 'Action required');
		$validator = Validator::make($inputs, $rules, $messages);
		if ($validator->fails()) {
			echo "error|~~|".$validator->messages()->first();exit;
		}

		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		if(is_null($inputs['invoice_id']) || $inputs['invoice_id'] == ""){
			echo "error|~~|".trans('myPurchases.invalid_invoice_details');exit;
		}
		$item_id = $inputs['item_id'];
		$invoice_id = $inputs['invoice_id'];
		$invoice_obj = Webshoporder::initializeInvoice();
		$invoice_det = $invoice_obj->getInvoiceDetails($invoice_id);
		//echo "<pre>";print_r($invoice_det);echo "</pre>";exit;
		if(!isset($invoice_det) || count($invoice_det) <=0){
			echo "error|~~|".trans('myPurchases.invalid_invoice_details');exit;
		}
		if($invoice_det && count($invoice_det) > 0)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($invoice_det['item_owner_id'] != $logged_user_id){
				echo "error|~~|".trans('common.not_authorize');exit;
			}
			if(strtolower($invoice_det['is_refund_approved_by_seller']) != "no"){
				echo "error|~~|".trans('myPurchases.refund_action_already_taken');exit;
			}


			$refund_response = $inputs['refund_response'];
			$refund_action = $inputs['refund_action'];

			$invoice_status = 'refund_requested';
			$order_status = 'refund_requested';
			$seller_refund_amount = 0;
			
			//Make request for cancel
			$invoice_obj->setInvoiceId($invoice_id);
			$invoice_obj->setIsRefundApprovedBySeller($inputs['refund_action']);
			$invoice_obj->setRefundResponseBySeller($inputs['refund_response']);
			
			$invoice_obj->setInvoiceStatus($invoice_status);
			//$invoice_obj->setIsRefundRequested('Yes');
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
				'subject'		=> Config::get('generalConfig.site_name').' - '.$order_status_msg,
				'user_name'	 => $user_details['user_name'],
				'user_email'	 => $user_details['email'],
				'order_id'	=> $invoice_det['order_id'],
				'order_status'  	=> $order_status_msg,
				'refund_action'  	=> strtolower($refund_action),
				'user_notes' => isset($inputs['user_notes'])?$inputs['user_notes']:'',
				'admin_notes' => $inputs['refund_response'],
				'seller_refund_amount' => $seller_refund_amount,
				'seller_refund_paypal_amount' => 0,
				'mail_from_send' => 'seller',
				'item_site_commission' => $invoice_det['item_site_commission'],
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
							'user_email'	 => Config::get('generalConfig.invoice_email')//$user_details['email']
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






//			$refund_response = $inputs['refund_response'];
//			$refund_action = $inputs['refund_action'];
//			$invoice_status = 'refund_requested';
//
//			if(strtolower($refund_action) == "rejected" || strtolower($invoice_det['is_refund_approved_by_admin']) == "rejected")
//				$invoice_status = 'refund_rejected';
//			elseif(strtolower($refund_action) == "yes" && strtolower($invoice_det['is_refund_approved_by_admin']) == "yes")
//				$invoice_status = 'refunded';

			//$refund_status = array();
			//$refund_status['invoice_status'] = $invoice_status;
			//$refund_status['is_refund_approved_by_seller'] = $inputs['refund_action'];
			//$refund_status['refund_response_by_seller'] = $inputs['refund_response'];

			//$this->service->updateInvoiceDetails($invoice_id, $refund_status);
			//$this->service->updateOrderDetails($invoice_det['order_id'], array('order_status' => $invoice_status));

			echo "success|~~|".trans('myPurchases.refund_respond_success');exit;
		}
		exit;
	}
	public function getInvoiceAction()
	{
		$action = Input::get('action');
		$invoice_id = Input::get('invoice_id');
		$invoice_obj = Webshoporder::initializeInvoice();

		$invoice_det = $invoice_obj->getInvoiceDetails($invoice_id);
		if(!empty($invoice_det))
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($invoice_det['buyer_id'] == $logged_user_id || $invoice_det['item_owner_id'] == $logged_user_id)
			{
				switch($action)
				{
					case 'download_file':
						$productService = new ProductService();
						$productService->downloadProductFile($invoice_det['item_id'], true);
						exit;
						break;
				}
			}
			else
				return Redirect::to('purchases/index')->with('error_message', trans('common.error').': '.trans('common.not_authorize').'!');
		}
		else
			return Redirect::to('purchases/index')->with('error_message', trans('common.error').': '.trans('myPurchases.invalid_invoice_details').'!');
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
		return View::make('setAsShippingPopup',compact('is_redirect','order_id','item_id','shop_order','order_code','invoice_details'));
	}
	public function postSetAsShippingPopup()
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

				//Send mail to user with invoice details
				$shop_order_det = DB::table('shop_order_item')
								->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
								->where('shop_order_item.order_id',$order_id)
								->where('shop_order_item.item_id',$item_id)
								->get();

				$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
				$user = DB::table('users')->where('id',$shop_order)->select('email','first_name')->first();
				$data = array('email' => $user->email, 'first_name' => $user->first_name, 'shop_order_details' => $shop_order_det, 'order_id' => $order_id, 'order_view_url' => URL::action('PurchasesController@getOrderDetails', $order_id));
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
				return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->with('success_message',trans('myPurchases.shipping_status_update_success'))->with('is_redirect',1);
			}
			else{
				return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',trans('myPurchases.invalid_invoice_details'));
			}
		}
		else{
			return Redirect::to('purchases/set-as-shipping-popup?order_id='.$order_id)->withInput()->with('error_message',trans('myPurchases.invalid_tracking_id'));
		}
	}
	public function getViewShippingPopup(){
		$order_id = Input::has('order_id') ? Input::get('order_id') : '';
		$order_code = CUtil::setOrderCode($order_id);
		$invoice_details = DB::table('common_invoice')->where('reference_id',$order_id)->first();
		$shop_order = DB::table('shop_order_item')->where('order_id',$order_id)->get();
		return View::make('viewShippingPopup',compact('order_id','order_code','invoice_details','shop_order'));
	}
	public function getSetAsDelivered($order_id){
		$item_id = Input::has('item_id')?Input::get('item_id'):'';
		$page = Input::has('page')?Input::get('page'):'';

		if($order_id != '' && $item_id!=''){
			$shop_order = DB::table('shop_order_item')
								->where('order_id',$order_id)
								->where('item_id',$item_id)
								->update(array('shipping_status' => 'delivered', 'delivered_date' => DB::raw('now()')));

			$delivered_date = date('Y-m-d H:i:s');//DB::table('shop_order')->where('id',$order_id)->where('set_as_delivered','yes')->pluck('delivered_date');
			$shop_order_det = DB::table('shop_order_item')
							->join('invoices', 'shop_order_item.id', '=', 'invoices.order_item_id')
							->where('shop_order_item.order_id',$order_id)
							->where('shop_order_item.item_id',$item_id)
							->get();
			$shop_order = DB::table('shop_order')->where('id',$order_id)->pluck('buyer_id');
			$user = DB::table('users')->where('id',$shop_order)->select('email','first_name')->first();
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
		}
		if($page == 'sale_order')
			return Redirect::to('purchases/sales-order-details/'.$order_id)->with('success_message', trans('myPurchases.delivered_status_updated_successfully'));
		else
			return  Redirect::to('purchases/my-sales')->with('success_message', trans('myPurchases.delivered_status_updated_successfully'));
	}
}