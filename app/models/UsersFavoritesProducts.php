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
class UsersFavoritesProducts extends CustomEloquent
{
    protected $table = "users_favorites_products";
    public $timestamps = false;
    protected $primarykey = 'favorite_id';
    protected $table_fields = array("favorite_id", "product_id", "date_added");
    public function addNew($data_arr)
	{
		$arr = $this->filterTableFields($data_arr);
		$id = $this->insertGetId($arr);
    	return $id;
	}
}