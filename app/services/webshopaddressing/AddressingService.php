<?php

class MissingAddressingParamsExecption extends Exception {}

class AddressingService
{
	public function addAddresses($inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($inputs) || !is_array($inputs) || empty($inputs))
				throw new MissingAddressingParamsExecption(trans('addressing.input_array_empty'));

			$rules = array(
				'user_id' 		=> 'required|numeric',
				'address_line1' => 'required',
				'city'			=> 'required',
				'state' 		=> 'required',
				'country'		=> 'required',
				'country_id'	=> 'required',
				'zip_code'		=> 'required',
				'phone_no'		=> 'required',
			);
			$valid_keys = array(
				'user_id' 			=> '',
				'address_line1'		=> '',
				'address_line2'		=> '',
				'street'			=> '',
				'city'	 			=> '',
				'state'			 	=> '',
				'country'			=> '',
				'country_id' 		=> '',
				'zip_code' 			=> '',
				'phone_no'			=> '',
				'address_type'		=> 'shipping',
				'is_primary'		=> 'No'
			);
			$inputs = array_intersect_key($inputs, $valid_keys);
			$inputs = $inputs+$valid_keys;


			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
			//	print_r($inputs['phone_no']);exit;
				$address = Addresses::create($inputs);
				return $address->id;
			}
			else
			{
				throw new MissingAddressingParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}


	public function validationrules($inputs = array())
	{
		$rules = array(
			'address_line1' => 'required',
			'user_id' 		=> 'required|numeric',
			'city'			=> 'required',
			'state' 		=> 'required',
			'country'		=> 'required',
			'country_id'	=> 'required',
			'zip_code'		=> 'required',
			'phone_no'		=> 'required'
		);
		return $rules;
	}

	public function getAddresses($options = array(), $return_type = 'all', $order_by = 'desc', $primary_first=true)
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($options) || !is_array($options) || (is_array($options) && (!isset($options['id']) && !isset($options['user_id']) && !isset($options['address_type']))))
				throw new MissingAddressingParamsExecption(trans('addressing.options_empty'));

			if(is_null($return_type)){$return_type = 'all';}
			if(!in_array($return_type, array('all','list', 'first', 'paginate')))
				throw new MissingAddressingParamsExecption(trans('addressing.return_type_invalid'));

			if(isset($options['id']) && $options['id'] <= 0)
				return false;
			//Create model object for address
			if($primary_first)
			{
				$address = Addresses::orderby(DB::raw('FIELD(is_primary, \'Yes\')'), 'desc');
				$address->orderby('id', $order_by);
			}
			else
			{
				$address = Addresses::orderby('id', $order_by);
			}

			//Conditions based on input
			if(isset($options['id']) && $options['id'] > 0)
				$address->where('id','=',$options['id']);
			if(isset($options['user_id']) && $options['user_id'] > 0)
				$address->where('user_id','=',$options['user_id']);
			if(isset($options['address_type']) && $options['address_type'] > 0)
				$address->where('address_type','=',$options['address_type']);
			if(isset($options['is_primary']) && $options['is_primary']!='')
				$address->where('is_primary','=',$options['is_primary']);

			//Check the return type and get hte list
			if($return_type == 'list')
				$addresslist = $address->lists('country_id','id');
			elseif($return_type == 'first')
				$addresslist = $address->first();
			elseif($return_type == 'paginate')
				$addresslist = $address->paginate(5);
			else
				$addresslist = $address->get();

			//Return the list
			if(count($addresslist) > 0)
				return $addresslist;
			else
				return false;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}


	public function updateAddress($id = null, $inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($id))
				throw new MissingAddressingParamsExecption(trans('addressing.address_id_empty'));

			$address_det = $this->getAddresses(array('id' => $id), 'first');
			if(!$address_det)
				throw new MissingAddressingParamsExecption(trans('addressing.address_id_not_avail'));

			$user_id = $address_det->user_id;
			if(!$user_id)
				throw new MissingAddressingParamsExecption(trans('addressing.something_went_wrong'));



			$rules = array(
				'address_line1' => 'sometimes|required',
				'city'			=> 'sometimes|required',
				'state' 		=> 'sometimes|required',
				'country'		=> 'sometimes|required',
				'country_id'	=> 'sometimes|required',
				'zip_code'		=> 'sometimes|required',
				'phone_no'		=> 'sometimes|required',
			);
			$valid_keys = array(
				'address_line1'		=> '',
				'address_line2'		=> '',
				'street'			=> '',
				'city'	 			=> '',
				'state'			 	=> '',
				'country'			=> '',
				'country_id' 		=> '',
				'zip_code' 			=> '',
				'phone_no'			=> '',
				'address_type'		=> 'shipping',
				'is_primary'		=> 'No'
			);
			$inputs = array_intersect_key($inputs, $valid_keys);

			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$affectedRows = Addresses::where('id', '=', $id)->update($inputs);
				return $affectedRows;
			}
			else
			{
				throw new MissingAddressingParamsExecption($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

	}
	public function makeAsPrimaryAddress($id=null, $user_id=null)
	{
		if(is_null($id))
			throw new MissingAddressingParamsExecption(trans('addressing.address_id_empty'));
		if(is_null($user_id))
			throw new MissingAddressingParamsExecption(trans('addressing.address_id_empty'));

		$input = array('is_primary' => 'Yes');
		Addresses::where('id', '=', $id)->update($input);
		$input = array('is_primary' => 'No');
		Addresses::where('user_id','=', $user_id)->where('id', '!=', $id)->update($input);
	}
	public function makeFirstAddrAsPrimaryAddress($user_id = null)
	{
		if(is_null($user_id))
			throw new MissingAddressingParamsExecption(trans('addressing.address_id_empty'));

		$input = array('is_primary' => 'Yes');
		Addresses::orderby('id','desc')->where('user_id', '=', $user_id)->take(1)->update($input);
	}
	public function deleteAddress($id = null, $inputs = array())
	{
		try
		{
			//Throw exceptions if inputs are wrong
			if(is_null($id))
				throw new MissingAddressingParamsExecption(trans('addressing.address_id_empty'));

			$address_det = $this->getAddresses(array('id' => $id), 'first');
			if(!$address_det)
				throw new MissingAddressingParamsExecption(trans('addressing.address_id_not_avail'));


			$affectedRows = Addresses::where('id', '=', $id)->delete();
			return $affectedRows;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
}