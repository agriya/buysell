<?php
class AdminUnpaidInvoiceListController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->adminManageUserService = new AdminManageUserService();
    }

	public function getIndex()
    {
    	$logged_user_id = BasicCUtil::getLoggedUserId();
        $search_id_from = Input::has('search_id_from')?Input::get('search_id_from'):'';
		$search_id_to = Input::has('search_id_to')?Input::get('search_id_to'):'';
		$search_user_code = Input::has('search_user_code')?Input::get('search_user_code'):'';
		$search_invoice_date_added = Input::has('search_invoice_date_added')?Input::get('search_invoice_date_added'):'';
		$search_invoice_total_amount = Input::has('search_invoice_total_amount')?Input::get('search_invoice_total_amount'):'';
    	$tab = (Input::has('tab') && Input::get('tab') == 'by_admin') ? Input::get('tab'):'by_user';
    	//echo $invoice_status;exit;
    	$logged_user_id = BasicCUtil::getLoggedUserId();
    	$invoice_details = DB::table('common_invoice')
    					   ->orderBy('common_invoice_id', 'DESC')
    					   ->leftjoin('credits_log','common_invoice.reference_id','=','credits_log.credit_id')
    					   ->leftjoin('shop_order','common_invoice.reference_id','=','shop_order.id')
						   ->where('common_invoice.status',"Unpaid");
		if($tab == 'by_user')
		{
			$invoice_details->whereRaw('(reference_type = ? OR reference_type = ?)',array("Products", "Usercredits"));
			if(Input::get('search_id_from') !='' )
				$invoice_details = $invoice_details->where('common_invoice_id', '>=', $search_id_from);

			if(Input::get('search_id_to') !='' )
				$invoice_details = $invoice_details->where('common_invoice_id', '<=', $search_id_to);

			if(Input::get('search_user_code') !='')
			{
				$user_id = BasicCUtil::getUserIDFromCode(Input::get('search_user_code'));
				$invoice_details = $invoice_details->whereRaw('common_invoice.user_id = ?', array($user_id));
			}

			if($search_invoice_date_added !='' )
				$invoice_details = $invoice_details->whereRaw("(DATE_FORMAT(shop_order.date_created, '%Y-%m-%d') = ?)", array($search_invoice_date_added));

			if($search_invoice_total_amount !='' )
				$invoice_details = $invoice_details->where('shop_order.total_amount', '=', $search_invoice_total_amount);
		}
		else
		{
			$invoice_details->where('reference_type',"Credits");
			if(Input::get('search_id_from') !='' )
				$invoice_details = $invoice_details->where('common_invoice_id', '>=', $search_id_from);

			if(Input::get('search_id_to') !='' )
				$invoice_details = $invoice_details->where('common_invoice_id', '<=', $search_id_to);

			if(Input::get('search_user_code') !='')
			{
				$user_id = BasicCUtil::getUserIDFromCode(Input::get('search_user_code'));
				$invoice_details = $invoice_details->whereRaw('common_invoice.user_id = ?', array($user_id));
			}

			if($search_invoice_date_added !='' )
				$invoice_details = $invoice_details->whereRaw("(DATE_FORMAT(credits_log.date_added, '%Y-%m-%d') = ?)", array($search_invoice_date_added));

			if($search_invoice_total_amount !='' )
				$invoice_details = $invoice_details->where('credits_log.amount', '=', $search_invoice_total_amount);
			$tab = "by_admin";
		}
		$invoice_details = $invoice_details->paginate(10);
		//echo "<pre>"; print_r($invoice_details); echo "</pre>";exit;
		$this->header->setMetaTitle(trans('meta.admin_unpaid_invoice_list_title'));
		return View::make('admin.unpaidInvoiceList', compact('invoice_details','tab','d_arr'));
    }

    public function getInvoiceDetails($common_invoice_id = Null)
	{
		$s = Input::has('s')?Input::get('s'):'';
		$i = Input::has('invoice')?Input::get('invoice'):'invoice';
		$tab = Input::has('tab')?Input::get('tab'):'';
		//echo Input::get('s');exit;
		if(is_null($common_invoice_id))
			return Redirect::action('AdminUnpaidInvoiceListController@getIndex')->with('error_message','Select a valid invoice');
		$logged_user_id = BasicCUtil::getLoggedUserId();
		$invoice_details = DB::table('common_invoice')
							->leftjoin('credits_log','common_invoice.reference_id','=','credits_log.credit_id')
							->leftjoin('shop_order','common_invoice.reference_id','=','shop_order.id')
							->whereRaw('common_invoice_id = ?', array($common_invoice_id))
							->first();

		if(isset($invoice_details->status) &&  $invoice_details->status == 'Draft')
			return Redirect::action('AdminUnpaidInvoiceListController@getIndex')->with('error_message','Select a valid invoice');

		if(isset($invoice_details->reference_type) &&  $invoice_details->reference_type == 'Products')
			return Redirect::action('AdminUnpaidInvoiceListController@getIndex')->with('error_message','Select a valid invoice');

		$this->header->setMetaTitle(trans('meta.invoice_details'));
		return View::make('admin.viewInvoice', compact('invoice_details','i','tab','d_arr','s'));
	}
}
?>