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
class ProductAttributes extends Eloquent
{
    protected $table = "product_attributes";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "attribute_label", "attribute_help_tip", "attribute_question_type", "default_value", "validation_rules", "date_added", "is_searchable", "show_in_list", "description", "status");
}