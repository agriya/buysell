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
class UsersTopPicks extends CustomEloquent
{
    protected $table = "users_top_picks";
    public $timestamps = false;
    protected $primarykey = 'top_pick_id';
    protected $table_fields = array("top_pick_id", "user_id", "date_added");
    public function addNew($data_arr)
	{
		$arr = $this->filterTableFields($data_arr);
		$id = $this->insertGetId($arr);
    	return $id;
	}

}