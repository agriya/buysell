<?php
//@added by ravikumar_131at10
class AdminManageWithdrawalService
{
	public function setWithdrawalFilterArr()
	{
		//$this->filter_arr['request_by']= '';
		//$this->filter_arr['date_added_from']= '';
		//$this->filter_arr['date_added_to']= '';
	//	$this->filter_arr['request_code']= '';
	//	$this->filter_arr['operator']= '';
		$this->filter_arr['request_status']= '';
		$this->filter_arr['request_id']= '';
	}

	public function setWithdrawalSrchArr($input, $default_status)
	{
		//$this->srch_arr['request_by'] =(isset($input['request_by']) && $input['request_by'] != '') ? $input['request_by'] : "";
		//$this->srch_arr['date_added_from']= (isset($input['date_added_from']) && $input['date_added_from'] != '') ? $input['date_added_from'] : "";
		//$this->srch_arr['date_added_to']= (isset($input['date_added_to']) && $input['date_added_to'] != '') ? $input['date_added_to'] : "";
	//	$this->srch_arr['request_code']= (isset($input['request_code']) && $input['request_code'] != '') ? $input['request_code'] : "";
	//	$this->srch_arr['operator']= (isset($input['operator']) && $input['operator'] != '') ? $input['operator'] : "";
		$this->srch_arr['request_status']= (isset($input['request_status'])) ? $input['request_status'] : $default_status;
		$this->srch_arr['request_id']= (isset($input['request_id'])) ? $input['request_id'] : '';
	}

	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : "";
	}


	public function buildWithdrawalQuery($withdraw_obj)
	{
		/*$this->qry = WithdrawalRequest::Select('withdrawal_request.id', 'withdrawal_request.date_added', 'withdrawal_request.user_id',
							'withdrawal_request.amount', 'withdrawal_request.currency', 'withdrawal_request.pay_to_user_account',
							'withdrawal_request.paid_notes', 'withdrawal_request.admin_notes', 'withdrawal_request.set_as_paid_by',
							'withdrawal_request.site_transaction_id', 'withdrawal_request.status', 'withdrawal_request.date_paid',
							'withdrawal_request.date_cancelled', 'withdrawal_request.cancelled_reason', 'withdrawal_request.cancelled_by');
							'user.first_name as user_firstname', 'user.last_name as user_lastname', 'user.email as user_email',
							'user.user_code as user_usercode', 'user.user_id as requester_id',
							'admin.first_name as admin_firstname', 'admin.last_name as admin_lastname', 'admin.email as admin_email',
							'admin.user_code as admin_usercode', 'admin.user_id as admin_id', 'withdrawal_request.fee', 'withdrawal_request.payment_type',
							'user_account_balance.cleared_amount')
						->LeftJoin('users as user', 'withdrawal_request.user_id', '=', 'user.user_id')
						->LeftJoin('users as admin', 'withdrawal_request.set_as_paid_by', '=', 'admin.user_id')
						->LeftJoin('user_account_balance', function($join)
		                         {
		                             $join->on('withdrawal_request.user_id', '=', 'user_account_balance.user_id');
		                             $join->on('withdrawal_request.currency', '=', 'user_account_balance.currency');
		                         });*/

		/*if($this->getSrchVal('request_by'))
		{
			if(is_numeric($this->getSrchVal('request_by')))
			{
				$this->qry->whereRaw("( user.user_id = ".$this->getSrchVal('request_by')." OR user.user_code = ".$this->getSrchVal('request_by')." )");
			}
			else
			{
				$name_arr = explode(" ",$this->getSrchVal('request_by'));
				if(count($name_arr) > 0)
				{
					foreach($name_arr AS $names)
					{
						$this->qry->whereRaw("( user.first_name LIKE '%$names%' OR user.last_name LIKE '%$names%' OR user.email LIKE '%$names%' )");
					}
				}
			}
		}

		if($this->getSrchVal('date_added_from') && $this->getSrchVal('date_added_to'))
		{
			$this->qry->whereRaw("DATE_FORMAT( withdrawal_request.date_added, '%Y-%m-%d' ) >= ?", array($this->getSrchVal('date_added_from')));
			$this->qry->whereRaw("DATE_FORMAT( withdrawal_request.date_added, '%Y-%m-%d' ) <= ?", array($this->getSrchVal('date_added_to')));
		}
		elseif($this->getSrchVal('date_added_from') && !$this->getSrchVal('date_added_to'))
		{
			$this->qry->whereRaw("DATE_FORMAT( withdrawal_request.date_added, '%Y-%m-%d' ) = ?", array($this->getSrchVal('date_added_from')));
		}
		elseif(!$this->getSrchVal('date_added_from') && $this->getSrchVal('date_added_to'))
		{
			$this->qry->whereRaw("DATE_FORMAT( withdrawal_request.date_added, '%Y-%m-%d' ) <= ?", array($this->getSrchVal('date_added_to')));
		}*/

		if($this->getSrchVal('request_status') != '' && $this->getSrchVal('request_status') != 'All')
		{
			$withdraw_obj->setFilterStatus($this->getSrchVal('request_status'));
			//$this->qry->Where('withdrawal_request.status', $this->getSrchVal('request_status'));
		}
		if($this->getSrchVal('request_id') != '')
		{
			$withdraw_obj->setFilterWithdrawId($this->getSrchVal('request_id'));
			//$this->qry->Where('withdrawal_request.id', $this->getSrchVal('request_id'));
		}
		/*$this->qry->groupBy('withdrawal_request.id');
		$this->qry->orderBy('withdrawal_request.id', 'DESC');
		return $this->qry;*/
	}

	public function fetchWithdrawRequestDetails($request_id, $withdraw_obj)
	{
		$req_details_arr = array();
		$withdraw_obj->setFilterWithdrawId($request_id);
		$req_details = $withdraw_obj->getWithdrwalRequests('get');
		if(count($req_details) > 0) {
			foreach($req_details as $req) {
				$req_details_arr['user_id'] = $req['user_id'];
				$req_details_arr['amount'] = $req['amount'];
				$req_details_arr['currency'] = $req['currency'];
				$req_details_arr['fee'] = $req['fee'];
				$req_details_arr['status'] = $req['status'];
				$req_details_arr['withdraw_id'] = $req['withdraw_id'];
				$req_details_arr['payment_type_del'] = $req['payment_type_del'];
				$req_details_arr['payment_type'] = $req['payment_type'];
				$req_details_arr['available_balance'] = $req['available_balance'];
				$req_details_arr['pay_to_user_account'] = $req['pay_to_user_account'];
				$req_details_arr['paid_notes'] = $req['paid_notes'];
				$req_details_arr['admin_notes'] = $req['admin_notes'];
				$req_details_arr['set_as_paid_by'] = $req['set_as_paid_by'];
				$req_details_arr['cancelled_reason'] = $req['cancelled_reason'];
				$req_details_arr['cancelled_by'] = $req['cancelled_by'];
				$req_details_arr['site_transaction_id'] = $req['site_transaction_id'];
				$req_details_arr['date_paid'] = $req['date_paid'];
				$req_details_arr['date_cancelled'] = $req['date_cancelled'];
				$req_details_arr['added_date'] = $req['added_date'];
			}
		}
		return $req_details_arr;

		/*$req_details = WithdrawalRequest::where('id', $request_id)->first();
		return $req_details;*/
	}

	public function updateWithdrawRequest($input)
	{
		$request_id = $input['request_id'];
		$request_action = $input['request_action'];
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$withdraw_obj = Credits::initializeWithDrawal();
		if($request_action == 'set_as_paid')
		{
			$det = $this->fetchWithdrawRequestDetails($request_id, $withdraw_obj);
			//check if balance available
			//$det = WithdrawalRequest::Select('amount', 'fee', 'user_id', 'currency')->where('id', $request_id)->first();
			if(count($det) > 0)
			{
				if(Credits::checkIsAllowedWithdrawalAmount($det['user_id'], $det['amount'], $det['currency']))
				{

					$sth = new SiteTransactionHandlerService();
					$txn_id = $sth->handlePaidWithdrawalRequest($request_id);

					$withdraw_obj->setWithDrawId($request_id);
					$withdraw_obj->setPaidNotes($input['paid_notes']);
					$withdraw_obj->setAdminNotes($input['admin_notes']);
					$withdraw_obj->setPaidBy($logged_user_id);
					$withdraw_obj->setStatus('Paid');
					$withdraw_obj->setSiteTransactionId($txn_id);
					$withdraw_obj->setDatePaid(new DateTime);
					$withdrwal_requests = $withdraw_obj->addWithdrawalRequest();

					/*$update_arr['paid_notes'] 		= $input['paid_notes'];
					$update_arr['admin_notes'] 		= $input['admin_notes'];
					$update_arr['set_as_paid_by'] 	= $logged_user_id;
					$update_arr['status'] 			= 'Paid';
					$update_arr['site_transaction_id'] = $txn_id;
					$update_arr['date_paid'] 		= new DateTime;
					WithdrawalRequest::where('id', $request_id)->update($update_arr);*/
				}
				else
				{
					return 'Insufficient balance in account';
				}
			}
		}
		elseif($request_action == 'decline')
		{
			$withdraw_obj->setWithDrawId($request_id);
			$withdraw_obj->setAdminNotes($input['admin_notes']);
			$withdraw_obj->setCancelledReason($input['cancelled_reason']);
			$withdraw_obj->setCancelledBy($logged_user_id);
			$withdraw_obj->setStatus('Cancelled');
			$withdraw_obj->setDateCancelled(new DateTime);
			$withdrwal_requests = $withdraw_obj->addWithdrawalRequest();

			/*$update_arr['admin_notes'] 		= $input['admin_notes'];
			$update_arr['cancelled_reason'] = $input['cancelled_reason'];
			$update_arr['cancelled_by'] 	= $logged_user_id;
			$update_arr['status'] 			= 'Cancelled';
			$update_arr['date_cancelled'] 	= new DateTime;
			WithdrawalRequest::where('id', $request_id)->update($update_arr);*/
		}
		//$this->sendNotificationOnWithdrawRequestDetailsUpdate($request_id, $request_action);
		return '';
	}

	public function sendNotificationOnWithdrawRequestDetailsUpdate($request_id, $request_action)
	{
		$mailer = new AgMailer;
		$staff =  getAuthUser()->first_name.' '.getAuthUser()->last_name .'('. getAuthUser()->user_code.')';
		$staff_profile_url = CUtil::getUserDetails(getAuthUser()->user_id, 'admin_profile_url', array('first_name' => getAuthUser()->first_name, 'user_code' =>getAuthUser()->user_code ));

		$request_details = WithdrawalRequest::where('id', $request_id)->first();
		if(count($request_details) > 0)
		{
			$user_details = CUtil::getUserDetails($request_details['user_id'], array('display_name','profile_url', 'email'));

			$data = array(	'requestid'  			=> $request_id,
							'posted_user_details'   => $user_details,
							'display_name'          => $user_details['display_name'],
							'user_email'			=> $user_details['email'],
							'request_details'     	=> $request_details,
							'request_added'			=> CUtil::FMTDate($request_details['date_added'], "Y-m-d H:i:s", ""),
							'action'				=> $request_action,
							'staff_name'			=> $staff,
							'staff_profile_url'		=> $staff_profile_url);

			$mail_template = "emails.myaccount.withdrawRequestUpdatedNotifyToAdmin";
			$subject = $user_subect = '';
			if($request_action == 'set_as_paid')
			{
				$subject = str_replace("VAR_REQ_ID", $data['request_details']['id'], trans('email.withdrawRequestApprovedAdmin'));
				$user_subect = str_replace("VAR_REQ_ID", $data['request_details']['id'], trans('email.withdrawRequestApprovedUser'));

			}
			elseif($request_action == 'decline')
			{
				$subject = str_replace("VAR_REQ_ID", $data['request_details']['id'], trans('email.withdrawRequestCancelledAdmin'));
				$user_subect = str_replace("VAR_REQ_ID", $data['request_details']['id'], trans('email.withdrawRequestCancelledUser'));
			}
			$data['subject'] = $subject;
			$mailer->sendAlertMail('withdrawal_details_updated', $mail_template, $data);

			// To user
			$data['to_email'] = $data['user_email'];
			$data['subject'] = $user_subect;
			$mailer->sendUserMail('withdrawal_details_updated', "emails.myaccount.withdrawRequestUpdatedNotifyToOperator", $data);
		}
	}
	/*public function checkIsValidWithdrawalAmount($user_id, $withdraw_amount, $withdraw_currency)
	{
		$acc_bal_det = UserAccountBalance::where('user_id', $user_id)->where('currency', $withdraw_currency)->first();
		if(count($acc_bal_det) > 0)
		{
			if(round($acc_bal_det['cleared_amount'], 2) >= $withdraw_amount)
				return true;
		}
		return false;
	}*/

}