<?php

class Webshoporders {

	protected $order_id;

	protected $fields_arr = array();

	protected $order_per_page = '';

	protected $filter_order_id = '';

	protected $filter_buyer_id = '';

	protected $filter_seller_id = '';

	protected $filter_status = '';

	protected $filter_date_from = '';

	protected $filter_date_to = '';

	protected $filter_not_status = '';

	protected $filter_pay_key = '';

	protected $filter_tracking_id = '';

	protected $receiver_paypal_email = '';

	public function __construct()
	{
	}

	public function setOrderId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setBuyerId($val)
	{
		$this->fields_arr['buyer_id'] = $val;
	}

	public function setSellerId($val)
	{
		$this->fields_arr['seller_id'] = $val;
	}

	public function setCouponCode($val)
	{
		$this->fields_arr['coupon_code'] = $val;
	}

	public function setSubTotal($val)
	{
		$this->fields_arr['sub_total'] = $val;
	}

	public function setDiscountAmount($val)
	{
		$this->fields_arr['discount_amount'] = $val;
	}

	public function setTotalAmount($val)
	{
		$this->fields_arr['total_amount'] = $val;
	}

	public function setSiteCommission($val)
	{
		$this->fields_arr['site_commission'] = $val;
	}

	public function setShippingFee($val)
	{
		$this->fields_arr['shipping_fee'] = $val;
	}

	public function setCurrency($val)
	{
		$this->fields_arr['currency'] = $val;
	}

	public function setOrderStatus($val)
	{
		$this->fields_arr['order_status'] = $val;
	}

	public function setPayKey($val)
	{
		$this->fields_arr['pay_key'] = $val;
	}

	public function setTrackingId($val)
	{
		$this->fields_arr['tracking_id'] = $val;
	}

	public function setPaymentStatus($val)
	{
		$this->fields_arr['payment_status'] = $val;
	}

	public function setPaymentResponse($val)
	{
		$this->fields_arr['payment_response'] = $val;
	}

	public function setDateCreated($val)
	{
		$this->fields_arr['date_created'] = $val;
	}

	public function setDateUpdated($val)
	{
		$this->fields_arr['date_updated'] = $val;
	}

	public function setItemOrderId($val)
	{
		$this->fields_arr['order_id'] = $val;
	}

	public function setItemId($val)
	{
		$this->fields_arr['item_id'] = $val;
	}

	public function setItemOwnerId($val)
	{
		$this->fields_arr['item_owner_id'] = $val;
	}

	public function setItemAmount($val)
	{
		$this->fields_arr['item_amount'] = $val;
	}

	public function setItemQuantity($val)
	{
		$this->fields_arr['item_qty'] = $val;
	}

	public function setItemShippingCompany($val)
	{
		$this->fields_arr['shipping_company'] = $val;
	}

	public function setItemShippingFee($val)
	{
		$this->fields_arr['shipping_fee'] = $val;
	}

	public function setItemTotalTaxAmount($val)
	{
		$this->fields_arr['total_tax_amount'] = $val;
	}

	public function setItemTaxIds($val)
	{
		$this->fields_arr['tax_ids'] = $val;
	}

	public function setItemTaxAmounts($val)
	{
		$this->fields_arr['tax_amounts'] = $val;
	}

	public function setSellerAmount($val)
	{
		$this->fields_arr['seller_amount'] = $val;
	}

	public function setDateAdded($val)
	{
		$this->fields_arr['date_added'] = $val;
	}

	public function setPaymentGatewayType($val)
	{
		$this->fields_arr['payment_gateway_type'] = $val;
	}
	public function setFilterPayKey($val)
	{
		$this->filter_pay_key = $val;
	}

	public function setOrderPagination($val)
	{
		$this->order_per_page = $val;
	}

	public function setFilterOrderId($val)
	{
		$this->filter_order_id = $val;
	}

	public function setReceiverPaypalEmail($val)
	{
		$this->receiver_paypal_email = $val;
	}

	public function setAdminPaypalEmail($val)
	{
		$this->admin_paypal_email = $val;
	}

	public function setFilterBuyerId($val)
	{
		$this->filter_buyer_id = $val;
	}

	public function setFilterSellerId($val)
	{
		$this->filter_seller_id = $val;
	}

	public function setFilterStaus($val)
	{
		$this->filter_status = $val;
	}

	public function setFilterNotStaus($val)
	{
		$this->filter_not_status = $val;
	}

	public function setFilterDateFrom($val)
	{
		$this->filter_date_from = $val;
	}

	public function setFilterDateTo($val)
	{
		$this->filter_date_to = $val;
	}

	public function setFilterTrackingId($val)
	{
		$this->filter_tracking_id = $val;
	}



	public function resetFilterKeys()
	{
		$this->filter_order_id = '';
		$this->filter_pay_key = '';
		$this->filter_tracking_id = '';
	}

	/**
	 * Inserts items into the order.
	 *
	 * @access   public
	 * @param    item
	 * @return   json
	 */
	public function add()
	{

		$rules = $message = array();
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$order_id = 0;
			if(isset($this->fields_arr['id'])) {
				$order_details = ShopOrder::Select('id')
											->whereRaw('id = ?', array($this->fields_arr['id']))
											->first();
				if(count($order_details) > 0) {
					$order_id = $order_details['id'];
				}
			}
			if($order_id > 0) {
				ShopOrder::whereRaw('id = ?', array($order_id))->update($this->fields_arr);
				return json_encode(array('status' => 'success'));
			}
			else {
				$order_id = ShopOrder::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'order_id' => $order_id));
			}
		}
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopOrderDetails()
	{
		$shop_order_arr = array();
		$shop_orders = ShopOrder::Select('id', 'buyer_id', 'seller_id', 'total_amount', 'site_commission', 'currency'
											, 'order_status', 'pay_key', 'tracking_id', 'payment_status'
											, 'payment_response', 'date_created', 'date_updated', 'deal_tipping_status');
		if($this->filter_order_id != '')
			$shop_orders = $shop_orders->whereRaw('id = ?', array($this->filter_order_id));
		if($this->filter_pay_key != '')
			$shop_orders = $shop_orders->whereRaw('pay_key = ?', array($this->filter_pay_key));
		if($this->filter_tracking_id != '')
			$shop_orders = $shop_orders->whereRaw('tracking_id = ?', array($this->filter_tracking_id));

		$shop_orders = $shop_orders->get();

		if(count($shop_orders) > 0) {
			foreach($shop_orders as $key => $vlaues) {
				$shop_order_arr['id'] = $vlaues->id;
				$shop_order_arr['buyer_id'] = $vlaues->buyer_id;
				$shop_order_arr['seller_id'] = $vlaues->seller_id;
				$shop_order_arr['total_amount'] = $vlaues->total_amount;
				$shop_order_arr['site_commission'] = $vlaues->site_commission;
				$shop_order_arr['currency'] = $vlaues->currency;
				$shop_order_arr['order_status'] = $vlaues->order_status;
				$shop_order_arr['pay_key'] = $vlaues->pay_key;
				$shop_order_arr['tracking_id'] = $vlaues->tracking_id;
				$shop_order_arr['payment_status'] = $vlaues->payment_status;
				$shop_order_arr['payment_response'] = $vlaues->payment_response;
				$shop_order_arr['date_created'] = $vlaues->date_created;
				$shop_order_arr['date_updated'] = $vlaues->date_updated;
				$shop_order_arr['deal_tipping_status'] = $vlaues->deal_tipping_status;
			}
		}
		return $shop_order_arr;
	}

	/**
	 * Remove an item from the order.
	 *
	 * @access   public
	 * @param    order_id
	 * @return   json
	 */
	public function remove($order_id = 0)
	{
		// Try to remove the item.
		$order = ShopOrder::whereRaw('id != ?', array(''));
		if($order_id)
			$order = $order->whereRaw('id = ?', array($order_id));
		$order = $order->delete();

		if ($order) {
			return json_encode(array('status' => 'success'));
		}
		return json_encode(array('status' => 'error', 'error_messages' => trans('order.order_not_exists')));
	}

	/**
	 * Returns the order contents.
	 *
	 * @return 		array
	 * @access 		public
	 * @throws   	Exception
	 */
	public function contents($return_type = 'get')
	{
		$order = ShopOrder::Select('id', 'buyer_id', 'seller_id', 'sub_total', 'discount_amount', 'total_amount', 'coupon_code', 'site_commission', 'currency', 'order_status', 'pay_key', 'tracking_id', 'payment_status',
									'payment_response', 'date_created', 'date_updated', 'payment_gateway_type','set_as_delivered','delivered_date', 'deal_tipping_status')
									->orderBy('id', 'DESC');
		if(isset($this->filter_order_id) && $this->filter_order_id != '') {
			$order = $order->whereRaw('id = ?', array($this->filter_order_id));
		}

		if(isset($this->filter_buyer_id) && $this->filter_buyer_id != '') {
			$order = $order->whereRaw('buyer_id = ?', array($this->filter_buyer_id));
		}

		if(isset($this->filter_seller_id) && $this->filter_seller_id != '') {
			$order = $order->whereRaw('seller_id = ?', array($this->filter_seller_id));
		}

		if(isset($this->filter_not_status) && $this->filter_not_status != '') {
			$order = $order->whereRaw('order_status != ?', array($this->filter_not_status));
		}

		if(isset($this->filter_status) && $this->filter_status != '') {
			$order = $order->whereRaw('order_status = ?', array($this->filter_status));
		}

		if(isset($this->filter_date_from) && $this->filter_date_from != '') {
			$order = $order->whereRaw('DATE(date_created) >= ?', array($this->filter_date_from));
		}
		if(isset($this->filter_date_to) && $this->filter_date_to != '') {
			$order = $order->whereRaw('DATE(date_created) <= ?', array($this->filter_date_to));
		}





		if($this->order_per_page != '' && $this->order_per_page > 0)
			$order = $order->paginate($this->order_per_page);
		else
		{
			if($return_type=='first')
				$order = $order->first();
			else
				$order = $order->get();
		}
		return $order;
	}

	/**
	 * Inserts order items.
	 *
	 * @access   public
	 * @param    item
	 * @return   json
	 */
	public function addOrderItems()
	{
		$rules = $message = array();
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$order_item_id = ShopOrderItem::insertGetId($this->fields_arr);
			return json_encode(array('status' => 'success', 'order_item_id' => $order_item_id));
		}
	}

	public function chkItemsAddedWithOrder($order_id)
	{
		$count = ShopOrderItem::whereRaw('order_id = ?', array($order_id))->count();
		return $count;
	}

	public function getOrderitemDetails($order_id)
	{
		$order_item_details = ShopOrderItem::whereRaw('order_id = ?', array($order_id))->get();
		return $order_item_details;
	}
	public function getOrderitems($item_id,$order_id)
	{
		$order_items = ShopOrderItem::whereRaw('item_id = ?', array($item_id))->whereRaw('order_id = ?', array($order_id))->first();
		return $order_items;
	}

	public function getOrderInvoices($order_id)
	{
		$order_item_details = ShopOrderItem::whereRaw('order_id = ?', array($order_id))->get();
		return $order_item_details;
	}

	public function calculateSiteCommission($total_price = 0)
	{
		$commission = 0;
		if($total_price<=0)
			return 0;
		$commission_type = Config::get("webshoporder.item_site_transaction_fee_type");
		if($commission_type != '')
		{
			//$fee_type = Config::get("mp_product.item_site_transaction_fee_type");
			switch($commission_type)
			{
				case 'Flat':
					$commission = Config::get("webshoporder.item_site_transaction_fee");
					break;

				case 'Percentage':
					if($total_price > 0)
					{ //Condition to calculate site transaction fee from product price if prodcut price greater than zero
						$commission = ($total_price * Config::get("webshoporder.item_site_transaction_fee_percent")/100);
					}
					break;

				case 'Mix':
					$commission = Config::get("webshoporder.item_site_transaction_fee");
					if($total_price > 0) //Condition to calculate site transaction fee from product price if prodcut price greater than zero
						$commission += ($total_price * Config::get("webshoporder.item_site_transaction_fee_percent")/100);
					break;
			}
		}
		return $commission;
	}

	public function addOrderReceiversDetails($common_invoice_id, $order_id, $order_items, $logged_user_id, $payment_gateway_chosen = 'paypal', $discount_amount = 0)
	{
		$receiver = array();
		$all_receivers = array();
		$site_commission = 0;
		if(empty($order_items) || count($order_items) <= 0)
			return false;

		DB::table('order_receivers')->where('common_invoice_id', '=', $common_invoice_id)->delete();

		foreach($order_items as $item)
		{
			$item_owner_id = $item['item_owner_id'];
			// For handle coupon code discount amount not handled issue amount calculated based on coupon.
			$seller_amt = $item['seller_amount'];
			$site_comm = $item['site_commission'];
			if($discount_amount > 0 && $item['discount_ratio_amount'] > 0)
			{
				$net_amount = $item['total_amount'] - $item['discount_ratio_amount'];
				$site_comm = $this->calculateSiteCommission($net_amount);
				$seller_amt = $net_amount - $site_comm;
			}

			if(isset($receiver[$item_owner_id]))
			{
				$receiver[$item_owner_id]['amount'] = number_format ($receiver[$item_owner_id]['amount'] + $seller_amt, 2, '.','');
			}
			else
			{
				$receiver[$item_owner_id]['common_invoice_id'] = $common_invoice_id;
				$receiver[$item_owner_id]['buyer_id'] = $logged_user_id;
				$receiver[$item_owner_id]['seller_id'] = $item_owner_id;
				if(is_array($this->receiver_paypal_email)) {
					$receiver[$item_owner_id]['receiver_paypal_email'] = isset($this->receiver_paypal_email[$item_owner_id]) ? $this->receiver_paypal_email[$item_owner_id]: '';
				}
				$receiver[$item_owner_id]['amount'] = number_format ($seller_amt, 2, '.','');
				$receiver[$item_owner_id]['currency'] = Config::get('generalConfig.site_default_currency');
				$receiver[$item_owner_id]['is_admin'] = 'No';
			}
			$site_commission += $site_comm;

			//if any user dont have the paypal email then return false
			//if($receiver[$item_owner_id]['receiver_paypal_email'] == '')
			//	return false;

		}
		foreach($receiver as $receiver_det)
		{
			//reduce the coupon discount amount. As in buysell concept can checkout with only one receiver.
			//So we have applied the discount directly here. Otherwise need to change the flow.
			// Commented the below line since discount amount handled above.
			//$receiver_det['amount'] = $receiver_det['amount'] - $discount_amount;
			if($payment_gateway_chosen!='paypal')
				$all_receivers[] = $receiver_det;
			else
			{
				if($receiver_det['amount'] > 0) //if the item is free item or if the total amount for the seller is less than zero then dont involve them in the payment process
				{
					$all_receivers[] = $receiver_det;
				}
			}
			OrderReceivers::insertGetId($receiver_det);
		}

		//$site_commission = 0;
		if($site_commission > 0)
		{
			if(is_array($this->admin_paypal_email))
			{
				$item_owner_id = key($this->admin_paypal_email);

				$main_receiver['common_invoice_id'] = $common_invoice_id;
				$main_receiver['buyer_id'] = $logged_user_id;
				$main_receiver['seller_id'] = $item_owner_id;
				$main_receiver['receiver_paypal_email'] = $this->admin_paypal_email[$item_owner_id];
				$main_receiver['amount'] = $site_commission;
				$main_receiver['currency'] = Config::get('generalConfig.site_default_currency');
				$main_receiver['is_admin'] = 'Yes';

				$all_receivers[] = $main_receiver;
				OrderReceivers::insertGetId($main_receiver);
			}
		}
		return $all_receivers;
	}

	public function updateOrderReceiverPayKey($common_invoice_id, $pay_key)
	{
		$data_arr['pay_key'] = $pay_key;
		OrderReceivers::where('common_invoice_id','=', $common_invoice_id )->update($data_arr);
	}

	public function updateOrderReceiverData($common_invoice_id, $data_arr)
	{
		OrderReceivers::where('common_invoice_id','=', $common_invoice_id )->update($data_arr);
	}

	public function checkTransactionProcessed($pay_key, $tracking_id)
	{
		$count = ShopOrder::whereRaw('pay_key = ? and tracking_id = ? and (payment_status = \'\' or payment_status = \'NULL\') ', array($pay_key, $tracking_id))
							->count();
		return $count;
	}

	public function updateReceiversTransactionId($receivers, $common_invoice_id, $pay_key)
	{
		if(!empty($receivers))
		{
			foreach($receivers as $receiver)
			{
				OrderReceivers::where('common_invoice_id','=',$common_invoice_id)
								->where('receiver_paypal_email','=',$receiver['email'])
								->update(array('txn_id' => $receiver['transactionId'], 'status' => $receiver['transactionStatus'], 'pay_key' => $pay_key));
			}
		}
	}

	public function updateReceiversStatus($common_invoice_id, $status, $payment_type)
	{
		OrderReceivers::where('common_invoice_id','=',$common_invoice_id)
							->update(array('txn_id' => '', 'status' => $status, 'pay_key' => '', 'payment_type' => $payment_type));
	}

	//Invoice functions
	public function getOrderItemDetailsForOrder($order_id){
		$order_item_details = array();
		$order_item_details = ShopOrderItem::select('shop_order_item.*',
							'shop_order.order_status', 'shop_order.tracking_id', 'shop_order.pay_key',
							'order_receivers.id as order_receiver_id', 'order_receivers.receiver_paypal_email', 'order_receivers.status as receiver_status', 'order_receivers.txn_id')
							->LeftJoin('shop_order', 'shop_order.id','=','shop_order_item.order_id')
							->LeftJoin('order_receivers', 'order_receivers.seller_id','=','shop_order_item.item_owner_id')
							->LeftJoin('common_invoice', 'common_invoice.common_invoice_id','=','order_receivers.common_invoice_id')
							->whereRaw('shop_order_item.order_id = ? AND common_invoice.reference_type = \'Products\' AND common_invoice.reference_id = shop_order_item.order_id',array($order_id))
							->get()->toArray();
		return $order_item_details;
	}
	
	public function getOrderItemDetailsForOrderRefund($order_id, $item_id){
		$order_item_details = array();
		$order_item_details = ShopOrderItem::select('shop_order_item.*')
							->whereRaw('shop_order_item.order_id = ? AND shop_order_item.item_id = ?',array($order_id, $item_id))
							->get()->toArray();
		return $order_item_details;
	}
	

	public function getSalesOrder($seller_id)
	{
		$sales_order = ShopOrder::Select('shop_order.*')
				->leftJoin('invoices', 'invoices.order_id', '=', 'shop_order.id')
				->where('shop_order.seller_id', '=', $seller_id)
				->where('order_status', '!=', 'draft')
				->orderby('date_created', 'desc')
				->groupby('shop_order.id');

		if(isset($this->filter_order_id) && $this->filter_order_id != '') {
			$sales_order = $sales_order->whereRaw('shop_order.id = ?', array($this->filter_order_id));
		}

		if(isset($this->filter_buyer_id) && $this->filter_buyer_id != '') {
			$sales_order = $sales_order->whereRaw('shop_order.buyer_id = ?', array($this->filter_buyer_id));
		}

		if(isset($this->filter_seller_id) && $this->filter_seller_id != '') {
			$sales_order = $sales_order->whereRaw('shop_order.seller_id = ?', array($this->filter_seller_id));
		}

		if(isset($this->filter_not_status) && $this->filter_not_status != '') {
			$sales_order = $sales_order->whereRaw('shop_order.order_status != ?', array($this->filter_not_status));
		}

		if(isset($this->filter_status) && $this->filter_status != '') {
			$sales_order = $sales_order->whereRaw('shop_order.order_status = ?', array($this->filter_status));
		}

		if(isset($this->filter_date_from) && $this->filter_date_from != '') {
			$sales_order = $sales_order->whereRaw('DATE(shop_order.date_created) >= ?', array($this->filter_date_from));
		}
		if(isset($this->filter_date_to) && $this->filter_date_to != '') {
			$sales_order = $sales_order->whereRaw('DATE(shop_order.date_created) <= ?', array($this->filter_date_to));
		}


		if($this->order_per_page != '' && $this->order_per_page > 0)
			$sales_order = $sales_order->paginate($this->order_per_page);
		else
			$sales_order = $sales_order->get();
		return $sales_order;

		//$sales_order


	}

	//Functions for credits start
	public function addCreditReceiversDetails($common_invoice_id, $common_invoice_details, $logged_user_id, $payment_gateway_chosen = 'paypal')
	{
		$receiver = array();
		$all_receivers = array();

		DB::table('order_receivers')->where('common_invoice_id', '=', $common_invoice_id)->delete();

		if(is_array($this->receiver_paypal_email))
		{
			$receiver_id = key($this->receiver_paypal_email);

			$main_receiver['common_invoice_id'] = $common_invoice_id;
			$main_receiver['buyer_id'] = $logged_user_id;
			$main_receiver['seller_id'] = $receiver_id;
			$main_receiver['receiver_paypal_email'] = $this->receiver_paypal_email[$receiver_id];
			$main_receiver['amount'] = number_format ($common_invoice_details['amount'], 2, '.','');
			$main_receiver['currency'] = Config::get('generalConfig.site_default_currency');
			$main_receiver['is_admin'] = 'Yes';

			$all_receivers[] = $main_receiver;
			OrderReceivers::insertGetId($main_receiver);
		}
		return $all_receivers;
	}
	public function updateOrderItemDiscountRate($order_id, $discount_single_rate){
		ShopOrderItem::where('order_id','=',$order_id)
							->update(array('discount_ratio_amount' => DB::raw('TRUNCATE(total_amount*'.$discount_single_rate.',2)')));
		//UPDATE shop_order_item SET discount_amount_rate = TRUNCATE(total_amount*0.117,2) WHERE order_id=212;
	}
	public function getOrderReceiversForOrder($common_invoice_id = '', $pay_key='')
	{
		$order_receiver = OrderReceivers::orderBy('id', 'asc');
		if($common_invoice_id != '')
		{
			$order_receiver->where('common_invoice_id',$common_invoice_id);
		}
		elseif($pay_key != '')
		{
			$order_receiver->where('pay_key',$pay_key);
		}
		$receiver_details = $order_receiver->get()->toArray();
		return $receiver_details;

	}
	public function getSingleOrderReceiverDetails($email, $pay_key)
	{
		$receiver = OrderReceivers::where('pay_key',$pay_key)->where('receiver_paypal_email',$email)->first()->toArray();
		return $receiver;
	}

	public function getOderDetailsForInvoiceMail($order_id = null)
	{
		if(is_null($order_id) || $order_id <=0)
			return array();


		$this->setFilterOrderId($order_id);
		$order = $this->contents('first');
		if(count($order) > 0)
		{
			$has_invoice = false;

			$deal_allowed = $variation_allowed = 0;
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
			//order item details
			$invoice_obj = Webshoporder::initializeInvoice();
			$invoices = $invoice_obj->getInvoicesForOrder($order->id);
			if(count($invoices) > 0) { // If invoice exits then take product values from invoice table
				$has_invoice = true;
				foreach($invoices as $ikey => $item)
				{
					//Product details
					$prod_obj = Products::initialize($item['item_id']);
					try
					{
						$prod_obj->setIncludeBlockedUserProducts(true);
						$prod_obj->setIncludeDeleted(true);
						$product_details = $prod_obj->getProductDetails();
						$product_details['view_url'] = Products::getProductViewURL($product_details['id'], $product_details);
						if($product_details['is_downloadable_product'] == 'Yes')
						{
							$product_details['download_url'] = $download_url = URL::action('PurchasesController@getInvoiceAction'). '?action=download_file&invoice_id='.$item['id'];
							$resources_arr = $prod_obj->populateProductResources('Archive', 'Yes');
							$resources_arr[0]['download_filename'] = preg_replace('/[^0-9a-z\.\_\-]/i','', $resources_arr[0]['title']);
							$product_details['resource_arr'] = $resources_arr;
						}
					}
					catch(Exception $e)
					{
						$product_details = array();
					}
					//echo "<pre>";print_r($product_details);echo "</pre>";exit;
					$invoices[$ikey]['product_details'] = $product_details;

					if($deal_allowed && isset($deal_service) && isset($item['deal_id']) && $item['deal_id'] >0)
					{
						$deal_details = $deal_service->fetchDealDetailsById($item['deal_id']);
						$invoices[$ikey]['deal_data'] = $deal_details;
					}
					if($variation_allowed && isset($variation_service) && $item['matrix_id'] > 0 )
					{
						$variations_details = $variation_service->populateVariationAttributesByMatrixId($item['item_id'], $item['matrix_id'], $item['item_owner_id']);
						$invoices[$ikey]['variation_data'] = $variations_details;
					}
				}
				$order->order_invoices = $invoices;
			}
			else { // If invoice not exits then take product values from order items table
				$order_items = $this->getOrderitemDetails($order->id);
				foreach($order_items as $ikey => $item)
				{
					$prod_obj = Products::initialize($item->item_id);
					try
					{
						$product_details = $prod_obj->getProductDetails();
						$product_details['view_url'] = Products::getProductViewURL($product_details['id'], $product_details);
						if($product_details['is_downloadable_product'] == 'Yes')
						{
							$product_details['download_url'] = $download_url = URL::action('PurchasesController@getInvoiceAction'). '?action=download_file&invoice_id='.$item['id'];
							$resources_arr = $prod_obj->populateProductResources('Archive', 'Yes');
							$resources_arr[0]['download_filename'] = preg_replace('/[^0-9a-z\.\_\-]/i','', $resources_arr[0]['title']);
							$product_details['resource_arr'] = $resources_arr;
						}
					}
					catch(Exception $e){
						$product_details = array();
					}
					//echo "<pre>";print_r($product_details);echo "</pre>";
					$order_items[$ikey]['product_details'] = $product_details;
					if($deal_allowed && isset($deal_service) && isset($item->deal_id) && $item->deal_id >0)
					{
						$deal_details = $deal_service->fetchDealDetailsById($item->deal_id);
						$order_items[$ikey]['deal_data'] = $deal_details;
					}
					if($variation_allowed && isset($variation_service) && $item->is_use_giftwrap > 0 && $item->matrix_id > 0 )
					{
						$variations_details = $variation_service->populateVariationAttributesByMatrixId($item->item_id, $item->matrix_id, $item->item_owner_id);
						$order_items[$ikey]['variation_data'] = $variations_details;
					}
				}
				$order_details->order_invoices = $order_items;
			}
			$order->has_invoice = $has_invoice;
			//echo "<pre>";print_r($order);echo "</pre>";exit;
			return $order;
		}

	}
	//Functions for credits end
}
?>