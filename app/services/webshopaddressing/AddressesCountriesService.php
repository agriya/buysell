<?php

class MissingAddressingCountryParamsExecption extends Exception {}

class AddressesCountriesService
{

	public function getCountries($options = array(), $return_type = 'all')
	{
		try
		{
			$country = Config::get('webshopaddressing.countries_table');
			$country_name = $country['country_name'];
			$country_id = $country['id'];

			//Throw exceptions if inputs are wrong
			if(is_array($options) && !empty($options) && (!isset($options['id']) && !isset($options['country_name'])))
				throw new MissingAddressingCountryParamsExecption(trans('addressing.country_options_empty'));

			if(is_null($return_type)){$return_type = 'all';}
			if(!in_array($return_type, array('all','list', 'first')))
				throw new MissingAddressingCountryParamsExecption(trans('addressing.country_return_type'));


			//Create model object for address
			$address = AddressesCountries::orderby('id','asc');



			//Conditions based on input
			if(isset($options['id']) && $options['id'] > 0)
				$address->where('id','=',$options['id']);
			if(isset($options['country_name']) && $options['country_name'] !='')
				$address->where($country_name,'like',$options['country_name']);

			//Check the return type and get hte list
			if($return_type == 'list')
				$addresslist = $address->lists($country_name,$country_id);
			elseif($return_type == 'first')
				$addresslist = $address->first();
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

}