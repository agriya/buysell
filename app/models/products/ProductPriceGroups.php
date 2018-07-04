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
class ProductPriceGroups extends Eloquent
{
    protected $table = "product_price_groups";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("price_group_id", "product_id", "group_id", "range_start", "range_end", "currency", "price", "price_usd", "discount_percentage", "discount", "discount_usd", "added_on");
}