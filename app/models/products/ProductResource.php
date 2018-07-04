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
class ProductResource extends Eloquent
{
    protected $table = "product_resource";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "product_id", "resource_type", "is_downloadable", "filename", "ext", "title", "default_flag", "server_url", "display_order",
									"width", "height", "l_width", "l_height", "t_width", "t_height", "s_width", "s_height");
}