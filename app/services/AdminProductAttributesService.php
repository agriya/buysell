<?php

class AdminProductAttributesService extends AdminCategoryAttributesService
{
	public $option_fields = array('select', 'check', 'option', 'multiselectlist');

	public function processValidationRules($validation_rules)
	{
		if(!empty($validation_rules) && is_array($validation_rules))
		{
			foreach($validation_rules as &$validation_rule)
			{
				if(Input::get($validation_rule.'_input') != "")
				{
					$validation_rule .= '-' . Input::get($validation_rule.'_input');
				}
			}
			//return implode('|',$validation_rules);
			return $validation_rules;
		}
		return '';
	}

	public function getHTMLListRow($row_id)
	{
		$attr_details = Products::getProductAttributeDetails($row_id);
		if(count($attr_details) > 0)
		{
			ob_start();
			?>
			<tr id="formBuilderRow_<?php echo $attr_details[$row_id]['attribute_id'];?>" class="formBuilderRow">
				<td><?php echo $attr_details[$row_id]['attribute_label'];?></td>
		        <td><?php echo $this->getHTMLElement($attr_details[$row_id]['attribute_question_type'], $attr_details[$row_id]['attribute_options'], $attr_details[$row_id]['default_value']);?></td>
		        <td><?php echo $attr_details[$row_id]['validation_rules'];?></td>
		        <td><?php echo $attr_details[$row_id]['default_value'];?></td>
		        <td>
                	<?php
						$lbl_class = "";
						if(strtolower ($attr_details[$row_id]['status']) == "active")
							$lbl_class = "label-success";
						elseif(strtolower ($attr_details[$row_id]['status']) == "inactive")
							$lbl_class = "label-info arrowed-in arrowed-in-right";
					?>
                	<span class="label <?php echo $lbl_class ;?>"><?php echo $attr_details[$row_id]['status'];?></span>
                </td>
		        <td class="formBuilderAction status-btn">
                    <a class="btn blue btn-xs" onclick="javascript:formBuilderEditListRow(<?php echo $attr_details[$row_id]['attribute_id'];?>);" href="javascript: void(0);" title="<?php echo trans('admin/manageCategory.edit_attribute'); ?>"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-xs" onclick="javascript:formBuilderRemoveListRow(<?php echo $attr_details[$row_id]['attribute_id'];?>);" href="javascript: void(0);" title="<?php echo trans('admin/manageCategory.remove_attribute'); ?>"><i class="fa fa-trash-o"></i></a>
		        </td>
	        </tr>
			<?php
			$content = ob_get_clean();
			return $content;
		}
	}

	public function deleteListRow($row_id)
	{
		$attribute = Products::initializeAttribute();
		$details = $attribute->deleteAttribute($row_id);
		$json_data = json_decode($details, true);
		if($json_data['status'] == 'error')
		{
			$result_arr = array('result' => 'failed', 'row_id'=> $row_id, 'err_msg' => $json_data['error_msg']);
		}
		else
		{
			$result_arr = array('result' => 'success', 'row_id'=> $row_id);
		}
		return $result_arr;
	}
}