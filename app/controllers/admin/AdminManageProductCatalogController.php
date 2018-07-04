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
class AdminManageProductCatalogController extends BaseController
{
	protected $isStaffProtected = 1;
    protected $page_arr = array('getIndex' => 'product_categories_list');
	protected $action_arr = array();

	function __construct()
	{
        $this->adminProductCatalogService = new AdminManageProductCatalogService();
        parent::__construct();
    }

    public function getIndex()
	{
		$root_category_id = Products::insertRootCategory();
		$ajax_page = false;
		$pageTitle = "Admin - ".trans('admin/manageCategory.manage_product_catalog_title');
		$this->header->setMetaTitle(trans('meta.admin_manage_categories_title'));
		return View::make('admin.productCategoryTree', compact('root_category_id', 'ajax_page', 'pageTitle'));
	}

	public function getCategoryTreeDetails()
	{
		return $this->adminProductCatalogService->get_children(array('category_id' => Input::get('category_id')));
	}

	public function postCategoryDetailsBlock()
	{
		if(Input::get('display_block') == 'category_details' ||
			Input::get('display_block') == 'add_sub_category')
		{
			$ajax_page = true;
			$category_id = Input::get('category_id');
			$display_block = Input::get('display_block');
			$root_category_id = Products::getRootCategoryId();
			$category_details = $this->adminProductCatalogService->getCategoryDetails($category_id);
			return View::make('admin.manageProductCatalogTabs', compact('category_details', 'category_id', 'ajax_page', 'root_category_id', 'display_block'));
		}
	}
	
	public function getMoveMyCategory()
	{
		$move_category_id = Input::get('category_id');
		$category_position = Input::get('category_position');
		$parent_category_id = Input::get('parent_category_id');
		$this->UpdateCategory($move_category_id, $parent_category_id, $category_position);
		$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key); // Clear cache for product details
		return array('err'=>false, 'err_msg'=>'', 'category_id'=>$move_category_id);;
	}
	
	public function UpdateCategory($move_category_id, $parent_category_id, $category_position)
	{
		/*echo '<br/>move cat id: '.$move_category_id;
		echo '<br/>parent cat id: '.$parent_category_id;
		echo '<br/>position: '.$category_position; exit;*/

		$category_details[$move_category_id] = $category_details[$parent_category_id] = array();

		$category_details_results = ProductCategory::Select('id', 'category_level', 'parent_category_id', 'category_left', 'category_right')
									->whereIn('id', array($move_category_id,$parent_category_id))->get();

		if(count($category_details_results) > 0){
			foreach($category_details_results as $category_details_result){
				$category_details[$category_details_result['id']] = $category_details_result;
			}
		}
		if($category_details[$move_category_id] && $category_details[$parent_category_id])
		{
			# parent category sub-categories
			$parent_sub_categories = array();
			$category_details_parent_childs = DB::select(DB::Raw('SELECT node.parent_category_id, node.id, node.category_left, node.category_right FROM product_category node, product_category parent
			WHERE node.category_left
			BETWEEN parent.category_left
			AND parent.category_right
			AND parent.id = '.$parent_category_id.'
			AND node.parent_category_id = '.$parent_category_id.'
			ORDER BY node.category_left'));					
			$cur_position = 0;
			if(count($category_details_parent_childs) > 0){
				foreach($category_details_parent_childs as $category_details_parent_child){
					$parent_sub_categories[$cur_position] = $category_details_parent_child;
						if(($cur_position + 1) == $category_position)
						{
							$aft_cat_id = $category_details_parent_child->id;
							return $this->moveMySelectedCategoryAfter($move_category_id, $aft_cat_id);
						}
						else if($cur_position >= $category_position)
						{
							break;
						}
						$cur_position++;
				}		
			}
			# get subcategories list
			$sub_categories = array();
			# move category sub-categories
			$move_categories_sub = DB::select(DB::Raw('SELECT node.parent_category_id, node.id, node.category_left, node.category_right FROM product_category node, product_category parent
			WHERE node.category_left
			BETWEEN parent.category_left
			AND parent.category_right
			AND parent.id = '.$move_category_id.'
			ORDER BY node.category_left'));
			if(count($move_categories_sub)){
				foreach($move_categories_sub as $move_categories_sub_cate){
					$sub_categories[$move_categories_sub_cate->id] = $move_categories_sub_cate;
				}
			}
			# update selected categories level & parent category id
			$add_count = $category_details[$move_category_id]['category_right'] - $category_details[$move_category_id]['category_left'] + 1;
			if(!$parent_sub_categories && $category_position == 0)
			{
				$right_id = $category_details[$parent_category_id]->category_right;
			}
			else if($parent_sub_categories && $category_position == 0)
			{
				$right_id = $parent_sub_categories[$category_position]->category_left - 1;
			}
			else
			{
				$right_id = $category_details[$move_category_id]['category_right'];
			}			
			$update_sql = ProductCategory::where('category_right', '>=', $right_id)->update(array("category_left" => DB::raw('IF(category_left > '.$right_id. ',category_left + '.$add_count.',category_left)'), "category_right" => DB::raw('IF(category_right >= '.$right_id. ',category_right + '.$add_count.', category_right)')));
			//echo '<br/>update sql: '.$update_sql;
			
			# update category level, category_left & category_right for subcategories
			$sub_cat_count =  count($sub_categories);
			$cat_count = 0;
			$category_right_add_cnt = 0;
			foreach($sub_categories AS $sub_cat_id => $sub_cat_details)
			{
				$cat_count ++;
				if($parent_sub_categories && $category_position == 0)
				{
					$right_id ++;
				}
				# update category_left & category_right
				if($sub_cat_id == $move_category_id)
				{
					$sql = array('category_level' => $this->getCategoryLevel($parent_category_id),
							'parent_category_id' => $parent_category_id);
					$update_sql = ProductCategory::where('id' , $move_category_id)->update($sql);
					
					if($category_details[$move_category_id]['category_right'] >= $right_id)
					{
						$category_right_add_cnt = ($right_id - $cat_count) - $category_details[$move_category_id]['category_right'];
					}
					else
					{
						$category_right_add_cnt = ($right_id + $add_count - $cat_count) - $category_details[$move_category_id]['category_right'];
					}

					$sql1 = array('category_left' => DB::raw('category_left + '.($category_right_add_cnt)),
							'category_right' => DB::raw('category_right + '.($category_right_add_cnt)));
					$update_sql = ProductCategory::where('id' , $sub_cat_id)->update($sql1);
				}
				else
				{
					$sql = array('category_level' => $this->getCategoryLevel($sub_cat_details->parent_category_id),
							'category_left' => DB::raw('category_left + '.$category_right_add_cnt),
							'category_right' => DB::raw('category_right + '.$category_right_add_cnt));
					$update_sql = ProductCategory::where('id' , $sub_cat_id)->update($sql);		
				
//					echo '<br/>sql: '.$sql;
//					echo '<pre>';
//					print_r(array(
//								'category_level'			=> $this->getCategoryLevel($sub_cat_details['parent_category_id']),
//								'category_id'				=> $sub_cat_id));
//					echo '</pre>';
				}
				if(!($parent_sub_categories && $category_position == 0))
				{
					$right_id ++;
				}
			}

			# update parent categories left & right of selected category
			if($category_details[$move_category_id]['category_right'] >= $right_id)
			{
				$category_left = $category_details[$move_category_id]['category_right'];
				$category_right = $category_details[$move_category_id]['category_right'] + $add_count;
			}
			else
			{
				$category_left = $category_details[$move_category_id]['category_left'];
				$category_right = $category_details[$move_category_id]['category_right'];
			}

//			echo '<br/>$category_left: '.$category_left;
//			echo '<br/>$category_right: '.$category_right;
//			echo '<br/>$add_count: '.$add_count;

			$update_left = ProductCategory::where('category_left', '>', $category_left)
						   ->update(array('category_left' => DB::raw('category_left - '. $add_count)));
			$update_right = ProductCategory::where('category_right', '>', $category_right)
							->update(array('category_right' => DB::raw('category_right - '. $add_count)));
			$array_multi_key = array('root_category_id_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		return false;
	}
	
	public function moveMySelectedCategoryAfter($move_category_id, $aft_category_id)
	{

//		echo '<br/>move cat id: '.$move_category_id;
//		echo '<br/>aft cat id: '.$aft_category_id;


		if(!$move_category_id || !$aft_category_id)
			return false;

		$category_details[$move_category_id] = $category_details[$aft_category_id] = array();
		
		$category_details_results = ProductCategory::Select('id', 'category_level', 'parent_category_id', 'category_left', 'category_right')
							->whereIn('id', array($move_category_id,$aft_category_id))->get();
		if(count($category_details_results) > 0)
		{
			foreach($category_details_results as $category_details_result){
				$category_details[$category_details_result['id']] = $category_details_result;
			}
		}

		if($category_details[$move_category_id] && $category_details[$aft_category_id])
		{
			# get subcategories list
			$sub_categories = array();			
			
			$category_details_parent_childs = DB::select(DB::Raw('SELECT node.parent_category_id, node.id, node.category_left, node.category_right FROM product_category node, product_category parent
			WHERE node.category_left
			BETWEEN parent.category_left
			AND parent.category_right
			AND parent.id = '.$move_category_id.'
			ORDER BY node.category_left'));
			if(count($category_details_parent_childs) > 0){
				foreach($category_details_parent_childs as $category_details_parent_child){	
					$sub_categories[$category_details_parent_child->id] = $category_details_parent_child;
				}
			}

			# update selected categories level & parent category id
			$right_id = $category_details[$aft_category_id]['category_right']; 
			$add_count = $category_details[$move_category_id]['category_right'] - $category_details[$move_category_id]['category_left'] + 1;
						
			$update_sql = ProductCategory::where('category_right', '>=', $right_id)->update(array("category_left" => DB::raw('IF(category_left > '.$right_id. ',category_left + '.$add_count.',category_left)'), "category_right" => DB::raw('IF(category_right > '.$right_id. ',category_right + '.$add_count.', category_right)')));

			# update category level, category_left & category_right for subcategories
			$sub_cat_count =  count($sub_categories);
			$cat_count = 0;
			$category_right_add_cnt = 0; 
			foreach($sub_categories AS $sub_cat_id => $sub_cat_details)
			{
				$cat_count ++;
				$right_id ++;
				# update category_left & category_right
				if($sub_cat_id == $move_category_id)
				{
					$sql =  array('category_level' => $category_details[$aft_category_id]['category_level'] ,
							'category_left' => $right_id ,
							'category_right' => DB::Raw($right_id + $add_count - $cat_count),
							'parent_category_id' => $category_details[$aft_category_id]['parent_category_id']);
							
					$update_sql = ProductCategory::where('id' , $move_category_id)->update($sql);

					if($category_details[$move_category_id]['category_right'] >= $right_id)
					{
						$category_right_add_cnt = ($right_id - $cat_count) - $category_details[$move_category_id]['category_right'];
					}
					else
					{
						$category_right_add_cnt = ($right_id + $add_count - $cat_count) - $category_details[$move_category_id]['category_right'];
					}
				}
				else
				{
					$sql = array('category_level' => $this->getCategoryLevel($sub_cat_details->parent_category_id) ,
							'category_left' => DB::Raw('category_left + '.$category_right_add_cnt),
							'category_right' => DB::Raw('category_right + '.$category_right_add_cnt));
					$update_sql = ProductCategory::where('id' , $sub_cat_id)->update($sql);
				}
			}

			if($category_details[$move_category_id]['category_right'] >= $right_id)
			{
				$category_left = $category_details[$move_category_id]['category_right'] + $add_count - 1;
				$category_right = $category_details[$move_category_id]['category_right'];
			}
			else
			{
				$category_left = $category_details[$move_category_id]['category_left'];
				$category_right = $category_details[$move_category_id]['category_right'];
			}

//			echo '<br/>$category_left: '.$category_left;
//			echo '<br/>$category_right: '.$category_right;
//			echo '<br/>$add_count: '.$add_count;
//			echo '<br/>$cat_count: '.$cat_count;

			$update_sql = ProductCategory::where('category_left', '>', $category_left)
										   ->update(array('category_left'  => DB::Raw('category_left - '. $add_count)));
					
			$update_sql = ProductCategory::where('category_right', '>', $category_right)
										   ->update(array('category_right'  => DB::Raw('category_right - '. $add_count)));
			$array_multi_key = array('root_category_id_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);			
			return true;
		}

		return false;
	}
	
	public function getCategoryLevel($parent_category_id = 0)
	{
		if($parent_category_id)
		{
			$category_details_results = ProductCategory::where('id', $parent_category_id)->first();
			if(isset($category_details_results)){
				return $category_details_results->category_level + 1;
			}else
				return 1;	
		}
		return 1;
	}
}