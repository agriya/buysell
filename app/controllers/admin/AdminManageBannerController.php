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
//@added by manikandan_133at10
class AdminManageBannerController extends BaseController
{
	function __construct()
	{
        $this->banner_service = new AdminManageBannerService();
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
			$d_arr['pageTitle'] = trans("admin/manageBanner.banner_settings");
			$d_arr['actionicon'] ='<i class="fa fa-photo"><sup class="fa fa-cog font11"></sup></i>';
			$d_arr['image_details'] = array();
		}
		else
		{
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = trans("admin/manageBanner.banner_settings");
			$d_arr['actionicon'] ='<i class="fa fa-photo"><sup class="fa fa-cog font11"></sup></i>';
			$d_arr['image_details'] 	= $this->banner_service->getBannerSettings($id);
		}
		$d_arr['id'] = $id;

		$perPage    					= 10;
		$q 								= $this->banner_service->buildBannerImageQuery();
		$details 						= $q->paginate($perPage);
		return View::make('admin.bannerSettings', compact('details', 'd_arr'));
	}

	public function postIndex()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$input = Input::All();

			$messages = $rules = array();
			if($input['settings_id'] == 0) {
				$rules['image_name'] = 'Required';
				$validator = Validator::make($input, $rules, $messages);
				if (!$validator->passes())
				{
					return Redirect::back()->withInput()->withErrors($validator);
				}
			}
			$allowed_ext = Config::get("generalConfig.user_image_uploader_allowed_extensions");
			$file = Input::file('image_name');
			if($file != '') {
				$file_size = $file->getClientSize();
				$image_ext = $file->getClientOriginalExtension();
				$allowed_size = Config::get("generalConfig.user_image_uploader_allowed_file_size");
				$allowed_size = $allowed_size * 1024; //To convert KB to Byte
				if(stripos($allowed_ext, $image_ext) === false)
				{
					$errMsg = trans("common.uploader_allow_format_err_msg");
					return Redirect::back()->withInput()->with('error_message',$errMsg);
					//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
				else if(($file_size > $allowed_size)  || $file_size <= 0)
				{
					$errMsg = trans("common.uploader_max_file_size_err_msg");
					return Redirect::back()->withInput()->with('error_message',$errMsg);
					//return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
			}

			$resp = $this->banner_service->updateBannerImage($input);
			if($resp != "")
			{
				return Redirect::back()->withInput()->with('error_message', $resp);
			}

			if($input['settings_id'] == 0)
			{
				return Redirect::to('admin/index-banner')->with('success_message', trans("admin/manageBanner.banner_added_success"));
			}
			else
			{
				return Redirect::to('admin/index-banner')->with('success_message', trans("admin/manageBanner.banner_update_success"));
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function getDeleteBanner()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$id = Input::has('id') ? Input::get('id') : 0;
			if($id) {
				$this->banner_service->deleteIndexSlider($id);
			}
			return Redirect::to('admin/index-banner')->with('success_message',trans("admin/manageBanner.banner_deleted_success"));
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/index-banner')->with('error_message',$errMsg);
		}
	}
}