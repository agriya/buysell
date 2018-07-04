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
class AdminNewsletterSubscriberController extends BaseController
{
	function __construct()
	{
       	$this->newsletterSubscriberService = new AdminNewsletterSubscriberService();
    }

    public function getList()
    {
		$d_arr['pageTitle'] = trans("admin/newsletterSubscriber.title");
		$d_arr['search_entries_arr'] = trans('common.per_page');
		$perPage = (Input::has('perpage')) ? Input::get('perpage') : 10;
		$q = $this->newsletterSubscriberService->buildNewsletterSubscriberQuery();
		$details = $q->where('status', '!=', 'delete')->paginate($perPage);
		$actions = array('' => trans('common.select'), 'active' => trans('common.activate'), 'inactive' => trans('common.inactive'), 'delete' => trans('common.delete'));
		return View::make('admin/newsletterSubscriberList',compact('details', 'd_arr', 'actions'));
	}

	public function postList()
    {
    	if(!BasicCUtil::checkIsDemoSite()){
	    	if(Input::has('import_copypaste')) {
				$rules = array("subscribers_list" => 'Required');
	    	}
	    	else {
				$rules = array("subscribers_importlist" => 'Required');
			}
			$v = Validator::make(Input::all(), $rules);
			if ( $v->passes()) {
				if(Input::has('import_copypaste')) {
					$import_file_details = $this->newsletterSubscriberService->addNewsletterSubscriberCopyPasteList(Input::all());
					$msg_arr = array();
					if(count($import_file_details))	{
						$msg_arr['total_subscribers'] = $import_file_details['total_subscribers'];
						$msg_arr['imported_subscribers'] = $import_file_details['imported_subscribers'];
						$msg_arr['duplicate_subscribers'] = $import_file_details['duplicate_subscribers'];
						$msg_arr['failed_subscribers'] = $import_file_details['failed_subscribers'];
						$msg_arr['failed_emails'] = $import_file_details['failed_emails'];
						$msg_arr['duplicate_emails'] = $import_file_details['duplicate_emails'];
					}
					if(count($msg_arr) > 0 && ($msg_arr['duplicate_subscribers']  > 0 || $msg_arr['failed_subscribers'] > 0)) {
						return Redirect::to('admin/newsletter-subscriber/list')->with('msg_arr', $msg_arr);
					}
					else {
						$success_msg = trans('admin/newsletterSubscriber.import_successfully');
						return Redirect::to('admin/newsletter-subscriber/list')->with('success_msg', $success_msg);
					}
				}
				else {
					$path_parts = pathinfo($_FILES["subscribers_importlist"]["name"]);
					$extension = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
					$rules = array();
					$valid_extension = array('csv');
					if(in_array($extension, $valid_extension))	{
						$rules = array("subscribers_importlist" => 'Required|max:'.Config::get('generalConfig.import_upload_max_filesize'));
					}
					else {
						$rules = array("subscribers_importlist" => 'Required|mimes:csv|max:'.Config::get('generalConfig.import_upload_max_filesize'));
					}
					$messages['subscribers_importlist.mimes'] = trans('admin/newsletterSubscriber.invalid_file_format');
					$messages['subscribers_importlist.max'] = trans('admin/newsletterSubscriber.import_file_size_error');
					$v = Validator::make(Input::all(), $rules, $messages);
					if($_FILES['subscribers_importlist']['name'] != '' && $_FILES['subscribers_importlist']['error'] == 1)	{
						$error_msg = trans('admin/newsletterSubscriber.import_file_size_error');
						return Redirect::to('admin/newsletter-subscriber/list')->with('error_message', $error_msg);
					}
					if ( $v->passes())	{
						$import_file_details = $this->newsletterSubscriberService->addNewsletterSubscriberImportFile(Input::all());
						$msg_arr = array();
						if(count($import_file_details))	{
							$msg_arr['total_subscribers'] = $import_file_details['total_subscribers'];
							$msg_arr['imported_subscribers'] = $import_file_details['imported_subscribers'];
							$msg_arr['duplicate_subscribers'] = $import_file_details['duplicate_subscribers'];
							$msg_arr['failed_subscribers'] = $import_file_details['failed_subscribers'];
							$msg_arr['failed_emails'] = $import_file_details['failed_emails'];
							$msg_arr['duplicate_emails'] = $import_file_details['duplicate_emails'];
						}
						if(count($msg_arr) > 0 && ($msg_arr['duplicate_subscribers']  > 0 || $msg_arr['failed_subscribers'] > 0)) {
							return Redirect::to('admin/newsletter-subscriber/list')->with('msg_arr', $msg_arr);
						}
						else {
							$success_msg = trans('admin/newsletterSubscriber.import_successfully');
							return Redirect::to('admin/newsletter-subscriber/list')->with('success_msg', $success_msg);
						}
					}
					else {
						return Redirect::to('admin/newsletter-subscriber/list')->withErrors($v);
					}
				}
			}
			else {
				return Redirect::to('admin/newsletter-subscriber/list')->withInput()->withErrors($v);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/newsletter-subscriber/list')->withInput()->with('error_message',$errMsg);
		}
	}

	public function getChangeStatus()
	{
		$page = Input::get('page');
		if(!BasicCUtil::checkIsDemoSite()) {
			if(Input::has('subscriber_id') && Input::has('action'))
			{
				$subscriber_id = Input::get('subscriber_id');
				$action = Input::get('action');
				$success_msg = "";
				$success_msg = $this->newsletterSubscriberService->updateSubscriberStatus($subscriber_id, $action);
			}
			return Redirect::to('admin/newsletter-subscriber/list?page='.$page)->with('success_msg', $success_msg);
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/newsletter-subscriber/list?page='.$page)->with('error_message',$errMsg);
		}
	}

	public function getChangeStatusIds()
	{
		$success_msg = '';
		$page = Input::get('page');
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			if(isset($inputs['ids']) && (count($inputs['ids']) > 0)) {
				if(strtolower($inputs['action']) == 'active') {
					$unsubscribe_ids = Input::get('ids');
					foreach($unsubscribe_ids as $unsubscribe_id) {
						$data_arr['status'] = $inputs['action'];
						$data_arr['unsubscribe_code'] = str_random(10);
						NewsletterSubscriber::where('id', $unsubscribe_id)->update($data_arr);
						$success_msg = trans('admin/newsletterSubscriber.activated_suc_msg');
					}
				} elseif(strtolower($inputs['action']) == 'inactive') {
					$unsubscribe_ids = Input::get('ids');
					$data_arr['status'] = $inputs['action'];
					$data_arr['date_unsubscribed'] = DB::Raw('Now()');
					$data_arr['unsubscribe_code'] = '';
					NewsletterSubscriber::whereIn('id', $unsubscribe_ids)->update($data_arr);
					$success_msg = trans('admin/newsletterSubscriber.deactivated_suc_msg');

				} elseif(strtolower($inputs['action']) == 'delete') {
					$unsubscribe_ids = Input::get('ids');
					$data_arr['status'] = $inputs['action'];
					$data_arr['date_unsubscribed'] = DB::Raw('Now()');
					$data_arr['unsubscribe_code'] = '';
					NewsletterSubscriber::whereIn('id', $unsubscribe_ids)->update($data_arr);
					$success_msg = trans('admin/newsletterSubscriber.delete_suc_msg');
				}
			}
			return Redirect::to('admin/newsletter-subscriber/list?page='.$page)->with('success_msg', $success_msg);
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/newsletter-subscriber/list?page='.$page)->with('error_message',$errMsg);
		}
	}
}