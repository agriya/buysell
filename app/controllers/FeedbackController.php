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
class FeedbackController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		//$this->adminManageUserService = new AdminManageUserService();
    }

	public function getIndex()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$inputs = Input::all();
		$view_type = isset($inputs['view_type'])?$inputs['view_type']:'awaiting';
		$productFeedbackService	= new ProductFeedbackService();
		$awaiting_invoices_list = array();
		$completed_feedbacks_list = array();
		$feedback_invoice_ids = $productFeedbackService->getFeedbackGivenInvoiceId($logged_user_id, $view_type);
		$feedback_counts = $productFeedbackService->feedbackCountDetails($feedback_invoice_ids);

		$invoice_obj = Webshoporder::initializeInvoice();
		if($view_type == 'awaiting')
		{
			$awaiting_invoices_list = $invoice_obj->getUserInvoiceList($logged_user_id, $feedback_invoice_ids, true, $view_type);
			if($awaiting_invoices_list && !empty($awaiting_invoices_list))
			{
				$perPage = 10;
				if(Input::has('page'))
				{
					$currentPage = Input::get('page') - 1;
				}
				else
				{
					$currentPage =  0;
				}
				$pagedData = array_slice($awaiting_invoices_list, $currentPage * $perPage, $perPage);
				$awaiting_invoices_list = Paginator::make($pagedData, count($awaiting_invoices_list), $perPage);
			}
		}

		if($view_type == 'feedback_completed' && $feedback_invoice_ids && !empty($feedback_invoice_ids))
		{
			$completed_feedbacks_list = $invoice_obj->getUserInvoiceList($logged_user_id, $feedback_invoice_ids, false, $view_type);
			if($completed_feedbacks_list && !empty($completed_feedbacks_list))
			{
				$perPage = 10;
				$currentPage = Input::get('page') - 1;
				$pagedData = array_slice($completed_feedbacks_list, $currentPage * $perPage, $perPage);
				$completed_feedbacks_list = Paginator::make($pagedData, count($completed_feedbacks_list), $perPage);
			}
		}
		$productService = new ProductService();
		$get_common_meta_values = Cutil::getCommonMetaValues('manage-feedbacks');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('feedbacks', compact('awaiting_invoices_list', 'completed_feedbacks_list', 'view_type', 'productService', 'feedback_counts'));
	}

	public function getAddFeedback($invoice_id = null)
	{
		//if invoice id not selected
		if(is_null($invoice_id) || $invoice_id=='' || $invoice_id<=0)
			App::Abort('404');

		$logged_user_id = BasicCUtil::getLoggedUserId();
		$invoice_obj = Webshoporder::initializeInvoice();

		$invoice_details = $invoice_obj->getInvoiceDetails($invoice_id);
		//if invoice details are not present
		if(!$invoice_details || empty($invoice_details) || $invoice_details=='')
			App::Abort('404');

		$productService = new ProductService();
		//if user dont have access to this invoice
		if($invoice_details && !empty($invoice_details) && $invoice_details['buyer_id']!=$logged_user_id)
			return Redirect::action('FeedbackController@getIndex')->with('error_message', trans('feedback.you_are_not_authorize'));

		//$review_for = 'buyer';
		//if($invoice_details['item_owner_id'] == $logged_user_id)
			$review_for = 'seller';
		$feedback_details=array();
		$action = 'add';
		$get_common_meta_values = Cutil::getCommonMetaValues('add-feedbacks');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addFeedback', compact('invoice_details', 'feedback_details', 'review_for', 'action', 'productService'));
	}

	public function postAddFeedback($invoice_id = null)
	{
		//if invoice id not selected
		if(is_null($invoice_id) || $invoice_id=='' || $invoice_id<=0)
			App::Abort('404');

		$logged_user_id = BasicCUtil::getLoggedUserId();
		$invoice_obj = Webshoporder::initializeInvoice();

		$invoice_details = $invoice_obj->getInvoiceDetails($invoice_id);
		//if invoice details are not present
		if(!$invoice_details || empty($invoice_details) || $invoice_details=='')
			App::Abort('404');

		$productService = new ProductService();
		//if user dont have access to this invoice
		if($invoice_details && !empty($invoice_details) && $invoice_details['buyer_id']!=$logged_user_id)
			return Redirect::back()->withInput()->with('error_message',trans('feedback.you_are_not_authorize'));

		$rules = array('feedback_remarks' => 'required|in:Positive,Negative,Neutral', 'feedback_comment' => 'required', 'rating' => 'required|numeric|min:0.1|max:5');
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$validator = Validator::make($inputs, $rules);
		if($validator->passes())
		{
			$data = array();
			$data['feedback_user_id'] = $logged_user_id;
			$data['invoice_id'] = $invoice_details['id'];
			$data['product_id'] = $invoice_details['product_id'];
			$data['buyer_id'] = $invoice_details['buyer_id'];
			$data['seller_id'] = $invoice_details['item_owner_id'];
			$data['feedback_remarks'] = $inputs['feedback_remarks'];
			$data['feedback_comment'] = $inputs['feedback_comment'];
			$data['rating'] = $inputs['rating'];

			$productFeedbackService = new ProductFeedbackService();
			$feedback_id = $productFeedbackService->addFeedback($data);

			// Remove cache for
			if($data['seller_id'])
			{
				$cache_key_forgot = 'product_invoice_feedback'.$data['seller_id'];
				HomeCUtil::cacheForgot($cache_key_forgot);
			}
			if($feedback_id)
				return Redirect::action('FeedbackController@getIndex')->with('success_message',trans('feedback.feedback_added_success'));
			else
				return Redirect::back()->withInput()->with('error_message',trans('feedback.some_problem_try_later'));
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($validator)->with('error_message',trans('feedback.some_problem_try_later'));
		}
	}

	public function getUpdateFeedback($feedback_id = null)
	{
		//$feedback_id = isset($feedback_id)?$feedback_id:'';
		if(is_null($feedback_id) || $feedback_id=='' || $feedback_id<=0)
			App::Abort('404');

		$productFeedbackService	= new ProductFeedbackService();
		$feedback_details = $productFeedbackService->getFeedbackDetails($feedback_id);

		if(!$feedback_details || count($feedback_details) <=0)
			App::Abort('404');

		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($feedback_details->feedback_user_id != $logged_user_id)
			return Redirect::to('feedback/index?view_type=feedback_completed')->with('error_message',trans('common.not_authorize'));

		$invoice_obj = Webshoporder::initializeInvoice();

		$invoice_id = (isset($feedback_details->invoice_id) && $feedback_details->invoice_id!='')?$feedback_details->invoice_id:'';
		$invoice_details = array();
		if($invoice_id!='')
		{
			$invoice_details = $invoice_obj->getInvoiceDetails($invoice_id);
		}
		$action = 'edit';
		$productService = new ProductService();
		//echo "<pre>";print_r($feedback_details);echo "</pre>";
		$get_common_meta_values = Cutil::getCommonMetaValues('edit-feedbacks');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addFeedback', compact('feedback_details', 'invoice_details', 'action', 'productService'));
	}

	public function postUpdateFeedback($feedback_id = null)
	{
		//$feedback_id = isset($feedback_id)?$feedback_id:'';
		if(is_null($feedback_id) || $feedback_id=='' || $feedback_id<=0)
			App::Abort('404');

		$productFeedbackService	= new ProductFeedbackService();
		$feedback_details = $productFeedbackService->getFeedbackDetails($feedback_id);

		if(!$feedback_details || count($feedback_details) <=0)
			App::Abort('404');

		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($feedback_details->feedback_user_id != $logged_user_id)
			return Redirect::to('feedback/index?view_type=feedback_completed')->with('error_message',trans('common.not_authorize'));

		$rules = array('feedback_remarks' => 'required|in:Positive,Negative,Neutral', 'feedback_comment' => 'required', 'rating' => 'required|numeric|min:0.1|max:5');
		$inputs = Input::all();
		$validator = Validator::make($inputs, $rules);
		if($validator->passes())
		{
			$data = array();
			$data['feedback_remarks'] = $inputs['feedback_remarks'];
			$data['feedback_comment'] = $inputs['feedback_comment'];
			$data['rating'] = $inputs['rating'];
			$update = $productFeedbackService->updateFeedabck($feedback_id, $data);

			if($update)
			{
				$qry = array('view_type' => 'feedback_completed');
				$qry_string ='?'. http_build_query($qry);

				// Remove cache associated with the invoice feedback
				if(isset($feedback_details->seller_id) && $feedback_details->seller_id)
				{
					$cache_key_forgot = 'product_invoice_feedback'.$feedback_details->seller_id;
					HomeCUtil::cacheForgot($cache_key_forgot);
				}

				//return Redirect::to( action('Controller@action', array(param, param2, param3) . '?page=' . $redirect['page'] ) );
				return Redirect::to( 'feedback/index?view_type=feedback_completed')->with('success_message',trans('feedback.feedback_update_success'));
			}
			else
				return Redirect::back()->withInput()->with('error_message',trans('feedback.some_problem_try_later'));

		}
		$action = 'edit';
		$productService = new ProductService();
		return View::make('addFeedback', compact('feedback_details', 'invoice_details', 'action', 'productService'));
	}

	public function postFeedbackAction()
	{
		$inputs = Input::all();
		$feedback_id = isset($inputs['feedback_id'])?$inputs['feedback_id']:'';
		if(is_null($feedback_id) || $feedback_id=='' || $feedback_id<=0)
			App::Abort('404');

		$productFeedbackService	= new ProductFeedbackService();
		$feedback_details = $productFeedbackService->getFeedbackDetails($feedback_id);
		if(!$feedback_details || count($feedback_details) <=0)
			App::Abort('404');

		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($feedback_details->feedback_user_id != $logged_user_id)
			return Redirect::to('feedback/index?view_type=feedback_completed')->with('error_message',trans('common.not_authorize'));

		$action = isset($inputs['feedback_action'])?$inputs['feedback_action']:'delete';
		if($action=='delete')
		{
			$deleted = $productFeedbackService->deleteFeedback($feedback_id);
			if($deleted)
			{
				// Remove cache associated with the invoice feedback
				if(isset($feedback_details->seller_id) && $feedback_details->seller_id)
				{
					$cache_key_forgot = 'product_invoice_feedback'.$feedback_details->seller_id;
					HomeCUtil::cacheForgot($cache_key_forgot);
				}
				return Redirect::action('FeedbackController@getIndex')->with('success_message',trans('feedback.feedback_delete_success'));
			}
			else
				return Redirect::action('FeedbackController@getIndex')->with('error_message',trans('feedback.some_problem_try_later'));
		}
	}
}
?>