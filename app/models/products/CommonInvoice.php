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
class CommonInvoice extends Eloquent
{
    protected $table = "common_invoice";
    public $timestamps = false;
    protected $primarykey = 'common_invoice_id';
    protected $table_fields = array("common_invoice_id", "user_id", "reference_type", "reference_id", "currency", "amount", "is_credit_payment", "paypal_amount", "pay_key", "tracking_id", "status", "date_paid", "date_added");
}