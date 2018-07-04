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
class ProductGroupPrice extends Eloquent
{
    protected $table = "product_group_price";
    public $timestamps = false;
    protected $primarykey = 'group_price_id';
    protected $table_fields = array("group_price_id", "product_id", "group_id", "min_qty_start", "min_qty_end", "min_qty_price", "min_qty_discount", "max_qty_start", "max_qty_end", "max_qty_price", "max_qty_discount", "date_added", "date_updated");
}