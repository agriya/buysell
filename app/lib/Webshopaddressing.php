<?php

class Webshopaddressing {

	public static function greeting(){
		return "What up dawg";
	}


	public static function Addressing()
	{

		return new AddressingService();
	}

	public static function AddressingCountry()
	{

		return new AddressesCountriesService();
	}

	public static function BillingAddress()
	{

		return new BillingAddressService();
	}
}