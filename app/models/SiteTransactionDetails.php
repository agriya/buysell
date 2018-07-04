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
class SiteTransactionDetails extends CustomEloquent
{
    protected $table = "site_transaction_details";
    public $timestamps = false;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "date_added", "user_id", "transaction_type", "amount", "currency", "transaction_key", "reference_content_id", "reference_content_table", "invoice_id", "purchase_code", "related_transaction_id", "status", "transaction_notes", "transaction_id", "paypal_adaptive_transaction_id", "payment_type");
    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
	}

}