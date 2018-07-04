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
class ProductTaxations extends Eloquent
{
	protected $table = "product_taxations";
	public $timestamps = false;
	protected $primarykey = 'id';
	protected $table_fields = array("id", "taxation_id", "product_id", "user_id", "tax_fee", "fee_type");
	protected $fillable = array("id", "taxation_id", "product_id", "user_id", "tax_fee", "fee_type");

	public function taxations()
    {
        return $this->belongsTo('Taxations','taxation_id','id');
    }

}