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
class ShippingTemplates extends CustomEloquent
{
    protected $table = "shipping_templates";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "template_name", "is_default", "status");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}