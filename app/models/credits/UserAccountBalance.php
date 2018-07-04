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
class UserAccountBalance extends Eloquent
{
    protected $table = "user_account_balance";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "currency", "amount", "cleared_amount");
}