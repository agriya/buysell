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
class AdminManageFeedbackController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$inputs = Input::all();

		$productFeedbackService	= new ProductFeedbackService();
		$feedbacks_list = array();
		$feedbacks_list = $productFeedbackService->getAdminFeedbacks($inputs);
		if($feedbacks_list && !empty($feedbacks_list))
		{
			$perPage = 10;
			$currentPage = Input::get('page') - 1;
			$pagedData = array_slice($feedbacks_list, $currentPage * $perPage, $perPage);
			$feedbacks_list = Paginator::make($pagedData, count($feedbacks_list), $perPage);
		}

		$productService = new ProductService();
		$actions = array(''=> trans('common.select'),'delete' => trans('common.delete'), 'positive' => trans('feedback.set_as_positive'), 'neutral' => trans('feedback.set_as_neutral'), 'negative' => trans('feedback.set_as_negative'));
		$status = array(''=>trans('common.select'),'positive' => trans('feedback.positive'), 'neutral' => trans('feedback.neutral'), 'negative' => trans('feedback.negative'));
		return View::make('admin.feedbacksList', compact('feedbacks_list','productService', 'actions', 'status'));
    	//
	}
	public function postBulkAction()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$inputs = Input::all();
			$productFeedbackService	= new ProductFeedbackService();
			if(isset($inputs['ids']) && $inputs['ids'] !=''){
				$action = $inputs['action'];
				$action_done = false;
				if(in_array($action, array('positive','negative', 'neutral') ))
				{
					$data = array();
					$data['feedback_remarks'] = ucfirst($action);
					$action_done = $productFeedbackService->bulkUpdateFeedabck($inputs['ids'], $data);
				}
				elseif($action == 'delete')
				{
					$action_done = $productFeedbackService->bulkDeleteFeedabck($inputs['ids']);
				}
				else
					$action_done = false;

				if($action_done)
					return Redirect::back()->with('success_message',trans('admin/feedback.feedbacks_updated_successfully'));
				else
					return Redirect::back()->with('error_message',trans('admin/feedback.there_are_some_problem'));
			}else{
				return Redirect::back()->with('error_message',trans('admin/feedback.please_select_checkbox'));
			}
		} else {
			return Redirect::back()->with('error_message',Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
	public function postFeedbackAction()
	{
		if(!BasicCUtil::checkIsDemoSite()){
			$inputs = Input::all();
			if($inputs['action']=='edit_feedback')
			{
				$productFeedbackService	= new ProductFeedbackService();
				$data = array();
				$feedback_id = $inputs['feedback_id'];
				$data['feedback_remarks'] = $inputs['status'];
				$data['feedback_comment'] = $inputs['comment'];
				$data['rating'] = $inputs['rating'];
				$update = $productFeedbackService->updateFeedabck($feedback_id,$data);
				if($update)
					echo json_encode(array('result' => 'success', 'message' => 'Feedback updated successfully', 'status' => ucfirst($inputs['status'])));
				else
					echo json_encode(array('result' => 'failure', 'message' => 'There are some problem in updating feedback'));
				exit;
			}
			else
				echo json_encode(array('result' => 'failure', 'message' => 'Select valid action for feedback'));
			exit;
		} else {
			echo json_encode(array('result' => 'failure', 'message' => Lang::get('common.demo_site_featured_not_allowed')));
		}
	}
}
?>