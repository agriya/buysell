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
class Taxations extends Eloquent
{
	protected $softDelete = true;
    protected $table = "taxations";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "tax_name", "tax_slug", "tax_description", "tax_fee", "fee_type", "deleted_at");
    protected $fillable = array("id", "user_id", "tax_name", "tax_slug", "tax_description", "tax_fee", "fee_type", "deleted_at");

    public function producttaxations()
    {
        return $this->hasMany('ProductTaxations','taxation_id','id');
    }

}