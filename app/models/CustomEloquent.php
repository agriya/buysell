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
class CustomEloquent extends \Eloquent {
	protected $table_fields = array();
	public function setFieldValue($key, $value)
    {
    	if(in_array($key, $this->table_fields)) $this->$key = $value;
	}
	public function setFieldValues($arr = array())
    {
    	foreach($arr as $key => $value)
    	{
    		if(in_array($key, $this->table_fields)) $this->$key = $value;
    	}
	}
	public function filterTableFields($in_arr = array())
    {
    	foreach($in_arr as $key => $value)
    	{
    		if(in_array($key, $this->table_fields)){ $arr[$key] = $value; }
    	}
    	return $arr;
	}


}