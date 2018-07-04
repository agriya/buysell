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
namespace App\Plugins\Importer\Controllers;
class ImporterCsvFile extends \CustomEloquent
{
    protected $table = "importer_csv_file";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "file_from", "file_name", "server_url", "status", "user_id", "file_original_name", "item_count", "parsed_item_count", "zip_file_name", "zip_org_name", "created_at", "updated_at");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}
}
