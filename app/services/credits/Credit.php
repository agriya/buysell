<?php

class Credit {

	protected $credit_id;

	protected $fields_arr = array();

	protected $filter_user_id = '';

	protected $filter_currency = '';

	protected $filter_group_by = '';

	public function setCreditId($val)
	{
		$this->fields_arr['id'] = $val;
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
		$val = number_format ($val, 2, '.','');
		$this->fields_arr['amount'] = $val;
	}

	public function setClearedAmount($val)
	{
		$this->fields_arr['cleared_amount'] = $val;
	}

	public function setPaymentType($val)
	{
		$this->fields_arr['payment_type'] = $val;
	}

	public function setFee($val)
	{
		$this->fields_arr['fee'] = $val;
	}

	public function setPayToUserAccountInfo($val)
	{
		$this->fields_arr['pay_to_user_account'] = $val;
	}

	public function setFilterCreditId($val)
	{
		$this->filter_credit_id = $val;
	}

	public function setFilterUserId($val)
	{
		$this->filter_user_id = $val;
	}

	public function setFilterCurrency($val)
	{
		$this->filter_currency = $val;
	}

	public function setFilterGroupBy($val)
	{
		$this->filter_group_by = $val;
	}

	public function credit() {
		$rules = $message = array();
		$rules += array(
			'user_id' => 'Required'
			, 'currency' => 'Required'
			, 'amount' => 'Required|regex:/^\d*(\.\d{2})?$/',
		);
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$added_credit_id = 0;
			$credit_details = UserAccountBalance::Select('id')
										->whereRaw('currency = ? AND user_id = ?'
													, array($this->fields_arr['currency'], $this->fields_arr['user_id']))
										->first();
			if(count($credit_details) > 0) {
				$added_credit_id = $credit_details['id'];
			}

			if($added_credit_id > 0) {
				UserAccountBalance::whereRaw('id = ?', array($added_credit_id))->increment('amount', $this->fields_arr['amount']);
				return json_encode(array('status' => 'success'));
			}
			else {
				$insert_id = UserAccountBalance::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'credit_id' => $insert_id));
			}
		}
	}

	public function creditAndDebit($field, $action) {

		$rules = $message = array();
		$rules += array(
			'user_id' => 'Required'
			, 'currency' => 'Required'
			, 'amount' => 'Required|regex:/^\d*(\.\d{2})?$/',
		);

		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$added_credit_id = 0;
			if(isset($this->filter_credit_id) && $this->filter_credit_id != 0 && $this->filter_credit_id > 0)
				$added_credit_id = $this->filter_credit_id;
			else
			{
				$credit_details = UserAccountBalance::Select('id')
										->whereRaw('currency = ? AND user_id = ?'
													, array($this->fields_arr['currency'], $this->fields_arr['user_id']))
										->first();
				if(count($credit_details) > 0) {
					$added_credit_id = $credit_details['id'];
				}
				else
				{
					$input_arr = array();
					$input_arr['user_id'] = $this->fields_arr['user_id'];
					$input_arr['currency'] = $this->fields_arr['currency'];
					$added_credit_id = UserAccountBalance::insertGetId($input_arr);
				}
			}
			//echo "added_credit_id: ".$added_credit_id;
			if($added_credit_id != 0 && $added_credit_id > 0) {
				if($action == 'minus') {
					UserAccountBalance::whereRaw('id = ?', array($added_credit_id))->decrement($field, $this->fields_arr['amount']);
					return json_encode(array('status' => 'success'));
				}
				else {
					UserAccountBalance::whereRaw('id = ?', array($added_credit_id))->increment($field, $this->fields_arr['amount']);
					return json_encode(array('status' => 'success'));
				}
			}
		}
	}

	public function getWalletAccountBalance()
	{
		$rules = array(
			'user_id' => 'Required'
		);
		$credit_details = array();
		$validator = Validator::make($this->fields_arr, $rules);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$credit_details = UserAccountBalance::orderBy('id', 'DESC');

			if($this->filter_user_id != '')
				$credit_details = $credit_details->whereRaw('user_id = ?', array($this->filter_user_id));

			if($this->filter_currency != '')
				$credit_details = $credit_details->whereRaw('currency = ?', array($this->filter_currency));

			if($this->filter_group_by != '')	{
				$credit_details = $credit_details->groupBy($this->filter_group_by);
			}
			$credit_details = $credit_details->get();

			return $credit_details;
		}
	}

	public function getWithdrwalRequests($return_type = 'get', $limit)
	{
		$rules = array(
			'user_id' => 'Required'
		);
		$credit_details = array();
		$validator = Validator::make($this->fields_arr, $rules);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {

			$withdraw = WithdrawalRequest::orderBy('added_date', 'ASC');
			$withdraw = $withdraw->where('user_id','=',$this->fields_arr['user_id']);


			if($return_type == 'get')
				$withdraw = $withdraw->get();
			else
				$withdraw = $withdraw->paginate($limit);

			return $withdraw;

		}
	}



	/*public static function totalCredits($user_id, $currency = 'USD') {
		if($user_id == '') {
			throw new UserNotFoundException('User not given');
		}
		$total = UserAccountBalance::whereRaw('user_id = ? AND currency = ?', array($user_id, $currency))->sum('amount');
		return $total;
	}

	public static function debit($user_id, $amount = null, $currency = 'USD') {
		if($user_id == '') {
			throw new UserNotFoundException((trans('credits.user_not_given'));
		}
		else if($amount == '') {
			throw new AmountNotFoundException(trans('credits.amount_not_given'));
		}
		if($amount != '') {
			if (!preg_match("/^[0-9]+(\\.[0-9]{1,2})?$/", $amount))
			{
				throw new InvalidAmountException(trans('credits.invalid_amount_format'));
			}
		}
		UserAccountBalance::whereRaw('user_id = ? AND currency = ?', array($user_id, $currency))->decrement('amount', $amount);
		return true;
	}

	public static function withdraw($user_id, $amount = null, $currency = 'USD') {
		if($user_id == '') {
			throw new UserNotFoundException(trans('credits.user_not_given'));
		}
		else if($amount == '') {
			throw new AmountNotFoundException(trans('credits.amount_not_given'));
		}
		if($amount != '') {
			if (!preg_match("/^[0-9]+(\\.[0-9]{1,2})?$/", $amount))
			{
				throw new InvalidAmountException(trans('credits.invalid_amount_format'));
			}
		}
		$data_arr['user_id'] = $user_id;
		$data_arr['currency'] = $currency;
		$data_arr['amount'] = $amount;
		$data_arr['added_date'] = DB::raw('NOW()');
		$data_arr['status'] = 'Pending';
		WithdrawalRequest::insertGetId($data_arr);
		return true;
	}

	public static function withdrawDetails() {
		$withdraw_details = self::getWithdrawDetails();
		if(count($withdraw_details) > 0) {
			return $withdraw_details;
		}
		throw new WithdrawalRequestNotFoundException;
	}

	public static function getWithdrawDetails()
	{
		$withdraw_arr = array();
		$withdraw = WithdrawalRequest::Select('withdraw_id', 'user_id', 'currency', 'amount', 'added_date', 'status')
									->orderBy('added_date', 'ASC')->get();
		if(count($withdraw) > 0) {
			foreach($withdraw as $key => $values) {
				$withdraw_arr[$key]['id'] = $values->withdraw_id;
				$withdraw_arr[$key]['user_id'] = $values->user_id;
				$withdraw_arr[$key]['currency'] = $values->currency;
				$withdraw_arr[$key]['amount'] = $values->amount;
				$withdraw_arr[$key]['added_date'] = $values->added_date;
				$withdraw_arr[$key]['status'] = $values->status;
			}
		}
		return $withdraw_arr;
	}*/
}