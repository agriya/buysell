<?php

class Webshoporder {

	public static function initialize()
	{
		return new Webshoporders();
	}

	public static function initializeInvoice()
	{
		return new Invoice();
	}
}
?>