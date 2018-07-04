<?php

class Invoice {

	protected $invoice_id;

	protected $fields_arr = array();

	public function __construct()
	{
	}

	public function setInvoiceId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setOrderId($val)
	{
		$this->fields_arr['order_id'] = $val;
	}

	public function setBuyerId($val)
	{
		$this->fields_arr['buyer_id'] = $val;
	}

	public function setItemId($val)
	{
		$this->fields_arr['item_id'] = $val;
	}

	public function setItemOwnerId($val)
	{
		$this->fields_arr['item_owner_id'] = $val;
	}

	public function setOrderItemId($val)
	{
		$this->fields_arr['order_item_id'] = $val;
	}

	public function setOrderReceiverId($val)
	{
		$this->fields_arr['order_receiver_id'] = $val;
	}

	public function setInvoiceStatus($val)
	{
		$this->fields_arr['invoice_status'] = $val;
	}

	public function setIsRefundRequested($val)
	{
		$this->fields_arr['is_refund_requested'] = $val;
	}

	public function setRefundReason($val)
	{
		$this->fields_arr['refund_reason'] = $val;
	}

	public function setIsRefundApprovedByAdmin($val)
	{
		$this->fields_arr['is_refund_approved_by_admin'] = $val;
	}

	public function setRefundResponseByAdmin($val)
	{
		$this->fields_arr['refund_response_by_admin'] = $val;
	}

	public function setRefundRespondedByAdminId($val)
	{
		$this->fields_arr['refund_responded_by_admin_id'] = $val;
	}

	public function setRefundResponseToUserByAdmin($val)
	{
		$this->fields_arr['refund_response_to_user_by_admin'] = $val;
	}

	public function setRefundAmountByAdmin($val)
	{
		$this->fields_arr['refunded_amount_by_admin'] = $val;
	}

	public function setIsRefundApprovedBySeller($val)
	{
		$this->fields_arr['is_refund_approved_by_seller'] = $val;
	}

	public function setRefundResponseBySeller($val)
	{
		$this->fields_arr['refund_response_by_seller'] = $val;
	}

	public function setRefundAmountBySeller($val)
	{
		$this->fields_arr['refund_amount_by_seller'] = $val;
	}

	public function setRefundPaypalAmountBySeller($val)
	{
		$this->fields_arr['refund_paypal_amount_by_seller'] = $val;
	}

	public function setRefundStatus($val)
	{
		$this->fields_arr['refund_status'] = $val;
	}

	public function setTransactionId($val)
	{
		$this->fields_arr['txn_id'] = $val;
	}

	public function setPayKey($val)
	{
		$this->fields_arr['pay_key'] = $val;
	}

	/**
	 * Inserts invoice.
	 *
	 * @access   public
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
			$invoice_id = 0;
			if(isset($this->fields_arr['id'])) {
				$invoice_details = Invoices::Select('id')
											->whereRaw('id = ?', array($this->fields_arr['id']))
											->first();
				if(count($invoice_details) > 0) {
					$invoice_id = $invoice_details['id'];
				}
			}
			if($invoice_id > 0) {
				Invoices::whereRaw('id = ?', array($invoice_id))->update($this->fields_arr);
				return json_encode(array('status' => 'success'));
			}
			else {
				$invoice_id = Invoices::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'invoice_id' => $invoice_id));
			}
		}
	}

	public function checkInvoiceGeneratedForOrderItem($order_id, $item_id)
	{
		$count = Invoices::whereRaw('order_id = ? and item_id = ?', array($order_id, $item_id))
							->count();
		return $count;
	}

	public function getInvoicesForOrder($order_id, $seller_id = null)
	{
		//item_owner_id

		$invoices_details = Invoices::Select('invoices.*',
									'shop_order_item.item_amount as item_amount',
									'shop_order_item.item_qty as item_qty',
									'shop_order_item.shipping_fee as shipping_fee',
									'shop_order_item.total_tax_amount as total_tax_amount',
									'shop_order_item.tax_ids as tax_ids',
									'shop_order_item.tax_amounts as tax_amounts',
									'shop_order_item.total_amount as item_total_amount',
									'shop_order_item.discount_ratio_amount as discount_ratio_amount',
									'shop_order_item.site_commission as item_site_commission',
									'shop_order_item.seller_amount as seller_amount',
									'shop_order_item.shipping_status as shipping_status',
									'shop_order_item.shipping_tracking_id as shipping_tracking_id',
									'shop_order_item.shipping_serial_number as shipping_serial_number',
									'shop_order_item.shipping_date as shipping_date',
									'shop_order_item.delivered_date as delivered_date',
									'shop_order_item.shipping_company',
									'shop_order_item.deal_id', 'shop_order_item.deal_tipping_status',
									'shop_order_item.matrix_id', 'shop_order_item.is_use_giftwrap', 'shop_order_item.giftwrap_price',
									'shop_order_item.giftwrap_price_per_qty', 'shop_order_item.giftwrap_msg'
									)
							->LeftJoin('shop_order', 'shop_order.id','=','invoices.order_id')
							->LeftJoin('shop_order_item', 'shop_order_item.id', '=', 'invoices.order_item_id')
							->where('invoices.order_id', '=', $order_id);

		if(!is_null($seller_id) && $seller_id!='')
		{
			$invoices_details = $invoices_details->where('invoices.item_owner_id', '=', $seller_id);
		}

		$invoices_details = $invoices_details->get();
		//item_owner_id
		if(count($invoices_details) > 0)
		{
			$invoices_details = $invoices_details->toArray();
		}

		//$this->order_invoices = $invoices_details;
		return $invoices_details;
	}
	public function getInvoiceDetails($invoice_id)
	{
		$invoices_details = Invoices::Select('invoices.*',
								'shop_order_item.item_amount as item_amount',
								'shop_order_item.item_qty as item_qty',
								'shop_order_item.shipping_fee as shipping_fee',
								'shop_order_item.total_tax_amount as total_tax_amount',
								'shop_order_item.tax_ids as tax_ids',
								'shop_order_item.tax_amounts as tax_amounts',
								'shop_order_item.total_amount as item_total_amount',
								'shop_order_item.discount_ratio_amount as discount_ratio_amount',
								'shop_order_item.site_commission as item_site_commission',
								'shop_order_item.seller_amount as seller_amount',
								'shop_order.payment_gateway_type',
								'shop_order.currency as currency',
								'product.id as product_id', 'product.product_user_id', 'product.product_code', 'product.product_name', 'product.url_slug')
							->LeftJoin('shop_order', 'shop_order.id','=','invoices.order_id')
							->LeftJoin('product', 'product.id','=','invoices.item_id')
							->LeftJoin('shop_order_item', 'shop_order_item.id', '=', 'invoices.order_item_id')
							->where('invoices.id', '=', $invoice_id)
							->first();
		if(count($invoices_details) > 0)
		{
			$invoices_details = $invoices_details->toArray();
		}

		//$this->order_invoices = $invoices_details;
		return $invoices_details;
	}

	public function getOrderUserInvoicesList($order_id = null, $is_seller = false, $seller_id = null){

		if(is_null($order_id))
			return array();

		$seller_id = (($is_seller))?((!is_null($seller_id))?$seller_id:$this->logged_user_id):false;

		$qry = Invoices::LeftJoin('shop_order', 'shop_order.id','=','invoices.order_id')
				->LeftJoin('product', 'product.id','=','invoices.item_id')
				->LeftJoin('shop_details', 'shop_details.user_id', '=', 'invoices.item_owner_id')
				->LeftJoin('users as seller', 'seller.id', '=', 'invoices.item_owner_id')
				->LeftJoin('users as buyer', 'buyer.id', '=', 'invoices.buyer_id')
				->Select("invoices.*", "product.id as product_id", "product.product_user_id", "product.product_code", "product.product_name", "product.url_slug",
						"shop_details.id as shop_id", "shop_details.shop_name", "shop_details.url_slug as shop_slug", "shop_order.id as order_id",
						"seller.email as seller_email", "seller.first_name as seller_firstname", "seller.last_name as seller_lastname",
						"buyer.email as buyer_email", "buyer.first_name as buyer_firstname", "buyer.last_name as buyer_lastname")
				->where('invoices.order_id', '=', $order_id);
		if($seller_id)
				$qry->where('invoices.item_owner_id', '=', $seller_id);

		return $qry->orderby('id', 'asc')->get()->toArray();
	}


	public function getUserInvoiceList($user_id = null, $invoice_ids = array(), $exclude_ids = true, $view_type)
	{
		if(is_null($user_id) || $user_id=='')
			return false;

		$qry = Invoices::LeftJoin('shop_order', 'shop_order.id','=','invoices.order_id')
				->LeftJoin('product', 'product.id','=','invoices.item_id')
				->LeftJoin('shop_details', 'shop_details.user_id', '=', 'invoices.item_owner_id')
				->LeftJoin('users as seller', 'seller.id', '=', 'invoices.item_owner_id')
				->LeftJoin('users as buyer', 'buyer.id', '=', 'invoices.buyer_id')
				->Select("invoices.*", "product.id as product_id", "product.product_user_id", "product.product_code", "product.product_name", "product.url_slug",
						"shop_details.id as shop_id", "shop_details.shop_name", "shop_details.url_slug as shop_slug", "shop_order.id as order_id",
						"seller.email as seller_email", "seller.first_name as seller_firstname", "seller.last_name as seller_lastname",
						"buyer.email as buyer_email", "buyer.first_name as buyer_firstname", "buyer.last_name as buyer_lastname");

		if ($view_type == 'awaiting') {
			$qry->whereRaw('invoices.buyer_id = ?', array($user_id));
		} else {
			$qry->whereRaw('(invoices.item_owner_id = ? OR invoices.buyer_id = ?)', array($user_id, $user_id));
		}
		if(!$exclude_ids)
		{
			$qry->addSelect("pib.id as feedback_id", "pib.feedback_user_id", "pib.buyer_id", "pib.seller_id", "pib.feedback_comment", "pib.feedback_remarks", "pib.updated_at as feedback_updated_at");//DB::raw("AVG(mp_product_rating.rating) AS avg_rating")
			$qry->LeftJoin('product_invoice_feedback as pib', 'pib.invoice_id', '=', 'invoices.id');
		}
		if(isset($invoice_ids) && !empty($invoice_ids))
		{
			if($exclude_ids)
				$qry->whereNotIn('invoices.id',$invoice_ids);
			else
				$qry->whereIn('invoices.id',$invoice_ids);
		}
		return $qry->orderby('id', 'desc')->get()->toArray();
	}
}
?>