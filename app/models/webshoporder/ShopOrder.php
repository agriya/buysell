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
class ShopOrder extends Eloquent
{
    protected $table = "shop_order";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "buyer_id", "seller_id", "total_amount", "shipping_fee", "site_commission", "currency", "order_status", "pay_key", "tracking_id", "payment_status", "payment_response", "date_created", "date_updated");
}