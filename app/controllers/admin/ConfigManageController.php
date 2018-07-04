<?php
//@added by mohamed_158at11
class ConfigManageController extends BaseController
{
	protected $isProtected = 1;
	protected $page_arr = array();
	protected $action_arr = array();

	protected $populate_section_arr = array();
	protected $config_type_arr = array();
	protected $tab_list_arr = array();

	public function configManage()
	{
		$input_arr = Input::All();
		//$this->tab_list_arr = array("site" => 1, "request" => 2, "tour" => 3, "module" => 4, "publish" => 5, "language" => 6 , "payment" => 7, "withdraw" => 8);
		$this->tab_list_arr = Config::get('generalConfig.config_tab_list_arr');
		$is_allowed_page = true; //*** Need to check with ACL
		if(isset($input_arr['config_category']) && $is_allowed_page)
		{
			$this->getConfigSections($input_arr['config_category']);
			if(isset($input_arr['act']) && $input_arr['act'] == 'config_update')
			{
				if(!BasicCUtil::checkIsDemoSite())
				{

					//*** Need to check allo ACL with config sections
					$rules_arr = array();
					foreach($this->config_type_arr AS $key => $value)
					{
						$temp_rule_arr = array();
						if(strtolower($value) == 'int')
						{
							$temp_rule_arr = array($key => 'required|regex:'."/^[0-9]+$/");
						}
						else if(strtolower($value) == 'email')
						{
							$temp_rule_arr = array($key => 'required|email');
						}
						else if(strtolower($value) == 'boolean')
						{
							$temp_rule_arr = array($key => 'required|isBoolean');
						}
						else if(strtolower($value) == 'real')
						{
							$temp_rule_arr = array($key => 'required|regex:'."/^[0-9]+(\\.[0-9]+)?$/");
						}
						$rules_arr = array_merge($rules_arr, $temp_rule_arr);
					}
					$validator = Validator::make($input_arr, $rules_arr, $messages = array());
					if ($validator->passes())
					{
						//To update config values
						$this->updateConfig($input_arr);
						$this->setConfigDetails($input_arr);
						//To falsh the success message
						return View::make('admin.populateConfig')->with('populate_section_arr', $this->populate_section_arr)->with('tab_list_arr', $this->tab_list_arr)->with('success_msg', trans('configManage.update_success'));
					}
					else
					{
						$error_message_arr = array();
						$messages = $validator->messages();
						foreach($rules_arr AS $field_name => $field_val)
						{
							if($messages->has($field_name))
							{
								foreach ($messages->get($field_name) as $message)
								{
								    $error_message_arr[$field_name] = $message;
								}
							}
						}
						$this->setConfigDetails($input_arr);

						//To falsh the error message
						return View::make('admin.populateConfig')->with('populate_section_arr', $this->populate_section_arr)->with('tab_list_arr', $this->tab_list_arr)->with('error_message_arr', $error_message_arr)->with('error_msg', trans('common.correct_errors'));
					}
				}
				else
				{

					return View::make('admin.populateConfig')->with('populate_section_arr', $this->populate_section_arr)->with('tab_list_arr', $this->tab_list_arr)->with('error_msg', trans('common.demo_site_featured_not_allowed'));
				}
			}
			return View::make('admin.populateConfig')->with('populate_section_arr', $this->populate_section_arr)->with('tab_list_arr', $this->tab_list_arr);
		}
		else
		{
			//To-do  Redirect to admin index page.
		}
		return View::make('admin.configManage')->with('tab_list_arr', $this->tab_list_arr);
	}

	public function setConfigDetails($input_arr)
	{
		//To rest to updated content
		foreach($this->populate_section_arr AS $section_key => $section_val)
		{
			foreach($section_val['records'] AS $config_key => $config_val)
			{
				$this->populate_section_arr[$section_key]['records'][$config_key]['config_value'] = $input_arr[$config_val['config_var']];
			}
		}

	}

	public function updateConfig($input_arr)
	{
		$cache_key = 'config_data_key';
		foreach($this->config_type_arr AS $key => $value)
		{
			ConfigData::whereRaw('config_var = ?', array($key))->update(array('config_value' => $input_arr[$key]));
			if(HomeCUtil::cacheForgot($cache_key))
			{
				$data = DB::table('config_data')->get();
				HomeCUtil::cachePut($cache_key, $data);
			}
		}
	}

	public function getConfigSections($config_category)
	{
		$config_sections = array();

		if(Config::get('generalConfig.config_tab_section_list_arr.'.$config_category))
		{
			$o_by_arr = Config::get('generalConfig.config_tab_section_list_arr.'.$config_category);
			$order_by = 'ORDER BY FIELD (config_section, "'.implode('", "', $o_by_arr).'")';
		}
		else
			$order_by = 'order by config_section';
		$config_datas = ConfigData::whereRaw('editable = ? AND config_category = ? group by config_section '.$order_by,
												array('Yes', $config_category))->get();

		if(count($config_datas) > 0)
		{
			$i = 0;
			foreach($config_datas AS $config)
			{
				$config_sections[$i] = array(
											'config_data_id' => $config->config_data_id,
											'config_type' => $config->config_type,
											'section' => $config->config_section,
											'records' => $this->populateRecords($config->config_section, $config_category)
											);
				$i++;
			}
		}
		$this->populate_section_arr = $config_sections;
	}

	public function populateRecords($section, $config_category)
	{
		$config_data_arr = array();
		$config_datas = ConfigData::whereRaw('editable = ? AND config_category = ? AND config_section = ?',
												array('Yes', $config_category, $section))->orderby('config_section', 'ASC')->orderby('edit_order', 'ASC')->get();
		if(count($config_datas) > 0)
		{
			$i = 0;
			foreach($config_datas AS $config)
			{
				$this->config_type_arr[$config->config_var] = $config->config_type;
				$config_data_arr[$i] = array(
										'config_data_id' => $config->config_data_id,
										'config_var' => $config->config_var,
										'config_value' => $config->config_value,
										'config_type' => $config->config_type,
										'description' => $config->description
										);
				$i++;
			}
		}
		return $config_data_arr;
	}

	public function clearCache()
	{
		if(Input::has('cache_options'))
		{
			$cache_options = Input::get('cache_options');
			if($cache_options == 'clear_all')
				Cache::flush();
			else
			{
				$cache_key = 'config_data_key';
				HomeCUtil::cacheForgot($cache_key);
			}
		}
	}

}

