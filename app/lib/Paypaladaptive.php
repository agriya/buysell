<?php

class Paypaladaptive {

	public static function greeting() {
		return "What up dawg Paypaladaptive";
	}

	public static function initialize()
	{
		return new Paypal();
	}

	public static function initializePaypalProcess()
	{
		return new PaypalProcess();
	}

	public static function initializePaypalTransaction()
	{
		return new PaypalPaymentTransaction();
	}
}