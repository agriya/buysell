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
class ImporterEtsyProductDetails extends \CustomEloquent
{
    protected $table = "importer_etsy_product_details";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "csv_file_id", "title", "description", "price", "currency_code", "quantity", "tags", "materials", "image1_path", "image2_path", "image3_path", "image4_path", "image5_path", "status", "product_id", "created_at", "updated_at", "error_reasons");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}
}