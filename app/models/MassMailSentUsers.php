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
class MassMailSentUsers extends CustomEloquent
{
    protected $table = "mass_mail_sent_users";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "mass_email_id", "user_id", "date_added");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}

}