<?php

class AdminManageProductCatalogService
{
	protected $root_category_id = 0;

	private function _get_children($category_id)
	{
		$children = array();

		$cat_details = Products::getCategoriesList($category_id, 'All');
		if(count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				$children[$cat->id] = $cat;
			}
		}
		return $children;

	}

	public function get_children($data)
	{
		$tmp = $this->_get_children((int)$data['category_id']);
		$result = array();
		if((int)$data['category_id'] === (int)Products::getRootCategoryId() && count($tmp) === 0)
		{
			$result[] = array(
				'attr' => '',
				'data' => trans('admin/manageCategory.no_category_msg'),
				'state' => ''
			);
		}

		if((int)$data['category_id'] === 0)
			return json_encode($result);


		$prod_count_arr = Products::getProductCountForAllCategories();
		foreach($tmp as $key => $value)
		{
			$category_id = $value['id'];
			//show the product count in () if set
			if(isset($prod_count_arr[$category_id]))
				$data = $value['category_name'].' ('.$prod_count_arr[$category_id].')';
			else
				$data = $value['category_name'];
			$result[] = array(
				'attr' => array('category_id' => 'node_'.$category_id, 'id' => 'node_'.$category_id),
				'data' => $data,
				'state' => ((int)$value['category_right'] - (int)$value['category_left'] > 1) ? 'closed' : ''
			);
		}
		return json_encode($result);
	}

	public function getCategoryDetails($cat_id)
	{
		$category_details = array();
		$cat_details = Products::getCategoryDetails($cat_id);
		if(count($cat_details) > 0)
		{
			$cat_details['full_parent_category_name'] = $this->getParentCategoryName($cat_details['id']);
			$category_details = $cat_details;
		}
		return $category_details;
	}

	public function getParentCategoryName($category_id)
	{
		$product = Products::initialize();
		$parent_category_name = '';
		$cat_details = $product->getCategoryArr($category_id);
		if (count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				$parent_category_name = ($parent_category_name)?($parent_category_name . ' > ' .$cat->category_name ):$cat->category_name;
			}
		}
		return $parent_category_name;
	}
}