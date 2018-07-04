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
class MassMail extends CustomEloquent
{
    protected $table = "mass_mail";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "send_on", "subject", "content", "from_email", "from_name", "reply_to_email", "upto_user_id", "status", "send_to", "user_id",
									"send_to_user_status", "offer_newsletter", "is_deleted", "repeat_every", "repeat_for", "reschedule_id", "reschedule_times");
    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}
}