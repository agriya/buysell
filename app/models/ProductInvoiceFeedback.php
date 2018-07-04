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
class ProductInvoiceFeedback extends CustomEloquent
{
    protected $table = "product_invoice_feedback";
    public $timestamps = true;
    protected $primarykey = 'id';
    protected $table_fields = array("id", "feedback_user_id", "invoice_id", "product_id", "buyer_id", "seller_id", "feedback_comment", "feedback_remarks", "rating", "created_at", "updated_at");

    public function addNew($data_arr)
	{
		$this->setFieldValues($data_arr);
		$this->save();
		return $this->id;
	}
}