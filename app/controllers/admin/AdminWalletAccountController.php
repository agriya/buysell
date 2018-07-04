<?php

class AdminWalletAccountController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
    }

	public function getIndex()
	{
		$credits_obj = Credits::initialize();
		//$user_id = BasicCUtil::getLoggedUserId();
		$credits_obj->setUserId(1);
		$credits_obj->setFilterUserId(1);
		$credits_obj->setFilterCurrency(Config::get('generalConfig.site_default_currency'));
		$account_balance_arr = $credits_obj->getWalletAccountBalance();
		if(is_string($account_balance_arr) || count($account_balance_arr) <= 0)
		{
			$account_balance_arr = array();
		}

		$this->header->setMetaTitle(trans('meta.site_wallet_account'));
		return View::make('admin.adminWalletAccount', compact('account_balance_arr'));
	}
}