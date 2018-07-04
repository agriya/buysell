<?php namespace App\Plugins\Variations\Models;
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
class Variation extends \Eloquent
{
    protected $table = "variation";
    public $timestamps = false;
    protected $primarykey = 'variation_id';
    protected $table_fields = array("variation_id", "name", "user_id", "date_added");
}