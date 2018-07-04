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
class AdminProductAttributesController extends BaseController
{
	protected $isStaffProtected = 1;
    protected $page_arr = array('getIndex' => 'product_attributes_list');
	protected $action_arr = array();
	public $option_fields = array('select', 'check', 'option', 'multiselectlist');

	function __construct()
	{
		parent::__construct();
        $this->adminProductAttributesService = new AdminProductAttributesService();
    }

    public function getIndex()
    {
    	$attribute_details = array();
    	$perPage = Config::get('webshoppack.attribute_per_page_list');

		$attribute_details = Products::getProductAttributeDetails('', true, $perPage);
		$prod_attr_service_obj = $this->adminProductAttributesService;
		$options = $this->option_fields;
		$d_arr['attribute_is_searchable'] = 'no';
		$d_arr['status'] = 'active';
		$ui_elements_all = array_merge(Config::get('webshoppack.ui_no_options'), Config::get('webshoppack.ui_options'));
		$this->header->setMetaTitle(trans('meta.admin_manage_attributes_title'));
    	return View::make('admin.productAttributesManagement', compact('attribute_details', 'options', 'prod_attr_service_obj', 'ui_elements_all', 'd_arr'));
	}

	public function postAdd()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$error_msg = '';
			$input_arr = Input::All();
			$validation_rules = '';
			if(!empty($input_arr['validation_rules']))
			{
				$validation_rules = $this->adminProductAttributesService->processValidationRules($input_arr['validation_rules']);
			}
			$attribute = Products::initializeAttribute();
			$attribute->setAttributeLabel($input_arr['attribute_label']);
			$attribute->setAttributeDescription($input_arr['description']);
			$attribute->setAttributeType($input_arr['attribute_question_type']);
			$attribute->setAttributeValidation($validation_rules);
			$attribute->setAttributeSearchable($input_arr['attribute_is_searchable']);
			$attribute->setAttributeStatus($input_arr['status']);
			$attribute->setAttributeDefaultValue($input_arr['attribute_default_value']);
			if (!empty($input_arr['attribute_options']) && sizeof($input_arr['attribute_options']) > 0)
			{
				$attribute->setAttributeOptions($input_arr['attribute_options']);
			}
			$details = $attribute->save();
			$json_data = json_decode($details, true);
			if($json_data['status'] == 'error')
			{
				foreach($json_data['error_messages'] AS $err_msg)
				{
					$error_msg .= "<p>".$err_msg."</p>";
				}
				echo json_encode(array('err' => true, 'err_msg' => $error_msg));
			}
			else
			{
				$attribute_id = $json_data['attribute_id'];
				echo json_encode(array('err' => false, 'err_msg' => '', 'list_row' => $this->adminProductAttributesService->getHTMLListRow($attribute_id)));
			}
		} else {
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			echo json_encode(array('err' => true, 'err_msg' => $error_msg));
		}
	}

	public function getAttributesRow()
	{
		$attribute = Products::initializeAttribute();
		$row_id = Input::get('row_id');
		echo json_encode($attribute->getAttribute($row_id));
	}

	public function postUpdate()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$input_arr = Input::All();
			$attribute_id = Input::get('attribute_id');
			$attribute = Products::initializeAttribute($attribute_id);
			$attributes = $attribute->getAttribute($attribute_id);

			$input_arr['attribute_options_update'] = array();

			for($i = 0 ; $i < $attributes['options_size']; $i++)
			{
				$attribute_options_ids = $attributes['options'][$i]['id'];
				if(isset($input_arr['attribute_options_' . $attribute_options_ids]) && !empty($input_arr['attribute_options_' . $attribute_options_ids]))
				{
					$input_arr['attribute_options_update'][$attribute_options_ids] = $input_arr['attribute_options_'.$attribute_options_ids];
				}
			}
			$validation_rules = array();
			if(isset($input_arr['validation_rules']) && !empty($input_arr['validation_rules']))
			{
				$validation_rules = $this->adminProductAttributesService->processValidationRules($input_arr['validation_rules']);
			}

			$attribute->setAttributeLabel($input_arr['attribute_label']);
			$attribute->setAttributeDescription($input_arr['description']);
			$attribute->setAttributeType($input_arr['attribute_question_type']);
			$attribute->setAttributeValidation($validation_rules);
			$attribute->setAttributeSearchable($input_arr['attribute_is_searchable']);
			$attribute->setAttributeStatus($input_arr['status']);
			$attribute->setAttributeDefaultValue($input_arr['attribute_default_value']);
			if (!empty($input_arr['attribute_options_update']) && sizeof($input_arr['attribute_options_update']) > 0)
			{
				$attribute->setAttributeOptionsIdVal($input_arr['attribute_options_update']);
			}
			if (!empty($input_arr['attribute_options']) && sizeof($input_arr['attribute_options']) > 0)
			{
				$attribute->setAttributeOptions($input_arr['attribute_options']);
			}
			$details = $attribute->save();
			$json_data = json_decode($details, true);
			if($json_data['status'] == 'error')
			{
				foreach($json_data['error_messages'] AS $err_msg)
				{
					$error_msg .= "<p>".$err_msg."</p>";
				}
				$result_arr = array('err' => true, 'err_msg' => $error_msg);
			}
			else
			{
				$result_arr = array('err' => false, 'err_msg' => '', 'list_row' => $this->adminProductAttributesService->getHTMLListRow($attribute_id), 'unremoved_options_count' => $json_data['unremoved_options_count']);
			}
			echo json_encode($result_arr);
		} else {
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			echo json_encode(array('err' => true, 'err_msg' => $error_msg));
		}
	}

	public function getAttributesDelete()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$row_id = Input::get('row_id');
			$result_arr = $this->adminProductAttributesService->deleteListRow($row_id);
			echo json_encode($result_arr);
		} else {
			$error_msg = Lang::get('common.demo_site_featured_not_allowed');
			echo json_encode(array('err' => true, 'err_msg' => $error_msg));
		}
	}
}