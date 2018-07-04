<?php
class SiteTransactionHandlerService
{
	public function addNewTransaction($arr)
	{
		//depending on the transaction type add or reduce amount from pending or account balance

		$obj = new SiteTransactionDetails();
		$arr = $obj->filterTableFields($arr);
		$id = $obj->insertGetId($arr);
		return $id;
	}

	public function updateAccountBalance($arr, $notes_arr = array(), $send_mail = true)
	{
		$credit_obj = Credits::initialize();
		$rec_id = $this->fetchUserAccountRecord($arr['user_id'], $arr['currency']);
		if($arr['transaction_type'] == 'pending_credit' or $arr['transaction_type'] == 'credit')
		{
			$action = 'add';
		}
		else
		{
			$action = 'minus';
		}
		if($arr['transaction_type'] == 'pending_credit' OR $arr['transaction_type'] == 'pending_debit')
			$field = 'amount';
		else
			$field = 'amount';

		$credit_obj->setFilterCreditId($rec_id);
		$credit_obj->setCurrency($arr['currency']);
		$credit_obj->setUserId($arr['user_id']);
		$credit_obj->setAmount($arr['amount']);
		$credit_obj->creditAndDebit($field, $action);

		/*if($action == 'add') {
			UserAccountBalance::where('id', $rec_id)->update(array($field => DB::raw($field.' + '.$arr['amount']),
																'currency' => $arr['currency']
															));
		}
		else {
			UserAccountBalance::where('id', $rec_id)->update(array($field => DB::raw($field.' - '.$arr['amount']),
																'currency' => $arr['currency']
															));
		}*/

		//todo send mail
		/*if($send_mail AND count($notes_arr))
		{
			$date = new DateTime();
			$notes_arr['trans_date'] = $date->format('Y-m-d H:i:s');;
			$notes_arr['trans_amount'] = $arr['amount'];
			$notes_arr['trans_currency'] = $arr['currency'];
			$notes_arr['transaction_id'] = isset($notes_arr['transaction_id']) ? $notes_arr['transaction_id'] : 0;
			$notes_arr['trans_type'] = (in_array($arr['transaction_type'], array('pending_credit', 'pending_debit'))) ? 'Pending' : 'Cleared';
			$notes_arr['action'] = ($action == 'add') ? 'creditted' : 'debitted';

			//if for user, get the user details for display
			if(isset($notes_arr['for_admin']) AND $notes_arr['for_admin'])
			{
				$this->sendNotificationMail($notes_arr);
			}
			else
			{
				$notes_arr['for_member'] = 1;
				$notes_arr['for_user_id'] = $arr['user_id'];
				$this->sendNotificationMail($notes_arr);
			}
		}*/
	}
	public function sendNotificationMail($notes_arr)
	{
		$mailer = new AgMailer;
		if(isset($notes_arr['for_admin']) AND $notes_arr['for_admin'])
		{
			$notes_arr['subject'] =  trans('email.siteTransactionInAdminAccount');
			$mailer->sendAlertMail('site_transaction', 'emails.invoice.siteTransactionInAdminAccount', $notes_arr);
		}
		elseif(isset($notes_arr['for_member']) AND $notes_arr['for_member'])
		{
			$m_details = CUtil::getUserDetails($notes_arr['for_user_id']);
			$notes_arr['member_name'] = $m_details['display_name'];
			$notes_arr['member_code'] = $m_details['user_code'];
			$notes_arr['subject'] =  str_replace('VAR_USER_NAME', $m_details['display_name'], trans('email.siteTransactionInMemberAccountForAdmin'));
			$mailer->sendAlertMail('site_transaction', 'emails.invoice.siteTransactionInMemberAccountForAdmin', $notes_arr);
			//mail for user
			$notes_arr['to_email'] = $m_details['email'];
			$notes_arr['subject'] =  trans('email.siteTransactionInMemberAccount');
			$mailer->sendUserMail('site_transaction', 'emails.invoice.siteTransactionInMemberAccount', $notes_arr);


		}
	}

	public function fetchUserAccountRecord($user_id, $currency)
	{
		$credit_obj = Credits::initialize();
		$credit_obj->setUserId($user_id);
		$credit_obj->setFilterUserId($user_id);
		$credit_obj->setFilterCurrency($currency);
		$withdrwal_requests = $credit_obj->getWalletAccountBalance();
		//echo '<pre>User id::'.$user_id.'===';
		//print_r($withdrwal_requests);
		$cleared_amount = 0;
		if(count($withdrwal_requests) > 0) {
			foreach($withdrwal_requests as $key => $val) {
				return $val['id'];
			}
		}
		else {
			$credit_obj->setUserId($receiver['seller_id']);
			$credit_obj->setCurrency($receiver['currency']);
			$details = $credit_obj->credit();
			$json_data = json_decode($details, true);
			if($json_data['status'] == 'success')
			{
				return $json_data['credit_id'];
			}
		}

		/*$id = UserAccountBalance::where('user_id', $user_id)->where('currency', $currency)->pluck('id');
		if($id)
		{
			return $id;
		}
		else
		{
			$arr['user_id'] = $user_id;
			$arr['currency'] = $currency;
			$uab = new UserAccountBalance;
			$id = $uab->insertGetId($arr);
			return $id;
		}*/
	}

	public function handlePaidWithdrawalRequest($id)
	{
		//debit the amount from the users account balance and credit the fees for the site if fee > 0
		$withdraw_obj = Credits::initializeWithDrawal();

		$withdraw_sevice = new AdminManageWithdrawalService();
		$det = $withdraw_sevice->fetchWithdrawRequestDetails($id, $withdraw_obj);
		//$det = WithdrawalRequest::Select('amount', 'fee', 'user_id', 'currency')->where('withdraw_id', $id)->first();
		/*echo '<pre>';
		print_r($det);die;*/
		$credited_amount_to_user = 0;
		$transaction_arr['date_added'] = new DateTime;
		$transaction_arr['user_id'] = $det['user_id'];
		$transaction_arr['transaction_type'] = 'debit';
		$transaction_arr['amount'] = $det['amount'];
		$transaction_arr['currency'] = $det['currency'];
		$transaction_arr['transaction_key'] = 'withdrawal';
		$transaction_arr['reference_content_table'] = 'withdrawal_request';
		$transaction_arr['reference_content_id'] = $id;
		$transaction_arr['transaction_notes'] = 'Debitted amount from wallet for the withdrawal request id: '.$id;
		$trans_id = $this->addNewTransaction($transaction_arr);
		$notes_arr = array();
		$notes_arr['transaction_id'] = $trans_id;
		$notes_arr['towards_notes'] = ' towards the processing of the withdrawal request: '.$id;
		$this->updateAccountBalance($transaction_arr, $notes_arr);

		//Credit amount notes for user
		$credited_amount_to_user = $det['amount']-$det['fee'];
		$transaction_arr['amount'] = $credited_amount_to_user;
		$transaction_arr['transaction_type'] = 'credit';
		if(strtolower($det['payment_type']) == 'wiretransfer') {
			$transaction_arr['payment_type'] = 'wiretransfer';
			$transaction_arr['transaction_notes'] = 'Credited amount to your bank account for the withdrawal request id: '.$id;
		} else {
			$transaction_arr['payment_type'] = 'paypal';
			$transaction_arr['transaction_notes'] = 'Credited amount to your paypal account for the withdrawal request id: '.$id;
		}
		$trans_id = $this->addNewTransaction($transaction_arr);

		//Credit for site for the fee
		if($det['fee'] > 0)
		{
			$transaction_arr['date_added'] = new DateTime;
			$transaction_arr['user_id'] = Config::get('generalConfig.admin_id');
			$transaction_arr['transaction_type'] = 'credit';
			$transaction_arr['amount'] = $det['fee'];
			$transaction_arr['currency'] = $det['currency'];
			$transaction_arr['transaction_key'] = 'withdrawal_fee';
			$transaction_arr['reference_content_table'] = 'withdrawal_request';
			$transaction_arr['reference_content_id'] = $id;
			$transaction_arr['transaction_notes'] = 'Credited amount to wallet towards the fees for the withdrawal request id: '.$id;
			$trans_id_1 = $this->addNewTransaction($transaction_arr);
			$notes_arr = array();
			$notes_arr['transaction_id'] = $trans_id_1;
			$notes_arr['towards_notes'] = ' towards the fees for the withdrawal request: '.$id;
			$notes_arr['for_admin'] = 1;
			$this->updateAccountBalance($transaction_arr, $notes_arr);

		}

		return $trans_id;
	}
}