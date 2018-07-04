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
class MyCollectionsController extends BaseController
{

	//Taxations
	public function getIndex($user_id = null)
	{
		$mycollectionservice = new CollectionService();
		$err_msg = '';
		$is_search_done = 0;
		$inputs=Input::all();
		$user_id = BasicCUtil::getLoggedUserId();
		$mycollectionservice->setCollectionFilter($inputs);
		$collections = $mycollectionservice->getCollections($user_id, 'paginate', 10);
		$get_common_meta_values = Cutil::getCommonMetaValues('my-collections');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('collectionsList', compact('collections', 'error_message', 'is_search_done'));

	}
	public function getAdd()
	{
		$max_product_allowed = Config::get('generalConfig.max_products_allowed_per_collection');
		$user_id = BasicCUtil::getLoggedUserId();
		$get_common_meta_values = Cutil::getCommonMetaValues('add-collection');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('addcollection',compact('user_id', 'max_product_allowed'));
	}
	public function postAdd()
	{
		$inputs =Input::all();

		$rules = array(
			'user_id' 	=> 'required|numeric',
			'collection_name' 	=> 'required',
			'collection_slug'	=> 'sometimes|required|alpha_dash|Unique:collections,collection_slug',
			'collection_access' 	=> 'sometimes|in:Public,Private',
		);
		$messages = array(
			'collection_slug.unique' => trans('collection.collection_name_already_exists')
		);
		$mycollectionservice = new CollectionService();
		$user_id = BasicCUtil::getLoggedUserId();
		$user_code = BasicCutil::setUserCode($user_id);
		if(isset($inputs['collection_name']) && $inputs['collection_name']!='')
			$inputs['collection_slug'] = $user_code.'-'.$mycollectionservice->generateSlug($inputs['collection_name']);
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$validator = Validator::make($inputs,$rules, $messages);
		if($validator->passes())
		{
			$collection_id  = $mycollectionservice->addCollection($inputs);
			if($collection_id > 0)
			{
				if(isset($inputs['listing_id']) && !empty($inputs['listing_id']))
				{
					$allproducts = array();
					$i=1;
					foreach($inputs['listing_id'] as $product_id)
					{
						$product_id = intval($product_id);
						if(is_numeric($product_id) && $product_id > 0)
						{
							$allproducts[]= array(
								'collection_id' => $collection_id,
								'product_id' => $product_id,
								'date_added' => date('Y-m-d'),
								'order' => $i);
							$i++;
						}
					}
					if(!empty($allproducts))
						$mycollectionservice->addCollectionProducts($allproducts);
				}
				return Redirect::action('MyCollectionsController@getIndex')->with('success_message',trans('collection.collection_added_success'));
			}
			else
			{
				return Redirect::action('MyCollectionsController@getAdd')->with('error_message',trans('common.some_problem_try_later'))->withInput();
			}
		}
		else
		{

			if($validator->errors()->has('collection_slug'))
			{
				$error_message = $validator->errors()->first('collection_slug');
				$validator->errors()->add('collection_name', $error_message);
			}
			unset($inputs['listing_id']);
			//echo "<pre>";print_r($validator->messages());echo "</pre>";exit;
			return Redirect::action('MyCollectionsController@getAdd')->with('error_message',trans('common.some_problem_try_later'))->withInput($inputs)->withErrors($validator);
		}

	}
	public function postProductDetails()
	{
		$listing_url = Input::get('listing_url');
		if($listing_url!='')
		{
			$url_arr = explode('/', $listing_url);
			$listing_url = end($url_arr);
			$url_slug_arr = explode('-', $listing_url);
			$listing_url = $url_slug_arr[0];
		}

		$product_details = array();
		try
		{
			$product = Products::initialize();
			$product->setFilterProductStatus('Ok');
			$product->setFilterProductExpiry(true);
			$product->setFilterProductCode($listing_url);
			$product_det = $product->getProductDetails();
			if(count($product_det) > 0)
			{
				$productService = new ProductService();
				$product_id = $product_det['id'];
				$user_id = $product_det['product_user_id'];
				$name = Cutil::getUserDetails($user_id);
				$product_code = $product_det['product_code'];
				$view_url = $productService->getProductViewURL($product_id, $product_det);
				$product_name = e(nl2br($product_det['product_name']));
				if($product_det['is_free_product'] == 'Yes'){
					$price = 'Free';
				}else{
					$price = $productService->formatProductPriceNew($product_det);
					$price = (isset($price['product']['discount']) && $price['product']['discount'] >= 0)? $price['product']['discount'] : 0;
					$price = CUtil::convertAmountToCurrency($price, Config::get('generalConfig.site_default_currency'), '', true);
				}
				$p_img_arr = $product->getProductImage($product_id);
                $p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
				$view_user_url = Cutil::userProfileUrl($name['user_code']);
				$view_product = '<a target="_blank" href="'.$view_url.'">'.$product_name.'</a>';
				$view_user_url_profile = '<a target="_blank" href="'.$view_user_url.'">'.$name['display_name'].'</a>';
                $view_url_with_image ='<a target="_blank" href="'.$view_url.'">	<img width="170" height="135" alt="" src="'.$p_thumb_img['image_url'].'" title = "'.$p_thumb_img['title'].'"></a>';
				$product_details = array('result' => 'success', 'view_url' => $view_url, 'product_code' => $product_code, 'product_name' => $view_product, 'product_id' => $product_id, 'listing_url' => $listing_url, 'view_url_with_image' => $view_url_with_image, 'user_id' => $view_user_url_profile, 'price' => $price);

			}
			else
			{
				throw new Exception('Invalid product');
				$product_details = array('result' => 'error', 'error_message' => trans('collection.product_not_found'));
			}
		}
		catch(Exception $e)
		{
			//Product id removed from search
			/*try
			{
				//echo "<br>listing_url: ".$listing_url;exit;
				$listing_url = (int)$listing_url;
				$product->setFilterProductCode('');
				$product->setFilterProductStatus('Ok');
				$product->setProductId($listing_url);
				$product_det = $product->getProductDetails();
				if(count($product_det) > 0)
				{
					$productService = new ProductService();
					$product_id = $product_det['id'];
					$user_id = $product_det['product_user_id'];
					$name = Cutil::getUserDetails($user_id);
					$product_code = $product_det['product_code'];
					$view_url = $productService->getProductViewURL($product_id, $product_det);
					$product_name = e(nl2br($product_det['product_name']));
					$price = $productService->formatProductPriceNew($product_det);
					if($product_det['is_free_product'] == 'Yes'){
						$price = 'Free';
					}else{
						$price = $productService->formatProductPriceNew($product_det);
						$price = (isset($price['product']['discount']) && $price['product']['discount'] >= 0)? $price['product']['discount'] : 0;
						$price = "$ ".$price;
					}
					$p_img_arr = $product->getProductImage($product_id);
	                $p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
					$view_user_url = Cutil::userProfileUrl($name['user_code']);
					$view_product = '<a target="_blank" href="'.$view_url.'">'.$product_name.'</a>';
					$view_user_url_profile = '<a target="_blank" href="'.$view_user_url.'">'.$name['display_name'].'</a>';
	                $view_url_with_image ='<a target="_blank" href="'.$view_url.'">	<img width="170" height="135" alt="" src="'.$p_thumb_img['image_url'].'" title = "'.$p_thumb_img['title'].'"></a>';
					$product_details = array('result' => 'success', 'view_url' => $view_url, 'product_code' => $product_code, 'product_name' => $view_product, 'product_id' => $product_id, 'listing_url' => $listing_url, 'view_url_with_image' => $view_url_with_image, 'user_id' => $view_user_url_profile, 'price' => $price);

				}
			}
			catch(Exception $e)
			{
				$error_msg = $e->getMessage();
				$product_details = array('result' => 'error', 'error_message' => $error_msg);
			}*/
		}
		echo json_encode($product_details);exit;
	}
	public function getUpdate($collection_id = 0)
	{
		if(is_null($collection_id) || $collection_id <= 0)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		$user_id = BasicCUtil::getLoggedUserId();
		$mycollectionservice = new CollectionService();
		$collection_det = $mycollectionservice->getCollectionDetails($collection_id);
		$max_product_allowed = Config::get('generalConfig.max_products_allowed_per_collection');
		if(!$collection_det)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		if($collection_det->user_id!=$user_id)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('common.not_authorize'));

		$collection_det->collection_products = array();
		$collection_det->products_count = 0;
		$collection_products = 	$mycollectionservice->getCollectionProductIds($collection_id);
		if($collection_products && count($collection_products) > 0)
		{
			$collection_det->collection_products = $collection_products;
			$collection_det->products_count = count($collection_products);
		}
		$get_common_meta_values = Cutil::getCommonMetaValues('update-collection');
		if($get_common_meta_values)
		{
			$this->header->setMetaKeyword($get_common_meta_values['meta_keyword']);
			$this->header->setMetaDescription($get_common_meta_values['meta_description']);
			$this->header->setMetaTitle($get_common_meta_values['meta_title']);
		}
		return View::make('updatecollection',compact('user_id','collection_det','error_message','is_search_done', 'max_product_allowed'));
	}
	public function postUpdate($collection_id = 0)
	{
		if(is_null($collection_id) || $collection_id <= 0)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		$user_id = BasicCUtil::getLoggedUserId();
		$mycollectionservice = new CollectionService();
		$collection_det = $mycollectionservice->getCollectionDetails($collection_id);
		//$max_product_allowed = Config::get('generalConfig.max_products_allowed_per_collection');
		if(!$collection_det && count($collection_det) > 0)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		if($collection_det->user_id!=$user_id)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('common.not_authorize'));

		$inputs =Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$rules = array(
			'user_id' 	=> 'required|numeric',
			'collection_name' 	=> 'required',
			'collection_slug'	=> 'sometimes|required|alpha_dash|Unique:collections,collection_slug,'.$collection_id.',id',
			'collection_access' 	=> 'sometimes|in:Public,Private',
		);

		$user_code = BasicCutil::setUserCode($user_id);
		if(isset($inputs['collection_name']) && $inputs['collection_name']!='')
			$inputs['collection_slug'] = $user_code.'-'.$mycollectionservice->generateSlug($inputs['collection_name']);
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;

		$messages = array(
			'collection_slug.unique' => trans('collection.collection_name_already_exists')
		);
		$validator = Validator::make($inputs,$rules, $messages);
		if($validator->passes())
		{
			$valid_arr = array('collection_name' => '', 'collection_slug' => '', 'collection_description' => '', 'collection_access' => '', 'collection_status' => '');
			$collection_update_arr = array_intersect_key($inputs,$valid_arr);

			$mycollectionservice->updateCollection($collection_id, $collection_update_arr);

			$mycollectionservice->removeCollectionProducts($collection_id);

			if($collection_id > 0)
			{
				if(isset($inputs['listing_id']) && !empty($inputs['listing_id']))
				{
					$allproducts = array();
					$i=1;
					foreach($inputs['listing_id'] as $product_id)
					{
						$product_id = intval($product_id);
						if(is_numeric($product_id) && $product_id > 0)
						{
							$allproducts[]= array(
								'collection_id' => $collection_id,
								'product_id' => $product_id,
								'date_added' => date('Y-m-d'),
								'order' => $i);
							$i++;
						}
					}
					if(!empty($allproducts))
						$mycollectionservice->addCollectionProducts($allproducts);
				}
				return Redirect::action('MyCollectionsController@getIndex')->with('success_message',trans('collection.collection_update_success'));
			}
			else
			{
				return Redirect::action('MyCollectionsController@getUpdate', $collection_id)->with('error_message',trans('common.some_problem_try_later'))->withInput();
			}
		}
		else
		{
			if($validator->errors()->has('collection_slug'))
			{
				$error_message = $validator->errors()->first('collection_slug');
				$validator->errors()->add('collection_name', $error_message);
			}
			unset($inputs['listing_id']);
			return Redirect::action('MyCollectionsController@getUpdate', $collection_id)->with('error_message',trans('common.some_problem_try_later'))->withInput($inputs)->withErrors($validator);
		}
	}

	public function postDelete()
	{
		$inputs = Input::all();
		//echo "<pre>";print_r($inputs);echo "</pre>";exit;
		$collection_id = Input::get('collection_id');

		if(is_null($collection_id) || $collection_id <=0)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		$user_id = BasicCUtil::getLoggedUserId();
		$mycollectionservice = new CollectionService();
		$collection_det = $mycollectionservice->getCollectionDetails($collection_id);
		if(!$collection_det && count($collection_det) > 0)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));
		if($collection_det->user_id!=$user_id)
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('common.not_authorize'));

		if($collection_id > 0)
		{

			$deleted = $mycollectionservice->deleteCollection($collection_id);
			if($deleted)
			{
				return Redirect::action('MyCollectionsController@getIndex')->with('success_message',trans('collection.collection_delete_success'));
			}
			else
			{
				return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('common.some_problem_try_later'));
			}
		}
		else
			return Redirect::action('MyCollectionsController@getIndex')->with('error_message',trans('collection.invalid_collection'));

	}

}