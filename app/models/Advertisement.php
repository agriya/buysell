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
class Advertisement extends CustomEloquent
{
    protected $table = "advertisement";
    public $timestamps = false;
    protected $primarykey = 'add_id';
    protected $table_fields = array("add_id", "user_id", "post_from", "block", "about", "source", "start_date", "end_date", "allowed_impressions",
									"completed_impressions", "status", "date_added");

	public function addNew($data_arr)
	{
		$arr = $this->filterTableFields($data_arr);
		$id = $this->insertGetId($arr);
    	return $id;
	}

}