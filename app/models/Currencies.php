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
class Currencies extends Eloquent
{
    protected $table = "currencies";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "currency_code", "currency_symbol", "currency_name", "exchange_rate", "exchange_rate_static", "paypal_supported", "display_currency", "status");
}