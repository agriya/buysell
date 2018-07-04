<?php

class PaypalPaymentTransaction {

	protected $transaction_id;

	protected $fields_arr = array();

	public function __construct()
	{
	}

	public function setTransactionId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setPayKey($val)
	{
		$this->fields_arr['pay_key'] = $val;
	}

	public function setTrackingId($val)
	{
		$this->fields_arr['tracking_id'] = $val;
	}

	public function setCurrencyCode($val)
	{
		$this->fields_arr['currency_code'] = $val;
	}

	public function setBuyerEmail($val)
	{
		$this->fields_arr['buyer_email'] = $val;
	}

	public function setReceiverDetails($val)
	{
		$this->fields_arr['receiver_details'] = $val;
	}

	public function setIpnPostString($val)
	{
		$this->fields_arr['ipn_post_str'] = $val;
	}

	public function setPaymentDetailsString($val)
	{
		$this->fields_arr['payment_details_str'] = $val;
	}

	public function setErrorId($val)
	{
		$this->fields_arr['error_id'] = $val;
	}

	public function setStatus($val)
	{
		$this->fields_arr['status'] = $val;
	}

	public function setBuyerTransId($val)
	{
		$this->fields_arr['buyer_trans_id'] = $val;
	}

	/**
	 * Inserts items into the transaction.
	 *
	 * @access   public
	 * @return   json
	 */
	public function add()
	{
		$rules = $message = array();
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$trans_id = 0;
			if(isset($this->fields_arr['id'])) {
				$trans_details = PaypalAdaptivePaymentsTransaction::Select('id')
											->whereRaw('id = ?', array($this->fields_arr['id']))
											->first();
				if(count($trans_details) > 0) {
					$trans_id = $trans_details['id'];
				}
			}
			if($trans_id > 0) {
				PaypalAdaptivePaymentsTransaction::whereRaw('id = ?', array($trans_id))->update($this->fields_arr);
				return json_encode(array('status' => 'success', 'transaction_id' => $trans_id));
			}
			else {
				$trans_id = PaypalAdaptivePaymentsTransaction::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'transaction_id' => $trans_id));
			}
		}
	}

	public function getPaypalAdaptivePaymentDetails($transaction_id = null)
	{
		$trans_details = PaypalAdaptivePaymentsTransaction::whereRaw('id = ?', array($transaction_id))
											->first()->toArray();
		return $trans_details;
		//echo "<pre>";print_r($trans_details);echo "</pre>";
	}
}
?>