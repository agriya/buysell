<?php

class CommonInvoices {

	protected $credit_id;

	protected $fields_arr = array();

	protected $filter_group_by = '';

	protected $filter_reference_type = '';

	protected $filter_reference_id = '';

	protected $filter_pay_key = '';

	protected $filter_tracking_id = '';

	public function setCommonInvoiceId($val)
	{
		$this->fields_arr['common_invoice_id'] = $val;
	}

	public function setUserId($val)
	{
		$this->fields_arr['user_id'] = $val;
	}

	public function setReferenceType($val)
	{
		$this->fields_arr['reference_type'] = $val;
	}

	public function setReferenceId($val)
	{
		$this->fields_arr['reference_id'] = $val;
	}

	public function setCurrency($val)
	{
		$this->fields_arr['currency'] = $val;
	}

	public function setAmount($val)
	{
		$this->fields_arr['amount'] = $val;
	}

	public function setIsCreditPayment($val)
	{
		$this->fields_arr['is_credit_payment'] = $val;
	}

	public function setPaypalAmount($val)
	{
		$this->fields_arr['paypal_amount'] = $val;
	}

	public function setStatus($val)
	{
		$this->fields_arr['status'] = $val;
	}

	public function setDatePaid($val)
	{
		$this->fields_arr['date_paid'] = $val;
	}

	public function setFilterGroupBy($val)
	{
		$this->filter_group_by = $val;
	}

	public function setFilterReferenceType($val)
	{
		$this->filter_reference_type = $val;
	}

	public function setFilterReferenceId($val)
	{
		$this->filter_reference_id = $val;
	}

	public function setFilterPayKey($val)
	{
		$this->filter_pay_key = $val;
	}

	public function setFilterTrackingId($val)
	{
		$this->filter_tracking_id = $val;
	}

	public function resetFilterKeys()
	{
		$this->filter_order_id = '';
		$this->filter_reference_type = '';
		$this->filter_reference_id = '';
		$this->filter_pay_key = '';
		$this->filter_tracking_id = '';
	}

	public function addCommonInvoice() {
		$rules = $message = array();
		$rules += array(
			'user_id' => 'Required',
			'reference_type' => 'Required',
			'reference_id' => 'Required',
			'currency' => 'Required',
			'amount' => 'Required|regex:/^\d*(\.\d{2})?$/',
			'status' => 'Required'
		);
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$common_invoice_id = (isset($this->fields_arr['common_invoice_id']) && $this->fields_arr['common_invoice_id'] > 0) ? $this->fields_arr['common_invoice_id'] : 0;
			if($common_invoice_id && $common_invoice_id > 0) {
				CommonInvoice::whereRaw('common_invoice_id = ?', array($common_invoice_id))->update($this->fields_arr);
				return json_encode(array('status' => 'success', 'common_invoice_id' => $common_invoice_id));
			}
			else {
				$this->fields_arr['date_added'] = DB::raw('NOW()');
				$common_invoice_id = CommonInvoice::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'common_invoice_id' => $common_invoice_id));
			}
		}
	}

	public function updateCommonInvoiceDetails($common_invoice_id, $data_arr)
	{
		CommonInvoice::where('common_invoice_id','=', $common_invoice_id )->update($data_arr);
	}

	public function getCommonInvoiceDetailsById($common_invoice_id = '')
	{
		$common_invoice_id = ($common_invoice_id != '' && $common_invoice_id > 0) ? $common_invoice_id : 0;
		$invoice_arr = array();

		$invoices = CommonInvoice::Select('common_invoice_id', 'user_id', 'reference_type', 'reference_id', 'currency'
											, 'amount', 'is_credit_payment', 'paypal_amount', 'pay_key', 'tracking_id'
											, 'status', 'date_paid', 'date_added');
		if($common_invoice_id != '')
			$invoices = $invoices->whereRaw('common_invoice_id = ?', array($common_invoice_id));
		if($this->filter_reference_type != '')
			$invoices = $invoices->whereRaw('reference_type = ?', array($this->filter_reference_type));
		if($this->filter_reference_id != '')
			$invoices = $invoices->whereRaw('reference_id = ?', array($this->filter_reference_id));
		if($this->filter_pay_key != '')
			$invoices = $invoices->whereRaw('pay_key = ?', array($this->filter_pay_key));
		if($this->filter_tracking_id != '')
			$invoices = $invoices->whereRaw('tracking_id = ?', array($this->filter_tracking_id));
		$invoices = $invoices->get();

		if(count($invoices) > 0) {
			foreach($invoices as $key => $vlaues) {
				$invoice_arr['common_invoice_id'] = $vlaues->common_invoice_id;
				$invoice_arr['user_id'] = $vlaues->user_id;
				$invoice_arr['reference_type'] = $vlaues->reference_type;
				$invoice_arr['reference_id'] = $vlaues->reference_id;
				$invoice_arr['currency'] = $vlaues->currency;
				$invoice_arr['amount'] = $vlaues->amount;
				$invoice_arr['is_credit_payment'] = $vlaues->is_credit_payment;
				$invoice_arr['paypal_amount'] = $vlaues->paypal_amount;
				$invoice_arr['pay_key'] = $vlaues->pay_key;
				$invoice_arr['tracking_id'] = $vlaues->tracking_id;
				$invoice_arr['status'] = $vlaues->status;
				$invoice_arr['date_paid'] = $vlaues->date_paid;
				$invoice_arr['date_added'] = $vlaues->date_added;
			}
		}
		return $invoice_arr;
	}

	public function getCommonInvoiceDetailsByReferenceId($reference_type = 'Products', $reference_id)
	{
		$reference_id = ($reference_id != '' && $reference_id > 0) ? $reference_id : 0;
		$invoice_arr = array();
		$invoices = CommonInvoice::whereRaw('reference_type = ? AND reference_id = ?', array($reference_type, $reference_id))
										->get();
		if(count($invoices) > 0) {
			foreach($invoices as $key => $vlaues) {
				$invoice_arr['common_invoice_id'] = $vlaues->common_invoice_id;
				$invoice_arr['user_id'] = $vlaues->common_invoice_id;
				$invoice_arr['reference_type'] = $vlaues->reference_type;
				$invoice_arr['reference_id'] = $vlaues->reference_id;
				$invoice_arr['currency'] = $vlaues->currency;
				$invoice_arr['amount'] = $vlaues->amount;
				$invoice_arr['is_credit_payment'] = $vlaues->is_credit_payment;
				$invoice_arr['paypal_amount'] = $vlaues->paypal_amount;
				$invoice_arr['pay_key'] = $vlaues->pay_key;
				$invoice_arr['tracking_id'] = $vlaues->tracking_id;
				$invoice_arr['status'] = $vlaues->status;
				$invoice_arr['date_paid'] = $vlaues->date_paid;
				$invoice_arr['date_added'] = $vlaues->date_added;
			}
		}
		return $invoice_arr;
	}
}