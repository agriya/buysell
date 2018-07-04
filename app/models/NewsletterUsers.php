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
class NewsletterUsers extends CustomEloquent
{
	//protected $softDelete = true;
    protected $table = "newsletter_users";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "newsletter_id", "user_id", "subscriber_id", "first_name", "last_name", "user_name", "email", "is_sent", "created_at", "updated_at");
    protected $fillable = array("id", "newsletter_id", "user_id", "subscriber_id", "first_name", "last_name", "user_name", "email", "is_sent", "created_at", "updated_at");

	public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}

	public function getTableFields(){
		return $this->table_fields;
	}


}