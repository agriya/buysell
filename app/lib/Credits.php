<?php

class Credits {

	public static function initialize()
	{
		return new Credit();
	}

	public static function initializeWithDrawal()
	{
		return new WithDrawal();
	}

	public static function checkIsAllowedWithdrawalCurrency($user_id, $withdraw_currency)
	{
		return true;
		$req_count = WithdrawalRequest::where('user_id', $user_id)->where('currency', $withdraw_currency)->where('status', 'Active')->count();
		if($req_count > 0)
			return false;
		return true;
	}

	public static function checkIsAllowedWithdrawalAmount($user_id, $withdraw_amount, $withdraw_currency)
	{
		$acc_bal_det = UserAccountBalance::where('user_id', $user_id)->where('currency', $withdraw_currency)->first();
		if(count($acc_bal_det) > 0)
		{
			if(round($acc_bal_det['amount'], 2) >= $withdraw_amount)
				return true;
		}
		return false;
	}
}