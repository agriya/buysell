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
//@added by manikandan_133at10
class AdminManageLanguageController extends BaseController
{
	function __construct()
	{
        $this->languages_service = new AdminManageLanguageService();
        parent::__construct();
    }

	/*function getIndex(){

	}*/
	public function getIndex()
	{
		$id = Input::has('languages_id') ? Input::get('languages_id') : 0;
		$details = $d_arr = array();
		$input = Input::All();
		if($id == 0)
		{
			$d_arr['mode'] 		= 'add';
			$d_arr['pageTitle'] = Lang::get('admin/languageManage.managelanguage_add_lang_det');
			$d_arr['actionicon'] ='<i class="fa fa-language"><sup class="fa fa-plus"></sup></i>';
			$d_arr['language_details'] = array();
		}
		else
		{
			$d_arr['mode'] 		= 'edit';
			$d_arr['pageTitle'] = Lang::get('admin/languageManage.managelanguage_edit_lang_det');
			$d_arr['actionicon'] ='<i class="fa fa-language"><sup class="fa fa-pencil font11"></sup></i>';
			$language_details = $this->languages_service->getLanguageSettings($id);
			if(count($language_details) > 0) {
				if($language_details->code == 'en') {
					return Redirect::to('admin/manage-language')->with('error_message', Lang::get('admin/languageManage.not_allowed_edit_lang'));
				}
			}
			$d_arr['language_details'] 	= $language_details;
		}
		$d_arr['languages_id'] = $id;

		$perPage    					= 10;
		$q 								= $this->languages_service->buildLanguagesQuery();
		$details 						= $q->paginate($perPage);
		return View::make('admin.manageLanguages', compact('details', 'd_arr'));
	}

	public function postIndex()
	{
		//echo '<pre>';print_r(Input::All());echo '</pre>';die;
		if(!BasicCUtil::checkIsDemoSite()){
			$input = Input::All();
			$messages = $rules = array();
			$languages_id = $input['languages_id'];
			if($languages_id == 0) {
				$rules = array('name'=>'required|unique:languages,name,'.$languages_id.',languages_id', 'code' => 'required|unique:languages,code,'.$languages_id.',languages_id', 'image_name' => 'required');
			} else {
				$rules = array('name'=>'required|unique:languages,name,'.$languages_id.',languages_id', 'code' => 'required|unique:languages,code,'.$languages_id.',languages_id');
			}
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			if(Input::has('is_translated')) {
				$lang_code = $input['code'];
				if($input['is_translated'] == 'Yes') {
					$files_exits = false;
					if (is_dir(app_path() . '/lang/'.$lang_code)
								&& file_exists(app_path() . '/lang/'.$lang_code.'/common.php')) {
						$files_exits = true;
					}
					if(!$files_exits)
						return Redirect::back()->withInput()->with('warning_message', str_replace('VAR_LANG_NAME', $input['name'], Lang::get('admin/languageManage.confirm_language_files_exists')));
				}
			}
			$value = $this->languages_service->updateLanguages($input);
			if($value != '') {
				return Redirect::back()->withInput()->with('error_message',$value);
			} else {
				if($languages_id == 0) {
					return Redirect::to('admin/manage-language')->with('success_message', Lang::get('admin/languageManage.managelanguage_lang_added_succ_msg'));
				} else {
					return Redirect::to('admin/manage-language')->with('success_message', Lang::get('admin/languageManage.managelanguage_lang_updated_succ_msg'));
				}
			}
		}else{
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
	}

	public function getSettings()
	{
		$details = $d_arr = array();
		$d_arr['pageTitle'] = Lang::get('admin/languageManage.managelanguage_lang_settings');
		$d_arr['language_list'] = $this->languages_service->getLanguagesList();
		$lang = Config::get('generalConfig.lang');
		$is_multi_lang_support = Config::get('generalConfig.is_multi_lang_support');
		return View::make('admin.manageLanguagesSettings', compact('details', 'd_arr', 'lang', 'is_multi_lang_support'));
	}

	public function postSettings()
	{
		$cache_key = 'config_data_key';
		if(BasicCUtil::checkIsDemoSite()) {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		} else {
			$input = Input::all();
			$messages = $rules = array();
			$rules = array('default_lang_code'=>'required');
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes()) {
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$default_lang_code = $input['default_lang_code'];
			$multi_lang_support = $input['multi_lang_support'];
			if(isset($default_lang_code)) {
				ConfigData::whereRaw('config_var = ? AND file_name = ?', array('lang', 'generalConfig'))->update(array('config_value' => $default_lang_code));
				if(HomeCUtil::cacheForgot($cache_key))
				{
					$data = DB::table('config_data')->get();
					HomeCUtil::cachePut($cache_key, $data);
				}
			}
			$cookie = BasicCUtil::getCookie(Config::get('generalConfig.site_cookie_prefix')."_selected_language");
			if(isset($multi_lang_support) && $multi_lang_support !='') {
				if($multi_lang_support == 'Yes') {
					$value = 1;
				} else {
					$value = 0;
				}
				$cookie = Cookie::forever(Config::get('generalConfig.site_cookie_prefix')."_selected_language", $default_lang_code);
				ConfigData::whereRaw('config_var = ? AND file_name = ?', array('is_multi_lang_support', 'generalConfig'))->update(array('config_value' => $value));
				if(HomeCUtil::cacheForgot($cache_key))
				{
					$data = DB::table('config_data')->get();
					HomeCUtil::cachePut($cache_key, $data);
				}
			}
			return Redirect::to('admin/manage-language/settings')->with('success_message', Lang::get('admin/languageManage.managelanguage_lang_det_updated_succ_msg'))->withCookie($cookie);
		}

	}

	public function getExport()
	{
		$d_arr['pageTitle'] = Lang::get('admin/languageManage.language_export_title');
		$d_arr['language_list'] = $this->languages_service->getLanguagesListHasFolder();
		return View::make('admin.languagesExport', compact('d_arr'));
	}

	public function postExport()
	{
		if(BasicCUtil::checkIsDemoSite()) {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
		else {
			$input = Input::All();
			$this->languages_service->downloadLanguage($input);
		}
	}

	public function getImport()
	{
		$d_arr['pageTitle'] = Lang::get('admin/languageManage.language_import_title');
		$d_arr['language_list'] = $this->languages_service->getLanguagesList();
		return View::make('admin.languagesImport', compact('d_arr'));
	}

	public function postImport()
	{
		if(BasicCUtil::checkIsDemoSite()) {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
		else {
			$input = Input::All();
			$messages = $rules = array();
			//$rules = array('name'=>'required|Exists:languages,name', 'code' => 'required|Exists:languages,code', 'language_file' => 'required');
			$rules = array('code'=>'required', 'language_file' => 'required');
			$validator = Validator::make($input, $rules, $messages);
			if (!$validator->passes()) {
				return Redirect::back()->withInput()->withErrors($validator);
			}

			$response = $this->languages_service->writeLanguage($input);
			$json_data = json_decode($response, true);
			if($json_data['status'] == 'error')
			{
				$error_msg = $json_data['error_messages'];
				return Redirect::to('admin/manage-language/import')->with('error_message', $error_msg)->withInput();
			}
			else {
				$success_msg = '';
				if(isset($json_data['success_msg']))
					$success_msg = $json_data['success_msg'];
				return Redirect::to('admin/manage-language/import')->with('success_message', $success_msg);
			}
		}
	}

	public function getFileEdit()
	{
		$d_arr['pageTitle'] = Lang::get('admin/languageManage.langedit_lang_editing');
		$d_arr['active_block'] = 'block_form_directory_list';
		$d_arr['language_list'] = $this->languages_service->getLanguagesList();
		// folders languae
		$d_arr['lang_folders_arr'] = array( 'root' => Lang::get('admin/languageManage.langedit_root'),
								   'admin' => Lang::get('admin/languageManage.langedit_admin'),
								   'auth' => Lang::get('admin/languageManage.langedit_auth'),
								   'myaccount' => Lang::get('admin/languageManage.langedit_myaccount')
						       );
		$modules_arr = CUtil::getModulesList();
		foreach($modules_arr as $value)	{
			if(CUtil::chkIsAllowedModule(strtolower($value))) {
		   		$d_arr['lang_folders_arr']['plugins~'.strtolower($value)] = Lang::get('admin/languageManage.langedit_'.strtolower($value));
		  	}
		}
		$languages_service = $this->languages_service;
		return View::make('admin.languagesFileEdit', compact('d_arr', 'languages_service'));
	}

	public function postFileEdit()
	{
		if(BasicCUtil::checkIsDemoSite()) {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
		else {
			$d_arr['pageTitle'] = Lang::get('admin/languageManage.langedit_lang_editing');
			$input = Input::All();
			$languages_service = $this->languages_service;
			$active_block = 'block_form_directory_list';

			if(isset($input['submit'])) {
				if(isset($input['language']) && isset($input['directory']) && isset($input['file']))
				{
					$active_block = 'block_form_edit_phrases';
				}
				if(isset($input['language']) && isset($input['directory']) && !isset($input['file']))
				{
					$active_block = 'block_form_files_list';
				}
			}
			if(isset($input['submit_back']))
			{
				if($input['submit_back'] == 'phrases')
				{
					$active_block = 'block_form_files_list';
				}
				elseif($input['submit_back']=='files')
				{
					$active_block = 'block_form_directory_list';
				}
			}

			if($active_block == 'block_form_directory_list') {
				$d_arr['active_block'] = 'block_form_directory_list';
				$d_arr['language_list'] = $this->languages_service->getLanguagesList();
				// folders languae
				$d_arr['lang_folders_arr'] = array( 'root' => Lang::get('admin/languageManage.langedit_root'),
										   'admin' => Lang::get('admin/languageManage.langedit_admin'),
										   'auth' => Lang::get('admin/languageManage.langedit_auth'),
										   'myaccount' => Lang::get('admin/languageManage.langedit_myaccount')
								       );
				$modules_arr = CUtil::getModulesList();
				foreach($modules_arr as $value)	{
					if(CUtil::chkIsAllowedModule(strtolower($value))) {
				   		$d_arr['lang_folders_arr']['plugins~'.strtolower($value)] = Lang::get('admin/languageManage.langedit_'.strtolower($value));
				  	}
				}
				return View::make('admin.languagesFileEdit', compact('d_arr', 'input', 'languages_service'));
			}
			else if($active_block == 'block_form_files_list') {

				$unwanted_files_list_arr = array();
				$d_arr['active_block'] = 'block_form_files_list';
				$lang_details = $this->languages_service->getLanguageSettingsByCode($input['language']);
				$d_arr['lang_name'] = isset($lang_details['name']) ? $lang_details['name'] : 'English';
				$d_arr['file_list'] = $this->languages_service->populateFileNamesList($input, '', $unwanted_files_list_arr);

				return View::make('admin.languagesFileEdit', compact('d_arr', 'input', 'languages_service'));
			}
			else if($active_block == 'block_form_edit_phrases') {
				if(isset($input['lang_phrases']) && !empty($input['lang_phrases']))
				{
					Session::flash('success_message', sprintf(Lang::get('admin/languageManage.langedit_msg_success'), $input['language']));
					$result = $this->languages_service->updateFileContent($input);
				}
				$d_arr['active_block'] = 'block_form_edit_phrases';
				$lang_orig = array();
				$file_path = $this->languages_service->getLangFilePath($input);
				if($file_path != '' && file_exists($file_path)) {
					$lang_orig = File::getRequire($file_path);
				}
				//To get lang file phrases in array
				foreach($lang_orig as $key => $value) {
					if(is_array($value)) {
						foreach($value as $key1 => $value1) {
							if(is_array($value1)) {
								foreach($value1 as $key2 => $val2) {
									$new_array[$key] = 'leveltwo';
									$new_array[$key.'~'.$key1.'~'.$key2] = $val2;
								}
							}
							else {
								$new_array[$key] = 'levelone';
								$new_array[$key.'~'.$key1] = $value1;
							}
						}
					}
					else {
						$new_array[$key] = $value;
					}
				}
				$d_arr['lang_orig'] = $new_array;
			    return View::make('admin.languagesFileEdit', compact('d_arr', 'input', 'languages_service'));
			}
		}
	}

	public function postLanguageDelete(){
		if(BasicCUtil::checkIsDemoSite()) {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::back()->withInput()->with('error_message',$errMsg);
		}
		else {
			$result = $this->languages_service->getDeleteLanguage(Input::all());
			if($result){
				$success_msg = Lang::get('admin/languageManage.managelanguage_lang_delete_succ_msg');
				return Redirect::to('admin/manage-language')->with('success_message', $success_msg);
			}else{
				$error_msg = Lang::get('admin/languageManage.managelanguage_invalid_id');
				return Redirect::to('admin/manage-language')->with('success_message', $error_msg);
			}
		}
	}
}