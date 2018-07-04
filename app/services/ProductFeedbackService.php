<?php
class ProductFeedbackService
{
	public function getFeedbackGivenInvoiceId($user_id = null, $view_type)
	{
		if(is_null($user_id))
			return false;

		$invoiceids = ProductInvoiceFeedback::orderby('id','desc');
		if(!is_null($user_id)){
			if ($view_type == 'awaiting') {
				$invoiceids->whereRaw('buyer_id = ?', array($user_id));
			} else {
				$invoiceids->whereRaw('(buyer_id = ? OR seller_id = ?)', array($user_id, $user_id));
			}
		}
		$invoiceids = $invoiceids->lists('invoice_id');
		return $invoiceids;
	}

	public function addFeedback($data = array()){
		$productInvoiceFeedback = new ProductInvoiceFeedback();
		$feedback_id = $productInvoiceFeedback->addNew($data);
		return $feedback_id;
	}

	public function getFeedbackDetails($feedback_id)
	{
		$feedback_details = ProductInvoiceFeedback::where('id', $feedback_id)->first();
		return $feedback_details;
	}

	public function deleteFeedback($feedback_id){
		$deleted = ProductInvoiceFeedback::where('id', $feedback_id)->delete();
		return $deleted;
	}

	public function updateFeedabck($feedback_id, $data){
		$update = ProductInvoiceFeedback::where('id', $feedback_id)->update($data);
		return $update;
	}

	public function bulkUpdateFeedabck($feedback_ids, $data){
		$update = false;
		if(!empty($feedback_ids))
		{
			try
			{
				$qry = ProductInvoiceFeedback::whereIn('id', $feedback_ids)->update($data);
				$update = true;
			}
			catch(exception $e)
			{
				echo $e->getMessage();exit;
				$update = false;
			}
		}
		return $update;
	}

	public function bulkDeleteFeedabck($feedback_ids)
	{
		if(!empty($feedback_ids))
		{
			try{
				ProductInvoiceFeedback::whereIn('id', $feedback_ids)->delete();
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
		}
		else
			return false;
	}

	public function feedbackCountDetails($feedback_invoice_ids=array()){

		$default_feedback_count = array('Positive' => 0, 'Negative' => 0, 'Neutral' => 0);
		$remarks = array();
		if($feedback_invoice_ids && !empty($feedback_invoice_ids))
			$remarks = ProductInvoiceFeedback::select(DB::Raw('COUNT(*) AS cnt, feedback_remarks'))->whereIn('invoice_id',$feedback_invoice_ids)->groupby('feedback_remarks')->lists('cnt','feedback_remarks');
		$remarks = $remarks+$default_feedback_count;
		return $remarks;
	}

	public function getAdminFeedbacks($inputs = array())
	{
		$qry = ProductInvoiceFeedback::LeftJoin('product', 'product.id','=','product_invoice_feedback.product_id')
				->LeftJoin('users as seller', 'seller.id', '=', 'product_invoice_feedback.seller_id')
				->LeftJoin('users as buyer', 'buyer.id', '=', 'product_invoice_feedback.buyer_id')
				->Select("product_invoice_feedback.*", "product.id as product_id", "product.product_user_id", "product.product_code", "product.product_name", "product.url_slug",
						"seller.email as seller_email", "seller.first_name as seller_firstname", "seller.last_name as seller_lastname", "seller.user_name as seller_username",
						"buyer.email as buyer_email", "buyer.first_name as buyer_firstname", "buyer.last_name as buyer_lastname", "buyer.user_name as buyer_username");

		if(isset($inputs['invoice_id_from']) && $inputs['invoice_id_from'] > 0)
			$qry->where('invoice_id', '>=', $inputs['invoice_id_from']);
		if(isset($inputs['invoice_id_to']) && $inputs['invoice_id_to'] > 0)
			$qry->where('invoice_id', '<=', $inputs['invoice_id_to']);

		if(isset($inputs['feedback_id_from']) && $inputs['feedback_id_from'] > 0)
			$qry->where('product_invoice_feedback.id', '>=', $inputs['feedback_id_from']);
		if(isset($inputs['feedback_id_to']) && $inputs['feedback_id_to'] > 0)
			$qry->where('product_invoice_feedback.id', '<=', $inputs['feedback_id_to']);

		if(isset($inputs['search_status']) && $inputs['search_status'] != '')
			$qry->where('product_invoice_feedback.feedback_remarks', '=', ucfirst($inputs['search_status']));

		if(isset($inputs['feedback_by']) && $inputs['feedback_by'] != '')
		{
			$qry->where('product_invoice_feedback.feedback_user_id', '=', BasicCUtil::getUserIDFromCode($inputs['feedback_by']));
		}

		return $qry->orderby('product_invoice_feedback.id', 'desc')->get()->toArray();
	}

	public function getFeedbackCountByProductId($product_id) {

		$default_feedback_count = array('Positive' => 0, 'Negative' => 0, 'Neutral' => 0);
		$remarks = array();
		if($product_id > 0) {
			$remarks = ProductInvoiceFeedback::select(DB::Raw('COUNT(*) AS cnt, feedback_remarks'))
											->whereRaw('product_id = ?', array($product_id))
											->whereRaw('feedback_user_id = buyer_id')
											->groupby('feedback_remarks')->lists('cnt', 'feedback_remarks');
		}
		$remarks = $remarks + $default_feedback_count;
		return $remarks;
	}

	public function getFeedbackCountBySellerId($seller_id) {

		$default_feedback_count = array('Positive' => 0, 'Negative' => 0, 'Neutral' => 0);
		$remarks = array();
		if($seller_id > 0) {
			$cache_key = 'FCBSIDCK_'.$seller_id;
			if (($remarks = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$remarks = ProductInvoiceFeedback::select(DB::Raw('COUNT(*) AS cnt, feedback_remarks'))
												->whereRaw('seller_id = ?', array($seller_id))
												->whereRaw('feedback_user_id = buyer_id')
												->groupby('feedback_remarks')->lists('cnt', 'feedback_remarks');
				HomeCUtil::cachePut($cache_key, $remarks, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		$remarks = $remarks + $default_feedback_count;
		return $remarks;
	}

	public function getFeedbackListBySellerId($seller_id, $limit, $paginate = false) {
		$feedback = ProductInvoiceFeedback::whereRaw('seller_id = ?', array($seller_id))
											->whereRaw('feedback_user_id = buyer_id')
											->orderby('product_invoice_feedback.id', 'desc');
		if($limit > 0) {
			if($paginate)
				$feedback = $feedback->paginate($limit);
			else
				$feedback = $feedback->take($limit)->get();
		}
		else {
			$feedback = $feedback->get();
		}
		return $feedback;
	}

	public function getAvgRatingForSeller($user_id = null)
	{
		if(is_null($user_id) || $user_id <=0 )
			return 0;
		$cache_key = 'product_invoice_feedback'.$user_id;
		if (($seller_rating = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$seller_rating = ProductInvoiceFeedback::select(DB::raw('sum(rating) as rating, count(*) as rating_count'))->where('seller_id', '=', $user_id)->whereRaw('feedback_user_id = buyer_id')->first()->toArray();
			HomeCUtil::cachePut($cache_key, $seller_rating, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(isset($seller_rating['rating_count']) && $seller_rating['rating_count'] > 0 && isset($seller_rating['rating']) && $seller_rating['rating'] > 0)
			$seller_rating['avg_rating'] = CUtil::formatAmount($seller_rating['rating']/$seller_rating['rating_count']);
		else
			$seller_rating['avg_rating'] = 0;
		return $seller_rating;

	}
}