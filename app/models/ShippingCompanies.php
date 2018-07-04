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
class ShippingCompanies extends CustomEloquent
{
    protected $table = "shipping_companies";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "company_name", "category", "is_custom_fee_available", "is_standard_fee_available", "is_custom_delivery_available", "default_delivery_days", "display");
}