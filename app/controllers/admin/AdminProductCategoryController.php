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
class AdminProductCategoryController extends BaseController
{
	protected $isStaffProtected = 1;
    protected $page_arr = array();
	protected $action_arr = array();
	function __construct()
	{
		session_start();
        $this->adminProductCategoryService = new AdminProductCategoryService();
    }

	public function postCategoryInfo($error_msg = '')
	{
		$root_category_id = Products::getRootCategoryId();
		$parent_category_id = $root_category_id;
		if (Input::get('parent_category_id') && Input::get('parent_category_id') != "")
		{
			$parent_category_id = Input::get('parent_category_id');
		}
		$d_arr['edit_form'] =  false;
		$d_arr['add_edit_mode_text'] = trans('admin/manageCategory.add_title');
		$category_info = array();
		if($error_msg == '')
			$category_info['status'] = "active";
		$category_image_details = array();
		$sel_category_id = $parent_category_id;
		$cat_url = URL::action('AdminProductCategoryController@postAdd');
		if (Input::get('category_id') && Input::get('category_id') != $root_category_id)
		{
			$category_info = $this->adminProductCategoryService->populateCategory(Input::get('category_id'));
	        if (count($category_info) > 0)
			{
				$cat_url = URL::action('AdminProductCategoryController@postEdit');
				$parent_category_id = $category_info['parent_category_id'];
				$sel_category_id = $parent_category_id;
				$d_arr['edit_form'] = true;
				$d_arr['add_edit_mode_text'] = trans('admin/manageCategory.edit_title');
		    }
	    }
	    $parent_category_name = $this->adminProductCategoryService->getParentCategoryName($sel_category_id);
	    $d_arr['parent_category_id'] =  $parent_category_id;
	    $d_arr['parent_category_name'] =  $parent_category_name;
	    $d_arr['category_id'] =  Input::get('category_id');
	    $d_arr['root_category_id'] =  $root_category_id;
	    $success_msg = '';
	    if(isset($_SESSION['category_info_success_msg']))
		{ 
			$success_msg = $_SESSION['category_info_success_msg'];
			unset($_SESSION['category_info_success_msg']);
		}
		if($error_msg != '')
			return View::make('admin.productCategoryInfo', compact('d_arr', 'category_info', 'category_image_details', 'cat_url', 'success_msg', 'error_msg'));
		else
			return View::make('admin.productCategoryInfo', compact('d_arr', 'category_info', 'category_image_details', 'cat_url', 'success_msg'));
	}

	public function postAdd()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$error_msg ='';
			$input_arr = Input::All();
			$category = Products::initializeCategory();
			$category->setCategoryName($input_arr['category_name']);
			$category->setSlugUrl($input_arr['seo_category_name']);
			$category->setCategoryDescription($input_arr['category_description']);
			$category->setCategoryStatus($input_arr['status']);
			$category->setMetaTitle($input_arr['category_meta_title']);
			$category->setMetaDescription($input_arr['category_meta_description']);
			$category->setMetaKeyword($input_arr['category_meta_keyword']);
			$category->setParentCategoryId($input_arr['parent_category_id']);
			$details = $category->save();
			$json_data = json_decode($details, true);
			if($json_data['status'] == 'error')
			{
				foreach($json_data['error_messages'] AS $err_msg)
				{
					$error_msg .= "<p>".$err_msg."</p>";
				}
				return $this->postCategoryInfo($error_msg);
			}
			else
			{ 
				$category_id = $json_data['category_id'];
				$_SESSION['category_info_success_msg'] = trans('admin/manageCategory.add-category.add_category_success_msg');
				echo '|##|true|##|'.$category_id.'|##|';
				exit;
			}
		}else{
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			return $this->postCategoryInfo($error_msg);
		}
	}

	public function postEdit()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$error_msg ='';
			$input_arr = Input::All();
			$category = Products::initializeCategory($input_arr['category_id']);
			$category->setCategoryName($input_arr['category_name']);
			$category->setSlugUrl($input_arr['seo_category_name']);
			$category->setCategoryDescription($input_arr['category_description']);
			$category->setCategoryStatus($input_arr['status']);
			$category->setMetaTitle($input_arr['category_meta_title']);
			$category->setMetaDescription($input_arr['category_meta_description']);
			$category->setMetaKeyword($input_arr['category_meta_keyword']);
			$category->setParentCategoryId($input_arr['parent_category_id']);
			$details = $category->save();
			$json_data = json_decode($details, true);
			if($json_data['status'] == 'error')
			{
				foreach($json_data['error_messages'] AS $err_msg)
				{
					$error_msg .= "<p>".$err_msg."</p>";
				}
				return $this->postCategoryInfo($error_msg);
			}
			else
			{
				echo '|##|true|##|'.$json_data['category_id'].'|##|';
				$_SESSION['category_info_success_msg'] = trans('admin/manageCategory.add-category.update_category_success_msg');
				exit;
			}
		}else{
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			return $this->postCategoryInfo($error_msg);
		}
	}

	public function getDeleteCategory()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$result_arr = $this->adminProductCategoryService->deleteCategory(Input::get('category_id'));
		}
		else
		{
			$result_arr = array('err' => true, 'err_msg' => Lang::get('common.demo_site_featured_not_allowed'));
		}
		echo json_encode($result_arr);
	}

	public function getDeleteCategoryImage()
	{
		$resource_id 	= Input::get("resource_id");
		$imagename 		= Input::get("imagename");
		$imageext 		= Input::get("imageext");
		$imagefolder 	= Input::get("imagefolder");

		if($imagename != "")
		{
			$delete_status = $this->adminProductCategoryService->deleteCategoryImage($resource_id, $imagename, $imageext, Config::get($imagefolder));
			if($delete_status)
			{
				return \Response::json(array('result' => 'success'));
			}
		}
		return \Response::json(array('result' => 'error'));
	}
	public function getCategoryMetaDetails()
	{
		$inputs = Input::all();
		$all_categories = $this->adminProductCategoryService->getAllCategoryList($inputs);
		if(count($all_categories) > 0)
		{
			$productService = new ProductService();
			foreach($all_categories as $category)
			{
				$category_link = $productService->getProductCategoryArr($category->id, true, true);
				$category->cat_link = $category_link['cat_link'];
				unset($category_link['cat_link']);
				$category->category_link = $category_link;

			}
		}
		$enable_edit = false;
		$category_info = array();
		if(isset($inputs['category_id']) && $inputs['category_id']>0)
		{
			$enable_edit = true;
			$category_info = $this->adminProductCategoryService->populateCategory($inputs['category_id']);
			if(count($category_info) > 0)
				$category_info['parent_category_name'] = $category_info->parent_category_name = $this->adminProductCategoryService->getParentCategoryName($inputs['category_id']);
		}

		return View::make('admin.categoryMetaDetails', compact('all_categories', 'enable_edit', 'category_info'));
	}
	public function postCategoryMetaDetails()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$inputs = Input::all();
			$inputs['use_parent_meta_detail'] = isset($inputs['use_parent_meta_detail'])?$inputs['use_parent_meta_detail']:'No';

			$update = $this->adminProductCategoryService->updateCategoryMetaDetails($inputs['id'], $inputs);
			if($update)
				return Redirect::action('AdminProductCategoryController@getCategoryMetaDetails')->with('success_message', Lang::get('admin/manageCategory.category_meta_details_updated_successfully'));
			else
				return Redirect::back()->with('error_message',Lang::get('admin/manageCategory.problem_in_updating_meta_details'))->withInput();
			//echo "<pre>";print_r($inputs);echo "</pre>";
		} else {
			return Redirect::back()->with('error_message', Lang::get('common.demo_site_featured_not_allowed'));
		}
	}
}