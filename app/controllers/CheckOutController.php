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
class CheckOutController extends BaseController
{
	public $item_owner_id = 0;
	public $checkout_currency = "";
	function __construct()
	{
        parent::__construct();
        $this->CheckOutService = new CheckOutService();
    }

    public function populateCheckOutItems($item_owner_id = 0)
    {
    	/*if (CUtil::isAdmin()) {
    		return Redirect::to('');
		}*/
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($logged_user_id<=0){
			return Redirect::to('');
		}
    	$cart_obj = Addtocart::initialize();
    	$prod_obj = Products::initialize();

    	$input = Input::all();
		$this->item_owner_id = $item_owner_id;
		//$selected_services = (isset($input['product_services']) && $input['product_services'] != '') ? explode(',',$input['product_services']) : array();
		//$this->CheckOutService->setSelectedService($selected_services);
		//echo "<pre>";print_r($selected_services);echo "</pre>";
    	//if(Input::has('item_owner_id') && Input::get('item_owner_id') != "")
    	//{
		//	$this->item_owner_id = $input['item_owner_id'];
		//}
		//if(Input::has('checkout_currency') && Input::get('checkout_currency') != "")
    	//{
    		$this->checkout_currency = Config::get('generalConfig.site_default_currency');//$input['checkout_currency'];
		//}
		/*if($this->item_owner_id == 0)
		{
			$this->item_owner_id = (Session::has('item_owner_id'))? Session::get('item_owner_id') : 0;
		}*/
		if($this->checkout_currency == "")
		{
			$this->checkout_currency = (Session::has('checkout_currency'))? Session::get('checkout_currency') : "";
		}
		$pid = 0;
		$type = "";
		/*if(Input::has('pid') && Input::has('type'))
		{
			$pid = Input::get('pid');
			$type = Input::get('type');
			//add code to add the item to the cart
			if($this->CheckOutService->chkValidCartProductId($pid))
			{
				 $this->CheckOutService->addItemIntoCart($pid, 'product', $cart_obj);
			}
		}*/
		$this->CheckOutService->populateCheckedItems($this->checkout_currency, $pid, $type, $cart_obj, $prod_obj, $this->item_owner_id);//$this->item_owner_id,
		/*if($pid > 0)
		{
			$this->item_owner_id = $this->CheckOutService->item_owner_id;
			$this->checkout_currency = $this->CheckOutService->checkout_currency;
		}*/

		$billing_details = array();
		//$billing_details = $this->CheckOutService->getBillingDetails(0, $logged_user_id);
		if(count($billing_details) < 1)
		{
			if($logged_user_id != 0)
			{
				$user = Sentry::getUser();
				$billing_details['name'] = $user->first_name.' '.$user->last_name;;
				$billing_details['contact_no'] = $user->phone;
			}
		}
		if($logged_user_id == 0)
		{
			if($pid > 0)
			{
				$qry_str = '?pid='.$pid.'&type='.$type;
			}
			else
			{
				$qry_str = '?checkout_currency='.$this->checkout_currency;//.'&item_owner_id='.$this->item_owner_id;
			}
			$checkout_url = URL::to('checkout/'.$item_owner_id).$qry_str;
			Session::put('login_redirect_url', urlencode($checkout_url));
		}
		$CheckOutService = $this->CheckOutService;
		$no_item_msg = "";
		//echo "<pre>";print_r($CheckOutService->cart_item_details_arr);echo "</pre>";
		if(count($CheckOutService->cart_item_details_arr) == 0)
		{
			$no_item_msg = trans('checkOut.items_not_found_cart');
			if($pid > 0)
			{
				$no_item_msg = trans('checkOut.checkout_buynow_invalid_item');
				return Redirect::to('cart')->with('error_message', $no_item_msg);
			}
			return Redirect::to('cart');
		}

		//Adress details
		$shipping_address_id = $billing_address_id = $last_shipping_country_id = 0;
		$shipping_address_details = array();
		//$shipping_billing_address_ids_arr = $CheckOutService->getUserCartShippingAddress($logged_user_id);
		$shipping_billing_address_ids_arr = $CheckOutService->getUserShippingAddress($logged_user_id);
		//echo "<pre>";print_r($shipping_billing_address_ids_arr);echo "</pre>";
		if(count($shipping_billing_address_ids_arr) > 0)
		{
			$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];
			$billing_address_id = $shipping_billing_address_ids_arr['billing_address_id'];
		}
		$address_obj = Webshopaddressing::Addressing();
		$shipping_address = $address_obj->getAddresses(array('id' => $shipping_address_id));
		$last_shipping_country_id = BasicCUtil::getCookie(Config::get('generalConfig.site_cookie_prefix')."_shipping_country");
		if($shipping_address && count($shipping_address) > 0)
		{
			$last_shipping_country_id = isset($shipping_address[0]->country_id) ? $shipping_address[0]->country_id : 0;
			//if($last_shipping_country_id!='' && $last_shipping_country_id > 0)
				//Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $last_shipping_country_id);
		}

		$countries = array('' => trans('common.select_a_country'));
		$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc');
		$countries_list = $countries+$countries_arr;
		$d_arr['countries_list'] = $countries_list;
		$d_arr['item_owner_id'] = $this->item_owner_id;
		$d_arr['checkout_currency'] = $this->checkout_currency;
		$d_arr['pid'] = $pid;
		$d_arr['type'] = $type;
		$ProductService = new ProductService();
		//echo "<pre>";print_r($d_arr);echo "</pre>";
		$breadcrumb_arr = array(trans("checkOut.checkout_title"));
		//return View::make('checkOut', compact('CheckOutService', 'breadcrumb_arr', 'd_arr', 'billing_details', 'no_item_msg', 'selected_services'));
		$get_common_meta_values = Cutil::getCommonMetaValues('checkout');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		$d_arr['change_variation_url'] = '';
		$d_arr['show_giftwrap_column'] = 0;
		if(CUtil::chkIsAllowedModule('variations') && Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap'))
			$d_arr['show_giftwrap_column'] = 1;
		//$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $last_shipping_country_id);
		return View::make('checkOut', compact('CheckOutService', 'ProductService', 'breadcrumb_arr', 'd_arr', 'billing_details', 'no_item_msg', 'shipping_address_details', 'shipping_address_id', 'billing_address_id', 'last_shipping_country_id', 'item_owner_id'));//->withCookie(BasicCUtil::getCookie(Config::get('generalConfig.site_cookie_prefix')."_shipping_country"));
	}

	public function getShippingAddressPopup()
	{
		$last_country_id = 0;
		$shipping_address_id = 0;
		$billing_address_id = 0;
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$options = array('user_id' => $logged_user_id);
		$user_addresses = Webshopaddressing::Addressing()->getAddresses($options);
		$address_type = (Input::has('address_type') && Input::get('address_type')!='')?Input::get('address_type'):'shipping';
		$selected_address_id = (Input::has('address_id') && Input::get('address_id')!='')?Input::get('address_id'):'0';
		$item_owner_id = (Input::has('item_owner_id') && Input::get('item_owner_id')!='')?Input::get('item_owner_id'):'0';
		$is_inserted = false;


		$logged_user_id = BasicCUtil::getLoggedUserId();
		$d_arr = array();
		$shipping_address_details = Webshopaddressing::BillingAddress()->getBillingAddress(array('user_id' => $logged_user_id), 'all', 'desc');

		$countries = array('' => trans('common.select_a_country'));
		$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc');
		$countries_arr = $countries_arr; //array_except($countries_arr, array('38'));//Remove china
		$countries_list = $countries+$countries_arr;
		$d_arr['countries_list'] = $countries_list;



		if(Input::has('country_id'))
		{
			$last_country_id = Input::get('country_id');
			$input = Input::all();
			//echo "<pre>";print_r($input);echo "</pre>";
			if(isset($input['make_as_default']) && $input['make_as_default']=='1')
				$input['is_primary'] = 'Yes';
			else
				$input['is_primary'] = 'No';
			$input['address_type'] = 'shipping';
			$rules = Webshopaddressing::Addressing()->validationrules();
			unset($rules['user_id']);
			unset($rules['country']);
			$billing_rule = array();

			$v = Validator::make($input, $rules);
			//echo "<pre>";print_r($input);echo "</pre>";exit;
			if($v->passes())
			{

				//echo "<pre>";print_r($input);echo "</pre>";exit;
				if($input['address_id']==0 || $input['address_id']=='')
					$shipping_address_id = $this->AddressesActions($input, 'add');
				else
				{
					$this->AddressesActions($input, 'update', $input['address_id']);
					$shipping_address_id = $input['address_id'];
				}

				if($input['is_primary'] && $input['is_primary']=='Yes')
				{
					$this->AddressesActions(array('user_id' => $logged_user_id), 'make_primary', $shipping_address_id);
				}
				else
				{
					$this->AddressesActions(array('user_id' => $logged_user_id), 'check_and_make_primary', $shipping_address_id);
				}

				//$address_type = Input::get('address_type');
				if($address_type == 'shipping')
					$cookie_name = Config::get('generalConfig.site_cookie_prefix').'_shipping_address_'.$logged_user_id;
				else
					$cookie_name = Config::get('generalConfig.site_cookie_prefix').'_billing_address_'.$logged_user_id;

				$cookie = Cookie::forever($cookie_name, $shipping_address_id);
				//return Response::make()->withCookie($cookie);
				$is_inserted = true;


				$country_id = Input::get("country_id");
				if($country_id != "")
				{
					$country_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $country_id);
				}


				return Response::make(View::make('shippingAddressPopup', compact('shipping_address_details', 'd_arr', 'shipping_address_id', 'billing_address_id', 'is_inserted', 'last_country_id', 'user_addresses', 'address_type', 'selected_address_id', 'item_owner_id')))->withCookie($country_cookie)->withCookie($cookie);




				/*if(!isset($input['use_as_billing_address']))
					$billing_address_id = $this->AddressesActions($input, 'billing');
				else
					$billing_address_id = $shipping_address_id;*/

				/*
				Commented to remove the user last purchase cart shipping concept
				$user_cart_shipping = UserCartShippingAddress::where('user_id', '=', $logged_user_id)->count();
				if($user_cart_shipping <=0)
				{
					$cart_shipping = array();
					$cart_shipping['user_id'] = $logged_user_id;
					$cart_shipping['shipping_address_id'] = $shipping_address_id;
					$cart_shipping['billing_address_id'] = $billing_address_id;

					$userCartShippingAddress = new UserCartShippingAddress();
					$userCartShippingAddress->addNew($cart_shipping);
				}
				else
				{
					$cart_shipping = array();
					$cart_shipping['shipping_address_id'] = $shipping_address_id;
					$cart_shipping['billing_address_id'] = $billing_address_id;
					UserCartShippingAddress::where('user_id','=', $logged_user_id)->update($cart_shipping);
				}*/
				$is_inserted = true;

			}
			else
			{
				return View::make('shippingAddressPopup', compact('shipping_address_id', 'billing_address_id', 'is_inserted', 'last_country_id', 'user_addresses', 'address_type', 'selected_address_id', 'item_owner_id'))->withInput()->withErrors($v);
			}
		}

		return View::make('shippingAddressPopup', compact('shipping_address_details', 'd_arr', 'shipping_address_id', 'billing_address_id', 'is_inserted', 'last_country_id', 'user_addresses', 'address_type', 'selected_address_id', 'item_owner_id'));
	}

	public function validateCouponCode($cart_obj, $prod_obj)
	{
		$input = Input::all();
		$pid = 0;
		$type = "";
		$this->err_msg = '';
		if(Input::has('pid') && Input::has('type'))
		{
			$pid = $input['pid'];
			$type = $input['type'];
		}
		$this->CheckOutService->populateCheckedItems($input['checkout_currency'], $pid, $type, $cart_obj, $prod_obj, $input['item_owner_id']);//$input['item_owner_id']
		$discount = 0;
		$shipping_amount = 0;
		if(isset($input['is_shipping_needed']) && $input['is_shipping_needed'] ==1)
		{
			$shipping_amount = $this->CheckOutService->chkAndSetShippingAmount($input['country_id']);
			//echo 'fgf<pre>';print_r($shipping_amount);echo '<pre>';exit;
			if(is_array($shipping_amount))
			{
				$error_countries = implode(',',$shipping_amount);
				$this->err_msg = trans('checkOut.following_products_wont_shipped')."(".$error_countries.")";
				//echo "shipping_amount: <pre>";print_r($shipping_amount);echo "</pre>";exit;
				return false;
			}
		}
		if(!isset($input['billing_address_id']) || (isset($input['billing_address_id']) && $input['billing_address_id']<=0))
		{
			$this->err_msg = trans('checkOut.billing_adress_is_mandatory');
			return false;
		}
		//$shipping_address = $this->CheckOutService->chkAndSetShippingAmount($input['country']);

		//echo "shipping_amount: ".$shipping_amount;exit;


		$total_amt = $this->CheckOutService->getTotalAmount();
		$coupon_code = Input::get('applied_coupon_code');
		$item_owner_id = Input::get('item_owner_id');

		$this->coupon_details = array();
		$couponService =  new CouponService();

		if($coupon_code!='')
		{
			$return_arr = $couponService->validateCouponCode($coupon_code, $item_owner_id, $total_amt);
			if($return_arr['status']!='success'){
				$this->err_msg = $return_arr['error_message'];
				return false;
			}
			else
			{
				$this->coupon_details = $return_arr;
			}
		}
		return true;
		/*if(!($coupon_details = $this->mpCheckOutService->getCouponCodeDetails($input)))
		{*/
			//$discounted_amt = CUtil::getBaseAmountToDisplay($total_amt - $discount, $input['checkout_currency']);
			//$discount = CUtil::getBaseAmountToDisplay($discount, $input['checkout_currency']);
			//return 'error::'.$discount.'::'.($discounted_amt);
		/*}*/

		/*$coupon_price_from = $coupon_details['price_from'];
		$coupon_price_to = $coupon_details['price_to'];
		$offer_amount = $coupon_details['offer_amount'];
		if($coupon_details['currency'] != $input['checkout_currency'])
		{
			$coupon_price_from = CUtil::convertAmountToCurrency($coupon_price_from, $coupon_details['currency'], $input['checkout_currency'], $apply_exchange_fee = false);
			$coupon_price_to = CUtil::convertAmountToCurrency($coupon_price_to, $coupon_details['currency'], $input['checkout_currency'], $apply_exchange_fee = false);
			if($coupon_details['offer_type'] != 'percent')
			{
				$offer_amount = CUtil::convertAmountToCurrency($offer_amount, $coupon_details['currency'], $input['checkout_currency'], $apply_exchange_fee = false);
			}
		}

		if(!(($coupon_price_from > 0 and $total_amt < $coupon_price_from) or ($coupon_price_to > 0 and $total_amt > $coupon_price_to)))
		{
			if($coupon_details['offer_type'] == 'percent')
			{
				$discount = $discount + ($total_amt * $offer_amount/100);
			}
			else
			{
				$discount = $discount + $offer_amount;
			}
		}
		$this->discount_amt = $discount;
		$discounted_amt = ($total_amt - $discount);
		if($discounted_amt < 0)
		{
			$discounted_amt = 0;
		}
		$discount = CUtil::getBaseAmountToDisplay($discount, $input['checkout_currency']);
		$discounted_amt = CUtil::getBaseAmountToDisplay($discounted_amt, $input['checkout_currency']);
		return 'success::'.$discount.'::'.$discounted_amt;*/
	}

	public function doCheckOut()
	{
		//Log::info('IN ====>doCheckOut');
		$cart_obj = Addtocart::initialize();
		$order_obj = Webshoporder::initialize();
		$prod_obj = Products::initialize();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		//echo get_class($prod_obj);exit;

		$input = Input::all();
		//echo "<pre>";print_r($input);echo "</pre>";exit;
		if(isset($input['act']) && $input['act'] == "remove_all")
		{
			$this->CheckOutService->deleteCart($input, $cart_obj);
			return Redirect::to('cart')->with('success_message', trans('showCart.cart_remove_success'));
		}
		else if(Input::has('do_checkout'))
		{
			$item_owner_id = Input::get('item_owner_id');
			//Log::info('IN STEP2 ====>doCheckOut');
			/*$rules = array();
			if(isset($input['is_shipping_needed']) && $input['is_shipping_needed'] ==1)
			{
				$rules = Webshopaddressing::Addressing()->validationrules();
				//echo "<pre>";print_r($rules);echo "</pre>";
				unset($rules['user_id']);
				unset($rules['country']);
				$billing_rule = array();
				if(!isset($input['use_as_billing_address']))
				{
					foreach($rules as $field => $rule)
					{

						$billing_rule['billing_'.$field] = $rule;
					}
				}
				//echo "<pre>";print_r($billing_rule);echo "</pre>";
				$rules = array_merge($rules,$billing_rule);
				//echo "<pre>";print_r($rules);echo "</pre>";
			}
			$v = Validator::make($input, $rules);*/
			if ( true)
			{
				$coupon_data = $this->validateCouponCode($cart_obj, $prod_obj);
				if(!$coupon_data)
				{
					return Redirect::to('checkout/'.$item_owner_id)->with('checkout_currency', $input['checkout_currency'])->with('error_message',$this->err_msg)->withInput();
				}

				/*$coupon_data = explode('::', $coupon_data);
				$discount_amount = $coupon_data[1];
				if($coupon_data[0] != 'success')
				{
					$input['coupon_code'] = "";
				}*/
				$input['discount_amount'] = 0;
				//$input['discounted_amount'] = $this->coupon_details['discounted_amount'];
				$input['coupon_code'] = '';
				if(isset($this->coupon_details) && !empty($this->coupon_details))
				{
					$input['discount_amount'] = $this->coupon_details['discount_amount'];
					$input['discounted_amount'] = $this->coupon_details['discounted_amount'];
					$input['coupon_code'] = $input['applied_coupon_code'];
				}
				$order_id = $this->CheckOutService->addOrderDetails($input, $cart_obj, $order_obj, $item_owner_id);

				//if(isset($input['is_shipping_needed']) && $input['is_shipping_needed'] ==1)
				//{
					/*$ship_address_id = $this->AddressesActions($input, 'shipping');
					if(!isset($input['use_as_billing_address']))
						$billing_address_id = $this->AddressesActions($input, 'billing');
					else
						$billing_address_id = $ship_address_id;*/
					//$shipping_billing_address_ids_arr = $this->CheckOutService->getUserCartShippingAddress($logged_user_id);
					$shipping_address_id = (input::has('shipping_address_id') && input::get('shipping_address_id')!='')?input::get('shipping_address_id'):'';
					$billing_address_id = (input::has('billing_address_id') && input::get('billing_address_id')!='')?input::get('billing_address_id'):'';
					if(isset($input['use_as_billing']) && $input['use_as_billing']=='1')
						$billing_address_id = $shipping_address_id;
					//echo "<br>check shipping_address_id: ".$shipping_address_id;
					//echo "<br>check billing_address_id: ".$billing_address_id;
					if($shipping_address_id=='' || $billing_address_id == '')
					{
						$shipping_billing_address_ids_arr = $this->CheckOutService->getUserShippingAddress($logged_user_id);
						if(count($shipping_billing_address_ids_arr) > 0)
						{
							if($shipping_address_id=='')
								$shipping_address_id = $shipping_billing_address_ids_arr['shipping_address_id'];
							if($billing_address_id=='')
								$billing_address_id = $shipping_billing_address_ids_arr['billing_address_id'];
						}
					}
					$inputs = array(
						'order_id' 			=> $order_id,
						'address_id' 		=> $shipping_address_id,
						'billing_address_id' => $billing_address_id,
					);
					//echo "<pre>";print_r($inputs);echo "</pre>";
					try{
						$billingaddressid = Webshopaddressing::BillingAddress()->addBillingAddress($inputs);
						if($shipping_address_id!='')
							$ship_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_shipping_address_'.$logged_user_id, $shipping_address_id);
						if($billing_address_id!='')
							$bill_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix').'_billing_address_'.$logged_user_id, $billing_address_id);
						//echo "<pre>";print_r($ship_cookie);echo "</pre>";
						//echo "<pre>";print_r($bill_cookie);echo "</pre>";
						//exit;
					}
					catch(Exception $e)
					{
						//dont do anything if there are any issue
					}
				//}
				$redirect = Redirect::to('pay-checkout/'.$order_id);
				if(isset($ship_cookie))
					$redirect = $redirect->withCookie($ship_cookie);
				if(isset($bill_cookie))
					$redirect = $redirect->withCookie($bill_cookie);

				return $redirect;
			}
			else
			{
				Log::info('IN STEP3 ====>checkout_currency'.$input['checkout_currency']);
				return Redirect::to('checkout/'.$item_owner_id)->with('checkout_currency', $input['checkout_currency'])->withInput()->withErrors($v);
			}
		}
	}

	public function AddressesActions($input, $action='add', $id=null)
	{
		try
		{
			switch($action)
			{
				case 'add':
						$address_type = isset($input['address_type'])?$input['address_type']:'shipping';
						$address_type = (in_array($address_type, array('shipping','billing')))?$address_type:'shipping';
						$prefix = ($address_type == 'shipping')?'':'biling_';
						$logged_user_id = BasicCUtil::getLoggedUserId();
						$country_name = Webshopaddressing::AddressingCountry()->getCountries(array('id'=>$input[$prefix.'country_id']), 'first');
						if(count($country_name) >0 )
							$country_name = $country_name->country;
						else
							$country_name = ' ';
						$inputs = array(
							'user_id' 			=> $logged_user_id,
							'address_line1' 	=> $input[$prefix.'address_line1'],
							'address_line2' 	=> $input[$prefix.'address_line2'],
							'street'			=> $input[$prefix.'street'],
							'city' 				=> $input[$prefix.'city'],
							'state'				=> $input[$prefix.'state'],
							'zip_code'			=> $input[$prefix.'zip_code'],
							'phone_no'			=> $input[$prefix.'phone_no'],
							'country'			=> $country_name,
							'country_id'		=> $input[$prefix.'country_id'],
							'address_type'		=> $address_type,
							'is_primary'		=> $input[$prefix.'is_primary'],
						);
						$address_id = Webshopaddressing::Addressing()->addAddresses($inputs);
						return $address_id;
					break;

				case 'update':
						if(isset($input['country_id']))
						{
							$country_name = Webshopaddressing::AddressingCountry()->getCountries(array('id'=>$input['country_id']), 'first');
							if($country_name && count($country_name) >0 )
								$input['country'] = $country_name->country;
							else
								$input['country'] = '';
						}
						Webshopaddressing::Addressing()->updateAddress($id, $input);
						return $id;
					break;

				case 'make_primary':
						Webshopaddressing::Addressing()->makeAsPrimaryAddress($id, $input['user_id']);
						return $id;
					break;

				case 'check_and_make_primary':
						$options = array('user_id' => $input['user_id'], 'is_primary' => 'Yes');
						$user_addresses = Webshopaddressing::Addressing()->getAddresses($options);
						if($user_addresses && count($user_addresses)>0)
							return $id;
						else
						{
							Webshopaddressing::Addressing()->makeAsPrimaryAddress($id, $input['user_id']);
						}
						return $id;
					break;

			}

		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	public function postUpdateShippingAddress()
	{
		$address_id = Input::get("address_id");
		$address_type = (Input::has("address_type") && Input::get("address_type")!='')?Input::get("address_type"):'shipping';
		$user_id = (Input::has("user_id") && Input::get("user_id")!='')?Input::get("user_id"):BasicCUtil::getLoggedUserId();
		if($user_id > 0 && $address_id > 0)
		{
			if($address_type == 'shipping')
				$cookie_name = Config::get('generalConfig.site_cookie_prefix').'_shipping_address_'.$user_id;
			else
				$cookie_name = Config::get('generalConfig.site_cookie_prefix').'_billing_address_'.$user_id;

			$cookie = Cookie::forever($cookie_name, $address_id);
			if($address_type == 'shipping')
			{
				$country_id=Input::get('country_id');
				if($country_id!='')
				{
					$country_cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_shipping_country", $country_id);
					return Response::make()->withCookie($cookie)->withCookie($country_cookie);
				}
				else
					return Response::make()->withCookie($cookie);
			}
			else
				return Response::make()->withCookie($cookie);
		}
		//echo "success";exit;
		//return 0;
	}
	public function postCouponDetails()
	{
		$coupon_code = Input::get('coupon_code');
		$item_owner_id = Input::get('item_owner_id');
		$total_amount = Input::get('total_amount');
		$couponService =  new CouponService();
		$return_arr = $couponService->validateCouponCode($coupon_code, $item_owner_id, $total_amount);
		echo json_encode($return_arr);exit;
	}
}