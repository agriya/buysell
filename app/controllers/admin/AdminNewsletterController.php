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
class AdminNewsletterController extends BaseController
{

	//Taxations
	public function getIndex($page_id=null)
	{
		$inputs = Input::all();
		$newsletterService = new NewsletterService();
		$newsletters = $newsletterService->getAllNewsLetter($inputs);

		$status = array(''=> trans('common.select'),'Pending' => trans('common.pending'), 'Started' => trans('common.started'), 'Finished' => trans('common.finished'));
		$actions = array(''=> trans('common.select'),'Finished' => trans('common.finish'), 'Pending' => trans('common.activate'));
		return View::make('admin.newslettersList', compact('newsletters', 'actions', 'status'));

	}
	public function getAdd(){

		$news_letter_details = array();
		return View::make('admin.addNewsletter', compact('news_letter_details', 'status'));
	}
	public function postAdd(){
		$inputs = Input::all();
		$rules = array('subject' => 'required', 'message' => 'required');
		$validator = Validator::make($inputs,$rules);
		if($validator->passes())
		{
			//echo "<pre>";print_r($inputs);echo "</pre>";
			$newsletterService = new NewsletterService();
			$newsletter_id = $newsletterService->addNewsletter($inputs);
			if($newsletter_id)
			{
				$users = $newsletterService->setUserDetailsFromFilter($newsletter_id, $inputs);
				return Redirect::action('AdminNewsletterController@getIndex')->with('success_message','Newsletter added successfully');
			}
			else
				return Redirect::action('AdminNewsletterController@getAdd')->withInput()->with('error_message','There are some problem in adding newsletter');
		}
		else
			return Redirect::action('AdminNewsletterController@getAdd')->withInput()->withErrors($validator)->with('error_message','Some problem in adding newsletter. Kindly check the form inputs');
	}
	public function postBulkAction(){

		$inputs = Input::all();
		$newsletterService = new NewsletterService();

		$action = $inputs['action'];
		$action_done = false;
		if(isset($inputs['ids']) && !empty($inputs['ids']))
		{
			if(in_array($action, array('Finished','Pending') ))
			{
				$data = array();
				$data['status'] = ucfirst($action);
				$action_done = $newsletterService->bulkUpdateNewsletter($inputs['ids'], $data);
			}
			else
				$action_done = false;
		}
		if($action_done)
			return Redirect::action('AdminNewsletterController@getIndex')->with('success_message','Newsletters updated successfully');
		else
			return Redirect::action('AdminNewsletterController@getIndex')->with('error_message','There are some problem in executing selected action');
	}
	public function getView($newsletter_id = null){
		if(is_null($newsletter_id) || $newsletter_id<=0)
			return false;
		$newsletterService = new NewsletterService();
		$newsletter_details = $newsletterService->getNewsletterDetails($newsletter_id);
		//return View::make('admin/viewNewsletter', compact('newsletter_details'));

	}

}