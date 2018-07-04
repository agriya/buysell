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
class ImporterGeneralProductDetails extends \CustomEloquent
{
    protected $table = "importer_general_product_details";
    public $timestamps = true;
    protected $primarykey = 'id';
	protected $table_fields = array("id", "csv_file_id", "title", "url_slug", "description", "summary", "price", "category_id", "is_downloadable", "tags", "demo_url", "stock_available", "shipping_template", "image_attached", "thumb_image", "default_image", "preview_image1", "preview_image2", "preview_image3", "preview_image4", "preview_image5", "status", "product_id", "created_at", "updated_at", "error_reasons");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}
}