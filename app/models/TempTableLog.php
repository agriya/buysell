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
class TempTableLog extends CustomEloquent
{
    protected $table = "temp_table_log";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "temp_table_name", "date_used");
}