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

class DealFeaturedRequest extends \Eloquent
{
    protected $table = "deal_featured_request";
    public $timestamps = false;
    protected $primarykey = 'request_id';
    protected $table_fields = array("request_id", "deal_id", "user_id", "date_featured_from", "date_featured_to", "deal_featured_days", "fee_paid_status", "date_added", "date_approved_on", "request_status", "admin_comment");
}