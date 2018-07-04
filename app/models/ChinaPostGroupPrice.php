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
 class ChinaPostGroupPrice extends CustomEloquent
{
    protected $table = "china_post_group_price";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "china_post_category", "china_post_group", "upto_weight", "upto_weight_price", "additional_weight", "additional_weight_price");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}