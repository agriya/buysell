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
class CartController extends BaseController
{
	public $cart_price_details = array();
	function __construct()
	{
        $this->ShowCartService = new ShowCartService();
        if(CUtil::chkIsAllowedModule('variations'))
			 $this->variation_service = new VariationsService();
    }

    public function getIndex()
    {
    	/*if (CUtil::isAdmin()) {
    		return Redirect::to('');
		}*/
    	$cart_obj = Addtocart::initialize();
    	$prod_obj = Products::initialize();
    	$currency_arr = Products::fetchCurrencyDetails();
    	$cart_details = $this->ShowCartService->populateMyCartItems($currency_arr, $cart_obj);
    	$cart_item_details = $this->ShowCartService->setMyCartProductDetails($currency_arr, $cart_obj);
    	$cart_total_count = $this->ShowCartService->getCartItemCount();
    	$ProductService = new ProductService();
    	$numbers_arr = CUtil::getNumbersListForSelectBox(1, Config::get('addtocart.product_quantity_limit'));
    	$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc', false);
    	$d_arr['countries_list'] = $countries_arr;//array_except($countries_arr, array('38'));//Remove china
    	$d_arr['shipping_country_id'] = CUtil::getShippingCountry();
    	$breadcrumb_arr = array(trans("showCart.cart_page_title"));

		$CheckOutService = new CheckOutService();
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$shipping_billing_address_ids_arr = $CheckOutService->getUserShippingAddress($logged_user_id);
    	$shipping_address_id = 0;
    	if(count($shipping_billing_address_ids_arr) > 0)
		{
			$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];

		}
		$address_obj = Webshopaddressing::Addressing();
		$shipping_address = $address_obj->getAddresses(array('id' => $shipping_address_id));
		$last_shipping_country_id = BasicCUtil::getCookie(Config::get('generalConfig.site_cookie_prefix')."_shipping_country");
		if($shipping_address && count($shipping_address) > 0)
		{
			$last_shipping_country_id = isset($shipping_address[0]->country_id) ? $shipping_address[0]->country_id : 0;
		}
		$d_arr['change_variation_url'] = '';
		$d_arr['show_giftwrap_column'] = 0;
		if(CUtil::chkIsAllowedModule('variations') && Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap'))
			$d_arr['show_giftwrap_column'] = 1;

    	//Log::info(print_r($cart_details,1));
    	//echo "<pre>";print_r($cart_item_details);echo "</pre>";
    	return View::make('view_cart', compact('cart_details', 'breadcrumb_arr', 'cart_item_details', 'currency_arr', 'ProductService', 'cart_total_count', 'prod_obj', 'numbers_arr', 'd_arr', 'shipping_address_id', 'last_shipping_country_id'));
	}

	public function postAdd()
	{
		# Validate product id
		$product_id = Input::get('product_id');
		if($this->ShowCartService->chkValidCartProductId($product_id))
		{
			$cart_obj = Addtocart::initialize();
    		$prod_obj = Products::initialize();
			$this->ShowCartService->setProductQty(Input::get('qty'));
			$this->ShowCartService->setMatrixID(Input::get('matrix_id', 0));
			$cart_details = $this->ShowCartService->addItemIntoCart($product_id, 'product', $cart_obj);
			$cart_cookie = isset($cart_details['cart_cookie_id']) ? $cart_details['cart_cookie_id'] : '';
			$cart_id = isset($cart_details['cart_id']) ? $cart_details['cart_id'] : 0;
			if($cart_cookie != "" && $cart_id > 0)
			{
				$cookie = Cookie::forever(Config::get('addtocart.site_cookie_prefix')."_mycart", $cart_cookie);
				$shipping_country_id = Input::has('shipping_country_id') ? Input::get('shipping_country_id') : '';
				//$shipping_company_id = Input::has('shipping_company') ? Input::get('shipping_company') : '';
				$shipping_company_id = Input::has('shipping_company_id') ? Input::get('shipping_company_id') : '';
				if($shipping_country_id != '' && $shipping_company_id > 0) {
					$shipping_country_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_country', $shipping_country_id);
					$shipping_company_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_company_'.$cart_id, $shipping_company_id);
			 		return Redirect::to('cart')->withCookie($cookie)->withCookie($shipping_country_cookie)->withCookie($shipping_company_cookie)->with('success_message', trans('showCart.cart_added_success'));
				}
				else {
					# Redirecting since we need to get the values from cookie
			 		return Redirect::to('cart')->withCookie($cookie)->with('success_message', trans('showCart.cart_added_success'));
				}
			}
			else
			{
				return Redirect::to('cart')->with('error_message', trans('showCart.cart_added_invalid_product'));
			}
		}
		else
		{
			return Redirect::to('cart')->with('error_message', trans('showCart.cart_added_invalid_product'));
		}
	}

	public function postUpdateProductQuantity()
	{
		$error_exists = false;
		$error_msg = '';
		$item_id = Input::has('item_id') ? Input::get('item_id') : 0;
		$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : 0;

		$quantity = Input::has('quantity') ? Input::get('quantity') : 0;
		$total = Input::has('total') ? Input::get('total') : 0;
		$tax_total = Input::has('tax_total') ? Input::get('tax_total') : 0;
		$ship_price = Input::has('ship_price') ? Input::get('ship_price') : 0;
		$sub_total = Input::has('subtotal') ? Input::get('subtotal') : 0;
		$old_sub_total = $sub_total;
		$checkout_total = Input::has('checkout_total') ? Input::get('checkout_total') : 0;
		$shipping_company_id = Input::has('shipping_company_id') ? Input::get('shipping_company_id') : 0;

		$this->cart_cookie = $this->ShowCartService->getCartCookieId();

		$prod_obj = Products::initialize();
		$cart_obj = Addtocart::initialize();
		$cart_obj->setFilterItemOwnerId($item_owner_id);
		$cart_obj->setFilterItemId($item_id);
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		//echo "<pre>";print_r($cart_details);echo "</pre>";die;
		if(count($cart_details) > 0) {
			foreach($cart_details as $cart) {
				$prod_obj->setProductId($cart['item_id']);
				$prod_obj->setFilterProductStatus('Ok');
				$prod_obj->setFilterProductExpiry(true);
				$product_details = $prod_obj->getProductDetails();
				if(count($product_details) > 0) {
					//update cart item quantity
					$cart_obj->setCartUserId($item_owner_id);
					$cart_obj->setCartItemId($item_id);
					$cart_obj->setCartCookieId($this->cart_cookie);
					$cart_obj->setCartItemQuantity($quantity);
					$response = $cart_obj->add();

					//Group price setting
	                $product_price = 0;
	                $logged_user_id = BasicCUtil::getLoggedUserId();
	                $price_group = $this->ShowCartService->getPriceGroupsDetailsNew($item_id, $logged_user_id, $quantity);
					if(count($price_group) > 0) {
						$product_price = $price_group['discount'];
					}

					//Get product tax price for 1 qty
					$product_tax_price = 0;
					$product_taxes = $this->ShowCartService->getTaxDetails($item_id, $product_price, 1);
					if($product_taxes['product_tot_tax_amount'] > 0) {
                    	$product_tax_price = $product_taxes['product_tot_tax_amount'];
                    }

					$shipping_fee = 0;
					$ship_template_id = $product_details['shipping_template'];
					$product_id = $item_id;
					if($ship_template_id > 0) {
						$shipping_template_service = new ShippingTemplateService();
						$shipping_company_id_cookie = Config::get('generalConfig.site_cookie_prefix').'_shipping_company_'.$cart['item_id'];
						$shipping_company_id_in_cookie = BasicCUtil::getCookie($shipping_company_id_cookie);
						if($shipping_company_id_in_cookie != '')
							$shipping_company_id = $shipping_company_id_in_cookie;
						$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($ship_template_id, $shipping_company_id, $product_id, $quantity);
						foreach($shipping_companies_list as $shipping) {
							if($shipping['company_id'] == $shipping_company_id) {
								$shipping_fee = $shipping['shipping_fee'];
								//$shipping_company_name = $shipping['company_name'];
							}
						}
					}

					$total = $product_price * $quantity;
					$tax_total = $product_tax_price * $quantity;
					$ship_total = $shipping_fee;//$ship_price * $quantity;
					$sub_total = $total + $tax_total + $ship_total;
					$checkout_total_diff = $sub_total - $old_sub_total;
					$checkout_total = $checkout_total + $checkout_total_diff;

					$ret_arr =array();

					$ret_arr['error_exists'] = $error_exists;
					/*$ret_arr['total'] = CUtil::getBaseAmountToDisplay($total, 'USD');
					$ret_arr['tax_total'] = CUtil::getBaseAmountToDisplay($tax_total, 'USD');
					$ret_arr['sub_total'] = CUtil::getBaseAmountToDisplay($sub_total, 'USD');
					$ret_arr['checkout_total'] = CUtil::getBaseAmountToDisplay($checkout_total, 'USD');
					$ret_arr['ship_total'] = CUtil::getBaseAmountToDisplay($ship_total, 'USD');

					$ret_arr['total_curr'] = CUtil::getCurrencyBasedAmount($total, $total, 'USD');
					$ret_arr['tax_total_curr'] = CUtil::getCurrencyBasedAmount($tax_total, $tax_total, 'USD');
					$ret_arr['sub_total_curr'] = CUtil::getCurrencyBasedAmount($sub_total, $sub_total, 'USD');
					$ret_arr['checkout_total_curr'] = CUtil::getCurrencyBasedAmount($checkout_total, $checkout_total, 'USD');
					$ret_arr['ship_total_curr'] = CUtil::getCurrencyBasedAmount($ship_total, $ship_total, 'USD');*/

					$ret_arr['total'] = CUtil::convertAmountToCurrency($total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['tax_total'] = CUtil::convertAmountToCurrency($tax_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['sub_total'] = CUtil::convertAmountToCurrency($sub_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['checkout_total'] = CUtil::convertAmountToCurrency($checkout_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['ship_total'] = CUtil::convertAmountToCurrency($ship_total, Config::get('generalConfig.site_default_currency'), '', true);

					$ret_arr['total_curr'] = CUtil::convertAmountToCurrency($total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true);
					$ret_arr['tax_total_curr'] = CUtil::convertAmountToCurrency($tax_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true);
					$ret_arr['sub_total_curr'] = CUtil::convertAmountToCurrency($sub_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true);
					$ret_arr['checkout_total_curr'] = CUtil::convertAmountToCurrency($checkout_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true);
					$ret_arr['ship_total_curr'] = CUtil::convertAmountToCurrency($ship_total, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true);

					echo json_encode($ret_arr);exit;

//					$return_str = $error_exists;
//					$return_str .= "|~~|".CUtil::getBaseAmountToDisplay($total, 'USD');
//					$return_str .= "|~~|".CUtil::getBaseAmountToDisplay($tax_total, 'USD');
//					$return_str .= "|~~|".CUtil::getBaseAmountToDisplay($sub_total, 'USD');
//					$return_str .= "|~~|".CUtil::getBaseAmountToDisplay($checkout_total, 'USD');
//					$return_str .= "|~~|".CUtil::getBaseAmountToDisplay($ship_total, 'USD');
//
//					echo $return_str;exit;
				}
			}
		}
		$error_exists = true;
		echo $error_exists;exit;
	}

	public function postUpdateProductVariation()
	{
		$error_exists = false;
		$error_msg = '';
		$item_id = Input::has('item_id') ? Input::get('item_id') : 0;
		$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : 0;

		$quantity = Input::has('quantity') ? Input::get('quantity') : 0;
		$matrix_id = Input::has('matrix_id') ? Input::get('matrix_id') : 0;
		$total = Input::has('total') ? Input::get('total') : 0;
		$tax_total = Input::has('tax_total') ? Input::get('tax_total') : 0;
		$ship_price = Input::has('ship_price') ? Input::get('ship_price') : 0;
		$sub_total = Input::has('subtotal') ? Input::get('subtotal') : 0;
		$old_sub_total = $sub_total;
		$checkout_total = Input::has('checkout_total') ? Input::get('checkout_total') : 0;
		$shipping_company_id = Input::has('shipping_company_id') ? Input::get('shipping_company_id') : 0;

		$this->cart_cookie = $this->ShowCartService->getCartCookieId();

		$prod_obj = Products::initialize();
		$cart_obj = Addtocart::initialize();
		$cart_obj->setFilterItemOwnerId($item_owner_id);
		$cart_obj->setFilterItemId($item_id);
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		if(count($cart_details) > 0) {
			foreach($cart_details as $cart) {
				$prod_obj->setProductId($cart['item_id']);
				$prod_obj->setFilterProductStatus('Ok');
				$prod_obj->setFilterProductExpiry(true);
				$product_details = $prod_obj->getProductDetails();
				if(count($product_details) > 0) {
					//update cart item quantity
					$cart_obj->setCartUserId($item_owner_id);
					$cart_obj->setCartItemId($item_id);
					$cart_obj->setCartCookieId($this->cart_cookie);
					$cart_obj->setCartItemQuantity($quantity);
					$cart_obj->setCartItemMatrixId($matrix_id);
					$response = $cart_obj->add();

					//Group price setting
	                $product_price = 0;
	                $logged_user_id = BasicCUtil::getLoggedUserId();
	                $price_group = $this->ShowCartService->getPriceGroupsDetailsNew($item_id, $logged_user_id, $quantity);
					if(count($price_group) > 0) {
						$product_price = $price_group['discount'];
					}

					//Get product tax price for 1 qty
					$product_tax_price = 0;
					$product_taxes = $this->ShowCartService->getTaxDetails($item_id, $product_price, 1);
					if($product_taxes['product_tot_tax_amount'] > 0) {
                    	$product_tax_price = $product_taxes['product_tot_tax_amount'];
                    }

					$shipping_fee = 0;
					$ship_template_id = $product_details['shipping_template'];
					$product_id = $item_id;
					if($ship_template_id > 0) {
						$shipping_template_service = new ShippingTemplateService();
						$shipping_company_id_cookie = Config::get('generalConfig.site_cookie_prefix').'_shipping_company_'.$cart['item_id'];
						$shipping_company_id_in_cookie = BasicCUtil::getCookie($shipping_company_id_cookie);
						if($shipping_company_id_in_cookie != '')
							$shipping_company_id = $shipping_company_id_in_cookie;
						$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($ship_template_id, $shipping_company_id, $product_id, $quantity);
						foreach($shipping_companies_list as $shipping) {
							if($shipping['company_id'] == $shipping_company_id) {
								$shipping_fee = $shipping['shipping_fee'];
								//$shipping_company_name = $shipping['company_name'];
							}
						}
					}

					$total = $product_price * $quantity;
					$tax_total = $product_tax_price * $quantity;
					$ship_total = $shipping_fee;//$ship_price * $quantity;
					$sub_total = $total + $tax_total + $ship_total;
					$checkout_total_diff = $sub_total - $old_sub_total;
					$checkout_total = $checkout_total + $checkout_total_diff;

					$ret_arr =array();
					$ret_arr['error_exists'] = $error_exists;
					$ret_arr['total'] = CUtil::convertAmountToCurrency($total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['tax_total'] = CUtil::convertAmountToCurrency($tax_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['sub_total'] = CUtil::convertAmountToCurrency($sub_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['checkout_total'] = CUtil::convertAmountToCurrency($checkout_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['ship_total'] = CUtil::convertAmountToCurrency($ship_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['total_curr'] = CUtil::convertAmountToCurrency($total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['tax_total_curr'] = CUtil::convertAmountToCurrency($tax_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['sub_total_curr'] = CUtil::convertAmountToCurrency($sub_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['checkout_total_curr'] = CUtil::convertAmountToCurrency($checkout_total, Config::get('generalConfig.site_default_currency'), '', true);
					$ret_arr['ship_total_curr'] = CUtil::convertAmountToCurrency($ship_total, Config::get('generalConfig.site_default_currency'), '', true);
					echo json_encode($ret_arr);exit;
				}
			}
		}
		$error_exists = true;
		echo $error_exists;exit;
	}

	public function postIndex()
	{
		$error_msg = '';
		$item_owner_id = Input::has('update_qty_of_owner') ? Input::get('update_qty_of_owner') : 0;
		$qty_item_arr = Input::has('product_qty') ? Input::get('product_qty') : array();
		$this->cart_cookie = $this->ShowCartService->getCartCookieId();
		$cart_arr = array();

		$cart_obj = Addtocart::initialize();
		$shop_obj = Products::initializeShops();
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		//echo "<pre>";print_r($cart_details);echo "</pre>";die;
		if(count($cart_details) > 0) {
			foreach($cart_details as $cart) {
				if($cart['item_owner_id'] == $item_owner_id) {
					$cart_obj->setCartUserId($item_owner_id);
					$cart_obj->setCartItemId($cart['item_id']);
					$cart_obj->setCartCookieId($this->cart_cookie);
					$cart_obj->setCartItemQuantity($qty_item_arr[$cart['item_id']]);
					$response = $cart_obj->add();

					$json_data = json_decode($response, true);
					if($json_data['status'] == 'error')
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
						return Redirect::to('cart')->with('error_message', $error_msg)->withInput();
					}
				}
			}
		}
		return Redirect::to('cart')->with('success_message', trans('showCart.qty_updated_success'));
	}

	public function getDelete()
	{
		$item_id = Input::get('item_id');
		$item_type = Input::get('item_type');
		if($item_id != '' && is_numeric($item_id))
		{
			$deleted_item = $this->ShowCartService->removeItemfromCart($item_id, $item_type);
			if($deleted_item)
			{
				return Redirect::to('cart')->with('success_message', trans('showCart.cart_remove_success'));
			}
		}
		return Redirect::to('cart')->with('error_message', trans('showCart.invalid_product'));
	}

	public function getDeleteOrder()
	{
		$item_id = Input::get('item_id');
		$item_type = Input::get('item_type');
		$item_owner_id = (Input::has('item_owner_id') && Input::get('item_owner_id')!='')?Input::get('item_owner_id'):'0';
		if($item_id != '' && is_numeric($item_id))
		{
			$deleted_item = $this->ShowCartService->removeItemfromCart($item_id, $item_type);
			if($deleted_item)
			{
				return Redirect::to('checkout/'.$item_owner_id)->with('success_message', trans('checkOut.order_remove_success'));
			}
		}
		return Redirect::to('checkout/'.$item_owner_id)->with('error_message', trans('showCart.invalid_product'));
	}

	public function getEmpty()
	{
		$this->ShowCartService->emptyCart();
		//return Redirect::to('cart')->with('success_message', trans('showCart.cart_empty_success'));
		return Redirect::to('cart');
	}

	public function getUpdateShippingCountryAndCost()
	{
		$ship_template_id = Input::has('ship_template_id') ? Input::get('ship_template_id') : '';
		$shipping_company_id = Input::has('shipping_company_id') ? Input::get('shipping_company_id') : '';
		$shipping_country_id = Input::has('shipping_country_id') ? Input::get('shipping_country_id') : '';
		$quantity = Input::has('quantity') ? Input::get('quantity') : '1';
		$product_id = Input::has('product_id') ? Input::get('product_id') : '';
		$matrix_id = Input::has('matrix_id') ? Input::get('matrix_id') : '';
		if($ship_template_id != '')
		{
			$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc', false);
			$countries_arr = array_except($countries_arr, array('38'));//Remove china
			$countries_list = $countries_arr;
			$d_arr['ship_template_id'] = $ship_template_id;
			$d_arr['countries_list'] = $countries_list;
			$d_arr['shipping_companies_list'] = array();
			$d_arr['shipping_country_id'] = $shipping_country_id;
			$d_arr['shipping_company_id'] = $shipping_company_id;
			$d_arr['quantity'] = $quantity;
			$d_arr['product_id'] = $product_id;
			$d_arr['matrix_id'] = $matrix_id;

			$shipping_template_service = new ShippingTemplateService();
			$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($ship_template_id,0,$product_id,$quantity,array('country_id' => $shipping_country_id));
			//echo "<pre>";print_r($shipping_companies_list);echo "</pre>";
			$error_msg = 'Invalid shipping template ID';
			if(count($shipping_companies_list) > 0) {
				$d_arr['shipping_companies_list'] = $shipping_companies_list;
				$error_msg = '';
			}
			return View::make('shippingCompanyPopup', compact('ship_template_id', 'd_arr', 'error_msg'));
		}
	}

	public function postUpdateShippingCountryAndCost()
	{
		$product_id = Input::has('product_id') ? Input::get('product_id') : '';
		$ship_template_id = Input::has('ship_template_id') ? Input::get('ship_template_id') : '';
		$shipping_country_id = Input::has('shipping_country') ? Input::get('shipping_country') : '';
		$shipping_company_id = Input::has('shipping_company') ? Input::get('shipping_company') : '';
		$quantity = Input::has('quantity') ? Input::get('quantity') : 1;
		$matrix_id = (CUtil::chkIsAllowedModule('variations') && Input::has('matrix_id')) ? Input::get('matrix_id') : 0;
		if($shipping_country_id != '')
		{
			$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_country', $shipping_country_id);
			$shipping_country_name = Products::getCountryNameByCountryId($shipping_country_id);
			$shipping_template_service = new ShippingTemplateService();

			$shipping_company_name = '--';
			$shipping_fee = 0;
			$shipping_fee_formated = '';
			$shipping_fee_unformated = array();
			$shipping_company_err_msg = '';
			$shipping_fee_impact = '';
			if(CUtil::chkIsAllowedModule('variations')){
				$variation_shipping_impact = new VariationsService;
				$shipping_fee_impact = $variation_shipping_impact->getItemMatrixDetailsShippingFee($product_id, $matrix_id);
			}
			$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($ship_template_id, $shipping_company_id, $product_id, $quantity, array('country_id' => $shipping_country_id));
			if(count($shipping_companies_list) > 0) {
				foreach($shipping_companies_list as $details) {
					$details['shipping_fee'] = str_replace(',','',$details['shipping_fee']);
					$shipping_company_name = $details['company_name'];
					$shipping_fee = (isset($details['shipping_fee']) && $details['shipping_fee']!='' && $details['shipping_fee'] >= 0)?$details['shipping_fee']:'0';
					$shipping_fee_formated = CUtil::convertAmountToCurrency($details['shipping_fee'] + $shipping_fee_impact, Config::get('generalConfig.site_default_currency'), '', true);
					$shipping_fee_unformated = CUtil::convertAmountToCurrency($details['shipping_fee'] + $shipping_fee_impact, Config::get('generalConfig.site_default_currency'), '', true, false, true);
					$shipping_company_err_msg = isset($details['error_message']) ? $details['error_message'] : '';
				}
			}

			//Group price setting
            $product_price = 0;
            $org_price = 0;
            $product_price_formated = '<strong>$0</strong>';
            $product_price_unformatted = array();
            $org_price_formated = '<strong>$0</strong>';
            $org_price_unformated = array();
            $logged_user_id = BasicCUtil::getLoggedUserId();
            $price_group = $this->ShowCartService->getPriceGroupsDetailsNew($product_id, $logged_user_id, $quantity, $matrix_id, false);
			if(count($price_group) > 0) {
				$product_price = $price_group['discount'] * $quantity;
				$product_price_formated = CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true);
				$product_price_unformated = CUtil::convertAmountToCurrency($price_group['discount'], Config::get('generalConfig.site_default_currency'), '', true, false, true);
				$org_price = $price_group['price'];
				$org_price_formated = CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true);
				$org_price_unformated = CUtil::convertAmountToCurrency($price_group['price'], Config::get('generalConfig.site_default_currency'), '', true, false, true);

			}
			$toal_with_shipping = $product_price + $shipping_fee + $shipping_fee_impact;
			$toal_with_shipping_formatted = CUtil::convertAmountToCurrency($toal_with_shipping, Config::get('generalConfig.site_default_currency'), '', true);
			$toal_with_shipping_unformatted = CUtil::convertAmountToCurrency($toal_with_shipping, Config::get('generalConfig.site_default_currency'), '', true, false, true);
			$values_checking = array();
			if(CUtil::chkIsAllowedModule('variations')){
				$variation_service = new VariationsService;
				$values_checking = $variation_service->getItemMatrixDetailsArr($product_id, $shipping_fee);
			}

			//echo "<pre>";print_r(array(	'result'=>'success', 'shipping_country_id'=> $shipping_country_id, 'shipping_country'=> $shipping_country_name, 'shipping_company_id' => $shipping_company_id, 'shipping_company' => $shipping_company_name, 'shipping_fee' => $shipping_fee, 'shipping_fee_formated' => $shipping_fee_formated, 'shipping_fee_unformated' => $shipping_fee_unformated, 'product_price' => $product_price, 'product_price_formated' => $product_price_formated, 'product_price_unformated' => $product_price_unformated, 'org_price' => $org_price, 'org_price_formated' => $org_price_formated, 'org_price_unformated' => $org_price_unformated, 'shipping_company_err_msg' => $shipping_company_err_msg, 'toal_with_shipping' => $toal_with_shipping, 'toal_with_shipping_formatted' => $toal_with_shipping_formatted, 'toal_with_shipping_unformatted' => $toal_with_shipping_unformatted));echo "</pre>";exit;
			echo json_encode(array(	'result'=>'success', 'matrix_details_arr' => $values_checking, 'matrix_id'=>$matrix_id, 'product_id' => $product_id, 'shipping_country_id'=> $shipping_country_id, 'shipping_country'=> $shipping_country_name, 'shipping_company_id' => $shipping_company_id, 'shipping_company' => $shipping_company_name, 'shipping_fee' => $shipping_fee, 'shipping_fee_formated' => $shipping_fee_formated, 'shipping_fee_unformated' => $shipping_fee_unformated, 'product_price' => $product_price, 'product_price_formated' => $product_price_formated, 'product_price_unformated' => $product_price_unformated, 'org_price' => $org_price, 'org_price_formated' => $org_price_formated, 'org_price_unformated' => $org_price_unformated, 'shipping_company_err_msg' => $shipping_company_err_msg, 'toal_with_shipping' => $toal_with_shipping, 'toal_with_shipping_formatted' => $toal_with_shipping_formatted, 'toal_with_shipping_unformatted' => $toal_with_shipping_unformatted));
			exit;
		}
		echo json_encode(array(	'result'=>'error', 'error_msg' => trans('showCart.select_shipping_country')));
		exit;
	}
	public function postCartCompaniesList()
	{
		$cart_id  = Input::get('cart_id');
		$item_id  = Input::get('item_id');
		$quantity  = Input::get('quantity');
		$shipping_template  = Input::get('shipping_template');
		$item_owner_id = Input::get('item_owner_id');
		$variation_shipping_price = Input::get('variation_shipping_price');
		$use_giftwrap = Input::get('use_giftwrap');
		$allow_variation = Input::has('allow_variation') ? Input::get('allow_variation') : 0;
		$cart['item_owner_id'] = $item_owner_id;

		$shipping_companies_details = $this->ShowCartService->getShippingTemplateDetails($cart_id, $shipping_template, $item_id, $quantity);
        $shipping_fee = 0;
        if(count($shipping_companies_details) > 0) {
        	if(isset($shipping_companies_details['shipping_company_fee_selected']) && $shipping_companies_details['shipping_company_fee_selected'] != '') {
            	$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'];
            	if($allow_variation) {
	            	if(isset($variation_shipping_price) && $variation_shipping_price !=''){
						$shipping_fee = $shipping_companies_details['shipping_company_fee_selected'] + $variation_shipping_price;
					}
	            	if($shipping_fee < 0) {
	                    $shipping_fee = 0;
	                }
	            }
	        }
        }
        $total_shipping_fee = $quantity * $shipping_fee;

        return view::make('shippingCompanyPopupMinAjax', array('shipping_companies_details' => $shipping_companies_details, 'shipping_template' => $shipping_template, 'cart_id' => $cart_id, 'item_id' => $item_id, 'cart' => $cart, 'variation_shipping_price' => $variation_shipping_price, 'allow_variation' => $allow_variation));

	}

	public function getChangeShippingCountry()
	{
		$redirect_to = Input::has('redirect_to') ? Input::get('redirect_to') : 'cart';
		if($redirect_to == 'checkout'){
			$redirect_to_pg = 'checkout';
			$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : '';
			if($item_owner_id!='')
				$redirect_to_pg.= '/'.$item_owner_id;
		}
		else{
			$redirect_to_pg = 'cart';
		}
		$shipping_country_id = Input::has('shipping_country') ? Input::get('shipping_country') : '';
		if($shipping_country_id != '' && $shipping_country_id > 0) {
			$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_country', $shipping_country_id);
		 	# Redirecting since we need to get the values from cookie
		 	return Redirect::to($redirect_to_pg)->withCookie($cookie);
		}
		return Redirect::to($redirect_to_pg);
	}

	public function getChangeShippingCompany()
	{
		$redirect_to = Input::has('redirect_to') ? Input::get('redirect_to') : 'cart';
		if($redirect_to == 'checkout'){
			$redirect_to_pg = 'checkout';
			$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : '';
			if($item_owner_id!='')
				$redirect_to_pg.= '/'.$item_owner_id;
		}
		else{
			$redirect_to_pg = 'cart';
		}

		$cart_id = Input::has('cart_id') ? Input::get('cart_id') : '';
		$shipping_company_id = Input::has('shipping_company') ? Input::get('shipping_company') : '';
		if($shipping_company_id > 0) {
			$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_company_'.$cart_id, $shipping_company_id);
		 	# Redirecting since we need to get the values from cookie
		 	return Redirect::to($redirect_to_pg)->withCookie($cookie);
		}
		return Redirect::to($redirect_to_pg);
	}

	public function getUpdateItemVariation()
	{
		$inputs = Input::all();
		if(CUtil::chkIsAllowedModule('variations'))
		{
			$this->variation_service = new VariationsService();
			$d_arr = array();
			$d_arr['item_id'] = Input::get('item_id', 0);
			$d_arr['matrix_id'] = Input::get('matrix_id', 0);
			$d_arr['r_fnname'] = Input::get('r_fnname', 0);
			$d_arr['err_msg'] = 'Invalid Access';
			if($d_arr['item_id'] > 0 && $d_arr['matrix_id'])
			{
				$product = Products::initialize();
				$product->setFilterProductStatus('Ok');
				$product->setFilterProductExpiry(true);
				$product->setProductId($d_arr['item_id']);
				$p_details = $product->getProductDetails();

				$d_arr['show_variation'] = 0;
				if($p_details['use_variation'] > 0 && $p_details['is_downloadable_product'] == 'No')
				{
					$d_arr['err_msg'] = '';
					$d_arr['allow_variation'] = (Config::has('plugin.variations') && Config::get('plugin.variations')) ? 1 : 0;
					if($d_arr['allow_variation'])
					{
						$this->variation_service->populateAttributeLabelsList($d_arr['item_id']);
						$variation_det = $this->variation_service->populateVariationAttributes($p_details['id'], $d_arr['matrix_id'], $p_details['product_user_id']);
						$d_arr['variation_det'] = $variation_det;
						$d_arr['show_variation'] = (isset($variation_det['show_variation']) && $variation_det['show_variation'] > 0 ) ? 1 : 0;
					}
				}
			}
		}
		$product_this_obj = $this;		//	'cart_id' => $cart_id, 'cart' => $cart,  'item_id' => $item_id
		return View::make('variations::updateItemVariation', compact('d_arr', 'p_details', 'product_this_obj'));
	}

	public function postUpdateItemVariation()
	{
		$error_exists = false;
		$error_msg = '';
		$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : 0;
		$item_id = Input::has('item_id') ? Input::get('item_id') : 0;
		$matrix_id = Input::has('matrix_id') ? Input::get('matrix_id') : 0;
		$this->cart_cookie = $this->ShowCartService->getCartCookieId();

		$cart_arr = array();

		$cart_obj = Addtocart::initialize();
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		//echo "<pre>";print_r($cart_details);echo "</pre>";die;
		if(count($cart_details) > 0 )
		{
			foreach($cart_details as $cart) {
				if($cart['item_owner_id'] == $item_owner_id && $cart['item_id'] == $item_id)
				{
					$cart_obj->setCartUserId($item_owner_id);
					$cart_obj->setCartItemId($cart['item_id']);
					$cart_obj->setCartCookieId($this->cart_cookie);
					$cart_obj->setCartItemQuantity($cart['qty']);
					$cart_obj->setCartItemMatrixId($matrix_id);
					$response = $cart_obj->add();

					$json_data = json_decode($response, true);
					if($json_data['status'] == 'error')
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
						$error_exists = true;
						echo $error_exists;exit;
					}
					$ret_arr['error_exists'] = $error_exists;
					echo json_encode($ret_arr);exit;
				}
			}
		}

		$error_exists = true;
		echo $error_exists;exit;
	}

	public function postUpdateItemGiftwrap()
	{
		$error_exists = false;
		$error_msg = '';
		$item_owner_id = Input::has('item_owner_id') ? Input::get('item_owner_id') : 0;
		$item_id = Input::has('item_id') ? Input::get('item_id') : 0;
		$use_giftwrap = Input::has('use_giftwrap') ? Input::get('use_giftwrap') : 0;
		$this->cart_cookie = $this->ShowCartService->getCartCookieId();

		$cart_arr = array();
		$cart_obj = Addtocart::initialize();
		$cart_obj->setFilterCookieId($this->cart_cookie);
		$cart_details = $cart_obj->contents();
		if(count($cart_details) > 0 )
		{
			foreach($cart_details as $cart) {
				if($cart['item_owner_id'] == $item_owner_id && $cart['item_id'] == $item_id)
				{
					$cart_obj->setCartUserId($item_owner_id);
					$cart_obj->setCartItemId($cart['item_id']);
					$cart_obj->setCartCookieId($this->cart_cookie);
					$cart_obj->setCartItemQuantity($cart['qty']);
					$cart_obj->setCartItemGiftwrap($use_giftwrap);
					$response = $cart_obj->add();

					$json_data = json_decode($response, true);
					if($json_data['status'] == 'error')
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
						$error_exists = true;
						echo $error_exists;exit;
					}
					$ret_arr['error_exists'] = $error_exists;
					echo json_encode($ret_arr);exit;
				}
			}
		}
		$error_exists = true;
		echo $error_exists;exit;
	}
}