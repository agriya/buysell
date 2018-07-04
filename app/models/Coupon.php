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
class Coupon extends CustomEloquent
{
    protected $table = "coupons";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "coupon_code", "from_date", "to_date", "price_restriction", "price_from", "price_to", "offer_type", "offer_amount", "status");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}

	public function getTableFields()
	{
		return $this->table_fields;
	}
}
