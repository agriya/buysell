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
class Message extends CustomEloquent
{
    protected $table = "message";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "date_added", "from_user_id", "to_user_id", "last_replied_by", "last_replied_date", "subject", "reply_count", "message_text", "from_message_status", "to_message_status", "open_alert_needed", "is_deleted", "is_replied", "rel_type", "rel_id", "rel_table");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}
}
