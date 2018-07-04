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
class ProductStocks extends Eloquent
{
    protected $table = "product_stocks";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "product_id", "stock_country_id", "quantity", "serial_numbers", "date_updated");
}