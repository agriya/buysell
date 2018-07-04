<?php

class AdminSiteBannerController extends BaseController
{
	function __construct()
	{
        $this->banner_service = new AdminSiteBannerService();
        parent::__construct();
    }

	/*function getIndex(){

	}*/
	public function getIndex()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0)
		{
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = trans('admin/accountmenu.manage_banner');
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-plus"></sup></i>';
			$d_arr['banner_details'] = array();
		}
		else
		{
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = trans('admin/accountmenu.manage_banner');
			$d_arr['actionicon'] ='<i class="fa fa-cogs"><sup class="fa fa-pencil font11"></sup></i>';
			$d_arr['banner_details'] 	= $this->banner_service->getSiteBannerDetails($id);
			if(count($d_arr['banner_details']) > 0)
			{
				$d_arr['banner_details']['start_date'] = CUtil::FMTDate($d_arr['banner_details']['start_date'], 'Y-m-d H:i:s', 'Y-m-d');
				$d_arr['banner_details']['end_date'] = CUtil::FMTDate($d_arr['banner_details']['end_date'], 'Y-m-d H:i:s', 'Y-m-d');
			}
			else
			{
				return Redirect::to('admin/manage-banner')->with('error_message', trans('admin/manageSiteBanner.invalid_banner_id'));
			}
		}
		$d_arr['id'] = $id;

		$perPage    					= 10;
		$q 								= $this->banner_service->buildSiteBannerQuery();
		$details 						= $q->paginate($perPage);
		$banner_block = Config::get("generalConfig.banner_position_arr");
		return View::make('admin.manageBanner', compact('details', 'd_arr', 'banner_block'));
	}

	public function postIndex()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$input = Input::All();
			if($input['banner_id'])
			{
				$banner_details 	= $this->banner_service->getSiteBannerDetails($input['banner_id']);
				if(count($banner_details) == 0)
				{
					return Redirect::to('admin/manage-banner')->with('error_message', trans('admin/manageSiteBanner.invalid_banner_id'));
				}
			}

			$date_format = 'Y-m-d';
			$curr_date = date('Y-m-d');

			$messages = $rules = array();
			$rules['block'] = 'Required';
			$rules['source'] = 'Required';
			$rules['about'] = 'Required';
			$rules['start_date'] = 'Required|date_format:'.$date_format.'|CustEqualOrAfter:'.$curr_date.','.$input['start_date'];
			$rules['end_date'] = 'Required|date_format:'.$date_format.'|CustEqualOrAfter:'.$input['start_date'].','.$input['end_date'];
			$rules['allowed_impressions'] = 'Numeric';
			$rules['status'] = 'Required';

			$messages['start_date.cust_equal_or_after'] = trans('admin/manageSiteBanner.start_date_err');
			$messages['end_date.cust_after'] = trans('admin/manageSiteBanner.end_date_err');

			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$this->banner_service->updateSiteBanner($input);

			if($input['banner_id'] == 0)
			{
				return Redirect::to('admin/manage-banner')->with('success_message', trans('admin/manageSiteBanner.add_success_msg'));
			}
			else
			{
				return Redirect::to('admin/manage-banner')->with('success_message', trans('admin/manageSiteBanner.edit_success_msg'));
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/manage-banner')->with('error_message',$errMsg);
		}
	}

	public function getDeleteBanner()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$id = Input::has('id') ? Input::get('id') : 0;
			if($id) {
				$this->banner_service->deleteSiteBanner($id);
			}
			return Redirect::to('admin/manage-banner')->with('success_message', trans('admin/manageSiteBanner.delete_success_msg'));
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/manage-banner')->with('error_message',$errMsg);
		}
	}

	public function getViewBannerPositions()
	{
		$details = Config::get("generalConfig.banner_position_arr");
		return View::make('admin.bannerPosition', compact('details'));
	}

	public function getBannerCode()
	{
		$id = Input::has('id') ? Input::get('id') : 0;
		if($id)
		{
			$banner_details = $this->banner_service->getSiteBannerDetails($id);
			if(count($banner_details) > 0)
			{
				$block = $banner_details['block'];
				$code = "{{ getAdvertisement('".$block."') }}";
				return View::make('admin.bannerCode', compact('code'));
			}
		}
		return false;
	}
}