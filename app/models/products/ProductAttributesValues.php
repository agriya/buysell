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
class ProductAttributesValues extends Eloquent
{
    protected $table = "product_attributes_values";
    public $timestamps = false;
    protected $primarykey = '';
    protected $table_fields = array("product_id", "attribute_id", "attribute_value");
}