<?php
namespace App\Plugins\Importer\Controllers;
use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products, ZipArchive, Image;
class ImporterCronService extends ImporterService
{
	public function getPendingImporterFiles($limit = 100){

		$pedingCSVFiles = ImporterCsvFile::whereIn('status',array('Active', 'Progress'))->take($limit)->orderby('id','asc')->get();
		return $pedingCSVFiles;
	}
	public function getImporterCSVRecordDetails($file_id = null, $importer_type = 'general', $limit = 100)
	{
		if(is_null($file_id) || $file_id == '')
			return false;

		if($importer_type == 'general')
			$importercsvfilerecord = ImporterGeneralProductDetails::where('csv_file_id',$file_id)->where('status','Active')->take($limit)->get();
		else
		{
			$importercsvfilerecord = ImporterEtsyProductDetails::where('csv_file_id',$file_id)->where('status','Active')->take($limit)->get();
		}
		return $importercsvfilerecord;

	}
	public function getValidCategoryId($category_id = null)
	{
		$valid = Products::checkIsValidCategoryId($category_id);
		if($valid)
			return $category_id;
		else
		{
			$root_category_id = Products::getRootCategoryId();
			return $root_category_id;
			/*if(count($top_cat) > 0)
				return $top_cat->id;
			else
				return 2;*/
		}
	}
	public function fetchEtsyProductsById($file)
	{

		$records = $this->getImporterCSVRecordDetails($file->id, $file->file_from, 200);
		if(count($records) > 0)
		{
			$parsed_cnt = 0;
			$this->updateCSVFile($file->id, array('status' => 'Progress'));
			foreach($records as $record)
			{
			//	echo "<pre>";print_r($record);echo "</pre>";
				$product = Products::initialize();
				$product->setProductUserId($file->user_id);
				$product->setTitle($record->title);
				$product->setUrlSlug(strtolower($record->url_slug));
				$product->setDescription($record->description);

				$category_id = $this->getValidCategoryId(0);
				$product->setCategory($category_id);

				$product->setProductTags($record->tags);
				$product->setIsDownloadableProduct('No');
				$details = $product->save('yes');

				$product_id = $this->checkProductUpdated($details);
				if($product_id)
				{
					//echo "<br>product_id: ".$product_id;exit;
					$parsed_cnt++;
					//update status as completed for the record
					$this->updateEtsyCSVRecord($record->id, array('product_id' => $product_id, 'status' => 'Completed'));

					//update the price
					$product = Products::initialize($product_id);
					$price = $record->price;
					if($price <= 0)
					{
						$product->setIsFreeProduct('Yes');
						$product->deleteGroupPriceDetails($product_id);
					}
					else
					{

						$product->setPurchasePrice($price);
						$details = $product->save('yes');

						$updated = $this->checkProductUpdated($details);
						$product_price_groups = array();
						$data['id'] = 0;
						$product_price_groups[] = $data;
						$product_data['range_start'] = 1;
						$product_data['range_end'] = -1;
						$product_data['price'] = $price;
						$product_data['discount_percentage'] = 0;
						$product_data['discount'] = $price;
						$product_data['error'] = '';
						$product_price_groups[0]['price_details'][] = $product_data;

						$product->updateGroupPriceDetailsById($product_id, $product_price_groups);

					}

					//update stocks
					$data_arr['product_id'] = $product_id;
					$data_arr['quantity'] = $record->quantity;
					$data_arr['serial_numbers'] = '';
					$response = $product->saveStocks($data_arr);




					//fetch image from the url and save it in the folder
					$max_allowed = Config::get('webshoppack.preview_max');
					for($i=1; $i<=$max_allowed; $i++)
					{
						$name = 'image'.$i.'_path';
						if(isset($record->$name) && $record->$name!='')
						{

							$image_name = $record->$name;
							$default_status = $this->uploadMediaFileFromUrl($image_name, 'image', $file_info);
							if($default_status['status'] == 'success')
							{
								$resource_arr = array(
									'product_id'=>$product_id,
									'resource_type'=>'Image', // hard coded
									'filename'=>$file_info['filename_no_ext'],
									'ext'=>$file_info['ext'],
									'title'=>$file_info['title'],
									'width'=>$file_info['width'],
									'height'=>$file_info['height'],
									's_width'=>$file_info['s_width'],
									's_height'=>$file_info['s_height'],
									't_width'=>$file_info['t_width'],
									't_height'=>$file_info['t_height'],
									'l_width'=>$file_info['l_width'],
									'l_height'=>$file_info['l_height'],
									'server_url'=>$file_info['server_url'],
									'is_downloadable'=>$file_info['is_downloadable']
							 	);
								if($i==1)
								{
									$product->removeItemImageFile($product_id, 'thumb');
									$product->updateProductThumbImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
								}
								elseif($i==2)
								{
									$product->removeItemImageFile($product_id, 'default');
									$product->updateProductDefaultImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
								}
								else
									$resource_id = $product->insertPreviewFiles($file_info['filename_no_ext'], $file_info['ext'], $file_info['title'], $file_info['server_url'], $file_info['width'], $file_info['height'], $file_info['l_width'], $file_info['l_height'], $file_info['t_width'], $file_info['t_height'], $file_info['s_width'], $file_info['s_height']);
							}
						}

					}
				}
				else
				{
					$error_msg = '';
					$json_data = json_decode($details, true);
					if($json_data['status'] == 'error')
					{
						$error_msg = $json_data['error_messages'];
						/*foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}*/
					}
					//Update error reason in csv record
					$this->updateEtsyCSVRecord($record->id, array('error_reasons' => serialize($error_msg), 'status' => 'Failed'));

					//there are any error in creating product loop to the next record
					continue;
				}
			}
			$parsed_item_count = $file->parsed_item_count+$parsed_cnt;
			$this->updateCSVFile($file->id, array('parsed_item_count' => $parsed_item_count));
		}
		else
		{
			$this->updateCSVFile($file->id, array('status' => 'Completed'));
		}
	}
	public function fetchGeneralProductsById($file)
	{ 
		if($file->status == 'Active')
		{
			if(isset($file->zip_file_name) && $file->zip_file_name!='')
				$this->doAttachmentExtract($file);
		}
		//get the records
		//echo "<pre>";print_r($file);echo "</pre>";
		$records = $this->getImporterCSVRecordDetails($file->id, $file->file_from, 200);
		if(count($records) > 0)
		{
			$parsed_cnt = 0;
			$this->updateCSVFile($file->id, array('status' => 'Progress'));
			foreach($records as $record)
			{
			//	echo "<pre>";print_r($record);echo "</pre>";
				$product = Products::initialize();
				$product->setProductUserId($file->user_id);
				$product->setTitle($record->title);
				$product->setUrlSlug(strtolower($record->url_slug));
				$product->setDescription($record->description);
				$product->setSummary($record->summary);

				$category_id = $this->getValidCategoryId($record->category_id);

				$product->setCategory($category_id);
				$product->setDemoUrl($record->demo_url);
				$product->setProductTags($record->tags);
				$product->setIsDownloadableProduct($record->is_downloadable);
				$details = $product->save('yes');

				$product_id = $this->checkProductUpdated($details);
				if($product_id)
				{
					$parsed_cnt++;
					//update status as completed for the record
					$this->updateGeneralCSVRecord($record->id, array('product_id' => $product_id, 'status' => 'Completed'));

					//update the price
					$product = Products::initialize($product_id);
					$price = $record->price;
					if($price <= 0)
					{
						$product->setIsFreeProduct('Yes');
						$product->deleteGroupPriceDetails($product_id);
					}
					else
					{

						$product->setPurchasePrice($price);
						$details = $product->save('yes');

						$updated = $this->checkProductUpdated($details);
						$product_price_groups = array();
						$data['id'] = 0;
						$product_price_groups[] = $data;
						$product_data['range_start'] = 1;
						$product_data['range_end'] = -1;
						$product_data['price'] = $price;
						$product_data['discount_percentage'] = 0;
						$product_data['discount'] = $price;
						$product_data['error'] = '';
						$product_price_groups[0]['price_details'][] = $product_data;

						$product->updateGroupPriceDetailsById($product_id, $product_price_groups);

					}

					//update stocks
					$data_arr['product_id'] = $product_id;
					$data_arr['quantity'] = $record->stock_available;
					$data_arr['serial_numbers'] = '';
					$response = $product->saveStocks($data_arr);

					//update shipping template
					if($record->is_downloadable != 'No')
					{
						$product->setShippingTemplate(0);
						$product->removePackageDetails($product_id);
					}
					else
					{
						//find shipping template it
						$template_det = $product->findShippingTemplateIdFromName($record->shipping_template);
						if(count($template_det) > 0)
						{
							$template_id = $template_det->id;
							$package_details = array();

							//$id = $inputs['id'];
							$package_details['id'] = $product_id;
							$package_details['weight'] = 1.50;
							$package_details['length'] = 25;
							$package_details['width'] = 15;
							$package_details['height'] = 10;
							//echo "<pre>";print_r($package_details);echo "</pre>";
							$package_details = $product->addPackageDetails($package_details);

							$product->setShippingTemplate($template_id);
							$details = $product->save('yes');
						}
					}


					//insert the images
					if(isset($record->image_attached) && $record->image_attached == 'Yes')
					{
						$target_folder_path = Config::get("importer.temp_extrac_file_folder");
						$targetURL = public_path().'/'.$target_folder_path.'/'.'general_csv_'.$file->id.'/';
						//thumb image
						if(isset($record->thumb_image) && $record->thumb_image!='')
						{
							$thumb_status = $this->uploadMediaFile($targetURL.$record->thumb_image, 'image', $file_info);
							if($thumb_status['status'] == 'success')
							{
								$product->removeItemImageFile($product_id, 'thumb');
								$product->updateProductThumbImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
							}
						}

						//default image
						if(isset($record->default_image) && $record->default_image!='')
						{
							$default_status = $this->uploadMediaFile($targetURL.$record->default_image, 'image', $file_info);
							if($default_status['status'] == 'success')
							{
								$product->removeItemImageFile($product_id, 'default');
								$product->updateProductDefaultImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
							}
						}

						$max_allowed = Config::get('webshoppack.preview_max');
						for($i=1; $i<=$max_allowed; $i++)
						{
							$name = 'preview_image'.$i;
							if(isset($record->$name) && $record->$name!='')
							{
								$image_name = $record->$name;
								$default_status = $this->uploadMediaFile($targetURL.$image_name, 'image', $file_info);
								if($default_status['status'] == 'success')
								{
									$resource_arr = array(
										'product_id'=>$product_id,
										'resource_type'=>'Image', // hard coded
										'filename'=>$file_info['filename_no_ext'],
										'ext'=>$file_info['ext'],
										'title'=>$file_info['title'],
										'width'=>$file_info['width'],
										'height'=>$file_info['height'],
										's_width'=>$file_info['s_width'],
										's_height'=>$file_info['s_height'],
										't_width'=>$file_info['t_width'],
										't_height'=>$file_info['t_height'],
										'l_width'=>$file_info['l_width'],
										'l_height'=>$file_info['l_height'],
										'server_url'=>$file_info['server_url'],
										'is_downloadable'=>$file_info['is_downloadable']
								 	);

									$resource_id = $product->insertPreviewFiles($file_info['filename_no_ext'], $file_info['ext'], $file_info['title'], $file_info['server_url'], $file_info['width'], $file_info['height'], $file_info['l_width'], $file_info['l_height'], $file_info['t_width'], $file_info['t_height'], $file_info['s_width'], $file_info['s_height']);
								}
							}
						}
					}
					else
					{
						//thumb image
						if(isset($record->thumb_image) && $record->thumb_image!='')
						{
							$thumb_status = $this->uploadMediaFileFromUrl($record->thumb_image, 'image', $file_info);
							if($thumb_status['status'] == 'success')
							{
								$product->removeItemImageFile($product_id, 'thumb');
								$product->updateProductThumbImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
							}
						}

						//default image
						if(isset($record->default_image) && $record->default_image!='')
						{
							$default_status = $this->uploadMediaFileFromUrl($record->default_image, 'image', $file_info);
							if($default_status['status'] == 'success')
							{
								$product->removeItemImageFile($product_id, 'default');
								$product->updateProductDefaultImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
							}
						}

						//fetch image from the url and save it in the folder
						$max_allowed = Config::get('webshoppack.preview_max');
						for($i=1; $i<=$max_allowed; $i++)
						{
							$name = 'preview_image'.$i;
							if(isset($record->$name) && $record->$name!='')
							{

								$image_name = $record->$name;
								$default_status = $this->uploadMediaFileFromUrl($image_name, 'image', $file_info);
								if($default_status['status'] == 'success')
								{
									$resource_arr = array(
										'product_id'=>$product_id,
										'resource_type'=>'Image', // hard coded
										'filename'=>$file_info['filename_no_ext'],
										'ext'=>$file_info['ext'],
										'title'=>$file_info['title'],
										'width'=>$file_info['width'],
										'height'=>$file_info['height'],
										's_width'=>$file_info['s_width'],
										's_height'=>$file_info['s_height'],
										't_width'=>$file_info['t_width'],
										't_height'=>$file_info['t_height'],
										'l_width'=>$file_info['l_width'],
										'l_height'=>$file_info['l_height'],
										'server_url'=>$file_info['server_url'],
										'is_downloadable'=>$file_info['is_downloadable']
								 	);

									$resource_id = $product->insertPreviewFiles($file_info['filename_no_ext'], $file_info['ext'], $file_info['title'], $file_info['server_url'], $file_info['width'], $file_info['height'], $file_info['l_width'], $file_info['l_height'], $file_info['t_width'], $file_info['t_height'], $file_info['s_width'], $file_info['s_height']);
								}
							}

						}

					}
				}
				else
				{
					$error_msg = '';
					$json_data = json_decode($details, true);
					if($json_data['status'] == 'error')
					{
						$error_msg = $json_data['error_messages'];
						/*foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}*/
					}
					//Update error reason in csv record
					$this->updateGeneralCSVRecord($record->id, array('error_reasons' => serialize($error_msg), 'status' => 'Failed'));

					//there are any error in creating product loop to the next record
					continue;
				}
			}
			//update the parsed item count
			$parsed_item_count = $file->parsed_item_count+$parsed_cnt;
			$this->updateCSVFile($file->id, array('parsed_item_count' => $parsed_item_count));
		}
		else
		{
			//if no records found then update as completed
			$this->updateCSVFile($file->id, array('status' => 'Completed'));
		}
	}
	public function checkProductUpdated($details){
		$json_data = json_decode($details , true);
		if($json_data['status'] == 'error')
			return false;
		else
		{
			$product_id = $json_data['product_id'];
			return $product_id;
		}
	}
	public function doAttachmentExtract($file)
	{ 
		if(isset($file->zip_file_name) && $file->zip_file_name!='')
		{
			$source = $file->zip_file_name.'.zip';
			$this->openZip($source, $file);
		}
	}
	public function openZip($source_file, $file)
	{
		$file_path = Config::get("importer.importer_file_folder");
		$target_folder_path = Config::get("importer.temp_extrac_file_folder");
		$sourceURL = $file_path.'/'.$source_file;
		$targetURL = $target_folder_path.'/'.'general_csv_'.$file->id;

		$zip = new ZipArchive();
		if (file_exists($sourceURL))
			chmod($sourceURL, 0777);
		$x = $zip->open($sourceURL); 
		if ($x === true)
		{
			if(is_file($targetURL))
				chmod($targetURL, 0777);
			$zip->extractTo($targetURL);
			$src1 = $targetURL;
			$dst1 = $targetURL; 
			$this->recurse_copy($src1,$src1,$dst1);
			$zip->close();
		}
//		else
//		{
//			echo "There was a problem. Please try again!";
//			//die("There was a problem. Please try again!");
//		}
	}
	
	public function recurse_copy($parent ,$src, $dst) {
		$dir = opendir($src); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					$this->recurse_copy($parent, $src . '/' . $file, $dst . '/' . $file); 
					rmdir($dst . '/' . $file);
				} 
				else { 
					if($file != 'Thumbs.db')
						copy($src.'/'.$file,$parent.'/'.$file);
					if($parent != $src)
						unlink($src.'/'.$file);
				} 
			} 
		} 
		closedir($dir); 
	}
	
	public function uploadMediaFile($image_src, $file_type,  &$file_info, $download_file = false)
	{

		// default settings
		$title = 'Image';
		$file_original = '';
		$file_thumb = '';
		$file_large ='';
		$width = 0;
		$height = 0;
		$t_width = 0;
		$t_height = 0;
		$l_width = 0;
		$l_height = 0;
		$server_url = '';
		$is_downloadable = 'No';

		$filename_no_ext = uniqid(); // generate filename
		$ext = pathinfo($image_src, PATHINFO_EXTENSION);



		switch($file_type) {
			case 'image':
				$file_path = Config::get("webshoppack.photos_folder");
				$server_url = URL::asset($file_path);
				$file_original  = $filename_no_ext . '.' . $ext;
				$file_thumb = $filename_no_ext . 'T.' . $ext;
				$file_large = $filename_no_ext . 'L.' . $ext;
				$file_small = $filename_no_ext . 'S.' . $ext;

				$this->chkAndCreateFolder($file_path);

				@chmod($file_original, 0777);
				@chmod($file_thumb, 0777);
				@chmod($file_large, 0777);
				@chmod($file_small, 0777);

				try{

					Image::make($image_src)->save($file_path.$file_original);

					//Resize original image for large image
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_large_width"), Config::get("webshoppack.photos_large_height"), true, false)
						->save($file_path.$file_large);

					 //Resize original image for thump image
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_thumb_width"), Config::get("webshoppack.photos_thumb_height"), true, false)
						->save($file_path.$file_thumb);

					//Resize original image for small image for index page
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_small_width"), Config::get("webshoppack.photos_small_height"), false, false)
						->save($file_path.$file_small);
				}
				catch(\Exception $e){
					return array('status'=>'error','error_message' => $e->getMessage());
				}

				list($width, $height) 		= getimagesize($file_path . $file_original);
				list($l_width, $l_height) 	= getimagesize($file_path . $file_large);
				list($t_width, $t_height) 	= getimagesize($file_path . $file_thumb);
				list($s_width, $s_height) 	= getimagesize($file_path . $file_small);
				break;
			default:
				$file_type = ($file_type == 'archive') ? 'zip' : $file_type;
				$file_path = Config::get("webshoppack.archive_folder");
				try
				{
					$file->move($file_path, $file_path . $filename_no_ext . '.' . $ext);
				}
				catch(\Exception $e)
				{
					return array('status'=>'error','error_message' => trans("product.products_file_upload_error"));
				}
				$is_downloadable = ($download_file) ? 'Yes' : 'No';
				break;
		}

		$file_info = array(
			'title'				=> $title,
			'filename_no_ext'	=> $filename_no_ext,
			'ext'				=> $ext,
			'file_original'		=> $file_original,
			'file_thumb'		=> $file_thumb,
			'file_large'		=> $file_large,
			'width'				=> $width,
			'height'			=> $height,
			's_width'			=> $s_width,
			's_height'			=> $s_height,
			't_width'			=> $t_width,
			't_height'			=> $t_height,
			'l_width'			=> $l_width,
			'l_height'			=> $l_height,
			'server_url'		=> $server_url,
			'is_downloadable'	=> $is_downloadable);

		 return array('status'=>'success');
	}
	public function uploadMediaFileFromUrl($image_url, $file_type,  &$file_info, $download_file = false)
	{

		// default settings
		$file_original 	= '';
		$file_thumb 	= '';
		$file_large 	= '';
		$width 			= 0;
		$height 		= 0;
		$t_width 		= 0;
		$t_height 		= 0;
		$l_width 		= 0;
		$l_height 		= 0;
		$server_url 	= '';
		$is_downloadable = 'No';

		$errMsg			= '';
		$ext_index 		= strrpos($image_url, '.') + 1;
		$ext 			= substr($image_url, $ext_index, strlen($image_url));
		$strBasename 	= basename($image_url, ".".$ext);
		$title 			= substr($strBasename,  strrpos($strBasename, '.') + 1);
		$filename_no_ext = uniqid(); // generate filename
		$file 			= $filename_no_ext . '.' . $ext;

		$filename_no_ext = uniqid(); // generate filename
		//$ext = pathinfo($image_url, PATHINFO_EXTENSION);


		switch($file_type) {
			case 'image':
				$file_path = Config::get("webshoppack.photos_folder");
				$server_url = URL::asset($file_path);
				$file_original  = $filename_no_ext . '.' . $ext;
				$file_thumb = $filename_no_ext . 'T.' . $ext;
				$file_large = $filename_no_ext . 'L.' . $ext;
				$file_small = $filename_no_ext . 'S.' . $ext;

				$this->chkAndCreateFolder($file_path);

				@chmod($file_original, 0777);
				@chmod($file_thumb, 0777);
				@chmod($file_large, 0777);
				@chmod($file_small, 0777);
				if(!$this->chkIsValidImgURL($image_url))
				{
					$errMsg = 'Warning: '.$image_url. ' does not exist';
				}
				else
				{
					if (function_exists("file_get_contents"))
					{
						if(@$content = file_get_contents($image_url))
						{
							file_put_contents($file_path.$file_original, $content);
						}
					}
					else
					{
						$ch = curl_init($image_url);
						$fp = fopen($file_path.$file_original, 'wb');
						curl_setopt($ch, CURLOPT_FILE, $fp);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_exec($ch);
						curl_close($ch);
						fclose($fp);
					}


					//if($this->chkValidImageFile($file_source, $fileFor)){}
				}
				$image_src = $file_path.$file_original;
				try{

					Image::make($image_src)->save($file_path.$file_original);

					//Resize original image for large image
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_large_width"), Config::get("webshoppack.photos_large_height"), true, false)
						->save($file_path.$file_large);

					 //Resize original image for thump image
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_thumb_width"), Config::get("webshoppack.photos_thumb_height"), true, false)
						->save($file_path.$file_thumb);

					//Resize original image for small image for index page
					Image::make($image_src)
						->resize(Config::get("webshoppack.photos_small_width"), Config::get("webshoppack.photos_small_height"), false, false)
						->save($file_path.$file_small);
				}
				catch(\Exception $e){
					return array('status'=>'error','error_message' => $e->getMessage());
				}

				list($width, $height) 		= getimagesize($file_path . $file_original);
				list($l_width, $l_height) 	= getimagesize($file_path . $file_large);
				list($t_width, $t_height) 	= getimagesize($file_path . $file_thumb);
				list($s_width, $s_height) 	= getimagesize($file_path . $file_small);
				break;
			default:
				$file_type = ($file_type == 'archive') ? 'zip' : $file_type;
				$file_path = Config::get("webshoppack.archive_folder");
				try
				{
					$file->move($file_path, $file_path . $filename_no_ext . '.' . $ext);
				}
				catch(\Exception $e)
				{
					return array('status'=>'error','error_message' => trans("product.products_file_upload_error"));
				}
				$is_downloadable = ($download_file) ? 'Yes' : 'No';
				break;
		}

		$file_info = array(
			'title'				=> $title,
			'filename_no_ext'	=> $filename_no_ext,
			'ext'				=> $ext,
			'file_original'		=> $file_original,
			'file_thumb'		=> $file_thumb,
			'file_large'		=> $file_large,
			'width'				=> $width,
			'height'			=> $height,
			's_width'			=> $s_width,
			's_height'			=> $s_height,
			't_width'			=> $t_width,
			't_height'			=> $t_height,
			'l_width'			=> $l_width,
			'l_height'			=> $l_height,
			'server_url'		=> $server_url,
			'is_downloadable'	=> $is_downloadable);

		 return array('status'=>'success');
	}
	public function chkIsValidImgURL($url)
	{
		$imageArray = getimagesize($url);
		if($imageArray[0])
		{
		    //echo "it's an image and here is the image's info<br>";
		    return true;
		}
		else
		{
		    //echo "invalid image";
		    return false;
		}

		//below is the old method that i used
		if (function_exists('curl_init'))
			{
				$ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL,            $url);
			    curl_setopt($ch, CURLOPT_HEADER,         true);
			    curl_setopt($ch, CURLOPT_NOBODY,         true);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    //curl_setopt($ch, CURLOPT_TIMEOUT,        15);
			    $result = curl_exec($ch);
			    if (!curl_errno($ch))
			    	{
			        	curl_close($ch);
						if(preg_match('/[2][0][0][ ][O][K]/' ,$result))
							{
								return true;
							}
			        }
			}
		else
			{
				$result = @get_headers($url);
				if(preg_match('/[2][0][0][ ][O][K]/' ,$result[0]))
					{
						return true;
					}
			}
		return false;

	}
}