<?php

class AdminStaticPageController extends BaseController
{

	//Taxations
	public function getIndex($page_id=null)
	{
		$lists_arr = array();
		$service = new StaticPageService();
		$lists_arr = $service->getLists();

		$page_details = array();
		if(!is_null($page_id) && $page_id > 0)
		{
			$page_details = $service->getPageDetails($page_id);
		}

		$page_type = array(''=> trans('common.select'),'static' => trans('common.static'), 'external' => trans('common.external_link'));
		$actions = array(''=> trans('common.select'),'activate' => trans('common.activate'), 'deactivate' => trans('common.deactivate'), 'delete' => trans('common.delete'));
		return View::make('admin.staticPagesList', compact('lists_arr', 'page_id', 'page_type', 'actions', 'page_details'));

	}
	public function postIndex($page_id=null)
	{
		$service = new StaticPageService();
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$message = '';
			$rules = array('page_type'=>'required', 'content' => 'required_if:page_type,static', 'external_link' => 'required_if:page_type,external|url', 'page_name' => 'required|unique:static_pages,page_name,'.$page_id.',id', 'title' => 'required_if:page_type,static', 'display_in_footer' => 'required', 'status' => 'required');
			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				if(!is_null($page_id) && $page_id >0)
				{
					$output = $service->updateStaticPage($inputs,$page_id);
					if($output)
						return Redirect::action('AdminStaticPageController@getIndex')->with('success_message', trans('admin/staticPage.static_page_updated_successfully'));
					else
						return Redirect::back()->with('error_message', trans('admin/staticPage.there_are_some_problem_in_updating_static_page'))->withInput();
				}
				else
				{
					$output = $service->addStaticPage($inputs);
					if($output)
						return Redirect::action('AdminStaticPageController@getIndex')->with('success_message', trans('admin/staticPage.static_page_updated_successfully'));
					else
						return Redirect::back()->with('error_message', trans('admin/staticPage.there_are_some_problem_in_adding_static_page'))->withInput();
				}
			}
			else
			{
				return Redirect::back()->with('error_message', trans('admin/staticPage.kindly_fix_the_following_issues'))->withInput()->withErrors($validator);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->with('error_message',$errMsg);
		}
	}
	public function postAction(){
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$err_msg='';
			$success_msg = '';
			if(isset($inputs['action']) && isset($inputs['id']) && $inputs['id'] > 0)
			{
				$action = $inputs['action'];
				$id = $inputs['id'];
				$service = new StaticPageService();
				switch($action)
				{
					case 'delete':
						$deleted = $service->deleteStaticPage($id);
						if($deleted)
							$success_msg = trans('admin/staticPage.deleted_successfully');
						else
							$err_msg = trans('admin/staticPage.some_problem_in_deleting_page');
						break;
				}
			}
			else
				$err_msg = 'Select valid Action';
			if($err_msg=='')
				return Redirect::action('AdminStaticPageController@getIndex')->with('success_message',$success_msg);
			else
				return Redirect::action('AdminStaticPageController@getIndex')->with('error_message',$err_msg);
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminStaticPageController@getIndex')->with('error_message',$errMsg);
		}
	}
	public function postBulkAction()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$err_msg='';
			$success_msg = '';
			if(isset($inputs['action']) && $inputs['action']!='' && isset($inputs['ids']) && !empty($inputs['ids']))
			{
				$ids = $inputs['ids'];
				$action = $inputs['action'];
				$service = new StaticPageService();

				$data = array();
				switch($action)
				{
					case 'activate':
					case 'deactivate':
						$data['status'] = ($action=='activate')?'Active':'Inactive';
						$update = $service->updateStaticpage($data,$ids);
						if($update)
							$success_msg = trans('admin/staticPage.pages_updated_successfully');
						else
							$err_msg = trans('admin/staticPage.there_are_some_problem_in_updating_static_page');
						break;

					case 'delete':
						$deleted = $service->deleteStaticPage($ids);
						if($deleted)
							$success_msg = trans('admin/staticPage.pages_deleted_successfully');
						else
							$err_msg = trans('admin/staticPage.there_are_some_problem_in_deleting_static_page');
						break;

					default :
						$err_msg = trans('admin/staticPage.select_valid_action');
						break;

				}
			}
			else
				$err_msg = trans('admin/staticPage.select_valid_action_and_pages');

			if($err_msg=='')
				return Redirect::action('AdminStaticPageController@getIndex')->with('success_message',$success_msg);
			else
				return Redirect::action('AdminStaticPageController@getIndex')->with('error_message',$err_msg);
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminStaticPageController@getIndex')->with('error_message',$errMsg);
		}
	}

	public function getSellStaticPage()
	{
		$service = new StaticPageService();
		$static_page_content = $service->getSellPageStaticContent();
		return View::make('admin.sellStaticPage', compact('static_page_content'));
	}

	public function postSellStaticPage()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$rules = array('page_title' => 'required', 'what_can_you_sell' => 'required', 'how_doest_it_work' => 'required');
			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$service = new StaticPageService();
				$static_page_content = $service->getSellPageStaticContent();
				if(count($static_page_content) > 0)
				{
					$update = $service->updateSellPageStaticContent($static_page_content['id'], $inputs);
				}
				else
				{
					$update = $service->addSellPageStaticContent($inputs);
				}
				if($update)
					return Redirect::action('AdminStaticPageController@getSellStaticPage')->with('success_message', trans('admin/staticPage.sell_page_static_content_updated_successfully'));
				else
					return Redirect::action('AdminStaticPageController@getSellStaticPage')->with('error_message',trans('admin/staticPage.there_are_some_problem_in_updating_sell_page'));
			}
			else
			{
				return Redirect::action('AdminStaticPageController@getSellStaticPage')->withInput()->withErrors($validator)->with('error_message', trans('admin/staticPage.there_are_some_problem_in_updating_sell_page'));
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminStaticPageController@getSellStaticPage')->with('error_message',$errMsg);
		}
	}

}