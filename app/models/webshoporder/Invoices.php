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
class Invoices extends Eloquent
{
    protected $table = "invoices";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "order_id", "buyer_id", "item_id", "item_owner_id", "order_item_id", "order_receiver_id", "invoice_status", "is_refund_requested", "refund_reason", "is_refund_approved_by_admin", "refund_response_by_admin", "refund_response_to_user_by_admin", "refunded_amount_by_admin", "is_refund_approved_by_seller", "refund_response_by_seller", "refund_amount_by_seller", "refund_paypal_amount_by_seller", "refund_responded_by_admin_id", "refund_status", "txn_id", "pay_key");
}