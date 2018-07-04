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
class AdminCategoryAttributesController extends BaseController
{
	protected $isStaffProtected = 1;
    protected $page_arr = array();
	protected $action_arr = array();

	function __construct()
	{
        $this->adminCategoryAttributesService = new AdminCategoryAttributesService();
    }

    public function postAttributesInfo()
    {
    	$category_id = Input::get('category_id');
    	$this->root_category_id = Products::getRootCategoryId();
    	$attribs_arr = $this->adminCategoryAttributesService->populateAttributes($category_id);
    	$d_arr['category_id'] =  $category_id;
	    $d_arr['root_category_id'] =  $this->root_category_id;
	    $attr_service_obj = $this->adminCategoryAttributesService;
    	return View::make('admin.categoryAttributesManagement', compact('attribs_arr', 'd_arr', 'attr_service_obj'));
	}

	public function postAdd()
	{
		$input_arr = Input::All();
		$category_id = Input::get('category_id');
		$attribute_id = Input::get('attribute_id');
		$result_arr = $this->adminCategoryAttributesService->assignAttribute($input_arr);
		echo json_encode($result_arr);
	}

	public function getDeleteAttributes()
	{
		$category_id = Input::get('category_id');
		$attribute_id = Input::get('attribute_id');
		$result_arr = $this->adminCategoryAttributesService->removeAttribute($category_id, $attribute_id);
		echo json_encode($result_arr);
	}

	public function getViewAttribute()
	{
		$attribute_id = Input::get('attribute_id');
		$attribute_details = $this->adminCategoryAttributesService->populateAttributes('', $attribute_id);
		$attr_service_obj = $this->adminCategoryAttributesService;
		return View::make('admin.viewAttribute', compact('attribute_details', 'attr_service_obj'));
	}

	public function getAttributesOrder()
	{
		$input_arr = Input::All();
		$this->adminCategoryAttributesService->updateListRowOrder($input_arr);
	}
}