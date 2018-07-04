<?php

class WithDrawal {

	protected $withdraw_id;

	protected $fields_arr = array();

	protected $filter_withdraw_id = '';

	protected $filter_user_id = '';

	protected $filter_status = '';

	protected $filter_group_by = '';

	public function setWithDrawId($val)
	{
		$this->fields_arr['withdraw_id'] = $val;
	}

	public function setUserId($val)
	{
		$this->fields_arr['user_id'] = $val;
	}

	public function setCurrency($val)
	{
		$this->fields_arr['currency'] = $val;
	}

	public function setAmount($val)
	{
		$this->fields_arr['amount'] = $val;
	}

	public function setFee($val)
	{
		$this->fields_arr['fee'] = $val;
	}

	public function setPaymentType($val)
	{
		$this->fields_arr['payment_type'] = $val;
	}

	public function setPayToUserAccountInfo($val)
	{
		$this->fields_arr['pay_to_user_account'] = $val;
	}

	public function setPaidNotes($val)
	{
		$this->fields_arr['paid_notes'] = $val;
	}

	public function setAdminNotes($val)
	{
		$this->fields_arr['admin_notes'] = $val;
	}

	public function setPaidBy($val)
	{
		$this->fields_arr['set_as_paid_by'] = $val;
	}

	public function setStatus($val)
	{
		$this->fields_arr['status'] = $val;
	}

	public function setDateAdded($val)
	{
		$this->fields_arr['added_date'] = $val;
	}

	public function setDateCancelled($val)
	{
		$this->fields_arr['date_cancelled'] = $val;
	}

	public function setCancelledBy($val)
	{
		$this->fields_arr['cancelled_by'] = $val;
	}

	public function setCancelledReason($val)
	{
		$this->fields_arr['cancelled_reason'] = $val;
	}

	public function setSiteTransactionId($val)
	{
		$this->fields_arr['site_transaction_id'] = $val;
	}

	public function setDatePaid($val)
	{
		$this->fields_arr['date_paid'] = $val;
	}

	public function setFilterUserId($val)
	{
		$this->filter_user_id = $val;
	}

	public function setFilterWithdrawId($val)
	{
		$this->filter_withdraw_id = $val;
	}

	public function setFilterStatus($val)
	{
		$this->filter_status = $val;
	}

	public function setFilterGroupBy($val)
	{
		$this->filter_group_by = $val;
	}

	public function addWithdrawalRequest() {
		$rules = $message = array();
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			/*echo '<pre>';
			print_r($failed = $validator->failed());*/
			$errors = $validator->errors()->all();

			//print_r($errors);
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			if(isset($this->fields_arr['withdraw_id']) && $this->fields_arr['withdraw_id'] > 0) {
				WithdrawalRequest::whereRaw('withdraw_id = ?', array($this->fields_arr['withdraw_id']))->update($this->fields_arr);
				return json_encode(array('status' => 'success'));
			}
			else {
				WithdrawalRequest::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success'));
			}
		}
	}

	public function getWithdrwalRequests($return_type = 'get', $limit = 10)
	{
		$rules = array();
		$credit_details = array();
		$validator = Validator::make($this->fields_arr, $rules);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {

			$withdraw = WithdrawalRequest::orderBy('added_date', 'DESC');

			if($this->filter_user_id != '')
				$withdraw = $withdraw->whereRaw('user_id = ?', array($this->filter_user_id));

			if($this->filter_withdraw_id != '')
				$withdraw = $withdraw->whereRaw('withdraw_id = ?', array($this->filter_withdraw_id));

			if($this->filter_status != '')
				$withdraw = $withdraw->whereRaw('status = ?', array($this->filter_status));

			if($return_type == 'get')
				$withdraw = $withdraw->get();
			else
				$withdraw = $withdraw->paginate($limit);

			return $withdraw;

		}
	}
}