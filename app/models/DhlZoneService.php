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
class DhlZoneService extends CustomEloquent
{
    protected $table = "dhl_zone_service";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("zone_service_id", "country_id", "dhl_pak_zone_id", "dhl_pak_express_9", "dhl_pak_express_12", "dhl_pak_express_worldwide", "dhl_pak_express_easy",
	"dhl_china_zone_id", "dhl_china_express_9", "dhl_china_express_12", "dhl_china_express_worldwide");


    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}