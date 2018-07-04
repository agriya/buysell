<?php
class AdminShippingTemplateController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }

	public function getIndex()
    {
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$shippingTemplateService = new ShippingTemplateService();
		$shippingTemplateService->setShippingSrchArr(Input::All());
		$q = $shippingTemplateService->buildShippingTemplatesQuery($logged_user_id);
		$shipping_templates = $q->paginate(5);
		//$shippingTemplateService->getDeleteTemplate(4);
		$this->header->setMetaTitle(trans('meta.manage_shipping_templates'));
		return View::make('admin.shippingTemplates', compact('shipping_templates'));
    }

    public function getAdd()
    {
    	$shippingTemplateService = new ShippingTemplateService();
		$shipping_template_details = array();
		$checked_companies = array();
    	$post_service_companies = $shippingTemplateService->getShippingCompaniesList(1);
		$express_companies = $shippingTemplateService->getShippingCompaniesList(2);
		$special_line_companies = $shippingTemplateService->getShippingCompaniesList(3);
		$other_companies = $shippingTemplateService->getShippingCompaniesList(4);

		$default_tab=1;
		if(count($post_service_companies) > 0)
			$default_tab=1;
		elseif(count($express_companies) > 0)
			$default_tab=2;
		elseif(count($special_line_companies) > 0)
			$default_tab=3;
		elseif(count($other_companies) > 0)
			$default_tab=4;

		$this->header->setMetaTitle(trans('meta.add_shipping_template'));
		$err_tab = (Session::has('err_tab') && Session::get('err_tab') != '')?Session::get('err_tab'):$default_tab;
        Session::forget('err_tab');
        $err_company = (Session::has('err_company') && Session::get('err_company') != '')?Session::get('err_company'):'';
        Session::forget('err_company');

		$err_delivery_company = (Session::has('err_delivery_company') && Session::get('err_delivery_company') != '')?Session::get('err_delivery_company'):'';
        Session::forget('err_delivery_company');
        if($err_company!='' || $err_delivery_company!='')
        {
	      	$checked_companies = ($err_company!='')?array($err_company):array($err_delivery_company);
        	if($err_company!='')
        		$shipping_template_details['fee_type_'.$err_company] = '1';
        	if($err_delivery_company!='')
        		$shipping_template_details['delivery_type_'.$err_delivery_company] = '1';
		}
		$id=0;
		return View::make('admin.addShippingTemplate', compact('id','shipping_template_details', 'checked_companies', 'post_service_companies', 'express_companies', 'special_line_companies', 'other_companies','err_tab','err_company','err_delivery_company'));
	}

	public function getEdit($id = '')
	{
		if($id=='')
			return Redirect::action('AdminShippingTemplateController@getIndex')->with('error','Select valid shipping template');

		$shippingTemplateService = new ShippingTemplateService();
		$checked_companies = array();
		$shipping_template_details = array();
		$post_service_companies = $shippingTemplateService->getShippingCompaniesList(1);
		$express_companies = $shippingTemplateService->getShippingCompaniesList(2);
		$special_line_companies = $shippingTemplateService->getShippingCompaniesList(3);
		$other_companies = $shippingTemplateService->getShippingCompaniesList(4);

		$default_tab=1;
		if(count($post_service_companies) > 0)
			$default_tab=1;
		elseif(count($express_companies) > 0)
			$default_tab=2;
		elseif(count($special_line_companies) > 0)
			$default_tab=3;
		elseif(count($other_companies) > 0)
			$default_tab=4;

		$shipping_template_det = $shippingTemplateService->getShippingTemplateDetails($id);
		if(!empty($shipping_template_det) && isset($shipping_template_det['shipping_template']) &&	count($shipping_template_det['shipping_template']) > 0)
		{
			$shipping_template = $shipping_template_det['shipping_template'];
			if(count($shipping_template) > 0)
			{
				$shipping_template_details['template_name'] = $shipping_template->template_name;
			}
			$shipping_template_companies = $shipping_template_det['shipping_temp_comp'];
			if(count($shipping_template_companies) > 0)
			{
				foreach($shipping_template_companies as $company)
				{
					$comp_id = $company->company_id;
					$checked_companies[] = $comp_id;

					$fee_type_name = 'fee_type_'.$comp_id;
			        $discount_name = 'discount_'.$comp_id;
			        $delivery_type_name = 'delivery_type_'.$comp_id;
			        $delivery_days_name = 'delivery_days_'.$comp_id;

					$shipping_template_details[$fee_type_name] = $company->fee_type;
					$shipping_template_details[$discount_name] = $company->fee_discount;
					$shipping_template_details[$delivery_type_name] = $company->delivery_type;
					$shipping_template_details[$delivery_days_name] = $company->days;

				}
			}
		}
		else
		{
			return Redirect::action('AdminShippingTemplateController@getIndex')->with('error','Select valid shipping template');
		}
		$this->header->setMetaTitle(trans('meta.edit_shipping_template'));

		$err_tab = (Session::has('err_tab') && Session::get('err_tab') != '')?Session::get('err_tab'):$default_tab;
        Session::forget('err_tab');

        $err_company = (Session::has('err_company') && Session::get('err_company') != '')?Session::get('err_company'):'';
        Session::forget('err_company');

        $err_delivery_company = (Session::has('err_delivery_company') && Session::get('err_delivery_company') != '')?Session::get('err_delivery_company'):'';
        Session::forget('err_delivery_company');

		if($err_company!='' || $err_delivery_company!='')
        {
        	$err_checked_companies = ($err_company!='')?array($err_company):array($err_delivery_company);
	      	$checked_companies = array_merge($checked_companies,$err_checked_companies);
        	if($err_company!='')
        		$shipping_template_details['fee_type_'.$err_company] = '1';
        	if($err_delivery_company!='')
        		$shipping_template_details['delivery_type_'.$err_delivery_company] = '1';
		}
		return View::make('admin.addShippingTemplate', compact('id', 'checked_companies', 'shipping_template_details', 'post_service_companies', 'express_companies', 'special_line_companies', 'other_companies', 'err_tab', 'err_company','err_delivery_company'));
	}

	public function postEdit($id='')
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			if($id=='')
				return Redirect::action('AdminShippingTemplateController@getIndex')->with('error','Select valid shipping template');

			$logged_user_id = BasicCUtil::getLoggedUserId();
			$inputs = Input::all();
			$rules = array('template_name' => 'required|unique:shipping_templates,template_name,'.$id.',id,user_id,'.$logged_user_id,'companies' => 'required|array');
			$messges = array('template_name.required'=>'Shipping Template Name is Required',
								'companies.required' => 'Kindly select shipping companies');

			$validator = Validator::make($inputs,$rules,$messges);
			if($validator->passes())
			{
				$companies = Input::get('companies');
				$another_rules = array();
				$another_messages = array();
				foreach($companies as $company)
				{
					$fee_type_name = 'fee_type_'.$company;
					$delivery_type_name = 'delivery_type_'.$company;
					$delivery_days_name = 'delivery_days_'.$company;

					$another_rules[$fee_type_name] = array('required');
					$another_rules[$delivery_type_name] = array('required');
					$another_rules[$delivery_days_name] = array('required_if:'.$delivery_type_name.',2');

				}
				$validator1 = Validator::make($inputs,$another_rules,array('required' => 'Required', 'required_if' => 'Provide the promissed time'));
				if($validator1->passes())
				{
					$template_id = $id;
					$shippingTemplateService = new ShippingTemplateService();
					foreach($companies as $company)
					{
						$fee_type = $inputs['fee_type_'.$company];
						if($fee_type == 1)
						{
							$check_is_custom_avail = $shippingTemplateService->checkIsCustomShippingAvailable($template_id,$company);
							if($check_is_custom_avail <= 0)
							{
								$company_details = $shippingTemplateService->getShippingCompanyDetails($company);
								$company_name = isset($company_details['company_name'])?$company_details['company_name']:'';
								$err_tab = isset($company_details['category'])?$company_details['category']:'';
								return Redirect::action('AdminShippingTemplateController@getEdit',$id)->withInput()->with('error','Specify countries for the custom fee type for '.$company_name)->with('err_tab',$err_tab)->with('err_company',$company);
							}
						}
						$delivery_type = $inputs['delivery_type_'.$company];
						if($delivery_type == 1)
						{
							$check_is_delivery_avail = $shippingTemplateService->checkIsCustomDeliveryAvailable($template_id,$company);
							if($check_is_delivery_avail <= 0)
							{
								$company_details = $shippingTemplateService->getShippingCompanyDetails($company);
								$company_name = isset($company_details['company_name'])?$company_details['company_name']:'';
								$err_tab = isset($company_details['category'])?$company_details['category']:'';
								return Redirect::action('AdminShippingTemplateController@getEdit',$id)->withInput()->with('error','Specify countries for the custom delivery type for '.$company_name)->with('err_tab',$err_tab)->with('err_delivery_company',$company);
							}
						}
					}

					$template_name  = Input::get('template_name');
					$temp_det = array();
					$temp_det['template_name'] = $template_name;
					$temp_det['user_id'] = $logged_user_id;
					$shippingTemplateService->updateShippingTemplate($id, $temp_det);


					//$shippingTemplateService->removeShippingTemplateCompanies($id);
					$current_comp_ids = array();
					$previous_comp_ids = array();
					$existing_temp_comp_list = $shippingTemplateService->getShippingTemplatesCompaniesList($id);
					if(count($existing_temp_comp_list) > 0) {
						foreach($existing_temp_comp_list as $key => $val) {
							$previous_comp_ids[] = $val->company_id;
						}
					}

					foreach($companies as $company)
					{
						$temp_comp = array();
						$fee_type = $inputs['fee_type_'.$company];
	                    $discount = isset($inputs['discount_'.$company])?$inputs['discount_'.$company]:0;
	                    $delivery_type = $inputs['delivery_type_'.$company];
	                    $delivery_days = $inputs['delivery_days_'.$company];

						$discount = ($fee_type == 2) ? $discount : 0;
						$delivery_days = ($delivery_type == 2) ? $delivery_days : 0;

						$temp_comp['template_id'] = $template_id;
						$temp_comp['company_id'] = $company;
						$temp_comp['fee_type'] = $fee_type;
						$temp_comp['fee_discount'] = $discount;
						$temp_comp['delivery_type'] = $delivery_type;
						$temp_comp['days'] = $delivery_days;

						//$temp_comp
						$temp_comp_details = $shippingTemplateService->chkShippingTemplateCompanyExistsByTemplate($id, $company);
						if(count($temp_comp_details) > 0) {
							Log::info('company => '.$company.' fee_type ==> '.$fee_type.' delivery_type ==> '.$delivery_type );
							$temp_comp_res = $shippingTemplateService->updateShippingTemplateCompany($temp_comp_details['id'], $temp_comp);
							if($fee_type == 2 || $fee_type == 3)
								$shippingTemplateService->deleteFeeCustomRelatedTables($id, $company);
							if($delivery_type == 2)
								$shippingTemplateService->deleteDeliveryCustomRelatedTables($id, $company);
						}
						else
							$temp_comp_res = $shippingTemplateService->addShippingTemplateCompany($temp_comp);

						$current_comp_ids[] = (int)$company;
					}
					Log::info(print_r($previous_comp_ids, 1));
					Log::info(print_r($current_comp_ids, 1));
					$remove_comp_ids = array_diff($previous_comp_ids, $current_comp_ids);
					Log::info(print_r($remove_comp_ids, 1));
					if(count($remove_comp_ids > 0)) {
						foreach($remove_comp_ids as $comp_id) {
							$shippingTemplateService->deleteFeeCustomRelatedTables($id, $comp_id);
							$shippingTemplateService->deleteDeliveryCustomRelatedTables($id, $comp_id);
							$shippingTemplateService->deleteShippingTemplateCompany($id, $comp_id);
						}
					}

					return Redirect::action('AdminShippingTemplateController@getIndex')->with('success','Shipping Template Updated Successfully');
				}
				else
				{
					return Redirect::action('AdminShippingTemplateController@getEdit',$id)->withInput()->withErrors($validator1);
				}
			}
			else
			{
				return Redirect::action('AdminShippingTemplateController@getEdit',$id)->withInput()->withErrors($validator);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminShippingTemplateController@getEdit',$id)->with('error_message',$errMsg);
		}
	}

	public function postAdd()
	{
		if(!BasicCUtil::checkIsDemoSite()) {
			$logged_user_id = BasicCUtil::getLoggedUserId();
			$inputs = Input::all();
			$rules = array('template_name' => 'required|unique:shipping_templates,template_name,NULL,id,user_id,'.$logged_user_id,'companies' => 'required|array');
			$messges = array('template_name.required'=>'Shipping Template Name is Required',
								'companies.required' => 'Kindly select shipping companies');

			$validator = Validator::make($inputs,$rules,$messges);
			if($validator->passes())
			{
				$companies = Input::get('companies');
				$another_rules = array();
				$another_messages = array();
				foreach($companies as $company)
				{
					$fee_type_name = 'fee_type_'.$company;
					$delivery_type_name = 'delivery_type_'.$company;
					$delivery_days_name = 'delivery_days_'.$company;

					$another_rules[$fee_type_name] = array('required');
					$another_rules[$delivery_type_name] = array('required');
					$another_rules[$delivery_days_name] = array('required_if:'.$delivery_type_name.',2');

				}
				$validator1 = Validator::make($inputs,$another_rules,array('required' => 'Required', 'required_if' => 'Provide the promissed time'));
				if($validator1->passes())
				{
					$shippingTemplateService = new ShippingTemplateService();

					foreach($companies as $company)
					{
						$fee_type = $inputs['fee_type_'.$company];
						if($fee_type == 1)
						{
							$company_details = $shippingTemplateService->getShippingCompanyDetails($company);
							$company_name = isset($company_details['company_name'])?$company_details['company_name']:'';
							$err_tab = isset($company_details['category'])?$company_details['category']:'';
							return Redirect::action('AdminShippingTemplateController@getAdd')->withInput()->with('error','Specify countries for the custom fee type for '.$company_name)->with('err_tab',$err_tab)->with('err_company',$company);
						}
						$delivery_type = $inputs['delivery_type_'.$company];
						if($delivery_type == 1)
						{
							$company_details = $shippingTemplateService->getShippingCompanyDetails($company);
							$company_name = isset($company_details['company_name'])?$company_details['company_name']:'';
							$err_tab = isset($company_details['category'])?$company_details['category']:'';
							return Redirect::action('AdminShippingTemplateController@getAdd')->withInput()->with('error','Specify countries for the custom delivery type for '.$company_name)->with('err_tab',$err_tab)->with('err_delivery_company',$company);
						}
					}
					$template_name  = Input::get('template_name');
					$temp_det = array();
					$temp_det['template_name'] = $template_name;
					$temp_det['user_id'] = $logged_user_id;
					$template_id = $shippingTemplateService->addShippingTemplate($temp_det);

					foreach($companies as $company)
					{
						$temp_comp = array();
						$fee_type = $inputs['fee_type_'.$company];
	                    $discount = isset($inputs['discount_'.$company])?$inputs['discount_'.$company]:'0';
	                    $delivery_type = $inputs['delivery_type_'.$company];
	                    $delivery_days = $inputs['delivery_days_'.$company];

						$temp_comp['template_id'] = $template_id;
						$temp_comp['company_id'] = $company;
						$temp_comp['fee_type'] = $fee_type;
						$temp_comp['fee_discount'] = $discount;
						$temp_comp['delivery_type'] = $delivery_type;
						$temp_comp['days'] = $delivery_days;

						$temp_comp = $shippingTemplateService->addShippingTemplateCompany($temp_comp);
					}
					return Redirect::action('AdminShippingTemplateController@getIndex')->with('success','Shipping Template Added Successfully');
				} else {
					return Redirect::action('AdminShippingTemplateController@getAdd')->withInput()->withErrors($validator1);
				}
			} else {
				return Redirect::action('AdminShippingTemplateController@getAdd')->withInput()->withErrors($validator);
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::action('AdminShippingTemplateController@getAdd')->with('error_message',$errMsg);
		}
	}

	public function getCustomShippingTemplate($id='',$company_id='')
	{
		$shippingTemplateService = new ShippingTemplateService();
		$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
		$temp_comp_name = $shippingTemplateService->getShippingTemplateCompanyName($company_id);
		$temp_name = $shippingTemplateService->getShippingTemplateName($id);
		//echo "temp_name: ".$temp_name;
		$shipping_custom_details = array();
		$prev_selected_countries = array();
		$other_countries_details = array();
		if($temp_comp_id!='' && $temp_comp_id >0)
		{
			$shipping_custom_details = $shippingTemplateService->getShippingTemplateFeeCustomDetails($temp_comp_id);
			$other_countries_details = $shippingTemplateService->getShippingTemplateFeeCustomOtherCountries($temp_comp_id);
			$prev_selected_countries = $shippingTemplateService->getSelectedTemplateCustomCountries($temp_comp_id);
		}

		$geo_countries_arr = array();
		$geo_countries_list = $shippingTemplateService->getCountryListId('geo_location_id', $company_id);
		foreach($geo_countries_list as $country)
		{
			$geo_countries_arr[$country->geo_location_id][] = $country;
		}

		$zone_contries_list = $shippingTemplateService->getCountryListId('zone_id', $company_id);
		foreach($zone_contries_list as $country)
		{
			$zone_contries_arr[$country->zone_id][] = $country;
		}
		$is_redirect = 0;
		$action_url = '';
		$is_close = 0;
		$validation_errors = (Session::has('validation_errors'))?Session::get('validation_errors'):array();
		Session::forget('validation_errors');
		$is_redirect = (Session::has('is_redirect'))?Session::get('is_redirect'):0;
		Session::forget('is_redirect');
		$is_close = (Session::has('is_close'))?Session::get('is_close'):0;
		Session::forget('is_close');
		if($is_redirect == 1)
		{
			$action_url = Url::action('AdminShippingTemplateController@getEdit',array($id));
		}

		$is_custom_only_company = CUtil::checkIsCustomOnlyCompany($company_id);
		$fee_type_arr = array('2' => 'Standard', '1' => 'Custom', '3' => 'Free');
		if($is_custom_only_company)
			$fee_type_arr = array_except($fee_type_arr, array('2'));//Remove standard

		return View::make('admin.customShippingTemplate', compact('id', 'company_id', 'geo_countries_arr', 'zone_contries_arr', 'shipping_custom_details', 'prev_selected_countries', 'other_countries_details', 'is_redirect', 'validation_errors', 'action_url', 'is_close', 'temp_name', 'is_custom_only_company', 'fee_type_arr','temp_comp_name'));
	}
	public function postCustomShippingTemplate($id='',$company_id='')
	{
		$inputs = Input::all();
		$t_name = $inputs['template_name'];
		//print_r($t_name);exit;
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$shippingTemplateService = new ShippingTemplateService();
		$is_custom_only_company = CUtil::checkIsCustomOnlyCompany($company_id);
		$do_redirect = false;
		if($id <= 0)
		{
			$do_redirect = true;
				$count = DB::table('shipping_templates')->where('template_name', $t_name)->count();
				if($t_name !='' )
					if($count == 0)
						$template_name = $t_name;
					else
						$template_name = $t_name.'_1';
				else
					$template_name  = 'Untitled '.rand();
				$temp_det = array();
				$temp_det['template_name'] = $template_name;
				$temp_det['user_id'] = $logged_user_id;
				$template_id = $shippingTemplateService->addShippingTemplate($temp_det);
				$id = $template_id;

				$temp_comp = array();
				$temp_comp['template_id'] = $template_id;
				$temp_comp['company_id'] = $company_id;
				$temp_comp['fee_type'] = 1;
				$temp_comp['fee_discount'] = 0;
				$temp_comp['delivery_type'] = 2;
				$temp_comp['days'] = 0;
				$temp_comp = $shippingTemplateService->addShippingTemplateCompany($temp_comp);
		}

		$url = Url::action('AdminShippingTemplateController@getEdit',array($id));
		if($id == '' || $company_id == '')
		{
			if(!$do_redirect) $url = '';
			return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'redirect' => $url));
		}


		$fee_custom_id = 0;
		if(Input::has('fee_custom_id'))
			$fee_custom_id = Input::get('fee_custom_id');

		$inputs = Input::all();


		if(isset($inputs['fee_type']))
		{
			if($inputs['fee_type']!='1')
			{
				$inputs['custom_fee_type'] = '';
			}
			if($inputs['fee_type']!='2')
			{
				$inputs['discount'] = '0';
			}
		}
		if(isset($inputs['shipping_setting']) && $inputs['shipping_setting']!='ship_to')
		{
			$inputs['min_order'] = '';
			$inputs['max_order'] = '';
			$inputs['cost_base_weight'] = '';
			$inputs['extra_units'] = '';
			$inputs['extra_costs'] = '';
			$inputs['initial_weight'] = '';
			$inputs['initial_weight_price'] = '';
			$inputs['fee_type'] = '';
			$inputs['custom_fee_type'] = '';
		}
		if(isset($inputs['custom_fee_type']) && $inputs['custom_fee_type']!='1')
		{
			$inputs['min_order'] = '';
			$inputs['max_order'] = '';
			$inputs['cost_base_weight'] = '';
			$inputs['extra_units'] = '';
			$inputs['extra_costs'] = '';
		}
		if(isset($inputs['custom_fee_type']) && $inputs['custom_fee_type']!='2')
		{
			$inputs['initial_weight'] = '';
			$inputs['initial_weight_price'] = '';
		}
		$new_group = (isset($inputs['new_group']) && $inputs['new_group'] == 1)?1:0;
		if($new_group == 1)
		{
			$rules = array('geo_location' => 'required_without:zone',
						'zone' => 'required_without:geo_location',
						'geo_countries' => 'required_with:geo_location',
						'zone_countries' => 'required_with:zone',
						'fee_type' => 'required_if:shipping_to,1',
						'discount' => 'sometimes|required_if:fee_type,2|numeric|min:0|max:99',
						'custom_fee_type' => 'required_if:fee_type,1',
						'min_order' => 'required_if:custom_fee_type,1|numeric',
						'max_order' => 'required_if:custom_fee_type,1|numeric',
						'cost_base_weight' => 'required_if:custom_fee_type,1|numeric',
						'extra_units' => 'required_with:extra_costs|numeric|min:1', //required_if:custom_fee_type,1|
						'extra_costs' => 'required_with:extra_units|numeric|min:1', //required_if:custom_fee_type,1|
						'initial_weight' => 'required_if:custom_fee_type,2|numeric|min:1',
						'initial_weight_price' => 'required_if:custom_fee_type,2|numeric',
					);
			if($inputs['shipping_setting'] == 'ship_to' && $inputs['fee_type'] == '1' && $inputs['custom_fee_type'] == '2')
			{
				$current_added_group = $inputs['current_added_group'];
				if($current_added_group > 0)
				{
					for($i=1;$i<=$current_added_group;$i++)
					{
						if($i==1)
						{
							$inputs['weight_from_'.$i] = $inputs['initial_weight'];
						}
						else
						{
							$prev=$i-1;
							$inputs['weight_from_'.$i] = $inputs['weight_to_'.$prev];
						}
						$new_rules = array();
						if($i!=1)
						{

							$max_weight_to = $inputs['weight_from_'.$i]+1;
							$max_addtional_weight = $inputs['weight_to_'.$i]-$inputs['weight_from_'.$i];
							$new_rules = array(
								'weight_from_'.$i => 'required|same:weight_to_'.$prev.'|numeric',
								'weight_to_'.$i => 'required|min:'.$max_weight_to.'|numeric',
								'additional_weight_'.$i => 'required|max:'.$max_addtional_weight.'|numeric',
								'additional_weight_added_'.$i => 'required|numeric',
							);
						}
						else
						{
							$inputs['weight_from_1'] = $inputs['initial_weight'];
							$max_weight_to = $inputs['initial_weight']+1;
							$max_addtional_weight = $inputs['weight_to_'.$i]-$inputs['weight_from_'.$i];
							$new_rules = array(
								'weight_from_'.$i => 'required|same:initial_weight|numeric',
								//'weight_to_'.$i => 'sometimes|required|min:'.$max_weight_to.'|numeric',
								'additional_weight_'.$i => 'required_with:weight_to_'.$i.'|max:'.$max_addtional_weight.'|numeric',
								'additional_weight_added_'.$i => 'required_with:weight_to_'.$i.'|numeric',
							);
							if($inputs['weight_to_'.$i]!='')
							{
								$new_rules['weight_to_'.$i] = 'sometimes|required|min:'.$max_weight_to.'|numeric';
							}

						}
						$rules = $rules + $new_rules;
					}
				}
			}
		}
		else
		{
			$rules = array('other_shipping_setting' => 'required',
						'other_fee_type' => 'required_if:other_shipping_setting,ship_to',
						'other_discount' => 'required_if:other_fee_type,2'
			);
		}

		$message = array('geo_location.required_without' => 'Either geo location or zone are mandatory',
						'zone.required_without' => 'Either geo location or zone are mandatory',
						'geo_countries.required_with' => 'Select the geo location or countries',
						'geo_locations.required_with' => 'Select the countries or geo location',
						'zone_countries.required_with' => 'Either geo location or zone are mandatory',
						'shipping_type.required' => 'Select The shipping type',
						'discount.required_if' => 'Required',
						'min_order.required_if' => 'Required',
						'max_order.required_if' => 'Required',
						'cost_base_weight.required_if' => 'Required',
						'extra_units.required_if' => 'Required',
						'extra_costs.required_if' => 'Required',
						'other_discount.required_if' => 'Required',
						);
		$v = Validator::make($inputs, $rules, $message);
		if ($v->fails())
		{
			$validation_errors = $v->errors()->toArray();
			return Redirect::action('AdminShippingTemplateController@getCustomShippingTemplate',array($id,$company_id))->with('validation_errors',$validation_errors)->withInput()->withErrors($v);
		}
		else
		{
			if($fee_custom_id > 0)
			{
				$shippingTemplateService = new ShippingTemplateService();
				$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
				$country_selected_type = isset($inputs['geo_location'])?$inputs['geo_location']:(isset($inputs['zone'])?$inputs['zone']:'');
				$temp_comp_custom = array();
				$temp_comp_custom['country_selected_type'] = $country_selected_type;
				$temp_comp_custom['shipping_setting'] = $inputs['shipping_setting'];
				$temp_comp_custom['fee_type'] = $inputs['fee_type'];
				$temp_comp_custom['discount'] = $inputs['discount'];
				$temp_comp_custom['custom_fee_type'] = $inputs['custom_fee_type'];
				$temp_comp_custom['min_order'] = $inputs['min_order'];
				$temp_comp_custom['max_order'] = $inputs['max_order'];
				$temp_comp_custom['cost_base_weight'] = $inputs['cost_base_weight'];
				$temp_comp_custom['extra_units'] = $inputs['extra_units'];
				$temp_comp_custom['extra_costs'] = $inputs['extra_costs'];
				$temp_comp_custom['initial_weight'] = $inputs['initial_weight'];
				$temp_comp_custom['initial_weight_price'] = $inputs['initial_weight_price'];

				if($inputs['fee_type'] == 1 && $inputs['custom_fee_type'] == 2)
				{
					$shippingTemplateService->deleteShippingTemplateCustomWeight($fee_custom_id);
					$current_added_group = $inputs['current_added_group'];
					if($current_added_group > 0)
					{
						for($i=1;$i<=$current_added_group;$i++)
						{
							if($i==1)
							{
								$inputs['weight_from_'.$i] = $inputs['initial_weight'];
							}
							$fee_custom_weight = array();
							$fee_custom_weight['template_id'] = $id;
							$fee_custom_weight['company_id'] = $company_id;
							$fee_custom_weight['template_company_id'] = $temp_comp_id;
							$fee_custom_weight['shipping_template_fee_custom_id'] = $fee_custom_id;
							$fee_custom_weight['weight_from'] = $inputs['weight_from_'.$i];
							$fee_custom_weight['weight_to'] = $inputs['weight_to_'.$i];
							$fee_custom_weight['additional_weight'] = $inputs['additional_weight_'.$i];
							$fee_custom_weight['additional_weight_price'] = $inputs['additional_weight_added_'.$i];
							$fee_custom_weight_id = $shippingTemplateService->addShippingTemplateCustomWeight($fee_custom_weight);
						}
					}
				}
				//Log::info('---------------------shiping setting '.$inputs['shipping_setting'].'--------------------');
				if($inputs['shipping_setting'] == 'ship_to')
				{
					if(isset($inputs['fee_type']) && $inputs['fee_type']=='2')
					{
						$shippingTemplateService->removeCustomWeightRecord($fee_custom_id);
						$shippingTemplateService->removeCustomQuanityRecord($fee_custom_id);
					}
					if(isset($inputs['fee_type']) && $inputs['fee_type']=='1')
					{
						$shippingTemplateService->removeCustomStandardRecord($fee_custom_id);
						if(isset($inputs['custom_fee_type']) && $inputs['custom_fee_type']=='1')
						{
							$shippingTemplateService->removeCustomWeightRecord($fee_custom_id);
						}
						if(isset($inputs['custom_fee_type']) && $inputs['custom_fee_type']=='2')
						{
							$shippingTemplateService->removeCustomQuanityRecord($fee_custom_id);
						}
					}
				}
				else
				{
					//Log::info('--------------------- else '.$fee_custom_id.' --------------------');
					$shippingTemplateService->removeCustomStandardRecord($fee_custom_id);
					$shippingTemplateService->removeCustomStandardRecord($fee_custom_id);
					$shippingTemplateService->removeCustomWeightRecord($fee_custom_id);
					$shippingTemplateService->removeCustomQuanityRecord($fee_custom_id);
				}

				$shippingTemplateService->updateShippingTemplateFeeCustom($fee_custom_id, $temp_comp_custom);

				$shippingTemplateService->deleteShippingTemplateFeeCustomCountry($fee_custom_id);
				$countries = ($country_selected_type == 'geo_location')?$inputs['geo_countries']:$inputs['zone_countries'];

				foreach($countries as $country)
				{
					$fee_custom_country = array();
					$fee_custom_country['template_id'] = $id;
					$fee_custom_country['company_id'] = $company_id;
					$fee_custom_country['template_company_id'] = $temp_comp_id;
					$fee_custom_country['shipping_template_fee_custom_id'] = $fee_custom_id;
					$fee_custom_country['country_id'] = $country;
					$temp_comp_fee_custom_country_id = $shippingTemplateService->addShippingTemplateFeeCustomCountry($fee_custom_country);
				}
				return Redirect::action('AdminShippingTemplateController@postCustomShippingTemplate',array($id,$company_id))->with('success','Custom fee details updated successfully');
			}
			else
			{
				$shippingTemplateService = new ShippingTemplateService();

				//Get template company
				$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
				if($new_group == 1)
					{
					if($temp_comp_id!='' && $temp_comp_id > 0)
					{
						//if template company present, then update the fee type in the controller
						$temp_comp = array();
						$temp_comp['fee_type'] = 1;
						$shippingTemplateService->updateShippingTemplateCompany($temp_comp_id, $temp_comp);
					}
					else
					{
						//if template company id not present, then add that to the db and get the id
						$temp_comp = array();
						$temp_comp['template_id'] = $id;
						$temp_comp['company_id'] = $company_id;
						$temp_comp['fee_type'] = 1;
						$temp_comp['fee_discount'] = 0;
						$temp_comp['delivery_type'] = 2;
						$temp_comp['days'] = 0;

						$temp_comp_id = $shippingTemplateService->addShippingTemplateCompany($temp_comp);
					}
					//add the template company custom fee details
					$country_selected_type = isset($inputs['geo_location'])?$inputs['geo_location']:(isset($inputs['zone'])?$inputs['zone']:'');
					$temp_comp_custom = array();
					$temp_comp_custom['template_id'] = $id;
					$temp_comp_custom['company_id'] = $company_id;
					$temp_comp_custom['template_company_id'] = $temp_comp_id;
					$temp_comp_custom['country_selected_type'] = $country_selected_type;
					$temp_comp_custom['shipping_setting'] = $inputs['shipping_setting'];
					$temp_comp_custom['fee_type'] = $inputs['fee_type'];
					$temp_comp_custom['discount'] = $inputs['discount'];
					$temp_comp_custom['custom_fee_type'] = $inputs['custom_fee_type'];
					$temp_comp_custom['min_order'] = $inputs['min_order'];
					$temp_comp_custom['max_order'] = $inputs['max_order'];
					$temp_comp_custom['cost_base_weight'] = $inputs['cost_base_weight'];
					$temp_comp_custom['extra_units'] = $inputs['extra_units'];
					$temp_comp_custom['extra_costs'] = $inputs['extra_costs'];
					$temp_comp_custom['initial_weight'] = $inputs['initial_weight'];
					$temp_comp_custom['initial_weight_price'] = $inputs['initial_weight_price'];

					$temp_comp_fee_custom_id = $shippingTemplateService->addShippingTemplateFeeCustom($temp_comp_custom);

					if($inputs['custom_fee_type'] == 2)
					{
						$current_added_group = $inputs['current_added_group'];
						if($current_added_group > 0)
						{
							for($i=1;$i<=$current_added_group;$i++)
							{
								if(isset($inputs['weight_to_'.$i]) && $inputs['weight_to_'.$i]!='')
								{
									if($i==1)
									{
										$inputs['weight_from_'.$i] = $inputs['initial_weight'];
									}
									$fee_custom_weight = array();
									$fee_custom_weight['template_id'] = $id;
									$fee_custom_weight['company_id'] = $company_id;
									$fee_custom_weight['template_company_id'] = $temp_comp_id;
									$fee_custom_weight['shipping_template_fee_custom_id'] = $temp_comp_fee_custom_id;
									$fee_custom_weight['weight_from'] = $inputs['weight_from_'.$i];
									$fee_custom_weight['weight_to'] = $inputs['weight_to_'.$i];
									$fee_custom_weight['additional_weight'] = $inputs['additional_weight_'.$i];
									$fee_custom_weight['additional_weight_price'] = $inputs['additional_weight_added_'.$i];
									$fee_custom_weight_id = $shippingTemplateService->addShippingTemplateCustomWeight($fee_custom_weight);
								}
								else
								{
									break;
								}
							}
						}
					}



					$countries = ($country_selected_type == 'geo_location')?$inputs['geo_countries']:$inputs['zone_countries'];

					foreach($countries as $country){
						//$country
						$fee_custom_country = array();
						$fee_custom_country['template_id'] = $id;
						$fee_custom_country['company_id'] = $company_id;
						$fee_custom_country['template_company_id'] = $temp_comp_id;
						$fee_custom_country['shipping_template_fee_custom_id'] = $temp_comp_fee_custom_id;
						$fee_custom_country['country_id'] = $country;
						$temp_comp_fee_custom_country_id = $shippingTemplateService->addShippingTemplateFeeCustomCountry($fee_custom_country);
					}

					//insert other countries/region by default
					$other_temp_comp_custom = array();
					$other_temp_comp_custom['template_id'] = $id;
					$other_temp_comp_custom['company_id'] = $company_id;
					$other_temp_comp_custom['template_company_id'] = $temp_comp_id;
					$other_temp_comp_custom['country_selected_type'] = 'other_countries';
					if($is_custom_only_company) {
						$other_temp_comp_custom['shipping_setting'] = 'dont_ship_to';
						$other_temp_comp_custom['fee_type'] = '';
					}
					else {
						$other_temp_comp_custom['shipping_setting'] = 'ship_to';
						$other_temp_comp_custom['fee_type'] = '2';
					}
					$other_temp_comp_custom['discount'] = 0;
					$other_temp_comp_custom['custom_fee_type'] = '';
					$other_contries = $shippingTemplateService->getShippingTemplateFeeCustomOtherCountries($temp_comp_id);
					if(count($other_contries) <= 0)
						$shippingTemplateService->addShippingTemplateFeeCustom($other_temp_comp_custom);
					if(!$do_redirect)
						return Redirect::action('AdminShippingTemplateController@postCustomShippingTemplate',array($id,$company_id))->with('success','Custom fee details addded successfully');
					else
					{
						$url = Url::action('AdminShippingTemplateController@getEdit',array($id));
						return Redirect::action('AdminShippingTemplateController@getCustomShippingTemplate',array($id,$company_id))->with('success','Custom fee details addded successfully') ->with('action_url',$url)->with('is_redirect',1);
						//return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'action_url' => $url, 'id' => $id));
					}
				}
				else
				{
					$other_temp_comp_custom = array();
					$other_temp_comp_custom['shipping_setting'] = isset($inputs['other_shipping_setting'])?$inputs['other_shipping_setting']:'ship_to';
					$other_temp_comp_custom['fee_type'] = (isset($inputs['other_shipping_setting']) && $inputs['other_shipping_setting'] == 'ship_to' )?2:'';
					$other_temp_comp_custom['discount'] = (isset($inputs['other_discount']) && $inputs['other_discount'] != '' )?$inputs['other_discount']:'0';
					$other_temp_comp_custom['custom_fee_type'] = '';
					$shippingTemplateService->updateShippingTemplateFeeCustomOtherCountries($temp_comp_id, $other_temp_comp_custom);

					$url = Url::action('AdminShippingTemplateController@getEdit',array($id));
					return Redirect::action('AdminShippingTemplateController@getCustomShippingTemplate',array($id,$company_id))->with('success','Custom fee details updated successfully')->with('is_close',1);

					//return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'redirect' => ''));
					//return Redirect::action('AdminShippingTemplateController@postCustomShippingTemplate',array($id,$company_id))->with('success','Custom fee details updated successfully');
				}
			}
		}
		exit;
	}

	public function postShippingTemplateFeeCustomAction()
	{
		$inputs = Input::all();
		$action = $inputs['action'];//echo "<pre>";print_r($inputs);echo "</pre>";
		$shippingTemplateService = new ShippingTemplateService();
		switch($action)
		{
			case 'delete_fee_custom':
				//$temp_comp_fee_custom_id = $shippingTemplateService->deleteShippingTemplateFeeCustom($inputs['fee_custom_id']);
				//echo json_encode(array(	'result'=>'success', 'success_message' => 'Custom Shipping Template Deleted Successfully'));exit;
				$deleted_details = $shippingTemplateService->deleteShippingTemplateFeeCustom($inputs['fee_custom_id']);
				$deleted_countries = $deleted_details['deleted_countries'];
				$temp_comp_id = $deleted_details['temp_comp_id'];
				$shippingTemplateService->checkAndDeleteShippingTemplateFeeCustom($temp_comp_id);
				echo json_encode(array(	'result'=>'success', 'success_message' => 'Custom Shipping Template Deleted Successfully', 'deleted_countries' => $deleted_countries));exit;
				break;


			case 'get_fee_custom':
				$fee_custom_details = $shippingTemplateService->getShippingTemplateFeeCustom($inputs['fee_custom_id']);
				$temp_comp_id = isset($fee_custom_details['template_company_id'])?$fee_custom_details['template_company_id']:0;
				//	echo "<br>temp_comp_id: ".$temp_comp_id;

				$prev_selected_countries = $shippingTemplateService->getSelectedTemplateCustomCountriesExcept($temp_comp_id, $inputs['fee_custom_id']);
				//echo "<pre>";print_r($prev_selected_countries);echo "</pre>";

				echo json_encode(array('fee_custom_details'=>$fee_custom_details, 'prev_selected_countries' => $prev_selected_countries));exit;
				break;

		}
	}

	public function getCustomDeliveryTime($id='',$company_id='')
	{
		$shippingTemplateService = new ShippingTemplateService();
		$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
		$temp_name = $shippingTemplateService->getShippingTemplateName($id);
		$delivery_custom_details = array();
		$prev_selected_countries = array();
		$other_countries_details = array();
		if($temp_comp_id!='' && $temp_comp_id >0)
		{
			$delivery_custom_details = $shippingTemplateService->getShippingTemplateDeliveryCustomDetails($temp_comp_id);
			$other_countries_details = $shippingTemplateService->getShippingTemplateDeliveryCustomOtherCountries($temp_comp_id);
			$prev_selected_countries = $shippingTemplateService->getSelectedDeliveryCustomCountries($temp_comp_id);
		}

		$countries_list = Webshopshipments::getCountriesList('list', 'country_name', 'asc', false);
		$geo_countries_arr = array();
		$geo_countries_list = $shippingTemplateService->getCountryListId('geo_location_id', $company_id);
		foreach($geo_countries_list as $country)
		{
			$geo_countries_arr[$country->geo_location_id][] = $country;
		}

		$zone_contries_list = $shippingTemplateService->getCountryListId('zone_id', $company_id);
		foreach($zone_contries_list as $country)
		{
			$zone_contries_arr[$country->zone_id][] = $country;
		}

		$is_redirect = 0;
		$action_url = '';
		$is_close = 0;

		$validation_errors = (Session::has('validation_errors'))?Session::get('validation_errors'):array();
		Session::forget('validation_errors');

		$is_redirect = (Session::has('is_redirect'))?Session::get('is_redirect'):0;
		Session::forget('is_redirect');

		$is_close = (Session::has('is_close'))?Session::get('is_close'):0;
		Session::forget('is_close');

		if($is_redirect == 1)
		{
			$action_url = Url::action('AdminShippingTemplateController@getEdit',array($id));
		}
		return View::make('admin.customDeliveryTime', compact('id', 'company_id', 'countries_list', 'geo_countries_arr', 'zone_contries_arr', 'delivery_custom_details', 'prev_selected_countries', 'other_countries_details', 'is_redirect', 'validation_errors', 'action_url', 'is_close', 'temp_name'));
	}

	public function postCustomDeliveryTime($id='',$company_id='')
	{
		$inputs = Input::all();
		$t_name = $inputs['template_name'];
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$shippingTemplateService = new ShippingTemplateService();
		$do_redirect = false;
		if($id <= 0)
		{
			$do_redirect = true;
			$count = DB::table('shipping_templates')->where('template_name', $t_name)->count();
			if($t_name !='' )
				if($count == 0)
					$template_name = $t_name;
				else
					$template_name = $t_name.'_1';
			else
				$template_name  = 'Untitled '.rand();
			
			$temp_det = array();
			$temp_det['template_name'] = $template_name;
			$temp_det['user_id'] = $logged_user_id;
			$template_id = $shippingTemplateService->addShippingTemplate($temp_det);
			$id = $template_id;

			$temp_comp = array();
			$temp_comp['template_id'] = $template_id;
			$temp_comp['company_id'] = $company_id;
			$temp_comp['fee_type'] = 1;
			$temp_comp['fee_discount'] = 0;
			$temp_comp['delivery_type'] = 2;
			$temp_comp['days'] = 0;
			$temp_comp = $shippingTemplateService->addShippingTemplateCompany($temp_comp);
		}

		$url = Url::action('AdminShippingTemplateController@getEdit',array($id));
		if($id == '' || $company_id == '')
		{
			if(!$do_redirect) $url = '';
			return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'redirect' => $url));
		}


		$delivery_custom_id = 0;
		if(Input::has('custom_delivery_id'))
			$delivery_custom_id = Input::get('custom_delivery_id');

		$inputs = Input::all();

		$new_group = (isset($inputs['new_group']) && $inputs['new_group'] == 1)?1:0;
		if($new_group == 1)
		{
			$rules = array('geo_location' => 'required_without:zone',
						'zone' => 'required_without:geo_location',
						'geo_countries' => 'required_with:geo_location',
						'zone_countries' => 'required_with:zone',
						'days' => 'required',
					);
		}
		else
		{
			$rules = array();
		}

		$message = array('geo_location.required_without' => 'Either geo location or zone are mandatory',
						'zone.required_without' => 'Either geo location or zone are mandatory',
						'geo_countries.required_with' => 'Select the geo location or countries',
						'geo_locations.required_with' => 'Select the countries or geo location',
						'zone_countries.required_with' => 'Either geo location or zone are mandatory',
						'days.required' => 'Required'
						);
		$v = Validator::make($inputs, $rules, $message);
		if ($v->fails())
		{
			$errors = $v->errors();
			return Redirect::action('AdminShippingTemplateController@postCustomDeliveryTime',array($id,$company_id))->withInput()->withErrors($v);
		}
		else
		{
			if($delivery_custom_id > 0)
			{
				$shippingTemplateService = new ShippingTemplateService();
				$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
				$country_selected_type = isset($inputs['geo_location'])?$inputs['geo_location']:(isset($inputs['zone'])?$inputs['zone']:'');
				$temp_comp_custom = array();
				$temp_comp_custom['country_selected_type'] = $country_selected_type;
				$temp_comp_custom['days'] = $inputs['days'];

				$shippingTemplateService->updateShippingTemplateDeliveryCustom($delivery_custom_id, $temp_comp_custom);

				$shippingTemplateService->deleteShippingTemplateDeliveryCustomCountry($delivery_custom_id);

				$countries = ($country_selected_type == 'geo_location')?$inputs['geo_countries']:$inputs['zone_countries'];

				foreach($countries as $country)
				{
					$delivery_custom_country = array();
					$delivery_custom_country['template_id'] = $id;
					$delivery_custom_country['company_id'] = $company_id;
					$delivery_custom_country['template_company_id'] = $temp_comp_id;
					$delivery_custom_country['template_company_delivery_custom_id'] = $delivery_custom_id;
					$delivery_custom_country['country_id'] = $country;
					$temp_comp_delivery_custom_country_id = $shippingTemplateService->addShippingTemplateDeliveryCustomCountry($delivery_custom_country);
				}
				return Redirect::action('AdminShippingTemplateController@postCustomDeliveryTime',array($id,$company_id))->with('success','Custom delivery details updated successfully');
			}
			else
			{
				$shippingTemplateService = new ShippingTemplateService();

				//Get template company
				$temp_comp_id = $shippingTemplateService->getShippingTemplateCompanyId($id, $company_id);
				if($new_group == 1)
				{
					if($temp_comp_id!='' && $temp_comp_id > 0)
					{
						//if template company present, then update the fee type in the controller
						$temp_comp = array();
						$temp_comp['delivery_type'] = 1;
						$shippingTemplateService->updateShippingTemplateCompany($temp_comp_id, $temp_comp);
					}
					else
					{
						//if template company id not present, then add that to the db and get the id
						$temp_comp = array();
						$temp_comp['template_id'] = $id;
						$temp_comp['company_id'] = $company_id;
						$temp_comp['delivery_type'] = 1;
						$temp_comp['days'] = 0;
						$temp_comp_id = $shippingTemplateService->addShippingTemplateCompany($temp_comp);
					}
					//add the template company custom fee details
					$country_selected_type = isset($inputs['geo_location'])?$inputs['geo_location']:(isset($inputs['zone'])?$inputs['zone']:'');
					$temp_comp_custom = array();
					$temp_comp_custom['template_id'] = $id;
					$temp_comp_custom['company_id'] = $company_id;
					$temp_comp_custom['template_company_id'] = $temp_comp_id;
					$temp_comp_custom['country_selected_type'] = $country_selected_type;
					$temp_comp_custom['days'] = $inputs['days'];
					$temp_comp_delivery_custom_id = $shippingTemplateService->addShippingTemplateDeliveryCustom($temp_comp_custom);

					$countries = ($country_selected_type == 'geo_location')?$inputs['geo_countries']:$inputs['zone_countries'];

					foreach($countries as $country){
						//$country
						$fee_custom_country = array();
						$fee_custom_country['template_id'] = $id;
						$fee_custom_country['company_id'] = $company_id;
						$fee_custom_country['template_company_id'] = $temp_comp_id;
						$fee_custom_country['template_company_delivery_custom_id'] = $temp_comp_delivery_custom_id;
						$fee_custom_country['country_id'] = $country;
						$temp_comp_delivery_custom_country_id = $shippingTemplateService->addShippingTemplateDeliveryCustomCountry($fee_custom_country);
					}

					//insert other countries/region by default
					$other_temp_comp_custom = array();
					$other_temp_comp_custom['template_id'] = $id;
					$other_temp_comp_custom['company_id'] = $company_id;
					$other_temp_comp_custom['template_company_id'] = $temp_comp_id;
					$other_temp_comp_custom['country_selected_type'] = 'other_countries';
					$other_temp_comp_custom['days'] = '0';
					$other_contries = $shippingTemplateService->getShippingTemplateDeliveryCustomOtherCountries($temp_comp_id);
					if(count($other_contries) <= 0)
						$shippingTemplateService->addShippingTemplateDeliveryCustom($other_temp_comp_custom);
					if(!$do_redirect)
					{
						return Redirect::action('AdminShippingTemplateController@postCustomDeliveryTime',array($id,$company_id))->with('success','Custom delivery details added successfully');
					}
					else
					{
						return Redirect::action('AdminShippingTemplateController@getCustomDeliveryTime',array($id,$company_id))->with('success','Custom delivery details added successfully')->with('is_redirect', 1);
						//return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'redirect' => $url));
					}

				}
				else
				{
					$other_temp_comp_custom = array();
					$other_temp_comp_custom['days'] = isset($inputs['other_days'])?$inputs['other_days']:'0';
					$shippingTemplateService->updateShippingTemplateDeliveryCustomOtherCountries($temp_comp_id, $other_temp_comp_custom);
					return Redirect::action('AdminShippingTemplateController@getCustomDeliveryTime',array($id,$company_id))->with('success','Custom delivery details updated successfully')->with('is_close', 1);
					//return View::make('admin.customShippingTemplate', array('is_redirect' => 1, 'redirect' => ''));
					//return Redirect::action('AdminShippingTemplateController@postCustomDeliveryTime',array($id,$company_id))->with('success','Custom delivery details updated successfully');
				}
			}
		}
		exit;
	}

	public function postShippingTemplateDeliveryCustomAction()
	{
		$inputs = Input::all();
		$action = $inputs['action'];//echo "<pre>";print_r($inputs);echo "</pre>";
		$shippingTemplateService = new ShippingTemplateService();
		switch($action)
		{
			case 'delete_custom_delivery':
				$deleted_details = $shippingTemplateService->deleteShippingTemplateDeliveryCustom($inputs['custom_delivery_id']);
				$deleted_countries = $deleted_details['deleted_countries'];
				$temp_comp_id = $deleted_details['temp_comp_id'];
				$shippingTemplateService->checkAndDeleteShippingTemplateDeliveryCustom($temp_comp_id);
				echo json_encode(array(	'result'=>'success', 'success_message' => 'Custom Shipping Template Deleted Successfully', 'deleted_countries' => $deleted_countries));exit;
				break;

			case 'get_custom_delivery':
				$custom_delivery_details = $shippingTemplateService->getShippingTemplateDeliveryCustom($inputs['custom_delivery_id']);
				$temp_comp_id = isset($custom_delivery_details['template_company_id'])?$custom_delivery_details['template_company_id']:0;
				$prev_selected_countries = $shippingTemplateService->getSelectedTemplateCustomDeliveryCountriesExcept($temp_comp_id, $inputs['custom_delivery_id']);
				echo json_encode(array('custom_delivery_details'=>$custom_delivery_details, 'prev_selected_countries' => $prev_selected_countries));exit;
				break;

		}
	}

	public function getSetAsDefaultAction($id)
	{
		$page = Input::has('page')?Input::get('page'):'';
		if(!BasicCUtil::checkIsDemoSite()) {
			//echo $page; exit;
			DB::table('shipping_templates')->update(array('is_default' => 0));
			if($id)
			{
				DB::table('shipping_templates')->where('id', $id)->update(array('is_default' => 1));
				return Redirect::to('admin/shipping-template/index?page='.$page)->with('success','Successfully set as default');
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/shipping-template/index?page='.$page)->with('error_message',$errMsg);
		}
	}
	public function getDeleteSippingTemplateAction($template_id)
	{
		//$page = Input::has('page')?Input::get('page'):'';
		if(!BasicCUtil::checkIsDemoSite()) {
			$shippingTemplateService = new ShippingTemplateService();
			if($template_id)
			{
				$shippingTemplateService->getDeleteTemplate($template_id);
				return Redirect::to('admin/shipping-template/index')->with('success','Successfully Deleted');
			}
		} else {
			$errMsg = Lang::get('common.demo_site_featured_not_allowed');
			return Redirect::to('admin/shipping-template/index')->with('error_message',$errMsg);
		}
	}
	public function getViewTemplate($template_id)
	{
		$company_details = array();
		$shippingTemplateService = new ShippingTemplateService();
		$shippingTemplateService->setShippingSrchArr(Input::All());
		$template_name = $shippingTemplateService->getTemplateName($template_id);
		//$fee_type = $shippingTemplateService->getCompanyDetails($template_id);
		$company_details = $shippingTemplateService->getCompanyName($template_id);
		$this->header->setMetaTitle(trans('meta.view_shipping_template'));
		//$company_name = $shippingTemplateService->getFeeType($template_id);
		//echo "<pre>";print_r($shipping_templates);exit;
		//echo "<pre>";print_r($temp_comp_id);exit;
	    if(count($company_details) > 0) {
			foreach($company_details as $key => $values) {
					$company_name[$values->company_id] = $values->company_name;

			}

		}
		return View::make('admin.viewTemplate', compact('company_name','fee_type','template_name','company_details'));
	}
	public function postCheckCustomValues()
	{
		$company_id = '';
		$delivery_id = '';
		$custom_company_id = array();
		$delivery_company_id = array();
		$shippingTemplateService = new ShippingTemplateService();
		$template_id = Input::has('template_id')? Input::get('template_id') : '';
		$fee_company_ids = Input::has('fee_company_ids')? Input::get('fee_company_ids') : '';
		$delivery_company_ids = Input::has('delivery_company_ids')? Input::get('delivery_company_ids') : '';
		$c_company_id = rtrim($fee_company_ids, ',');
		$d_company_id = rtrim($delivery_company_ids, ',');
		$custom_company_id = explode(',', $c_company_id);
		$delivery_company_id = explode(',', $d_company_id);
		$error_company_id = array();
		$error_delivery_id = array();
		$error_company_id = '';
		$error_delivery_id = '';
		if($custom_company_id)
		{
			foreach($custom_company_id as $c_id)
			{
				//echo "<br>";print_r($c_id); echo "<br>";
				$check_is_custom_avail = $shippingTemplateService->checkIsCustomShippingAvailable($template_id,$c_id);
				if($check_is_custom_avail <= 0)
				{
					$company_id .= ','.$c_id;
				}
				else{
				}
			}
		}
		$error_company_id = ltrim($company_id,',');
		if($delivery_company_id)
		{
			foreach($delivery_company_id as $d_id)
			{
				$check_is_delivery_avail = $shippingTemplateService->checkIsCustomDeliveryAvailable($template_id,$d_id);
				if($check_is_delivery_avail <= 0)
				{
					$delivery_id .= ','.$d_id;
				}
				else
				{
				}
			}
		}
		$error_delivery_id = ltrim($delivery_id,',');
		echo $error_company_id."##".$error_delivery_id;
	}
}