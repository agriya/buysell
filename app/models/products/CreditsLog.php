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
class CreditsLog extends Eloquent
{
    protected $table = "credits_log";
    public $timestamps = false;
    protected $primarykey = 'credit_id';
    protected $table_fields = array("credit_id", "currency", "amount", "credited_by", "credited_to", "admin_notes", "user_notes", "paid", "date_paid", "generate_invoice", "date_added", "date_updated");
}