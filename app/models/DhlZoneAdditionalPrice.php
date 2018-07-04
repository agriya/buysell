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
class DhlZoneAdditionalPrice extends CustomEloquent
{
    protected $table = "dhl_zone_additional_price";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "service_type", "goods_weight_from", "goods_weight_to", "zone1", "zone2", "zone3", "zone4", "zone5", "zone6", "zone7", "zone8", "zone9", "zone10");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}