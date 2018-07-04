<?php
class MyAccountListingService extends ProductService
{
	private $logged_user_id = 0;

	function __construct()
	{
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
    }

    public function fetchuserAccountBalance($credit_obj, $user_id = '')
	{
		$user_id = ($user_id == '') ? $this->logged_user_id : $user_id;
		$credit_obj->setUserId($user_id);
		$credit_obj->setFilterUserId($user_id);
		$credit_obj->setFilterGroupBy('currency');
		$user_account_balance = $credit_obj->getWalletAccountBalance();
		if(count($user_account_balance) > 0){
			foreach($user_account_balance as $key => $val) {
				//echo '<br>Key===>'.$key.'Val====>'.$val;
				$currency_details = Products::chkIsValidCurrency($val['currency']);
				if(count($currency_details) > 0) {
					$user_account_balance[$key]['currency_symbol'] = $currency_details['currency_symbol'];
				}
			}
		}
		return $user_account_balance;
	}

	public function fetchAllowedWithdrawals()
	{
		$paypal_fee = Config::get("payment.withdraw_paypal_transaction_fee_usd");
		$wiretransfer_fee = Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
		$neft_fee = Config::get("payment.withdraw_neft_transaction_fee_inr");
		$allowed_wtihdrawal_details[] = array('method' => 'Paypal', 'description' => Lang::get('walletAccount.withdrawal_paypal_title'),
											'fee' => $paypal_fee, 'fee_currency' => 'USD');
		$allowed_wtihdrawal_details[] = array('method' => 'NEFT', 'description' => Lang::get('walletAccount.withdraw_fund_to_net_account'),
											'fee' => $neft_fee, 'fee_currency' => 'INR');
		$allowed_wtihdrawal_details[] = array('method' => 'Wire Transfer', 'description' => Lang::get('walletAccount.withdraw_directly_to_account'), 'fee' => $wiretransfer_fee, 'fee_currency' => 'USD');

		return $allowed_wtihdrawal_details;
	}

	public function fetchUserWithdrawalsRequestList($withdraw_obj)
	{
		$user_id = $this->logged_user_id;
		$withdraw_obj->setFilterUserId($user_id);
		$withdrwal_requests = $withdraw_obj->getWithdrwalRequests('paginate', 10);
		return $withdrwal_requests;
	}

	public function fetchuserAccountBalanceByCurrency($currency, $credit_obj)
	{
		$user_id = $this->logged_user_id;
		$credit_obj->setUserId($user_id);
		$credit_obj->setFilterUserId($user_id);
		$credit_obj->setFilterCurrency($currency);
		$withdrwal_requests = $credit_obj->getWalletAccountBalance();
		$amount = 0;
		if(count($withdrwal_requests) > 0) {
			foreach($withdrwal_requests as $key => $val) {
				$amount = $val['amount'];
			}
		}
		return $amount;
	}

	public function addWithdrawalRequest($input, $withdraw_obj)
	{
		$user_id = $this->logged_user_id;
		$txn_fee = 0;
		$transfer_thru = $input['transfer_thru'];
		$payment_type = '';
		if($transfer_thru == "neft") {
			$payment_type = 'NEFT';
			$txn_fee = Config::get("payment.withdraw_neft_transaction_fee_inr");
		}
		elseif($transfer_thru == "paypal") {
			$payment_type = 'Paypal';
			$txn_fee = Config::get("payment.withdraw_paypal_transaction_fee_usd");
		}
		elseif($transfer_thru == "wire_transfer") {
			$payment_type = 'WireTransfer';
			$txn_fee = Config::get("payment.withdraw_wire_transfer_transaction_fee_usd");
		}

		$withdraw_obj->setUserId($user_id);
		$withdraw_obj->setCurrency($input['withdraw_currency']);
		$withdraw_obj->setAmount($input['request_amount']);
		$withdraw_obj->setFee($txn_fee);
		$withdraw_obj->setPaymentType($payment_type);
		$withdraw_obj->setPayToUserAccountInfo($input['pay_to_details']);
		$withdraw_obj->setDateAdded(DB::raw('NOW()'));
		$withdraw_obj->setStatus('Active');
		$withdrwal_requests = $withdraw_obj->addWithdrawalRequest();
		return $withdrwal_requests;

		// Add mail entry to admin - for notifying
		// Add mail for user for information
		//$this->notifyCancelWithdrawalRequest($request_id, 'add');
	}

	public function chkIsValidCancelRequest($request_id, $withdraw_obj)
	{
		$req_details_arr = array();
		$user_id = $this->logged_user_id;
		$withdraw_obj->setFilterWithdrawId($request_id);
		$withdraw_obj->setFilterUserId($user_id);
		$withdraw_obj->setFilterStatus('Active');
		$req_details = $withdraw_obj->getWithdrwalRequests('get');
		if(count($req_details) > 0) {
			foreach($req_details as $req) {
				$req_details_arr['amount'] = $req['amount'];
				$req_details_arr['currency'] = $req['currency'];
			}
		}
		return $req_details_arr;
	}

	public function cancelWithdrawalRequest($request_id, $cancel_reason)
	{
		$withdraw_obj = Credits::initializeWithDrawal();
		$withdraw_obj->setWithDrawId($request_id);
		$withdraw_obj->setCancelledBy($this->logged_user_id);
		$withdraw_obj->setCancelledReason($cancel_reason);
		$withdraw_obj->setDateCancelled(date('Y-m-d H:i:s'));
		$withdraw_obj->setStatus('Cancelled');
		$withdraw_obj->addWithdrawalRequest();
		/*$arr['status'] = 'Rejected';
		$arr['date_cancelled'] = date('Y-m-d H:i:s');
		$arr['cancelled_reason'] = $cancel_reason;
		$arr['cancelled_by'] = $this->logged_user_id;*/
		//WithdrawalRequest::where('id', $request_id)->Update($arr);
		//$this->notifyCancelWithdrawalRequest($request_id, 'cancel');
	}
}
