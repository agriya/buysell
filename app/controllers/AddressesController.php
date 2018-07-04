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
class AddressesController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }
	public function getIndex()
	{
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$address_obj = Webshopaddressing::Addressing();
		$shipping_addresses = $address_obj->getAddresses(array('user_id' => $logged_user_id), 'paginate');
		$get_common_meta_values = Cutil::getCommonMetaValues('my-addresses');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('myAddressesList', compact('shipping_addresses'));
	}
	public function getAddAddress($addr_id = 0)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$action = 'add';
		if($addr_id <=0)
			$address_details = array();
		else
		{
			$address_obj = Webshopaddressing::Addressing();
			$address_details = $address_obj->getAddresses(array('id' => $addr_id), 'first');
			if(!$address_details || count($address_details) <= 0)
				return Redirect::action('AddressesController@getIndex')->with('error_message', Lang::get('myAddresses.address_invalid'));
			else
			{
				if($address_details->user_id != $logged_user_id)
					return Redirect::action('AddressesController@getIndex')->with('error_message', Lang::get('myAddresses.address_invalid_authentication'));
			}
			if($address_details->is_primary == 'Yes')
				$address_details->make_as_default = 1;
			$action = 'edit';
		}
		$countries = array('' => trans('common.select_a_country'));
		$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc');
		$countries_arr = array_except($countries_arr, array('38'));//Remove china
		$countries_list = $countries+$countries_arr;
		if($addr_id <=0)
		{
			$get_common_meta_values = Cutil::getCommonMetaValues('add-addresses');
			if($get_common_meta_values)
			{
				$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
				$this->header->setMetaDescription($get_common_meta_values['meta_description']);
				$this->header->setMetaTitle($get_common_meta_values['meta_title']);
			}
		}else{
			$get_common_meta_values = Cutil::getCommonMetaValues('edit-addresses');
			if($get_common_meta_values)
			{
				$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
				$this->header->setMetaDescription($get_common_meta_values['meta_description']);
				$this->header->setMetaTitle($get_common_meta_values['meta_title']);
			}
		}
		return View::make('addAddress', compact('address_details','logged_user_id','countries_list','action', 'addr_id'));
	}

	public function postAddAddress($addr_id = 0)
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$input = Input::all();

		$address_obj = Webshopaddressing::Addressing();
		//if this is edit check whether the user have access to edit
		if($addr_id > 0)
		{
			$address_details = $address_obj->getAddresses(array('id' => $addr_id), 'first');
			if(!$address_details || count($address_details) <= 0)
				return Redirect::action('AddressesController@getIndex')->with('error_message', Lang::get('myAddresses.address_invalid'));
			else
			{
				if($address_details->user_id != $logged_user_id)
					return Redirect::action('AddressesController@getIndex')->with('error_message', Lang::get('myAddresses.address_invalid_authentication'));
			}
		}



		if(isset($input['make_as_default']) && $input['make_as_default']=='1')
			$input['is_primary'] = 'Yes';
		else
			$input['is_primary'] = 'No';
		$input['address_type'] = 'shipping';

		$rules = $address_obj->validationrules();
		unset($rules['user_id']);
		unset($rules['country']);
		$messages=array('required' => 'Required');
		$v = Validator::make($input, $rules, $messages);
		if($v->passes())
		{
			if($addr_id<=0)
			{

				$address_type = isset($input['address_type'])?$input['address_type']:'shipping';
				$address_type = (in_array($address_type, array('shipping','billing')))?$address_type:'shipping';
				$logged_user_id = BasicCUtil::getLoggedUserId();
				$country_name = Webshopaddressing::AddressingCountry()->getCountries(array('id'=>$input['country_id']), 'first');
				if($country_name && count($country_name) >0 )
					$country_name = $country_name->country;
				else
					$country_name = ' ';
				$inputs = array(
					'user_id' 			=> $logged_user_id,
					'address_line1' 	=> $input['address_line1'],
					'address_line2' 	=> $input['address_line2'],
					'street'			=> $input['street'],
					'city' 				=> $input['city'],
					'state'				=> $input['state'],
					'zip_code'			=> $input['zip_code'],
					'phone_no'			=> $input['phone_no'],
					'country'			=> $country_name,
					'country_id'		=> $input['country_id'],
					'address_type'		=> $address_type,
					'is_primary'		=> $input['is_primary'],
				);

				$addr_id = Webshopaddressing::Addressing()->addAddresses($inputs);
				$success_message = Lang::get('myAddresses.address_added_success');

			}
			else
			{
				if(isset($input['country_id']))
				{
					$country_name = Webshopaddressing::AddressingCountry()->getCountries(array('id'=>$input['country_id']), 'first');
					if($country_name && count($country_name) >0 )
						$input['country'] = $country_name->country;
					else
						$input['country'] = '';
				}
				Webshopaddressing::Addressing()->updateAddress($addr_id, $input);
				$success_message = Lang::get('myAddresses.address_updated_success');
			}
			if($input['is_primary'] == 'Yes')
			{
				Webshopaddressing::Addressing()->makeAsPrimaryAddress($addr_id, $logged_user_id);
			}
			else
			{
				$options = array('user_id' => $logged_user_id, 'is_primary' => 'Yes');
				$user_addresses = Webshopaddressing::Addressing()->getAddresses($options);
				if(!$user_addresses || count($user_addresses)<=0)
					Webshopaddressing::Addressing()->makeAsPrimaryAddress($addr_id, $logged_user_id);

			}
			return Redirect::action('AddressesController@getIndex')->with('success_message', $success_message);
		}
		else
		{
			return Redirect::action('AddressesController@getAddAddress')->withInput()->withErrors($v);
		}

	}

	public function postAddressAction()
	{
		$error_msg = Lang::get('myAddresses.address_invalid_action');
		$sucess_msg = '';
		if(Input::has('addr_action') && Input::has('addr_id'))
		{
			$addr_id = Input::get('addr_id');
			$addr_action = Input::get('addr_action');

			//Validate product id
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$address_obj = Webshopaddressing::Addressing();
			$shipping_addresses = $address_obj->getAddresses(array('id' => $addr_id), 'first');
			if($shipping_addresses && count($shipping_addresses) > 0)
			{
				//echo "<pre>";print_r($shipping_addresses);echo "</pre>";exit;
				if($shipping_addresses->user_id != $logged_user_id)
					$error_msg = Lang::get('myAddresses.address_invalid_authentication');
				else
				{
					switch($addr_action)
					{
						# Delete product
						case 'delete':
							$error_msg = '';
							# Product status is changed as Deleted
							$status = $address_obj->deleteAddress($addr_id);
							# Display delete success msg
							if($status)
							{
								$options = array('user_id' => $logged_user_id, 'is_primary' => 'Yes');
								$user_addresses = $address_obj->getAddresses($options);
								if(!$user_addresses || count($user_addresses)<=0)
								{
									$address_obj->makeFirstAddrAsPrimaryAddress($logged_user_id);
								}
								$sucess_msg = Lang::get('myAddresses.address_success_deleted');
							}
							else
							{
								$error_msg = Lang::get('myAddresses.address_error_on_action');
							}
							break;
						case 'make_primary':
							Webshopaddressing::Addressing()->makeAsPrimaryAddress($addr_id, $logged_user_id);
							$sucess_msg = Lang::get('myAddresses.address_primary_success');
							break;
					}
				}
			}
		}
		if($sucess_msg != '')
		{
			return Redirect::action('AddressesController@getIndex')->with('success_message', $sucess_msg);
		}
		return Redirect::action('AddressesController@getIndex')->with('error_message', $error_msg);
	}
}