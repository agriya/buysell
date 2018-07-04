<?php
class AdminSellerRequestController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
	{
		$inputs = Input::all();

		$userAccountService = new UserAccountService();
		$seller_requests = $userAccountService->getAllSellerRequest($inputs);

		$status = array('' => Lang::get('common.select'), 'NewRequest' => Lang::get('common.new_request'), 'Allowed' => Lang::get('common.allowed'), 'Rejected' => Lang::get('common.rejected'));
		$actions = array('' => Lang::get('common.select'), 'NewRequest' => Lang::get('common.new_request'), 'Allowed' => Lang::get('common.allow'), 'Rejected' => Lang::get('common.reject'));
		$view_type = (isset($inputs['view_type']) && $inputs['view_type']!='')?$inputs['view_type']:'new';
		return View::make('admin.sellerRequestsList',compact('seller_requests', 'view_type', 'status', 'actions'));
	}
	public function postBulkAction()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$inputs = Input::all();
			$userAccountService = new UserAccountService();

			$action = $inputs['action'];
			$action_done = false;
			$actions = array('NewRequest','Allowed','Rejected');
			if(isset($inputs['ids']) && (count($inputs['ids']) > 0) && in_array($action, $actions ))
			{
				$data = array();
				$data['request_status'] = $action;
				$action_done = $userAccountService->bulkSellerRequestUpdate($inputs['ids'], $data);
			}
			else
				$action_done = false;

			if($action_done)
				return Redirect::back()->with('success_message','Requests updated successfully');
			else
				return Redirect::back()->with('error_message','There are some problem in executing selected action');
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}
	public function getRequestAction(){
		if(!BasicCUtil::checkIsDemoSite()){
			$inputs = Input::all();
			if(isset($inputs['action']) && $inputs['action']=='allow')
			{
				$userAccountService = new UserAccountService();
				$data = array();
				$request_id = $inputs['request_id'];
				$data['request_status'] = 'Allowed';

				$update = $userAccountService->updateSellerRequest($request_id,$data);
				if($update)
					return Redirect::back()->with('success_message','Requests updated successfully');
				else
					return Redirect::back()->with('error_message','There are some problem in executing selected action');
			}
			else
				return Redirect::back()->with('error_message','There are some problem in executing selected action');
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}
	public function postRequestAction(){
		if(!BasicCUtil::checkIsDemoSite()){
			$inputs = Input::all();
			if(isset($inputs['action']) && $inputs['action']=='send_reply')
			{
				$request_id = $inputs['request_id'];
				//echo "<pre>";print_r($inputs);echo "</pre>";exit;
				$data['reply_sent'] = 'Yes';
				$data['reply_message'] = (isset($inputs['comment']) && $inputs['comment']!='')?$inputs['comment']:'';

				$userAccountService = new UserAccountService();
				$update = $userAccountService->updateSellerRequest($request_id, $data);
				if($update)
					return Redirect::back()->with('success_message','Reply sent successfully');
				else
					return Redirect::back()->with('error_message','There are some problem in sending reply');
			}
			else
				return Redirect::back()->with('error_message','There are some problem in sending reply');
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}
}
?>