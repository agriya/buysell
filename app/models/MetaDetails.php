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
class MetaDetails extends Eloquent
{
    protected $table = "meta_details";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "page_name", "meta_title", "meta_description", "meta_keyword", "date_added",	"date_updated", "status");
}