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
class UsersFeatured extends CustomEloquent
{
    protected $table = "users_featured";
    public $timestamps = false;
    protected $primarykey = 'featured_id';
    protected $table_fields = array("featured_id", "user_id", "date_from", "date_to", "date_added");
    public function addNew($data_arr)
	{
		$arr = $this->filterTableFields($data_arr);
		$id = $this->insertGetId($arr);
    	return $id;
	}

}