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
class Countries extends Eloquent
{
	protected $table = "countries";
	public $timestamps = false;
	protected $primarykey = 'id';
	protected $table_fields = array("id", "country_slug", "country_name");

	public function shippingfees()
	{
		return $this->hasMany('ShippingFees');
	}
}