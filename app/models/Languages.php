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
class Languages extends CustomEloquent
{
    protected $table = "languages";
    public $timestamps = false;
    protected $primarykey = "languages_id";
    protected $table_fields = array("languages_id", "name", "code", "sort_order", "status", "is_published", "is_translated");
}
