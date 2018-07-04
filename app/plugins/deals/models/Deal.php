<?php namespace App\Plugins\Deals\Models;
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
class Deal extends \Eloquent
{
    protected $table = "deal";
    public $timestamps = false;
    protected $primarykey = 'deal_id';

    protected $table_fields = array("deal_id", "user_id", "deal_title", "url_slug", "deal_short_description", "deal_description", "meta_title", "meta_keyword", "meta_description", "img_name", "img_ext", "img_width", "img_height", "l_width", "l_height", "t_width", "t_height", "server_url", "discount_percentage", "date_deal_from", "date_deal_to", "applicable_for", "tipping_qty_for_deal", "deal_status", "date_added", "listing_fee_paid", "deal_tipping_status", "tipping_notified");
}