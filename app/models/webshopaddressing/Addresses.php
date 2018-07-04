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
class Addresses extends Eloquent
{
    protected $table = "addresses";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "address_line1", "address_line2", "street", "city", "state", "country", "phone_no", "zip_code", "country_id", "address_type", "is_primary");
    protected $fillable = array("id", "user_id", "address_line1", "address_line2", "street", "city", "state", "country", "phone_no", "zip_code", "country_id", "address_type", "is_primary");

}