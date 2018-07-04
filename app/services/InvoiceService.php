<?php

class InvoiceService
{
	public function generateInvoiceForOrder($order_id, $input_arr = array(), $currency = 'USD', $status='pending_payment')
	{
		//$input_arr
		//possible keys -> payment_gateway_chosen, created_by,
		//Add Invoice Record

		$order_details = $this->getOrderDetails($order_id);
		$coupon_code = "";
		if($order_details['discount_amount'] > 0)
		{
			$coupon_code = $order_details['coupon_code'];
		}

		$logged_user_id = (isLoggedin()) ? getAuthUser()->user_id : 0;
		$inv_arr['created_by_user_id'] = $logged_user_id;
		$inv_arr['date_added'] = 'NOW()';
		$inv_arr['user_id'] = $logged_user_id;
		$inv_arr['item_owner_id'] = $order_details['item_owner_id'];
		$inv_arr['seller_notes'] = $order_details['seller_notes'];
		$inv_arr['seller_payment_type'] = $order_details['currency'];
		$inv_arr['invoice_status'] = $status;
		$inv_arr['discount_amount'] = $order_details['discount_amount'];
		$inv_arr['auto_added'] = 0; //only for invoices created for recurring , it will be set as
		$inv_arr['coupon_code'] = $coupon_code;
		$inv_arr['billing_id'] = $order_details['billing_id'] ;

		$currency = $this->getValidCurrencyForGateway($input_arr['payment_gateway_chosen'], $currency);
		$inv_arr['currency'] = $currency;
		$inv_arr['conversion_fee_paid_by'] = Config::get("payment.currency_conversion_fees_paid_by");
		if($order_details['currency'] == $currency)
		{
			$inv_arr['conversion_fee_paid_by'] = 'none';
		}
		$inv_arr['base_amount'] = $order_details['discounted_amount'];
		$inv_arr['base_currency'] = $order_details['currency'];

		$invoice_amount = 0;
		if($order_details['discounted_amount'] > 0)
		{
			//with fees
			$amount_arr = $this->calculateOrderAmount($order_details['currency'], $order_details['discounted_amount'], $currency, 1);
			$amount_arr_without_conversion_fee = $this->calculateOrderAmount($order_details['currency'], $order_details['discounted_amount'], $currency, 0);
			$inv_arr['conversion_fee']  = $amount_arr['amount'] - $amount_arr_without_conversion_fee['amount'];
			if($inv_arr['conversion_fee'] > 0)
				$inv_arr['conversion_fee_currency']  = $currency;
			$invoice_amount = $amount_arr['amount'];
		}
		$inv_arr['invoice_total_amount'] = $invoice_amount;
		$inv_id = $this->addNewInvoice($inv_arr);
		$order_item_details = $order_details['item_details'];
		$fees = 0;
		foreach($order_item_details as $item)
		{
			if(isset($item['id']))
			{
				$invoice_item_arr = array();
				$invoice_item_arr['item_type'] = 'product';
				$invoice_item_arr['user_id'] = $logged_user_id;
				$price = isset($item['product_price']) ? $item['product_price'] : 0;
				$item_amount = 0;
				if($price > 0)
				{
					$item_amount = CUtil::convertAmountToCurrency($price, $order_details['currency'], $currency, $apply_exchange_fee = false);
				}
				$invoice_item_arr['item_amount'] = $item_amount;
				$invoice_item_arr['base_amount'] = $price;
				$invoice_item_arr['base_currency'] = $order_details['currency'];
				$invoice_item_arr['invoice_id'] = $inv_id;
				$invoice_item_arr['product_id'] = $item['id'];
				$invoice_item_arr['date_added'] = 'NOW()';
				$invoice_item_arr['item_owner_id'] = $order_details['item_owner_id'];
				$fee_arr = $this->getItemTransactionFee($item['id'], $price);
				$invoice_item_arr['item_site_transaction_fee'] = $fee_arr['fees'];
				$invoice_item_arr['fee_settings'] = $fee_arr['fee_settings'];
				$invoice_item_obj = new MpInvoiceItem;
				$inv_item_id = $invoice_item_obj->addNew($invoice_item_arr);
			}
		}
		$this->updateInvoiceRecordAfterItemAdd($inv_id);
		$this->updateOrderStatus($inv_id, $order_details['id']);
		$this->newInvoiceGeneratedHook($inv_id);
		return $inv_id;
	}

	public function calculateOrderAmount($order_currency, $order_amount, $currency, $apply_exchange_rate = 0)
	{
		//if the base currency and the passed currency are the same, return order amount
		$arr['exchange_rate']  = 0;
		$arr['site_service_rate']  = 0;
		$arr['currency']  = $currency;
		$arr['amount']  = 0;
		$arr['conversion_notes'] = '';
		$arr['conversion_fee_paid_by'] = 'none';


		$arr['conversion_notes'] = 'Paying in the same currency as product. No currency conversion applied'."\r\n";
		$arr['amount'] = $order_amount;

		//if the base currency and the passed currency are the same, return order amount
		if($order_currency == $currency)
		{
			$arr['conversion_notes'] = 'Paying in the same currency as product. No currency conversion applied'."\r\n";
			$arr['amount'] = $order_amount;
		}
		else
			//convert the order amount to USD -> get the exchange rate + service fee , apply to the amount
		{
			$ex_det = Products::chkIsValidCurrency($order_currency);
			if(count($ex_det) > 0)
			{
				$arr['exchange_rate']  = $ex_det['exchange_rate'];
				if($order_currency != 'USD')
					$arr['conversion_notes'] .= ' Exchange rate 1 USD =  '.$arr['exchange_rate'] . $order_currency. "\r\n";
			}
			//if the conversion fee has to be paid by buyer, apply the conversion rate
			if(Config::get("payment.currency_conversion_fees_paid_by") == 'buyer' && $apply_exchange_rate)
			{
				$arr['site_service_rate'] = ($arr['exchange_rate'] * (doubleval(Config::get("webshoppack.site_exchange_rate")) * 0.01));
				$arr['conversion_notes'] .= 'Conversion fees of '.Config::get("webshoppack.site_exchange_rate"). ' % paid by buyer ';
			}
			$arr['conversion_fee_paid_by'] = Config::get("payment.currency_conversion_fees_paid_by");
			$rate = $arr['exchange_rate'] - $arr['site_service_rate'];
			if($arr['site_service_rate'] > 0)
				$arr['conversion_notes'] .= ' Exchange rate 1 USD =  '.$rate . $order_currency. "\r\n";
			$arr['amount_usd'] = $arr['amount'] = $order_amount / $rate;

			if($currency != 'USD')
			{
				$ex_det = Products::chkIsValidCurrency($currency);
				if(count($ex_det) > 0)
				{
					$rate  = $ex_det['exchange_rate'];
					$arr['conversion_notes'] .= ' Exchange rate 1 USD =  '.$rate . $currency. "\r\n";
					$arr['amount'] = $arr['amount_usd'] * $rate;
					$arr['conversion_notes'] .= $arr['amount_usd']. ' USD =  '.	$arr['amount'] . $currency. "\r\n";
				}
				else
					$arr['amount'] = 0;
			}
			$arr['conversion_notes'] .= $order_amount. $order_currency. ' = '. $arr['amount'] . $currency. "\r\n";
		}
		return $arr;

	}
	public function getOrderDetails($order_id)
	{
		$order_details = MpOrder::where('id', $order_id)->first();
		$discount_amt = 0;
		$discounted_amt = 0;
		if(count($order_details)> 0)
		{
			$i_details = $this->getOrderItemDetails($order_id, $order_details);

			$order_details['item_details'] =$i_details['order_item_details_arr'];
			$discounted_amt = $total_amt = $order_details['total_amount'] =$i_details['order_total_amount'];
			$coupon_code = $order_details['coupon_code'];
			$item_owner_id = $order_details['item_owner_id'];
			$currency = $order_details['currency'];

			if(!Config::get('products.item_couponcode_exists'))
			{
				$coupon_code = "";
			}
			if($coupon_code != "")
			{
				$data['coupon_code'] = $coupon_code;
				$data['item_owner_id'] = $item_owner_id;
				if(!($coupon_details = $this->getCouponCodeDetails($data)))
				{
					return $discount_amt."::".$discounted_amt;
				}

				$coupon_price_from = $coupon_details['price_from'];
				$coupon_price_to = $coupon_details['price_to'];
				$offer_amount = $coupon_details['offer_amount'];
				if($coupon_details['currency'] != $currency)
				{
					$coupon_price_from = CUtil::convertAmountToCurrency($coupon_price_from, $coupon_details['currency'], $currency, $apply_exchange_fee = false);
					$coupon_price_to = CUtil::convertAmountToCurrency($coupon_price_to, $coupon_details['currency'], $currency, $apply_exchange_fee = false);
					if($coupon_details['offer_type'] != 'percent')
					{
						$offer_amount = CUtil::convertAmountToCurrency($offer_amount, $coupon_details['currency'], $currency, $apply_exchange_fee = false);
					}
				}

				if(!(($coupon_price_from > 0 and $total_amt < $coupon_price_from) or ($coupon_price_to > 0 and $total_amt > $coupon_price_to)))
				{
					if($coupon_details['offer_type'] == 'percent')
					{
						$discount_amt = $discount_amt + ($total_amt * $offer_amount/100);
					}
					else
					{
						$discount_amt = $discount_amt + $offer_amount;
					}
				}
				$discounted_amt = ($total_amt - $discount_amt);
				if($discounted_amt < 0)
				{
					$discounted_amt = 0;
				}
			}

		}
		$order_details['discount_amount'] = $discount_amt;
		$order_details['discounted_amount'] = $discounted_amt;
		return $order_details;
	}
	public function getOrderItemDetails($order_id, $order_details = array())
	{
		$order_item_details_arr = array();
		$order_total_amount = 0;
		$product_details = MpOrderItem::LeftJoin('mp_product', 'mp_order_item.item_id', '=', 'mp_product.id')->
								where('mp_order_item.order_id', $order_id)->get(array('mp_product.id', 'product_name', 'product_price', 'product_discount_price', 'product_discount_fromdate', 'product_discount_todate',
								'product_price_currency', 'is_free_product', 'amount'));
		if(count($product_details) > 0)
		{
			foreach($product_details as $product)
			{

				if(isset($order_details['is_custom_order']) AND isset($order_details['currency']) AND $order_details['is_custom_order'] )//generated quote, price from the order item table
				{
					$product['product_price'] = $product['price']  = $product['amount'];
					$product['product_price_currency'] = $order_details['currency'];

				}
				else
				{
					if($product['product_discount_todate'] != '0000-00-00' && $product['product_discount_fromdate'] != '0000-00-00')
					{
						$discount_from_date = strtotime($product['product_discount_fromdate']);
						$discount_end_date = strtotime($product['product_discount_todate']);
						$curr_date = strtotime(date('Y-m-d'));
						if($discount_end_date >= $curr_date && $discount_from_date <= $curr_date)
						{
							$product['product_price'] = $product['product_discount_price'];
						}

					}
					if($product['is_free_product'] == 'Yes')
						$product['price'] = $product['product_price'] = 0;
					else
						$product['price'] = $product['product_price'];

				}
				$order_total_amount += $product['price'];
				$order_item_details_arr[$product['id']] = $product;
			}
		}
		$order_item_details_arr['order_total_amount'] = $order_total_amount;
		return compact('order_item_details_arr', 'order_total_amount');
	}

	public function updateOrderStatus($inv_id, $order_id)
	{
		$data_arr['invoice_id'] = $inv_id;
		$data_arr['status'] = 'pending_payment';
		MpOrder::whereRaw('id = ?', array($order_id))->update($data_arr);
	}

	public function updateInvoiceRecordAfterItemAdd($invoice_id)
	{
		$item_details = MpInvoiceItem::Where('invoice_id', $invoice_id)->select(DB::raw('sum(item_amount) as total_items_amt, sum(item_site_transaction_fee) as total_site_fee' ))->first();
		$discount_amount = MpInvoice::where('id', $invoice_id)->pluck('discount_amount');

		$update_arr['invoice_items_amount'] = $item_details['total_items_amt'];
		$update_arr['invoice_site_transaction_fee'] = $item_details['total_site_fee'];
		$update_arr['invoice_total_amount']  = $update_arr['invoice_items_amount'] - $discount_amount;

		MpInvoice::WHERE('id', $invoice_id)->update($update_arr);
	}

	public function getInvoiceSiteTransactionFee($invoice_id)
	{
		$site_fee_arr = array();
		$site_fee_arr['site_transaction_fee'] = 0;
		$site_fee_arr['item_transaction_fee'] = array();

		$invoice_item_details = MpInvoiceItem::whereRaw('invoice_id = ?', array($invoice_id))->get(array('id',  'item_type', 'product_id', 'item_amount'));

		if(count($invoice_item_details) > 0)
		{
			foreach($invoice_item_details as $item)
			{
				$site_fee_arr['site_transaction_fee'] += $this->getInvoiceItemProductTransactionFee($item['product_id'], $item['item_amount']);
				$site_fee_arr['item_transaction_fee'][$item['id']] = $this->getInvoiceItemProductTransactionFee($item['product_id'], $item['item_amount']);
			}
		}
		return $site_fee_arr;
	}

	//Function to get invoice item product site transaction fee
	//pass the item amount if need not be fetched from the table
	public function getItemTransactionFee($item_id, $item_amount = 0, $item_type = 'product')
	{
		$fees = 0;
		$fee_settings = '';
		$product_details = MpProduct::Select('product_price', 'product_price_currency', 'site_transaction_fee_type', 'site_transaction_fee', 'site_transaction_fee_percent', 'global_transaction_fee_used')->whereRaw('id = ? AND is_free_product != \'Yes\'', array($item_id))->first();

		if(count($product_details) > 0 && isset($product_details['product_price']) && $product_details['product_price'] != "")
		{
			$fee_type =  '';
			$fee_percentage = $fee_flat = $fee_percentage_amt = 0;
			$product_price =($item_amount > 0 ) ? $item_amount : $product_details['product_price'];
			if($product_details['global_transaction_fee_used'] == 'Yes')
			{
				if(Config::get("mp_product.item_site_transaction_fee_type"))
				{
					switch(Config::get("mp_product.item_site_transaction_fee_type"))
					{
						case 'Flat':
							//todo convert to base currency
							$fee_flat = Config::get("mp_product.item_site_transaction_fee");
							break;

						case 'Percentage':
							$fee_percentage = Config::get("mp_product.item_site_transaction_fee_percent");
							break;

						case 'Mix':
							$fee_flat = Config::get("mp_product.item_site_transaction_fee");
							$fee_percentage = Config::get("mp_product.item_site_transaction_fee_percent");
							break;
					}
				}
				else
				{
					switch($product_details['site_transaction_fee_type'])
					{
						case 'Flat':
							$fee_flat = $product_details['site_transaction_fee'];
							break;

						case 'Percentage':
							$fee_percentage = $product_details['site_transaction_fee_percent'];
						case 'Mix':
							$fee_flat = $product_details['site_transaction_fee'];
							$fee_percentage = $product_details['site_transaction_fee_percent'];
					}
				}
			}
			//if flat fee and the base currency is not in USD , convert the flat amount to the currency
			$flat_fee_amt = $fee_flat;
			if($fee_flat > 0)
			{
				$fee_settings = "Flat fee : $fee_flat USD";
				if($product_details['product_price_currency'] != 'USD')
				{
					$flat_fee_amt = CUtil::convertAmountToCurrency($fee_flat, 'USD', $product_details['product_price_currency'], false);
					$fee_settings .= " = $flat_fee_amt ".$product_details['product_price_currency'];
				}
			}
			Log::info('Site feee '.$product_price);
			if($fee_percentage > 0)
			{
				$fee_percentage_amt = $fee_percentage * 0.01 * $product_price;
				$fee_settings .= "Percentage: $fee_percentage = $fee_percentage_amt . ";
			}
			Log::info('AFee amot'.$flat_fee_amt .'=='. $fee_percentage_amt);
			$fees = $flat_fee_amt + $fee_percentage_amt;
			$fee_settings .= "Total: $fees ".$product_details['product_price_currency'];
			//check with min commission and max commission and store the notes for the commission setting
		}
		return compact('fees', 'fee_settings');
	}

	public function getInvoiceItemProductTransactionFee($product_id, $invoice_item_amount = 0)
	{
		$transaction_fee = 0;
		$product_details = MpProduct::Select('product_price',  'site_transaction_fee_type', 'site_transaction_fee', 'site_transaction_fee_percent', 'global_transaction_fee_used')->whereRaw('id = ? AND is_free_product != \'Yes\'', array($product_id))->first();
		if(count($product_details) > 0 && isset($product_details['product_price']) && $product_details['product_price'] != "")
		{
			if($product_details['global_transaction_fee_used'] == 'Yes')
			{
				if(Config::get("mp_product.item_site_transaction_fee_type"))
				{
					switch(Config::get("mp_product.item_site_transaction_fee_type"))
					{
						case 'Flat':
							$transaction_fee += Config::get("mp_product.item_site_transaction_fee");
							break;

						case 'Percentage':
							if($product_details['product_price'] > 0) //Condition to calculate site transaction fee from product price if prodcut price greater than zero
								$transaction_fee += ($product_details['product_price'] * Config::get("mp_product.item_site_transaction_fee_percent")/100);
							else
								$transaction_fee += ($invoice_item_amount * Config::get("mp_product.item_site_transaction_fee_percent")/100);
							break;

						case 'Mix':
							$transaction_fee += Config::get("mp_product.item_site_transaction_fee");
							if($product_details['product_price'] > 0) //Condition to calculate site transaction fee from product price if prodcut price greater than zero
								$transaction_fee += ($product_details['product_price'] * Config::get("mp_product.item_site_transaction_fee_percent")/100);
							else
								$transaction_fee += ($invoice_item_amount * Config::get("mp_product.item_site_transaction_fee_percent")/100);
							break;
					}
				}
				else
				{
					switch($product_details['site_transaction_fee_type'])
					{
						case 'Flat':
							$transaction_fee += $product_details['site_transaction_fee'];
							break;

						case 'Percentage':
							if($product_details['product_price'] > 0) //Condition to calculate site transaction fee from product price if prodcut price greater than zero
								$transaction_fee += ($product_details['product_price'] * $product_details['site_transaction_fee_percent']/100);
							else
								$transaction_fee += ($invoice_item_amount * $product_details['site_transaction_fee_percent']/100);
							break;

						case 'Mix':
							$transaction_fee += $product_details['site_transaction_fee'];

							if($product_details['product_price'] > 0) //Condition to calculate site transaction fee from product price if prodcut price greater than zero
								$transaction_fee += ($product_details['product_price'] * $product_details['site_transaction_fee_percent']/100);
							else
								$transaction_fee += ($invoice_item_amount * $product_details['site_transaction_fee_percent']/100);
							break;
					}
				}
			}
		}
		return $transaction_fee;
	}

	public function updateInvoiceSiteTransactionFee($data_arr)
	{
		if(!isset($data_arr['invoice_id']) OR !isset($data_arr['invoice_id']))
			return;

		//Update mp_invoice site transaction fee
		$input_arr['invoice_site_transaction_fee'] = $data_arr['site_fee_arr']['site_transaction_fee'];
		MpInvoice::whereRaw('id = ?', array($data_arr['invoice_id']))->update($input_arr);

		//Update mp_invoice item site transaction fee
		foreach($data_arr['site_fee_arr']['item_transaction_fee'] as $invoice_item_id => $item_transaction_fee)
		{
			$update_arr['item_site_transaction_fee'] = $item_transaction_fee;
			MpInvoiceItem::whereRaw('id = ?', array($invoice_item_id))->update($update_arr);
		}
	}

	public function addNewInvoice($data_arr)
	{
		$inv = new MpInvoice;
		$data_arr['date_added'] = new DateTime;
		Log::info(print_r($data_arr, 1));
		$arr = $inv->filterTableFields($data_arr);
		$inv_id = $inv->insertGetId($arr);
		if($data_arr['coupon_code'] != '')
			$this->updateCouponCodeUsedCount();
		//add code to insert invoice log
		$log_arr['invoice_id'] = $inv_id;
		if(!isset($data_arr['log_text']))
			$log_text = 'New invoice created';
		else
			$log_arr['log_text'] = 	$data_arr['log_text'];
		//todo send mail as invoice created to the cc emails configured

		$this->addInvoiceLog($inv_id, $log_arr);
		return $inv_id;
	}



	public function addInvoiceLog($invoice_id, $data_arr)
	{
		if(isset($data_arr['log_text']))
		{
			$status = isset($data_arr['status']) ? $data_arr['status'] : '';
			Db::Table('transaction')->insert(
			array(
				'txn_date' => new DateTime,
				'invoice_id' => $invoice_id,
				'notes'  =>  $data_arr['log_text'],
				'status' => $status
		));
		}
	}
	public function getInvoiceItemDetails($invoice_id, $item_id = 0)
	{
		$item_details = MpInvoiceItem::Select('product_code', 'product_name', 'mp_invoice_item.*')->
			LeftJoin('mp_product', 'mp_product.id', '=', 'mp_invoice_item.product_id')->
			where('invoice_id', $invoice_id)->get();
		Log::info('Count'.count($item_details).'Invoice'.$invoice_id);
		return $item_details;
	}
	public function newInvoiceGeneratedHook($inv_id, $arr=array())
	{
		$arr['log_text'] =(!isset($arr['log_text'])) ?  'New invoice generated'	: $arr['log_text'];
		$this->addInvoiceLog($inv_id,$arr);
		$inv_arr = MpInvoice::where('id', $inv_id)->first();
		if(Config::get('generalConfig.invoice_email') != "" && $inv_id > 0 )
		{
			$buyer_details = CUtil::getUserDetails($inv_arr['user_id'], array('admin_profile_url', 'email', 'display_name','profile_url'));
			$seller_details = CUtil::getUserDetails($inv_arr['item_owner_id'], array('admin_profile_url', 'email', 'display_name','profile_url'));
			$inv_details = MpInvoice::where('id', $inv_id)->first();
			$inv_item_details = $this->getInvoiceItemDetails($inv_id);

			$data = array('invoice_id' => $inv_id,
							'siteName'	=> Config::get('site.site_name'),
							'invoice_amount' =>  $inv_details['currency']." ".number_format ($inv_details['invoice_total_amount'], 2, '.',''),
							'buyer_details' => $buyer_details,
							'seller_details' => $seller_details,
							'invoice_status' => 'Pending Payment',
							'inv_details'    => $inv_details,
							'inv_item_details'    => $inv_item_details,
							'mail_for' => 'new_invoice'
						);
			//Mail::send('emails.request.newInvoiceGenerated', $data,  function($m)
			try {
				Mail::send('emails.payment.invoiceNotificationToAdmin', $data,  function($m)
				{
					$to_arr = explode(',', Config::get('generalConfig.invoice_email'));
					foreach($to_arr as $to)
					{
						if($to != '')
							$m->to($to);
					}
					$m->subject('New invoice generated');
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}

	/*public function updatePaymentGatewayDetails($invoice_id, $arr)
	{
		$payment_gateway_fields = array('payment_gateway_status', 'payment_gateway_ref_id', 'payee_account',
									 'payer_account', 'payment_gateway', 'amount_paid', 'payment_gateway_fee');
		$update_arr = array();
		foreach($payment_gateway_fields as $f_name)
		{
			if(isset($arr[$f_name]))
			{
				$update_arr[$f_name] = $arr[$f_name];
			}
		}
		if(count($update_arr))
		{
			MpInvoice::where('id', $invoice_id)->update($update_arr);
		}
	}


	public function getGatewayPaymentDetails($quote_id, $gateway)
	{
		//array returned will be of the format

		//main_currency, main_currency_amout, quote_currency, quote_currency_amount, other currency_arr
		//currency, amount
		$other_currency_arr = array();
		$quote_currency = '';
		$quote_currency_amount = 0;

		$quote_details = RequestQuotes::find($quote_id);
		$supported_currency_arr = explode(",", Config::get('payment.'.$gateway.'_supported_currencies'));
		$main_currency = Config::get('payment.'.$gateway.'_main_currency');
		$amount_arr = $this->calculateQuoteInvoiceAmount($quote_id, $main_currency);
		$main_currency_amount = $amount_arr['amount'];
		if(Config::get('payment.allow_payment_in_quote_currency')
			&& $quote_details['quote_currency'] != $main_currency
			&& in_array($quote_details['quote_currency'], $supported_currency_arr))
		{
			$quote_currency = $quote_details['quote_currency'];
			$amount_arr = $this->calculateQuoteInvoiceAmount($quote_details['id'], $quote_details['quote_currency']);
			$quote_currency_amount = $amount_arr['amount'];
		}
		if(Config::get('payment.allow_payment_in_multi_currency'))
		{
			$inc =0;
			foreach($supported_currency_arr as $currency)
			{
				if($currency == $main_currency OR $currency == $quote_currency)
				{
					continue;
				}
				$amount_arr = $this->calculateQuoteInvoiceAmount($quote_details['id'], $currency);
				$other_currency_arr[$inc]['amount'] = $amount_arr['amount'];;
				$other_currency_arr[$inc]['currency'] = $currency;
				$inc++;
			}
		}
		return compact('main_currency', 'main_currency_amount', 'quote_currency', 'quote_currency_amount', 'other_currency_arr');
	}*/
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
}