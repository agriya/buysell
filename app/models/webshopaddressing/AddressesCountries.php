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
class AddressesCountries extends Eloquent
{
    protected $table = "countries";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "address_line1", "address_line2", "street", "city", "state", "country", "country_id");
    //protected $fillable = array("id", "user_id", "address_line1", "address_line2", "street", "city", "state", "country", "country_id");

    public function __construct()
    {
        $country = Config::get('webshopaddressing.countries_table');
        $tablename = (isset($country['table_name']) && $country['table_name']!='')?$country['table_name']:'';
        if($tablename!='')
        {
            $this->setTableName($country['table_name']);
        }

    }

    public function setTableName($table_name = '')
    {
    	if($table_name != '')
    		$this->table = $table_name;
    }

    public function getTableName()
    {
        return $this->table;
    }

}