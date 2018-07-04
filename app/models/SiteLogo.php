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
class SiteLogo extends CustomEloquent
{
    protected $table = "site_logo";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "logo_image_name", "logo_image_ext", "logo_width", "logo_height", "logo_server_url");
}