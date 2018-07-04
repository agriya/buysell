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
//use BasicCUtil, URL, DB, Lang, View, Input, Validator, Str, Config, Products, Image;
class FeaturedProductsService
{
	//Featured products plan functions start
	public function getFeaturedProductsPlanSettings($id = 0)
 	{
 		if($id) {
 			return DB::table('featured_product_plans')
			 			->where('featured_prod_plan_id', $id)
						->first();
 		}
 	}

 	public function buildFeaturedProductsPlanQuery()
	{
		return DB::table('featured_product_plans')
					->Select("featured_prod_plan_id", "featured_days", "featured_price", "status")
					->orderBy('featured_prod_plan_id', 'DESC');
	}

	public function updateFeaturedProductsPlan($input)
 	{
 		$featured_days = $input['featured_days'];
 		$featured_price = $input['featured_price'];
 		$status = $input['status'];
 		$featured_qry = DB::table('featured_product_plans')->whereRaw('featured_days = ? AND featured_price = ?', array($featured_days, $featured_price));
		if($input['feature_id']) {
			$featured_qry = $featured_qry->whereRaw('featured_prod_plan_id != ?', array($input['feature_id']));
		}
		$featured_id = $featured_qry->pluck('featured_prod_plan_id');

		if($featured_id > 0) {
			return json_encode(array('status' => 'error', 'error_message' => trans("featuredproducts::featuredproducts.already_exists")));
		}
		if($input['feature_id']) {
			DB::table('featured_product_plans')->where('featured_prod_plan_id', $input['feature_id'])->update(array('featured_days' => $featured_days, 'featured_price' => $featured_price, 'status' => $status));
			$id = $input['feature_id'];
		}
		else {
			$id = DB::table('featured_product_plans')->insertGetId(array('featured_days' => $featured_days, 'featured_price' => $featured_price, 'status' => $status));
		}
		return json_encode(array('status' => 'success', 'id' => $id));
 	}

	public function updateFeaturedProductsPlanStatus($id, $action)
	{
		//echo 'id==>'.$id.' action==>'.$action;die;
		switch($action)
		{
			case 'Active':
				DB::table('featured_product_plans')
					->where('featured_prod_plan_id', $id)
					->update(array('status' => $action));
				$success_msg = Lang::get('featuredproducts::featuredproducts.activated_suc_msg');
				break;
			case 'Inactive':
				DB::table('featured_product_plans')
					->where('featured_prod_plan_id', $id)
					->update(array('status' => $action));
				$success_msg = Lang::get('featuredproducts::featuredproducts.deactivated_suc_msg');
				break;
			default;
				$success_msg = Lang::get('featuredproducts::featuredproducts.select_valid_actiion');
				break;
		}
		return $success_msg;
	}
	//Featured products plan functions end

	//Featured products list start
	public function changeFeaturedStatus($p_id, $status)
	{
		if($status == 'Yes') {
			$affected_rows = Product::where('id', '=', $p_id)->update( array('is_featured_product' => $status));
		}
		else{
			$affected_rows = Product::where('id', '=', $p_id)->update( array('is_featured_product' => $status, 'featured_product_expires' => '0000-00-00 00:00:00'));
		}
		if($affected_rows) {
			$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		return false;
	}
	//Featured prodcuts list end

	public function updateFeaturedProdcutExpiryDate($p_details, $plan_details) {
		$product_id = $p_details['id'];
		$number_of_days = $plan_details['featured_days'];
		if($number_of_days > 0)
			$date = date('Y-m-d', strtotime("+".$number_of_days." days"));
		Product::where('id', '=', $product_id)->update(array('featured_product_expires' => $date, 'is_featured_product' => 'Yes'));
		$array_multi_key = array('root_category_id_key', 'product_details', 'top_categories_cache_key', 'TFP_cache_key');
		HomeCUtil::forgotMultiCacheKey($array_multi_key);
	}

	public function setFeaturedProdcutTransaction($p_details, $plan_details) {
		$product_id = $p_details['id'];
		$product_user_id = $p_details['product_user_id'];
		$featured_price = $plan_details['featured_price'];

		$credit_obj = \Credits::initialize();
		$credit_obj->setUserId($product_user_id);
		$credit_obj->setCurrency(Config::get('generalConfig.site_default_currency'));
		$credit_obj->setAmount($featured_price);
		$credit_obj->creditAndDebit('amount', 'minus');

		$credit_obj->setUserId(Config::get('generalConfig.admin_id'));
		$credit_obj->setCurrency(Config::get('generalConfig.site_default_currency'));
		$credit_obj->setAmount($featured_price);
		$credit_obj->creditAndDebit('amount', 'plus');

		//Add the site transaction
		$trans_obj = new \SiteTransactionHandlerService();
		$transaction_arr['date_added'] = new DateTime;
		$transaction_arr['user_id'] = $product_user_id;
		$transaction_arr['transaction_type'] = 'debit';
		$transaction_arr['amount'] = $featured_price;
		$transaction_arr['currency'] = Config::get('generalConfig.site_default_currency');
		$transaction_arr['transaction_key'] = 'product_featured_fee';
		$transaction_arr['reference_content_table'] = 'product';
		$transaction_arr['reference_content_id'] = $product_id;
		$transaction_arr['status'] = 'completed';
		$transaction_arr['transaction_notes'] = 'Debited product featured fee from wallet for product id: '.$product_id;
		$transaction_arr['payment_type'] = 'wallet';
		$trans_id = $trans_obj->addNewTransaction($transaction_arr);

		$transaction_arr['user_id'] = Config::get('generalConfig.admin_id');
		$transaction_arr['transaction_type'] = 'credit';
		$transaction_arr['transaction_notes'] = 'Credited product featured fee to wallet for product id: '.$product_id;
		$trans_id = $trans_obj->addNewTransaction($transaction_arr);

		//Send mail
		//$this->sendFeaturedProductFeeMail($p_details, 'admin');
	}

	public function sendFeaturedProductFeeMail($p_details, $mail_to = 'admin')
	{
		$this->productService = new ProductService();
		# Send mail to the admin
		$mail_det_arr = array();
		$mail_det_arr['product_code'] 				= $p_details['product_code'];
		$mail_det_arr['product_name'] 				= $p_details['product_name'];
		$mail_det_arr['product_view_url'] 			= $this->productService->getProductViewURL($p_details['id'], $p_details);
		$mail_det_arr['transaction_date'] 			= \CUtil::FMTDate($credit_details['date_paid'], "Y-m-d H:i:s", "");
		$mail_det_arr['product_added_by'] 			= \CUtil::getUserDetails($p_details['product_user_id']);
		$mail_det_arr['to_email'] 					= Config::get("generalConfig.invoice_email");

		# Send mail to admin regarding featured product listing fee paid
		$template = "featuredproducts::emails.productFeaturedListingFeePaidNotificationToAdmin";
		try {
			\Mail::send($template, $mail_det_arr, function($m) use ($mail_det_arr) {
				$m->to(\Config::get("generalConfig.invoice_email"));
				$subject = Lang::get('featuredproducts::featuredproducts.featured_product_listing_fee_paid_admin_notify_mail_sub');
				$subject = str_replace("VAR_SITE_NAME", \Config::get('generalConfig.site_name'), $subject);
				$subject = str_replace("VAR_PRODUCT_CODE", $mail_det_arr['product_code'], $subject);
				$m->subject($subject);
			});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
	}

	public function getFeaturedProductsPlans()
	{
		$plans_list = array();
		$product_plans = DB::table('featured_product_plans')->where('status', '=', 'Active')->orderBy('featured_days', 'ASC')->get();
		if(count($product_plans) > 0) {
			foreach($product_plans as $key => $plan) {
				$lang_days = Ucfirst(Lang::get('common.for')).' '.$plan->featured_days.' '.strtolower(Lang::choice('featuredproducts::featuredproducts.day_choice', $plan->featured_days));
				$lang_price = Config::get('generalConfig.site_default_currency').' '.$plan->featured_price;
				$plans_list[$plan->featured_prod_plan_id] = $lang_days.': '.$lang_price;
			}
		}
		return $plans_list;
	}

	public function getTotalFeaturedProducts()
	{
		$cache_key = 'TFP_cache_key';
		if (($total_feature_products = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$total_feature_products = Product::whereRaw('product_status = ? AND is_featured_product = ? AND date_expires >= ? AND date_expires >= ?', array('Ok', 'Yes', '0000-00-00 00:00:00', new DateTime('today')))->count();
			HomeCUtil::cachePut($cache_key, $total_feature_products);
		}
		return $total_feature_products;
	}
}