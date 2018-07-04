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
class PaypalAdaptivePaymentsTransaction extends Eloquent
{
    protected $table = "paypal_adaptive_payments_transaction";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "pay_key", "tracking_id", "currency_code", "buyer_email", "receiver_details", "ipn_post_str", "payment_details_str", "error_id", "status", "buyer_trans_id", "date_added");
}