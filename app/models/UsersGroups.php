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
class UsersGroups extends CustomEloquent
{
    protected $table = "users_groups";
    public $timestamps = false;
    protected $primarykey = 'user_id';
    protected $table_fields = array("user_id", "group_id");
}