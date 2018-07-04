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
namespace App\Plugins\Importer\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products;
use Session, Redirect, BaseController;
class ImporterController extends \BaseController
{
	function __construct()
	{
		$shop = Products::initializeShops();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$this->beforeFilter(function(){
			if(!\CUtil::chkIsAllowedModule('importer'))
	    	{
				return Redirect::to('/');
			}
		});
		if(!$shop->checkIsShopNameExist($logged_user_id) || !$shop->checkIsShopPaypalUpdated($logged_user_id))
    	{
			return Redirect::to('shop/users/shop-details');
		}
		parent::__construct();
	}
	public function getIndex()
	{
		$shop = Products::initializeShops();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if(!$shop->checkIsShopNameExist($logged_user_id) || !$shop->checkIsShopPaypalUpdated($logged_user_id))
    	{
			return Redirect::to('shop/users/shop-details');
		}
		$importer_type = array('etsy' => Lang::get('importer::importer.etsy'), 'general' => Lang::get('importer::importer.general'));
		$user_id = BasicCUtil::getLoggedUserId();
		$service = new ImporterService();
		$imported_files = $service->getImportedFiles($user_id, 'paginate', 20);

		return View::make('importer::index', compact('importer_type', 'imported_files'));
	}
	public function postIndex(){ 
		$shop = Products::initializeShops();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		if(!$shop->checkIsShopNameExist($logged_user_id) || !$shop->checkIsShopPaypalUpdated($logged_user_id))
    	{
			return Redirect::to('shop/users/shop-details');
		}
		$inputs = Input::all();
		$rules = array('csv_file_input'	=> 'Required');
		$validator = Validator::make(array('csv_file_input' => Input::get('csv_file_input')), $rules);
		if($validator->fails()){
			return Redirect::back()->withInput()->withErrors($validator)->with('error_message', Lang::get('importer::importer.some_problem'));
		}else{
			if (Input::has('csv_file_input'))
				{ 
					$data = array();
					$data['file_from'] = $importer_type = $inputs['importer_type'];
					$data['file_name'] = str_replace(array('.csv', '.CSV'),'',Input::get('csv_file_input'));
					$file_name_new = Input::get('csv_file_input');
					$data['file_original_name'] = Input::get('file_original_name');
					$data['status'] = 'InActive';
					$data['user_id'] = $user_id = BasicCUtil::getLoggedUserId();
					$data['server_url'] = $destinationpath = URL::asset(Config::get("importer.importer_file_folder"));
					if(Input::has('image_file_input')){
						$data['zip_file_name'] = str_replace(array('.zip', '.ZIP'),'',Input::get('image_file_input'));
						$data['zip_org_name'] = Input::get('zip_original_name');
					}
					$importerCsvFile = new ImporterCsvFile();
					$id = $importerCsvFile->addNew($data);
					//$csv_file_id = $service->addCsvFile($data); 
					if($id)
					{ 
						$service = new ImporterService;
						$result = $service->insertCSVRecords($id, $file_name_new, $importer_type, $destinationpath); 
						if($result['result'] == 'success')
							return Redirect::back()->with('success_message', $result['message']);
						else
							return Redirect::back()->with('error_message', $result['message']);
					}
					else
						return Redirect::back()->with('error_message', Lang::get('importer::importer.some_problem'));	
			}
			else
			{
				return Redirect::back()->withInput()->withErrors($validator)->with('error_message', Lang::get('importer::importer.check_form_inputs'));
			}
		}
	}



	public function anyUploadActions()
	{ 
		$input = Input::all(); 
		$action = Input::get('action');
		switch($action)
		{ 	
			case 'upload_product_thumb_csv':
				$file = Input::file('uploadfile');
				$folder_path = Config::get("importer.importer_file_folder"); 
				$csv_size = Config::get('importer.max_csv_file_size') * 1024;
				$rules = array('uploadfile' => 'max:'.$csv_size);
				$validator = Validator::make($input,$rules);
				if($validator->passes()){				
					$file_ext = $file->getClientOriginalExtension(); 
					$file_name = Str::random(20);
					$original_name = $file->getClientOriginalName();
					$service = new ImporterService();
					$service->chkAndCreateFolder($folder_path);
					$success = $file->move($folder_path, $file_name.'.'.$file_ext);
					if($success){
						return json_encode(array('file_name_with_extension'=>$file_name.'.'.$file_ext, 'file_original_name' => $original_name));
					}else{
						return 0;
					}
				}else{
					return 'file_size_error';
				}
				break;
			case 'upload_product_thumb_image':
				$zip_file = Input::file('uploadfile');
				$folder_path = Config::get("importer.importer_file_folder"); 
				$zip_size = Config::get('importer.max_csv_file_size') * 1024;
				$rules = array('uploadfile' => 'max:'.$zip_size);
				$validator = Validator::make($input,$rules);
				if($validator->passes())
				{
					$zip_file_name = Str::random(20);
					$zip_ext = $zip_file->getClientOriginalExtension();
					$original_name = $zip_file->getClientOriginalName();
					$service = new ImporterService();
					$service->chkAndCreateFolder($folder_path);
					$success = $zip_file->move($folder_path,$zip_file_name.'.'.$zip_ext);
					if($success){
						return json_encode(array('file_name_with_extension'=>$zip_file_name.'.'.$zip_ext, 'file_original_name' => $original_name));
					}else{
						echo 0;
					}
				}else{
					return 'file_size_error';
				}
				break;
		}
	}
	
	/*public function anyUploadImage()
	{ 
		$input = Input::all(); 
		$action = Input::get('action');
		switch($action)
		{ 	
			case 'upload_product_thumb_image':
				$zip_file = Input::file('uploadfile');
				$folder_path = Config::get("importer.importer_file_folder"); 
				$zip_file_name = Str::random(20);
				$zip_ext = $zip_file->getClientOriginalExtension();
				$zip_file->move($folder_path,$zip_file_name.'.'.$zip_ext);
				if($success){
					echo $zip_file_name.'.'.$zip_ext;
				}else{
					echo 0;
				}
				break;
		}
	}*/





	public function getView($csv_file_id = null)
	{
		$service = new ImporterService();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$imported_file = $service->getImporterCSVDetails($csv_file_id);
		if(count($imported_file) > 0)
		{
			if(isset($imported_file->user_id) && $imported_file->user_id == $logged_user_id)
			{
				$importer_type = $imported_file->file_from;
				$file_records = $service->getImporterCSVRecordDetails($csv_file_id, $importer_type, 20);

				return View::make('importer::viewRecords', compact('imported_file', 'file_records', 'csv_file_id', 'service'));
			}
			else
			{
				return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.not_authorize'));
			}
		}
		else
		{
			return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.select_valid_csv_file'));
		}

		$imported_records = $service->getImporterCSVRecordDetails($csv_file_id);

	}
	public function getAction()
	{
		$inputs = Input::all();
		$action = (isset($inputs['action']) && $inputs['action']!='')?$inputs['action']:'';
		$csv_file_id = (isset($inputs['file_id']) && $inputs['file_id']!='')?$inputs['file_id']:0;

		$service = new ImporterService();
		if($action=='download_general_csv')
		{
			$service->downloadGeneralCSVFile();
		}
		else
		{

			$logged_user_id = BasicCUtil::getLoggedUserId();
			$imported_file = $service->getImporterCSVDetails($csv_file_id);
			if(count($imported_file) > 0)
			{
				if(isset($imported_file->user_id) && $imported_file->user_id == $logged_user_id)
				{
					switch($action){
						case 'download_csv':
							$service->downloadCSVResouceFile($csv_file_id);
							break;
					}
				}
				else
				{
					return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.not_authorize'));
				}
			}
			else
			{
				return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.select_valid_csv_file'));
			}
		}
	}
	public function postAction()
	{
		$inputs = Input::all();
		$file_id = (isset($inputs['file_id']) && $inputs['file_id']!='')?$inputs['file_id']:0;
		$file_action = (isset($inputs['file_action']) && $inputs['file_action']!='')?$inputs['file_action']:'';

		$service = new ImporterService();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$imported_file = $service->getImporterCSVDetails($file_id);
		if(count($imported_file) > 0)
		{
			if(isset($imported_file->user_id) && $imported_file->user_id == $logged_user_id)
			{
				switch($file_action)
				{
					case 'activate':
						$service->changeCSVFileStatus($file_id, $file_action, $imported_file->file_from);
						return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('success_message', Lang::get('importer::importer.file_status_updated_succes'));
						break;

					case 'delete':
						$service->deleteCSVFile($file_id);
						return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('success_message', Lang::get('importer::importer.file_deleted_success'));
						break;
				}
			}
			else
			{
				return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.not_authorize'));
			}
		}
		else
		{
			return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.select_valid_csv_file'));
		}
	}
	public function postRecordAction()
	{
		$inputs = Input::all();
		$file_id = (isset($inputs['file_id']) && $inputs['file_id']!='')?$inputs['file_id']:0;
		$record_id = (isset($inputs['record_id']) && $inputs['record_id']!='')?$inputs['record_id']:0;
		$record_action = (isset($inputs['record_action']) && $inputs['record_action']!='')?$inputs['record_action']:'';

		$service = new ImporterService();
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$imported_file = $service->getImporterCSVDetails($file_id);
		if(count($imported_file) > 0)
		{
			if(isset($imported_file->user_id) && $imported_file->user_id == $logged_user_id)
			{
				switch($record_action)
				{
					case 'activate':
						$service->changeCSVRecordStatus($record_id, $record_action, $imported_file->file_from);
						return Redirect::back()->with('success_message', Lang::get('importer::importer.record_status_updated_success'));
						break;

					case 'deactivate':
						$service->changeCSVRecordStatus($record_id, $record_action, $imported_file->file_from);
						return Redirect::back()->with('success_message', Lang::get('importer::importer.record_status_updated_success'));
						break;
				}
			}
			else
			{
				return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.not_authorize'));
			}
		}
		else
		{
			return Redirect::action('App\Plugins\Importer\Controllers\ImporterController@getIndex')->with('error_message', Lang::get('importer::importer.select_valid_csv_file'));
		}
	}
	public function getCategoryListing()
	{
		//get categories list
		$categorylist = Products::getCategoriesList();
		$category_arr = array();
		if(count($categorylist) > 0)
		{
			$categorylist = $categorylist->toArray();
			//echo "<pre>";print_r($categorylist);echo "</pre>";
			$inc=0;
			foreach($categorylist as $category)
			{
				$category['category_id_lbl'] = 	$category['id'];
				if($category['category_level'] == 0)
				{
					$category['category_name'] 	= 'Categories';
					$category['category_id_lbl'] = 	'-';
				}
				$category_arr[$inc] = $category;
				$inc++;
			}
		}
		//Used below code to form the tree structure. but omitted now
		//$arr = $category_arr;
		//$new = array();
		//foreach ($arr as $a){
		//    $new[$a['parent_category_id']][] = $a;
		//}
		//$service = new ImporterService();
		//$service->createTree($new, $new[0]);
		$category_list = $category_arr;

		return View::make('importer::categoryListing', compact('category_list', 'service'));

	}

}

?>