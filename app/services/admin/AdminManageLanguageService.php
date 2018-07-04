<?php
class AdminManageLanguageService
{
	public function getLanguageSettings($id = 0)
 	{
 		if($id) {
 			return Languages::where('languages_id', $id)->first();
 		}
 	}

 	public function getLanguageSettingsByCode($code = 'en')
 	{
 		if($code) {
			$cache_key = 'language_setting_code_cache_key'.$code;
			if (($return_language = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$return_language = Languages::where('code', $code)->first();
				HomeCUtil::cachePut($cache_key, $return_language, Config::get('generalConfig.cache_expiry_minutes'));
			}
 			return $return_language;
 		}
 	}

 	public function buildLanguagesQuery()
	{
		return Languages::Select("languages_id", "name", "code", "sort_order", "status", "is_published", "is_translated")->orderBy("languages_id","DESC");
	}

	public function updateLanguages($input)
 	{
 		$fileds = array("name", "code", "status", "is_published", "is_translated");
		$arr = array();
		foreach ($fileds as $value) {
			if(isset($input[$value]) && $input[$value] != '') {
				$arr[$value] = $input[$value];
			}
		}
		if($input['languages_id']) {
			Languages::where('languages_id',$input['languages_id'])->update($arr);
			$image_id = $input['languages_id'];
		}
		else {
			$image_id = Languages::insertGetId($arr);
		}
		$array_multi_key = array('active_language_list_cache_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
		$file = Input::file('image_name');

		if($file != '')
		{
			$image_ext = $file->getClientOriginalExtension();
			$image_name = $input['code'];//Str::random(20);
			$destinationpath = URL::asset(Config::get("generalConfig.language_image_folder"));
			$allowed_ext = Config::get('generalConfig.language_image_allowed_extensions');
			if(stripos($allowed_ext, $image_ext) === false) {
				$errMsg = trans('admin/languageManage.file_accept');
				return $errMsg;
			} else {
				$image_id = $this->uploadLanguageImage($file, $image_ext, $image_name, $destinationpath, $image_id);
			}
		}
 	}

 	public function uploadLanguageImage($file, $image_ext, $image_name, $destinationpath, $image_id)
	{
		$folder_path = Config::get('generalConfig.language_image_folder');
		$obj = new UserAccountService();
		$obj->chkAndCreateFolder($folder_path);
		// open file a image resource
		Image::make($file->getRealPath())->save(Config::get("generalConfig.language_image_folder").$image_id.'.'.$image_ext);
		list($width, $height)= getimagesize($file);

		$large_width = Config::get('generalConfig.language_image_width');
		$large_height = Config::get('generalConfig.language_image_height');
		if(isset($large_width) && isset($large_height)) {
			Image::make($file->getRealPath())
				->resize($large_width, $large_height,true, false)
				->save($folder_path.$image_id.'.'.$image_ext);
		}
	}

	public function getDeleteLanguage($inputs)
	{
		$languages_list = Languages::where('languages_id',$inputs['language_id'])->first();
		if(isset($languages_list) && count($languages_list) > 0) {
			$lang_base_path = base_path().'/public/'.Config::get("generalConfig.language_image_folder").'/'.$languages_list->languages_id.'.gif';
			if (file_exists($lang_base_path)) {
				unlink('files/language_image/'. $languages_list->languages_id.'.gif');
			}
			if(is_dir(app_path() . '/lang/'.$languages_list->code)) {
				$dir= app_path() . '/lang/'.$languages_list->code.'/';
				$this->rrmdir($dir);
			}
			Languages::where('languages_id',$inputs['language_id'])->delete();
			$array_multi_key = array('active_language_list_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			$result = "success";
		} else {
			$result = "error";
		}
		return $result;
	}

	public function rrmdir($dir)
	{
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (filetype($dir."/".$object) == "dir")
	                    $this->rrmdir($dir."/".$object);
	                else
	                    unlink($dir."/".$object);
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	    }
	}

	public function getLanguagesList()
	{
		$languages_list = Languages::where('status', '=', 'Yes')->lists('name','code');
		return $languages_list;
	}

	public function getLanguagesListHasFolder()
	{
		$languages_arr =array();
		$languages_list = Languages::where('status', '=', 'Yes')->lists('name','code');
		if(count($languages_list) > 0) {
			foreach($languages_list as $key => $val) {
				if (is_dir(app_path() . '/lang/'.$key)
					&& file_exists(app_path() . '/lang/'.$key.'/common.php')) {
						$languages_arr[$key] = $val;
				}
			}
		}
		return $languages_arr;
	}

	public function getActiveLanguagesList()
	{
		$cache_key = 'active_language_list_cache_key';
		if (($languages_list = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$languages_list = Languages::whereRaw('status = ? AND is_published =? AND is_translated = ?', array('Yes', 'Yes', 'Yes'))->select('name', 'languages_id', 'code')->get();
			HomeCUtil::cachePut($cache_key, $languages_list);
		}
		return $languages_list;
	}

	/**
	 * LanguageExport::downloadLanguage()
	 * To download language
	 *
	 * @return void
	 * @access 	public
	 */
	public function downloadLanguage($input)
	{
		$folder_path_arr = array();
		switch($input['folder'])
		{
			case 'all':
				$folder_path_arr = Config::get('translate.trans_folder');
				break;

			default:
				$folder_path_arr[] = $input['folder'];
		}
		$write_string = '<language_editor>';

		//Get modules folder path
		$modules_folder_path_arr = CUtil::getModulesTransFolderList();
		$folder_path_arr = array_merge($folder_path_arr, $modules_folder_path_arr);
		foreach($folder_path_arr as $folder_name=>$folder_path)
		{
			$path = base_path().'/app/'.sprintf($folder_path, $input['language']);
			//echo $path;die;
			if(is_dir($path))
			{
				$files_list = CUtil::readDirectory($path);
			}
			else if(is_file($path))
			{
				$files_list = array($path);
			}
			foreach($files_list as $file_name)
			{
				/*if(in_array($file_name, $this->CFG['not_trans_files'][$folder_name]))
					{
						continue;
					}*/
				$r_file = $file_name;
				if(is_dir($path))
				{
					$r_file = $path.$file_name;
				}
				else
				{
					$spos = 0;
					if(strrpos($file_name, '\\'))
					{
						$spos = strrpos($file_name, '\\')+1;
					}
					elseif(strrpos($file_name, '/'))
					{
						$spos = strrpos($file_name, '/')+1;
					}
					$file_name = substr($file_name, $spos);
				}
				$write_string .= '<file name="'.$file_name.'" folder="'.$folder_name.'" language="'.$input['language'].'"><![CDATA[';
				$write_string .= CUtil::read_file($r_file);
				$write_string .= ']]></file>';
			}
		}
		$write_string .= '</language_editor>';
		//Log::info($write_string);
		CUtil::force_download('language_'.time().'.xml', $write_string);
		exit;
	}

	/**
	 * LanguageExport::writeLanguage()
	 * To write language
	 *
	 * @return boolean
	 * @access 	public
	 */
	public function writeLanguage($input)
	{
		$file = Input::file('language_file');
		/*if($file) {
			echo $file->getRealPath();die;
		}*/
		$objXML = new XmlParser();
		$strYourXML = CUtil::read_file($file->getRealPath());
		if(!($arrOutput = $objXML->parse($strYourXML)))
		{
			//$this->setCommonErrorMsg($this->LANG['language_error_msg_invalid_file_format']);
			//$this->setPageBlockShow('block_msg_form_error');
			return json_encode(array('status' => 'error', 'error_messages' => Lang::get('admin/languageManage.language_error_msg_invalid_file_format')));
			//return false;
		}
		if (isset($arrOutput[0]['children']))
	    {
	    	$this->addNewLanguage($input['code'], '');
	     	$folder_path_arr = Config::get('translate.trans_folder');
	     	//Get modules folder path
			$modules_folder_path_arr = CUtil::getModulesTransFolderList();
			$folder_path_arr = array_merge($folder_path_arr, $modules_folder_path_arr);
	     	foreach($folder_path_arr as $folder_name=>$folder_path)
     		{
     			//$path = $this->CFG['site']['project_path'].sprintf($folder_path, $input['code']);
     			$path = base_path().'/app/'.sprintf($folder_path, $input['code']);
				$folder_path_arr[$folder_name] = $path;
				/*if(isset($this->CFG['not_trans_files'][$folder_name]))
				{
					$from_path = $this->CFG['site']['project_path'].sprintf($this->CFG['trans']['folder'][$folder_name], $this->CFG['lang']['default']);
					foreach($this->CFG['not_trans_files'][$folder_name] as $exclude_file)
					{
						if(is_file($from_path.$exclude_file))
						{
							copy($from_path.$exclude_file, $path.$exclude_file);
						}
					}
				}*/
			}
			foreach($arrOutput[0]['children'] as $key=>$value)
			{
				if(!isset($arrOutput[0]['children'][$key]['attrs']['FOLDER']))
				{
					return false;
				}
				$folder_name = $arrOutput[0]['children'][$key]['attrs']['FOLDER'];

				if($folder_name!='')
				{
					$path = $folder_path_arr[$folder_name];
				}
				if(!isset($arrOutput[0]['children'][$key]['attrs']['NAME']))
				{
					return false;
				}
				if(is_dir($path))
				{
					$path .= $arrOutput[0]['children'][$key]['attrs']['NAME'];
				}
				if(isset($arrOutput[0]['children'][$key]['tagData']))
					CUtil::write_file($path, $arrOutput[0]['children'][$key]['tagData']);
			}
			if ($this->checkIsFilesExist($folder_path_arr, $input['code'], base_path().'/app/'))
			{
				$this->addTranslatedLanguage($input['code']);
			}
			//$this->setFormField('language', '');
			//$this->setFormField('language_label', '');
			//$this->setCommonSuccessMsg($this->LANG['language_success_msg_import']);
			//$this->setPageBlockShow('block_msg_form_success');
			return json_encode(array('status' => 'success', 'success_msg' => Lang::get('admin/languageManage.language_success_msg_import')));
			return true;
	    }
		//$this->setPageBlockShow('block_msg_form_error');
		//$this->setCommonErrorMsg($this->LANG['language_error_msg_invalid_data']);
		return json_encode(array('status' => 'error', 'error_messages' => Lang::get('admin/languageManage.language_error_msg_invalid_data')));
		return false;
	}

	/**
	 * TranslationHandler::addNewLanguage()
	 * To add new language
	 * @param mixed $lang
	 * @param mixed $label
	 * @return
	 */
	public function addNewLanguage($lang, $label)
	{
		$folder_path_arr = Config::get('translate.trans_folder');
		//Get modules folder path
		$modules_folder_path_arr = CUtil::getModulesTransFolderList();
		$folder_path_arr = array_merge($folder_path_arr, $modules_folder_path_arr);
		foreach($folder_path_arr as $key=>$value) {
			$lang_path = base_path().'/app/'.sprintf($value, $lang);
			if (!strstr($lang_path, '.php')) {
				@mkdir($lang_path, 0777, true);
			}
		}
	}

	public function checkIsFilesExist($trans_folder_arr, $lang, $project_path)
	{
		//global $CFG;
		foreach($trans_folder_arr as $key => $val)
		{
			if ($key == 'list_arr' )
			{
				if (!is_dir($project_path.sprintf($val, $lang)))
				{
					return false;
				}
			}
			else if (!is_dir($project_path.sprintf($val, $lang)))
				return false;
		}
		$dir_count_arr=array();
		foreach($trans_folder_arr as $key => $val)
		{
			//todo
			/*foreach($CFG['site']['modules_arr'] as $index=>$module_key)
			{
				if (!is_dir($project_path.'lang'.'/'.$lang.'/'.sprintf($module_key, $lang)))
				{
					return false;
				}
			}*/
			if ($key == 'list_arr')
			{
				if(is_dir($project_path.sprintf($val, $lang)))
				$dir_count_arr[$key]=$this->getFilesCount($project_path.sprintf($val, $lang));
			}
			elseif(is_dir($project_path.sprintf($val, $lang)))
				$dir_count_arr[$key]=$this->getFilesCount($project_path.sprintf($val, $lang));
			else
				$dir_count_arr[$key]=0;
		}
		$basic_dir_count_arr=array();
		$lang = 'en';
		foreach($trans_folder_arr as $key => $val)
		{
			if ($key == 'list_arr' )
			{
				if(is_dir($project_path.sprintf($val, $lang)))
					$basic_dir_count_arr[$key]=$this->getFilesCount($project_path.sprintf($val, $lang));
			}
			elseif (is_dir($project_path.sprintf($val, $lang)))
				$basic_dir_count_arr[$key]=$this->getFilesCount($project_path.sprintf($val, $lang));
			else
				$basic_dir_count_arr[$key]=0;
		}
		foreach($basic_dir_count_arr as $index=>$value)
		{
			if(isset($dir_count_arr[$index]))
			{
				if($dir_count_arr[$index] < $value)
					return false;
			}
		}
		return true;
	}

	/**
	 * TranslationHandler::getFilesCount()
	 * To add new language
	 * @param mixed $folder
	 * @return file count
	 */
	public function getFilesCount($folder)
	{
		$files_count=0;
		if($handle = @opendir($folder))
		{
			while(false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && $file != ".svn" && $file != "admin")
				{
					$files_count++;
				}
			}
			closedir($handle);
		}
		return $files_count;
	}

	//Edit file functions
	public function getLangDirectoryPath($input)
	{
		$dir_path = '';
		if(isset($input['language']) && isset($input['directory']) && $input['language'] != '' && $input['directory'] != '') {
			if(strstr($input['directory'], 'plugins~')) {
				$sub_arr = explode('~', $input['directory']);
				$dir_path = app_path().'/plugins/'.$sub_arr[1].'/lang/'.$input['language'].'/';
			}
			else{
				$dir_path = app_path().'/lang/'.$input['language'].'/'.$input['directory'].'/';
				if($input['directory'] == 'root')
					$dir_path = app_path().'/lang/'.$input['language'].'/';
			}
		}
		return $dir_path;
	}

	public function getLangFilePath($input)
	{
		$file_path = '';
		if(isset($input['language']) && isset($input['directory'])  && isset($input['file']) && $input['directory'] != '' && $input['language'] != '' && $input['file'] != '') {
			if(strstr($input['directory'], 'plugins~')) {
				$sub_arr = explode('~', $input['directory']);
				$file_path = app_path().'/plugins/'.$sub_arr[1].'/lang/'.$input['language'].'/'.$input['file'];
			}
			else {
				$file_path = app_path().'/lang/'.$input['language'].'/'.$input['directory'].'/'.$input['file'];
				if($input['directory'] == 'root')
					$file_path = app_path().'/lang/'.$input['language'].'/'.$input['file'];
			}
		}
		return $file_path;
	}

	public function populateFileNamesList($input, $highlight_value, $unwanted_files_list_arr)
	{
		$files_list = array();
		$dir_path = $this->getLangDirectoryPath($input);
		if(!is_dir($dir_path))
			return $files_list;

		$files = CUtil::readDirectory($dir_path, 'file');
		if(!empty($files)) {
			foreach($files as $file)
				$files_list[$file] = $file;
		}
		return $files_list;
	}

	public function updateFileContent($input)
	{
		$file_path = $this->getLangFilePath($input);
		if($file_path == '')
			return FALSE;

		if( is_dir($file_path) || ! file_exists($file_path))
			return FALSE;

		if(isset($input['lang_phrases']) && !empty($input['lang_phrases'])) {
			$phrases = $input['lang_phrases'];
			foreach($phrases as $key => $val) {
				if(strstr($key, '~'))
				{
					$sub_arr = explode('~', $key);
					unset($phrases[$key]);
					$sub_arr_len = count($sub_arr);
					if($sub_arr_len == 2)
						$phrases[$sub_arr[0]][$sub_arr[1]] = $val;
					else
						$phrases[$sub_arr[0]][$sub_arr[1]][$sub_arr[2]] = $val;
				}
			}
			$output = var_export($phrases,true);
			$output = '<?php return '.$output.';';
			file_put_contents($file_path, $output);
		}
		return true;
	}
}