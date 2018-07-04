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
class NewsletterSubscriber extends CustomEloquent
{
    protected $table = "newsletter_subscriber";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "email", "ip", "date_added", "date_unsubscribed", "unsubscribe_code", "status", "first_name", "last_name", "user_id");

     public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}

}
?>