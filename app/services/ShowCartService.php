<?php
class ShowCartService extends ProductService
{
	# All item details listed in current page
	public $cart_item_details = array();
	public $cart_cookie = '';
	public $product_qty = 1;
	public $product_matrix_id = 0;

	public function chkValidCartProductId($product_id)
	{
		$is_ok = false;
		$prod_obj = Products::initialize($product_id);
		$product = $prod_obj->getProductDetails(0, false);
		if(count($product) > 0)
		{
			if($product['product_status'] == 'Ok' && $product['date_activated'] != '0')
			{
				$this->product_details[$product['id']] = $product;
				$is_ok = true;
			}
		}
		return $is_ok;
	}

	public function addItemIntoCart($item_id, $item_type = 'product', $cart_obj)
	{
		# Cart item added user id.
		$cart_user_id = BasicCUtil::getLoggedUserId();

		# Item owner id.
		$item_owner_id = 0;

		if($item_type == 'product') //Condition to set product owner id if item type is product
		{
			if(!isset($this->product_details[$item_id]))
			{
				return '';
			}
			$item_owner_id = $this->product_details[$item_id]['product_user_id'];
		}

		# Cart item cookie id.
		$cart_cookie_id = $this->getCartCookieId();
		if(!$cart_cookie_id)
		{
			$cart_cookie_id = $this->generateCartCookieId();
		}

		# Checked item already added in cart or not.
		$item_qty = ($this->product_qty > 0) ? $this->product_qty : 1;
		$item_matrix_id = (CUtil::chkIsAllowedModule('variations') && $this->product_matrix_id > 0) ? $this->product_matrix_id : 0;
		$cart_obj->setFilterCookieId($cart_cookie_id);
		$cart_obj->setFilterItemId($item_id);
		$cart_obj->setFilterItemOwnerId($item_owner_id);
		$cart_details = $cart_obj->contents();
		if(count($cart_details) > 0) {
			foreach($cart_details as $cart) {
				$item_qty = $cart['qty'] + ($this->product_qty > 0) ? $this->product_qty : 1;
			}
		}

		$cart_obj->setCartUserId($cart_user_id);
		$cart_obj->setCartItemId($item_id);
		$cart_obj->setCartItemOwnerId($item_owner_id);
		$cart_obj->setCartItemQuantity($item_qty);
		$cart_obj->setCartItemMatrixId($item_matrix_id);
		$cart_obj->setCartItemDateModified(date('Y-m-d H:i:s'));
		$cart_obj->setCartCookieId($cart_cookie_id);
		$response = $cart_obj->add();

		$json_data = json_decode($response, true);
		$cart_id = 0;
		if($json_data['status'] == 'success') {
			$cart_id = $json_data['cart_id'];
		}
		return array('cart_cookie_id' => $cart_cookie_id, 'cart_id' => $cart_id);
	}

	public function getCartCookieId()
	{
		$cookie_id = Config::get('addtocart.site_cookie_prefix')."_mycart";
		$cart_cookie_id = BasicCUtil::getCookie($cookie_id);
		return $cart_cookie_id;
	}

	public function generateCartCookieId()
	{
		$cart_cookie_id = uniqid();
		return $cart_cookie_id;
	}

	public function populateMyCartItems($currency_arr, $cart_obj)
	{
		$this->cart_cookie = $this->getCartCookieId();
		$cart_arr = array();
		$shop_obj = Products::initializeShops();
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();

		if(CUtil::chkIsAllowedModule('variations'))
			 $this->variation_service = new VariationsService();
		//echo "<pre>";print_r($cart_details);echo "</pre>";die;
		if(count($cart_details) > 0)
		{
			$item_owner_id = array();
			foreach($cart_details as $cart)
			{
				if(CUtil::isMember()){
					$logged_user_id = BasicCUtil::getLoggedUserId();
					if($logged_user_id == $cart['item_owner_id']){
						$this->removeItemfromCart($cart['item_id'], 'Product');
						continue;
					}
				}
				if(in_array($cart['item_owner_id'], $item_owner_id)) {
					continue;
				}
				$item_owner_id[] = $cart['item_owner_id'];

				foreach($currency_arr AS $currency)
				{
					# Defined empty item details array in each owner will be updated when set item details
					$this->cart_item_details[$cart['item_owner_id']]['product'][$currency] = array();

					# Assigned price details as 0 in each owner will be updated when set item details
					$this->cart_item_details[$cart['item_owner_id']]['download'][$currency] = 0;
				}
				$this->cart_item_details[$cart['item_owner_id']]['product']['free_item'] = array();
				$this->cart_item_details[$cart['item_owner_id']]['not_available_product']['item'] = array();
				$this->cart_item_details[$cart['item_owner_id']]['not_available_product']['free_item'] = array();

				//if(Cutil::isUserAllowedToAddProduct())
					$shop_details = $shop_obj->getShopDetails($cart['item_owner_id']);

				$cart['shop_name'] = isset($shop_details['shop_name'])?$shop_details['shop_name']:'From user';
				$cart['url_slug'] = isset($shop_details['url_slug'])?$shop_details['url_slug']:'';

				$cart_arr[] = $cart;
			}
		}
		//echo "<pre>";print_r($cart_arr);echo "</pre>";die;
		return $cart_arr;
	}

	public function setMyCartProductDetails($currency_arr, $cart_obj)
	{
		$item_arr = array();
		if(CUtil::chkIsAllowedModule('variations'))
			$this->variation_service = new VariationsService();

		$prod_obj = Products::initialize();
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if(count($cart_details) > 0) {
			foreach($cart_details as $key => $vlaues) {
				$prod_obj->setProductId($vlaues->item_id);
				$item = $prod_obj->getProductDetails();
				if (count($item) > 0) {
					$item['cart_id'] = $vlaues->id;
					$item['qty'] = $vlaues->qty;

					//echo "<pre>"; print_r($item);echo "</pre>";exit;
					/*if($item['product_discount_fromdate']!='0000-00-00' && $item['product_discount_todate']!='0000-00-00')
					{
						$discount_from_time = strtotime($item['product_discount_fromdate']);
						$discount_end_time = strtotime($item['product_discount_todate']);
						$curr_time = strtotime(date('Y-m-d'));
						 if($discount_end_time >= $curr_time && $discount_from_time <= $curr_time)
						{
							$item['product_price'] = $item['product_discount_price'];
						}
					}
					else if($item['product_discount_fromdate'] != '0000-00-00')
					{
						$discount_from_time = strtotime($item['product_discount_fromdate']);
						$curr_time = strtotime(date('Y-m-d'));
						if($discount_from_time <= $curr_time)
						{
							$item['product_price'] = $item['product_discount_price'];
						}
					}
					else if($item['product_discount_todate'] != '0000-00-00')
					{
						$discount_end_time = strtotime($item['product_discount_todate']);
						$curr_time = strtotime(date('Y-m-d'));
						if($discount_end_time >= $curr_time)
						{
							$item['product_price'] = $item['product_discount_price'];
						}
					}
					else
					{
						if($item['product_discount_price'] > 0)
							$item['product_price'] = $item['product_discount_price'];
					}*/
					//if($item['have_discount'] && $item['product_discount_price'] > 0) {
						//$item['product_price'] = $item['product_discount_price'];
					//}

					$price_group = $this->getPriceGroupsDetailsNew($vlaues->item_id, $logged_user_id, $item['qty'], $vlaues->matrix_id);
					if(count($price_group) > 0) {
						$item['product_price'] = $price_group['discount'];
						$item['product_price_currency'] = $price_group['currency'];
						$item['deal_details'] = isset($price_group['deal_details']) ? $price_group['deal_details'] : array();
					}

					# For variation related changes starts
					if($item['is_downloadable_product'] == 'No' )
					{
						$matrix_id = (CUtil::chkIsAllowedModule('variations')) ? $vlaues->matrix_id : 0;
						$item['matrix_id'] = $matrix_id;
						$variations_det_arr = array();
						if($matrix_id != 0 )
						{
							//$variation_data_arr = $this->variation_service->populateVariationAttributesByMatrixId($vlaues->item_id, $vlaues->matrix_id, $vlaues->item_owner_id);
							$variation_service = new VariationsService();
							//$variations_det_arr = $variation_service->populateMatrixDetails($vlaues->matrix_id, $vlaues->item_id);
							$variations_det_arr = $variation_service->populateVariationAttributesByMatrixId($vlaues->item_id, $vlaues->matrix_id, $vlaues->item_owner_id);
						}
						$item['variation_details'] = $variations_det_arr;
					}
					$item['use_giftwrap'] = 0;
					if(CUtil::chkIsAllowedModule('variations') && Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap'))
					{
						$item['use_giftwrap'] = $vlaues->use_giftwrap;
					}

					# Assigned empty values & assigned later if item can't be able to purchase
					$item['not_available_reason'] = $item['not_available_reason_msg'] = '';

					if($item['is_free_product'] == 'Yes')
						$item['product_price'] = $item['product_discount_price'] = 0;

					# Check product status
					if($item['product_status'] != 'Ok') {
						$item['not_available_reason'] = 'product_status';
						$item['not_available_reason_msg'] = trans('showCart.product_not_available_msg');
					}

					#check the expiry date
					if($item['date_expires'] == '0000-00-00 00:00:00' || ($item['date_expires']!='9999-12-31 00:00:00' && strtotime($item['date_expires']) < strtotime(date('Y-m-d'))) )
					{
						$item['not_available_reason'] = 'product_expired';
						$item['not_available_reason_msg'] = trans('showCart.product_not_available_msg');
					}
					#	Check the variation stock
					if(CUtil::chkIsAllowedModule('variations') && isset($this->variation_service)&& $vlaues['matrix_id'] >0)
					{
						if(!$this->variation_service->chkIsAllowedVariationStock($vlaues['item_id'], $vlaues['matrix_id'], $vlaues['qty']))
						{
							$item['not_available_reason'] = 'product_stock';
							$item['not_available_reason_msg'] = trans('showCart.stock_not_avalible_for_the_product');
						}
					}

					# Checked not available values of product
					if($item['not_available_reason'] && $item['not_available_reason_msg']) {
						if($item['product_price'] > 0) {
							$this->cart_item_details[$vlaues['item_owner_id']]['not_available_product']['item'][] = $item;
						}
						else {
							$this->cart_item_details[$vlaues['item_owner_id']]['not_available_product']['free_item'][] = $item;
						}
					}
					# Checked downloadable option
					else if($item['is_downloadable_product'] == 'Yes' || $item['is_downloadable_product'] == 'No') {
						if($item['product_price'] > 0 AND in_array($item['product_price_currency'], $currency_arr)) {
							$this->cart_item_details[$vlaues['item_owner_id']]['download'][$item['product_price_currency']] += ($item['product_price'] * $vlaues->qty);
							$this->cart_item_details[$vlaues['item_owner_id']]['product'][$item['product_price_currency']][] = $item;
						}
						else if($item['product_price'] == 0) {
							$this->cart_item_details[$vlaues['item_owner_id']]['product']['free_item'][] = $item;
						}
					}
					# Set item not available message
					else
					{
						$item['not_available_reason'] = 'shippable_item';
						$item['not_available_reason_msg'] = trans('showCart.product_not_available_msg');
						if($item['product_price'] > 0) {
							$this->cart_item_details[$vlaues['item_owner_id']]['not_available_product']['item'][] = $item;
						}
						else {
							$this->cart_item_details[$vlaues['item_owner_id']]['not_available_product']['free_item'][] = $item;
						}
					}
				}
			}
			/*echo '<pre>';
			print_r($this->cart_item_details[6]['product']);die;*/
		}
		//echo "<pre>";print_r($this->cart_item_details);echo "</pre>";die;
		return $this->cart_item_details;
	}

	public function removeItemFromCart($item_id, $item_type = 'product')
	{
		$cart_obj = Addtocart::initialize();
		$cart_cookie_id = $this->getCartCookieId();

		$added_cart_id = $cart_obj->isCartItemAlreadyAdded($cart_cookie_id, $item_id);
		if($added_cart_id) {
			$cart_obj->remove($added_cart_id);
			$cache_key = 'cart_count_cache_key_'.$cart_cookie_id;
			$total_cart_items = UserCart::whereRaw('cookie_id = ?', array($cart_cookie_id))->count();
			HomeCUtil::cachePut($cache_key, $total_cart_items, Config::get('generalConfig.cache_expiry_minutes'));
			return true;
		}
		return false;
	}

	public function emptyCart($seller_id=0)
	{
		$cart_cookie_id = $this->getCartCookieId();
		$cart_obj = Addtocart::initialize();
		$cart_obj->destroy($cart_cookie_id,$seller_id);
		return true;
	}

	public function getTaxDetails($product_id, $product_price, $qty) {
		$product_taxes = array();
		$product_tax_details = array();
		$product_tot_tax_amount = 0;
		$product_taxes = Webshoptaxation::ProductTaxations()->getProductTaxations(array('product_id' => $product_id));
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
					$calc_tax_amount = ($product_price * $qty) * ($tax_fee/100);
					$tax_details['calculated_tax_amount'] = $calc_tax_amount;
				}
				else
				{
					$tax_label .= '<small>'.Config::get('generalConfig.site_default_currency').'</small> <strong>'.$tax['tax_fee'].'</strong> / <small>'.trans('showCart.quantity').'</small>';
					$tax_details['calculated_tax_amount'] = $tax_fee * $qty;
				}
				$tax_details['tax_label'] = $tax_label;

				$product_tax_details[$inc] = $tax_details;
				$product_tot_tax_amount += $tax_details['calculated_tax_amount'];
				$inc++;
			}
		}
		$product_taxes['product_tax_details'] = $product_tax_details;
		$product_taxes['product_tot_tax_amount'] = $product_tot_tax_amount;
		return $product_taxes;
	}

	public function getCartItemCount()
	{
		$total_cart_items = 0;
		$cart_cookie_id = $this->getCartCookieId();
		$cart_obj = Addtocart::initialize();
		$total_cart_items = $cart_obj->getCartItemCount($cart_cookie_id);
		return $total_cart_items;
	}

	public function setProductQty($qty) {
		if($qty > 0)
			$this->product_qty = $qty;
	}

	public function setMatrixID($matrix_id) {
		if($matrix_id > 0)
			$this->product_matrix_id = $matrix_id;
	}

}