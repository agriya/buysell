<?php

class MissingBilingAddressParamsExecption extends Exception {}
class BillingAddressService
{
	public function addBillingAddress($inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($inputs) || !is_array($inputs))
				throw new MissingBilingAddressParamsExecption(trans('addressing.input_array_empty'));

			if(!isset($inputs['address_id']) || empty($inputs['address_id']))
				throw new MissingBilingAddressParamsExecption('address_id cant be empty. ');

			$address_det = Webshopaddressing::Addressing()->getAddresses(array('id'=>$inputs['address_id']), 'first');
			if(!$address_det || !$address_det->user_id)
				throw new MissingBilingAddressParamsExecption(trans('addressing.address_id_not_avail'));
			$shipping_address = '';
			$shipping_address_arr = array();
			if($address_det->address_line1 != '')
				$shipping_address_arr[]=$address_det->address_line1;
			if($address_det->address_line2 != '')
				$shipping_address_arr[]=$address_det->address_line2;
			if($address_det->street != '')
				$shipping_address_arr[]=$address_det->street;
			if($address_det->city != '')
				$shipping_address_arr[]=$address_det->city;
			if($address_det->state != '')
				$shipping_address_arr[]=$address_det->state;
			if($address_det->country != '')
				$shipping_address_arr[]=$address_det->country;
			if($address_det->zip_code != '')
				$shipping_address_arr[]=$address_det->zip_code;
			if($address_det->phone_no != '')
				$shipping_address_arr[]=$address_det->phone_no;
			$shipping_address = implode(',',$shipping_address_arr);
			$inputs['shipping_address'] = $shipping_address;
			$user_id = $address_det->user_id;
			$inputs['user_id'] = $user_id;

			if(!isset($inputs['billing_address_id']))
			{
				$inputs['billing_address_id'] = $inputs['address_id'];
			}
			$billing_address_det = Webshopaddressing::Addressing()->getAddresses(array('id'=>$inputs['billing_address_id']), 'first');
			if(!$billing_address_det)
				throw new MissingBilingAddressParamsExecption(trans('addressing.bliing_address_id_invalid'));

			$billing_address = '';
			$billing_address_arr = array();
			if($billing_address_det->address_line1 != '')
				$billing_address_arr[]=$billing_address_det->address_line1;
			if($billing_address_det->address_line2 != '')
				$billing_address_arr[]=$billing_address_det->address_line2;
			if($billing_address_det->street != '')
				$billing_address_arr[]=$billing_address_det->street;
			if($billing_address_det->city != '')
				$billing_address_arr[]=$billing_address_det->city;
			if($billing_address_det->state != '')
				$billing_address_arr[]=$billing_address_det->state;
			if($billing_address_det->country != '')
				$billing_address_arr[]=$billing_address_det->country;
			if($billing_address_det->zip_code != '')
				$billing_address_arr[]=$billing_address_det->zip_code;
			if($billing_address_det->phone_no != '')
				$billing_address_arr[]=$billing_address_det->phone_no;
			$billing_address = implode(',',$billing_address_arr);
			$inputs['billing_address'] = $billing_address;

			$rules = array(
				'user_id' 				=> 'required|numeric',
				'order_id'				=> 'required|numeric',
				'address_id' 			=> 'required|numeric',
				'billing_address_id' 	=> 'required|numeric',
				'shipping_address'		=> 'required',
				'billing_address'		=> 'required',
			);
			$valid_keys = array(
				'user_id' 				=> '',
				'order_id'				=> '',
				'address_id' 			=> '',
				'billing_address_id'	=> '',
				'shipping_address'		=> '',
				'billing_address'		=> '',
			);

			$inputs = array_intersect_key($inputs, $valid_keys);
			$inputs = $inputs+$valid_keys;
			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$insertbilingaddress = BillingAddress::create($inputs);
				return $insertbilingaddress->id;
			}
			else
				throw new MissingBilingAddressParamsExecption($validator->messages()->first());
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	public function getBillingAddress($options = array(), $return_type = 'all', $order_by = 'asc')
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(!is_array($options) || empty($options))
				throw new MissingBilingAddressParamsExecption(trans('addressing.option_array_empty'));
			if(is_array($options) && !empty($options) && (!isset($options['id']) && !isset($options['order_id']) && !isset($options['user_id']))) {
				throw new MissingBilingAddressParamsExecption(trans('addressing.option_input_invalid'));
			}
			if(is_null($return_type)){$return_type = 'all';}
			if(!in_array($return_type, array('all', 'first')))
				throw new MissingBilingAddressParamsExecption(trans('addressing.billing_return_type_invalid'));

			//Create model object for address
			$address = BillingAddress::orderby('id',$order_by);

			//Conditions based on input
			if(isset($options['id']) && $options['id'] > 0)
				$address->where('id','=',$options['id']);
			if(isset($options['order_id']) && $options['order_id'] !='')
				$address->where('order_id','=',$options['order_id']);
			if(isset($options['user_id']) && $options['user_id'] !='')
				$address->where('user_id','=',$options['user_id']);

			//Check the return type and get hte list
			if($return_type == 'first')
				$addresslist = $address->first();
			else
				$addresslist = $address->get();

			//Return the list
			if(count($addresslist) > 0)
			{
				foreach($addresslist as $key => $addressdet)
				{
					$addresslist[$key]->shipping_address_arr = ($addressdet->shipping_address!='')?explode(',',$addressdet->shipping_address):array();
					$addresslist[$key]->billing_address_arr = ($addressdet->billing_address)?explode(',',$addressdet->billing_address):array();
					//echo "<pre>";print_r($addressdet);echo "</pre>";exit;
					$address_obj = Webshopaddressing::Addressing();
					$shipping_address = $address_obj->getAddresses(array('id' => $addressdet->address_id));
					$addresslist[$key]->shipping_address = $shipping_address;

					$addressdet->billing_address_id = (is_null($addressdet->billing_address_id) || $addressdet->billing_address_id == '')?$addressdet->address_id:$addressdet->billing_address_id;
					if($addressdet->address_id == $addressdet->billing_address_id)
						$addresslist[$key]->billing_address = $shipping_address;
					else
						$addresslist[$key]->billing_address = $address_obj->getAddresses(array('id' => $addressdet->billing_address_id));;
				}
				return $addresslist;
			}
			else
				return false;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

}