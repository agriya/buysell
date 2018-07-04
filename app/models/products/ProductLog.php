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
class ProductLog extends Eloquent
{
    protected $table = "product_log";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "product_id", "date_added", "added_by", "user_id", "notes");
}