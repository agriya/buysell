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
class OrderReceivers extends Eloquent
{
    protected $table = "order_receivers";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "common_invoice_id", "buyer_id", "seller_id", "amount", "currency", "is_admin", "receiver_paypal_email", "status", "txn_id", "pay_key", "is_refunded", "refunded_amount", "payment_type");
}