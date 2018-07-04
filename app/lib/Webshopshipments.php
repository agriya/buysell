<?php

class MissingShippingParamsException extends Exception {}

class Webshopshipments
{

	public static function greeting()
	{
		return "Whats up man";
	}
	public static function getCountriesList($return = 'all', $order_by = 'country_name', $list = 'asc', $include_row = false)
	{
		try {
			$country = Config::get('webshopshipments.countries_table_details');
			$country_table = $country['table_name'];

			$list = !in_array($list,array('asc','desc'))?'asc':$list;
			if($order_by == 'id')
				$order_by = $country['id'];
			else
				$order_by  = $country['country_name'];


			if(!in_array($return, array('list','all')))
				throw new MissingShippingParamsException(trans('webshopshipments.param_list_or_all'));

			$default_arr = array();
			if($return == 'list')
			{
				if($include_row)
					$default_arr = array('-1' => trans('webshopshipments.rest_of_the_world'));
				$countries = DB::table($country_table)->orderby($order_by, $list)->lists($country['country_name'],$country['id']);
				$countries = $default_arr+$countries;
			}
			else
			{
				if($include_row)
					$default_arr = array($country['id'] => '-1', $country['country_name'] => trans('webshopshipments.rest_of_the_world'));
				$countries = DB::table($country_table)->orderby($order_by, $list)->get();
				if(count($countries) > 0)
					array_splice($countries,0,0,array('0'=>$default_arr));

			}
			if(count($countries) > 0)
				return $countries;
			else
				return array();
		}
		catch(MissingShippingParamsException $e)
		{
			throw new MissingShippingParamsException($e->getMessage());
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	/*public static function addPackageDetails($inputs = array())
	{
		try
		{
		//print_r($inputs);exit;
			$id = $inputs['id'];
			$product_id = $inputs['id'];
			$weight = $inputs['weight'];
			$length = $inputs['length'];
			$width = $inputs['width'];
			$height = $inputs['height'];
			$custom = isset($inputs['custom'])?$inputs['custom']:'NO';
			$first_qty = isset($inputs['first_qty'])?$inputs['first_qty']:'';
			$additional_qty = isset($inputs['additional_qty'])?$inputs['additional_qty']:'';
			$additional_weight = isset($inputs['additional_weight'])?$inputs['additional_weight']:'';
			$validator = Validator::make
			(
	  			array('weight' => $weight,'length' =>$length,'width' => $width,'height' => $height,'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight),
	    		array('weight' => 'required|numeric|between:1,500','length' => 'required|numeric|between:1,700','width' => 'required|numeric|between:1,700','height' => 'required|numeric|between:1,700','first_qty'=> 'integer','additional_qty' => 'integer','additional_weight' => 'numeric')
			);
			if($validator->passes())
			{
				$product = DB::table('product_package_details')->where('product_id',$product_id)->get();
				if(count($product) == 0)
				{
					//echo $size_add.$size_add1.$size_add2;exit;
					$package_details_id = DB::table('product_package_details')->insertGetId(
					    array('weight' => $weight, 'length' => $length, 'width' => $width, 'height' => $height, 'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight, 'custom' => $custom, 'product_id' => $id)
					);
				}
				else
				{
					$package_details_id = DB::table('product_package_details')
										->where('product_id', '=', $product_id)
						            	->update(array('weight' => $weight, 'length' => $length, 'width' => $width, 'height' => $height, 'first_qty' => $first_qty, 'additional_qty' => $additional_qty, 'additional_weight' => $additional_weight, 'custom' => $custom, 'product_id' => $id)
					);
				}
					return $package_details_id;
			}
			else
			{
				throw new MissingShippingParamsException($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}*/
	public static function addShipments($inputs = array())
	{
		try
		{
			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];
			$shipping_fee_shipping_fee = $shipping_fee_table['shipping_fee'];



			if(!is_array($inputs))
			{
				throw new MissingShippingParamsException(trans('webshopshipments.input_not_an_array'));
			}
			if(empty($inputs))
			{
				throw new MissingShippingParamsException(trans('webshopshipments.input_array_empty'));
			}
			if($inputs['shipping_fee'] == -0)
			{
				$inputs['shipping_fee'] = 0;
			}
			$inputs['shipping_fee'] = round($inputs['shipping_fee'], 2);
			/*if($inputs['shipping_fee'] <= 0)
			{
				throw new MissingShippingParamsException(trans('webshopshipments.shipping_fee_min'));
			}*/


			$rules = array($shipping_fee_foreign_id =>'required|numeric|min:0', $shipping_fee_country_id => 'required|numeric|Unique:shipping_fees,'.$shipping_fee_country_id.',NULL,id,'.$shipping_fee_foreign_id.','.$inputs['foreign_id'], $shipping_fee_shipping_fee => 'required|numeric|min:0');
			$messages = array(
				$shipping_fee_foreign_id.'.required' => trans('webshopshipments.product_required'),
				$shipping_fee_foreign_id.'.numeric' => trans('webshopshipments.product_numeric'),
				$shipping_fee_country_id.'.required' => trans('webshopshipments.country_required'),
				$shipping_fee_country_id.'.unique' => trans('webshopshipments.shipping_already_added'),
				$shipping_fee_country_id.'.numeric' => trans('webshopshipments.shipping_already_added'),
				$shipping_fee_shipping_fee.'.required' => trans('webshopshipments.shipping_fee_required'),
				$shipping_fee_shipping_fee.'.min' => trans('webshopshipments.shipping_fee_min'),
			);

			$validator = Validator::make($inputs,$rules,$messages);

			if($validator->passes())
			{
				$shipping_fee_id = DB::table($shipping_fee_table_name)->insertGetId(
				    array($shipping_fee_country_id => $inputs['country_id'], $shipping_fee_foreign_id => $inputs['foreign_id'], $shipping_fee_shipping_fee => $inputs['shipping_fee'])
				);
				return $shipping_fee_id;
			}
			else
			{
				throw new MissingShippingParamsException($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	public static function getItemShippingList($foreign_id = null)
	{
		try {
			if(is_null($foreign_id) || $foreign_id <=0)
			{
				throw new MissingShippingParamsException(trans('webshopshipments.product_id_invalid'));
			}

			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

			$country = Config::get('webshopshipments.countries_table_details');
			$country_table = $country['table_name'];

			//$shipping_details = ShippingFees::with('countries')->where('foreign_id', '=', $shipping_fee_foreign_id)->get();

			$shipping_details =	DB::table($shipping_fee_table_name)->where($shipping_fee_foreign_id, '=', $foreign_id)->get();
			if(count($shipping_details) > 0)
			{
				foreach($shipping_details as $key => $shipping_det)
				{
					$shipping_country = new stdClass();
					if($shipping_det->country_id >0)
						$shipping_country =	DB::table($country_table)->where($country['id'], '=', $shipping_det->country_id)->first();
					else
					{
						$shipping_country->$country['id'] = '-1';
						$shipping_country->$country['country_name'] =  trans('webshopshipments.rest_of_the_world');
					}
					$shipping_details[$key]->countries = $shipping_country;
				}
			}
			if(count($shipping_details) > 0)
			{
				return $shipping_details;
			}
			else
				return array();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	public static $_product_package_details;
	public static function getEditPackageDetails($id = null)
	{
		try {
			if(is_null($id) || $id <=0)
			{
				throw new MissingShippingParamsException(trans('webshopshipments.product_id_invalid'));
			}
			if (isset(Webshopshipments::$_product_package_details[$id])) {
				$product_package_details = Webshopshipments::$_product_package_details[$id];
			} else {
				$product_package_details =	DB::table('product_package_details')->where('product_id', '=', $id)->first();
				Webshopshipments::$_product_package_details[$id] = $product_package_details;
			}

			if(count($product_package_details) > 0)
				return $product_package_details;
			else
			 	return array();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	public static function getItemShippingCountries($foreign_id = null)
	{
		try {
			if(is_null($foreign_id) || $foreign_id <=0)
			{
				throw new MissingShippingParamsException(trans('webshopshipments.product_id_invalid'));
			}

			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

			$country = Config::get('webshopshipments.countries_table_details');
			$country_table = $country['table_name'];

			$ro_world = array();
			$is_ro_world_available = DB::table($shipping_fee_table_name)->where($shipping_fee_foreign_id, '=', $foreign_id)->where($shipping_fee_country_id,'=','-1')->count();
			if($is_ro_world_available > 0)
				$ro_world = array('-1' => trans('webshopshipments.rest_of_the_world'));

			$shipping_countries =	DB::table($country_table)->leftjoin($shipping_fee_table_name, $shipping_fee_table_name.'.'.$shipping_fee_country_id, '=', $country_table.'.'.$country['id'])->where($shipping_fee_table_name.'.'.$shipping_fee_foreign_id, '=', $foreign_id)->lists($country['country_name'],$shipping_fee_country_id);
			if(count($shipping_countries) > 0)
			{
				return $ro_world+$shipping_countries;
			}
			elseif($is_ro_world_available > 0)
			{
				return $ro_world;
			}
			else
				return array();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	public static function updateShippingFee($inputs = array(), $primary_id = null)
	{
		try
		{
			if(!is_array($inputs))
			{
				throw new MissingShippingParamsException(trans('webshopshipments.input_not_an_array'));
			}
			if(empty($inputs))
			{
				throw new MissingShippingParamsException(trans('webshopshipments.input_array_empty'));
			}


			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_id = $shipping_fee_table['id'];
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];
			$shipping_fee_shipping_fee = $shipping_fee_table['shipping_fee'];


			$rules = array($shipping_fee_shipping_fee => 'required|numeric|min:0');
			$messages = array($shipping_fee_shipping_fee.'.required' => trans('webshopshipments.shipping_fee_required'),
						$shipping_fee_shipping_fee.'.min' => trans('webshopshipments.shipping_fee_min'));
			if(is_null($primary_id) || $primary_id <0)
			{
				$rules[$shipping_fee_foreign_id] = 'required';
				$rules[$shipping_fee_country_id] = 'required';

				$messages[$shipping_fee_foreign_id.'.required'] = trans('webshopshipments.product_required');
				$messages[$shipping_fee_country_id.'.required'] = trans('webshopshipments.country_required');
			}

			$validator = Validator::make($inputs,$rules,$messages);

			if($validator->passes())
			{
				if($primary_id > 0)
				{
					$affectedRows = DB::table($shipping_fee_table_name)
						            ->where($shipping_fee_id, '=', $primary_id)
						            ->update(array($shipping_fee_shipping_fee => $inputs['shipping_fee']));

				}
				else
				{

					$affectedRows = DB::table($shipping_fee_table_name)
									->where($shipping_fee_country_id, '=', $inputs['country_id'])
						            ->where($shipping_fee_foreign_id, '=', $inputs['foreign_id'])
						            ->update(array($shipping_fee_shipping_fee => $inputs['shipping_fee']));
				}
				return $affectedRows;

			}
			else
			{
				throw new MissingShippingParamsException($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	public static function IsShippingAvailableForProduct($foreign_id = 0)
	{
		if(is_null($foreign_id) || $foreign_id <=0 || $foreign_id == '')
			return false;

		$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
		$shipping_fee_table_name = $shipping_fee_table['table_name'];
		$shipping_fee_country_id = $shipping_fee_table['country_id'];
		$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

		$shipping_details =	DB::table($shipping_fee_table_name)->where($shipping_fee_foreign_id, '=', $foreign_id)->count();
		return $shipping_details;


	}
	public static function getShippingDetails($inputs = array(), $primary_id = null, $return_roworld_record = false)
	{
		try
		{
			if(is_null($primary_id))
			{

				if(!is_array($inputs))
				{
					throw new MissingShippingParamsException(trans('webshopshipments.input_not_an_array'));
				}
				if(empty($inputs))
				{
					throw new MissingShippingParamsException(trans('webshopshipments.input_array_empty'));
				}
			}

			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

			$country = Config::get('webshopshipments.countries_table_details');
			$country_table = $country['table_name'];


			if(!is_null($primary_id) && $primary_id >0)
			{
				//$shipping_details = ShippingFees::with('countries')->where('id', '=', $primary_id)->get();
				$shipping_details =	DB::table($shipping_fee_table_name)->where($shipping_fee_id, '=', $primary_id)->get();
				if(count($shipping_details) > 0)
				{
					foreach($shipping_details as $key => $shipping_det)
					{
						$shipping_country =	DB::table($country_table)->where($country['id'], '=', $shipping_det->country_id)->first();
						$shipping_details[$key]->countries = $shipping_country;
					}
				}
			}
			else
			{
				$rules = array($shipping_fee_foreign_id => 'required', $shipping_fee_country_id => 'required');
				$messages = array($shipping_fee_foreign_id.'.required' => trans('webshopshipments.product_required'), $shipping_fee_country_id.'.required' => trans('webshopshipments.country_required'));

				$validator = Validator::make($inputs,$rules,$messages);

				if($validator->passes())
				{
					//$shipping_details = ShippingFees::with('countries')->where('country_id', '=', $inputs['country_id'])->where('foreign_id', '=', $inputs['foreign_id'])->get();

					$shipping_details =	DB::table($shipping_fee_table_name)
										->where($shipping_fee_country_id, '=', $inputs['country_id'])
										->where($shipping_fee_foreign_id, '=', $inputs['foreign_id'])->get();
					if(count($shipping_details) > 0)
					{
						foreach($shipping_details as $key => $shipping_det)
						{
							$shipping_country =	DB::table($country_table)->where($country['id'], '=', $shipping_det->country_id)->first();
							$shipping_details[$key]->countries = $shipping_country;
						}
					}
					else
					{
						if($return_roworld_record)
						{
							$roworlddet = self::checkAndGetROWorldFee($inputs['foreign_id']);

							if($roworlddet && count($roworlddet) > 0)
								$shipping_details = $roworlddet;
						}
					}
				}
				else
				{
					throw new MissingShippingParamsException($validator->messages()->first());
				}
			}
			if(count($shipping_details) > 0)
				return $shipping_details;
			else
				return array();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	public static function checkAndGetROWorldFee($foreign_id = null)
	{
		try
		{
			if(is_null($foreign_id))
			{
				return false;
			}

			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

			$country = Config::get('webshopshipments.countries_table_details');
			$country_table = $country['table_name'];


			$shipping_details =	DB::table($shipping_fee_table_name)
								->where($shipping_fee_country_id, '=', '-1')
								->where($shipping_fee_foreign_id, '=', $foreign_id)->get();
			if(count($shipping_details) > 0)
			{
				$shipping_country = new stdClass();
				$shipping_country->$country['id'] = '-1';
				$shipping_country->$country['country_name'] =  trans('webshopshipments.rest_of_the_world');

				$shipping_details[0]->countries = $shipping_country;
					return $shipping_details;
			}
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	public static function deleteShippingFee($inputs = array(), $primary_id = null)
	{
		try
		{
			if(is_null($primary_id))
			{
				if(!is_array($inputs))
				{
					throw new MissingShippingParamsException(trans('webshopshipments.input_not_an_array'));
				}
				if(empty($inputs))
				{
					throw new MissingShippingParamsException(trans('webshopshipments.input_array_empty'));
				}
			}
			$shipping_fee_table = Config::get('webshopshipments.shipping_fees_table_details');
			$shipping_fee_id		 = $shipping_fee_table['id'];
			$shipping_fee_table_name = $shipping_fee_table['table_name'];
			$shipping_fee_country_id = $shipping_fee_table['country_id'];
			$shipping_fee_foreign_id = $shipping_fee_table['foreign_id'];

			if(is_null($primary_id) || $primary_id <0)
			{
				$rules[$shipping_fee_foreign_id] = 'required';
				$rules[$shipping_fee_country_id] = 'required';

				$messages[$shipping_fee_foreign_id.'.required'] = trans('webshopshipments.product_required');
				$messages[$shipping_fee_country_id.'.required'] = trans('webshopshipments.country_required');
			}
			else
			{
				$rules['primary_id'] = 'required';
				$messages['primary_id.required'] = trans('webshopshipments.shipping_id_required');
				$inputs = array('primary_id' => $primary_id);
			}

			$validator = Validator::make($inputs,$rules,$messages);

			if($validator->passes())
			{
				if($primary_id > 0)
				{
					//$affectedRows = ShippingFees::where('id', '=', $primary_id)->delete();

					$affectedRows = DB::table($shipping_fee_table_name)
						            ->where($shipping_fee_id, '=', $primary_id)
						            ->delete();
				}
				else
				{
					//$affectedRows = ShippingFees::where('country_id', '=', $inputs['country_id'])->where('foreign_id', '=', $inputs['foreign_id'])->delete();

					$affectedRows = DB::table($shipping_fee_table_name)
									->where($shipping_fee_country_id, '=', $inputs['country_id'])
						            ->where($shipping_fee_foreign_id, '=', $inputs['foreign_id'])
						            ->delete();
				}
				return $affectedRows;

			}
			else
			{
				throw new MissingShippingParamsException($validator->messages()->first());
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
}