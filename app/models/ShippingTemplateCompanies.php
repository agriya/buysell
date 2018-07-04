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
class ShippingTemplateCompanies extends CustomEloquent
{
    protected $table = "shipping_template_companies";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "template_id", "company_id", "fee_type", "fee_discount", "delivery_type", "days");
}