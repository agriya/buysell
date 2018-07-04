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
class ProductPackageDetails extends Eloquent
{
    protected $table = "product_package_details";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("product_id", "weight", "length", "width", "height", "custom", "first_qty", "additional_qty", "additional_weight");
}
?>