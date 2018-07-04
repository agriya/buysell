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
class ProductImage extends Eloquent
{
    protected $table = "product_image";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "product_id", "thumbnail_title", "thumbnail_img", "thumbnail_ext", "thumbnail_width", "thumbnail_height", "thumbnail_s_width", "thumbnail_s_height", "thumbnail_t_width", "thumbnail_t_height", "thumbnail_l_width", "thumbnail_l_height",
	"default_title", "default_img", "default_ext", "default_width", "default_height", "default_s_width", "default_s_height", "default_t_width", "default_t_height", "default_l_width", "default_l_height");
}