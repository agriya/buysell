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
namespace App\Plugins\Deals\Models;

class DealItem extends \Eloquent
{
    protected $table = "deal_item";
    public $timestamps = false;
    protected $primarykey = 'deal_item_id';
    protected $table_fields = array("deal_item_id", "deal_id", "item_id");
}