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
class StaticPages extends CustomEloquent
{
	//protected $softDelete = true;
    protected $table = "static_pages";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "page_name", "url_slug", "title", "content", "main_page_content", "display_in_footer", "status", "page_display_order", "page_type", "external_link", "created_at", "updated_at");
    protected $fillable = array("id", "page_name", "url_slug", "title", "content", "main_page_content", "display_in_footer", "status", "page_display_order", "page_type", "external_link", "created_at", "updated_at");

	public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}

	public function getTableFields(){
		return $this->table_fields;
	}


}