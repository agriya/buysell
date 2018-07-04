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
class ShippingTemplateFeeCustom extends CustomEloquent
{
    protected $table = "shipping_template_fee_custom";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "template_id", "company_id", "template_company_id", "country_selected_type", "shipping_setting", "fee_type", "discount");
}