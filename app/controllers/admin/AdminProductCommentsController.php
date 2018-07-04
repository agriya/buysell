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
class AdminProductCommentsController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
	{
		$inputs = Input::all();

		$productCommentsService	= new ProductCommentsService();
		$comments_list = $productCommentsService->getAdminProductComments($inputs);

		$productService = new ProductService();
		$actions = array(''=> trans('common.select'),'delete' => trans('common.delete'));
		return View::make('admin.productCommentsList', compact('comments_list','productService', 'actions'));
    	//
	}
	public function postBulkAction()
	{
		$inputs = Input::all();
		$productCommentsService	= new ProductCommentsService();

		$action = $inputs['action'];
		$action_done = false;

		if($action == 'delete')
		{
			$action_done = $productCommentsService->bulkDeleteComment($inputs['ids']);
		}
		else
			$action_done = false;

		if($action_done)
			return Redirect::back()->with('success_message','Comments deleted successfully');
		else
			return Redirect::back()->with('error_message','There are some problem in executing selected action');
	}
	public function postAction()
	{
		$inputs = Input::all();
		if($inputs['action']=='update_comment')
		{
			$productCommentsService	= new ProductCommentsService();
			$data = array();
			$comment_id = $inputs['comment_id'];
			$data['comments'] = $inputs['comment'];
			$update = $productCommentsService->updateComment($comment_id,$data);
			if($update)
				echo json_encode(array('result' => 'success', 'message' => 'Comment updated successfully'));
			else
				echo json_encode(array('result' => 'failure', 'message' => 'There are some problem in updating comment'));
			exit;
		}
		else
			echo json_encode(array('result' => 'failure', 'message' => 'Select valid action for comment'));
		exit;

	}
}
?>