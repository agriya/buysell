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
class BillingAddress extends Eloquent
{
	protected $table = "billing_address";
	public $timestamps = false;
	protected $primarykey = 'id';
	protected $table_fields = array("id","order_id","address_id", "billing_address_id", "user_id", "shipping_address", "billing_address");
	protected $fillable = array("id","order_id","address_id", "billing_address_id", "user_id", "shipping_address", "billing_address");
}