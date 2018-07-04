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
class ShippingTemplateFeeCustomCountries extends CustomEloquent
{
    protected $table = "shipping_template_fee_custom_countries";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "template_id", "company_id", "template_company_id", "shipping_template_fee_custom_id", "country_id");
}