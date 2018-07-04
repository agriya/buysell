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
class ShopDetails extends Eloquent
{
    protected $table = "shop_details";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "user_id", "shop_name", "url_slug", "shop_slogan", "shop_desc", "shop_address1", "shop_address2", "shop_city", "shop_state", "shop_zipcode", "shop_country", "shop_message", "shop_contactinfo", "image_name", "image_ext", "image_server_url", "t_height", "t_width", "is_featured_shop", "created_at", "updated_at");
}