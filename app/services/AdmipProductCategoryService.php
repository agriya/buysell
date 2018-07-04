<?php

class AdminProductCategoryService extends AdminManageProductCatalogService
{
	public function populateCategory($category_id)
	{
		$category_details = array();
		$cat_info = Products::getCategoryDetails($category_id);
		if (count($cat_info) > 0)
	    {
	    	$category_details = $cat_info;
			$available_sort_options = explode(',', $cat_info['available_sort_options']);
			$category_details['available_sort_options'] = $available_sort_options;
			if($cat_info['available_sort_options'] == 'all')
			{
				$category_details['use_all_available_sort_options'] = 'Yes';
			}
			else
			{
				$category_details['use_all_available_sort_options'] = '';
			}
			$category_details['image_name'] = $cat_info['image_name'];
			$category_details['image_ext'] = $cat_info['image_ext'];
			$category_details['image_width'] = $cat_info['image_width'];
			$category_details['image_height'] = $cat_info['image_height'];
	    }
		return $category_details;
	}

	public function uploadCategoryImage($file, $image_ext, $image_name, $destinationpath, $reference_id, $mode)
	{
		$return_arr = array();
		$config_path = Config::get('webshoppack.product_category_image_folder');
		CUtil::chkAndCreateFolder($config_path);

		// open file a image resource
		Image::make($file->getRealPath())->save(Config::get("webshoppack.product_category_image_folder").$image_name.'_O.'.$image_ext);

		list($width,$height)= getimagesize($file);
		list($upload_img['width'], $upload_img['height']) = getimagesize(base_path().'/public/'.$config_path.$image_name.'_O.'.$image_ext);

		$thumb_width = Config::get("webshoppack.product_category_image_thumb_width");
		$thumb_height = Config::get("webshoppack.product_category_image_thumb_height");
		if(isset($thumb_width) && isset($thumb_height))
		{
			$timg_size = CUtil::DISP_IMAGE($thumb_width, $thumb_height, $upload_img['width'], $upload_img['height'], true);
			Image::make($file->getRealPath())
				->resize($thumb_width, $thumb_height, true, false)
				->save($config_path.$image_name.'_T.'.$image_ext);
		}

		$img_path = base_path().'/public/'.$config_path;
		list($upload_input['thumb_width'], $upload_input['thumb_height']) = getimagesize($img_path.$image_name.'_T.'.$image_ext);
		if($mode == 'edit')
		{
			$this->deleteExistingImageFiles($reference_id);
		}
		$return_arr = array('image_ext' => $image_ext, 'image_name' => $image_name, 'image_width' => $upload_input['thumb_width'], 'image_height' => $upload_input['thumb_height']);
		return $return_arr;
	}

	public function deleteExistingImageFiles($reference_id)
	{
		$existing_images = ProductCategory::where('id', '=', $reference_id)->first();

		if(count($existing_images) > 0 && $existing_images['image_name'] != '')
		{
			$data_arr = array('image_name' => '', 'image_ext' => '', 'image_height' => '', 'image_width' => '');
			$affectedRows = ProductCategory::whereRaw('id = ?', array($reference_id))->update($data_arr);
			$this->deleteImageFiles($existing_images['image_name'], $existing_images['image_ext'], Config::get("webshoppack.product_category_image_folder"));
			$array_multi_key = array('root_category_id_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}
	}

	public function deleteCategory($del_category_id)
	{
		$product = Products::initialize();
		$details = $product->deleteCategory($del_category_id);
		$json_data = json_decode($details, true);
		if($json_data['status'] == 'error')
		{
			$result_arr = array('err' => true, 'err_msg' => $json_data['error_msg']);
		}
		else
		{
			$result_arr = array('err' => false, 'category_id' => $json_data['category_id']);
		}
		return $result_arr;
	}

	public function deleteCategoryImage($id, $filename, $ext, $folder_name)
	{
		$data_arr = array('image_name' => '', 'image_ext' => '', 'image_height' => '', 'image_width' => '');
		$affectedRows = ProductCategory::whereRaw('id = ?', array($id))->update($data_arr);
		if($affectedRows)
		{
			$this->deleteImageFiles($filename, $ext, $folder_name);
			$array_multi_key = array('root_category_id_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		return false;
	}

	public function deleteImageFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_T.".$ext))
		{
			unlink($folder_name.$filename."_T.".$ext);
		}
		if (file_exists($folder_name.$filename."_O.".$ext))
		{
			unlink($folder_name.$filename."_O.".$ext);
		}
	}
	public function getAllCategoryList($inputs){
		$all_categories_list = ProductCategory::where('parent_category_id','!=',0)->orderby('category_right', 'DESC')->orderby('id','ASC');
		if(isset($inputs['category_name']) && $inputs['category_name']!='')
		{
			$all_categories_list = $all_categories_list->where('category_name', 'like', '%'.$inputs['category_name'].'%');
		}
		$page = (isset($inputs['page']) && $inputs['page'] > 0)?$inputs['page']:1;
		Paginator::setCurrentPage($page);
		$all_categories_list = $all_categories_list->paginate(10);
		return $all_categories_list;
	}
	public function updateCategoryMetaDetails($category_id, $data){
		try{
			$default_valid_array = array('use_parent_meta_detail' => 'No', 'category_meta_title' => 'VAR_SITE_NAME - Marketplace - VAR_TITLE', 'category_meta_keyword' => '', 'category_meta_description' => '');
			$data = array_intersect_key($data, $default_valid_array);
			if($category_id > 0)
			{
				ProductCategory::where('id','=',$category_id)->update($data);
				$array_multi_key = array('root_category_id_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				return true;
			}
			else
				return false;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	public function getAllMetaList($inputs){ 
		$current_language = (Session::has('admin_choose_lang'))?Session::get('admin_choose_lang'):Config::get('generalConfig.lang');
		$all_meta_list = MetaDetails::orderby('id','ASC')->where('language', $current_language);
		if(isset($inputs['page_name']) && $inputs['page_name']!='')
		{ 
			$search_page_name = str_replace(' ', '-', $inputs['page_name']);
			$all_meta_list = $all_meta_list->where('page_name', 'like', '%'.$search_page_name.'%');
		}
		$page = (isset($inputs['page']) && $inputs['page'] > 0)?$inputs['page']:1;
		Paginator::setCurrentPage($page);
		$all_meta_list = $all_meta_list->paginate(10);
		return $all_meta_list;
	}
	public function updateMetaDetails($id, $inputs)	{
		try{
			if($id > 0)
			{
				$check_exits = MetaDetails::where('id', $id)->first();
				if(isset($check_exits))
				{
					$data_array = array('meta_title' => $inputs['meta_title'], 'meta_keyword' => $inputs['meta_keyword'], 'meta_description' => $inputs['meta_description'], 'date_updated' => DB::raw('NOW()'));
					MetaDetails::where('id','=',$id)->update($data_array);
					$cache_key = 'meta_details_key_'.$check_exits->language;
					HomeCUtil::cacheForgot($cache_key);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
}