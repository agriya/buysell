<?php
class CheckOutService extends ShowCartService
{
	public $item_owner_id = 0;
	public $cookie_id = 0;
	public $cart_item_details_arr = array();
	public $is_purchasing_own_item = false;
	public $checkout_currency = "";
	public $product_details_arr = array();
	public $cart_product_ids_arr = array();
	public $delete_cart = false;
	public $buy_now_add_cart = true;
	public $is_shipping_needed = false;
	public $disable_checkout = false;
	public $disable_checkout_err_msg = '';
	public $shipping_country_id = 0;


	function __construct()
	{
		$this->cookie_id = $this->getCartCookieId();
    }

    public function populateCheckedItems($currency, $pid, $type, $cart_obj, $prod_obj, $owner_id)//$owner_id,
	{
		if(CUtil::chkIsAllowedModule('variations'))
			 $this->variation_service = new VariationsService();

		//$this->item_owner_id = $owner_id;
		$this->checkout_currency = $currency;
		$proceed = false;
		if(isset($pid) && $pid >0) {
			$proceed = $this->populateBuyNowProductDetails($pid, $type, $cart_obj, $prod_obj, false);
		}
		else {
			$proceed = $this->populateCartDetails($cart_obj, $prod_obj, $owner_id, false);
		}
		//Condition checked to purchase either cart items or purchase product when buynow option is clicked
		if($proceed)
		{
			if (count($this->cart_product_ids_arr))
			{
				$this->setProductDataForCart($this->cart_product_ids_arr, $prod_obj, false);
			}
			if(count($this->cart_item_details_arr))
			{
				foreach($this->cart_item_details_arr as $key => $item)
				{
					$is_status = $this->getItemData($item['item_id'], 'product_status', $item['item_type']);
					if(strtolower($is_status) != 'ok')
						unset($this->cart_item_details_arr[$key]);

					# Check the variation stock
					if(CUtil::chkIsAllowedModule('variations') && isset($this->variation_service)&& $item['matrix_id'] >0)
					{
						if(!$this->variation_service->chkIsAllowedVariationStock($item['item_id'], $item['matrix_id'], $item['item_qty']))
						{
							unset($this->cart_item_details_arr[$key]);
						}
					}
				}
				if($this->is_purchasing_own_item) //Condition to check is logged in user is allowed to purchase his own items
				{
					//Condition to allow users to purchas his own items if allow purchase own item config variable is set to true
					if(Config::get('webshoppack.allow_to_purchase_own_item'))
					{
						$this->is_purchasing_own_item = false;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function populateCartDetails($cart_obj, $prod_obj, $owner_id, $allow_cache = true)
	{
		$cart_obj->setFilterItemOwnerId($owner_id);
		$cart_obj->setFilterCookieId($this->cookie_id);
		$cart_details = $cart_obj->contents();
		if(count($cart_details) > 0)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			foreach($cart_details as $cart)
			{
				$variations_det_arr = array();
				if(CUtil::chkIsAllowedModule('variations') && $cart['matrix_id'] != 0)
				{
					$variation_service = new VariationsService();
					$variations_det_arr = $variation_service->populateVariationAttributesByMatrixId($cart['item_id'], $cart['matrix_id'], $cart['item_owner_id']);
				}
				$this->cart_item_details_arr[] = array('cart_id' => $cart['id'],
												 'item_id' => $cart['item_id'],
												 'item_owner_id' => $cart['item_owner_id'],
												 'item_qty' => $cart['qty'],
												 'item_type' => 'product',
												 'matrix_id' => $cart['matrix_id'],
												 'use_giftwrap' => $cart['use_giftwrap'],
												 'variation_details' => $variations_det_arr );
				$this->cart_product_ids_arr[] = $cart['item_id'];
				if($logged_user_id != 0 && ($logged_user_id == $cart['item_owner_id']))
				{
					$this->is_purchasing_own_item = Config::get('webshoppack.allow_to_purchase_own_item')?false:true;
				}
			}
			//echo "<pre>";print_r($this->cart_item_details_arr);echo "</pre>";exit;
			//echo "<pre>";print_r($this->cart_product_ids_arr);echo "</pre>";//exit;
			return true;
		}
		return false;
	}

	public function populateBuyNowProductDetails($pid, $type, $cart_obj, $prod_obj, $allow_cache = true)
	{
		if($pid > 0 && $type == 'product')
		{
			$item_type = $type;
			$product_id = $pid;
			$logged_user_id = BasicCUtil::getLoggedUserId();

			//Add item into cart while buy now is clicked
			if($this->buy_now_add_cart)
				$this->addItemIntoCart($pid, 'product', $cart_obj);

			$prod_obj->setProductId($pid);
			$prod_obj->setFilterProductStatus('Ok');
			$prod_obj->setFilterProductExpiry(true);
			$product_details = $prod_obj->getProductDetails(0, $allow_cache);
			/*$product_details = MpProduct::Select('id', 'product_user_id', 'product_price_currency')->whereRaw('id = ? AND product_status =\'Ok\'', array($product_id))->first();*/
			if(count($product_details) > 0 && isset($product_details['product_user_id']) && $product_details['product_user_id'] != "")
			{
				$this->cart_item_details_arr[] = array('item_id' => $product_details['id'],
												 'item_owner_id' => $product_details['product_user_id'],
												 'item_type' => $item_type
												 );
				$this->cart_product_ids_arr[] = $product_details['id'];

				$this->item_owner_id = $product_details['product_user_id'];
				$this->checkout_currency = $product_details['product_price_currency'];

				if($logged_user_id != 0 && ($logged_user_id == $product_details['product_user_id']))
				{
					$this->is_purchasing_own_item = Config::get('webshoppack.allow_to_purchase_own_item')?false:true;;
				}
				return true;
			}
		}
		return false;
	}

	public function setProductDataForCart($ids, $prod_obj, $allow_cache = true)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$prod_obj->setFilterProductIds($ids);
		$prod_obj->setFilterProductExpiry(true);
		$product_details = $prod_obj->getProductsList(0, $allow_cache);
		if(count($product_details) > 0)
		{
			$shipping_country_id_in_cookie = CUtil::getShippingCountry();
			foreach($product_details as $product)
			{
				$qty = $cart_id = $matrix_id = $use_giftwrap = 0;
				$variation_details = array();
				foreach($this->cart_item_details_arr as $cart_key => $cart_item)
				{
					if($cart_item['item_id'] == $product['id'] && $cart_item['item_type'] == 'product') {
						$cart_id = $cart_item['cart_id'];
						$qty = $cart_item['item_qty'];
						$variation_details = (CUtil::chkIsAllowedModule('variations') && isset($cart_item['variation_details'])) ? $cart_item['variation_details'] : array();
						$matrix_id = $cart_item['matrix_id'];
						$use_giftwrap = $cart_item['use_giftwrap'];
					}
				}
				$product['variation_details'] = isset($variation_details) ? $variation_details : array();
				$product_price = 0;
                $price_group = $this->getPriceGroupsDetailsNew($product['id'], $logged_user_id, $qty, $matrix_id, false);
                //$price_group = $this->getPriceGroupsDetailsNew($product['id'], $logged_user_id, $qty, $vlaues->matrix_id);
				if(count($price_group) > 0) {
					$product_price = $price_group['discount'];
					$product['product_price_currency'] = Config::get('generalConfig.site_default_currency');
					$product['deal_details'] = isset($price_group['deal_details']) ? $price_group['deal_details'] : array();
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

					$shipping_billing_address_ids_arr = $this->getUserShippingAddress($logged_user_id);
					//echo "<pre>";print_r($shipping_billing_address_ids_arr);echo "</pre>";
					if(count($shipping_billing_address_ids_arr) > 0)
					{
						$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];
						$billing_address_id = $shipping_billing_address_ids_arr['billing_address_id'];
						if($shipping_address_id=='' || $shipping_address_id <=0 || $billing_address_id=='' || $billing_address_id <=0)
						{
							$this->disable_checkout = true;
							$this->disable_checkout_err_msg = trans('payCheckOut.provide_shipping_address');
							$is_disabled_to_checkout = true;
						}
					}
					else
					{
						$this->disable_checkout = true;
						$this->disable_checkout_err_msg = trans('payCheckOut.provide_shipping_address');
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

	/*public function getProductServicesList($product_id){
		$product_service_details = MpProductServices::whereRaw('mp_product_id = ? and status != ? ', array($product_id,'Deleted'))->get()->toArray();
		return $product_service_details;
	}

	public function getSelectedProductServicesList($order_id, $product_id){
		$product_service_details = MpOrderItemServices::LeftJoin('mp_product_services','mp_product_services.id','=','mp_order_item_services.service_id')
													->whereRaw('mp_order_item_services.order_id = ? and mp_order_item_services.item_id = ?', array($order_id, $product_id))
													->get()->toArray();
		return $product_service_details;
	}*/

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
		if(isset($this->product_details_arr[$id]))
		{

			return isset($this->product_details_arr[$id][$field]) ? $this->product_details_arr[$id][$field] : '';
		}
		return '';
	}

	public function calculateTotalAmount($selected_services = array())
	{
		$this->total_amt = 0;
		foreach($this->cart_item_details_arr as $cartitem)
		{
			if($cartitem['item_type'] == 'product')
			{
				$price = $this->getItemData($cartitem['item_id'], 'product_price', $cartitem['item_type']);
				$this->total_amt = $this->total_amt + ($price * $cartitem['item_qty']);
			}
		}
		return $this->total_amt;
	}

	public function calculateTotalShippingAmount($selected_services = array())
	{
		$total_shipping_amt = 0;
		foreach($this->cart_item_details_arr as $cartitem)
		{
			if($cartitem['item_type'] == 'product')
			{
				$shipping_amt = CUtil::formatAmount($this->getItemData($cartitem['item_id'], 'product_shipping_fees', $cartitem['item_type']));
				//$total_shipping_amt = $total_shipping_amt + ($shipping_amt * $cartitem['item_qty']);
				$total_shipping_amt = $total_shipping_amt + $shipping_amt;
			}
		}
		return $total_shipping_amt;
	}

	public function calculateTotalTaxAmount($selected_services = array())
	{
		$total_tax_amt = 0;
		foreach($this->cart_item_details_arr as $cartitem)
		{
			if($cartitem['item_type'] == 'product')
			{
				$tax_amt = CUtil::formatAmount($this->getItemData($cartitem['item_id'], 'product_tot_tax_amount', $cartitem['item_type']));
				$total_tax_amt = $total_tax_amt + $tax_amt;
			}
		}
		return $total_tax_amt;
	}

	public function getTaxIdsAmounts($product_id)
	{
		$product_tax_details = $this->getItemData($product_id, 'product_tax_details', 'product');
		$tax_ids = array();
		$tax_amounts = array();
		foreach($product_tax_details as $tax_details)
		{
			$tax_ids[] = $tax_details['taxation_id'];
			$tax_amounts[] = CUtil::formatAmount($tax_details['calculated_tax_amount']);
		}
		if(!empty($tax_amounts) && !empty($tax_ids))
		{
			$product_taxids = implode(',', $tax_ids);
			$product_taxamounts = implode(',', $tax_amounts);
		}
		else
		{
			$product_taxids = '';
			$product_taxamounts = '';
		}
		return compact('product_taxids','product_taxamounts');
	}

	public function calculateDiscountAmount()
	{
		if(isset($this->discount_amt))
			{
				return $this->discount_amt;
			}
		$this->discount_amt = 0;
		return $this->calculateDiscountAmount();
	}

	public function chkAndSetShippingAmount($country_id = 0)
	{
		if(is_null($country_id) || $country_id == '')
		{
			return 0;
		}

		$this->shipping_fee_amount = 0;
		$no_shipping_fee = array();
		//echo '<pre>';print_r($this->cart_item_details_arr);echo '<pre>';
		foreach($this->cart_item_details_arr as $key => $cartitem)
		{
			$is_downloadable_product = $this->getItemData($cartitem['item_id'], 'is_downloadable_product', $cartitem['item_type']);
//echo 'is_downloadable_product====>'.$is_downloadable_product;
			//If no country have assigned for shipping fee, then consider this as free shipping
			//$shipping_countries = $this->getItemData($cartitem['item_id'], 'shipping_countries', $cartitem['item_type']);
//echo '<pre>####';print_r($shipping_countries);echo '<pre>';//exit;
			if($cartitem['item_type'] == 'product' && $is_downloadable_product == "No")
			{
				//$shipping_price = Webshopshipments::getShippingDetails(array('foreign_id'=>$cartitem['item_id'], 'country_id' =>$country_id), '', true);
				$shipping_price = $this->getItemData($cartitem['item_id'], 'product_shipping_fees', $cartitem['item_type']);
				$shipping_company_name = $this->getItemData($cartitem['item_id'], 'product_shipping_company_name', $cartitem['item_type']);
				$is_disabled_to_checkout = $this->getItemData($cartitem['item_id'], 'is_disabled_to_checkout', $cartitem['item_type']);
//echo '<pre>####';print_r($shipping_price);echo '<pre>';

				if($shipping_company_name == '' || $is_disabled_to_checkout)
				{
					$item_name = $this->getItemData($cartitem['item_id'], 'product_name', $cartitem['item_type']);
					$no_shipping_fee[] = $item_name;
					//return false;
				}
				else
				{
					$this->shipping_fee_amount = $this->shipping_fee_amount + $shipping_price;
					$this->cart_item_details_arr[$key]['shipping_fee'] = $shipping_price;
				}
			}
		}
		//exit;
		if(empty($no_shipping_fee))
		{
			return $this->shipping_fee_amount;
		}
		else
		{
			$this->shipping_fee_amount = 0;
			return $no_shipping_fee;
		}
	}

	public function getShippingAmount()
	{
		if(isset($this->shipping_fee_amount))
			{
				return $this->shipping_fee_amount;
			}
		return $this->shipping_fee_amount = 0;
		//return $this->calculateDiscountAmount();
	}

	public function getTotalAmount()
	{
		//return (($this->calculateTotalAmount()+$this->calculateTotalShippingAmount()+$this->calculateTotalTaxAmount()) - $this->calculateDiscountAmount());
		return (($this->calculateTotalAmount()+$this->calculateTotalTaxAmount()) - $this->calculateDiscountAmount());
	}
/*
	public function getCouponCodeDetails($input)
	{
		if(isset($input_arr['coupon_code']) && $input_arr['coupon_code'] != '')
		{
			$coupon_details = MpCoupons::Select('id', 'status', 'from_date', 'to_date', 'offer_type', 'offer_amount', 'currency', 'added_by_user_id', 'price_from', 'price_to', 'coupon_code', 'coupon_code_used', 'coupon_total_uses', 'price_restriction')
						->whereRaw('coupon_code = ? AND status = \'Yes\' AND (from_date = \'0000-00-00\' OR from_date <= current_date) AND (to_date = \'0000-00-00\' OR to_date >= current_date) AND added_by_user_id = ?', array($input['coupon_code'], $input['item_owner_id']))->first();
			if(count($coupon_details) > 0)
			{
				if($coupon_details['coupon_code_used'] < $coupon_details['coupon_total_users'] || !$coupon_details['coupon_total_users'])
				{
					return $coupon_details;
				}
			}
		}
		return false;
	}*/

	public function deleteCart($input, $cart_obj)
	{
		$cart_obj->setFilterItemOwnerId($input['item_owner_id']);
		$cart_obj->setFilterCookieId($input['cookie_id']);
		$cart_obj->remove();
	}
	/*
	public function getBillingDetails($id, $user_id)
	{
		$billing_details = array();
		if($id > 0)
			$billing_details = BillingAddress::whereRaw('id = ? AND user_id = ?', array($id, $user_id))->first();
		else
			$billing_details = BillingAddress::whereRaw('user_id = ?', array($user_id))->orderby('id', 'desc')->first();

		return $billing_details;
	}*/

	public function addOrderDetails($data_arr, $cart_obj, $order_obj, $item_owner_id)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		//Add Billing address
		$billing_id = 0;//$this->addBillingAddress($data_arr, $logged_user_id);

		//Insert in Order table
		$order_id = $this->addOrder($data_arr, $logged_user_id, $billing_id, $cart_obj, $order_obj, $item_owner_id);

		$data_arr['productservices'] = (isset($data_arr['productservices']) && !empty($data_arr['productservices']))?$data_arr['productservices']:array();
		//Insert in Order item table
		$site_fees = $this->addOrderItem($order_id, $data_arr['productservices'], $cart_obj, $data_arr);
//		Log::info("============================Site Fees============================");
//		Log::info(print_r($site_fees,true));
//		Log::info("============================Site Fees============================");
		//update order details
		$site_fees['total_amount'] = (isset($site_fees['total_amount']) && $site_fees['total_amount'] > 0) ? CUtil::formatAmount($site_fees['total_amount']) : 0;
		$site_fees['site_commission'] = (isset($site_fees['site_commission']) && $site_fees['site_commission'] > 0) ? CUtil::formatAmount($site_fees['site_commission']) : 0;
		$site_fees['shipping_fee'] = (isset($site_fees['shipping_fee']) && $site_fees['shipping_fee'] > 0) ? CUtil::formatAmount($site_fees['shipping_fee']) : 0;
		if(isset($data_arr['coupon_code']) && $data_arr['coupon_code']!='')
		{
			$site_fees['coupon_code'] = $data_arr['coupon_code'];
			$site_fees['sub_total'] = $site_fees['total_amount'];
			$site_fees['discount_amount'] = $data_arr['discount_amount'];
			if($site_fees['discount_amount'] > 0 && $site_fees['total_amount'] > 0)
				$discount_single_rate = $site_fees['discount_amount'] / $site_fees['total_amount'];
			else
				$discount_single_rate = 0;
			$site_fees['total_amount'] = ($site_fees['total_amount']>=$data_arr['discount_amount'])?($site_fees['total_amount']-$data_arr['discount_amount']):0;
			//$discount_single_rate = $site_fees['discount_amount'] / $site_fees['total_amount'];
			$this->updateOrderItemDiscountRate($order_id, $discount_single_rate);
		}
		$this->updateOrderDetails($order_id, $site_fees);

		// Update Giftwrap price details here.
		$giftwrap_price = (isset($site_fees['giftwrap_price']) && $site_fees['giftwrap_price'] > 0) ? CUtil::formatAmount($site_fees['giftwrap_price']) : 0;
		DB::table('shop_order')->whereRaw('id = ?', array($order_id))->update(array('giftwrap_price' => $giftwrap_price));

		//Add common invoice
		$default_curreny = Config::get('generalConfig.site_default_currency');
		$common_invoice_obj =  Products::initializeCommonInvoice();
		$common_invoice_obj->setUserId($logged_user_id);
		$common_invoice_obj->setReferenceType('Products');
		$common_invoice_obj->setReferenceId($order_id);
		$common_invoice_obj->setCurrency($default_curreny);
		$common_invoice_obj->setAmount(CUtil::formatAmount($site_fees['total_amount']));
		$common_invoice_obj->setStatus('Draft');
		$common_invoice_obj->addCommonInvoice();

		//Delete cart
		if($this->delete_cart)
		{
			//$input['item_owner_id'] = $data_arr['item_owner_id'];
			$cart_obj->destroy($this->cookie_id);
		}

		return $order_id;
	}
	/*
	public function addBillingAddress($data_arr, $logged_user_id)
	{
		$billing_info = array('user_id' => $logged_user_id,
								'name' => isset($data_arr['uname']) ? $data_arr['uname'] : "",
								'street_address' => isset($data_arr['street_address']) ? $data_arr['street_address'] : "",
								'country' => isset($data_arr['billing_country']) ? $data_arr['billing_country'] : "",
								'pincode' => isset($data_arr['pincode']) ? $data_arr['pincode'] : "",
								'contact_no' => isset($data_arr['contact_no']) ? $data_arr['contact_no'] : "",
								'date_added' => date('Y-m-d H:i:s'));
		$billing_address = new BillingAddress();
		$billing_id = $billing_address->insertGetId($billing_info);
		return $billing_id;
	}
	*/
	public function addOrder($data_arr, $logged_user_id, $billing_id, $cart_obj, $order_obj, $item_owner_id)
	{
		$order_obj->setBuyerId($logged_user_id);
		$order_obj->setSellerId($item_owner_id);

		$order_obj->setSellerId($item_owner_id);

		$order_obj->setCurrency($data_arr['checkout_currency']);
		$order_obj->setOrderStatus('draft');
		$order_obj->setDateCreated(date('Y-m-d H:i:s'));

		$data_arr['invoice_id'] = 0;
		$data_arr['buyer_id'] = $logged_user_id;
		$data_arr['currency'] = $data_arr['checkout_currency'];
		//$data_arr['billing_id'] = $billing_id;
		$data_arr['order_status'] = 'draft';
		$data_arr['date_created'] = date('Y-m-d H:i:s');

		$resp = $order_obj->add();
		$respd = json_decode($resp, true);
		$order_id = 0;
		if ($respd['status'] == 'success') {
			if(isset($respd['order_id']))
				$order_id = $respd['order_id'];
		}
		return $order_id;
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

	public function addOrderItem($order_id, $selected_services = array(), $cart_obj, $input_arr)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$total_payment_amt = $tot_site_commission = $tot_shipping_fee = $tot_giftwrap_price = 0;
		foreach($this->cart_item_details_arr as $cart_item)
		{
			if(isset($cart_item['item_id']) AND isset($cart_item['item_type']))
			{
				//if the item type is product
				if($cart_item['item_type'] == 'product')
				{
					$invoice_item_arr['item_type'] = 'product';
					$invoice_item_arr['order_id'] = $order_id;
					$invoice_item_arr['item_id'] = $cart_item['item_id'];
					$invoice_item_arr['buyer_id'] = $logged_user_id;
					$invoice_item_arr['item_owner_id'] = $cart_item['item_owner_id'];
					$invoice_item_arr['matrix_id'] = $cart_item['matrix_id'];
					$invoice_item_arr['use_giftwrap'] = $cart_item['use_giftwrap'];
					$tot_shipping_fee = 0;
					$price = $this->getItemData($cart_item['item_id'], 'product_price', $cart_item['item_type']);
					$dealData = $this->getItemData($cart_item['item_id'], 'deal_details', $cart_item['item_type']);
					$variationData = $this->getItemData($cart_item['item_id'], 'variation_details', $cart_item['item_type']);

					$invoice_item_arr['deal_id'] = (isset($dealData['item_deal_available']) && $dealData['item_deal_available']) ? $dealData['deal_id']: 0;

					//$service_price = $this->calculateProductServicePrice($order_id, $cart_item['item_id'], $cart_item['item_type'], $selected_services);
					$invoice_item_arr['item_amount'] = $price;
					//$invoice_item_arr['services_amount'] = $service_price['total_services_price'];
					//$invoice_item_arr['total_amount'] = $price+$service_price['total_services_price'];
					//$invoice_item_arr['service_ids'] = $service_price['product_service_ids'];
					$total_price = $price * $cart_item['item_qty'];
					$commission = $this->calculateSiteCommission($total_price);

					//added the shipping fee after calculated the commission
					$shipping_company = $this->getItemData($cart_item['item_id'], 'product_shipping_company', $cart_item['item_type']);
					$invoice_item_arr['shipping_fee'] = $shipping_fee = isset($cart_item['shipping_fee'])?$cart_item['shipping_fee']:0;


					$invoice_item_arr['site_commission'] = ($commission < $total_price)?$commission:'0';

					$invoice_item_arr['tax_fee'] = $tax_fee = $this->getItemData($cart_item['item_id'], 'product_tot_tax_amount', $cart_item['item_type']);
					$total_price = $total_price + $tax_fee;
					$tax_ids_amount = $this->getTaxIdsAmounts($cart_item['item_id']);

					//$invoice_item_arr['currency'] = ;
					$invoice_item_arr['date_added'] = date('Y-m-d');

					$tot_site_commission += $invoice_item_arr['site_commission'];
					$tot_shipping_fee+= $invoice_item_arr['shipping_fee'];
					$variation_shipping = isset($variationData['shipping_price']) ? $variationData['shipping_price'] : "";;
					$variation_shipping_impact = isset($variationData['shipping_price_impact']) ? $variationData['shipping_price_impact'] : "";
					if($variation_shipping_impact == 'increase' || $variation_shipping_impact == 'decrease')
					{
						$tot_shipping_fee = $shipping_fee + $variation_shipping;
						if(!isset($tot_shipping_fee)){
							$tot_shipping_fee = 0;
						}
						$total_price = $total_price + $tot_shipping_fee;
					}else{
						$total_price = $total_price + $shipping_fee;
					}
					$invoice_item_arr['seller_amount'] = ($commission < $total_price)?($total_price - $commission):$total_price;
					$total_payment_amt += $total_price;
					//$order_item = new OrderItem();
					$order_obj = Webshoporder::initialize();
					$order_obj->setItemOrderId($order_id);
					$order_obj->setItemId($cart_item['item_id']);
					$order_obj->setBuyerId($logged_user_id);
					$order_obj->setItemOwnerId($cart_item['item_owner_id']);
					$order_obj->setItemAmount(CUtil::formatAmount($price));
					$order_obj->setItemQuantity($cart_item['item_qty']);
					$order_obj->setItemShippingCompany($shipping_company);
					$order_obj->setItemShippingFee(CUtil::formatAmount($tot_shipping_fee));
					$order_obj->setItemTotalTaxAmount(CUtil::formatAmount($tax_fee));

					$order_obj->setItemTaxIds($tax_ids_amount['product_taxids']);
					$order_obj->setItemTaxAmounts($tax_ids_amount['product_taxamounts']);

					$order_obj->setTotalAmount(CUtil::formatAmount($total_price));
					$order_obj->setSiteCommission(CUtil::formatAmount($invoice_item_arr['site_commission']));
					$order_obj->setSellerAmount(CUtil::formatAmount($invoice_item_arr['seller_amount']));
					$order_obj->setDateAdded($invoice_item_arr['date_added']);
					$resp = $order_obj->addOrderItems();
					$respd = json_decode($resp, true);
					$order_item_id = 0;
					if ($respd['status'] == 'success') {
						if(isset($respd['order_item_id']))
							$order_item_id = $respd['order_item_id'];
					}
					if($order_item_id  && isset($invoice_item_arr['deal_id']) && $invoice_item_arr['deal_id'] > 0)
					{
						DB::table('shop_order_item')->whereRaw('id = ?', array($order_item_id))->update(array('deal_id' => $invoice_item_arr['deal_id']));
					}
					$giftwrap_amount = 0;
					if(CUtil::chkIsAllowedModule('variations') && $order_item_id
						&& isset($invoice_item_arr['matrix_id']) && $invoice_item_arr['matrix_id'] > 0)
					{
						$var_det_array['matrix_id'] 		= $invoice_item_arr['matrix_id'];
						$var_det_array['is_use_giftwrap']	= 0;
						if(Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap'))
						{
							$var_det_array['is_use_giftwrap'] 	= (isset($input_arr['use_giftwrap']) && $input_arr['use_giftwrap'] ) ? $input_arr['use_giftwrap'] : 0;
							if($var_det_array['is_use_giftwrap'])
							{
								$giftwrap_type = $this->getItemData($cart_item['item_id'], 'giftwrap_type', $cart_item['item_type']);
								$giftwrap_pricing = $this->getItemData($cart_item['item_id'], 'giftwrap_pricing', $cart_item['item_type']);
								$giftwrap_price_impact = isset($variationData['giftwrap_price_impact']) ? $variationData['giftwrap_price_impact'] : "";
								if($giftwrap_price_impact == 'increase' || $giftwrap_price_impact == 'decrease')
								{
									$giftwrap_amount = ($giftwrap_type == 'single') ? ($giftwrap_pricing + $variationData['giftwrap_price']) * $cart_item['item_qty'] : $giftwrap_pricing;

									if($giftwrap_amount < 0){
										$giftwrap_amount = 0;
									}
								}
								else
								{
									$giftwrap_amount = ($giftwrap_type == 'single') ? $giftwrap_pricing * $cart_item['item_qty'] : $giftwrap_pricing;
								}

								$var_det_array['giftwrap_price'] 		= $giftwrap_amount;
								$var_det_array['giftwrap_price_per_qty'] = $giftwrap_pricing;
								$var_det_array['giftwrap_msg'] 	= '';
								if(isset($input_arr['giftwrap_msg_'.$cart_item['item_owner_id'].'_'.$cart_item['item_id']]) && Lang::get('variations::variations.cart_giftwrap_msg_note') != trim($input_arr['giftwrap_msg_'.$cart_item['item_owner_id'].'_'.$cart_item['item_id']]))
								{
									$var_det_array['giftwrap_msg'] = $input_arr['giftwrap_msg_'.$cart_item['item_owner_id'].'_'.$cart_item['item_id']];
								}
								$var_det_array['seller_amount'] = CUtil::formatAmount($invoice_item_arr['seller_amount'] +$giftwrap_amount);
								$var_det_array['total_amount'] = CUtil::formatAmount($total_price + $giftwrap_amount);
							}
						}
						DB::table('shop_order_item')->whereRaw('id = ?', array($order_item_id))->update($var_det_array);
					}
					$tot_giftwrap_price += $giftwrap_amount;
					$total_payment_amt += $giftwrap_amount;
					$price_group = $this->getPriceGroupsDetailsNew($cart_item['item_id'], $logged_user_id, $cart_item['item_qty'], $cart_item['matrix_id']);
					if(count($price_group) > 0) {
						//ProductPriceGroupsOrders table entry
						$data_arr["order_id"] = $order_id;
						$data_arr["product_id"] = $price_group['product_id'];
						$data_arr["group_id"] = $price_group['group_id'];
						$data_arr["range_start"] = 1;
						$data_arr["range_end"] = -1;
						$data_arr["currency"] = $price_group['currency'];
						$data_arr["price"] = $price_group['price'];
						$data_arr["price_usd"] = $price_group['price_usd'];
						$data_arr["discount_percentage"] = $price_group['discount_percentage'];
						$data_arr["discount"] = $price_group['discount'];
						$data_arr["discount_usd"] = $price_group['discount_usd'];
						$data_arr["added_on"] = DB::raw('NOW()');
						ProductPriceGroupsOrders::insert($data_arr);
					}
					//$order_item_services = $this->calculateProductServicePrice($order_id, $cart_item['item_id'], $cart_item['item_type'], $selected_services, $order_item_id);
				}
			}
		}
		return array('total_amount' => $total_payment_amt, 'site_commission' => $tot_site_commission, 'shipping_fee' => $tot_shipping_fee, 'giftwrap_price' => $tot_giftwrap_price);
	}
	/*
	public function calculateProductServicePrice($order_id, $product_id, $product_type, $selected_services, $order_item_id = null)
	{
		$total_services_price = 0;
		$product_service_ids = '';
		$product_service_ids_arr = array();
		$product_services = $this->getItemData($product_id, 'product_services', $product_type);
		if(empty($product_services))
			return compact('product_service_ids','total_services_price');
		else
		{
			foreach($product_services as $service)
			{
				if(in_array($service['id'], $selected_services))
				{
					if(!is_null($order_item_id) && $order_item_id > 0)
					{
						$order_item_service['order_id'] = $order_id;
						$order_item_service['item_id'] = $product_id;
						$order_item_service['mp_order_item_id'] = $order_item_id;
						$order_item_service['service_id'] = $service['id'];
						$order_item_service['price'] = $service['price'];
						$order_item_service['date_added'] = date('Y-m-d h:i:s');
						$orderitemservices = new MpOrderItemServices();
						$order_item_service_id = $orderitemservices->addNew($order_item_service);
					}
					$total_services_price+=$service['price'];
					$product_service_ids_arr[] = $service['id'];
				}
			}
		}
		$product_service_ids = implode(',',$product_service_ids_arr);
		return compact('product_service_ids','total_services_price');
	}*/

	public function updateOrderDetails($order_id, $data_arr){
		$order_obj = Webshoporder::initialize();
		$order_obj->setOrderId($order_id);
		$order_obj->setTotalAmount($data_arr['total_amount']);
		$order_obj->setSiteCommission($data_arr['site_commission']);
		$order_obj->setShippingFee($data_arr['shipping_fee']);
		if(isset($data_arr['coupon_code']) && $data_arr['coupon_code']!='')
		{
			$order_obj->setCouponCode($data_arr['coupon_code']);
			$order_obj->setSubTotal($data_arr['sub_total']);
			$order_obj->setDiscountAmount($data_arr['discount_amount']);
		}
		$order_obj->add();
		//MpOrder::whereRaw('id = ?', array($order_id))->update($data_arr);
	}
	public function updateOrderItemDiscountRate($order_id, $discount_single_rate)
	{
		$order_obj = Webshoporder::initialize();
		$order_obj->updateOrderItemDiscountRate($order_id, $discount_single_rate);
		//$array = array('discount_amount_rate' => DB::raw('TRUNCATE(total_amount*'.$discount_single_rate.',2)'));

	}
	/*
	public function updateOrderItemDetails($order_id, $data_arr){
		MpOrderItem::where('order_id','=', $order_id )->update($data_arr);
	}
	public function updateOrderReceiversDetails($order_id, $data_arr){
		MpOrderReceivers::where('order_id','=', $order_id )->update($data_arr);
	}*/

	/*public function setSelectedService($selected_services = array())
	{
		$this->selected_services = $selected_services;
	}*/
	/*
	public function calculateSelectedServiceAmount()
	{
		$selected_services = $this->selected_services;
		$this->sel_services_amt = 0;
		foreach($this->cart_item_details_arr as $cartitem)
		{
			if($cartitem['item_type'] == 'product')
			{
				$product_services = $this->getItemData($cartitem['item_id'], 'product_services', $cartitem['item_type']);
				foreach($product_services as $service)
				{
					if(in_array($service['id'], $selected_services))
					{
						$this->sel_services_amt = $this->sel_services_amt + $service['price'];
					}
				}

			}
		}
		//echo $this->sel_services_amt;
		return $this->sel_services_amt;
	}*/

	/**
	 *
	 * @author 		manikandan_133at10
	 * @return 		void
	 * @access 		public
	 */
	public function getUserCartShippingAddress($user_id) {
		$shipping_address_id = 0;
		$billing_address_id = 0;

		$cart_shipping_address_arr = array();
		$cart_shipping_address = UserCartShippingAddress::Select('id', 'user_id', 'shipping_address_id', 'billing_address_id')
									->whereRaw('user_id = ?', array($user_id))
									->get();
		if(count($cart_shipping_address) > 0) {
			foreach($cart_shipping_address as $key => $vlaues) {
				$shipping_address_id = $vlaues->shipping_address_id;
				$billing_address_id = $vlaues->billing_address_id;
			}
		}
		else {
			$shipping_address_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('user_id' => $user_id), 'all', 'desc');
			if(isset($shipping_address_details[0]->shipping_address)) {
				$shipping_address_id = isset($shipping_address_details[0]->address_id)?$shipping_address_details[0]->address_id:0;
				$billing_address_id = isset($shipping_address_details[0]->billing_address_id)?$shipping_address_details[0]->billing_address_id:0;
			}
			else {
				$shipping_address = Webshopaddressing::Addressing()->getAddresses(array('user_id' => $user_id, 'address_type' => 'shipping'), 'first', 'desc');
				if($shipping_address) {
					$shipping_address_id = $shipping_address['id'];
				}
				$billing_address = Webshopaddressing::Addressing()->getAddresses(array('user_id' => $user_id, 'address_type' => 'billing'), 'first', 'desc');
				if($billing_address) {
					$billing_address_id = $billing_address['id'];
				}
			}

			if($shipping_address_id > 0) {
				$cart_shipping = array();
				$cart_shipping['user_id'] = $user_id;
				$cart_shipping['shipping_address_id'] = $shipping_address_id;
				$cart_shipping['billing_address_id'] = $billing_address_id;
				$userCartShippingAddress = new UserCartShippingAddress();
				$userCartShippingAddress->addNew($cart_shipping);
			}
		}
		$shipping_billing_address_ids_arr['shipping_address_id'] = $shipping_address_id;
		$shipping_billing_address_ids_arr['billing_address_id'] = $billing_address_id;
		return $shipping_billing_address_ids_arr;
	}
	public function getUserShippingAddress($user_id = 0)
	{
		if($user_id == 0)
			$user_id = BasicCUtil::getLoggedUserId();
		$shipping_address_id = $this->getCookie(Config::get('generalConfig.site_cookie_prefix').'_shipping_address_'.$user_id);
		$billing_address_id = $this->getCookie(Config::get('generalConfig.site_cookie_prefix').'_billing_address_'.$user_id);
		if($shipping_address_id == '')
		{
			$address_det = Webshopaddressing::Addressing()->getAddresses(array('user_id' => $user_id, 'address_type' => 'shipping', 'is_primary' => 'Yes'), 'first', 'desc');
			if(count($address_det) > 0 && $address_det['id']>0)
				$shipping_address_id = $address_det['id'];
		}
		$billing_address_id = ($billing_address_id !='')?$billing_address_id:$shipping_address_id;
		return array('shipping_address_id' => $shipping_address_id, 'billing_address_id' => $billing_address_id);
	}
	public function emptyUserShippingCookie($user_id=0)
	{
		if($user_id=='' || $user_id<=0)
			$user_id = BasicCUtil::getLoggedUserId();

		$ship_cookie = Cookie::forget(Config::get('generalConfig.site_cookie_prefix').'_shipping_address_'.$user_id);
		$bill_cookie = Cookie::forget(Config::get('generalConfig.site_cookie_prefix').'_billing_address_'.$user_id);

		return compact('ship_cookie','bill_cookie');

	}
}