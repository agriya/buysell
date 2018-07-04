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

class AddressingController extends BaseController
{

	//Taxations
	public function getIndex($user_id = null)
	{
		try
		{

			$country_id = Webshopaddressing::AddressingCountry()->getCountries(array(), 'list'); 
			//echo "here";exit;
			echo "<br>country list from controller <pre>";print_r($country_id);echo "</pre></br>";
			$user_id = (!is_null($user_id))?$user_id:1;
			$taxationslist = Webshopaddressing::Addressing()->getAddresses(array('user_id'=>$user_id), 'all');
			if($taxationslist)
			{
				echo "<pre>";print_r($taxationslist);echo "</pre>";
			}
			else
				echo "No Taxations Found for the specified id";
		}
		catch(Exception $e)
		{
			echo "some problem: ".$e->getMessage();
		}

	}
	public function getAddAddress()
	{
		
		try
		{
			$country_id = Webshopaddressing::AddressingCountry()->getCountries(array('country_name'=>'India'), 'first'); 
			if(count($country_id) >0 )
				$country_id = $country_id->id;
			else
				$country_id = 0;

			$inputs = array(
				'user_id' 	=> 1,
				'address_line1' 	=> 'no 7/8, East madhava perumal street',
				'address_line2' 	=> 'Alandur',
				'street'			=> 'Guindy',
				'city' 				=> 'Chennai',
				'state'				=> 'Tamilnadu',
				'country'			=> 'India',
				'country_id'		=> $country_id,
				'address_type'		=> 'billing'
			);
			//echo "<pre>";print_r($inputs);echo "</pre>";
			$addressid = Webshopaddressing::Addressing()->addAddresses($inputs);
			if($addressid)
			{
				echo "<br>Address id: ".$addressid;
			}
			else
				echo "Taxation not added";
		}
		catch(Exception $e)
		{
			echo "some problem: ".$e->getMessage();
		}
	}

	public function getUpdateAddress($addressid = 0)
	{
		try
		{
			$country_id = Webshopaddressing::AddressingCountry()->getCountries(array('country_name'=>'India'), 'first'); 
			if(count($country_id) >0 )
				$country_id = $country_id->id;
			else
				$country_id = 0;

			if($addressid > 0)
			{
				$inputs = array(
					'address_line1' 	=> 'no 7/8, East madhava perumal street',
					'address_line2' 	=> 'alandur',
					'street'			=> 'Guindy',
					'city' 				=> 'Chennai',
					'state'				=> 'Tamilnadu',
					'country'			=> 'India',
					'country_id'		=> $country_id
				);

				$taxatonid = Webshopaddressing::Addressing()->updateAddress($addressid, $inputs);
				if($taxatonid)
				{
					echo "<br>address updated successfully : ".$taxatonid;
				}
				else
					echo "Address not updated";
			}
			else
				echo "pass the address id to update";
		}
		catch(Exception $e)
		{
			echo "some problem: ".$e->getMessage();
		}
	}

	public function getDeleteAddress($addressid = 0)
	{
		try
		{
			if($addressid > 0)
			{
				$address_id = Webshopaddressing::Addressing()->deleteAddress($addressid);
				if($address_id)
				{
					echo "<br>Address deleted successfully : ".$address_id;
				}
				else
					echo "Address not deleted";
			}
			else
				echo "pass the address id to delete";
		}
		catch(Exception $e)
		{
			echo "some problem: ".$e->getMessage();
		}
	}

	public function getAddBillingAddress()
	{
		
			$inputs = array(
				'order_id' 			=> 2,
				'address_id' 		=> 1,
				'billing_address_id' => 6,
			);
			//echo "<pre>";print_r($inputs);echo "</pre>";
			$billingaddressid = Webshopaddressing::BillingAddress()->addBillingAddress($inputs);
			if($billingaddressid)
			{
				echo "<br>Biling Address id: ".$billingaddressid;
			}
			else
				echo "Biling Address not added";

		

	}

}