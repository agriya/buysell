<?php

class Webshoptaxation {

	public static function greeting(){
		return "What up dawg";
	}
	public static function Taxations(){
		return new TaxationsService();
	}
	public static function ProductTaxations(){
		return new ProductTaxationsService();
	}
}