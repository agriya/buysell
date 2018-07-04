<?php
//@added by ravikumar_131at10
class AdminWithdrawalController extends BaseController
{
	/*protected $isStaffProtected = 1;
   	protected $page_arr = array('getindex' => 'withdrawal_list');
   	protected $action_arr = array('getUpdateRequest' => 'withdrawal_change_status', 'postUpdateRequest', 'withdrawal_change_status');*/

	function __construct()
	{
		parent::__construct();
        $this->manageWithdrawalService = new AdminManageWithdrawalService();

    }

    public function getIndex()
	{
		$withdraw_obj = Credits::initializeWithDrawal();
		$request_details = $d_arr = $details = array();
		$d_arr['request_status_arr'] = array("All" => trans('common.all'), "Active" => trans('common.active'), "Paid" => trans('common.paid'), "Cancelled" => trans('common.cancelled'));
		$d_arr['allow_update_request'] = true;

		$default_status = 'All';
		$this->manageWithdrawalService->setWithdrawalFilterArr();
		$this->manageWithdrawalService->setWithdrawalSrchArr(Input::All(),$default_status);
		$this->manageWithdrawalService->buildWithdrawalQuery($withdraw_obj);

		$request_details = $withdraw_obj->getWithdrwalRequests('paginate', Config::get('generalConfig.request_per_page_list'));
		$this->header->setMetaTitle(trans('meta.withdrawal_request_list'));
		return View::make('admin/listWithdrawalRequests', compact('request_details', 'd_arr'));
	}


	public function getUpdateRequest($request_id = '', $request_action)
	{
		if($request_id != '')
		{
			$withdraw_obj = Credits::initializeWithDrawal();
			$request_details = $this->manageWithdrawalService->fetchWithdrawRequestDetails($request_id, $withdraw_obj);
			$error_msg = 'Invalid request ID';
			if(count($request_details) > 0)
			{
				if($request_details['status'] == 'Active')
				{
					$error_msg = '';
				}
				else
				{
					$error_msg = 'Invalid access, request already paid / declined';
				}
			}
			return View::make('admin/manageWithdrawalAction', compact('request_id', 'request_details', 'error_msg', 'request_action'));
		}
	}

	public function postUpdateRequest()
	{

		if(Input::has('request_id') && Input::get('request_id') != '' && Input::get('request_action') != '')
		{
			$request_id = Input::get('request_id');
			$request_action = Input::get('request_action');
			if(BasicCUtil::checkIsDemoSite()) {
				$error_msg = Lang::get('common.demo_site_featured_not_allowed');
				return View::make('admin/manageWithdrawalAction', compact('error_msg', 'request_action'));
			}
			$messages = $rules = array();
			$rules['admin_notes'] = 'required';
			if($request_action == "set_as_paid")
			{
				$rules['paid_notes'] = 'required';
			}
			else
			{
				$rules['cancelled_reason'] = 'required';
			}
			$v = Validator::make(Input::all(), $rules, $messages);
			if($v->passes())
			{
				$input = Input::all();
				$error_msg = $this->manageWithdrawalService->updateWithdrawRequest($input);
				if($error_msg == '')
				{
					$this->sendWithdrawalMailToUser($request_id, $request_action);
					$this->sendWithdrawalMailToAdmin($request_id, $request_action);
					$success_msg = trans("admin/manageWithdrawals.withdrawallist_updated_succ_msg");
					return View::make('admin/manageWithdrawalAction', compact('success_msg', 'request_action'));
				}
				else
				{
					return View::make('admin/manageWithdrawalAction', compact('error_msg', 'request_action'));
				}
			}
			else
			{
				$error_msg = trans("admin/manageWithdrawals.withdrawallist_updated_err_msg");
				return View::make('admin/manageWithdrawalAction', compact('error_msg', 'request_action'));
			}
		}
	}
	public function sendWithdrawalMailToUser($request_id, $request_action = 'set_as_paid')
	{
		$withdraw_obj = Credits::initializeWithDrawal();
		$request_details = $this->manageWithdrawalService->fetchWithdrawRequestDetails($request_id, $withdraw_obj);
		if($request_details) {
			if($request_details['status'] == 'Paid') {
				$subject = Lang::get('email.withdrawal_amount_has_been_approved');
				$msg = Lang::get('email.withdrawal_amount_has_been_approved');
			}else{
				$subject = Lang::get('email.withdrawal_request_cancelled');
				$msg = Lang::get('email.withdrawal_request_cancelled');
			}
			$subject = Config::get('generalConfig.site_name')." - ".$subject;
			$user_details = CUtil::getUserDetails($request_details['user_id']);
			$data = array(
				'withdraw_id' => $request_details['withdraw_id'],
				'currency' => $request_details['currency'],
				'status' => $request_details['status'],
				'admin_notes' => $request_details['admin_notes'],
				'amount' => $request_details['amount'],
				'withdraw_id' => $request_details['withdraw_id'],
				'payment_type_del' => $request_details['payment_type_del'],
				'available_balance' => $request_details['available_balance'],
				'pay_to_user_account' => $request_details['pay_to_user_account'],
				'paid_notes' => $request_details['paid_notes'],
				'set_as_paid_by' => $request_details['set_as_paid_by'],
				'date_paid' => $request_details['date_paid'],
				'added_date' => $request_details['added_date'],
				'cancelled_reason' => $request_details['cancelled_reason'],
				'user_name'	 => $user_details['user_name'],
				'user_email'	 => $user_details['email'],
				'subject' =>  $subject,
				'msg' => $msg,
			);
			try {
				//Mail to User
				Mail::send('emails.withdrawalPaidAndDeclineMailToUser', $data, function($m) use ($data) {
						$m->to($data['user_email']);
						$m->subject($data['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}
	public function sendWithdrawalMailToAdmin($request_id, $request_action = 'set_as_paid')
	{
		$withdraw_obj = Credits::initializeWithDrawal();
		$request_details = $this->manageWithdrawalService->fetchWithdrawRequestDetails($request_id, $withdraw_obj);
		if($request_details) {
			if($request_details['status'] == 'Paid') {
				$user_details = CUtil::getUserDetails($request_details['set_as_paid_by']);
				$subject = Config::get('generalConfig.site_name')." - ".Lang::get('email.withdrawal_amount_has_been_approved_by').' '.$user_details['user_name'];
				$msg = Config::get('generalConfig.site_name')." - ".Lang::get('email.withdrawal_amount_has_been_approved_by').' '.$user_details['user_name'];
			}else{
				$user_details = CUtil::getUserDetails($request_details['cancelled_by']);
				$subject = Config::get('generalConfig.site_name')." - ".Lang::get('email.withdrawal_request_cancelled_by').' '.$user_details['user_name'];
				$msg = Config::get('generalConfig.site_name')." - ".Lang::get('email.withdrawal_request_cancelled_by').' '.$user_details['user_name'];
			}
			$subject = Config::get('generalConfig.site_name')." - ".$subject;
			$data = array(
				'withdraw_id' => $request_details['withdraw_id'],
				'currency' => $request_details['currency'],
				'status' => $request_details['status'],
				'admin_notes' => $request_details['admin_notes'],
				'amount' => $request_details['amount'],
				'withdraw_id' => $request_details['withdraw_id'],
				'payment_type_del' => $request_details['payment_type_del'],
				'available_balance' => $request_details['available_balance'],
				'pay_to_user_account' => $request_details['pay_to_user_account'],
				'paid_notes' => $request_details['paid_notes'],
				'set_as_paid_by' => $request_details['set_as_paid_by'],
				'date_paid' => $request_details['date_paid'],
				'added_date' => $request_details['added_date'],
				'user_name'	 => $user_details['user_name'],
				'user_email'	 => Config::get("generalConfig.invoice_email"),//$user_details['email'],
				'subject' => $subject,
				'msg' => $msg,
			);
			try {
				//Mail to User
				Mail::send('emails.withdrawalPaidAndDeclineMailToAdmin', $data, function($m) use ($data) {
						$m->to($data['user_email']);
						$m->subject($data['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}
		}
	}
}