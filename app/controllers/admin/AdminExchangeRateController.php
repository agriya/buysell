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
class AdminExchangeRateController extends BaseController
{
	public function getIndex()
	{
		$d_arr = array();
		$d_arr['page_title'] = trans('common.currency_exchange_rate');

		$currenciesService = new CurrenciesService();
		$inputs = Input::all();
		$currencies_list = $currenciesService->getAvailableCurrenciesList($inputs);

		$nonavail_currencies_list = $currenciesService->getNotAvailableCurrenciesList();
		$nonavail_currencies_list = array('' => trans('common.select'))+$nonavail_currencies_list;

		$this->header->setMetaTitle(trans('meta.admin_currency_exchange_rate_title'));
		$actions = array(''=>trans('common.select'), 'Delete' => trans('common.delete'), 'Active' => trans('common.active'), 'InActive' => trans('common.deactivate'));
		return View::make('admin.currenciesList', compact('d_arr', 'currencies_list', 'actions', 'nonavail_currencies_list'));

	}
	public function postAdd()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$rules = array('currency_id'=>'required|min:1');
			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$currenciesService = new CurrenciesService();
				$currency_id = $currenciesService->addCurrency($inputs['currency_id']);
				if($currency_id)
					return Redirect::action('AdminExchangeRateController@getIndex')->with('success_message', Lang::get('admin/currencies.currency_added_successfully'));
				else
					return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('admin/currencies.there_are_some_problem_in_adding_currency'));
			}
			else
				return Redirect::action('AdminExchangeRateController@getIndex')->withInput()->withErrors($validator)->with('error_message', Lang::get('admin/currencies.kindly_select_the_valid_currency'));
		} else {
			return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message',Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
	public function postAction()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$update_arr = array();
			if($inputs['action']=='activate')
			{
				$update_arr = array('status' => 'Active');
				$succ_message = Lang::get('admin/currencies.currency_activate_successfully');
			}
			if($inputs['action']=='deactivate')
			{
				$update_arr = array('status' => 'InActive');
				$succ_message = Lang::get('admin/currencies.currency_deactivate_successfully');
			}
			if($inputs['action']=='delete')
			{
				$update_arr = array('display_currency' => 'No');
				$succ_message = Lang::get('admin/currencies.currency_deleted_successfully');
			}
			if(!empty($update_arr) && isset($inputs['id']) && $inputs['id'] >0)
			{

				$currenciesService = new CurrenciesService();
				$update = $currenciesService->updateCurrency($inputs['id'], $update_arr);

				if($update)
					return Redirect::action('AdminExchangeRateController@getIndex')->with('success_message', $succ_message);
				else
					return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('admin/currencies.there_are_some_problem_in_adding_currency'));
			}
			else
				return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('admin/currencies.select_valid_action_currency_to_update'));
		}else {
			return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
	public function postBulkAction()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$currenciesService = new CurrenciesService();

			$action = $inputs['action'];
			$action_done = false;
			if(isset($inputs['ids']) && !empty($inputs['ids']))
			{
				if(in_array($action, array('Active','InActive') ))
				{
					$data = array();
					$data['status'] = ucfirst($action);
					$action_done = $currenciesService->bulkUpdateCurrencies($inputs['ids'], $data);
					$succ_message = Lang::get('admin/currencies.currency_updated_successfully');
				}
				elseif($action == 'Delete')
				{
					$action_done = $currenciesService->bulkDeleteCurrencies($inputs['ids']);
					$succ_message = Lang::get('admin/currencies.currency_deleted_successfully');
				}
				else{
					$action_done = false;
					$succ_message = Lang::get('admin/currencies.currency_updated_successfully');
				}
			}
			if($action_done)
				return Redirect::action('AdminExchangeRateController@getIndex')->with('success_message', $succ_message);
			else
				return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('admin/currencies.there_are_some_problem_in_executing_selected_action'));
		} else {
			return Redirect::action('AdminExchangeRateController@getIndex')->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
	}

	public function postUpdateStaticCurrencyExchangeRate()
	{
		Log::info('STrta');
		Log::info(print_r(Input::All(), 1));
		Log::info('End');
		if (is_numeric(Input::get('fee'))) {
			Products::updateStaticCurrencyExchangeRate(Input::All());
			return number_format(Input::get('fee'), 4, '.','');
		}
		else
			return 0;
	}
}