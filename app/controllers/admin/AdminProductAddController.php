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
class AdminProductAddController extends BaseController {

	function __construct()
	{
        $this->productAddService = new AdminProductAddService();
        $this->productService = new ProductService();
        if(CUtil::chkIsAllowedModule('variations'))
		{
        	$this->variations_service = new VariationsService();
        }
        parent::__construct();
    }

	public function getIndex()
	{
		$d_arr = $category_main_arr = $category_sub_arr = $stock_details = array();

		$logged_user_id = BasicCUtil::getLoggedUserId();
		$admin_user_code = BasicCUtil::setUserCode($logged_user_id);


		$title = trans('product.add_product_title');
		$error_msg = '';
		$category_id = (Input::get('my_category_id') == '')? Input::old('my_category_id', 1): Input::get('my_category_id', 1);

		//To add/edit product details
		$p_id = (Input::get('id') == '')? Input::old('id'): Input::get('id');
		$tab = (Input::get('p') == '')? Input::old('p', 'basic'): Input::get('p', 'basic');
		$p_url = URL::action('AdminProductAddController@postAdd');
		$action = 'add';
		$p_details = array();
		if($p_id != '' && is_numeric($p_id))
		{
			$this->header->setMetaTitle(trans('meta.admin_edit_product_title'));
			//To validate product id
			$product = Products::initialize($p_id);
			$product->setIncludeBlockedUserProducts(true);
			$p_details = $product->getProductDetails(0, false);
			if(isset($p_details) && count($p_details) > 0)
			{
				try
				{
					$p_url = URL::action('AdminProductAddController@postEdit');
					$category_id = $p_details['product_category_id'];
					$action = 'edit';
					$d_arr['product_user_details'] = CUtil::getUserDetails($p_details['product_user_id']);

				}
				catch (Exception $e)
				{
					if($e instanceOf ProductNotFoundException)
					{
						$error_msg = $e->getMessage();
					}
					else  if($e instanceOf InvalidProductIdException)
					{
						$error_msg = $e->getMessage();
					}
					else
					{
						$error_msg = $e->getMessage();
					}
				}
			}
			else
			{
				$p_id = '';
				$error_msg = trans('product.invalid_product_id');
			}
		}

		//Render user alert mesage
		if($error_msg != '')
		{
			Session::put('error_message', $error_msg);
			return Redirect::to('admin/product/list')->with('error_message', $error_msg);
		}

		$this->header->setMetaTitle(trans('meta.admin_add_product_title').' - '.ucwords($tab).' - '.'- VAR_SITE_NAME');
		if($action == 'edit' && isset($p_details['product_name']))
			$this->header->setMetaTitle($p_details['product_name'].' - '.ucwords(str_replace('_', ' ', $tab)).' - '.trans('meta.admin_edit_product_title'));
		//$this->header->setMetaTitle($title);
		if($tab == 'basic')
		{
			$user_code = (Input::get('user_code') == '')? Input::old('user_code'): Input::get('user_code');
			$section_arr = array('' => trans('common.select_option'));
			if($action == 'edit')
			{
				$section_arr = $this->productService->getProductSectionDropList($p_details['product_user_id']);
			}

			//To get category list
			$root_category_id = Products::getRootCategoryId();
			$category_main_arr = $this->productService->getCategoryListArr();
			$cat_list = $this->productService->getAllTopLevelCategoryIds($category_id);
			$top_cat_list_arr = explode(',', $cat_list);
			$top_cat_count = count($top_cat_list_arr);
			if($top_cat_count > 1)
			{
				foreach($top_cat_list_arr AS $sel_key => $top_cat_id)
				{
					$category_sub_arr[$top_cat_id] = $this->productService->getSubCategoryList($top_cat_id);
				}
			}
			$d_arr['my_selected_categories'] = $cat_list;
			$d_arr['top_cat_list_arr'] = $top_cat_list_arr;
			$d_arr['my_category_id'] = $category_id;
			$d_arr['top_cat_count'] = $top_cat_count;
			$d_arr['root_category_id'] = $this->productService->root_category_id;
			$d_arr['user_code'] = $user_code;
		}
		elseif($tab == 'price')
		{
			$data['id'] = 0;
			$data['name'] = trans("product.product_price");
			$data['price_details'] = $product->getGroupPriceDetailsById($p_id, 0, 1, 1, false);
			$data['required'] = 'required-icon';
		 	$d_arr['product_price_info'] = $data;
		 	$d_arr['giftwrap_type_arr'] = array('single' => trans('common.single'), 'bulk' => trans('common.bulk'));
		}
		elseif($tab == 'shipping')
		{
			//$countries = array('' => 'Select a Country');
			$countries_arr = Webshopshipments::getCountriesList('list', 'country_name', 'asc', false);
			$countries_list = $countries_arr;
			$d_arr['countries_list'] = $countries_list;

			$this->shipping_template_service = new ShippingTemplateService();
			$ship_templates_list = $this->shipping_template_service->getShippingTemplatesList($p_details['product_user_id']);

			$ship_template_id = '';
			if($p_details['shipping_template'] > 0)
				$ship_template_id = $p_details['shipping_template'];
			else {
				$ship_template_id = $this->shipping_template_service->getDefaultShippingTemplate($p_details['product_user_id']);
				if($ship_template_id == '') {
					if(count($ship_templates_list) > 0) {
						$ship_template_id = CUtil::getArrayFirstKey($ship_templates_list);
					}
				}
			}

			$ship_country_id = CUtil::getShippingCountry();
			$shipping_companies_list = $this->shipping_template_service->getShippingTemplatesCompaniesListWithDetails($ship_template_id, 0, $p_id, 1, array('country_id' => $ship_country_id));
			//echo '<pre>';print_r($shipping_companies_list);echo '</pre>';//exit;
			$d_arr['ship_template_id'] = $ship_template_id;
			$d_arr['ship_country_id'] = $ship_country_id;
			$d_arr['ship_templates_list'] = $ship_templates_list;
			$d_arr['shipping_companies_list'] = $shipping_companies_list;
			$d_arr['shipping_from_country'] = $p_details['shipping_from_country'];
			$d_arr['shipping_from_zip_code'] = $p_details['shipping_from_zip_code'];

			$d_arr['shipping_fees'] = Webshopshipments::getItemShippingList($p_id);
			$d_arr['package_details'] = Webshopshipments::getEditPackageDetails($p_id);
			//echo "<pre>"; print_r($d_arr['package_details']);echo "</pre>";exit;

		}
		elseif($tab == 'tax')
		{
			$taxations = array('' => trans('common.select_a_tax'));

			$admin_group_id = Config::get('generalConfig.admin_group_id');
			$adminManageUserService = new AdminManageUserService();
			$user_ids = $adminManageUserService->fetchGroupMembersLists($admin_group_id);
			$product_user_id = array($p_details['product_user_id']);
			//$user_ids = array_unique(array_merge($user_ids,$product_user_id));

			//$user_id = BasicCUtil::getLoggedUserId();
			//$user_id = $p_details['product_user_id'];
			$taxationslist = Webshoptaxation::Taxations()->getTaxations(array('user_id' => $product_user_id), 'list', array('order_by' => 'tax_name', 'sort_type' => 'asc'));

			if(!$taxationslist) $taxationslist = array();

			$taxations_list = $taxations+$taxationslist;
			$d_arr['taxations_list'] = $taxations_list;

			$d_arr['product_taxations_list'] = Webshoptaxation::ProductTaxations()->getProductTaxations(array('product_id' => $p_id));
		}
		elseif($tab == 'stocks')
		{
			$product = Products::initialize($p_id);
			//$d_arr['stocks_arr'] = $product->getProductStocks($p_id);

			$stock_details_arr = $product->getProductStocksList($p_id);
			$stock_details = array();
			foreach($stock_details_arr as $stock) {
				$stock_details['quantity'] = $stock['quantity'];
				$stock_details['serial_numbers'] = $stock['serial_numbers'];
//				if($stock['stock_country_id'] == 38) {
//					$stock_details['stock_country_id_china'] = $stock['stock_country_id'];
//					$stock_details['quantity_china'] = $stock['quantity'];
//					$stock_details['serial_numbers_china'] = $stock['serial_numbers'];
//				}
//				if($stock['stock_country_id'] == 153) {
//					$stock_details['stock_country_id_pak'] = $stock['stock_country_id'];
//					$stock_details['quantity_pak'] = $stock['quantity'];
//					$stock_details['serial_numbers_pak'] = $stock['serial_numbers'];
//				}
			}
			//echo '<pre>';print_r($stock_details);echo '</pre>';exit;
			//$d_arr['stocks_arr'] = $stock_details;
		}
		elseif($tab == 'attribute')
		{
			$product = Products::initialize($p_id);
			$d_arr['attr_arr'] = $product->getAttributesList($category_id);
		}
		elseif($tab == 'preview_files')
		{
			$product = Products::initialize($p_id);
			$d_arr['p_img_arr'] = $product->getProductImage($p_id, false);
			$d_arr['thumb_no_image'] = CUtil::DISP_IMAGE(145, 145, Config::get("webshoppack.photos_thumb_width"), Config::get("webshoppack.photos_thumb_height"), true);
			$d_arr['default_no_image'] = CUtil::DISP_IMAGE(578, 385, Config::get("webshoppack.photos_large_width"), Config::get("webshoppack.photos_large_height"), true);
			$d_arr['resources_arr'] = $product->populateProductResources('Image');
			$d_arr['swap_imges_arr'] = (CUtil::chkIsAllowedModule('variations')) ? $this->variations_service->populateProductSwapImages($p_id) : array();
		}
		elseif($tab == 'variations' && CUtil::chkIsAllowedModule('variations'))
		{
			$d_arr['head_label_arr'] = array();
			$d_arr['matrix_options_arr'] = array();
			$d_arr['show_matrix_block'] = 0;
			$d_arr['matrix_edit_giftwrap'] = 0;
			$d_arr['matrix_edit_stock'] = 0;
			$d_arr['price_details'] = $product->getGroupPriceDetailsById($p_id, 0, 1, 1, false);

			// For generate headers.
			$head_label_arr = $this->variations_service->getItemVariationsGenerateHeaders($p_id);
			$d_arr['head_label_arr'] = $head_label_arr;
			$d_arr['head_label_str'] = $this->variations_service->convertHeaderLabelArrToStr($head_label_arr);
			// Matrix populate Starts
			$result_arr = $this->variations_service->populateItemVariations($p_id);

			if(isset($result_arr)) {
				$d_arr['matrix_options_arr'] = $result_arr['matrix_data_arr'];
				$d_arr['show_matrix_block'] = (count($d_arr['matrix_options_arr']) > 0) ? 1 : 0;
				$d_arr['matrix_edit_giftwrap'] = $result_arr['show_giftwrap'];
				$d_arr['matrix_edit_stock'] = $result_arr['show_stock'];
			}
			$d_arr['select_action'] = $this->variations_service->getSelectAction($d_arr);
			/*echo '<pre>';print_r($p_details);
			exit;*/
		}
		elseif($tab == 'download_files')
		{
			$product = Products::initialize($p_id);
			$d_arr['resources_arr'] = $product->populateProductResources('Archive', 'Yes');
		}
		elseif($tab == 'cancellation_policy')
		{
			$cancellation_policy = Products::initializeCancellationPolicy();
			$d_arr['cancellation_policy'] = $cancellation_policy->getCancellationPolicyDetails($p_details['product_user_id']);
			$cancellation_policy_value = $d_arr['cancellation_policy'];
			//print_r($d_arr['cancellation_policy']); exit;
			if($d_arr['cancellation_policy']!='')
				$d_arr['default_cancel_available'] = true;
			else
				$d_arr['default_cancel_available'] = false;
			$d_arr['used_default'] = false;
			$d_arr['used_default_text'] = false;
			$d_arr['used_default_file'] = false;
			if($p_details['cancellation_policy_text'] == '' && $p_details['cancellation_policy_filename'] == '')
			{
				$d_arr['used_default'] = true;
				if($d_arr['cancellation_policy']['cancellation_policy_filename'] != '')
				{
					$d_arr['used_default_file'] = true;

					$d_arr['default_cancellation_policy_filename'] = $d_arr['cancellation_policy']->cancellation_policy_filename;
					$d_arr['default_cancellation_policy_filetype'] = $d_arr['cancellation_policy']->cancellation_policy_filetype;
					$d_arr['default_cancellation_policy_server_url'] = $d_arr['cancellation_policy']->cancellation_policy_server_url;
					//echo "<pre>";print_r($d_arr);echo "</pre>";

				}
				elseif($d_arr['cancellation_policy']['cancellation_policy_text'] != '')
				{
					$d_arr['used_default_text'] = true;
					//$p_details['cancellation_policy_text'] = $d_arr['cancellation_policy']['cancellation_policy_text'];
				}
			}
		}
		elseif($tab == 'status')
		{
			$product = Products::initialize($p_id);
			$d_arr['product_notes'] = $product->getProductNotes();
			$d_arr['status_arr'] = $this->productAddService->getProductStatusDropList();
			$d_arr['product_view_url'] = $this->productService->getProductViewURL($p_id, $p_details);
		}
		$d_arr['p'] = $tab;
		$d_arr['tab_list'] = $this->productAddService->getTabList($p_id, $p_details, $action);
		if((!isset($d_arr['tab_list'][$tab])) || (isset($d_arr['tab_list'][$tab]) && !$d_arr['tab_list'][$tab]))
		{
			return Redirect::to('admin/product/add')->with('error_message', trans('common.invalid_access'));
		}
		$service_obj = $this->productService;
		$d_arr['allow_variation'] = (CUtil::chkIsAllowedModule('variations')) ? 1 : 0;
		$variations_obj = ($d_arr['allow_variation'] > 0) ? $this->variations_service : '';
		$d_arr['allow_swap_image'] = 0;
		if($p_id != '') {
			if($d_arr['allow_variation'] > 0 && $p_details['is_downloadable_product'] == 'No' &&  $p_details['use_variation'])
			{
				$d_arr['allow_swap_image'] = 1;
			}
			$d_arr['allow_giftwrap'] = ($d_arr['allow_variation'] && Config::has('plugin.allowusers_to_use_giftwrap') && Config::get('plugin.allowusers_to_use_giftwrap')) ? 1 : 0;
		}
		return View::make('admin.addProduct', compact('cancellation_policy_value', 'd_arr', 'section_arr', 'p_details', 'stock_details', 'p_id', 'p_url', 'category_main_arr', 'category_sub_arr', 'service_obj', 'action', 'category_id', 'admin_user_code', 'variations_obj', 'logged_user_id'));
	}

	public function postAdd()
	{
		if(Input::has('add_product'))
		{
			$input_arr = Input::All();

			$error_msg = '';
			$is_downloadable_product = isset($input_arr['is_downloadable_product'])? $input_arr['is_downloadable_product']: 'No';
			$user_section_id = (isset($input_arr['user_section_id']) && is_numeric($input_arr['user_section_id'])) ? $input_arr['user_section_id'] : 0;
			$user_id = CUtil::getUserId($input_arr['user_code']);

			if($user_id != '' && !ctype_digit($user_id)) {
				$error_msg = trans('admin/productAdd.invalid_user_code');
				return Redirect::to('admin/product/add')->with('error_message', $error_msg)->withInput();
			}

			if(!CUtil::isShopOwner($user_id)) {
				$error_msg = trans('product.invalid_seller_usercode');
				return Redirect::to('admin/product/add')->with('error_message', $error_msg)->withInput();
			}

			$product = Products::initialize();
			$product->setProductUserId($user_id);
			$product->setTitle($input_arr['product_name']);
			$product->setUrlSlug($input_arr['url_slug']);
			$product->setDescription($input_arr['product_description']);
			$product->setSupportContent($input_arr['product_support_content']);
			$product->setSummary($input_arr['product_highlight_text']);
			$product->setCategory($input_arr['my_category_id']);
			$product->setSection($user_section_id);
			$product->setDemoUrl($input_arr['demo_url']);
			$product->setDemoDetails($input_arr['demo_details']);
			$product->setProductTags($input_arr['product_tags']);
			$product->setIsDownloadableProduct($is_downloadable_product);

			if($is_downloadable_product == 'Yes')
			{
				$product->setShippingTemplate(0);
				$product->removePackageDetails($input_arr['id']);

				$product->setCancellationPolicyFileName('');
				$product->setCancellationPolicyFileType('');
				$product->setCancellationPolicyServerUrl('');
				$product->setCancellationPolicyText('');
				$product->setUseDefaultCancellation('No');
				$product->setUseCancellationPolicy('No');
			}

			$details = $product->save();
			$json_data = json_decode($details, true);

			if($json_data['status'] == 'error')
			{
				foreach($json_data['error_messages'] AS $err_msg)
				{
					$error_msg .= "<p>".$err_msg."</p>";
				}
			}
			else
			{
				$add_product_id = $json_data['product_id'];
				return Redirect::to('admin/product/add?id='.$add_product_id.'&p=price');
			}
			if($error_msg != '')
			{
				return Redirect::to('admin/product/add')->with('error_message', $error_msg)->withInput();
			}
		}
		return Redirect::to('admin/product/add');
	}

	public function postProductActions()
	{
		$action = Input::get('action');
		$p_id = Input::get('product_id');

		switch($action)
		{
			case 'delete_cancellation_file':
				$details = $this->productAddService->deleteProductCancellationPolicyFile($p_id);
				if($details){
					$product = Products::initialize($p_id);
					$product->setUseCancellationPolicy('No');
					$product->save();
					echo json_encode(array(	'result'=>'success'));
				} else {
					echo json_encode(array(	'result'=>'failed', 'error_message' => trans('product.cancellation_pliicy_delete_error')));
				}
			break;
			case 'save_product_thumb_image_title':
				$title = Input::get('product_image_title');
				$product = Products::initialize($p_id);
				echo ($product->saveProductImageTitle('thumb', $title)) ? 'success': 'error';
				//$product->changeStatus('Draft');
				exit;
				break;

			case 'save_product_default_image_title':
				$title = Input::get('product_image_title');
				$product = Products::initialize($p_id);
				echo ($product->saveProductImageTitle('default', $title)) ? 'success': 'error';
				//$product->changeStatus('Draft');
				exit;
				break;

			case 'upload_product_thumb_image':
				$title = Input::get('product_image_title');
				$this->productAddService->product_media_type = 'image';
				$this->productAddService->setAllowedUploadFormats('thumb');
				$this->productAddService->setMaxUploadSize('thumb');

				$file_info = array();
				$file = Input::file('uploadfile');
				$upload_file_name = $file->getClientOriginalName();
				$upload_status = $this->productAddService->uploadMediaFile('uploadfile', 'image', $file_info);
				if ($upload_status['status'] == 'success')
				{
					$this->productService->removeItemImageFile($p_id, 'thumb'); // removes actual file if already exists
					$product = Products::initialize($p_id);
					$product->updateProductThumbImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
					$image_dim = CUtil::DISP_IMAGE(145, 145, $file_info['t_width'], $file_info['t_height'], true);
					echo json_encode(array('status'=>'success',
									'server_url'=>$file_info['server_url'],
									'filename'=>$file_info['filename_no_ext'] .'T.'.$file_info['ext'] ,
									't_width'=>$image_dim['width'],
									't_height'=>$image_dim['height'],
									'title'=>$file_info['title']
									));
					//$this->productService->updateProductStatus($p_id, 'Draft');
					//$product->changeStatus('Draft');
				}
				else
				{
					echo json_encode(array('status'=>'error', 'error_message'=>$upload_status['error_message'], 'filename'=>$upload_file_name));
				}
				exit;
				break;

			case 'upload_item_default_image':
				$title = Input::get('product_image_title');
				$this->productAddService->product_media_type = 'image';
				$this->productAddService->setAllowedUploadFormats('default');
				$this->productAddService->setMaxUploadSize('default');

				$file_info = array();
				$file = Input::file('uploadfile');
				$upload_file_name = $file->getClientOriginalName();
				$upload_status = $this->productAddService->uploadMediaFile('uploadfile', 'image', $file_info);
				if ($upload_status['status'] == 'success')
				{
					$this->productService->removeItemImageFile($p_id, 'default'); // removes actual file if already exists
					$product = Products::initialize($p_id);
					$product->updateProductDefaultImage($file_info['title'], $file_info['filename_no_ext'], $file_info['ext'], $file_info['width'], $file_info['height'], $file_info['s_width'], $file_info['s_height'], $file_info['t_width'], $file_info['t_height'], $file_info['l_width'], $file_info['l_height']);
					$image_dim = CUtil::DISP_IMAGE(578, 385, $file_info['l_width'], $file_info['l_height'], true);
					echo json_encode(array('status'=>'success',
									'server_url'=>$file_info['server_url'],
									'filename'=>$file_info['filename_no_ext'] .'L.'.$file_info['ext'] ,
									't_width'=>$image_dim['width'],
									't_height'=>$image_dim['height'],
									'title'=>$file_info['title']
									));
					//$this->productService->updateProductStatus($p_id, 'Draft');
					//$product->changeStatus('Draft');
				}
				else
				{
					echo json_encode(array('status'=>'error', 'error_message'=>$upload_status['error_message'], 'filename'=>$upload_file_name));
				}
				exit;
				break;

			case 'remove_default_thumb_image':
				$this->productService->removeItemImageFile($p_id, 'thumb'); // removes actual file
				$product = Products::initialize($p_id);
				echo ($product->removeProductThumbImage()) ? 'success': 'error';
				//$product->changeStatus('Draft');
				exit;
				break;

			case 'remove_default_image':
				$this->productService->removeItemImageFile($p_id, 'default'); // removes actual file
				$product = Products::initialize($p_id);
				echo ($product->removeProductDefaultImage()) ? 'success': 'error';
				//$product->changeStatus('Draft');
				exit;
				break;

			case 'upload_resource_preview': // images on the image tab
				$resource_type = Input::get('resource_type');
				$this->productAddService->setProductPreviewType($p_id);
				$this->productAddService->setAllowedUploadFormats('preview');
				$this->productAddService->setMaxUploadSize('preview');

				$product = Products::initialize($p_id);
				$resource_count = $product->getProductResourceCount($this->productAddService->product_media_type);
				if($resource_count < Config::get('webshoppack.preview_max'))
				{
					$file_info = array();
					$file = Input::file('uploadfile');
					$upload_file_name = $file->getClientOriginalName();
					$upload_status = $this->productAddService->uploadMediaFile('uploadfile',  $this->productAddService->product_media_type, $file_info);
					if ($upload_status['status'] == 'success')
					{
						$resource_arr = array(
							'product_id'=>$p_id,
							'resource_type'=>$resource_type, // hard coded
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
						$image_dim = CUtil::DISP_IMAGE(74, 74, $file_info['t_width'], $file_info['t_height'], true);

						//$product->changeStatus('Draft');
						echo json_encode(array('status' => 'success',
										'resource_type' => ucwords($resource_type),
										'server_url' => $file_info['server_url'],
										'filename' => $file_info['file_thumb'],
										't_width' => $image_dim['width'],
										't_height' => $image_dim['height'],
										'title' => $file_info['title'],
										'resource_id' => $resource_id
										));

					}
					else
					{
						echo json_encode(array('status'=>'error', 'error_message'=>$upload_status['error_message'], 'filename'=>$upload_file_name));
					}
				}
				else
				{
					echo json_encode(array('status'=>'error', 'error_message'=> trans('product.products_max_file'), 'filename'=> ''));
				}

				exit;
				break;
			case 'upload_swap_image': // images on the image tab
				if(isset($this->variations_service))
				{
					$resource_type = Input::get('resource_type');
					$this->productAddService->setAllowedUploadFormats('preview');
					//$this->productAddService->setMaxUploadSize('preview');
					$this->productAddService->product_max_upload_size = Config::get("variations::variations.swap_img_max_size");
					$resource_count = $this->variations_service->getProductSwapImageCount($p_id);
					if($resource_count < Config::get('variations::variations.swap_img_max'))
					{
						$file_info = array();
						$file = Input::file('uploadfile');
						$upload_file_name = $file->getClientOriginalName();
						$upload_status = $this->productAddService->uploadMediaFile('uploadfile', $resource_type, $file_info);
						if ($upload_status['status'] == 'success')
						{
							$swap_image_arr = array(
								'item_id'=>$p_id,
								'filename'=>$file_info['filename_no_ext'],
								'ext'=>$file_info['ext'],
								'title'=>$file_info['title'],
								'width'=>$file_info['width'],
								'height'=>$file_info['height'],
								't_width'=>$file_info['t_width'],
								't_height'=>$file_info['t_height'],
								'l_width'=>$file_info['l_width'],
								'l_height'=>$file_info['l_height'],
								'server_url'=>$file_info['server_url']
						 	);
							$swap_image_id = $this->variations_service->insertSwapImageFiles($swap_image_arr);
							$image_dim = CUtil::DISP_IMAGE(74, 74, $file_info['t_width'], $file_info['t_height'], true);
							//$product->changeStatus('Draft');
							echo json_encode(array('status' => 'success',
											'resource_type' => $resource_type,
											'server_url' => $file_info['server_url'],
											'filename' => $file_info['file_thumb'],
											't_width' => $image_dim['width'],
											't_height' => $image_dim['height'],
											'title' => $file_info['title'],
											'resource_id' => $swap_image_id
											));
						}
						else
						{
							echo json_encode(array('status'=>'error', 'error_message'=>$upload_status['error_message'], 'filename'=>$upload_file_name));
						}
					}
					else
					{
						echo json_encode(array('status'=>'error', 'error_message'=> trans('product.products_max_file'), 'filename'=> ''));
					}
				}
				exit;
				break;

			case 'save_resource_title':
				$row_id = Input::get('row_id');
				$resource_title = Input::get('resource_title');

				$product = Products::initialize($p_id);
				echo ($product->updateProductResourceTitle($row_id, $resource_title)) ? 'success': 'error';
				//$product->changeStatus('Draft');
				exit;
				break;

			case 'save_swap_image_title':
				if(isset($this->variations_service))
				{
					$row_id = Input::get('row_id');
					$resource_title = Input::get('resource_title');
					echo ($this->variations_service->updateProductSwapImageTitle($row_id, $resource_title)) ? 'success': 'error';
				}
				exit;
				break;

			case 'delete_resource':
				$row_id = Input::get('row_id');
				$product = Products::initialize($p_id);
				if($this->productService->deleteProductResource($product, $row_id))
				{
					//$product->changeStatus('Draft');
					echo json_encode(array(	'result'=>'success','row_id'=> $row_id));
				}
				else
				{
					echo json_encode(array(	'result'=>'failed','row_id'=> $row_id));
				}

				exit;
				break;

			case 'delete_swap_image':
				if(isset($this->variations_service))
				{
					$row_id = Input::get('row_id');
					$product = Products::initialize($p_id);
					if($this->variations_service->deleteProductSwapImage($row_id))
					{
						//$product->changeStatus('Draft');
						echo json_encode(array(	'result'=>'success','row_id'=> $row_id));
					}
					else
					{
						echo json_encode(array(	'result'=>'failed','row_id'=> $row_id));
					}
				}
				exit;
				break;

			case 'order_resource':
				$resourcednd_arr = Input::get('resourcednd');
				$this->productService->updateProductResourceImageDisplayOrder($resourcednd_arr);
				// set status is not called since only re-ordering
				exit;
				break;

			case 'upload_resource_file': // the download file in zip format
				$resource_type = 'Archive';
				$this->productAddService->product_media_type = 'archive';
				$this->productAddService->setAllowedUploadFormats('archive');
				$this->productAddService->setMaxUploadSize('archive');

				$product = Products::initialize($p_id);
				$resource_count = $product->getProductResourceCount($this->productAddService->product_media_type);
				if($resource_count == 0)
				{
					$file_info = array();
					$file = Input::file('uploadfile');
					$upload_file_name = $file->getClientOriginalName();
					$upload_status = $this->productAddService->uploadMediaFile('uploadfile',$this->productAddService->product_media_type, $file_info,  true);
					if ($upload_status['status'] == 'success') {
						$resource_arr = array(
							'product_id'=>$p_id,
							'resource_type'=>$resource_type,
							'server_url'=>$file_info['server_url'],
							'filename'=>$file_info['filename_no_ext'],
							'ext'=>$file_info['ext'],
							'title'=>$file_info['title'],
							'width'=>$file_info['width'],
							'height'=>$file_info['height'],
							't_width'=>$file_info['t_width'],
							't_height'=>$file_info['t_height'],
							'l_width'=>$file_info['l_width'],
							'l_height'=>$file_info['l_height'],
							'is_downloadable'=>$file_info['is_downloadable']
					 	);

						$resource_id = $product->insertDownloadFile($file_info['filename_no_ext'], $file_info['ext'], $file_info['title']);
						if ($file_info['title'] != '')
						{
							$download_filename = preg_replace('/[^0-9a-z\.\_\-)]/i', '', $file_info['title']) . '.' . $file_info['ext'];
						}
						else
						{
							$download_filename = md5($p_id) . '.' .$file_info['ext'];
						}

						echo json_encode(array('status'=>'success',
										'server_url'=>$file_info['server_url'],
										'download_url'=> URL::action('AdminProductAddController@getProductActions'). '?action=download_file&product_id=' . $p_id,
										'filename'=>$download_filename ,
										't_width'=>$file_info['t_width'],
										't_height'=>$file_info['t_height'],
										'title'=>$file_info['title'],
										'resource_id'=>$resource_id,
										'is_downloadable'=>$file_info['is_downloadable']
										));
						//$product->changeStatus('Draft');
					}
					else
					{
						echo json_encode(array('status'=>'error', 'error_message'=>$upload_status['error_message'], 'filename'=>$upload_file_name));
					}
				}
				else
				{
					echo json_encode(array('status'=>'error', 'error_message'=> trans('product.products_max_file'), 'filename'=> ''));
				}

				exit;
				break;

			case 'delete_shipping':
				$shipping_id = Input::get('shipping_id');
				$product = Products::initialize($p_id);

				try
				{
					$shipment_id = Webshopshipments::deleteShippingFee(array(), $shipping_id);
					if($shipment_id)
					{
						//$product->changeStatus('Draft');
						echo json_encode(array(	'result'=>'success','shipping_id'=> $shipping_id));
					}
					else
					{
						echo json_encode(array(	'result'=>'failed','shipping_id'=> $shipping_id));
					}
				}
				catch(Exception $e)
				{
					echo json_encode(array(	'result'=>'failed','shipping_id'=> $shipping_id));
				}

				exit;
				break;

			case 'edit_shipping':
				$inputs = Input::all();
				$shipping_id = Input::get('shipping_id');
				$shipping_fee = Input::get('shipping_fee');
				//echo "<pre>";print_r($inputs);echo "</pre>";
				$product = Products::initialize($p_id);
				try
				{

					$shipment_id = Webshopshipments::updateShippingFee(array('shipping_fee' => $shipping_fee), $shipping_id);
					if($shipment_id)
					{
						//$product->changeStatus('Draft');
						echo json_encode(array(	'result'=>'success','shipping_id'=> $shipping_id));
					}
					else
					{
						echo json_encode(array(	'result'=>'success','shipping_id'=> $shipping_id, 'error_msg' => 'Fee not changed'));
					}
				}
				catch(Exception $e)
				{
					echo json_encode(array(	'result'=>'failed','shipping_id'=> $shipping_id, 'error_msg' => $e->getMessage()));
				}

				exit;
				break;

			case 'delete_tax':
				$taxation_id = Input::get('taxation_id');
				$product = Products::initialize($p_id);

				try
				{
					$taxatonid = Webshoptaxation::ProductTaxations()->deleteProductTaxation($taxation_id);

					if($taxatonid)
					{
						//$product->changeStatus('Draft');
						echo json_encode(array(	'result'=>'success','taxation_id'=> $taxation_id));
					}
					else
					{
						echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id));
					}
				}
				catch(Exception $e)
				{
					echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id));
				}

				exit;
				break;

			case 'edit_tax':
				$inputs = Input::all();
				$taxation_id = Input::get('taxation_id');
				$tax_fee = Input::get('tax_fee');
				$fee_type = Input::get('fee_type');
				//echo "<pre>";print_r($inputs);echo "</pre>";
				$product = Products::initialize($p_id);

				try
				{
					if($taxation_id > 0)
					{
						$inputs = array(
							'tax_fee' 	=> $tax_fee,
							'fee_type'	=> $fee_type,
						);

						$taxatonid = Webshoptaxation::ProductTaxations()->updateProductTaxation($taxation_id, $inputs);
						if($taxatonid)
						{
							echo json_encode(array(	'result'=>'success','taxation_id'=> $taxation_id));
						}
						else{
							echo json_encode(array(	'result'=>'success','taxation_id'=> $taxation_id, 'error_msg' => 'Fee not changed'));
						}
					}
					else{
						echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id, 'error_msg' => trans('admin/taxation.select_valid_taxation')));
					}
				}
				catch(Exception $e)
				{
					echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id, 'error_msg' => $e->getMessage()));
				}
				exit;
				break;

			case 'get_taxation_details':
				$inputs = Input::all();
				$taxation_id = Input::get('taxation_id');
				$product = Products::initialize($p_id);

				try
				{
					if($taxation_id > 0)
					{

						$taxation_det = Webshoptaxation::Taxations()->getTaxations(array('id' => $taxation_id), 'first');
						if(isset($taxation_det) && $taxation_det!='' && count($taxation_det) > 0)
						{
							echo json_encode(array(	'result'=>'success','taxation_id'=> $taxation_id, 'tax_fee' => $taxation_det->tax_fee, 'fee_type' => $taxation_det->fee_type));
						}
						else{
							echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id, 'error_msg' => 'Fee details not avalable'));
						}
					}
					else{
						echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id, 'error_msg' => trans('admin/taxation.select_valid_taxation')));
					}
				}
				catch(Exception $e)
				{
					echo json_encode(array(	'result'=>'failed','taxation_id'=> $taxation_id, 'error_msg' => $e->getMessage()));
				}
				exit;
				break;

			case 'check_user':
				$user_code = Input::get('user_code');
				$user_id = CUtil::getUserId($user_code);
				if($user_id != "" && $user_id > 0)
				{
					$user_details = CUtil::getUserDetails($user_id);
					if(count($user_details) > 0)
					{
						$shop_obj = Products::initializeShops();
						//echo $user_id;exit;
						if(!CUtil::isShopOwner($user_id, $shop_obj))//$this->productAddService->checkIsShopOwner($user_id)
						{
							echo json_encode(array('status'=>'error', 'message' => trans('product.invalid_seller_usercode')));
						}
						/*elseif($user_arr['user_status'] != 'Ok')
						{
							echo json_encode(array('status'=>'error', 'message' => trans('mp_product/form.not_active_seller')));
						}*/
						else
						{
							$section_options = $this->productAddService->getProductUserSections($user_id);
							echo json_encode(array('status'=>'success', 'section_options' => $section_options));
						}
					}
					else
					{
						echo json_encode(array('status'=>'error', 'message' => trans('product.invalid_user_code')));
					}
				}
				else
				{
					echo json_encode(array('status'=>'error', 'message' => trans('product.invalid_user_code')));
				}
				exit;
				break;

			case 'load_group_variations_list':
				if(isset($this->variations_service))
				{
					$group_id = Input::get('group_id');
					$product_id = Input::get('product_id');
					$result_arr = $this->variations_service->populateVariationsInGroupByGroupId($group_id, $product_id, 1);
					$var_resource_options_arr = $result_arr['var_resource_options_arr'];
					$var_show_cancel_button = $result_arr['var_show_cancel_button'];
					echo View::make('variations::admin.manageItemsVariationsAttributesBlock', compact('var_resource_options_arr', 'var_show_cancel_button'));
				}
				exit;
				break;

			case 'getMatrixDetails':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();
					//check if single edit or multiple edit
					if(isset($inputs_arr['mul_mat_edit'])){
						$matIdsArr = (isset($inputs_arr['matrix_ids'][0]) ) ? explode(',', $inputs_arr['matrix_ids'][0]) : array();
						if(COUNT($matIdsArr) == 1 && isset($matIdsArr[0]))
						{
							$variations_det_arr = $this->variations_service->populateMatrixDetails($matIdsArr[0], $inputs_arr['product_id']);
						}
						else
						{
							$variations_det_arr = $this->variations_service->populateDefaultMatrixDetails($inputs_arr['matrix_ids'], $inputs_arr['product_id']);
						}
						$variations_det_arr['multiple_edit'] = 1;
						if($inputs_arr['edit_field'] != '')
							$variations_det_arr['edit_field'] = $inputs_arr['edit_field'];
						else
							$variations_det_arr['edit_field'] = 'all';
					}
					else
					{
						$variations_det_arr = $this->variations_service->populateMatrixDetails($inputs_arr['matrix_id'], $inputs_arr['product_id']);
						$variations_det_arr['multiple_edit'] = 0;
						$variations_det_arr['edit_field'] = 'all';
					}
					$update_block_select_action = array('unchange' => Lang::get('variations::variations.no_change'), 'increase' => Lang::get('variations::variations.increase_lbl'), 'decrease' => Lang::get('variations::variations.decrease_lbl'));
					$variations_det_arr['update_block_select_action_arr'] = $update_block_select_action;
					$variations_det_arr['swap_img_folder'] = URL::asset(Config::get('variations::variations.swap_img_folder'));
					$variations_det_arr['swap_img_no_image_folder'] = URL::asset('images/no_image');
					$item_matrix_details_arr = $variations_det_arr;
					echo View::make('variations::admin.matrixItemUpdateBlock', compact('item_matrix_details_arr'));
				}
				exit;
				break;

			case 'delete_matrix':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();
					$this->variations_service->removeItemVariationAttributes($inputs_arr['product_id'], $inputs_arr['attribute_id']);
					$this->variations_service->removeMatrixEntryById($inputs_arr['product_id'], $inputs_arr['matrix_id']);
					echo json_encode(array(	'status'=>'success'));
				}
	        	exit;
				break;

			case 'enable_matrix':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();

					$dat_arr = array();
					$dat_arr['matrix_id'] = $inputs_arr['matrix_id'];
					$dat_arr['product_id'] = $inputs_arr['product_id'];
					$dat_arr['is_active'] = 1;
					$this->variations_service->updateMatrixDetails($dat_arr);
					$this->variations_service->updateMatrixContentDetails($dat_arr);
					$this->variations_service->updateItemVariationAttributes($inputs_arr['product_id'], $inputs_arr['attribute_id'], '1');

					$matrix_row_det = array();
					$matrix_row_det = $this->variations_service->populateMatrixDetails($inputs_arr['matrix_id'], $inputs_arr['product_id']);
					$matrix_row_details = $matrix_row_det;
					echo View::make('variations::admin.manageItemsMatrixRowBlock', compact('matrix_row_details'));
				}
				exit;
				break;

			case 'disable_matrix':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();

					$dat_arr = array();
					$dat_arr['matrix_id'] = $inputs_arr['matrix_id'];
					$dat_arr['product_id'] = $inputs_arr['product_id'];
					$dat_arr['is_active'] = 0;
					$this->variations_service->updateMatrixDetails($dat_arr);
					$this->variations_service->updateMatrixContentDetails($dat_arr);
					$this->variations_service->updateItemVariationAttributes($inputs_arr['product_id'], $inputs_arr['attribute_id'], '0');

					$matrix_row_det = array();
					$matrix_row_det = $this->variations_service->populateMatrixDetails($inputs_arr['matrix_id'], $inputs_arr['product_id']);
					$matrix_row_details = $matrix_row_det;
					echo View::make('variations::admin.manageItemsMatrixRowBlock', compact('matrix_row_details'));
					exit;
				}
				exit;
				break;

			case 'remove_matrix_swapimg':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();
					$data_arr = array();
					$data_arr['product_id'] = $inputs_arr['product_id'];
					$data_arr['matrix_id'] = $inputs_arr['matrix_id'];
		    		$data_arr['swap_img_id'] = 0;
					$this->variations_service->updateMatrixDetails($data_arr);

					$img_det_arr = array();
					$file_path = URL::asset('images/no_image');
					$img_src = $file_path.'/prodnoimage-215x170.jpg';
					$img_det_arr['img_src'] = $img_src;
					$img_det_arr['img_name'] = 	$img_det_arr['img_title'] = "No image";
					$img_det_arr['disp_img'] = 0;
					echo json_encode(array('status'=>'success', 'data_arr' => $img_det_arr));
				}
				exit;
				break;

			case 'removeMatrixSwapimg':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();

					$data_arr = array();
					$data_arr['product_id'] = $inputs_arr['product_id'];
					$data_arr['matrix_id'] = $inputs_arr['matrix_id'];
		    		$data_arr['swap_img_id'] = 0;
					$this->variations_service->updateMatrixDetails($data_arr);
					$matrix_row_det = array();
					$matrix_row_det = $this->variations_service->populateMatrixDetails($inputs_arr['matrix_id'], $inputs_arr['product_id']);
					ob_start();
					$matrix_row_details = $matrix_row_det;
					echo View::make('variations::manageItemsMatrixRowBlock', compact('matrix_row_details'));
		        	$resp_html = ob_get_contents();
					ob_end_clean();
					echo json_encode(array('status'=>'success', 'op_html'=>$resp_html));
				}
				exit;
				break;

			case 'set_default_matrix':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();
					$dat_arr = array();
					$dat_arr['matrix_id'] = $inputs_arr['matrix_id'];
					$dat_arr['product_id'] = $inputs_arr['product_id'];
					$dat_arr['is_default'] = '1';
					$this->variations_service->setAsDefaultMatrix($dat_arr);

					$d_arr['head_label_arr'] = array();
					$d_arr['matrix_options_arr'] = array();
					$d_arr['show_matrix_block'] = 0;
					$d_arr['matrix_edit_giftwrap'] = 0;
					$d_arr['matrix_edit_stock'] = 0;
					// For generate headers.
					$head_label_arr = $this->variations_service->getItemVariationsGenerateHeaders($inputs_arr['product_id']);
					$d_arr['head_label_arr'] = $head_label_arr;
					$d_arr['head_label_str'] = $this->variations_service->convertHeaderLabelArrToStr($head_label_arr);
					// Matrix populate Starts
					$result_arr = $this->variations_service->populateItemVariations($inputs_arr['product_id']);
					if(isset($result_arr)) {
						$d_arr['matrix_options_arr'] = $result_arr['matrix_data_arr'];
						$d_arr['show_matrix_block'] = (count($d_arr['matrix_options_arr']) > 0) ? 1 : 0;
						$d_arr['matrix_edit_giftwrap'] = $result_arr['show_giftwrap'];
						$d_arr['matrix_edit_stock'] = $result_arr['show_stock'];
					}
					$d_arr['select_action'] = $this->variations_service->getSelectAction($d_arr);

					$d_arr['p'] = (Input::get('p') == '')? Input::old('p', 'basic'): Input::get('p', 'basic');
					$p_id = $inputs_arr['product_id'];
					echo View::make('variations::admin.manageItemsAttribMatrix', compact('d_arr', 'p_id'));
	        	}
				exit;
				break;

			case 'rem_default_matrix':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();

					$dat_arr = array();
					$dat_arr['matrix_id'] = $inputs_arr['matrix_id'];
					$dat_arr['product_id'] = $inputs_arr['product_id'];
					$dat_arr['is_default'] = '0';
					$this->variations_service->setAsDefaultMatrix($dat_arr);
					//ob_start();
					$d_arr['head_label_arr'] = array();
					$d_arr['matrix_options_arr'] = array();
					$d_arr['show_matrix_block'] = 0;
					$d_arr['matrix_edit_giftwrap'] = 0;
					$d_arr['matrix_edit_stock'] = 0;
					// For generate headers.
					$head_label_arr = $this->variations_service->getItemVariationsGenerateHeaders($inputs_arr['product_id']);
					$d_arr['head_label_arr'] = $head_label_arr;
					$d_arr['head_label_str'] = $this->variations_service->convertHeaderLabelArrToStr($head_label_arr);
					// Matrix populate Starts
					$result_arr = $this->variations_service->populateItemVariations($inputs_arr['product_id']);
					if(isset($result_arr)) {
						$d_arr['matrix_options_arr'] = $result_arr['matrix_data_arr'];
						$d_arr['show_matrix_block'] = (count($d_arr['matrix_options_arr']) > 0) ? 1 : 0;
						$d_arr['matrix_edit_giftwrap'] = $result_arr['show_giftwrap'];
						$d_arr['matrix_edit_stock'] = $result_arr['show_stock'];
					}
					$d_arr['select_action'] = $this->variations_service->getSelectAction($d_arr);
					$d_arr['p'] = (Input::get('p') == '')? Input::old('p', 'basic'): Input::get('p', 'basic');
					$p_id = $inputs_arr['product_id'];
					echo View::make('variations::admin.manageItemsAttribMatrix', compact('d_arr', 'p_id'));
	        	}
				exit;
				break;

			case 'updateMatrixSwapImage':
				if(isset($this->variations_service))
				{
					$inputs_arr = Input::All();

		    		$data_arr = array();
					$data_arr['product_id'] = $inputs_arr['product_id'];
		    		$data_arr['matrix_id'] = $inputs_arr['matrix_id'];
		    		$data_arr['swap_img_id'] = $inputs_arr['matrix_swap_img_id'];
					$this->variations_service->updateMatrixDetails($data_arr);

					$matrix_row_det = array();
					$matrix_row_det = $this->variations_service->populateMatrixDetails($inputs_arr['matrix_id'], $inputs_arr['product_id']);
					$matrix_row_details = $matrix_row_det;
					echo View::make('variations::admin.manageItemsMatrixRowBlock', compact('matrix_row_details'));
		        }
				exit;
				break;

			case 'update_matrix_details':
				$inputs_arr = Input::All();
				$valid_matrix_update = 1;
				$err_msg = '';
				$edit_all = 0;
				//$edit_fields_allowed_arr = array('edit_price', 'edit_shipping_fee', 'edit_stock', 'edit_gift_wrapprice', 'edit_swap_image');
				$edit_field = $inputs_arr['edit_field'];
				if($edit_field == '' OR $edit_field == 'all') {
					$edit_all = 1;
				}
				if(isset($this->variations_service))
				{
		    		$data_arr = array();
					if($edit_all == 1 OR $edit_field == 'edit_price')
					{
						$data_arr['price_impact'] = isset($inputs_arr['matrix_price_impact']) ? $inputs_arr['matrix_price_impact'] : "";
						switch($data_arr['price_impact'])
						{
			    			case 'increase':
			    				$data_arr['price'] = + $inputs_arr['matrix_price'];
			    				break;
			    			case 'decrease':
			    				if(!$this->variations_service->chkIsValidDecreasePriceChange($inputs_arr['matrix_price'], $inputs_arr['product_id']))
			    				{
									$valid_matrix_update = 0;
									$err_msg.= ''.Lang::get('variations::variations.item_matrix_price_err_msg');
								}
			    				$data_arr['price'] = - $inputs_arr['matrix_price'];
			    				break;
			    			default:
			    				$data_arr['price'] = 0;
			    		}
			    	}
					if($edit_all == 1 OR $edit_field == 'edit_gift_wrapprice')
					{
						$data_arr['giftwrap_price_impact'] = isset($inputs_arr['matrix_giftwrap_price_impact']) ? $inputs_arr['matrix_giftwrap_price_impact'] : "";
			    		switch($data_arr['giftwrap_price_impact'])
						{
			    			case 'increase':
			    				$data_arr['giftwrap_price'] = $inputs_arr['matrix_giftwrap_price'];
			    				break;

			    			case 'decrease':
			    				if(!$this->variations_service->chkIsValidDecreaseGiftPriceChange($inputs_arr['matrix_giftwrap_price'], $inputs_arr['product_id']))
			    				{
									$valid_matrix_update = 0;
									$err_msg.= ''.Lang::get('variations::variations.item_matrix_giftprice_err_msg');
								}
			    				$data_arr['giftwrap_price'] = - $inputs_arr['matrix_giftwrap_price'];
			    				break;

			    			default:
			    				$data_arr['giftwrap_price'] = 0;
			    		}
			    	}
					if($edit_all == 1 OR $edit_field == 'edit_shipping_fee')
					{
						$data_arr['shipping_price_impact'] = isset($inputs_arr['matrix_shippingfee_impact']) ? $inputs_arr['matrix_shippingfee_impact'] : "";
						switch($data_arr['shipping_price_impact'])
						{
			    			case 'increase':
			    				$data_arr['shipping_price'] = $inputs_arr['matrix_shippingfee'];
			    				break;
			    			case 'decrease':
			    				$data_arr['shipping_price'] = -$inputs_arr['matrix_shippingfee'];
			    				break;
			    			default:
			    				$data_arr['shipping_price'] = 0;
			    		}
		    		}
			    	if($edit_all == 1 OR $edit_field == 'edit_stock')
		    			$data_arr['stock'] = $inputs_arr['matrix_stock'];
		    		if($edit_all == 1 OR $edit_field == 'edit_swap_image')
			    		$data_arr['swap_img_id'] = $inputs_arr['matrix_swap_img_id'];

					if($valid_matrix_update == 1)
		    		{
			    		$data_arr['product_id'] = $inputs_arr['product_id'];
			    		$data_arr['matrix_id'] = $inputs_arr['matrix_id'];
			    		$data_arr['description'] = isset($inputs_arr['matrix_desc']) ? $inputs_arr['matrix_desc'] : "";
			    		if($inputs_arr['mul_mat_edit'])
			    		{
			    			$edit_mat_arr = explode(',',$inputs_arr['matrix_id']);
			    			foreach($edit_mat_arr as $edit_mat_id)
			    			{
			    				$data_arr['matrix_id'] = $edit_mat_id;
			    				$this->variations_service->updateMatrixDetails($data_arr);
			    			}
						}
						else
						{

							$this->variations_service->updateMatrixDetails($data_arr);
						}
						$p_id = $inputs_arr['product_id'];
						// For generate headers.
						$head_label_arr = $this->variations_service->getItemVariationsGenerateHeaders($inputs_arr['product_id']);
						$d_arr['head_label_arr'] = $head_label_arr;
						$d_arr['head_label_str'] = $this->variations_service->convertHeaderLabelArrToStr($head_label_arr);
						ob_start();
						$d_arr['matrix_options_arr'] = array();

						$result_arr = $this->variations_service->populateItemVariations($inputs_arr['product_id']);
						if(isset($result_arr)) {
							$d_arr['matrix_options_arr'] = $result_arr['matrix_data_arr'];
							$d_arr['show_matrix_block'] = (count($d_arr['matrix_options_arr']) > 0) ? 1 : 0;
							$d_arr['matrix_edit_giftwrap'] = $result_arr['show_giftwrap'];
							$d_arr['matrix_edit_stock'] = $result_arr['show_stock'];
						}
						$d_arr['select_action'] = $this->variations_service->getSelectAction($d_arr);
						echo View::make('variations::admin.manageItemsAttribMatrix', compact('d_arr', 'p_id'));
			        	$resp_html = ob_get_contents();
						ob_end_clean();
			        	echo json_encode(array('status'=>'success', 'op_html'=>$resp_html));
			        }
			        else
			        {
						echo json_encode(array('status'=>'error', 'error_message' => $err_msg));
					}
				}
				exit;
				break;
		}
	}

	public function postAddSectionName()
	{
		if(Request::ajax())
		{
			$input_arr = Input::all();
			$product_user_id = CUtil::getUserId($input_arr['user_code']);
			$product = Products::initialize();
			$product->setProductUserId($product_user_id);
			$details = $product->addSection($input_arr['section_name']);
			$json_data = json_decode($details, true);

			if($json_data['status'] == 'error')
			{
				echo json_encode(array('status'=>'error', 'error_message' => $json_data['error_messages']));
				return ;
			}
			else {
				echo json_encode(array('status'=>'success', 'error_message'=>'', 'section_id' => $json_data['user_section_id'], 'section_name' => $input_arr['section_name']));
				return ;
			}
		}
	}

	public function getProductSubCategories()
	{
		if(Request::ajax())
		{
			$input_arr = Input::all();
			$category_id = $input_arr['category_id'];
			if(is_numeric($category_id) && $category_id > 0)
			{
				$sub_categories_arr = $this->productService->getSubCategoryList($category_id);
				$disp_result = '';
				if(count($sub_categories_arr) > 1)
				{
					$disp_result .= '<span id="span_'.$category_id.'"></span>';
					$disp_result .= '<div class="row">';
					$disp_result .= '<div class="col-md-8"><select id="sub_category_'.$category_id.'" name="sub_category_'.$category_id.'" class="form-control bs-select fn_subCat_'.$category_id.'" onchange="listSubCategories(\'sub_category_'.$category_id.'\', \''.$category_id.'\');">';
					foreach($sub_categories_arr AS $sub_category_id => $category_name)
					{
						$disp_result .= '<option value="'.$sub_category_id.'">'.$category_name.'</option>';
					}
					$disp_result .= '</select></div></div>';
				}
				else
				{
					$disp_result .= '';//<div class="fn_clsNoSubCategryFound note note-info" id="sub_category_'.$category_id.'" name="sub_category_'.$category_id.'">'.trans('product.product_no_subcategories').'</div>
				}
				echo $disp_result;
				echo '~~~';
				$cat_list = $this->productService->getAllTopLevelCategoryIds($category_id);
				echo $cat_list.'~~~';
			}
		}
	}

	public function postEdit()
	{
		$error_msg = '';
		if(Input::has('edit_product'))
		{

			$input_arr = Input::All();
			//echo "<pre>";print_r($input_arr);die;
			$tab = $input_arr['p'];
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$d_arr['user_id'] = $logged_user_id;
			$product_new = Products::initialize($input_arr['id']);
			$product_new->changeStatus('Draft');

			if($tab == 'basic')
			{
				$user_id = CUtil::getUserId($input_arr['user_code']);
				$is_downloadable_product = isset($input_arr['is_downloadable_product'])? $input_arr['is_downloadable_product']: 'No';
				$user_section_id = (isset($input_arr['user_section_id']) && is_numeric($input_arr['user_section_id'])) ? $input_arr['user_section_id']: 0;
				$product = Products::initialize($input_arr['id']);
				$product->setProductUserId($user_id);
				$product->setTitle($input_arr['product_name']);
				$product->setDescription($input_arr['product_description']);
				$product->setSupportContent($input_arr['product_support_content']);
				$product->setSummary($input_arr['product_highlight_text']);
				$product->setCategory($input_arr['my_category_id']);
				$product->setSection($user_section_id);
				$product->setDemoUrl($input_arr['demo_url']);
				$product->setDemoDetails($input_arr['demo_details']);
				$product->setProductTags($input_arr['product_tags']);
				$product->setIsDownloadableProduct($is_downloadable_product);
				if($is_downloadable_product == 'Yes')
				{
					$product->setShippingTemplate(0);
					$product->removePackageDetails($input_arr['id']);

					$product->setCancellationPolicyFileName('');
					$product->setCancellationPolicyFileType('');
					$product->setCancellationPolicyServerUrl('');
					$product->setCancellationPolicyText('');
					$product->setUseDefaultCancellation('No');
					$product->setUseCancellationPolicy('No');
				}
				$details = $product->save();
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					foreach($json_data['error_messages'] AS $err_msg)
					{
						$error_msg .= "<p>".$err_msg."</p>";
					}
				}
				else {
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				if($error_msg != '')
				{
					//return Redirect::to('admin/product/add')->with('error_message', trans('common.correct_errors'))->withInput();
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'price')
			{
				$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
				$product = Products::initialize($input_arr['id']);
				$product->setIsFreeProduct($is_free_product);
				$valid_price_inputs = true;
				if($is_free_product == 'No')
				{
					$price = $input_arr['price_0_0'];
					$discount_percentage = $input_arr['discount_percentage_0_0'];
					$discount = $input_arr['discount_0_0'];

					$data['id'] = 0;
					$product_price_groups[] = $data;
					$product_data['range_start'] = 1;
					$product_data['range_end'] = -1;
					$product_data['price'] = $price;
					$product_data['discount_percentage'] = $discount_percentage;
					$product_data['discount'] = $discount;
					$product_data['error'] = '';

					//Price details from user inputs
					$product_price_groups[0]['price_details'][] = $product_data;

					if ($price == '' || ($price != '' && $price <=  0)) {
		                //$error_msg = 'Enter valid "Price"';
		                $valid_price_inputs = false;
		            } else if ($discount_percentage != '' && $discount_percentage >  100) {
		                //$error_msg = 'Enter valid "Discount"';
		                $valid_price_inputs = false;
					} else if ($discount != '' && ($discount <=  0 || $price < $discount)) {
		                //$error_msg = '"Discounted Price" is not valid';
		                $valid_price_inputs = false;
					}

					if (!$valid_price_inputs) {
						$error_msg = 'Invalid price details';
						//return Redirect::to('product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
					}

					$product->setProductUserId($input_arr['product_user_id']);
					//$product->setProductPriceCurrency(Config::get('webshoppack.site_default_currency'));
					$product->setPurchasePrice($input_arr['purchase_price']);
					//$product->setProductPrice($input_arr['product_price']);
					//$product->setPriceAfterDiscount($input_arr['product_discount_price']);
					if (isset($input_arr['product_discount_fromdate']))
						$product->setDiscountPriceFromDate($input_arr['product_discount_fromdate']);
					if (isset($input_arr['product_discount_todate']))
						$product->setDiscountPriceToDate($input_arr['product_discount_todate']);
					if(isset($input_arr['global_transaction_fee_used']) && $input_arr['global_transaction_fee_used'] == 'Yes')
					{
						$global_transaction_fee_used = 'Yes';
					}
					else
					{
						$global_transaction_fee_used = 'No';
						$site_transaction_fee_percent = isset($input_arr['site_transaction_fee_percent'])? $input_arr['site_transaction_fee_percent']: 0;
						$site_transaction_fee = isset($input_arr['site_transaction_fee'])? $input_arr['site_transaction_fee']: 0;
						$product->setSiteTransactionFeePercent($site_transaction_fee_percent);
						$product->setSiteTransactionFee($site_transaction_fee);
					}
					$product->setGlobalTransactionFeeUsed($global_transaction_fee_used);

					//Variation Module start
					if(CUtil::allowVariation()) {
						$use_variation = 0;
						if(isset($input_arr['use_variation']) && $input_arr['use_variation'] == 'Yes') {
							$use_variation = 1;
						}
						$product->setUseVariation($use_variation);

						if(CUtil::allowGiftwrap()) {
							//Log::info('Giftwrap enabled ===========>');
							$accept_giftwrap = 0;
							$accept_giftwrap_message = 0;
							$giftwrap_type = 'single';
							$giftwrap_pricing = 0.00;
							if(isset($input_arr['accept_giftwrap']) && $input_arr['accept_giftwrap'] == 'Yes') {
								$accept_giftwrap = 1;
								if(isset($input_arr['accept_giftwrap_message']) && $input_arr['accept_giftwrap_message'] == 'Yes'){
									$accept_giftwrap_message = 1;
									//$accept_giftwrap_message = $input_arr['accept_giftwrap_message'];
								}
								$giftwrap_type = $input_arr['giftwrap_type'];
								$giftwrap_pricing = $input_arr['giftwrap_pricing'];
							}
							$product->setAcceptGiftwrape($accept_giftwrap);
							$product->setAcceptGiftwrapMessage($accept_giftwrap_message);
							$product->setGiftwrapType($giftwrap_type);
							$product->setGiftwrapPricing($giftwrap_pricing);
						}
					}
					//Variation Module end
				}
				$details = $product->save();
				//Update group price details
				if($is_free_product == 'No') {
					if ($valid_price_inputs) {
						$product->updateGroupPriceDetailsById($input_arr['id'], $product_price_groups);
					}
				} else {
					$product->deleteGroupPriceDetails($input_arr['id']);
					Webshoptaxation::ProductTaxations()->deleteProductAllTaxation($input_arr['id']);
				}
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					foreach($json_data['error_messages'] AS $err_msg)
					{
						$error_msg .= "<p>".$err_msg."</p>";
					}
				} else if($error_msg == ''){
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				if($error_msg != '')
				{
					//return Redirect::to('admin/product/add')->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'stocks')
			{
				//echo '<pre>';print_r($input_arr);'</pre>';exit;
				$prod_obj = Products::initialize($input_arr['id']);
				$p_id = $input_arr['id'];
				$china_country_id = 38;//country id for china in currency_exchange_rate tbl
				$pak_country_id = 153;//country id for china in currency_exchange_rate tbl
				$data_arr = array();
				$error_msg = '';
				//if(isset($input_arr['stock_country_id_china']) && $input_arr['stock_country_id_china'] == $china_country_id) {
					$data_arr['product_id'] = $p_id;
					//$data_arr['stock_country_id'] = $input_arr['stock_country_id_china'];
					$data_arr['quantity'] = $input_arr['quantity'];
					$data_arr['serial_numbers'] = $input_arr['serial_numbers'];
					//echo "<pre>";print_r($data_arr);echo "</pre>";exit;
					$response = $prod_obj->saveStocks($data_arr);

					$json_data = json_decode($response , true);
					if(isset($json_data['status']) && $json_data['status'] == 'error')
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
					}
				//}

				if($error_msg == '') {
						//$prod_obj->deleteProductStocksByProductCountry($p_id);//, $china_country_id


					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					//echo "new_tabl_key: ".$new_tab_key;exit;
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				if($error_msg != '')
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'meta')
			{
				$product = Products::initialize($input_arr['id']);
				$product->setProductUserId($input_arr['product_user_id']);
				$product->setMetaTitle($input_arr['meta_title']);
				$product->setMetaDescription($input_arr['meta_description']);
				$product->setMetaKeyword($input_arr['meta_keyword']);

				$details = $product->save();
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					foreach($json_data['error_messages'] AS $err_msg)
					{
						$error_msg .= "<p>".$err_msg."</p>";
					}
				}
				else
				{
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					//echo "new_tabl_key: ".$new_tab_key;exit;
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				if($error_msg != '')
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'shipping')
			{
				$inputs = Input::all();
				$product = Products::initialize($input_arr['id']);

				//Add package details
				$package_details = $product->addPackageDetails($inputs);
				$json_data = json_decode($package_details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					if(is_array($json_data['error_messages']))
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
					}
					else
						$error_msg .= "<p>".$json_data['error_messages']."</p>";
				}

				//Add shipping template
				if(isset($input_arr['shipping_template']) && $input_arr['shipping_template']!='')
					$product->setShippingTemplate($input_arr['shipping_template']);
				if(isset($input_arr['shipping_from_country']) && $input_arr['shipping_from_country']!='')
					$product->setShippingFromCountry($input_arr['shipping_from_country']);
				if(isset($input_arr['shipping_from_zip_code']) && $input_arr['shipping_from_zip_code']!='')
					$product->setShippingFromZipCode($input_arr['shipping_from_zip_code']);
				$details = $product->save();
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					if(is_array($json_data['error_messages']))
					{
						foreach($json_data['error_messages'] AS $err_msg)
						{
							$error_msg .= "<p>".$err_msg."</p>";
						}
					}
					else
						$error_msg .= "<p>".$json_data['error_messages']."</p>";
				}

				if($error_msg != '')
				{
					//return Redirect::to('admin/product/add')->with('error_message', trans('common.correct_errors'))->withInput();
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
				else {
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
			}
			elseif($tab == 'tax')
			{
				$inputs = Input::all();
				try
				{
					if($inputs['edit_product'] == 'add_tax')
					{
						$inputs = array(
							'taxation_id' 	=> $inputs['taxation_id'],
							'product_id' 	=> $input_arr['id'],
							'tax_fee' 		=> $inputs['tax_fee'],
							'fee_type'		=> $inputs['fee_type'],
						);

						$taxatonid = Webshoptaxation::ProductTaxations()->addProductTaxation($inputs);
						if($taxatonid)
						{
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('success_message', 'Tax have been added successfully');
						}
						else
						{
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', 'Some problem in adding taxation fee')->withInput();
						}
					}
					else
					{
						$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
						if($new_tab_key == '')
						{
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
						}
						else
						{
							//To redirect to next tab..
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
						}
					}
				}
				catch(Exception $e)
				{
					$error_msg = $e->getMessage();
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'attribute')
			{
				$validator_arr = $this->productAddService->getproductValidation($input_arr, $input_arr['id'], $input_arr['p'], 'edit');
				$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
				if($validator->passes())
				{
					$product = Products::initialize($input_arr['id']);
					$product->removeProductCategoryAttribute();
					$return_data = $this->productService->addProductCategoryAttribute($input_arr);
					if($return_data != '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $return_data)->withInput();
					}
					else
					{
						$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
						if($new_tab_key == '')
						{
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
						}
						else
						{
							//To redirect to next tab..
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
						}
					}
				}
				else
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
				}
			}
			elseif($tab == 'preview_files')
			{
				$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
				if($new_tab_key == '')
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
				}
				else
				{
					//To redirect to next tab..
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
				}
			}
			elseif ($tab == 'variations')
			{
				$redirectKey = 'status';
				if(isset($this->variations_service))
				{
					$insert_arr = array();
					$insert_arr['product_id'] = $input_arr['id'];
					if(isset($input_arr['prd_var_grp']) && isset($input_arr['variation_ids']) && isset($input_arr['attrib_ids']))
					{
						$insert_arr['variation_group_id'] = $input_arr['prd_var_grp'];
						$insert_arr['var_id_arr'] = array_unique($input_arr['variation_ids']);
						$insert_arr['attr_id_arr'] = array_unique($input_arr['attrib_ids']);

						if(isset($insert_arr['var_id_arr']) && COUNT($insert_arr['var_id_arr']) > 0 &&
							isset($insert_arr['attr_id_arr']) && COUNT($insert_arr['attr_id_arr']) > 0 )
						{
							$this->variations_service->updateItemVariationAttribute($insert_arr);
						}
						$redirectKey = 'variations';
					}
					else
					{
						if($this->variations_service->chkIsDefaultMatrixExist($input_arr['id']))
						{
							$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
							$redirectKey = ($new_tab_key == '') ? 'status' : $new_tab_key;
						}
						else
						{
							$errMsg = Lang::get('variations::variations.deafult_variation_none_err');
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=variations')->with('error_message', $errMsg);
						}
					}
				}
				//To redirect to next tab..
				return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$redirectKey);
			}
			elseif($tab == 'download_files')
			{
				$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
				if($new_tab_key == '')
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
				}
				else
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
				}
			}
			elseif($tab == 'cancellation_policy')
			{
				$p_id = $input_arr['id'];
				$product = Products::initialize($input_arr['id']);
				$product->setUseCancellationPolicy($input_arr['use_cancellation_policy']);
				if($input_arr['use_cancellation_policy'] == 'No')
				{
					$product->setCancellationPolicyFileName('');
					$product->setCancellationPolicyFileType('');
					$product->setCancellationPolicyServerUrl('');
					$product->setCancellationPolicyText('');
					$product->setUseDefaultCancellation('No');
				}
				else
				{
					if(isset($input_arr['use_default_cancellation']) && $input_arr['use_default_cancellation'] == 'Yes')
					{
						$product->setUseDefaultCancellation('Yes');
						$product->setCancellationPolicyText('');
						$product->setCancellationPolicyFilename('');
						$product->setCancellationPolicyFiletype('');
						$product->setCancellationPolicyServerUrl('');
						$this->productAddService->deleteProductCancellationPolicyFile($p_id);
					}
					else
					{
						$rules = array();
						$p_details = $product->getProductDetails();
						if(!isset($p_details['cancellation_policy_filename']) || (isset($p_details['cancellation_policy_filename']) && $p_details['cancellation_policy_filename']==''))
						{
							$rules = array(
								'cancellation_policy_text' => 'required_without:shop_cancellation_policy_file',
								'shop_cancellation_policy_file' => 'required_without:cancellation_policy_text|mimes:'.str_replace(' ', '', Config::get("webshoppack.shop_cancellation_policy_allowed_extensions")).'|max:'.Config::get("webshoppack.shop_cancellation_policy_allowed_file_size"),
							);
						}
						$message = array(
							'shop_cancellation_policy_file.mimes' => trans('common.uploader_allow_format_err_msg'),
							'shop_cancellation_policy_file.max' => trans('common.uploader_max_file_size_err_msg'),
							'required_without' => trans('admin/cancellationpolicy.either_cancellation_text_or_file_required')
						);

						$v = Validator::make($input_arr, $rules, $message);
						if ($v->fails())
						{
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->withErrors($v)->withInput();
						}
						else
						{
							if (Input::hasFile('shop_cancellation_policy_file'))
							{
								$file = Input::file('shop_cancellation_policy_file');
								$file_ext = $file->getClientOriginalExtension();
								$file_name = Str::random(20);
								$destinationpath = URL::asset(Config::get("webshoppack.product_cancellation_policy_folder"));
								$file_arr = $this->productAddService->updateCancellationPolicyFile($file, $file_ext, $file_name, $destinationpath, $p_id);

								$product->setCancellationPolicyFileName($file_arr['file_name']);
								$product->setCancellationPolicyFileType($file_arr['file_ext']);
								$product->setCancellationPolicyServerUrl($file_arr['file_server_url']);
								$product->setUseDefaultCancellation('No');
								$product->setCancellationPolicyText('');

							}
							elseif(Input::has('cancellation_policy_text'))
							{
								$product->setCancellationPolicyText(Input::get('cancellation_policy_text'));
								$product->setCancellationPolicyFilename('');
								$product->setCancellationPolicyFiletype('');
								$product->setCancellationPolicyServerUrl('');
								$product->setUseDefaultCancellation('No');
								$this->productAddService->deleteProductCancellationPolicyFile($p_id);
							}
						}
					}
				}
				$details = $product->save();
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					foreach($json_data['error_messages'] AS $err_msg)
					{
						$error_msg .= "<p>".$err_msg."</p>";
					}
				}
				else
				{
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					if($new_tab_key == '')
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=publish');
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				if($error_msg != '')
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}
			elseif($tab == 'status')
			{
				//$user_id = CUtil::getUserId($input_arr['user_code']);
				$product = Products::initialize($input_arr['id']);
				$product->setProductUserId($input_arr['product_user_id']);
				$product_notes = isset($input_arr['product_notes']) ? $input_arr['product_notes'] : '';
				if($product_notes != '')
				{
					$note_arr = array('product_id' => $input_arr['id'], 'comment' => $input_arr['product_notes']);
					$this->productService->addProductStatusComment($note_arr);
				}
				//$product->setDeliveryDays($input_arr['delivery_days']);

				if($input_arr['product_status'] == 'Ok' && $input_arr['date_expires'] == '0000-00-00 00:00:00') {
					$number_of_days = Config::get('products.product_listing_days');
					if($number_of_days > 0)
						$date = date('Y-m-d', strtotime("+".$number_of_days." days"));
					else
						$date = '9999-12-31 00:00:00';
					$product->setDateExpires($date);
				}

				$details = $product->save();
				$json_data = json_decode($details , true);
				if(isset($json_data['status']) && $json_data['status'] == 'error')
				{
					foreach($json_data['error_messages'] AS $err_msg)
					{
						$error_msg .= "<p>".$err_msg."</p>";
					}
				}
				else {
					if($input_arr['product_status'] == 'Ok') {
						$details = $product->publish();
						$json_data = json_decode($details, true);
					}
					else {
						$json_data['status'] == 'success';
					}
					if($json_data['status'] == 'error')
					{
						if(is_array($json_data['error_messages']))
						{
							foreach($json_data['error_messages'] AS $err_msg)
							{
								$error_msg .= "<p>".$err_msg."</p>";
							}
						}
						else
						{
							$error_msg .= "<p>".$json_data['error_messages']."</p>";
						}
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
					}
					else
					{
						//To update status
						$product->changeStatus($input_arr['product_status']);
						$product->updateUserTotalProducts($input_arr['product_user_id']);
						if($input_arr['edit_product'] != '')
						{
							if($logged_user_id != $input_arr['product_user_id']) {
								if($input_arr['product_status'] == 'Ok' || $input_arr['product_status'] == 'ToActivate' || $input_arr['product_status'] == 'NotApproved'){
									$this->productService->sendProductMailToUserAndAdmin($input_arr['id'], $product_notes);
								}
							}
						}

						$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
						if($new_tab_key == '')
						{
							return Redirect::to('admin/product/list')->with('success_message', 'Product updated successfully!!!');
						}
						else
						{
							//To redirect to next tab..
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
						}
					}
				}
				if($error_msg != '')
				{
					//return Redirect::to('admin/product/add')->with('error_message', trans('common.correct_errors'))->withInput();
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$input_arr['p'])->with('error_message', $error_msg)->withInput();
				}
			}

			//Do the manual validation for download file
			/*if(isset($input_arr['p']) && $input_arr['p'] == 'download_files')
			{
				if($this->productService->validateDownloadTab($input_arr['id']))
				{
					$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
					if($new_tab_key == '')
					{
						//If all the completed, then redirect to product list page
						$msg = (empty($this->productAddService->alert_message))? '' : trans('product.'.$this->productAddService->alert_message);
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status')->with('success_message', $msg);
					}
					else
					{
						//To redirect to next tab..
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key);
					}
				}
				else
				{
					return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=download_files')->with('error_message', trans('common.correct_errors'));
				}
			}
			else
			{
				$validator_arr = $this->productAddService->getproductValidation($input_arr, $input_arr['id'], $input_arr['p'], 'edit');
				$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
				if($validator->passes())
				{
					$input_arr['product_preview_type'] = 'image'; //Make default preview type is image now, We will get preview type from user in future..
					$update_product_arr = $this->productAddService->updateProduct($input_arr, $input_arr['p']);
					$validate_tab_arr = $update_product_arr['validate_tab_arr'];
					if($update_product_arr['status'])
					{
						$new_tab_key =  $this->productAddService->getNewTabKey($input_arr['p'], $input_arr['id']);
						if($new_tab_key == '')
						{
							//If all the completed, then redirect to product list page
							$msg = (empty($this->productAddService->alert_message))? '' : trans('product.'.$this->productAddService->alert_message);
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status')->with('success_message', $msg)->with('validate_tab_arr', $validate_tab_arr);
						}
						else
						{
							//To redirect to next tab..
							return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p='.$new_tab_key)->with('validate_tab_arr', $validate_tab_arr);
						}
					}
					else
					{
						return Redirect::to('admin/product/add?id='.$input_arr['id'].'&p=status')->with('validate_tab_arr', $validate_tab_arr);
					}
				}
				else
				{
					return Redirect::to('admin/product/add')->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
				}
			}*/
		}
		return Redirect::to('admin/product/add');
	}

	public function postShippingCostEstimate()
	{
		$d_arr = array();
		$inputs = Input::all();

		$template_id = Input::get('template_id');
		$shipping_country = Input::get('shipping_country');
		$shipping_from_country = Input::get('shipping_from_country');
		$shipping_from_zip_code = Input::get('shipping_from_zip_code');
		$shipping_template_service = new ShippingTemplateService();
		$product_id = Input::get('product_id');
		$package_details = array();
		$package_details['weight'] = Input::has('weight')?Input::get('weight'):0;
		$package_details['length'] = Input::has('length')?Input::get('length'):0;
		$package_details['width'] = Input::has('width')?Input::get('width'):0;
		$package_details['height'] = Input::has('height')?Input::get('height'):0;
		$package_details['custom'] = Input::has('custom')?Input::get('custom'):'No';
		$package_details['first_qty'] = Input::has('custom')?Input::get('first_qty'):'No';
		$package_details['additional_qty'] = Input::has('additional_qty')?Input::get('additional_qty'):0;
		$package_details['additional_weight'] = Input::has('additional_weight')?Input::get('additional_weight'):0;


		$d_arr['shipping_companies_list'] = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($template_id, 0, $product_id, 1, array('country_id' => $shipping_country), $package_details, '', array('country_id' =>$shipping_from_country, 'zip_code' => $shipping_from_zip_code));
		return View::make('admin/referenceShippingCost', compact('d_arr'));
	}

	public function getProductActions()
	{
		$action = Input::get('action');
		$p_id = Input::get('product_id');
		switch($action)
		{
			case 'download_file':
				$this->productAddService->downloadProductResouceFile($p_id, true);
				exit;
				break;
		}
	}
}