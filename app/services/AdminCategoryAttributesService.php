<?php

class AdminCategoryAttributesService extends AdminProductCategoryService
{
	public $parent_category_attributes = array();
	public $option_fields = array('select', 'check', 'option', 'multiselectlist');

	public function populateAttributes($category_id = '', $sel_attribute = '')
	{
		if($sel_attribute == '')
		{
			$data_arr['assigned'] = $data_arr['new'] = $data_arr['parent'] = array();
			$inc_assign = $inc_new = $inc_parent = 0;
			// get all assigned attributes based on the selected category
			$added_attribute_details = $this->getCategoryAttributeDetails($category_id);
		}

		$attr_details = Products::getProductAttributeDetails($sel_attribute);
		if(count($attr_details) > 0)
		{
			if($sel_attribute != '')
			{
				return $attr_details[$sel_attribute];
			}
			else
			{
				foreach($attr_details as $key => $attr)
				{
					// check the attribute assigned in parent category
					if(in_array($key, $this->parent_category_attributes))
					{
						$data_arr['parent'][$inc_parent] = $attr_details[$key];
						$inc_parent++;
					}
					else if(in_array($key, $added_attribute_details))
					{
						$assigned_arr[$key] = $attr_details[$key];
						$inc_assign++;
					}
					else
					{
						$data_arr['new'][$inc_new] = $attr_details[$key];
						$inc_new++;
					}
				}

				if($inc_assign)
				{
					$assign_order_arr = array();
					foreach($added_attribute_details as $assign_index => $assign_category_id)
					{
						$assign_order_arr[$assign_index] = $assigned_arr[$assign_category_id];
					}

					$data_arr['assigned'] = $assign_order_arr;
				}
			}
		}
		return $data_arr;
	}

	public function getCategoryAttributeDetails($category_id)
	{
		$current_category_attributes = $parent_category_attributes = array();
		$current_category_id = $category_id;

		$attr_details = Products::getAttributesAssignedForCategory($category_id);
		if(count($attr_details) > 0)
		{
			foreach($attr_details as $attr)
			{
				// compare category id of attribute with current category to get parent category attributes
				if($current_category_id != $attr->category_id)
				{
					$parent_category_attributes[] = $attr->attribute_id;
				}
				// check the attribute already assigned in parent category
				else if(!in_array($attr->attribute_id, $parent_category_attributes))
				{
					$current_category_attributes[] = $attr->attribute_id;
				}
			}
		}
		// assign parent category attributes array in object
		//$this->parent_category_attributes = $parent_category_attributes;
		$this->parent_category_attributes = $parent_category_attributes;
		return $current_category_attributes;
	}

	public function assignAttribute($input_arr)
	{
		$product = Products::initialize();
		$details = $product->assignAttributeForCategory($input_arr['attribute_id'], $input_arr['category_id']);
		$json_data = json_decode($details, true);
		if($json_data['status'] == 'error')
		{
			$result_arr = array('err' => true, 'err_msg' => $json_data['error_msg']);
		}
		else
		{
			$result_arr = array('err' => false, 'err_msg' => '', 'list_row' => $this->getHTMLListRowAssigned($input_arr['attribute_id'], 'assigned', $input_arr['category_id']), 'row_id' => $input_arr['attribute_id']);
		}
		return $result_arr;
	}

	public function getHTMLElement($type, $attr_options, $default_value)
	{
		switch($type)
		{
			case 'text':
				?>
				<input type="text" name="" value="<?php echo $default_value ; ?>" tabindex=-1 class="form-control">
				<?php
				break;
			case 'textarea':
				?>
				<textarea name="" rows="4" cols="35" class="form-control" tabindex=-1><?php echo $default_value ; ?></textarea>
				<?php
				break;
			case 'select':
				?>
				<select name="" tabindex=-1 class="form-control input-medium select2me">
					<option value="">-- Select --</option>
					<?php
					foreach($attr_options as $opt_id => $opt_value)
					{
					?>
					<option value="<?php echo $opt_id;?>"<?php if($opt_value['is_default_option'] == 'yes') echo ' selected="selected"'?>><?php echo $opt_value['option_label'];?></option>
					<?php
					}
					?>
				</select>
				<?php
				break;
			case 'option':?>
                <div class="radio-list">
					<?php
                    foreach($attr_options as $opt_id => $opt_value)
                    {
                    ?>
                    <label>
                        <input type="radio" class="ace" name="" value="<?php echo $opt_id;?>"<?php if($opt_value['is_default_option'] == 'yes') echo ' checked="checked"'?>>
                        <?php echo $opt_value['option_label']; ?>
                    </label>
                    <?php
                    }?>
                </div>
                <?php
				break;
			case 'check':?>
                <div class="checkbox-list">
            	<?php
				foreach($attr_options as $opt_id => $opt_value)
				{
				?>
                <label>
                    <input type="checkbox" class="ace" name="" value="<?php echo $opt_id;?>" tabindex=-1<?php if($opt_value['is_default_option'] == 'yes') echo ' checked="checked"'?>> 
                    <?php echo $opt_value['option_label'];?>
                </label>
				<?php
				}?>
				</div>
                <?php
				break;
			case 'multiselectlist':
				?>
				<select name="" multiple="multiple" tabindex=-1 size="10" class="form-control select2me input-medium">
					<option value="">-- Select --</option>
					<?php
					foreach($attr_options as $opt_id => $opt_value)
					{
					?>
					<option value="<?php echo $opt_id;?>"<?php if($opt_value['is_default_option'] == 'yes') echo ' selected="selected"'?>><?php echo $opt_value['option_label'];?></option>
					<?php
					}
					?>
				</select>
				<?php
				break;
			default:
				die('Error: Developer, Configuration file update, needs to be reflected in fn:getHTMLElement , ' . $type);
				?>
				<input type="text" name=""  class="form-control" value="<?php echo $type;?>" tabindex=-1>
				<?php
		}
	}

	public function getHTMLListRowAssigned($row_id, $sel_option = 'assigned', $category_id)
	{
		$attr_details = Products::getProductAttributeDetails($row_id);
		if(count($attr_details) > 0)
		{
			foreach($attr_details as $key => $attr)
			{
				ob_start();
				if($sel_option == 'assigned')
				{
				?>
				<tr id="formBuilderRow_<?php echo $attr['attribute_id'];?>" class="formBuilderRow formAssignedAttributes">
				<?php
				}
				else
				{
				?>
				<tr id="formBuilderNewRow_<?php echo $attr['attribute_id'];?>" class="nodrag nodrop formBuilderAddRow formUnassignedAttributes" title="<?php echo trans('admin/manageCategory.double_click_assign_attributes_msg'); ?>">
				<?php
				}
				?>
						<td><?php echo $attr['attribute_label'];?></td>
				        <td><?php echo $this->getHTMLElement($attr['attribute_question_type'], $attr['attribute_options'], $attr['default_value']);?></td>
				        <td class="formBuilderAction status-btn clsUnasinedAtributes">                        
						<a class="btn btn-xs btn-info" onclick="openViewAttributeFancyBox('<?php echo URL::action('AdminCategoryAttributesController@getViewAttribute') ?>?attribute_id=<?php echo $attr['attribute_id'];?>')" href="javascript:;" title="<?php echo trans('admin/manageCategory.view_attribute') ?> " id="formBuilderRowView_<?php echo $attr['attribute_id'];?>"><i class="fa fa-eye"></i></a>
				        <?php
				        if($sel_option == 'assigned')
				        {
				        ?>
				        <a class="btn btn-xs red" onclick="javascript:formBuilderRemoveListRow(<?php echo $attr['attribute_id'];?>, <?php echo $category_id;?>);" href="javascript: void(0);" title="<?php echo trans('admin/manageCategory.remove_attribute'); ?> "><i class="fa fa-trash-o"></i> </a>
				        <?php
				        }
				        else
				        {
						?>
						<a class="btn btn-xs green" onclick="javascript:formBuilderAddListRow(<?php echo $attr['attribute_id'];?>, <?php echo $category_id;?>);" href="javascript: void(0);" title="<?php echo trans('admin/manageCategory.assign_attribute_title'); ?> "><i class="fa fa-share bigger-130"></i> </a>
						<?php
						}
						?>
				        </td>
				        </tr>
				<?php
				$content = ob_get_clean();
				return $content;
			}
		}
	}

	public function removeAttribute($category_id, $attribute_id)
	{
		$product = Products::initialize();
		$details = $product->removeAssignedAttributeForCategory($category_id, $attribute_id);


		$json_data = json_decode($details, true);
		if($json_data['status'] == 'error')
		{
			$result_arr = array('err' => true, 'err_msg' => $json_data['error_msg']);
		}
		else
		{
			$result_arr = array('err' => false, 'err_msg' => '', 'list_row' => $this->getHTMLListRowAssigned($attribute_id, 'removed', $category_id), 'row_id' => $attribute_id);
		}
		return $result_arr;
	}

	public function updateListRowOrder($input_arr)
	{
		foreach($input_arr['attrdnd'] as $display_order => $attribute_id_str)
		{
			$temp = explode("_", $attribute_id_str);
			$attribute_id = (isset($temp[1]) && $temp[1]) ? (int) $temp[1] : false;
			$category_id = $input_arr['category_id'];
			if($attribute_id)
			{
				Products::updateAssignedAttributeDisplayOrder($attribute_id, $category_id, $display_order);
			}
		}
	}
}