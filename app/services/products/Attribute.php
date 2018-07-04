<?php

class Attribute {

	protected $attribute_id;

	protected $fields_arr = array();

	public function __construct($attribute_id = '')
	{
		$this->attribute_id = $attribute_id;
	}

	public function getAttributeId()
	{
		return $this->attribute_id;
	}

	public function setAttributeLabel($val)
	{
		$this->fields_arr['attribute_label'] = $val;
	}

	public function setAttributeDescription($val)
	{
		$this->fields_arr['description'] = $val;
	}

	public function setAttributeType($val)
	{
		$this->fields_arr['attribute_question_type'] = $val;
	}

	public function setAttributeValidation($val)
	{
		$this->fields_arr['validation_rules'] = $val;
	}

	public function setAttributeSearchable($val)
	{
		$this->fields_arr['is_searchable'] = $val;
	}

	public function setAttributeStatus($val)
	{
		$this->fields_arr['status'] = $val;
	}

	public function setAttributeDefaultValue($val)
	{
		$this->fields_arr['default_value'] = $val;
	}

	public function setAttributeOptions($val)
	{
		$this->fields_arr['attribute_options'] = $val;
	}

	public function setAttributeOptionsIdVal($val)
	{
		$this->fields_arr['attribute_options_val'] = $val;
	}

	public function validateAttributeDetails($input_arr)
	{
		$rules_arr['attribute_label'] = 'Required';
		$rules_arr['attribute_question_type'] = 'Required';

		$message = array('attribute_label.required' => trans('products.attribute_label_required'),
						'attribute_question_type.required' => trans('products.attribute_type_required')
						);
		return array('rules' => $rules_arr, 'messages' => $message);
	}

	public function isAttributeExists($attribute_id)
	{
		$attr_details = $this->getAttributeDetails($attribute_id);
		$attribute_count = count($attr_details);
	    return $attribute_count;
	}

	public function getAttributeDetails($attribute_id)
	{
		$attr_details = ProductAttributes::Select('attribute_label', 'attribute_question_type', 'validation_rules')->whereRaw('id = ?', array($attribute_id))->first();
		return $attr_details;
	}

	public function save()
	{
		$validator_arr = $this->validateAttributeDetails($this->fields_arr);
		$validator = Validator::make($this->fields_arr, $validator_arr['rules'], $validator_arr['messages']);
		if($validator->passes())
		{
			if($this->attribute_id == '')
			{
				if(count($this->fields_arr) > 0)
				{
					$validation_rules = '';
					if(isset($this->fields_arr['validation_rules']) && !empty($this->fields_arr['validation_rules']))
					{
						$validation_rules = $this->fields_arr['validation_rules'];
						if(is_array($validation_rules))
						{
							$validation_rules = implode('|', $this->fields_arr['validation_rules']);
						}
						$add_data_arr['validation_rules'] = $validation_rules;
					}

					$add_data_arr['date_added'] = DB::raw('NOW()');
					if(isset($this->fields_arr['attribute_label']))
						$add_data_arr['attribute_label'] = $this->fields_arr['attribute_label'];
					if(isset($this->fields_arr['description']))
						$add_data_arr['description'] = $this->fields_arr['description'];
					if(isset($this->fields_arr['default_value']))
						$add_data_arr['default_value'] = $this->fields_arr['default_value'];
					if(isset($this->fields_arr['is_searchable']))
						$add_data_arr['is_searchable'] = $this->fields_arr['is_searchable'];
					if(isset($this->fields_arr['status']))
						$add_data_arr['status'] = $this->fields_arr['status'];
					if(isset($this->fields_arr['attribute_question_type']))
						$add_data_arr['attribute_question_type'] = $this->fields_arr['attribute_question_type'];

					$attribute_id = ProductAttributes::insertGetId($add_data_arr);

					if (isset($this->fields_arr['attribute_options']) && !empty($this->fields_arr['attribute_options']) && sizeof($this->fields_arr['attribute_options']) > 0)
					{
						$default_value = '';
						if(isset($this->fields_arr['default_value']) && $this->fields_arr['default_value'] != '')
						{
							$default_value = $this->fields_arr['default_value'];
						}
				    	$this->addAttributeOptions($attribute_id, $this->fields_arr['attribute_options'], $default_value);
				    }
				    return json_encode(array('status' => 'success', 'attribute_id' => $attribute_id));
				}
			}
			else
			{
				if(count($this->fields_arr) > 0)
				{
					if(!$this->isAttributeExists($this->attribute_id))
					{
						return json_encode(array('status' => 'error', 'error_messages' => trans('products.attribute_not_found')));
					}
					else
					{
						$validation_rules = null;
						if(isset($this->fields_arr['validation_rules']) && !empty($this->fields_arr['validation_rules']))
						{
							$validation_rules = $this->fields_arr['validation_rules'];
							if(is_array($validation_rules))
							{
								$validation_rules = implode('|', $this->fields_arr['validation_rules']);
							}

						}
						$this->fields_arr['validation_rules'] = $validation_rules;

						$unremoved_options_count = $this->updateListRow($this->attribute_id, $this->fields_arr);
						return json_encode(array('status' => 'success', 'unremoved_options_count' => $unremoved_options_count));
					}
				}
			}
		}
		else
		{
			$error_msg = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
	}

	public function getAttributeOptions($attribute_id)
	{
		$cache_key = 'attribute_options_'.$attribute_id;
		if (($d_arr = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$d_arr = ProductAttributeOptions::where('attribute_id', '=', $attribute_id)	->orderBy('id', 'ASC')->get(array('id', 'option_label'))->toArray();
			HomeCUtil::cachePut($cache_key, $d_arr, Config::get('generalConfig.cache_expiry_minutes'));
		}
		$data = array();
		foreach($d_arr AS $val)
		{
			$data[$val['id']] = $val['option_label'];
		}
		return $data;
	}

	public function addAttributeOptions($attribute_id, $attribute_options, $attribute_default_value)
	{
		if(empty($attribute_options))
			return false;

		for($i = 0 ; $i < sizeof($attribute_options) ; $i++)
		{
			if(empty($attribute_options[$i])) continue;

			$is_default_option = (strcmp($attribute_options[$i], $attribute_default_value) == 0) ?  'yes': 'no';

			$data_arr['attribute_id'] = $attribute_id;
			$data_arr['option_label'] = $attribute_options[$i];
			$data_arr['option_value'] = $attribute_options[$i];
			$data_arr['is_default_option'] = $is_default_option;

			$attribute_option_id = ProductAttributeOptions::insertGetId($data_arr);

	        // if default option saved then update this id in attribute default value
	        if($is_default_option == 'yes')
	        {
				$this->setAttributeDefaultOptionId($attribute_id, $attribute_option_id);
			}
	    }
	    return true;
	}

	public function setAttributeDefaultOptionId($attribute_id, $attribute_option_id)
	{
		$data_arr['default_value'] = $attribute_option_id;
		ProductAttributes::whereRaw('id = ?', array($attribute_id))->update($data_arr);
	}

	public function isOptionsAlreadyUsed($attribute_id)
	{
		$attribute_used = false;

		// check attribute options used in items
		$attr_option_count = ProductAttributesOptionValues::whereRaw('attribute_id = ?', array($attribute_id))->count();
		if($attr_option_count > 0)
		{
			$attribute_used = true;
		}
		else
		{
			// check attributes without options like textbox/textarea used in items
			$attr_values_count = ProductAttributesValues::whereRaw('attribute_id = ?', array($attribute_id))->count();
			if($attr_values_count > 0)
			{
				$attribute_used = true;
			}
		}
		return $attribute_used;
	}

	public function getAttribute($attribute_id)
	{
		$option_fields = array('select', 'check', 'option', 'multiselectlist');
		$attr_details = Products::getProductAttributeDetails($attribute_id);
		if(count($attr_details) > 0)
		{
			$attributes = $attr_details[$attribute_id];

			if(in_array($attributes['attribute_question_type'], $option_fields))
			{
	    		$attributes['options'] = $this->getAttributeOptionRows($attributes['attribute_id']);
	    		$attributes['options_size'] = sizeof($attributes['options']);
	        	$attributes['options_used'] = $this->isOptionsAlreadyUsed($attributes['attribute_id']);
	    	}
			else
			{
				$attributes['options_size'] = 0;
	    		$attributes['options_used'] = false; // just to make sure that this field exists to check condition
			}
			return $attributes;
		}
		return false;
	}

	public function getAttributeOptionRows($row_id)
	{
		$attr_option_details = ProductAttributeOptions::Select('id', 'is_default_option', 'option_label')->whereRaw('attribute_id = ?', array($row_id))->get();
		$attrib = array();
		$i = 0;
		if(count($attr_option_details) > 0)
		{
			foreach($attr_option_details as $attr_key => $attr)
			{
				$attrib[$i]['id'] = $attr['id'];
				$attrib[$i]['option_label'] = $attr['option_label'];
				$attrib[$i]['is_default_option'] = $attr['is_default_option'];
				$i++;
			}
			return $attrib;
		}
		return $attrib;
	}

	public function deleteAllAttributeOptions($attribute_id)
	{
		# Get all attribute option ids related to the deleted attribute
		$attr_option_details = ProductAttributeOptions::Select('id')->whereRaw('attribute_id = ?', array($attribute_id))->get();
		if(count($attr_option_details) > 0)
		{
			foreach($attr_option_details as $attr_option)
			{
				# Delete attribute options
		    	$this->deleteAttributeOption($attribute_id, $attr_option['id']);
			}
		}
		return true;
	}

	public function deleteAttributeOption($attribute_id, $attribute_option_id)
	{
		ProductAttributeOptions::whereRaw('id = ? AND attribute_id = ?', array($attribute_option_id, $attribute_id))->delete();
		return true;
	}

	public function updateAttributes($attribute_id, $input_arr)
	{
		$validation_rules = '';
		if(isset($input_arr['validation_rules']) && !empty($input_arr['validation_rules']))
		{
			$validation_rules = $input_arr['validation_rules'];
			if(is_array($validation_rules))
			{
				$validation_rules = implode('|', $input_arr['validation_rules']);
			}
			$data_arr['validation_rules'] = $validation_rules;
		}

		if(isset($input_arr['attribute_label']))
			$data_arr['attribute_label'] = $input_arr['attribute_label'];
		if(isset($input_arr['attribute_question_type']))
			$data_arr['attribute_question_type'] = $input_arr['attribute_question_type'];
		if(isset($input_arr['default_value']))
			$data_arr['default_value'] = $input_arr['default_value'];
		if(isset($input_arr['is_searchable']))
			$data_arr['is_searchable'] = $input_arr['is_searchable'];
		if(isset($input_arr['status']))
			$data_arr['status'] = $input_arr['status'];
		if(isset($input_arr['status']))
			$data_arr['description'] = $input_arr['description'];
		ProductAttributes::whereRaw('id = ?', array($attribute_id))->update($data_arr);
	}

	public function updateAttributesOptions($attribute_id, $input_arr, $attribute_options_ids, $is_default_option)
	{
		$data_arr['option_value'] = $input_arr['attribute_options_val'][$attribute_options_ids];
		$data_arr['option_label'] = $input_arr['attribute_options_val'][$attribute_options_ids];
		$data_arr['is_default_option'] = $is_default_option;
		ProductAttributeOptions::whereRaw('id = ?', array($attribute_options_ids))->update($data_arr);
	}

	public function isAttributeOptionsUsed($attribute_option_id)
	{
		$option_used = false;

		// check attribute options used in items
		$attr_option_count = ProductAttributesOptionValues::whereRaw('attribute_options_id = ?', array($attribute_option_id))->count();
		if($attr_option_count > 0)
		{
			$option_used = true;
		}

		return $option_used;
	}

	public function updateListRow($attribute_id, $data_arr)
	{
		$attributes = $this->getAttribute($attribute_id);

		$default_value = '';
		if(isset($data_arr['default_value']) && $data_arr['default_value'] != '')
		{
			$default_value = $data_arr['default_value'];
		}

		// update attributes if the attribute type didn't change
		if(isset($data_arr['attribute_question_type']))
		{
			if($attributes['attribute_question_type'] != $data_arr['attribute_question_type'])
			{
				if($this->isOptionsAlreadyUsed($attribute_id) &&
					((in_array($data_arr['attribute_question_type'], array_keys(Config::get('products.ui_no_options'))) && in_array($attributes['attribute_question_type'], array_keys(Config::get('products.ui_options')))) ||
					(in_array($attributes['attribute_question_type'], array_keys(Config::get('products.ui_no_options'))) && in_array($data_arr['attribute_question_type'], array_keys(Config::get('products.ui_options'))))))
				{
					// Check attribute is used or not
					return json_encode(array('status' => 'error', 'error_messages' => trans('products.attribute_in_use_cant_change')));
					//return array('err' => true, 'err_msg' => 'The attribute options are in use, so attribute type cannot be changed.');
				}
				else
				{
					if( in_array($data_arr['attribute_question_type'], array_keys(Config::get('products.ui_no_options'))) &&
						 in_array($attributes['attribute_question_type'], array_keys(Config::get('products.ui_no_options'))))
					{
						# Remove all options for this attribute.
						$this->deleteAllAttributeOptions($attribute_id);
					}
				}
			}
		}

		# Remove all options for this attribute.
		$this->updateAttributes($attribute_id, $data_arr);

		// check if this attribute type has options? and if available update them.
		$ui_elements_options =  array_keys(Config::get('products.ui_options'));
		$unremoved_options_count = 0;
		if(isset($data_arr['attribute_question_type']))
		{
			if(in_array($data_arr['attribute_question_type'], $ui_elements_options)
				|| in_array($attributes['attribute_question_type'], $ui_elements_options))
			{
				// options available
				// update any existing options changes
				$removed_options = array();
				for($i = 0 ; $i < $attributes['options_size']; $i++)
				{
					$attribute_options_ids = $attributes['options'][$i]['id'];
					if(isset($data_arr['attribute_options_val'][$attribute_options_ids]) && !empty($data_arr['attribute_options_val'][$attribute_options_ids]))
					{
						//$this->setFormField('attribute_options_' . $attribute_options_ids, $_REQUEST['attribute_options_' . $attribute_options_ids]);
						//Update Product attribute options
						$is_default_option = (strcmp($data_arr['attribute_options_val'][$attribute_options_ids], $default_value) == 0) ? 'yes': 'no';
						$this->updateAttributesOptions($attribute_id, $data_arr, $attribute_options_ids, $is_default_option);

						// if default option saved then update this id in attribute default value
				        if($is_default_option == 'yes')
				        {
							$this->setAttributeDefaultOptionId($attribute_id, $attribute_options_ids);
						}
					}
					else
					{
						$removed_options[] = $attribute_options_ids;
					}
				}

				// Add any new options
				if (isset($data_arr['attribute_options']) && !empty($data_arr['attribute_options']) && sizeof($data_arr['attribute_options']) > 0)
				{
					$this->addAttributeOptions($attribute_id, $data_arr['attribute_options'], $default_value);
				}

				// Remove existing options which are removed by user
				$removed_options_size = sizeof($removed_options);
				if($removed_options_size)
				{
					for($j = 0 ; $j < $removed_options_size ; $j++)
					{
						if(!$this->isAttributeOptionsUsed($removed_options[$j]))
						{
							# Remove all details for the selected attribute option.
							$this->deleteAttributeOption($attribute_id, $removed_options[$j]);
						}
						else
						{
							$unremoved_options_count++;
						}
			        }
				}
			}
		}
		return $unremoved_options_count;
	}

	public function deleteAttribute($attribute_id)
	{
		if($this->isOptionsAlreadyUsed($attribute_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => trans('products.attribute_in_use_cant_delete')));
		}
		ProductAttributes::whereRaw('id = ?', array($attribute_id))->delete();
		$this->deleteAllAttributeOptions($attribute_id);
		return json_encode(array('status' => 'success', 'attribute_id' => $attribute_id));
	}
}