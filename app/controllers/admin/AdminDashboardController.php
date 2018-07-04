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
//@added by mohamed_158at10
class AdminDashboardController extends BaseController
{
	function __construct()
	{
		$this->adminProductCategoryService = new AdminProductCategoryService();
	}
	public function getIndex()
	{
		$adminDashboardService = new AdminDashboardService();
		$member_stats_details = $adminDashboardService->getDashboardUserStats();
		$product_stats_details = $adminDashboardService->getDashboardProductStats();
		$site_earning_details = $adminDashboardService->getDashboardSiteEarningDetails();

		$credits_obj = Credits::initialize();
		$credits_obj->setUserId(1);
		$credits_obj->setFilterUserId(1);
		$account_balances = $credits_obj->getWalletAccountBalance();
		if(is_string($account_balances) || count($account_balances) <= 0) {
			$account_balance_arr = array();
		} else {
			$account_balance_arr['amount'] = $account_balances->get(0)->amount;
			$account_balance_arr['currency'] = $account_balances->get(0)->currency;
		}

		return View::make('admin.dashboard',compact('member_stats_details', 'product_stats_details', 'site_earning_details', 'account_balance_arr'));
	}
	public function getMetaDetails()
	{
		$inputs = Input::all();
		$all_meta_list = $this->adminProductCategoryService->getAllMetaList($inputs);
		$enable_edit = false;
		$meta_info = array();
		if(isset($inputs['id']) && $inputs['id']>0)
		{
			$meta_info = MetaDetails::where('id', $inputs['id'])->first();
			if(!isset($meta_info)){
				Session::flash('error', Lang::get('admin/dashboard.id_not_exits'));
				return Redirect::to('admin/meta-details');
			}
			$enable_edit = true;
		}
		$adminManageLanguageService = new AdminManageLanguageService();
		$languages_list = $adminManageLanguageService->getLanguagesListHasFolder();
		$current_language = (Session::has('admin_choose_lang'))?Session::get('admin_choose_lang'):Config::get('generalConfig.lang');
		return View::make('admin.MetaDetails', compact('all_meta_list', 'enable_edit', 'meta_info', 'languages_list', 'current_language'));
	}
	
	public function postMetaDetails()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$update = $this->adminProductCategoryService->updateMetaDetails($inputs['id'], $inputs);
			if($update)
				return Redirect::action('AdminDashboardController@getMetaDetails')->with('success_message', Lang::get('admin/manageCategory.page_meta_details_updated_successfully'));
			else
				return Redirect::back()->with('error_message',Lang::get('admin/manageCategory.problem_in_updating_meta_details'))->withInput();
			//echo "<pre>";print_r($inputs);echo "</pre>";
		} else {
			return Redirect::back()->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
	public function postAdminUpdateLanguage()
	{
		$language_name = Input::get('current_language');
		$check_exits = MetaDetails::where('language', $language_name)->get();
		if(count($check_exits) <= 0){
			$insert_query = 'insert into `meta_details` (page_name, meta_title, meta_description, meta_keyword, common_terms, language, date_added)
			select page_name, meta_title, meta_description, meta_keyword, common_terms, "'.$language_name.'", now()
			from `meta_details`
			where language = "en"';
			$update_query = DB::insert(DB::Raw($insert_query));
		}
		Session::put('admin_choose_lang', $language_name);
		return 'success';
	}
}