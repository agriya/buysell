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
class BannerImages extends CustomEloquent
{
    protected $table = "banner_images";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "title", "content", "filename", "ext",
										 "width", "height", "large_width", "large_height", "server_url",
										 "display", "date_added");
    public function addNew($data_arr)
	{
		$arr = $this->filterTableFields($data_arr);
		$id = $this->insertGetId($arr);
    	return $id;
	}

}