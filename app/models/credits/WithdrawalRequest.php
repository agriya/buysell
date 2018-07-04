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
class WithdrawalRequest extends Eloquent
{
    protected $table = "withdrawal_request";
    public $timestamps = false;
    protected $primarykey = 'withdraw_id';
    protected $table_fields = array("withdraw_id", "user_id", "currency", "amount", "payment_type_del", "available_balance"
										, "fee", "payment_type", "pay_to_user_account", "paid_notes", "admin_notes"
										, "set_as_paid_by", "site_transaction_id", "date_paid", "date_cancelled"
										, "cancelled_reason", "cancelled_by", "added_date", "status");
}