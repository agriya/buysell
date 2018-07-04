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
class ProductAttributeOptions extends Eloquent
{
    protected $table = "product_attribute_options";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "attribute_id", "option_label", "option_value", "is_default_option");
}