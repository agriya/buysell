<?php

//@added by mohamed_158at11
class ShipmentService
{
	public static function getCountriesTable()
	{
		return Config::get('webshopshipments.countries_table_name');
	}
}