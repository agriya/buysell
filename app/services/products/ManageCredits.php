<?php

class ManageCredits {

	protected $credit_id;

	protected $fields_arr = array();

	protected $filter_credited_by = '';

	protected $filter_credited_to = '';

	protected $filter_paid = '';

	protected $filter_group_by = '';

	public function setCreditId($val)
	{
		$this->fields_arr['credit_id'] = $val;
	}

	public function setCreditType($val)
	{
		$this->fields_arr['credit_type'] = $val;
	}

	public function setCurrency($val)
	{
		$this->fields_arr['currency'] = $val;
	}

	public function setAmount($val)
	{
		$this->fields_arr['amount'] = $val;
	}

	public function setCreditedBy($val)
	{
		$this->fields_arr['credited_by'] = $val;
	}

	public function setCreditedTo($val)
	{
		$this->fields_arr['credited_to'] = $val;
	}

	public function setAdminNotes($val)
	{
		$this->fields_arr['admin_notes'] = $val;
	}

	public function setUserNotes($val)
	{
		$this->fields_arr['user_notes'] = $val;
	}

	public function setPaid($val)
	{
		$this->fields_arr['paid'] = $val;
	}

	public function setDatePaid($val)
	{
		$this->fields_arr['date_paid'] = $val;
	}

	public function setGenerateInvoice($val)
	{
		$this->fields_arr['generate_invoice'] = $val;
	}

	public function setCreditsDateAdded($val)
	{
		$this->fields_arr['date_added'] = $val;
	}

	public function setCreditsDateUpdated($val)
	{
		$this->fields_arr['date_updated'] = $val;
	}

	public function setFilterCreditedBy($val)
	{
		$this->filter_credited_by = $val;
	}

	public function setFilterCreditedTo($val)
	{
		$this->filter_credited_to = $val;
	}

	public function setFilterInvoicePaid($val)
	{
		$this->filter_paid = $val;
	}

	public function setFilterGroupBy($val)
	{
		$this->filter_group_by = $val;
	}

	public function Addcredits() {
		$rules = $message = array();
		$rules += array(
			'currency' => 'Required',
			'amount' => 'Required|regex:/^\d*(\.\d{2})?$/',
			'credited_by' => 'Required',
			'credited_to' => 'Required',
			'user_notes' => 'Required',
			'admin_notes' => 'Required'
		);
		$validator = Validator::make($this->fields_arr, $rules, $message);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $errors));
		}
		else {
			$credit_id = (isset($this->fields_arr['credit_id']) && $this->fields_arr['credit_id'] > 0) ? $this->fields_arr['credit_id'] : 0;
			if($credit_id && $credit_id > 0) {
				CreditsLog::whereRaw('credit_id = ?', array($credit_id))->update($this->fields_arr);
				return json_encode(array('status' => 'success', 'credit_id' => $credit_id));
			}
			else {
				$insert_id = CreditsLog::insertGetId($this->fields_arr);
				return json_encode(array('status' => 'success', 'credit_id' => $insert_id));
			}
		}
	}

	public function getCreditsDetailsById($credit_id)
	{
		$credit_id = ($credit_id != '' && $credit_id > 0) ? $credit_id : 0;
		$credit_arr = array();
		$credits = CreditsLog::where('credit_id', '=', $credit_id)
										->get();
		if(count($credits) > 0) {
			foreach($credits as $key => $vlaues) {
				$credit_arr['credit_id'] = $vlaues->credit_id;
				$credit_arr['credit_type'] = $vlaues->credit_type;
				$credit_arr['currency'] = $vlaues->currency;
				$credit_arr['amount'] = $vlaues->amount;
				$credit_arr['credited_by'] = $vlaues->credited_by;
				$credit_arr['credited_to'] = $vlaues->credited_to;
				$credit_arr['admin_notes'] = $vlaues->admin_notes;
				$credit_arr['user_notes'] = $vlaues->user_notes;
				$credit_arr['paid'] = $vlaues->paid;
				$credit_arr['date_paid'] = $vlaues->date_paid;
				$credit_arr['generate_invoice'] = $vlaues->generate_invoice;
				$credit_arr['date_added'] = $vlaues->date_added;
				$credit_arr['date_updated'] = $vlaues->date_updated;
			}
		}
		return $credit_arr;
	}

	public function getCreditsList($user_id, $result_type = 'paginate', $records = 10)
	{
		//$credits_arr = array();
		$credits = CreditsLog::Select('credit_id', 'credit_type', 'currency', 'amount', 'credited_by', 'credited_to'
										, 'admin_notes', 'user_notes', 'paid', 'date_paid', 'generate_invoice'
										, 'date_added', 'date_updated')
									->where('credited_to', $user_id)
									->orderBy('credit_id', 'DESC');
		if($result_type == 'paginate')
			$credits = $credits->paginate($records);
		else
			$credits = $credits->get();

		/*if(count($credits) > 0) {
			foreach($credits as $key => $vlaues) {
				$credits_arr[$key]['credit_id'] = $vlaues->credit_id;
				$credits_arr[$key]['currency'] = $vlaues->currency;
				$credits_arr[$key]['amount'] = $vlaues->amount;
				$credits_arr[$key]['credited_by'] = $vlaues->credited_by;
				$credits_arr[$key]['credited_to'] = $vlaues->credited_to;
				$credits_arr[$key]['admin_notes'] = $vlaues->admin_notes;
				$credits_arr[$key]['user_notes'] = $vlaues->user_notes;
				$credits_arr[$key]['paid'] = $vlaues->paid;
				$credits_arr[$key]['date_paid'] = $vlaues->date_paid;
				$credits_arr[$key]['generate_invoice'] = $vlaues->generate_invoice;
				$credits_arr[$key]['date_added'] = $vlaues->date_added;
				$credits_arr[$key]['date_updated'] = $vlaues->date_updated;
			}
		}*/
		return $credits;
	}

	public function updateCreditsLogDetails($credit_id, $data_arr)
	{
		CreditsLog::where('credit_id','=', $credit_id )->update($data_arr);
	}
}