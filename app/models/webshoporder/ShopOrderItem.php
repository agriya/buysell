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
class ShopOrderItem extends Eloquent
{
    protected $table = "shop_order_item";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "order_id", "item_id", "buyer_id", "item_owner_id", "item_amount", "item_qty", "shipping_company", "shipping_fee", "total_tax_amount", "tax_ids", "tax_amounts", "services_amount", "total_amount", "discount_amount_rate", "service_ids", "item_type", "site_commission", "seller_amount", "date_added");
}