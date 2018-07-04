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
class CurrencyExchangeRate extends Eloquent
{
    protected $table = "currency_exchange_rate";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "country", "country_code", "iso2_country_code", "currency_code", "currency_symbol", "currency_name", "exchange_rate", "status", "paypal_supported", "display_currency", "country_name_chinese", "china_post_group", "geo_location_id", "zone_id", "capital", "zip_code");
}