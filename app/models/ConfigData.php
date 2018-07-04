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
class ConfigData extends CustomEloquent
{
    protected $table = "config_data";
    public $timestamps = false;
    protected $primarykey = 'config_data_id';
    protected $table_fields = array("config_data_id", "config_var", "config_value", "config_type", "config_category", "config_section", "editable", "edit_order", "description");
}