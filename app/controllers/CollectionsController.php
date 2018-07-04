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
class CollectionsController extends BaseController
{
	//Collections
	public function getIndex()
	{
		$collectionservice = new CollectionService();
		$productService = new ProductService();
		$shop_obj = Products::initializeShops();
		$inputs=Input::all();
		$collectionservice->setCollectionFilter($inputs);
		$collections = $collectionservice->getCollectionsList('paginate', 10);
		$current_url = BasicCUtil::getCurrentUrl(true);
		if(!isset($inputs['collection_name']) || (isset($inputs['collection_name']) && $inputs['collection_name']==''))
			$inputs['collection_name']=Lang::get('collection.default_search_collection_name');
		if(!isset($inputs['collection_by']) || (isset($inputs['collection_by']) && $inputs['collection_by']==''))
			$inputs['collection_by']=Lang::get('collection.default_search_by_member');
		$get_common_meta_values = Cutil::getCommonMetaValues('collections');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('collections', compact('collections', 'collectionservice', 'productService', 'shop_obj', 'inputs', 'current_url'));
	}

	public function getViewCollection($collection_slug = null)
	{
		$err = '';
		if(is_null($collection_slug) || $collection_slug == '')	{
			$err = trans('collection.invalid_collection');
		}
		$collectionservice = new CollectionService();
		$productService = new ProductService();
		$shop_obj = Products::initializeShops();
		$current_url = BasicCUtil::getCurrentUrl(true);
		$inputs=Input::all();
		$current_url = BasicCUtil::getCurrentUrl(true);
		$collection_details = $collectionservice->getCollectionDetailsBySlug($collection_slug);
		if($collection_details && count($collection_details) >0)
			Event::fire('collection.viewcount', array($collection_details));

		if(!$collection_details){
			$err = trans('collection.invalid_collection');
		}
		if($err!='')
			return Redirect::action('CollectionsController@getIndex')->with('error_message', $err);

		//owner_details
		$collection_owner_details = array();
		if($collection_details->user_id > 0) {
			$collection_owner_details = CUtil::getUserDetails($collection_details->user_id);
			if(count($collection_owner_details) > 0) {
				if($collection_owner_details['is_banned'] == 1) {
					$err = trans('shop.collection_owner_blocked');
					return Redirect::action('CollectionsController@getIndex')->with('error_message', $err);
				}
			}
		}

		$collections_comments = $collectionservice->getCollectionComments($collection_details->id, 'get', 10, false);
		//if($collections_comments)
			//echo "<br>collections_comments: ".$collections_comments->count();

		//)roduct details
		$collection_details->collection_products = array();
		$collection_details->products_count = 0;
		$collection_products = 	$collectionservice->getCollectionProductIds($collection_details->id);
		if($collection_products && count($collection_products) > 0)
		{
			$collection_details->collection_products = $collection_products;
			$collection_details->products_count = count($collection_products);
		}

		$collectionFavoritesService = App::make('FavoriteInterface', array('favorites' => 'collection'));
		$get_common_meta_values = Cutil::getCommonMetaValues('view-collections');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('viewCollection', compact('collection_details', 'collection_owner_details', 'collections_comments', 'shop_obj', 'collectionservice', 'productService', 'collectionFavoritesService', 'inputs', 'current_url'))->withInput(Input::all());
	}
	public function postViewCollection($collection_slug = null)
	{
		$err = '';
		if(is_null($collection_slug) || $collection_slug == '')	{
			$err = trans('collection.invalid_collection');
		}
		$collectionservice = new CollectionService();
		$collection_details = $collectionservice->getCollectionDetailsBySlug($collection_slug);
		if(!$collection_details || count($collection_details) <=0)
			$err = trans('collection.invalid_collection');

		$collection_id = $collection_details->id;

		$inputs = Input::all();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if(Input::has('post_comment'))
		{
			$inputs['user_id'] = $logged_user_id;
			$inputs['collection_id'] = $collection_id;
			$rules = array('comment' => 'required|max:500', 'user_id' => 'required|numeric|min:1', 'collection_id' => 'required|numeric|min:1');
			$validator = Validator::make($inputs,$rules);
			if($validator->passes())
			{
				$default_data = array('comment' => '', 'user_id' => $logged_user_id, 'collection_id' => $collection_id);
				$data = array_intersect_key($inputs, $default_data);
				//echo "<pre>";print_r($data);echo "</pre>";exit;

				$collection_comment_id = $collectionservice->addCollectionComment($data);
				return Redirect::action('CollectionsController@getViewCollection',$collection_slug)->with('success_message', trans('collection.comment_added_success'));

			}
			else
			{
				return Redirect::action('CollectionsController@getViewCollection',$collection_slug)->withInput()->withErrors($validator);
			}
		}

	}

	public function postCollectionAction()
	{
		$logged_user_id = BasicCUtil::getLoggedUserId();

		$inputs = Input::all();
		$collectionservice = new CollectionService();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$comment_id = (isset($inputs['comment_id']) && $inputs['comment_id']!='')?$inputs['comment_id']:'';
		if($comment_id =='' || $comment_id <=0)
		{
			echo "error|~~|".trans('collection.invalid_comment_selection');exit;
		}
		$comment_details = $collectionservice->getCollectionCommentDetails($comment_id);
		if(!$comment_details || count($comment_details) <= 0)
		{
			echo "error|~~|".trans('collection.invalid_comment_selection');exit;
		}
		if($comment_details->user_id != $logged_user_id && $comment_details->owner_id != $logged_user_id)
		{
			echo "error|~~|".trans('collection.dont_have_access_to_comment');exit;
		}

		if(Input::has('action'))
		{
			$action = $inputs['action'];
			switch($action)
			{
				case 'update_comment':
						$comment = (isset($inputs['comment']) && $inputs['comment']!='')?$inputs['comment']:'';
						if($comment!='')
						{
							$collectionservice->updateCollectionCommentDetails($comment_id, array('comment' => $comment));
							Session::flash('success_message', trans('collection.comment_update_success'));
							echo "success|~~|".trans('collection.comment_update_success');exit;
						}
						else
						{
							echo "error|~~|".trans('collection.please_enter_comment');exit;
						}

					break;
				case 'delete_comment':
						$deleted = $collectionservice->deleteCollectionComment($comment_id);
						if($deleted)
						{
							Session::flash('success_message', trans('collection.comment_delete_success'));
							echo "success|~~|".trans('collection.comment_delete_success');exit;
						}else{
							echo "error|~~|".trans('collection.some_problem_delete_comment');exit;
						}
					break;

			}
		}

	}
	public function postIncreaseClicks()
	{
		//postIncreaseClicks

		$inputs = Input::all();
		if(isset($inputs['collection_id']) && $inputs['collection_id'] > 0)
		{
			$collectionservice = new CollectionService();
			$collectionservice->increaseClicks($inputs['collection_id']);
		}
		echo "success";exit;
	}
}