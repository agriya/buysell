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
class AdminManageCollectionsController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function getIndex()
	{
		$inputs = Input::all();

		$collectionService	= new CollectionService();

		$collections_list = $collectionService->getAllCollectionAdmin($inputs);

		$actions = array(''=>trans('common.select'), 'delete' => trans('common.delete'), 'Active' => trans('common.active'), 'InActive' => trans('common.inactive'));
		$status = array(''=>trans('common.select'), 'Active' => trans('common.active'), 'InActive' => trans('common.inactive'));
		$privacy_list = array('' => trans('common.select'), 'Public' => trans('common.public'), 'Private' => trans('common.private'));
		return View::make('admin.collectionsList', compact('collections_list','collectionService', 'actions', 'status', 'privacy_list'));

	}
	public function postBulkAction()
	{
		$inputs = Input::all();
		$collectionService	= new CollectionService();
		if(!BasicCUtil::checkIsDemoSite()) {
			$action = $inputs['action'];
			$action_done = false;
			if(isset($inputs['ids']) && !empty($inputs['ids']))
			{
				if(in_array($action, array('Active','InActive') ))
				{
					$data = array();
					$data['collection_status'] = ucfirst($action);
					$action_done = $collectionService->bulkUpdateCollections($inputs['ids'], $data);
				}
				elseif($action == 'delete')
				{
					$action_done = $collectionService->bulkDeleteCollections($inputs['ids']);
				}
				else
					$action_done = false;
			}
			if($action_done)
				return Redirect::action('AdminManageCollectionsController@getIndex')->with('success_message', Lang::get('admin/collection.collections_updated_successfully'));
			else
				return Redirect::action('AdminManageCollectionsController@getIndex')->with('error_message', Lang::get('admin/collection.there_are_some_problem_in_executing_selected_action'));
		} else {
			return Redirect::action('AdminManageCollectionsController@getIndex')->with('error_message',Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
	public function postAction()
	{
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$update_arr = array();
		if(!BasicCUtil::checkIsDemoSite()) {
			if($inputs['action']=='set_as_featured')
			{
				$update_arr = array('featured_collection' => 'Yes');
			}
			if($inputs['action']=='remove_from_featured')
			{
				$update_arr = array('featured_collection' => 'No');
			}
			if(!empty($update_arr) && isset($inputs['id']) && $inputs['id'] >0)
			{

				$collectionService	= new CollectionService();
				$update = $collectionService->updateCollection($inputs['id'], $update_arr);

				if($update)
					return Redirect::action('AdminManageCollectionsController@getIndex')->with('success_message', Lang::get('admin/collection.collection_updated_successfully'));
				else
					return Redirect::action('AdminManageCollectionsController@getIndex')->with('error_message', Lang::get('admin/collection.there_are_some_problem_in_updating_collection'));
			}
			else
				return Redirect::action('AdminManageCollectionsController@getIndex')->with('error_message', Lang::get('admin/collection.select_valid_action_collection_to_update'));
		} else {
			return Redirect::action('AdminManageCollectionsController@getIndex')->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
}
?>