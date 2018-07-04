<?php

class AdminTaxationsController extends BaseController
{

	//Taxations
	public function getIndex($user_id = null)
	{
		$err_msg = '';
		$is_search_done = 0;
		$taxationslist = array();
		try
		{
			$admin_group_id = Config::get('generalConfig.admin_group_id');
			$adminManageUserService = new AdminManageUserService();
			$user_ids = $adminManageUserService->fetchGroupMembersLists($admin_group_id);

			$search_arr = array('user_id' => $user_ids);
			if(Input::has('tax_name') && Input::get('tax_name')!='')
				$search_arr['tax_name'] = Input::get('tax_name');

			$taxationslist = Webshoptaxation::Taxations()->getTaxations($search_arr, 'paginate');
			if(!$taxationslist) $taxationslist = array();
		}
		catch(Exception $e)
		{
			$err_msg = $e->getMessage();
		}
		$this->header->setMetaTitle(trans('meta.admin_manage_taxations_title'));
		return View::make('admin.taxationsList', compact('taxationslist', 'error_message', 'is_search_done'));

	}
	public function getAddTaxation()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = Lang::get('admin/taxation.add_taxation');
		$d_arr['mode'] = 'add';
		$d_arr['user_id'] = $user_id = BasicCUtil::getLoggedUserId();
		$tax_details = array();
		$this->header->setMetaTitle(trans('meta.admin_add_taxations_title'));
		return View::make('admin.addUpdateTaxation', compact('d_arr', 'user_id', 'tax_details'));
	}
	public function postAddTaxation()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			try
			{
				$inputs =Input::all();

				$taxatonid = Webshoptaxation::Taxations()->addTaxation($inputs, true);//true for admin mode
				if($taxatonid)
				{
					return Redirect::action('AdminTaxationsController@getIndex')->with('success_message','Taxation have been added successfully');
				}
				else{
					return Redirect::action('AdminTaxationsController@getAddTaxation')->with('error_message','Some problem in adding taxation')->withInput();
				}
			}
			catch(Exception $e)
			{
				return Redirect::action('AdminTaxationsController@getAddTaxation')->with('error_message',$e->getMessage())->withInput();
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminTaxationsController@getAddTaxation')->with('error_message',$errMsg);
		}
	}
	public function getUpdateTaxation($taxation_id = 0)
	{
		if(is_null($taxation_id) || $taxation_id <=0)
			return Redirect::action('AdminTaxationsController@getIndex')->with('error_message','Kindly select valid taxation');
		$user_id = BasicCUtil::getLoggedUserId();
		$tax_details = Webshoptaxation::Taxations()->getTaxations(array('id'=>$taxation_id), 'first');
		if(!$tax_details)
			return Redirect::action('AdminTaxationsController@getIndex')->with('error_message','Kindly select valid taxation');

		$d_arr = array();
		$d_arr['pageTitle'] = Lang::get('admin/taxation.update_taxation');
		$d_arr['mode'] = 'edit';
		$d_arr['user_id'] = $user_id = BasicCUtil::getLoggedUserId();

		$this->header->setMetaTitle(trans('meta.admin_edit_taxations_title'));
		return View::make('admin.addUpdateTaxation',compact('user_id','tax_details', 'd_arr', 'taxation_id'));
	}
	public function postUpdateTaxation($taxation_id = 0)
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			try
			{
				if($taxation_id > 0)
				{
					$inputs = Input::all();

					$taxatonid = Webshoptaxation::Taxations()->updateTaxation($taxation_id, $inputs, true);//true for admin mode
					if($taxatonid){
						return Redirect::action('AdminTaxationsController@getIndex')->with('success_message','Taxation have been updated successfully');
					}
					else{
						return Redirect::action('AdminTaxationsController@getUpdateTaxation',$taxation_id)->with('error_message','Some problem in updating taxation or no changes made')->withInput();
					}
				}
				else{
					return Redirect::action('AdminTaxationsController@getUpdateTaxation',$taxation_id)->with('error_message','Some problem in updating taxation')->withInput();
				}
			}
			catch(Exception $e)
			{
				return Redirect::action('AdminTaxationsController@getUpdateTaxation',$taxation_id)->with('error_message',$e->getMessage())->withInput();
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminTaxationsController@getUpdateTaxation',$taxation_id)->with('error_message',$errMsg);
		}
	}

	public function postDeleteTaxations()
	{
		$taxation_id = Input::get('taxation_id');
		if(!BasicCUtil::checkIsDemoSite()) {
			try
			{
				$taxation_id = Input::get('taxation_id');
				if($taxation_id > 0)
				{
					$taxaton_id = Webshoptaxation::Taxations()->deleteTaxation($taxation_id);
					if($taxaton_id)
					{
						return Redirect::action('AdminTaxationsController@getIndex')->with('success_message','Taxation have been deleted successfully');
					}
					else
					{
						return Redirect::action('AdminTaxationsController@getIndex')->with('error_message','There are some problem in deleting taxations');
					}
				}
				else
					return Redirect::action('AdminTaxationsController@getIndex')->with('error_message','Select valid taxation to delete');
			}
			catch(Exception $e)
			{
				return Redirect::action('AdminTaxationsController@getIndex')->with('error_message',$e->getMessage());
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminTaxationsController@getIndex',$taxation_id)->with('error_message',$errMsg);
		}
	}


}