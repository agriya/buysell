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
class ShippingFees extends Eloquent
{
    protected $table = "shipping_fees";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "country_id", "shippng_fee", "foreign_id");

    public function countries()
    {
        return $this->belongsTo('Countries','country_id','id');
    }
}