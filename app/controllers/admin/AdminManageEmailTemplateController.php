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
//@added by mohamed_158at11
class AdminManageEmailTemplateController extends BaseController
{
	function __construct()
	{
        $this->languages_service = new AdminManageLanguageService();
        parent::__construct();
    }

	public function getIndex()
	{

		$adminManageLanguageService = new AdminManageLanguageService();
		$languages_list = $adminManageLanguageService->getLanguagesListHasFolder();


		$current_language = (Input::has('current_language') && Input::get('current_language')!='')?Input::get('current_language'):Config::get('generalConfig.lang');
		$error ='';

		$file_path =  app_path().'/lang/'.$current_language.'/emaiTemplates.php';
		$email_templates = array();
		if(file_exists($file_path))
			$email_templates = File::getRequire(base_path().'/app/lang/'.$current_language.'/emaiTemplates.php');
		else
		{
			if(!empty($languages_list))
			{
				reset($languages_list);
				$current_language = key($languages_list);

				$file_path =  app_path().'/lang/'.$current_language.'/emaiTemplates.php';
				$email_templates = array();
				if(file_exists($file_path))
					$email_templates = File::getRequire(base_path().'/app/lang/'.$current_language.'/emaiTemplates.php');
				else
				{
					$error = trans('admin/manageEmailTemplate.language_file_not_found');
				}
			}
			else
			{
				$error = trans('admin/manageEmailTemplate.language_file_not_found');
			}
		}


		$d_arr['pageTitle'] = trans('admin/manageEmailTemplate.edit_email_template');
		$d_arr['actionicon'] ='<i class="fa fa-language"><sup class="fa fa-pencil font11"></sup></i>';
		$d_arr['languag_phrases'] = $email_templates;

		return View::make('admin.manageEmailTemplates', compact('languages_list', 'd_arr', 'current_language', 'error'));
	}

	public function postIndex()
	{
		$inputs = Input::all();
		$current_language = (Input::has('current_language') && Input::get('current_language'))?Input::get('current_language'):Config::get('generalConfig.lang');
		$error_found = false;
		if(isset($inputs['languag_phrases']) && !empty($inputs['languag_phrases']))
		{
			foreach ($inputs['languag_phrases'] as $key => $value)
			{
				if(is_array($value))
				{
					foreach($value as $key1 => $value1)
					{
						if(trim($value1)=='')
						{
							$error_found = true;
							break;
						}
					}
				}
				else
				{
					if(trim($value)=='')
					{
						$error_found = true;
						break;
					}
				}
			}
		}
		if($error_found)
			return Redirect::to(Url::action('AdminManageEmailTemplateController@getIndex').'?current_language='.$current_language)->withInput()->with('error_message', trans('admin/manageEmailTemplate.email_content_subject_cant_empty'));

		//if no error found then just add it to the language phrases
		$output = var_export($inputs['languag_phrases'],true);
		$output = '<?php return '.$output.';';

		file_put_contents(app_path().'/lang/'.$current_language.'/emaiTemplates.php', $output);

		return Redirect::to(Url::action('AdminManageEmailTemplateController@getIndex').'?current_language='.$current_language)->with('success_message', trans('admin/manageEmailTemplate.template_updated_successfully'));
	}
}