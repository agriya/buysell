<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
  namespace App\Plugins\Sudopay\Controllers;

use BasicCUtil, URL, DB, Lang, View, Input, Validator, Products, Config;
use Session, Redirect, BaseController;

class AdminSudopayController extends \BaseController
{
	public function __construct()
	{
		parent::__construct();
		if(!\CUtil::chkIsAllowedModule('sudopay'))
		{
			return Redirect::to('/admin');
		}
		$this->sudopay_service = new \SudopayService();
		$mode = (Config::get('plugin.sudopay_payment_test_mode')) ? 'test' : 'live';
		$sudopay_credential = array(
		    'api_key' => Config::get('plugin.sudopay_'.$mode.'_api_key'),
		    'merchant_id' => Config::get('plugin.sudopay_'.$mode.'_merchant_id'),
		    'website_id' => Config::get('plugin.sudopay_'.$mode.'_website_id'),
		    'secret' => Config::get('plugin.sudopay_'.$mode.'_secret_string')
		);
		$this->sa = new \SudoPay_API($sudopay_credential);
		$this->logged_user_id = BasicCUtil::getLoggedUserId();
	}

	public function getManagePaymentGateways()
	{
		$details = $d_arr = array();
		$d_arr['pageTitle'] = Lang::get('sudopay::sudopay.payment_gateways');
		return View::make('sudopay::admin.paymentGateways', compact('d_arr'));
	}

	public function getEditPaymentGateways()
	{
		$type = Input::has('type') ? Input::get('type') : '';
		$valid_types = array('sudopay', 'wallet');
		if(!in_array($type, $valid_types)) {
			return Redirect::to('admin/sudopay/manage-payment-gateways')->with('error_message', Lang::get('sudopay::sudopay.invalid_action'));
		}

		$details = $d_arr = array();
		$input = Input::All();

		$d_arr['mode'] 		= 'edit';
		$d_arr['pageTitle'] = ($type == 'sudopay') ? Lang::get('sudopay::sudopay.edit_payment_gateway_sudopay') : Lang::get('sudopay::sudopay.edit_payment_gateway_wallet');
		$d_arr['actionicon'] ='<i class="fa fa-edit"></i>';
		$d_arr['plan_details'] = $this->sudopay_service->getPlanDetails($this->sa);
		$d_arr['enabled_gateways'] = $this->sudopay_service->getPaymentGatewayDetails($this->sa);
		$d_arr['type'] = $type;
		$sudopay_live = $this->checkSudopayLive();
		$sudopay_test = $this->checkSudopayTest();
		return View::make('sudopay::admin.editPaymentGateways', compact('details', 'd_arr', 'sudopay_live', 'sudopay_test'));
	}

	public function checkSudopayLive()
	{
		if(Config::get("plugin.sudopay_live_merchant_id") != '' && Config::get("plugin.sudopay_live_website_id") != '' && Config::get("plugin.sudopay_live_secret_string") != '' && Config::get("plugin.sudopay_live_api_key") != ''){
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}

	public function checkSudopayTest()
	{
		if(Config::get("plugin.sudopay_test_merchant_id") != '' && Config::get("plugin.sudopay_test_website_id") != '' && Config::get("plugin.sudopay_test_secret_string") != '' && Config::get("plugin.sudopay_test_api_key") != ''){
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}

	public function postEditPaymentGateways()
	{
		$type = Input::has('type') ? Input::get('type') : '';
		$valid_types = array('sudopay', 'wallet');
		if(!in_array($type, $valid_types)) {
			return Redirect::to('admin/sudopay/manage-payment-gateways')->with('error_message', Lang::get('sudopay::sudopay.invalid_action'));
		}

		$input = Input::All();
		$messages = $rules = array();
		$validator = Validator::make($input, $rules, $messages);

		if($type == 'sudopay')
			$chk_box_arr = array('sudopay_payment_test_mode', 'sudopay_payment_used_product_purchase', 'sudopay_payment_used_addtowallet');
		else
			$chk_box_arr = array('wallet_payment_used_product_purchase');
		foreach($chk_box_arr as $config_var) {
			if(!isset($input[$config_var]))
				$input[$config_var] = 0;
		}

		if (!$validator->passes()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$json_res = $this->sudopay_service->updatePaymentGatewayDetails($input);
		$json_data = json_decode($json_res , true);
		if(isset($json_data['status']) && $json_data['status'] == 'error') {
			return Redirect::back()->withInput()->withErrors($validator)->with('success_message',$json_data['error_message']);
		}
		else {
			return Redirect::to('admin/sudopay/edit-payment-gateways?type='.$type)->with('success_message',Lang::get('sudopay::sudopay.payment_settings_updated_successfully'));
		}
	}

	public function postSynchronizeWithSudopay()
	{
		$payment_gateway_details = $this->sudopay_service->getPaymentGatewayDetailsLive($this->sa);
		return 'success';exit;
	}

	public function getChangeStatus()
	{
		$action = $success_msg = '';
		if(Input::has('config_var') && Input::has('config_var')) {
			$config_var = Input::get('config_var');
			$action = Input::get('action');
			$success_msg = $this->sudopay_service->updateConfigStatus('plugin', $config_var, $action);
		}
		Session::flash('success', $success_msg);
		return Redirect::to('admin/sudopay/manage-payment-gateways');
	}

	public function getChangeStatusPayment()
	{
		$action = $success_msg = '';
		if(Input::has('config_var') && Input::has('config_var')) {
			$config_var = Input::get('config_var');
			$action = Input::get('action');
			$success_msg = $this->sudopay_service->updateConfigStatus('payment', $config_var, $action);
		}
		Session::flash('success', $success_msg);
		return Redirect::to('admin/sudopay/manage-payment-gateways');
	}

	public function getSudopayTransactionList()
	{
		$this->header->setMetaTitle(trans('sudopay::sudopay.sudopay_transaction_list'));
		$display_transactions = DB::table('sudopay_transaction_logs')
								->orderBy('id', 'DESC')
								->paginate(20);
		return View::make('sudopay::admin.sudopayTransactionList', compact('display_transactions'));
	}

	public function getSudopayIpnLogs()
	{
		$user_list = $user_details = array();

		$q = $this->sudopay_service->buildSudopayIpnLogsQuery();
		$page 		= (Input::has('page')) ? Input::get('page') : 1;
		$start 		= (Input::has('start')) ? Input::get('start') : 20;
		$perPage	= 20;
		$ipn_log_list 	= $q->paginate($perPage);
		$this->header->setMetaTitle(trans('sudopay::sudopay.sudopay_ipn_logs'));
		return View::make('sudopay::admin.sudopayIpnLogs', compact('ipn_log_list'));
	}
}