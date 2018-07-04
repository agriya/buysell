<?php
namespace App\Plugins\Importer\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products;
class ImporterService
{
	public function chkAndCreateFolder($folderName)
	{
		$folder_arr = explode('/', $folderName);
		$folderName = '';
		foreach($folder_arr as $key=>$value)
			{
				$folderName .= $value.'/';
				if($value == '..' or $value == '.')
					continue;
				if (!is_dir($folderName))
					{
						mkdir($folderName);
						@chmod($folderName, 0777);
					}
			}
	}
	public function addCsvFile($data = array())
	{ 
		if(!is_array($data) || empty($data))
			return false;

		$importerCsvFile = new ImporterCsvFile();
		$id = $importerCsvFile->addNew($data); 
		return $id;
	}
	public function getImportedFiles($user_id, $return_type='paginate', $limit = 20)
	{
		$imported_files = ImporterCsvFile::where('user_id',$user_id)->orderby('id','desc');
		if($return_type == 'paginate')
			$imported_files = $imported_files->paginate($limit);
		else
			$imported_files = $imported_files->get();
		return $imported_files;
	}
	public function updateCSVFile($file_id, $data){
		ImporterCsvFile::where('id', $file_id)->update($data);
	}
	public function updateGeneralCSVRecord($record_id, $data){
		//echo "<br>record_id: ".$record_id;
		//echo "<pre>";print_r($data);echo "</pre>";exit;
		ImporterGeneralProductDetails::where('id', $record_id)->update($data);
	}
	public function updateEtsyCSVRecord($record_id, $data){
		//echo "<br>record_id: ".$record_id;
		//echo "<pre>";print_r($data);echo "</pre>";exit;
		ImporterEtsyProductDetails::where('id', $record_id)->update($data);
	}

	public function validateUploadedCSVFile($file_name, $importer_type, $destinationpath = null)
	{
		if(is_null($destinationpath) || $destinationpath == '')
			$destinationpath = Config::get("importer.importer_file_folder");
		$result_arr = array('result' => 'success', 'message' => 'CSV Imported successfully');
		$file_path = $destinationpath.'/'.$file_name.'.csv';
		//echo "<br>file_path: ".$file_path;exit;
		$file = fopen($file_path, 'r');
		$row = 1;
		$rec_cnt = 0;
		$rules = $this->getValidationRules($importer_type);
		$succes_records=array();
		$error_records=array();
		while (($line = fgetcsv($file,"10000", ',')) !== FALSE)//$this->csv_delimiter
		{
			if ($row == 1)//AND $this->ignore_first_row
			{
				$row ++;
				continue; //skip the first row if it contains the header
			}
			$no_of_columns = count($line);

			$this->setTableFormFields($importer_type);
			$available_count = count($this->mass_upload_fields_array);

			$data = array();
			if($no_of_columns == $available_count)
			{
				for ($col = 0; $col < $no_of_columns; $col ++)
				{
					$data[$this->mass_upload_fields_array[$col]] = $line[$col];
				}
				$validator = Validator::make($data, $rules);
				if($validator->passes())
				{
					$rec_cnt+=1;
					$succes_records[]= $data;
				}
				else
				{
					$data['error_message'] = json_encode($validator->errors());
					$error_records[]= $data;
				}
			}
			else
			{
				$data['error_message'] = Lang::get('importer::importer.insufficient_records');
				$error_records[]= $data;
			}
		}

		if(!empty($succes_records))
			return true;
		else
			return false;
	}
	public function deleteUploadedCSVFile($file_name = '', $zip_file_name = '', $destinationpath = null)
	{
		if(is_null($destinationpath) || $destinationpath == '')
			$destinationpath = Config::get("importer.importer_file_folder");

		if (file_exists($destinationpath.'/'.$file_name.'.csv'))
		{
			unlink($destinationpath.'/'.$file_name.'.csv');
		}
		if (file_exists($destinationpath.'/'.$zip_file_name.'.zip'))
		{
			unlink($destinationpath.'/'.$zip_file_name.'.zip');
		}
	}
	public function insertCSVRecords($file_id, $file_name, $importer_type, $destinationpath = null)
	{ 
		if(is_null($destinationpath) || $destinationpath == '')
			$destinationpath = Config::get("importer.importer_file_folder");
		$result_arr = array('result' => 'success', 'message' => 'CSV Imported successfully');
		$file_path = $destinationpath.'/'.$file_name;
		//echo "<br>file_path: ".$file_path;
		$file = fopen($file_path, 'r');
		$row = 1;
		$rec_cnt = 0;
		$rules = $this->getValidationRules($importer_type);
		$succes_records=array();
		$error_records=array();
		while (($line = fgetcsv($file,"10000", ',')) !== FALSE)//$this->csv_delimiter
		{
			if ($row == 1)//AND $this->ignore_first_row
			{
				$row ++;
				continue; //skip the first row if it contains the header
			}
			$no_of_columns = count($line);

			$this->setTableFormFields($importer_type);
			$available_count = count($this->mass_upload_fields_array);
			$data = array();
			if($no_of_columns == $available_count)
			{
				for ($col = 0; $col < $no_of_columns; $col ++)
				{
					$data[$this->mass_upload_fields_array[$col]] = $line[$col];
				}
				$validator = Validator::make($data, $rules);
				if($validator->passes())
				{
					$rec_cnt+=1;
					$data['csv_file_id'] = $file_id;
					$data['created_at'] = DB::Raw('Now()');
					$data['updated_at'] = DB::Raw('Now()');
					if($importer_type == 'general')
					{
						$data['is_downloadable'] = (isset($data['is_downloadable']) && ($data['is_downloadable'] == 0 || strtolower($data['is_downloadable']) == 'no'))?'No':'Yes';
						$data['image_attached'] = (isset($data['image_attached']) && ($data['image_attached'] == 0 || strtolower($data['image_attached']) == 'no'))?'No':'Yes';
					}
					$succes_records[] = $data;
				}
				else
				{
					$data['error_message'] = json_encode($validator->errors());
					$error_records[] = $data;
				}
			}
			else
			{
				$data['error_message'] = Lang::get('importer::importer.insufficient_records');
				$error_records[]= $data;
			}
		}

		if(!empty($succes_records))
		{
			$this->insertCSVValues($importer_type, $succes_records);
			$this->updateCSVFile($file_id, array('item_count' => $rec_cnt, 'parsed_item_count' => 0));
		}
		if($rec_cnt > 0)
		{
			if(!empty($error_records)){
				$result_arr['message'] = Lang::get('importer::importer.partially_imported');
			}
			else{
				$result_arr['message'] = Lang::get('importer::importer.all_records_imported_successfully');
			}
		}
		else
		{
			$result_arr['result'] = 'failure';
			$result_arr['message'] = Lang::get('importer::importer.no_records_inserted');
		}
		return $result_arr;

	}
	public function setTableFormFields($importer_type = 'general'){
		switch($importer_type){
			case 'etsy':
				$this->mass_upload_fields_array = array(	'title',
													'description',
													'price',
													'currency_code',
													'quantity',
													'tags',
													'materials',
													'image1_path',
													'image2_path',
													'image3_path',
													'image4_path',
													'image5_path'		);
				break;
			case 'artfire':
				// @todo -needs to add artfire related
				break;

			case 'general':
				$this->mass_upload_fields_array = array(	'title',
													'url_slug',
													'description',
													'summary',
													'price',
													'category_id',
													'is_downloadable',
													'tags',
													'demo_url',
													'stock_available',
													'shipping_template',
													'image_attached',
													'thumb_image',
													'default_image',
													//'preview_type',
													'preview_image1',
													'preview_image2',
													'preview_image3',
													'preview_image4',
													'preview_image5');
				break;
		}
	}

	public function getValidationRules($importer_type = 'general'){
		switch($importer_type){
			case 'etsy':
				$rules = array('title' => 'required', 'price' => 'required|min:0', 'quantity' => 'required', 'tags' => 'required');
				break;
			case 'artfire':
				// @todo -needs to add artfire related
				break;

			case 'general':
				$rules = array('title' => 'required',
								'url_slug' => 'required',
								'price' => 'required|min:0',
								'category_id' => 'required',
								'tags' => 'required',
								'stock_available' => 'required|min:1');

				break;
		}
		return $rules;
	}

	public function insertCSVValues($importer_type, $data)
	{
		if($importer_type=='general')
			ImporterGeneralProductDetails::insert($data);
		else
			ImporterEtsyProductDetails::insert($data);
	}

	public function downloadCSVResouceFile($csv_file_id = 0)
	{
		$allowed_download = false;
		$csv_file = ImporterCsvFile::find($csv_file_id);

		if(count($csv_file) > 0)
		{
			$logged_user_id = BasicCUtil::getLoggedUserId();
			//check if the logged in user has access
			if($csv_file->file_name !='' && $csv_file->user_id == $logged_user_id)
			{
				$allowed_download = true;
				$filename = $csv_file->file_name . '.csv';
				//$media_type = (strtolower($q[0]->resource_type) == 'archive') ? 'zip' : strtolower($q[0]->resource_type);
				$path = Config::get("importer.importer_file_folder");
				$save_filename = $csv_file->file_original_name;


				$pathToFile = public_path().'/'.$path.'/'.$filename;
				//echo "<br>pathToFile: ".$pathToFile;exit;

				if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header("Content-Transfer-Encoding: binary");
					header('Pragma: public');
					header("Content-Length: ".filesize($pathToFile));
				}
				else
				{
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$save_filename.'"');
					header("Content-Transfer-Encoding: binary");
					header('Expires: 0');
					header('Pragma: no-cache');
					header("Content-Length: ".filesize($pathToFile));
				}

				ob_clean();
				flush();
				@readfile($pathToFile);
			}
		}
		if(!$allowed_download)
			die('Error: Unable to get the file!');
	}
	public function downloadGeneralCSVFile($type='general'){
		$file_path = app_path().'/plugins/importer/files/';
		if($type=='etsy')
			$filename = 'sample_etsy_csv.csv';
		else
			$filename = 'sample_general_csv.csv';

		$save_filename = $filename;
		$pathToFile = $file_path.$filename;
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$save_filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".filesize($pathToFile));
		}
		else
		{
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$save_filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".filesize($pathToFile));
		}

		ob_clean();
		flush();
		@readfile($pathToFile);

	}

	public function changeCSVFileStatus($file_id, $action, $importer_type)
	{
		if($action == 'activate' || $action == 'deactivate' )
		{
			$action = ($action == 'activate')?'Active':'InActive';
			ImporterCsvFile::where('id', $file_id)->update(array('status' => $action));

			if($importer_type == 'general')
			{
				ImporterGeneralProductDetails::where('csv_file_id', $file_id)->update(array('status' => $action));
			}
			else
			{
				ImporterEtsyProductDetails::where('csv_file_id', $file_id)->update(array('status' => $action));
			}
		}
	}

	public function changeCSVRecordStatus($record_id, $action, $importer_type)
	{
		if($action == 'activate' || $action == 'deactivate' )
		{
			$action = ($action == 'activate')?'Active':'InActive';
			if($importer_type == 'general')
				ImporterGeneralProductDetails::where('id', $record_id)->update(array('status' => $action));
			else
				ImporterEtsyProductDetails::where('id', $record_id)->update(array('status' => $action));
		}
	}

	public function deleteCSVFile($file_id){
		$imported_file = $this->getImporterCSVDetails($file_id);
		if(count($imported_file) > 0)
		{
			$file_name = (isset($imported_file->file_name) && $imported_file->file_name != '')?$imported_file->file_name:'';
			$zip_file_name = (isset($imported_file->zip_file_name) && $imported_file->zip_file_name != '')?$imported_file->zip_file_name:'';
			ImporterGeneralProductDetails::where('csv_file_id',$file_id)->delete();
			ImporterEtsyProductDetails::where('csv_file_id',$file_id)->delete();
			ImporterCsvFile::where('id', $file_id)->delete();
			$this->deleteUploadedCSVFile($file_name, $zip_file_name);
		}
	}
	public function getImporterCSVDetails($file_id)
	{
		$file_details = ImporterCsvFile::find($file_id);
		return $file_details;
	}
	public function getImporterCSVRecordDetails($file_id = null, $importer_type = 'general', $limit = 20)
	{
		if(is_null($file_id) || $file_id == '')
			return false;
		if($importer_type == 'general')
			$importercsvfilerecord = ImporterGeneralProductDetails::where('csv_file_id',$file_id)->paginate($limit);
		else
			$importercsvfilerecord = ImporterEtsyProductDetails::where('csv_file_id',$file_id)->paginate($limit);

		return $importercsvfilerecord;

	}
	public function getRecordPreview($imported_files, $record)
	{
		$importer_type = $imported_files->file_from;
		//$record = $record->id;
		if($importer_type == 'etsy')
		{
			$record->category = Products::getCategoryName($record->category_id);
			$images_arr = array();
			for($i=1;$i<=5;$i++)
			{
				$name = 'image'.$i.'_path';
				if(isset($record->$name) && $record->$name !='')
					$images_arr[] = $record->$name;
			}

			return View::make('importer::etsyRecordDetails', compact('imported_files', 'record'));
		}
		else
		{
			$record->category = Products::getCategoryName($record->category_id);
			$images_arr = array();
			for($i=1;$i<=5;$i++)
			{
				$name = 'preview_image'.$i;
				if(isset($record->$name) && $record->$name !='')
					$images_arr[] = $record->$name;
			}
			$record->images_arr = $images_arr;
			return View::make('importer::generalRecordDetails', compact('imported_files', 'record'));
		}
	}
	function createTree(&$list, $parent)
	{
	    $tree = array();
	    foreach ($parent as $k=>$l){
	        if(isset($list[$l['id']])){
	            $l['children'] = $this->createTree($list, $list[$l['id']]);
	        }
	        $tree[] = $l;
	    }
	    return $tree;
	}
	public function renderTree($items)
	{
		$render = '<ul>';
		foreach ($items as $item) {
			$render .= '<li><span><i class="icon-minus-sign"></i> '.$item['category_name'].'</span>';
			if (isset($item['children']) && !empty($item['children'])) {
			    $render .= $this->renderTree($item['children']);
			}
			$render .= '</li>';
		}
		return $render . '</ul>';
	}
}