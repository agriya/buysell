<?php

class ReportedProductService
{

	public function checkReportExists($product_id, $user_id){
		$report_count = ReportedProduct::where('product_id',$product_id)->where('user_id',$user_id)->count();
		return $report_count;
	}
	public function addProductReport($inputs){
		$reported_product = new ReportedProduct();
		$reported_id = $reported_product->addNew($inputs);
		$this->sendMailToShopAndAdmin($inputs);
		return $reported_id;
	}
	public function updateProductReport($inputs){
		$product_id = $inputs['product_id'];
		$user_id = $inputs['user_id'];
		unset($inputs['user_id']);
		unset($inputs['product_id']);
		$reported_product = new ReportedProduct();
		$default_fields = $reported_product->getTableFields();
		$default_fields = array_fill_keys($default_fields,'');
		$final_arr = array_intersect_key($inputs,$default_fields);
		$return =true;
		try{
			ReportedProduct::where('product_id',$product_id)->where('user_id',$user_id)->update($final_arr);
			$return = true;
		}
		catch(exception $e){
			echo $e->getMessage();
			$return = false;
		}
		return $return;
	}
	public function getAllProductReports()
	{
		//used pagination here also to restrict the large number of fetching records
		$reported_details = ReportedProduct::select('reported_products.*','product.product_name', 'product.url_slug', 'product.product_code', 'users.user_name')
											->leftjoin('product', 'product.id', '=', 'reported_products.product_id')
											->leftJoin('users', 'users.id', '=', 'reported_products.user_id')
											->orderby('reported_products.id', 'desc')
											->get()->toArray();


		$product_wise_report = array();
		if(!empty($reported_details))
		{
			foreach($reported_details as $report)
			{
				if(isset($report['report_thread']) && $report['report_thread']!='')
					$reported_threads = explode(',',$report['report_thread']);

				if(isset($product_wise_report[$report['product_id']]['reported_threads']))
					$report['reported_threads'] = array_merge($product_wise_report[$report['product_id']]['reported_threads'],$reported_threads);
				else
					$report['reported_threads'] = $reported_threads;

				$reported_user = array();
				$reported_user[0]['user_name'] = $report['user_name'];
				$reported_user[0]['user_id'] = $report['user_id'];
				$reported_user[0]['profile_url'] = CUtil::userProfileUrl($report['user_id']);
				if(isset($product_wise_report[$report['product_id']]['reported_users']))
					$report['reported_users'] = array_merge($product_wise_report[$report['product_id']]['reported_users'],$reported_user);
				else
					$report['reported_users'] = $reported_user;

				$product_wise_report[$report['product_id']] = $report;
			}
		}
		$reported_products = array_values($product_wise_report);
		return $reported_products;

	}
	public function bulkDeleteReport($report_ids = array()){
		$return = true;
		if(!empty($report_ids))
		{
			try{
				ReportedProduct::whereIn('id',$report_ids)->delete();
			}
			catch(Exception $e)
			{
				$return = false;
			}
		}
		return $return;
	}
	public function getProductReportDetails($product_id, $user_id){
		$cache_key = 'PRDCKA_'.$product_id.'_'.$user_id;
		if (($reported_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$reported_details = ReportedProduct::where('product_id', $product_id)->where('user_id', $user_id)->first();
			HomeCUtil::cachePut($cache_key, $reported_details, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $reported_details;
	}

	public function getSingleProductReport($product_id){
		$reported_details = array();
		if($product_id >0)
		{
			$reported_details = ReportedProduct::select('reported_products.*','users.user_name')
								->leftJoin('users', 'users.id', '=', 'reported_products.user_id')
								->where('reported_products.product_id',$product_id)
								->orderby('reported_products.id', 'desc')
								->get()->toArray();

			if(!empty($reported_details))
			{
				foreach($reported_details as $key => $report)
				{
					$reported_threads = array();
					$report['profile_url'] = CUtil::userProfileUrl($report['user_id']);
					if(isset($report['report_thread']) && $report['report_thread']!='')
						$reported_threads = explode(',',$report['report_thread']);
					$report['reported_threads'] = (!empty($reported_threads))?$reported_threads:array();

					$reported_details[$key] = $report;
				}
			}
		}
		return $reported_details;
	}
	public function sendMailToShopAndAdmin($inputs)
	{
		$product_id = (isset($inputs['product_id']) && $inputs['product_id'] >0)?$inputs['product_id']:0;
		$p_details = array();
		try{
			$prod_obj = Products::initialize($product_id);
			$p_details = $prod_obj->getProductDetails();
		}
		catch(Exception $e)
		{
		}
		if(!empty($p_details))
		{
			$threads = explode(',',$inputs['report_thread']);

			if(isset($threads) && !empty($threads))
			{
				$reported_threads = array();
				foreach($threads as $thread)
				{
					$reported_threads[] = Lang::get('viewProduct.'.$thread);
				}
			}

			$data_arr['reported_by'] = CUtil::getUserDetails($inputs['user_id']);
			$data_arr['product_owner'] = CUtil::getUserDetails($p_details['product_user_id']);
			$data_arr['product_details'] = $p_details;
			$data_arr['product_view_url'] = Products::getProductViewURL($p_details['id'], $p_details);
			$data_arr['subject'] = trans('common.report_posted_for_product');
			$data_arr['date_posted'] = date('Y-m-d');
			$data_arr['reported_threads'] = $reported_threads;

			//send mail to admin
			try {
				Mail::send('emails.newThreadPostedMailForAdmin', $data_arr, function($m) use ($data_arr){
					$m->to(Config::get('generalConfig.support_email'));
					$m->subject($data_arr['subject']);
				});
			} catch (Exception $e) {
				//return false
				CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
			}

			//send mail to owner
			if(isset($data_arr['product_owner']['email']) && $data_arr['product_owner']['email']!='')
			{
				try {
					Mail::send('emails.newThreadPostedMailForSeller', $data_arr, function($m) use ($data_arr){
						$m->to($data_arr['product_owner']['email']);
						$m->subject($data_arr['subject']);
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}
			}
		}
	}

}