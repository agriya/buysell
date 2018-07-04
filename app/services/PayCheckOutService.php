<?php
class PayCheckOutService extends CheckOutService
{
	public $order_details = array();
	public $order_item_ids_arr = array();
	public $order_item_details_arr = array();
	public $order_total_amount = 0;
	public $discount_amount = 0;

	public function checkValidOrderId($order_id, $buyer_id = 0)
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->setFilterOrderId($order_id);
		$order_info = $order_obj->contents();
		if(count($order_info) > 0)
		{
			if($buyer_id == 0)
				$buyer_id = BasicCUtil::getLoggedUserId();
			foreach($order_info as $values)
			{
				$order_status = strtolower($values['order_status']);
				if($values['buyer_id'] == $buyer_id && ($order_status == 'draft' || $order_status == 'not_paid'))
				{
					$this->order_details = $values;
				}
			}
		}
		return $this->order_details;
	}

	public function chkItemsAddedWithOrder($order_id)
	{
		$order_obj = Webshoporder::initialize();
		$order_item_count = $order_obj->chkItemsAddedWithOrder($order_id);
		return $order_item_count;
	}

	public function updatePaymentOrderDetails($order_id, $data_arr)
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->setOrderId($order_id);

		if(isset($data_arr['pay_key']) && $data_arr['pay_key'] != '')
			$order_obj->setPayKey($data_arr['pay_key']);

		if(isset($data_arr['tracking_id']) && $data_arr['tracking_id'] != '')
			$order_obj->setTrackingId($data_arr['tracking_id']);

		if(isset($data_arr['payment_gateway_type']) && $data_arr['payment_gateway_type'] != '')
			$order_obj->setPaymentGatewayType($data_arr['payment_gateway_type']);

		$order_obj->add();
	}

	public function updatePaymentOrderReceiversDetails($common_invoice_id, $data_arr)
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->updateOrderReceiverPayKey($common_invoice_id, $data_arr['pay_key']);
	}

	public function updateCommonInvoiceDetails($common_invoice_id, $data_arr)
	{
		$manage_invoice_obj = Products::initializeCommonInvoice();
		$manage_invoice_obj->updateCommonInvoiceDetails($common_invoice_id, $data_arr);
	}

	public function setItemDetails($order_id, $order_details = array())
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->setFilterOrderId($order_id);
		$order_details = $order_obj->contents();
		//echo "<pre>";print_r($order_details);echo "</pre>";exit;
		if(count($order_details) <= 0)
			return false;

		$order_item_details = $order_obj->getOrderitemDetails($order_id);
		if(count($order_item_details) > 0)
		{
			foreach($order_item_details as $product)
			{
				$product = $product->toArray();
				$prod_obj = Products::initialize($product['item_id']);
				$prod_obj->setFilterProductStatus('Ok');
				$prod_obj->setFilterProductExpiry(true);
				$product_details = $prod_obj->getProductDetails();

				$product = array_merge($product, $product_details);

				$product['product_price'] = $product['item_amount'];
				//$product['shipping_fee'] = $product['shipping_fee'];
				//$product['total_tax_amount'] = $product['total_tax_amount'];
				$tax_ids = $product['tax_ids'];//total_tax_amount
				$tax_amounts = $product['tax_amounts'];
				$product_tax_details = array();
				if($tax_ids!='')
				{
					$product_tax_ids = explode(',',$tax_ids);
					$product_tax_amount = explode(',',$tax_amounts);
					$tax_details = array();
					foreach($product_tax_ids as $inc => $tax_id)
					{
						$tax_det = Webshoptaxation::Taxations()->getTaxations(array('id' => $tax_id), 'first', array('include_deleted' => true));
						$tax_details[$inc]['tax_name'] = $tax_det['tax_name'];
						$tax_details[$inc]['calculated_tax_amount'] = isset($product_tax_amount[$inc])?$product_tax_amount[$inc]:0;
					}
					$product['product_tax_details'] = $tax_details;
				}


				$product['sub_total'] = ($product['item_amount'] * $product['item_qty']) + $product['shipping_fee'] + $product['total_tax_amount'];
				$this->order_total_amount += $product['total_amount'];
				$this->order_item_details_arr[$product['id']] = $product;
			}
			$this->discount_amount = 0;
			if(isset($order_details{0}->discount_amount) && $order_details{0}->discount_amount !='' && $order_details{0}->discount_amount > 0)
			{
				$this->discount_amount = $order_details{0}->discount_amount;
			}
		}
		return $this->order_item_details_arr;
	}

	public function getItemData($id, $field, $type)
	{
		if($type == 'product')
		{
			return $this->getProductData($id, $field);
		}
		return '';
	}

	public function getProductData($id, $field)
	{
		if(isset($this->order_item_details_arr[$id]))
		{
			return isset($this->order_item_details_arr[$id][$field]) ? $this->order_item_details_arr[$id][$field] : '';
		}
		return '';
	}

	public function getDiscountAmount($order_details)
	{
		$currency = $order_details['currency'];
		$total_amt = $this->order_total_amount;
		$discount_amt = $this->discount_amount;
		$discounted_amt = $total_amt-$discount_amt;
		return $discount_amt."::".$discounted_amt;
	}

	public function receiverDetails($order_id)
	{
		$order_items = ShopOrderItem::whereRaw('order_id = ?', array($order_id))->get();
		$receiver = array();
		$site_commission = 0;
		foreach($order_items as $item)
		{
			$item_owner_id = $item['item_owner_id'];
			if(isset($receiver[$item_owner_id]))
			{
				$receiver[$item_owner_id]['receiver_id'] = $item_owner_id;
				$receiver[$item_owner_id]['amount'] = $receiver[$item_owner_id]['amount'] + $item['seller_amount'];
			}
			else
			{
				$receiver[$item_owner_id]['receiver_id'] = $item_owner_id;
				$receiver[$item_owner_id]['receiver_paypal_email'] = CUtil::getUserFields($item_owner_id, 'paypal_id');
				$receiver[$item_owner_id]['amount'] = $item['seller_amount'];
				$receiver[$item_owner_id]['is_admin'] = 'No';
			}
			$site_commission += $item['site_commission'];
			//if any user dont have the paypal email then return false
			if($receiver[$item_owner_id]['receiver_paypal_email'] == '')
				return false;
		}
		if($site_commission > 0)
		{

		}
		return $receiver;
	}

	public function addOrderReceiversDetails($common_invoice_id, $order_id, $payment_gateway_chosen = 'paypal')
	{
		$receiver_emails = array();
		$site_commission = 0;
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$admin_id = Config::get("generalConfig.admin_id");
		$order_obj = Webshoporder::initialize();

		$order_items = $order_obj->getOrderitemDetails($order_id);
		if(empty($order_items) || count($order_items) <= 0)
			return false;

		$shop = Products::initializeShops();
		foreach($order_items as $item)
		{
			$item_owner_id = $item['item_owner_id'];
			$shop_details = $shop->getUsersShopDetails($item_owner_id);
			//if(CUtil::isUserAllowedToAddProduct()) {
				//if any user dont have the paypal email then return false
				//if($shop_details['paypal_id'] == '')
				//	return false;
				$receiver_emails[$item_owner_id] = $shop_details['paypal_id'];
			//}
			//else {
			//	$receiver_emails[$item_owner_id] = Config::get('payment.paypal_merchant_email');
			//}
		}

		$business = Config::get('payment.paypal_merchant_email');

		$order_obj->setReceiverPaypalEmail($receiver_emails);
		$admin_email[$admin_id] = $business;
		//Log::info("==================admin_details==========================");
		//	Log::info(print_r($admin_email,1));
		//Log::info("============================================");
		$order_obj->setAdminPaypalEmail($admin_email);
		$discount_amount = 0;
		if(isset($this->order_details['coupon_code']) && $this->order_details['coupon_code']!='' && isset($this->order_details['discount_amount']) && $this->order_details['discount_amount'] > 0)
			$discount_amount = $this->order_details['discount_amount'];
		$all_receivers = $order_obj->addOrderReceiversDetails($common_invoice_id, $order_id, $order_items, $logged_user_id, $payment_gateway_chosen, $discount_amount);
		return $all_receivers;
	}


	public function getPaymentDetails($order_currency, $order_amount)
	{
		$payment_curr_options = array();
		$inv_service = new InvoiceService();
		$amount_arr = $inv_service->calculateOrderAmount($order_currency, $order_amount, 'USD');
		$disp_amount = $amount_arr;

		// Show pay in USD option
		$main_currency = Config::get('payment.paypal_main_currency');
		$main_currency_amt = number_format ($disp_amount['amount'], 2, '.','');

		$main_payment['text'] = Lang::get('payCheckOut.pay_in')." ".$main_currency." ".$main_currency_amt;
		$main_payment['amount'] = $main_currency_amt;
		$main_payment['currency'] = $main_currency;

		$payment_curr_options['default_currency'] = $main_payment;

		$paypal_supported_currency_arr = explode(",", Config::get('payment.paypal_supported_currencies'));

		$product_currency['allow_product_currency'] = 0;

		// show pay in Product amount option (if allow_payment_in_product_currency and product currency in supported currencies array and it not paypal main currency
		$product_payment = array();
		if(Config::get('payment.allow_payment_in_product_currency')
				&& $order_currency != $main_currency
					&& in_array($order_currency, $paypal_supported_currency_arr))
		{
			$product_currency['allow_product_currency'] = 1;
			$product_amount_arr = $inv_service->calculateOrderAmount($order_currency, $order_amount, $order_currency);
			$product_currency_amt = number_format ($product_amount_arr['amount'], 2, '.','');
			$product_currency['text'] = "Pay in ".$order_currency." ".$product_currency_amt;
			$product_currency['amount'] = $product_currency_amt;
			$product_currency['currency'] = $order_currency;
		}
		$currency_pay_note_msg = '';
		if(Config::get('payment.currency_conversion_fees_paid_by') == 'buyer' && Config::get("webshoppack.site_exchange_rate") != ""
			&& Config::get("webshoppack.site_exchange_rate") > 0)
		{
			$currency_pay_note_msg = trans('payCheckOut.transaction_product_currency_note_msg');
			$currency_pay_note_msg = str_replace('VAR_PRODUCT_CURRENCY', $order_currency, $currency_pay_note_msg);
			$currency_pay_note_msg = str_replace('VAR_CONVERSTION_RATE', Config::get("webshoppack.site_exchange_rate"), $currency_pay_note_msg);
		}

		$payment_curr_options['product_currency'] = $product_currency;

		if(Config::get('payment.allow_payment_in_multi_currency'))
		{
			$temp_curr_arr = array();
			array_push($temp_curr_arr, $main_currency);
			array_push($temp_curr_arr, $order_currency);
			$other_currency = array_diff($paypal_supported_currency_arr, $temp_curr_arr);

			// show other option when paypal supported currencies have more option except paypal main currency and user defined currency. if except these options having more than one option then show drop down else show only that payment button alone
			$other_currency_arr = array();
			$inc =0;
			foreach($other_currency as $other)
			{
				$amount_arr = $inv_service->calculateOrderAmount($order_currency, $order_amount, $other);
				$currency_amt = number_format ($amount_arr['amount'], 2, '.','');

				$other_currency_arr[$inc]['text'] = "Pay in ".$other." ".$currency_amt;
				$other_currency_arr[$inc]['amount'] = $currency_amt;
				$other_currency_arr[$inc++]['currency'] = $other;
			}
			$payment_curr_options['other_currency'] = $other_currency_arr;
		}

		$details = array();
		$details['disp_amount'] = $disp_amount;
		$details['payment_curr_options'] = $payment_curr_options;
		$details['currency_pay_note_msg'] = $currency_pay_note_msg;
		return $details;
	}

	public function getValidCurrencyForGateway($gateway, $currency)
	{
		//amount depending on the gateway chosen
		if($gateway == 'paypal')
		{
			if ($currency == '')
			{
				$currency = Config::get('payment.paypal_main_currency');
			}
			else if($currency != '' AND !in_array($currency, explode(',', Config::get('payment.paypal_supported_currencies'))))
				$currency = Config::get('payment.paypal_main_currency');
		}
		return $currency;
	}
	public function getSellerItems($user_id)
	{
		$list_arr = array('' => trans('common.select_option'));
		$q = MpProduct::select('id','product_code','product_name')->where('product_status', '=', 'Ok')
						->where('product_user_id', '=', $user_id);
		$arr = $q->get();
		foreach($arr AS $rec)
		{
			$list_arr[$rec->id] = $rec->product_code.'-'.$rec->product_name;
		}
		return $list_arr;

	}
	public function getOrderVRules($field_name)
	{
		$rules['user_code'] = 'Required|exists:users,user_code';
		$rules['price_currency'] = 'Required';
		$rules['product_id'] = 'Required';
		$rules['product_price'] = 'Required|numeric';
		if(isset($rules[$field_name]))
			return $rules[$field_name];
		else
			return '';

	}
	public function addCustomOrder($data)
	{
		//check if there is atleast one item
		//not created for the same user, user code is valid
		//the items belongs to the user and price not empty
		//fields: user_code, buyer_notes, items array with product_id, product_currency
		if(isset($data['user_code']) && $data['currency'] )
		{
			$data_arr['user_id'] = User::where('user_code', $data['user_code'])->pluck('user_id');
			if($data_arr['user_id'])
			{
				$data_arr['item_owner_id'] = getAuthUser()->user_id;
				$data_arr['quote_code'] = CUtil::generateRandomUniqueCode('Q', 'mp_order', 'quote_code');
				$data_arr['currency'] = isset($data['currency'])? $data['currency'] : '';
				$data_arr['buyer_notes'] = isset($data['buyer_notes'])? $data['buyer_notes'] : '';
				$data_arr['is_custom_order'] = 1;
				$data_arr['date_added'] = new DateTime;
				$order = new ShopOrder();
				$order_id = $order->addNew($data_arr);
				if($order_id)
				{
					//add the items
					$ids_arr = $this->getSellerItems($data_arr['item_owner_id']);
					foreach($data['items'] as $item)
					{
						if(key_exists($item['product_id'], $ids_arr))
						{
							$item_arr['amount'] = $item['product_price'];
							$item_arr['item_id'] = $item['product_id'];
							$item_arr['order_id'] = $order_id;
							$item_arr['item_type'] = 'product';
							$order = new ShopOrderItem();
							$order->addNew($item_arr);
						}
					}
					//send notify mail to admin and user
					$this->sendMailNotificationForQuote($order_id);
									echo 'quote_code'. $data_arr['quote_code'];
					return $data_arr['quote_code'];
				}

			}
		}
		return false;
	}
	public function sendMailNotificationForQuote($order_id)
	{
		$order_arr = ShopOrder::select("quote_code","item_owner_id", "user_id", "currency", "buyer_notes", "is_custom_order", "status", "date_added")->where('id', $order_id)->first();
		$items_arr = ShopOrderItem::LeftJoin('mp_product', 'mp_order_item.item_id', '=', 'mp_product.id')->
								where('mp_order_item.order_id', $order_id)->get(array('mp_product.id', 'product_name', 'product_code', 'amount', 'url_slug'));
		//todo generate link for quote code for the buyer to view
		if(count($order_arr))
		{
			$arr['buyer_details'] 	= CUtil::getUserDetails($order_arr['user_id'], array('admin_profile_url', 'email', 'display_name','profile_url'));
			$arr['seller_details'] 	=  CUtil::getUserDetails($order_arr['item_owner_id'], array('admin_profile_url', 'email', 'display_name','profile_url'));
			$arr['quote_code'] 		= $order_arr['quote_code'];
			$arr['user_id'] 		= $order_arr['user_id'];
			$arr['item_owner_id'] 	= $order_arr['item_owner_id'];
			$arr['currency'] 	= $order_arr['currency'];
			$arr['buyer_notes'] 	= $order_arr['buyer_notes'];
			$inc = 0;
			foreach($items_arr as $item)
			{
				$arr['items'][$inc]['product_code'] = $item['product_code'];
				$arr['items'][$inc]['product_name'] = $item['product_name'];
				$arr['items'][$inc]['amount'] = $item['amount'];
				$arr['items'][$inc]['view_url'] = $this->getProductViewURL($item['id'], $item);
				$inc++;
			}
			if($inc)
			{
				$mailer = new AgMailer;
				$arr['subject'] = 'New quote order created: '.$arr['quote_code'];
				$mailer->sendAlertMail('sale', 'emails.payment.quoteNotificationForAdmin', $arr);
				if(isset($arr['seller_details']['email']))
				{
					$arr['to_email'] = $arr['seller_details']['email'];
					$mailer->sendUserMail('sale', 'emails.payment.quoteNotificationForSeller', $arr);
				}

				if(isset($arr['buyer_details']['email']))
				{
					$arr['to_email'] = $arr['buyer_details']['email'];
					$mailer->sendUserMail('sale', 'emails.payment.quoteNotificationForBuyer', $arr);
				}
			}

		}
	}

	//Functions for credits start
	public function checkValidCommonInvoiceId($common_invoice_id)
	{
		$invoice_details = array();
		$manage_invoice_obj = Products::initializeCommonInvoice();
		$invoice_details = $manage_invoice_obj->getCommonInvoiceDetailsById($common_invoice_id);
		if(count($invoice_details) > 0) {
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($invoice_details['user_id'] == $logged_user_id && strtolower($invoice_details['status']) == 'unpaid') {
				return $invoice_details;
			}
		}
		return $invoice_details;
	}

	public function checkValidCreditId($credit_id)
	{
		$credit_details = array();
		$manage_credits_obj = Products::initializeManageCredits();
		$credit_details = $manage_credits_obj->getCreditsDetailsById($credit_id);
		if(count($credit_details) > 0) {
			$logged_user_id = BasicCUtil::getLoggedUserId();
			if($credit_details['credited_to'] == $logged_user_id && strtolower($credit_details['paid']) == 'yes') {
				return $credit_details;
			}
		}
		return $credit_details;
	}

	public function addCreditReceiversDetails($common_invoice_id, $common_invoice_details, $payment_gateway_chosen = 'paypal')
	{
		$receiver_emails = array();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$admin_id = Config::get("generalConfig.admin_id");
		$order_obj = Webshoporder::initialize();

		$receiver_emails[$admin_id] = Config::get('payment.paypal_merchant_email');
		$order_obj->setReceiverPaypalEmail($receiver_emails);
		$all_receivers = $order_obj->addCreditReceiversDetails($common_invoice_id, $common_invoice_details, $logged_user_id, $payment_gateway_chosen);
		return $all_receivers;
	}
	public function setOrderItems($order_id = 0)
	{
		$order_obj = Webshoporder::initialize();
		$order_item_details = $order_obj->getOrderitemDetails($order_id);
		$product_ids = array();
		foreach($order_item_details as $order_item)
		{
			$this->cart_item_details_arr[] = array('item_id'=>$order_item->item_id,
													'item_owner_id' => $order_item->item_owner_id,
													'item_qty' => $order_item->item_qty,
													'item_type' => 'product',
													);
			$product_ids[]= $order_item->item_id;
		}
		$prod_obj = Products::initialize();
		$this->setProductDataFromOrder($product_ids, $prod_obj, $order_id);
		//echo "<pre>";print_r($this->cart_item_details_arr);echo "</pre>";exit;
	}
	public function setProductDataFromOrder($ids, $prod_obj, $order_id)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$prod_obj->setFilterProductIds($ids);
		$prod_obj->setFilterProductExpiry(true);
		$product_details = $prod_obj->getProductsList();
		if(count($product_details) > 0)
		{
			$billing_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('order_id' => $order_id));
			if(count($billing_details) > 0)
				$billing_details = $billing_details->toArray();
			//echo "<pre>";print_r($billing_details);echo "</pre>";exit;
			$shipping_country_id_in_cookie = CUtil::getShippingCountry();
			foreach($product_details as $product)
			{
				$qty = 0;
				$cart_id = 0;
				foreach($this->cart_item_details_arr as $cart_key => $cart_item)
				{
					if($cart_item['item_id'] == $product['id'] && $cart_item['item_type'] == 'product') {
						//$cart_id = $cart_item['cart_id'];
						$qty = $cart_item['item_qty'];
					}
				}

				$product_price = 0;
                $price_group = $this->getPriceGroupsDetailsNew($product['id'], $logged_user_id, $qty);
				if(count($price_group) > 0) {
					$product_price = $price_group['discount'];
					$product['product_price_currency'] = $price_group['currency'];
				}
				$product['product_price'] = $product_price;

				if($product['is_free_product'] == 'Yes')
					$product['price'] = $product['product_price'] = 0;
				else
					$product['price'] = $product['product_price'];

				$shipping_fees = $shipping_company = 0;
				$shipping_company_name = '';
				$is_shipping_country_set = false;
				$is_shipping_country_match = true;
				$is_disabled_to_checkout = false;
				if($product['is_downloadable_product'] == 'No')
				{
					$this->is_shipping_needed = true;

					//Shipping fee calculation based on shipping template
					if($product['shipping_template'] > 0) {
						$is_shipping_country_set = true;
						$shipping_companies_details = $this->getShippingTemplateDetails($cart_id, $product['shipping_template'], $product['id'], $qty);
						//echo "<pre>";print_r($shipping_companies_details);echo "</pre>";
						if(count($shipping_companies_details) > 0) {
							$shipping_company = $shipping_companies_details['shipping_company_id_selected'];
							$shipping_company_name = $shipping_companies_details['shipping_company_name_selected'];
							$shipping_fees = $shipping_companies_details['shipping_company_fee_selected'];
						}
						if(isset($shipping_companies_details['shipping_companies_list']) && !empty($shipping_companies_details['shipping_companies_list']))
						{
							$comp_lists = $shipping_companies_details['shipping_companies_list'];
							//echo "<pre>";print_r($comp_lists);echo "</pre>";
							foreach($comp_lists as $companies_list)
							{
								if($companies_list['company_id'] == $shipping_company)
								{
									$error_msg = (isset($companies_list['error_message']) && $companies_list['error_message']!='') ? $companies_list['error_message'] : '';
									if($error_msg!='')
									{
										$this->disable_checkout = true;
										$this->disable_checkout_err_msg = trans('payCheckOut.check_shipping_company');
										$is_disabled_to_checkout = true;
										break;
									}
								}
							}
						}
					}
					if($shipping_company_name == '') {
						$this->disable_checkout = true;
						$this->disable_checkout_err_msg = trans('payCheckOut.check_shipping_company');
						$is_disabled_to_checkout = true;
					}
				}
				$shipping_fees = ($shipping_fees > 0) ? $shipping_fees : 0;
				//$product['shipping_countries'] = $shipping_country_list;
				$product['is_disabled_to_checkout'] = $is_disabled_to_checkout;
				$product['product_shipping_company'] = $shipping_company;
				$product['product_shipping_company_name'] = $shipping_company_name;
				$product['product_shipping_fees'] = $shipping_fees;
				$product['product_shipping_country_set'] = $is_shipping_country_set;
				$product['product_shipping_country_match'] = $is_shipping_country_match;
				$this->shipping_country_id = $shipping_country_id_in_cookie;
				//echo "<br>shipping_country_id: ==> ".$shipping_country_id;

				//echo "<br>".$product['id'].'=>'.$product['product_name'];
				$product_tax_details = array();
				$product_tot_tax_amount = 0;

				$product_taxes = Webshoptaxation::ProductTaxations()->getProductTaxations(array('product_id' => $product['id']));

				if(!empty($product_taxes) && count($product_taxes) > 0)
				{
					$inc=0;
					foreach($product_taxes as $tax)
					{
						$tax_details['id'] = $tax['id'];
						$tax_details['product_id'] = $tax['product_id'];
						$tax_details['taxation_id'] = $tax['taxation_id'];
						$tax_details['tax_name'] = $tax['taxations']['tax_name'];
						$tax_details['tax_fee'] = $tax_fee = $tax['tax_fee'];
						$tax_details['fee_type'] = $fee_type =  $tax['fee_type'];
						$tax_label = '<span class="text-muted">'.$tax['taxations']['tax_name'].':</span> ';
						if(strtolower($fee_type) == 'percentage')
						{
							$tax_label .= '<strong>'.$tax['tax_fee'].'%</strong>';
							$calc_tax_amount = ($product['product_price'] * $qty) * ($tax_fee/100);
							$tax_details['calculated_tax_amount'] = $calc_tax_amount;
						}
						else
						{
							$tax_label .= Config::get('generalConfig.site_default_currency').' <strong>'.$tax['tax_fee'].' </strong> / '.trans('showCart.quantity');
							$tax_details['calculated_tax_amount'] = $tax_fee * $qty;
						}
						$tax_details['tax_label'] = $tax_label;

						$product_tax_details[$inc] = $tax_details;
						$product_tot_tax_amount += $tax_details['calculated_tax_amount'];
						$inc++;
					}
				}
				$product['product_tax_details'] = $product_tax_details;
				$product['product_tot_tax_amount'] = $product_tot_tax_amount;
				//$product['product_tot_shipping_amount'] = $shipping_fees;

				//Calculate sub total
				$product['product_sub_total'] = ($product['product_price'] * $qty) + $product['product_tot_tax_amount'] + $product['product_shipping_fees'];


				/*$product['product_services'] = array();
				$product['product_services'] = $this->getProductServicesList($product['id']);*/

				if($product['price'] > 0)
				{
					if($product['product_price_currency'] == $this->checkout_currency)
					{
						$this->product_details_arr[$product['id']] = $product;
					}
					else //Condition to unset cart item details array if selected payment type is not set for corresponding product
					{
						foreach($this->cart_product_ids_arr as $key => $product_id)
						{
							if($product_id == $product['id'])
								unset($this->cart_product_ids_arr[$key]);
						}
						foreach($this->cart_item_details_arr as $cart_key => $cart_item)
						{
							if($cart_item['item_id'] == $product['id'] && $cart_item['item_type'] == 'product')
								unset($this->cart_item_details_arr[$cart_key]);
						}
					}
				}
				else
				{
					$this->product_details_arr[$product['id']] = $product;
				}


				//$productservicedet = $this->getProductServicesList($product['id']);

			}
		}
		//echo "<pre>";print_r($this->product_details_arr);echo "</pre>";
		return $this->product_details_arr;
	}
	//Functions for credits end
}