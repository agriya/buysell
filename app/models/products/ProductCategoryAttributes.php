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
class ProductCategoryAttributes extends Eloquent
{
    protected $table = "product_category_attributes";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "attribute_id", "category_id", "date_added", "display_order");
}