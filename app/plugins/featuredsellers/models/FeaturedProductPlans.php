<?php namespace App\Plugins\FeaturedSellers\Models;
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
class FeaturedSellersPlans extends \Eloquent
{
    protected $table = "featured_sellers_plans";
    public $timestamps = false;
    protected $primarykey = 'featured_seller_plan_id';
    protected $table_fields = array("featured_seller_plan_id", "featured_days", "featured_price", "status", "created_at", "updated_at");
}